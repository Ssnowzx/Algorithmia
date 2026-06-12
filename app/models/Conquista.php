<?php
/**
 * Catálogo de conquistas e a relação com personagens.
 */
class Conquista extends Model
{
    protected string $table = 'conquistas';

    public function porCodigo(string $codigo): ?array
    {
        return $this->findBy('codigo', $codigo);
    }

    /**
     * Ids das conquistas já obtidas por um personagem.
     *
     * @return int[]
     */
    public function obtidasIds(int $personagemId): array
    {
        $stmt = $this->db->prepare(
            "SELECT conquista_id FROM conquistas_personagem WHERE personagem_id = :p"
        );
        $stmt->execute(['p' => $personagemId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    /**
     * Concede uma conquista (ignora se já possui). Retorna true se foi nova.
     */
    public function conceder(int $personagemId, int $conquistaId): bool
    {
        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO conquistas_personagem (personagem_id, conquista_id)
             VALUES (:p, :c)"
        );
        $stmt->execute(['p' => $personagemId, 'c' => $conquistaId]);
        return $stmt->rowCount() > 0;
    }
}
