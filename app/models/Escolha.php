<?php
/**
 * Decisões narrativas do jogador, usadas para determinar o final.
 */
class Escolha extends Model
{
    protected string $table = 'escolhas';

    /**
     * Grava (substituindo) uma escolha identificada por código.
     */
    public function definir(int $personagemId, string $codigo, string $valor): void
    {
        $stmt = $this->db->prepare(
            "DELETE FROM escolhas WHERE personagem_id = :p AND codigo = :c"
        );
        $stmt->execute(['p' => $personagemId, 'c' => $codigo]);
        $this->create(['personagem_id' => $personagemId, 'codigo' => $codigo, 'valor' => $valor]);
    }

    public function valor(int $personagemId, string $codigo): ?string
    {
        $stmt = $this->db->prepare(
            "SELECT valor FROM escolhas WHERE personagem_id = :p AND codigo = :c LIMIT 1"
        );
        $stmt->execute(['p' => $personagemId, 'c' => $codigo]);
        $v = $stmt->fetchColumn();
        return $v === false ? null : (string) $v;
    }
}
