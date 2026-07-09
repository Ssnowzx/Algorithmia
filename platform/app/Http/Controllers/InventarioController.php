<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dominio\Progressao\ServicoDeConquistas;
use App\Models\Conquista;
use App\Models\Item;
use App\Models\ItemDoInventario;
use App\Models\Personagem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Equipar, usar e descartar itens.
 *
 * Tudo o que muda estado é POST. No legado, `inventario/descartar/5` destruía um
 * item permanentemente por GET.
 */
final class InventarioController extends Controller
{
    public function __construct(private readonly ServicoDeConquistas $conquistas) {}

    public function index(Request $requisicao): View
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        return view('inventario.index', [
            'heroi' => $heroi,
            'itens' => ItemDoInventario::doPersonagem($heroi->id),
        ]);
    }

    public function equipar(Request $requisicao, Item $item): RedirectResponse
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        if (ItemDoInventario::quantidade($heroi->id, $item->id) < 1 || ! $item->ehEquipavel()) {
            return back()->with('erro', 'Este item não pode ser equipado.');
        }

        ItemDoInventario::equipar($heroi->id, $item);

        return back()->with('sucesso', "{$item->nome} equipado!".$this->objetivosDeEquipar($heroi, $item));
    }

    public function desequipar(Request $requisicao, Item $item): RedirectResponse
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        ItemDoInventario::desequipar($heroi->id, $item->id);

        return back();
    }

    /** Usa uma poção fora de batalha: cura na hora, respeitando o teto. */
    public function usar(Request $requisicao, Item $item): RedirectResponse
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        if (! $item->ehPocao() || ItemDoInventario::quantidade($heroi->id, $item->id) < 1) {
            return back()->with('erro', 'Item indisponível.');
        }

        $heroi->update([
            'hp_atual' => min($heroi->hp_max, $heroi->hp_atual + $item->efeito('cura_hp')),
            'mp_atual' => min($heroi->mp_max, $heroi->mp_atual + $item->efeito('cura_mp')),
        ]);
        ItemDoInventario::remover($heroi->id, $item->id);

        $objetivo = $this->conquistas->concederObjetivo($heroi, 'primeira_pocao');

        return back()->with('sucesso', "{$item->nome} usada. Você se sente revigorado!".$this->textoObjetivo($objetivo));
    }

    public function descartar(Request $requisicao, Item $item): RedirectResponse
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        ItemDoInventario::remover($heroi->id, $item->id);

        return back()->with('info', 'Item descartado.');
    }

    /**
     * Objetivos da loja disparados por equipar: a primeira arma, e o arsenal
     * completo (arma + escudo + acessório ao mesmo tempo).
     */
    private function objetivosDeEquipar(Personagem $heroi, Item $item): string
    {
        $texto = '';

        if ($item->tipo === 'arma') {
            $texto .= $this->textoObjetivo($this->conquistas->concederObjetivo($heroi, 'primeira_arma'));
        }

        /** @var list<string> $equipaveis */
        $equipaveis = config('jogo.tipos_equipaveis');
        $equipados = ItemDoInventario::tiposEquipados($heroi->id);

        if (array_diff($equipaveis, $equipados) === []) {
            $texto .= $this->textoObjetivo($this->conquistas->concederObjetivo($heroi, 'arsenal_completo'));
        }

        return $texto;
    }

    /** @param  array{conquista:Conquista,ouro:int}|null  $objetivo */
    private function textoObjetivo(?array $objetivo): string
    {
        if ($objetivo === null) {
            return '';
        }

        $ouro = $objetivo['ouro'];

        return ' 🏅 Objetivo: '.$objetivo['conquista']->nome.($ouro > 0 ? " (+{$ouro} de ouro)" : '');
    }
}
