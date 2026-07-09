<?php

declare(strict_types=1);

namespace App\Dominio\Combate;

/**
 * Onde a batalha em andamento vive entre um turno e o próximo.
 *
 * O motor não conhece `$_SESSION`. Essa costura é o que permite testá-lo sem
 * servidor web — e o que deixará trocar sessão por banco, ou por um cache com
 * TTL, sem tocar em uma linha de regra de combate.
 */
interface RepositorioDeBatalha
{
    public function carregar(): ?EstadoDeBatalha;

    public function salvar(EstadoDeBatalha $estado): void;

    public function limpar(): void;
}
