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
