<?php
/**
 * Catálogo de itens (armas, escudos, acessórios, poções, especiais).
 */
class Item extends Model
{
    protected string $table = 'itens';

    public function compraveis(): array
    {
        return $this->where('compravel', 1, 'preco ASC');
    }

    /**
     * Decodifica o campo JSON 'efeito'.
     */
    public static function efeito(array $item): array
    {
        return $item['efeito'] ? json_decode($item['efeito'], true) : [];
    }
}
