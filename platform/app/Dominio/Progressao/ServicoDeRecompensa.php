<?php

declare(strict_types=1);

namespace App\Dominio\Progressao;

use App\Models\Conquista;
use App\Models\Fase;
use App\Models\Item;
use App\Models\ItemDoInventario;
use App\Models\Personagem;
use App\Models\ProgressoFase;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Concede as recompensas de uma vitória: XP, ouro, estrelas, drop, avanço de
 * capítulo, conquistas e reputação. Porte de `app/services/RecompensaService.php`.
 *
 * A diferença deliberada em relação ao legado está aqui: **a concessão é
 * idempotente por chave no banco**, e não por um flag de sessão. No legado,
 * `conceder()` chamado duas vezes duplica a reputação (XP e ouro escapam por
 * acidente, gravados como valor absoluto de uma linha lida antes). A guarda vivia
 * em `$_SESSION['batalha']['recompensado']`, o que a tornava tão durável quanto a
 * sessão — e inútil contra requisições concorrentes.
 *
 * Ver `tests/Dominio/RecompensaTest.php::conceder_a_mesma_batalha_duas_vezes_nao_credita_de_novo`.
 */
final class ServicoDeRecompensa
{
    public function __construct(
        private readonly ServicoDeProgressao $progressao,
        private readonly ServicoDeReputacao $reputacao,
        private readonly ServicoDeConquistas $conquistas,
    ) {}

    /**
     * @return array<string,mixed>|null null quando esta batalha já foi recompensada
     */
    public function conceder(Personagem $heroi, ResultadoDaBatalha $resultado): ?array
    {
        return DB::transaction(function () use ($heroi, $resultado): ?array {
            if (! $this->reivindicar($heroi, $resultado)) {
                return null;
            }

            $fase = Fase::query()->find($resultado->faseId)
                ?? throw new RuntimeException("Fase {$resultado->faseId} não existe.");

            $estrelas = $this->progressao->calcularEstrelas($resultado->erros, $resultado->usouIa);

            $ouro = $fase->ouro_recompensa;
            if ($heroi->classe === 'ranger') {
                $ouro = (int) round($ouro * (float) config('jogo.progressao.bonus_ouro_ranger'));
            }

            $ganho = $this->progressao->ganharXp($heroi, $fase->xp_recompensa);
            $heroi->update(['ouro' => $heroi->ouro + $ouro]);

            ProgressoFase::registrar(
                $heroi->id, $fase->id, $estrelas,
                $resultado->acertos, $resultado->erros, $resultado->usouIa
            );

            $itemDrop = $this->concederDrop($heroi, $fase);

            // Recarrega: as conquistas de nível dependem do XP recém-creditado.
            $heroi->refresh();
            $this->progressao->atualizarCapitulo($heroi, $fase);

            $conquistas = $this->conquistas->avaliarAposFase($heroi, $fase, $resultado->erros, $resultado->usouIa);
            $this->coletar($conquistas, $this->conquistas->concederConquistaDaRegiao($heroi, $fase));
            $this->coletar($conquistas, $this->conquistas->concederPuroDeCoracao($heroi, $fase));

            // Vencer sem recorrer à IA recompensa a disciplina.
            if (! $resultado->usouIa) {
                $this->reputacao->ajustar($heroi, (int) config('jogo.progressao.reputacao_por_vitoria_limpa'));
            }

            return [
                'estrelas' => $estrelas,
                'xp' => $fase->xp_recompensa,
                'ouro' => $ouro,
                'niveis' => $ganho['niveis_ganhos'],
                'nivel' => $ganho['nivel'],
                'item_drop' => $itemDrop === null ? null : ['nome' => $itemDrop->nome, 'svg' => $itemDrop->svg_slug],
                'conquistas' => array_map(
                    fn (Conquista $c): array => ['nome' => $c->nome, 'svg' => $c->svg_slug],
                    $conquistas
                ),
                'fase_final' => $fase->tipo === 'chefe_final',
            ];
        });
    }

    /**
     * Marca a batalha como recompensada. Devolve false se outra chamada já a
     * reivindicou — inclusive uma concorrente, pois a chave primária serializa a
     * disputa no banco, e não na aplicação.
     */
    private function reivindicar(Personagem $heroi, ResultadoDaBatalha $resultado): bool
    {
        $inseridas = DB::affectingStatement(
            'INSERT INTO recompensas_batalha (batalha_id, personagem_id, fase_id)
             VALUES (?, ?, ?)
             ON CONFLICT (batalha_id) DO NOTHING',
            [$resultado->batalhaId, $heroi->id, $resultado->faseId]
        );

        return $inseridas > 0;
    }

    /** O drop da fase entra uma única vez: rejogar não multiplica o item. */
    private function concederDrop(Personagem $heroi, Fase $fase): ?Item
    {
        if ($fase->item_drop_id === null) {
            return null;
        }
        if (ItemDoInventario::quantidade($heroi->id, $fase->item_drop_id) > 0) {
            return null;
        }

        ItemDoInventario::adicionar($heroi->id, $fase->item_drop_id);

        return Item::query()->find($fase->item_drop_id);
    }

    /** @param  list<Conquista>  $lista */
    private function coletar(array &$lista, ?Conquista $conquista): void
    {
        if ($conquista !== null) {
            $lista[] = $conquista;
        }
    }
}
