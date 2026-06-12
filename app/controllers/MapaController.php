<?php
/**
 * Mapa-múndi: trilha de fases agrupadas por região/mestre, no estilo Duolingo.
 */
class MapaController extends Controller
{
    public function index(): void
    {
        $heroi = Auth::exigirPersonagem();

        $fases = (new Fase())->comMestre();
        $mapaProgresso = (new ProgressoFase())->mapaDoPersonagem((int) $heroi['id']);
        $progresso = new ProgressaoService();

        // Agrupa as fases por região, calculando o estado de cada nó.
        // As fases sem mestre que vêm ANTES dos mestres formam o "início";
        // as que vêm DEPOIS formam o "fim da jornada" (renderizado por último).
        $regioes = [];
        $vistoMestre = false;
        foreach ($fases as $fase) {
            if (!empty($fase['mestre_id'])) {
                $vistoMestre = true;
                $chave = (int) $fase['mestre_id'];
            } else {
                $chave = $vistoMestre ? 'fim' : 'inicio';
            }
            if (!isset($regioes[$chave])) {
                $regioes[$chave] = [
                    'mestre_nome' => $fase['mestre_nome'],
                    'svg_slug'    => null,
                    'cor'         => $fase['cor_tema'] ?? '#7c5cff',
                    'fases'       => [],
                ];
            }

            $faseId = (int) $fase['id'];
            $concluida = isset($mapaProgresso[$faseId]);
            $liberada = $progresso->faseLiberada($fase, $mapaProgresso);

            $fase['estado'] = $concluida ? 'concluida' : ($liberada ? 'atual' : 'bloqueada');
            $fase['estrelas'] = $concluida ? (int) $mapaProgresso[$faseId]['estrelas'] : 0;
            $regioes[$chave]['fases'][] = $fase;
        }

        // Anexa o slug do mestre para o retrato da região.
        foreach ((new Mestre())->todosOrdenados() as $m) {
            if (isset($regioes[$m['id']])) {
                $regioes[$m['id']]['svg_slug'] = $m['svg_slug'];
                $regioes[$m['id']]['regiao'] = $m['regiao'];
            }
        }

        // Metadados das regiões especiais de início e de fim.
        if (isset($regioes['inicio'])) {
            $regioes['inicio'] += ['regiao' => 'Terras de Hello World', 'subtitulo' => 'O começo da jornada', 'emoji' => '🌍', 'fundo' => 'fundo-vila'];
            $regioes['inicio']['cor'] = '#5b8cff';
        }
        if (isset($regioes['fim'])) {
            $regioes['fim'] += ['regiao' => 'O Fim da Jornada', 'subtitulo' => 'O abismo aguarda o escolhido', 'emoji' => '🌌', 'fundo' => 'fundo-abismo'];
            $regioes['fim']['cor'] = '#9c88ff';
        }

        $totalFases = count($fases);
        $concluidas = count($mapaProgresso);

        $this->view('mapa/index', [
            'pageTitle'   => 'Mapa de Algorithmia',
            'regioes'     => $regioes,
            'totalFases'  => $totalFases,
            'concluidas'  => $concluidas,
            'totalEstrelas' => (new ProgressoFase())->totalEstrelas((int) $heroi['id']),
        ]);
    }
}
