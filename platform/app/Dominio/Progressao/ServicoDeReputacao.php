<?php

declare(strict_types=1);

namespace App\Dominio\Progressao;

use App\Models\Personagem;

/**
 * Eixo Disciplina (+) vs. IA (−). Cai a cada uso do Fragmento e sobe ao vencer
 * sem ele. Determina variantes de diálogo e qual dos três finais o jogador vê.
 */
final class ServicoDeReputacao
{
    /** Aplica uma variação, limitando ao intervalo permitido. Devolve o novo valor. */
    public function ajustar(Personagem $personagem, int $delta): int
    {
        // Relê do banco: o valor em memória pode estar velho se outro efeito do
        // mesmo turno já mexeu na reputação.
        $atual = (int) Personagem::query()->whereKey($personagem->id)->value('reputacao');

        $novo = max(
            (int) config('jogo.reputacao.min'),
            min((int) config('jogo.reputacao.max'), $atual + $delta)
        );

        $personagem->update(['reputacao' => $novo]);

        return $novo;
    }

    /** Reputação negativa o aproxima do caminho da IA. */
    public function variante(Personagem $personagem): string
    {
        return $personagem->reputacao <= -20 ? 'ia' : 'padrao';
    }
}
