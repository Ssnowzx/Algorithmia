<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dominio\Progressao\ServicoDeConquistas;
use App\Dominio\Progressao\ServicoDeProgressao;
use App\Dominio\Progressao\ServicoDeReputacao;
use App\Models\Dialogo;
use App\Models\Escolha;
use App\Models\Fase;
use App\Models\Personagem;
use App\Models\ProgressoFase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        private readonly ServicoDeConquistas $conquistas,
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

        // A cena que precede o confronto derradeiro emenda direto nele. O legado
        // comparava `ordem_global === 34`; aqui a próxima fase é quem se declara.
        $proxima = $fase->proxima();
        if ($proxima !== null && $proxima->tipo === 'chefe_final') {
            return redirect()->route('historia.ver', $proxima);
        }

        return redirect()->route('mapa');
    }

    /**
     * A escolha diante da IA Ancestral, ou o epílogo se ela já foi feita.
     *
     * Só chega aqui quem venceu o confronto derradeiro. A fase é encontrada pelo
     * tipo `chefe_final`, e não pelo id 35 fixo do legado: um seeder diferente
     * moveria o id e a tela ficaria inalcançável sem que nada acusasse.
     */
    public function final(Request $requisicao): RedirectResponse|View
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        $confronto = Fase::confrontoFinal();
        if ($confronto === null || ! ProgressoFase::concluiu($heroi->id, $confronto->id)) {
            return redirect()->route('mapa');
        }

        if (Escolha::valor($heroi->id, 'final') !== null) {
            return $this->epilogo($heroi);
        }

        return view('historia.escolha-final', ['heroi' => $heroi]);
    }

    public function escolherFinal(Request $requisicao): RedirectResponse|View
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        $confronto = Fase::confrontoFinal();
        if ($confronto === null || ! ProgressoFase::concluiu($heroi->id, $confronto->id)) {
            return redirect()->route('mapa');
        }

        $dados = $requisicao->validate([
            'escolha' => ['required', Rule::in(config('jogo.escolhas_finais'))],
        ]);

        Escolha::definir($heroi->id, 'final', $dados['escolha']);

        return $this->epilogo($heroi);
    }

    /** A página de lore. Pública: é a vitrine da história, não exige conta. */
    public function lore(): View
    {
        return view('historia.lore');
    }

    /** Resolve o desfecho e concede a conquista secreta correspondente. */
    private function epilogo(Personagem $heroi): View
    {
        $final = $this->reputacao->finalDeterminado($heroi);

        /** @var array<string,string> $conquistas */
        $conquistas = config('jogo.conquistas_de_final');
        if (isset($conquistas[$final])) {
            // Idempotente por chave primária composta: reabrir o epílogo não
            // concede a conquista de novo.
            $this->conquistas->conceder($heroi, $conquistas[$final]);
        }

        return view('historia.final', ['heroi' => $heroi, 'final' => $final]);
    }
}
