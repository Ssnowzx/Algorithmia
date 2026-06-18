<?php
/**
 * Perguntas de uma fase. Os campos opcoes/resposta são JSON.
 */
class Desafio extends Model
{
    protected string $table = 'desafios';

    public function daFase(int $faseId): array
    {
        return $this->where('fase_id', $faseId, 'ordem ASC');
    }

    /**
     * Pool completo de desafios de uma fase (base do sorteio anti-repetição).
     * Alias semântico de daFase(): a fase guarda MAIS desafios do que entram numa
     * batalha; o sorteio escolhe um subconjunto a cada combate.
     */
    public function poolDaFase(int $faseId): array
    {
        return $this->where('fase_id', $faseId, 'ordem ASC');
    }

    /**
     * Ids dos desafios de uma fase que o personagem JÁ respondeu (qualquer vez).
     * Usado para priorizar perguntas inéditas no sorteio da batalha.
     *
     * @return int[]
     */
    public function idsVistos(int $personagemId, int $faseId): array
    {
        $stmt = $this->db->prepare(
            "SELECT DISTINCT r.desafio_id
             FROM respostas_log r
             JOIN desafios d ON d.id = r.desafio_id
             WHERE r.personagem_id = :p AND d.fase_id = :f"
        );
        $stmt->execute(['p' => $personagemId, 'f' => $faseId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    /**
     * Todos os desafios com o nome da fase (para o painel administrativo).
     */
    public function todosComFase(): array
    {
        return $this->db->query(
            "SELECT d.*, f.nome AS fase_nome, f.ordem_global
             FROM desafios d
             JOIN fases f ON f.id = d.fase_id
             ORDER BY f.ordem_global ASC, d.ordem ASC"
        )->fetchAll();
    }

    /**
     * Decodifica os campos JSON (opcoes, resposta) de uma linha de desafio.
     */
    public static function decodificar(array $desafio): array
    {
        $desafio['opcoes'] = $desafio['opcoes'] ? json_decode($desafio['opcoes'], true) : [];
        $desafio['resposta'] = json_decode($desafio['resposta'], true);
        return $desafio;
    }
}
