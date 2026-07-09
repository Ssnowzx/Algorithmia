<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dominio\Progressao\ServicoDeProgressao;
use App\Dominio\Progressao\ServicoDeReputacao;
use App\Models\Dialogo;
use App\Models\Fase;
use App\Models\Personagem;
use App\Models\ProgressoFase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Os diálogos antes de cada fase e a conclusão das fases de história.
 *
 * `concluir` é POST. No legado era GET, e gravava progresso e XP — bastava um
 * `<img src="...historia/concluir/3">` numa página qualquer para o navegador de
 * um jogador logado avançar a campanha dele sozinho.
 * Ver `docs/migracao/INVENTARIO.md` §1.
 */
final class HistoriaController extends Controller
{
    public function __construct(
        private readonly ServicoDeProgressao $progressao,
        private readonly ServicoDeReputacao $reputacao,
    ) {}

    public function ver(Request $requisicao, Fase $fase): RedirectResponse|View
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        if (! $this->progressao->faseLiberada($fase, ProgressoFase::mapaDoPersonagem($heroi->id))) {
            return redirect()->route('mapa')->with(
                'erro',
                'Calma, herói. Essa fase ainda está trancada — termine a anterior antes de bancar o atrevido.'
            );
        }

        return view('historia.dialogo', [
            'heroi' => $heroi,
            'fase' => $fase->load('mestre'),
            'dialogos' => Dialogo::paraMomento($fase->id, 'antes', $this->reputacao->variante($heroi)),
            'ehCombate' => $fase->tipo !== 'historia',
        ]);
    }

    public function concluir(Request $requisicao, Fase $fase): RedirectResponse
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        if ($fase->tipo !== 'historia') {
            return redirect()->route('mapa');
        }

        if (! $this->progressao->faseLiberada($fase, ProgressoFase::mapaDoPersonagem($heroi->id))) {
            return redirect()->route('mapa')->with('erro', 'Essa fase ainda está trancada.');
        }

        // Idempotente: reler uma cena não deve pagar XP de novo.
        if (! ProgressoFase::concluiu($heroi->id, $fase->id)) {
            ProgressoFase::registrar($heroi->id, $fase->id, 3, 0, 0, usouIa: false);

            if ($fase->xp_recompensa > 0) {
                $this->progressao->ganharXp($heroi, $fase->xp_recompensa);
            }
        }

        return redirect()->route('mapa');
    }
}
