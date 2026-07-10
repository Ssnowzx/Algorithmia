<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dominio\Operacao\ServicoDeAuditoria;
use App\Http\Requests\SalvarDesafioRequest;
use App\Models\Desafio;
use App\Models\Fase;
use App\Models\Item;
use App\Models\ItemDoInventario;
use App\Models\Mestre;
use App\Models\Personagem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Painel do Mestre: CRUD de fases, desafios e itens.
 *
 * O acesso é do middleware `mestre`; todas as escritas são POST. No legado,
 * `mestre/excluirFase/5` apagava a fase e seus desafios em cascata por GET.
 *
 * Criar e editar são métodos separados, e não um só com parâmetro opcional: com
 * `?Desafio $desafio = null` o Laravel resolveria a dependência pelo container e
 * injetaria um model VAZIO no lugar de null, e a criação viraria uma atualização
 * de um registro inexistente.
 */
final class MestreController extends Controller
{
    public function __construct(private readonly ServicoDeAuditoria $auditoria) {}

    public function index(): View
    {
        return view('mestre.index', [
            'totais' => [
                'fases' => Fase::query()->count(),
                'desafios' => Desafio::query()->count(),
                'itens' => Item::query()->count(),
                'mestres' => Mestre::query()->count(),
                'jogadores' => Personagem::query()->count(),
            ],
        ]);
    }

    // ---------------------------------------------------------------- desafios

    public function desafios(): View
    {
        return view('mestre.desafios', [
            'desafios' => Desafio::query()
                ->join('fases', 'fases.id', '=', 'desafios.fase_id')
                ->orderBy('fases.ordem_global')->orderBy('desafios.ordem')
                ->select('desafios.*')
                ->with('fase')
                ->paginate(50),
        ]);
    }

    public function desafioNovo(): View
    {
        return view('mestre.desafio-form', ['desafio' => null, 'fases' => Fase::todasOrdenadas()]);
    }

    public function desafioEditar(Desafio $desafio): View
    {
        return view('mestre.desafio-form', ['desafio' => $desafio, 'fases' => Fase::todasOrdenadas()]);
    }

    public function desafioCriar(SalvarDesafioRequest $requisicao): RedirectResponse
    {
        $desafio = Desafio::create($requisicao->paraBanco());
        $this->auditoria->registrar('desafio.criar', 'desafio', $desafio->id, [
            'fase_id' => $desafio->fase_id,
            'tipo' => $desafio->tipo,
        ]);

        return redirect()->route('mestre.desafios')->with('sucesso', 'Desafio criado.');
    }

    public function desafioAtualizar(SalvarDesafioRequest $requisicao, Desafio $desafio): RedirectResponse
    {
        $antes = $desafio->only(['fase_id', 'tipo', 'assunto', 'pergunta']);
        $desafio->update($requisicao->paraBanco());
        $this->auditoria->registrar('desafio.atualizar', 'desafio', $desafio->id, ['antes' => $antes]);

        return redirect()->route('mestre.desafios')->with('sucesso', 'Desafio atualizado.');
    }

    public function desafioExcluir(Desafio $desafio): RedirectResponse
    {
        // O resumo é montado ANTES do delete: depois dele não há de onde tirá-lo.
        $this->auditoria->registrar('desafio.excluir', 'desafio', $desafio->id, [
            'fase_id' => $desafio->fase_id,
            'tipo' => $desafio->tipo,
            'assunto' => $desafio->assunto,
            // A pergunta identifica o que sumiu. O gabarito NÃO entra aqui: auditoria
            // não é backup, e espalhar a resposta por mais uma tabela não ajuda ninguém.
            'pergunta' => $desafio->pergunta,
        ]);
        $desafio->delete();

        return redirect()->route('mestre.desafios')->with('info', 'Desafio removido.');
    }

    // ------------------------------------------------------------------- fases

    public function fases(): View
    {
        return view('mestre.fases', ['fases' => Fase::query()->with('mestre')->orderBy('ordem_global')->get()]);
    }

    public function faseNova(): View
    {
        return view('mestre.fase-form', ['fase' => null] + $this->opcoesDeFase());
    }

    public function faseEditar(Fase $fase): View
    {
        return view('mestre.fase-form', ['fase' => $fase] + $this->opcoesDeFase());
    }

    public function faseCriar(Request $requisicao): RedirectResponse
    {
        $fase = Fase::create($this->validarFase($requisicao, null));
        $this->auditoria->registrar('fase.criar', 'fase', $fase->id, ['nome' => $fase->nome]);

        return redirect()->route('mestre.fases')->with('sucesso', 'Fase criada.');
    }

    public function faseAtualizar(Request $requisicao, Fase $fase): RedirectResponse
    {
        $antes = $fase->only(['nome', 'tipo', 'ordem_global']);
        $fase->update($this->validarFase($requisicao, $fase));
        $this->auditoria->registrar('fase.atualizar', 'fase', $fase->id, ['antes' => $antes]);

        return redirect()->route('mestre.fases')->with('sucesso', 'Fase atualizada.');
    }

