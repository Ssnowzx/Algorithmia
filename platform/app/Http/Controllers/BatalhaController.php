<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dominio\Combate\MotorDeBatalha;
use App\Dominio\Progressao\ResultadoDaBatalha;
use App\Dominio\Progressao\ServicoDeProgressao;
use App\Dominio\Progressao\ServicoDeRecompensa;
use App\Models\Fase;
use App\Models\Personagem;
use App\Models\ProgressoFase;
use App\Support\LeituraDoInimigo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Monta a arena e atende os turnos.
 *
 * Todos os endpoints de turno são POST com CSRF — no legado eles validavam o
 * token mas aceitavam GET, o que os deixava acionáveis por um `<img src>`.
 *
 * A regra que dá nome ao jogo mora no motor, não aqui: o gabarito nunca sai do
 * servidor, e o controller só repassa o que `paraCliente()` autoriza.
 */
final class BatalhaController extends Controller
{
    public function __construct(
        private readonly MotorDeBatalha $motor,
        private readonly ServicoDeRecompensa $recompensa,
        private readonly ServicoDeProgressao $progressao,
    ) {}

    public function iniciar(Request $requisicao, Fase $fase): RedirectResponse|View
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        $progresso = ProgressoFase::mapaDoPersonagem($heroi->id);
        if (! $this->progressao->faseLiberada($fase, $progresso)) {
            return redirect()->route('mapa')
                ->with('erro', 'Tentando pular a fila? Essa fase ainda está trancada. Volte quando merecer.');
        }

        $estado = $this->motor->iniciar($heroi, $fase);
        if ($estado->total === 0) {
            return redirect()->route('mapa')->with('erro', 'Esta fase não possui desafios cadastrados.');
        }

        return view('batalha.arena', [
            'heroi' => $heroi,
            'fase' => $fase,
            'estado' => $estado->paraCliente(),
            'itensUsaveis' => $this->itensDeBatalha($heroi),
            'intel' => LeituraDoInimigo::frase($fase->inimigo_hp, $fase->inimigo_ataque),
            'bestiario' => config("bestiario.{$estado->inimigoSvg}"),
        ]);
    }

    public function responder(Request $requisicao): JsonResponse
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        $resultado = $this->motor->responder($requisicao->input('resposta'));
        $this->recompensarSeVenceu($heroi, $resultado);

        return response()->json($resultado);
    }

    public function fragmento(Request $requisicao): JsonResponse
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        $resultado = $this->motor->usarFragmentoIa($heroi);
        if (! isset($resultado['erro'])) {
            $this->recompensarSeVenceu($heroi, $resultado);
        }

        return response()->json($resultado);
    }

    public function especial(Request $requisicao): JsonResponse
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        return response()->json($this->motor->armarEspecial($heroi));
    }

    public function pocao(Request $requisicao): JsonResponse
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        return response()->json($this->motor->usarPocao($heroi, (int) $requisicao->input('item_id')));
    }

    public function fugir(): JsonResponse
    {
        $this->motor->limpar();

        return response()->json(['ok' => true, 'redirect' => route('mapa')]);
    }

    /**
     * A vitória concede a recompensa uma única vez. A garantia é a chave de
     * idempotência de `recompensas_batalha`, e não um flag de sessão: um replay
     * do POST vencedor devolve null, e o jogador não é creditado de novo.
     *
     * @param  array<string,mixed>  $resultado
     */
    private function recompensarSeVenceu(Personagem $heroi, array &$resultado): void
    {
        if (($resultado['resultado'] ?? null) !== 'vitoria') {
            return;
        }

        $estado = $this->motor->estado();
        if ($estado === null) {
            return;
        }

        $recompensa = $this->recompensa->conceder($heroi, ResultadoDaBatalha::de($estado));
        if ($recompensa !== null) {
            $resultado['recompensa'] = $recompensa;
        }
    }

    /**
     * Poções e Fragmentos que o herói pode usar em combate.
     *
     * @return list<array{item_id:int,nome:string,quantidade:int,svg_slug:string}>
     */
    private function itensDeBatalha(Personagem $heroi): array
    {
        // Query builder, e não Eloquent: as colunas vêm do join e não existem no
        // model, o que confundiria tanto o PHPStan quanto quem lê depois.
        return DB::table('inventario')
            ->join('itens', 'itens.id', '=', 'inventario.item_id')
            ->where('inventario.personagem_id', $heroi->id)
            ->whereIn('itens.tipo', ['pocao', 'especial'])
            ->orderBy('itens.nome')
            ->get(['inventario.item_id', 'itens.nome', 'inventario.quantidade', 'itens.svg_slug'])
            ->map(fn (object $linha): array => [
                'item_id' => (int) $linha->item_id,
                'nome' => (string) $linha->nome,
                'quantidade' => (int) $linha->quantidade,
                'svg_slug' => (string) $linha->svg_slug,
            ])
            ->all();
    }
}
