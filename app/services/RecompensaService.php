<?php
/**
 * Concede as recompensas de uma vitória em batalha: XP, ouro, estrelas, drop de
 * item, avanço de capítulo, conquistas e reputação — tudo numa única transação.
 *
 * Vive na camada de serviço (não no controller) porque é regra de negócio que
 * orquestra vários models e gerencia uma transação de banco.
 */
class RecompensaService
{
    /**
     * Aplica XP, ouro, drop de item, estrelas, conquistas e avanço de capítulo
     * para uma fase vencida, e devolve o resumo consumido pela tela de vitória.
     */
    public function conceder(array $heroi, array $estado): array
    {
        // As 7 escritas (XP, ouro, progresso, drop, capítulo, conquistas, reputação)
        // vão numa transação: um erro no meio reverte tudo, sem personagem corrompido.
        $db = getConnection();
        $transacaoPropria = !$db->inTransaction();
        if ($transacaoPropria) {
            $db->beginTransaction();
        }
        try {
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
            $this->concederPuroDeCoracao($heroiAtual, $fase, $conquistas);

            // Sobe reputação ao vencer sem usar a IA (recompensa a disciplina).
            if (!$usouIa) {
                (new ReputacaoService())->ajustar((int) $heroi['id'], 5);
            }

            $retorno = [
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
            if ($transacaoPropria) {
                $db->commit();
            }
            return $retorno;
        } catch (\Throwable $e) {
            if ($transacaoPropria && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
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
        // Chaveia pelo svg_slug (estável) em vez do nome de exibição da região.
        $codigo = REGIOES_MESTRE[$mestre['svg_slug'] ?? '']['conquista'] ?? null;
        if ($codigo) {
            $nova = (new ConquistaService())->conceder((int) $heroi['id'], $codigo);
            if ($nova) {
                $conquistas[] = $nova;
            }
        }
    }

    /**
     * Concede "Puro de Coração" ao concluir um capítulo (região) inteiro sem nunca
     * recorrer ao Fragmento da IA. Dispara ao derrotar o chefe da região; checa
     * todas as fases principais (lição + chefe) daquela região. As secundárias
     * opcionais não bloqueiam a conquista.
     */
    private function concederPuroDeCoracao(array $heroi, array $fase, array &$conquistas): void
    {
        if ($fase['tipo'] !== 'chefe' || empty($fase['mestre_id'])) {
            return;
        }
        $fasesRegiao = (new Fase())->doMestre((int) $fase['mestre_id']);
        $mapa = (new ProgressoFase())->mapaDoPersonagem((int) $heroi['id']);
        foreach ($fasesRegiao as $f) {
            if (!in_array($f['tipo'], ['licao', 'chefe'], true)) {
                continue; // secundárias opcionais não contam
            }
            $prog = $mapa[(int) $f['id']] ?? null;
            if ($prog === null || (int) $prog['usou_ia'] === 1) {
                return; // capítulo incompleto ou houve cola em alguma fase
            }
        }
        $nova = (new ConquistaService())->conceder((int) $heroi['id'], 'puro_de_coracao');
        if ($nova) {
            $conquistas[] = $nova;
        }
    }
}
