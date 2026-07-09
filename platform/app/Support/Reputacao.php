<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Traduz o número da reputação no título que o jogador carrega.
 *
 * O número cru (−100 a 100) não diz nada a quem joga; "Tentado pelo Atalho" diz
 * tudo. Porte de `rotuloReputacao()` de `app/core/helpers.php`.
 */
final class Reputacao
{
    public static function rotulo(int $reputacao): string
    {
        /** @var list<array{ate:int,rotulo:string}> $faixas */
        $faixas = config('finais.rotulos_de_reputacao');

        foreach ($faixas as $faixa) {
            if ($reputacao <= $faixa['ate']) {
                return $faixa['rotulo'];
            }
        }

        return end($faixas)['rotulo'];
    }
}
