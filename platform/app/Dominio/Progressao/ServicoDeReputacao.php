<?php

declare(strict_types=1);

namespace App\Dominio\Progressao;

use App\Models\Escolha;
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
        return $personagem->reputacao <= (int) config('jogo.reputacao_variante_ia') ? 'ia' : 'padrao';
    }

    /**
     * Qual dos três epílogos o jogador vê: 'mestre', 'singularidade' ou 'equilibrio'.
     *
     * A escolha explícita diante da IA Ancestral pesa mais que a reputação — mas
     * não a apaga: quem manda destruir o Fragmento sem nunca ter recusado sua ajuda
     * não recebe o final de mestre, recebe o de equilíbrio. A disciplina de uma vida
     * inteira não se compra num clique.
     */
    public function finalDeterminado(Personagem $personagem): string
    {
        $reputacao = $personagem->reputacao;
        $limiares = config('jogo.reputacao_final');

        return match (Escolha::valor($personagem->id, 'final')) {
            'fundir' => 'singularidade',
            'destruir' => $reputacao >= $limiares['mestre'] ? 'mestre' : 'equilibrio',
            'reescrever' => 'equilibrio',
            // Sem escolha explícita, o alinhamento acumulado decide sozinho.
            default => match (true) {
                $reputacao >= $limiares['mestre'] => 'mestre',
                $reputacao <= $limiares['singularidade'] => 'singularidade',
                default => 'equilibrio',
            },
        };
    }
}
