<?php
/**
 * Professores-NPC. Cada um governa uma região/capítulo do mapa.
 */
class Mestre extends Model
{
    protected string $table = 'mestres';

    public function todosOrdenados(): array
    {
        return $this->findAll('ordem ASC');
    }
}
