<?php

declare(strict_types=1);

namespace App\Dominio\Combate;

/**
 * Guarda a batalha num campo, não na sessão.
 *
 * É o que permite exercitar o motor inteiro sem uma requisição HTTP: os testes o
 * usam para medir a aritmética, e o comando `algorithmia:smoke` o usa para jogar
 * uma fase real após o deploy, dentro de uma transação que é desfeita.
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
