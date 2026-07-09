<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dominio\Progressao\ServicoDeMaestria;
use App\Dominio\Progressao\ServicoDeMissoes;
use App\Dominio\Progressao\ServicoDeOnboarding;
use App\Dominio\Progressao\ServicoDeProgressao;
use App\Dominio\Progressao\ServicoDeRegioes;
use App\Models\Conquista;
use App\Models\Personagem;
use App\Models\ProgressoFase;
use App\Models\RespostaLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * A ficha do herói.
 *
 * Tudo aqui é derivado do que já existe (`respostas_log`, `progresso_fases`,
 * inventário, conquistas). Nenhuma tabela nova, nenhum cron, nenhuma escrita.
 */
final class PerfilController extends Controller
{
    public function index(Request $requisicao): View
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        $progresso = ProgressoFase::mapaDoPersonagem($heroi->id);
        $maestria = ServicoDeMaestria::porMateria(RespostaLog::estatisticasPorAssunto($heroi->id));
        $regioes = ServicoDeRegioes::dominio($heroi->id);
        $missoes = ServicoDeMissoes::daSemana($heroi->id);

        return view('perfil.index', [
            'heroi' => $heroi,
            'fasesConcluidas' => count($progresso),
            'estrelas' => array_sum(array_map(fn (ProgressoFase $p): int => $p->estrelas, $progresso)),
            'xpProximoNivel' => ServicoDeProgressao::xpParaNivel($heroi->nivel + 1),

            'maestria' => $maestria,
            'materiasDominadas' => ServicoDeMaestria::totalDominadas($maestria),
            'totalMaterias' => count($maestria),

            'regioes' => $regioes,
            'regioesDominadas' => ServicoDeRegioes::totalDominadas($regioes),
            'tituloLenda' => ServicoDeRegioes::tituloLenda($regioes),

            'missoes' => $missoes,
            'missoesCompletas' => ServicoDeMissoes::totalCompletas($missoes),

            'onboarding' => ServicoDeOnboarding::primeirosPassos($heroi),
            'conquistas' => Conquista::query()->orderBy('id')->get(),
            'conquistasObtidas' => ServicoDeOnboarding::conquistasObtidas($heroi->id),
        ]);
    }
}
