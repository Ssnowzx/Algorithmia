<?php

declare(strict_types=1);

namespace App\Dominio\Progressao;

use App\Dominio\Combate\EstadoDeBatalha;

/**
 * O desfecho de uma batalha, reduzido ao que a recompensa precisa saber.
 *
 * O `batalhaId` é a chave de idempotência: ele nasce no motor, nunca vem do
 * cliente, e é o que impede que um replay do POST de vitória credite duas vezes.
 */
final readonly class ResultadoDaBatalha
{
    public function __construct(
        public string $batalhaId,
        public int $faseId,
        public int $acertos,
        public int $erros,
        public bool $usouIa,
    ) {}

    public static function de(EstadoDeBatalha $estado): self
    {
        return new self(
            batalhaId: $estado->batalhaId,
            faseId: $estado->faseId,
            acertos: $estado->acertos,
            erros: $estado->erros,
            usouIa: $estado->usouIa,
        );
    }
}
