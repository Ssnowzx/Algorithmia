<?php
/**
 * Avatar do jogador. Concentra os atributos de RPG e o progresso narrativo.
 */
class Personagem extends Model
{
    protected string $table = 'personagens';

    /**
     * Ranking por XP (com nome do dono para exibição).
     */
    public function ranking(int $limite = 50): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, u.nome AS nome_usuario
             FROM personagens p
             JOIN usuarios u ON u.id = p.usuario_id
             ORDER BY p.nivel DESC, p.xp DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ajusta HP mantendo-o entre 0 e o máximo.
     */
    public function ajustarVida(int $personagemId, int $delta): int
    {
        $p = $this->findById($personagemId);
        $novo = max(0, min((int) $p['hp_max'], (int) $p['hp_atual'] + $delta));
        $this->update($personagemId, ['hp_atual' => $novo]);
        return $novo;
    }
}
