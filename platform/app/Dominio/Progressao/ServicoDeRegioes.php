<?php

declare(strict_types=1);

namespace App\Dominio\Progressao;

use App\Models\Mestre;

/**
 * Domínio das regiões — a maestria horizontal, contraparte da maestria por
 * matéria.
 *
 * "Dominada" exige perfeição total: todas as fases com 3 estrelas, o que só
 * acontece sem um único erro e sem tocar no Fragmento da IA. É o objetivo de
 * quem já terminou o jogo e volta para fazer direito.
 *
 * Read-only: não concede as conquistas de mestre nem grava nada.
 */
final class ServicoDeRegioes
{
    /**
     * @return array{chave:string,rotulo:string,cor:string,total:int,concluidas:int,
     *   perfeitas:int,estrelas:int,max_estrelas:int,pct:int,dominada:bool,dica:string}
     */
    public static function faixaDe(int $total, int $concluidas, int $perfeitas, int $estrelas): array
    {
        $total = max(0, $total);
        $concluidas = max(0, min($concluidas, $total));
        $perfeitas = max(0, min($perfeitas, $concluidas));
        $maxEstrelas = 3 * $total;
        $estrelas = max(0, min($estrelas, $maxEstrelas));
        $pct = $maxEstrelas > 0 ? (int) round(min(100, $estrelas / $maxEstrelas * 100)) : 0;

        [$chave, $dica] = match (true) {
            $concluidas === 0 => ['a_explorar', 'Entre na região'],
            $concluidas < $total => ['em_jornada', self::faltam($total - $concluidas, 'fase', 'fases')],
            $perfeitas < $total => ['conquistada', 'Perfeccione '.($total - $perfeitas).' p/ dominar'],
            default => ['dominada', 'Domínio total 👑'],
        };

        /** @var array{rotulo:string,cor:string} $faixa */
        $faixa = config("jogo.regiao_faixas.{$chave}");

        return [
            'chave' => $chave,
            'rotulo' => $faixa['rotulo'],
            'cor' => $faixa['cor'],
            'total' => $total,
            'concluidas' => $concluidas,
            'perfeitas' => $perfeitas,
            'estrelas' => $estrelas,
            'max_estrelas' => $maxEstrelas,
            'pct' => $pct,
            'dominada' => $chave === 'dominada',
            'dica' => $dica,
        ];
    }

    /**
     * Domínio de todas as regiões de um personagem.
     *
     * @return list<array{regiao:string,titulo:string,cor_tema:string,svg_slug:string,faixa:array<string,mixed>}>
     */
    public static function dominio(int $personagemId): array
    {
        return array_map(
            static fn (object $linha): array => [
                'regiao' => (string) $linha->regiao,
                'titulo' => (string) $linha->titulo,
                'cor_tema' => (string) ($linha->cor_tema ?: '#7c5cff'),
                'svg_slug' => (string) $linha->svg_slug,
                'faixa' => self::faixaDe(
                    (int) $linha->total,
                    (int) $linha->concluidas,
                    (int) $linha->perfeitas,
                    (int) $linha->estrelas,
                ),
            ],
            Mestre::progressoPorRegiao($personagemId)
        );
    }

    /** @param  list<array{faixa:array<string,mixed>}>  $dominio */
    public static function totalDominadas(array $dominio): int
    {
        return count(array_filter($dominio, static fn (array $d): bool => (bool) $d['faixa']['dominada']));
    }

    /**
     * O título culminante, quando todas as regiões estão dominadas. String vazia
     * enquanto faltar uma.
     *
     * @param  list<array{faixa:array<string,mixed>}>  $dominio
     */
    public static function tituloLenda(array $dominio): string
    {
        if ($dominio === [] || self::totalDominadas($dominio) < count($dominio)) {
            return '';
        }

        return (string) config('jogo.regiao_titulo_lenda');
    }

    private static function faltam(int $quantidade, string $singular, string $plural): string
    {
        return "Faltam {$quantidade} ".($quantidade === 1 ? $singular : $plural);
    }
}
