<?php
/**
 * Verifica e concede conquistas após eventos do jogo.
 * As regras consultam o estado atual do personagem e dos logs.
 */
class ConquistaService
{
    private Conquista $conquistas;
    private ProgressoFase $progresso;
    private RespostaLog $respostas;

    public function __construct()
    {
        $this->conquistas = new Conquista();
        $this->progresso = new ProgressoFase();
        $this->respostas = new RespostaLog();
    }

    /**
     * Concede uma conquista pelo código. Retorna a linha se foi inédita, ou null.
     */
    public function conceder(int $personagemId, string $codigo): ?array
    {
        $conquista = $this->conquistas->porCodigo($codigo);
        if (!$conquista) {
            return null;
        }
        $nova = $this->conquistas->conceder($personagemId, (int) $conquista['id']);
        return $nova ? $conquista : null;
    }

    /**
     * Avalia conquistas dependentes do resultado de uma fase recém-concluída.
     *
     * @return array<int,array> conquistas recém-obtidas (para exibir ao jogador)
     */
    public function avaliarAposFase(array $personagem, array $fase, array $resultado): array
    {
        $id = (int) $personagem['id'];
        $novas = [];

        // Primeira fase concluída (conceder é idempotente: só vale uma vez).
        $this->coletar($novas, $this->conceder($id, 'primeiro_passo'));

        // Fase sem erros.
        if (($resultado['erros'] ?? 1) === 0 && empty($resultado['usou_ia'])) {
            $this->coletar($novas, $this->conceder($id, 'sem_falhas'));
        }

        // Derrotou um chefe.
        if (in_array($fase['tipo'], ['chefe', 'chefe_final'], true)) {
            $this->coletar($novas, $this->conceder($id, 'cacador_de_chefes'));
        }

        // Usou a IA pela primeira vez (a tentação).
        if (!empty($resultado['usou_ia'])) {
            $this->coletar($novas, $this->conceder($id, 'tentacao'));
        }

        // Recuperou todos os Logs do Zero: concluiu as 4 fases secundárias.
        $secundarias = [8, 14, 20, 32];
        if (in_array((int) $fase['id'], $secundarias, true)) {
            $todasConcluidas = true;
            foreach ($secundarias as $faseId) {
                if (!$this->progresso->concluiu($id, $faseId)) {
                    $todasConcluidas = false;
                    break;
                }
            }
            if ($todasConcluidas) {
                $this->coletar($novas, $this->conceder($id, 'arquivista_do_vazio'));
            }
        }

        // Atingiu nível 5 / 10.
        if ((int) $personagem['nivel'] >= 5) {
            $this->coletar($novas, $this->conceder($id, 'aprendiz_veterano'));
        }
        if ((int) $personagem['nivel'] >= 10) {
            $this->coletar($novas, $this->conceder($id, 'lenda_viva'));
        }

        // Colecionador: juntou 8+ itens diferentes (cobre drops de fase).
        $this->coletar($novas, $this->avaliarColecao($id));

        return $novas;
    }

    /**
     * Concede "Colecionador" quando o personagem acumula 8+ itens DISTINTOS.
     * Era a única conquista do catálogo sem gatilho. Chamável após qualquer
     * aquisição (drop de fase ou compra na loja).
     */
    public function avaliarColecao(int $personagemId): ?array
    {
        $distintos = count((new Inventario())->doPersonagem($personagemId));
        return $distintos >= 8 ? $this->conceder($personagemId, 'colecionador') : null;
    }

    private function coletar(array &$lista, ?array $conquista): void
    {
        if ($conquista !== null) {
            $lista[] = $conquista;
        }
    }
}
