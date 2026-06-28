<?php
/**
 * Conduz as batalhas: monta a arena e atende os endpoints AJAX de cada turno.
 */
class BatalhaController extends Controller
{
    private BatalhaService $batalha;
    private RecompensaService $recompensa;

    public function __construct()
    {
        $this->batalha = new BatalhaService();
        $this->recompensa = new RecompensaService();
    }

    /**
     * Prepara o estado da batalha e renderiza a arena.
     */
    public function iniciar(string $faseId = '0'): void
    {
        $heroi = Auth::exigirPersonagem();
        $fase = (new Fase())->findById((int) $faseId);
        if (!$fase) {
            $this->redirect('mapa');
        }

        $mapaProgresso = (new ProgressoFase())->mapaDoPersonagem((int) $heroi['id']);
        if (!(new ProgressaoService())->faseLiberada($fase, $mapaProgresso)) {
            $this->flash('erro', 'Tentando pular a fila? Essa fase ainda está trancada. Volte quando merecer.');
            $this->redirect('mapa');
        }

        // Recarrega o herói com HP/MP atuais e inicia a batalha.
        $estado = $this->batalha->iniciar($heroi, $fase);
        if ($estado['total'] === 0) {
            $this->flash('erro', 'Esta fase não possui desafios cadastrados.');
            $this->redirect('mapa');
        }

        // Poções e Fragmentos disponíveis para usar em combate.
        $itensUsaveis = $this->itensDeBatalha((int) $heroi['id']);

        // Cenário por bioma: o fundo da arena reflete a região da fase.
        $fundoBioma = 'fundo-batalha';
        if (!empty($fase['mestre_id'])) {
            $mestre = (new Mestre())->findById((int) $fase['mestre_id']);
            if ($mestre && !empty($mestre['svg_slug'])) {
                $fundoBioma = fundoRegiao($mestre['svg_slug']);
            }
        }
        $bioma = str_replace('fundo-', '', $fundoBioma); // ex.: 'montanha', 'torre'

        // Leitura do inimigo: sugere uma tática (ataque/defesa) conforme a ameaça.
        $intelInimigo = taticaInimigo((int) $fase['inimigo_hp'], (int) $fase['inimigo_ataque']);

        $this->view('batalha/arena', [
            'pageTitle'    => $fase['nome'],
            'fase'         => $fase,
            'estado'       => $this->batalha->estadoPublico($estado),
            'itensUsaveis' => $itensUsaveis,
            'fundoBioma'   => $fundoBioma,
            'bioma'        => $bioma,
            'intelInimigo' => $intelInimigo,
        ]);
    }

    /**
     * Recebe a resposta de um desafio (AJAX) e devolve o resultado do turno.
     */
    public function responder(): void
    {
        $this->exigirCsrfAjax();
        $heroi = Auth::exigirPersonagem();
        $corpo = $this->corpoJson();
        $resposta = $corpo['resposta'] ?? null;

        $resultado = $this->batalha->responder($resposta);
        $this->finalizarSePreciso($heroi, $resultado);
        $this->json($resultado);
    }

    /**
     * Usa o Fragmento da IA Ancestral no desafio atual (AJAX).
     */
    public function fragmento(): void
    {
        $this->exigirCsrfAjax();
        $heroi = Auth::exigirPersonagem();
        $resultado = $this->batalha->usarFragmentoIa($heroi);
        if (!isset($resultado['erro'])) {
            $this->finalizarSePreciso($heroi, $resultado);
        }
        $this->json($resultado);
    }

    /**
     * Arma o ataque especial (AJAX).
     */
    public function especial(): void
    {
        $this->exigirCsrfAjax();
        $heroi = Auth::exigirPersonagem();
        $this->json($this->batalha->armarEspecial($heroi));
    }

    /**
     * Usa uma poção durante a batalha (AJAX).
     */
    public function pocao(): void
    {
        $this->exigirCsrfAjax();
        $heroi = Auth::exigirPersonagem();
        $corpo = $this->corpoJson();
        $itemId = (int) ($corpo['item_id'] ?? 0);
        $this->json($this->batalha->usarPocao($heroi, $itemId));
    }

    /**
     * Foge da batalha e volta ao mapa.
     */
    public function fugir(): void
    {
        $this->exigirCsrfAjax();
        $this->batalha->limpar();
        $this->json(['ok' => true, 'redirect' => url('mapa')]);
    }

    /**
     * Quando a batalha termina em vitória, concede as recompensas uma única vez
     * (delegado ao RecompensaService) e injeta o resumo no resultado, consumido
     * pela tela de vitória.
     */
    private function finalizarSePreciso(array $heroi, array &$resultado): void
    {
        if (empty($resultado['resultado'])) {
            return;
        }
        $estado = $this->batalha->estado();
        if (!$estado || !empty($estado['recompensado'])) {
            return;
        }

        if ($resultado['resultado'] === 'vitoria') {
            $resultado['recompensa'] = $this->recompensa->conceder($heroi, $estado);
        }

        // Marca como processado para não repetir (estado é dono do BatalhaService).
        $this->batalha->marcarRecompensado();
    }

    /**
     * Lista poções e Fragmentos da IA que o herói pode usar em batalha.
     */
    private function itensDeBatalha(int $personagemId): array
    {
        $itens = (new Inventario())->doPersonagem($personagemId);
        return array_values(array_filter($itens, fn($i) => in_array($i['tipo'], ['pocao', 'especial'], true)));
    }
}
