<?php
/**
 * Conduz as batalhas: monta a arena e atende os endpoints AJAX de cada turno.
 */
class BatalhaController extends Controller
{
    private BatalhaService $batalha;

    public function __construct()
    {
        $this->batalha = new BatalhaService();
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

        $this->view('batalha/arena', [
            'pageTitle'    => $fase['nome'],
            'fase'         => $fase,
            'estado'       => $this->batalha->estadoPublico($estado),
            'itensUsaveis' => $itensUsaveis,
        ]);
    }

    /**
     * Recebe a resposta de um desafio (AJAX) e devolve o resultado do turno.
     */
    public function responder(): void
    {
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
        $heroi = Auth::exigirPersonagem();
        $this->json($this->batalha->armarEspecial($heroi));
    }

    /**
     * Usa uma poção durante a batalha (AJAX).
     */
    public function pocao(): void
    {
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
        $this->batalha->limpar();
        $this->json(['ok' => true, 'redirect' => url('mapa')]);
    }

    /**
     * Quando a batalha termina em vitória, processa as recompensas uma única vez
     * e injeta o resumo no resultado (consumido pela tela de vitória).
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
            $resultado['recompensa'] = $this->concederRecompensas($heroi, $estado);
        }

        // Marca como processado para não repetir.
        $estado['recompensado'] = true;
        $_SESSION['batalha'] = $estado;
    }

    /**
     * Aplica XP, ouro, drop de item, estrelas, conquistas e avanço de capítulo.
     */
    private function concederRecompensas(array $heroi, array $estado): array
    {
        $fase = (new Fase())->findById($estado['fase_id']);
        $progressao = new ProgressaoService();
        $personagens = new Personagem();

        $erros = (int) $estado['erros'];
        $usouIa = (bool) $estado['usou_ia'];
        $estrelas = $progressao->calcularEstrelas($erros, $usouIa);

        // Ouro (Ranger ganha um bônus de 20%).
        $ouro = (int) $fase['ouro_recompensa'];
        if ($heroi['classe'] === 'ranger') {
            $ouro = (int) round($ouro * 1.2);
        }

        // XP e possíveis subidas de nível.
        $ganho = $progressao->ganharXp($heroi, (int) $fase['xp_recompensa']);
        $personagens->update((int) $heroi['id'], ['ouro' => (int) $heroi['ouro'] + $ouro]);

        // Registra o progresso da fase (mantém o melhor desempenho).
        (new ProgressoFase())->registrar((int) $heroi['id'], $estado['fase_id'], $estrelas, $estado['acertos'], $erros, $usouIa);

        // Drop de item, se houver e ainda não estiver no inventário.
        $itemDrop = null;
        if (!empty($fase['item_drop_id'])) {
            $inv = new Inventario();
            if ($inv->quantidade((int) $heroi['id'], (int) $fase['item_drop_id']) === 0) {
                $inv->adicionar((int) $heroi['id'], (int) $fase['item_drop_id']);
                $itemDrop = (new Item())->findById((int) $fase['item_drop_id']);
            }
        }

        // Avança o capítulo e concede conquistas (recarrega herói atualizado).
        $heroiAtual = $personagens->findById((int) $heroi['id']);
        $progressao->atualizarCapitulo($heroiAtual, $fase);
        $conquistas = (new ConquistaService())->avaliarAposFase($heroiAtual, $fase, [
            'erros' => $erros, 'usou_ia' => $usouIa,
        ]);
        $this->concederConquistaDeRegiao($heroiAtual, $fase, $conquistas);

        // Sobe reputação ao vencer sem usar a IA (recompensa a disciplina).
        if (!$usouIa) {
            (new ReputacaoService())->ajustar((int) $heroi['id'], 5);
        }

        return [
            'estrelas'    => $estrelas,
            'xp'          => (int) $fase['xp_recompensa'],
            'ouro'        => $ouro,
            'niveis'      => $ganho['niveis_ganhos'],
            'nivel'       => $ganho['nivel'],
            'item_drop'   => $itemDrop ? ['nome' => $itemDrop['nome'], 'svg' => $itemDrop['svg_slug']] : null,
            'conquistas'  => array_map(fn($c) => ['nome' => $c['nome'], 'svg' => $c['svg_slug']], $conquistas),
            'fase_final'  => $fase['tipo'] === 'chefe_final',
            'redirect_final' => $fase['tipo'] === 'chefe_final' ? url('historia/final') : null,
        ];
    }

    /**
     * Concede a conquista de "discípulo" ao derrotar o chefe de uma região.
     */
    private function concederConquistaDeRegiao(array $heroi, array $fase, array &$conquistas): void
    {
        if ($fase['tipo'] !== 'chefe' || empty($fase['mestre_id'])) {
            return;
        }
        $mestre = (new Mestre())->findById((int) $fase['mestre_id']);
        if (!$mestre) {
            return;
        }
        $mapaCodigos = [
            'Porto da Sintaxe'        => 'mestre_willen',
            'Cidadela dos Objetos'    => 'mestre_clayton',
            'Floresta das Estruturas' => 'mestre_marcelo',
            'Montanha do Cálculo'     => 'mestre_cesar',
            'Torre das Conexões'      => 'mestre_cassandro',
        ];
        $codigo = $mapaCodigos[$mestre['regiao']] ?? null;
        if ($codigo) {
            $nova = (new ConquistaService())->conceder((int) $heroi['id'], $codigo);
            if ($nova) {
                $conquistas[] = $nova;
            }
        }
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
