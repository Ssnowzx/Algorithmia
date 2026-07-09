<?php

declare(strict_types=1);

namespace App\Dominio\Combate;

use App\Models\Fase;
use App\Models\Personagem;

/**
 * Escolhe quais perguntas entram numa batalha.
 *
 * É uma interface, e não um método privado, porque o sorteio é a única parte não
 * determinística do motor. Injetá-lo permite que os vetores-ouro fixem a
 * sequência e meçam só o que precisa sobreviver ao port: a aritmética do combate.
 */
interface SorteadorDeDesafios
{
    /**
     * @return array{lista:list<array<string,mixed>>,limite:int} `lista` é a sequência
     *                                                           inteira jogável; `limite` é o número de perguntas antes do Duelo Final.
     */
    public function sortear(Personagem $personagem, Fase $fase): array;
}