    public function faseExcluir(Fase $fase): RedirectResponse
    {
        // A cascata leva os desafios junto. Contar depois devolveria zero, e o
        // registro diria que nada se perdeu.
        $this->auditoria->registrar('fase.excluir', 'fase', $fase->id, [
            'nome' => $fase->nome,
            'tipo' => $fase->tipo,
            'ordem_global' => $fase->ordem_global,
            'desafios_em_cascata' => Desafio::query()->where('fase_id', $fase->id)->count(),
        ]);
        $fase->delete();

        return redirect()->route('mestre.fases')->with('info', 'Fase removida (e seus desafios).');
    }

    // ------------------------------------------------------------------- itens

    public function itens(): View
    {
        return view('mestre.itens', ['itens' => Item::query()->orderBy('tipo')->orderBy('nome')->get()]);
    }

    public function itemNovo(): View
    {
        return view('mestre.item-form', ['item' => null]);
    }

    public function itemEditar(Item $item): View
    {
        return view('mestre.item-form', ['item' => $item]);
    }

    public function itemCriar(Request $requisicao): RedirectResponse
    {
        $item = Item::create($this->validarItem($requisicao));
        $this->auditoria->registrar('item.criar', 'item', $item->id, ['nome' => $item->nome]);

        return redirect()->route('mestre.itens')->with('sucesso', 'Item criado.');
    }

    public function itemAtualizar(Request $requisicao, Item $item): RedirectResponse
    {
        $antes = $item->only(['nome', 'tipo', 'preco']);
        $item->update($this->validarItem($requisicao));
        $this->auditoria->registrar('item.atualizar', 'item', $item->id, ['antes' => $antes]);

        return redirect()->route('mestre.itens')->with('sucesso', 'Item atualizado.');
    }

    public function itemExcluir(Item $item): RedirectResponse
    {
        // Ele sai do inventário de todos os heróis. Quantos, ninguém saberia depois.
        $this->auditoria->registrar('item.excluir', 'item', $item->id, [
            'nome' => $item->nome,
            'tipo' => $item->tipo,
            'inventarios_afetados' => ItemDoInventario::query()->where('item_id', $item->id)->count(),
        ]);
        $item->delete();

        return redirect()->route('mestre.itens')->with('info', 'Item removido.');
    }

    // ----------------------------------------------------------------- apoio

    /** @return array<string,mixed> */
    private function opcoesDeFase(): array
    {
        return [
            'mestres' => Mestre::query()->orderBy('ordem')->get(),
            'fases' => Fase::todasOrdenadas(),
            'itens' => Item::query()->orderBy('nome')->get(),
        ];
    }

    /** @return array<string,mixed> */
    private function validarFase(Request $requisicao, ?Fase $fase): array
    {
        return $requisicao->validate([
            'mestre_id' => ['nullable', 'integer', Rule::exists('mestres', 'id')],
            'ordem_global' => ['required', 'integer', 'min:1'],
            'nome' => ['required', 'string', 'max:150'],
            'tipo' => ['required', Rule::in(['historia', 'licao', 'chefe', 'chefe_final', 'secundaria'])],
            'descricao' => ['nullable', 'string'],
            'inimigo_nome' => ['nullable', 'string', 'max:120'],
            'inimigo_svg' => ['nullable', 'string', 'max:80'],
            'inimigo_hp' => ['required', 'integer', 'min:1'],
            'inimigo_ataque' => ['required', 'integer', 'min:0'],
            'xp_recompensa' => ['required', 'integer', 'min:0'],
            'ouro_recompensa' => ['required', 'integer', 'min:0'],
            'item_drop_id' => ['nullable', 'integer', Rule::exists('itens', 'id')],
            // Uma fase não pode exigir a si mesma: ficaria trancada para sempre.
            'requisito_fase_id' => array_filter([
                'nullable', 'integer', Rule::exists('fases', 'id'),
                $fase !== null ? Rule::notIn([$fase->id]) : null,
            ]),
        ]);
    }

    /** @return array<string,mixed> */
    private function validarItem(Request $requisicao): array
    {
        $dados = $requisicao->validate([
            'nome' => ['required', 'string', 'max:120'],
            'descricao' => ['nullable', 'string'],
            'tipo' => ['required', Rule::in(['arma', 'escudo', 'acessorio', 'pocao', 'especial'])],
            'preco' => ['required', 'integer', 'min:0'],
            'svg_slug' => ['required', 'string', 'max:80'],
            'raridade' => ['required', Rule::in(['comum', 'raro', 'epico', 'lendario'])],
            'compravel' => ['nullable', 'boolean'],
            'ef_ataque' => ['nullable', 'integer'],
            'ef_defesa' => ['nullable', 'integer'],
            'ef_cura_hp' => ['nullable', 'integer'],
            'ef_cura_mp' => ['nullable', 'integer'],
        ]);

        // Efeito zerado vira NULL, e não `{}`: o motor lê `efeito['ataque'] ?? 0`,
        // mas um objeto vazio poluiria os dados e os diffs.
        $efeito = [];
        foreach (['ataque', 'defesa', 'cura_hp', 'cura_mp'] as $chave) {
            $valor = (int) ($dados["ef_{$chave}"] ?? 0);
            if ($valor !== 0) {
                $efeito[$chave] = $valor;
            }
        }

        return [
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'] ?? null,
            'tipo' => $dados['tipo'],
            'efeito' => $efeito === [] ? null : $efeito,
            'preco' => $dados['preco'],
            'svg_slug' => $dados['svg_slug'],
            'raridade' => $dados['raridade'],
            'compravel' => (bool) ($dados['compravel'] ?? false),
        ];
    }
}
