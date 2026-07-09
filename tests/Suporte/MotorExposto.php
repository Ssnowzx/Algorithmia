<?php

declare(strict_types=1);

namespace Algorithmia\Testes\Suporte;

/**
 * Expõe o sorteio de desafios para que suas invariantes possam ser testadas
 * diretamente, sem passar por uma batalha inteira.
 */
final class MotorExposto extends \BatalhaService
{
    /**
     * @param array<string,mixed> $personagem
     * @param array<string,mixed> $fase
     * @return array{lista:list<array<string,mixed>>,limite:int}
     */
    public function sortear(array $personagem, array $fase): array
    {
        return $this->sortearDesafios($personagem, $fase);
    }
}
