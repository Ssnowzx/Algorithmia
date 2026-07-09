<?php

declare(strict_types=1);

namespace App\Support;

/**
 * "Leitura do inimigo": diz ao jogador qual equipamento ajuda naquela fase, sem
 * empurrar a loja.
 *
 * Devolve string vazia para inimigos comuns — de propósito. Um aviso em toda fase
 * não avisa nada; o silêncio é o que dá peso ao aviso quando ele vem.
 *
 * A frase inteira vai na arena; a `tag()` curta cabe num chip do mapa, com a
 * frase no tooltip.
 */
final class LeituraDoInimigo
{
    public static function frase(int $hp, int $ataque): string
    {
        return match (self::ameaca($hp, $ataque)) {
            'ambos' => 'Resistente e violento: vá com ataque E defesa elevados — ou vire estatística.',
            'resistente' => 'Couro grosso, muito HP: ataque elevado ajuda a derrubá-lo a tempo.',
            'brutal' => 'Golpes pesados: defesa elevada faz cada erro doer bem menos.',
            default => '',
        };
    }

    public static function tag(int $hp, int $ataque): string
    {
        return match (self::ameaca($hp, $ataque)) {
            'ambos' => '⚠ Leve ataque e defesa',
            'resistente' => '⚔ Leve ataque',
            'brutal' => '🛡 Leve defesa',
            default => '',
        };
    }

    /**
     * "Poder Total": um número único que resume o quão forte o herói está.
     *
     * Soma simples de ataque e defesa — os dois atributos que a loja afeta — para
     * que o jogador veja o número subir ao equipar. É a métrica que dá sentido a
     * comprar.
     */
    public static function poderTotal(int $ataque, int $defesa): int
    {
        return $ataque + $defesa;
    }

    private static function ameaca(int $hp, int $ataque): string
    {
        $resistente = $hp >= (int) config('jogo.inimigo_hp_alto');
        $brutal = $ataque >= (int) config('jogo.inimigo_ataque_alto');

        return match (true) {
            $resistente && $brutal => 'ambos',
            $resistente => 'resistente',
            $brutal => 'brutal',
            default => 'nenhuma',
        };
    }
}
