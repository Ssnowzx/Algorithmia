<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dominio\Progressao\ServicoDeConquistas;
use App\Models\Item;
use App\Models\ItemDoInventario;
use App\Models\Personagem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Compra e venda de itens com ouro.
 *
 * Comprar e vender são POST. No legado eram alcançáveis por GET com o id na URL,
 * então um `<img src="...loja/vender/5">` esvaziava o inventário de quem abrisse
 * a página errada.
 */
final class LojaController extends Controller
{
    public function __construct(private readonly ServicoDeConquistas $conquistas) {}

    public function index(Request $requisicao): View
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        $inventario = ItemDoInventario::doPersonagem($heroi->id);
        $bonus = ItemDoInventario::bonusEquipados($heroi->id);

        // O que ocupa cada slot hoje, para a loja mostrar o ganho real de trocar
        // — e não só o número absoluto do item novo.
        $equipadoPorTipo = [];
        foreach ($inventario as $linha) {
            if ($linha->equipado && $linha->item->ehEquipavel()) {
                $equipadoPorTipo[$linha->item->tipo] = $linha->item;
            }
        }

        return view('loja.index', [
            'heroi' => $heroi,
            'itens' => Item::compraveis(),
            'possui' => $inventario->pluck('quantidade', 'item_id')->all(),
            'equipadoPorTipo' => $equipadoPorTipo,
            'atributos' => [
                'ataque' => $heroi->ataqueDaClasse() + $bonus['ataque'],
                'defesa' => $heroi->defesaDaClasse() + $bonus['defesa'],
            ],
        ]);
    }

    public function comprar(Request $requisicao, Item $item): RedirectResponse
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        if (! $item->compravel) {
            return back()->with('erro', 'Item indisponível para compra.');
        }

        // Relê o ouro dentro da transação: dois cliques rápidos comprariam dois
        // itens com o mesmo ouro se a checagem ficasse fora dela.
        $comprou = DB::transaction(function () use ($heroi, $item): bool {
            $ouro = (int) Personagem::query()->whereKey($heroi->id)->lockForUpdate()->value('ouro');
            if ($ouro < $item->preco) {
                return false;
            }

            $heroi->update(['ouro' => $ouro - $item->preco]);
            ItemDoInventario::adicionar($heroi->id, $item->id);

            return true;
        });

        if (! $comprou) {
            return back()->with('erro', "Ouro insuficiente para comprar {$item->nome}.");
        }

        $colecao = $this->conquistas->avaliarColecao($heroi);

        return back()->with('sucesso', "{$item->nome} comprado!".($colecao !== null ? ' 🏆 Conquista: Colecionador!' : ''));
    }

    public function vender(Request $requisicao, Item $item): RedirectResponse
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        if (ItemDoInventario::quantidade($heroi->id, $item->id) < 1) {
            return back()->with('erro', 'Você não possui esse item.');
        }

        if (! $item->podeSerVendido()) {
            return back()->with('erro', 'O Fragmento da IA Ancestral não pode ser vendido.');
        }

        $valor = $item->valorDeVenda();

        DB::transaction(function () use ($heroi, $item, $valor): void {
            $heroi->refresh();
            $heroi->update(['ouro' => $heroi->ouro + $valor]);
            ItemDoInventario::remover($heroi->id, $item->id);
        });

        return back()->with('sucesso', "{$item->nome} vendido por {$valor} de ouro.");
    }
}
