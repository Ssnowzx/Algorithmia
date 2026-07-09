<?php

declare(strict_types=1);

namespace Tests\Suporte;

use App\Dominio\Combate\SorteadorDeDesafios;
use App\Models\Fase;
use App\Models\Personagem;

/**
 * Sequência de desafios fixa, na ordem dada.
 *
 * O sorteio real embaralha, e amarrar as asserções à permutação do Mt19937
 * travaria a IMPLEMENTAÇÃO do sorteio em vez das REGRAS de combate. Fixando a
 * sequência, os vetores-ouro medem só o que precisa sobreviver ao port: dano,
 * combo, especial, fúria, XP, ouro, reputação, estrelas e conquistas.
 *
 * As invariantes do sorteio em si são cobertas à parte, sem RNG, em SorteioTest.
 */
final class SorteioFixo implements SorteadorDeDesafios
{
    /** @param  list<array<string,mixed>>  $sequencia */
    public function __construct(
        private readonly array $sequencia,
        private readonly ?int $limite = null,
    ) {}

    public function sortear(Personagem $personagem, Fase $fase): array
    {
        return [
            'lista' => $this->sequencia,
            'limite' => $this->limite ?? count($this->sequencia),
        ];
    }
}
