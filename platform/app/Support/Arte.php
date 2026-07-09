<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Resolve o caminho de uma arte a partir do seu slug.
 *
 * O banco guarda slugs (`heroi-mago`, `inimigo-bug`, `item-espada`), nunca
 * caminhos: a mesma arte muda de formato entre versões, e um caminho gravado em
 * `seeds.sql` viraria link quebrado. A extensão é escolhida aqui, preferindo
 * WebP quando existe — as cópias PNG continuam sendo a fonte.
 */
final class Arte
{
    private const EXTENSOES = ['webp', 'png', 'svg', 'jpg'];

    /** Cache por requisição: o mapa desenha 35 fases e consultaria o disco 35 vezes. */
    /** @var array<string,string|null> */
    private static array $memo = [];

    /** URL da arte, ou null se nenhuma variante existir. */
    public static function src(string $caminho): ?string
    {
        return self::$memo[$caminho] ??= self::procurar($caminho);
    }

    /** Como src(), mas cai num placeholder em vez de sumir da tela. */
    public static function srcOu(string $caminho, string $reserva): string
    {
        return self::src($caminho) ?? self::src($reserva) ?? '';
    }

    private static function procurar(string $caminho): ?string
    {
        foreach (self::EXTENSOES as $extensao) {
            $relativo = "img/{$caminho}.{$extensao}";
            if (is_file(public_path($relativo))) {
                // filemtime no query string: cache longo sem servir arte velha.
                return asset($relativo).'?v='.filemtime(public_path($relativo));
            }
        }

        return null;
    }
}
