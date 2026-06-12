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

        // Atingiu nível 5 / 10.
        if ((int) $personagem['nivel'] >= 5) {
            $this->coletar($novas, $this->conceder($id, 'aprendiz_veterano'));
        }
        if ((int) $personagem['nivel'] >= 10) {
            $this->coletar($novas, $this->conceder($id, 'lenda_viva'));
        }

        return $novas;
    }

    private function coletar(array &$lista, ?array $conquista): void
    {
        if ($conquista !== null) {
            $lista[] = $conquista;
        }
    }
}
