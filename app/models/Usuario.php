<?php
/**
 * Conta de acesso. Pode ser 'jogador' ou 'mestre' (admin).
 */
class Usuario extends Model
{
    protected string $table = 'usuarios';

    public function emailExiste(string $email): bool
    {
        return $this->findBy('email', $email) !== null;
    }
}
