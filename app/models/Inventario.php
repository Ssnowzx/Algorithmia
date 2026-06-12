<?php
/**
 * Itens que cada personagem possui. Faz join com itens para exibição.
 */
class Inventario extends Model
{
    protected string $table = 'inventario';

    /**
     * Inventário detalhado de um personagem (com dados do item).
     */
    public function doPersonagem(int $personagemId): array
    {
        $stmt = $this->db->prepare(
            "SELECT inv.*, i.nome, i.descricao, i.tipo, i.efeito, i.svg_slug, i.raridade, i.preco
             FROM inventario inv
             JOIN itens i ON i.id = inv.item_id
             WHERE inv.personagem_id = :pid
             ORDER BY FIELD(i.tipo,'arma','escudo','acessorio','pocao','especial'), i.nome"
        );
        $stmt->execute(['pid' => $personagemId]);
        return $stmt->fetchAll();
    }

    /**
     * Linha de inventário para um item específico, ou null.
     */
    public function pegar(int $personagemId, int $itemId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM inventario WHERE personagem_id = :p AND item_id = :i"
        );
        $stmt->execute(['p' => $personagemId, 'i' => $itemId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Adiciona um item (ou incrementa a quantidade se já existir).
     */
    public function adicionar(int $personagemId, int $itemId, int $qtd = 1): void
    {
        $existente = $this->pegar($personagemId, $itemId);
        if ($existente) {
            $this->update((int) $existente['id'], ['quantidade' => (int) $existente['quantidade'] + $qtd]);
        } else {
            $this->create(['personagem_id' => $personagemId, 'item_id' => $itemId, 'quantidade' => $qtd]);
        }
    }

    /**
     * Remove uma unidade; apaga a linha quando zera.
     */
    public function remover(int $personagemId, int $itemId, int $qtd = 1): void
    {
        $existente = $this->pegar($personagemId, $itemId);
        if (!$existente) {
            return;
        }
        $nova = (int) $existente['quantidade'] - $qtd;
        if ($nova > 0) {
            $this->update((int) $existente['id'], ['quantidade' => $nova]);
        } else {
            $this->delete((int) $existente['id']);
        }
    }

    /**
     * Conta quantas unidades de um item o personagem tem.
     */
    public function quantidade(int $personagemId, int $itemId): int
    {
        $linha = $this->pegar($personagemId, $itemId);
        return $linha ? (int) $linha['quantidade'] : 0;
    }
}
