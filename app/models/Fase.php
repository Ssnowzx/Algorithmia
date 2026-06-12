<?php
/**
 * Nós do mapa. Sequenciadas por ordem_global e agrupadas por mestre.
 */
class Fase extends Model
{
    protected string $table = 'fases';

    public function todasOrdenadas(): array
    {
        return $this->findAll('ordem_global ASC');
    }

    public function doMestre(int $mestreId): array
    {
        return $this->where('mestre_id', $mestreId, 'ordem_global ASC');
    }

    /**
     * Fases com o nome do mestre, para o mapa e o painel admin.
     */
    public function comMestre(): array
    {
        return $this->db->query(
            "SELECT f.*, m.nome AS mestre_nome, m.cor_tema
             FROM fases f
             LEFT JOIN mestres m ON m.id = f.mestre_id
             ORDER BY f.ordem_global ASC"
        )->fetchAll();
    }
}
