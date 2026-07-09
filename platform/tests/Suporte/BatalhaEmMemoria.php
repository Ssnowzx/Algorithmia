<?php

declare(strict_types=1);

namespace Tests\Suporte;

use App\Dominio\Combate\EstadoDeBatalha;
use App\Dominio\Combate\RepositorioDeBatalha;

/**
 * Guarda a batalha num campo, não na sessão. É o que permite exercitar o motor
 * inteiro sem levantar uma requisição HTTP.
 */
final class BatalhaEmMemoria implements RepositorioDeBatalha
{
    private ?EstadoDeBatalha $estado = null;

    public function carregar(): ?EstadoDeBatalha
    {
        return $this->estado;
    }

    public function salvar(EstadoDeBatalha $estado): void
    {
        $this->estado = $estado;
    }

    public function limpar(): void
    {
        $this->estado = null;
    }
}
