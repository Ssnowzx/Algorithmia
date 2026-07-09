<?php

declare(strict_types=1);

namespace App\Dominio\Progressao;

/**
 * Maestria por matéria: traduz (total, acertos) numa FAIXA de domínio e no
 * progresso rumo à próxima. Lógica pura — não toca no banco.
 *
 * Domínio é volume de acertos **mais** precisão sustentada, e não a porcentagem
 * bruta: "Mestre" tem de significar competência consistente, não sorte com três
 * respostas.
 *
 * @phpstan-type Faixa array{rotulo:string,icone:string,cor:string,min_acertos:int,piso:float}
 */
final class ServicoDeMaestria
{
    /**
     * @return array{tier:int,rotulo:string,icone:string,cor:string,total:int,acertos:int,
     *   precisao:int,maximo:bool,dominada:bool,proximo:array{rotulo:string,pct:int,dica:string}|null}
     */
    public static function faixaDe(int $total, int $acertos): array
    {
        /** @var list<Faixa> $faixas */
        $faixas = config('jogo.maestria_faixas');

        $total = max(0, $total);
        $acertos = max(0, min($acertos, $total)); // acertos nunca excedem o total
        $precisao = $total > 0 ? $acertos / $total : 0.0;

        // Maior tier cujos dois portões (mínimo de acertos E piso de precisão) são
        // satisfeitos. Como ambos crescem monotonicamente, basta subir enquanto
        // passa: ao falhar numa faixa, as seguintes falhariam também.
        $tier = 0;
        if ($total >= 1) {
            $tier = 1;
            for ($t = 2, $n = count($faixas); $t < $n; $t++) {
                if ($acertos >= $faixas[$t]['min_acertos'] && $precisao >= $faixas[$t]['piso']) {
                    $tier = $t;
                } else {
                    break;
                }
            }
        }

        $atual = $faixas[$tier];
        $maximo = $tier >= count($faixas) - 1;

        return [
            'tier' => $tier,
            'rotulo' => $atual['rotulo'],
            'icone' => $atual['icone'],
            'cor' => $atual['cor'],
            'total' => $total,
            'acertos' => $acertos,
            'precisao' => (int) round($precisao * 100),
            'maximo' => $maximo,
            'dominada' => $tier >= (int) config('jogo.maestria_tier_dominada'),
            // Tier 0 significa nada respondido: a meta é simplesmente começar, e a
            // fórmula de acertos/precisão não se aplica.
            'proximo' => match (true) {
                $maximo => null,
                $tier === 0 => ['rotulo' => $faixas[1]['rotulo'], 'pct' => 0, 'dica' => 'Responda para começar'],
                default => self::proximo($faixas, $tier, $acertos, $precisao),
            },
        ];
    }

    /**
     * Maestria de todas as matérias, na ordem do catálogo. As sem resposta entram
     * como 0/0 (Não iniciado).
     *
     * @param  array<string,array{total:int,acertos:int}>  $estatisticas
     * @return array<string,array{rotulo:string,faixa:array<string,mixed>}>
     */
    public static function porMateria(array $estatisticas): array
    {
        $resumo = [];

        /** @var array<string,string> $assuntos */
        $assuntos = config('jogo.assuntos_rotulos');

        foreach ($assuntos as $chave => $rotulo) {
            $estatistica = $estatisticas[$chave] ?? ['total' => 0, 'acertos' => 0];
            $resumo[$chave] = [
                'rotulo' => $rotulo,
                'faixa' => self::faixaDe($estatistica['total'], $estatistica['acertos']),
            ];
        }

        return $resumo;
    }

    /** @param  array<string,array{rotulo:string,faixa:array<string,mixed>}>  $maestria */
    public static function totalDominadas(array $maestria): int
    {
        return count(array_filter($maestria, static fn (array $m): bool => (bool) $m['faixa']['dominada']));
    }

    /**
     * Progresso e dica rumo ao próximo tier.
     *
     * A barra reflete o fator mais ATRASADO entre acumular acertos e sustentar
     * precisão. Se refletisse só os acertos, ela encheria enquanto a precisão
     * despencava — uma barra que mente.
     *
     * @param  list<Faixa>  $faixas
     * @return array{rotulo:string,pct:int,dica:string}
     */
    private static function proximo(array $faixas, int $tier, int $acertos, float $precisao): array
    {
        $proximo = $faixas[$tier + 1];
        $minimoAcertos = $proximo['min_acertos'];
        $piso = $proximo['piso'];

        $progressoAcertos = $minimoAcertos > 0 ? $acertos / $minimoAcertos : 1.0;
        $progressoPrecisao = $piso > 0.0 ? $precisao / $piso : 1.0;
        $pct = (int) round(100 * max(0.0, min(1.0, min($progressoAcertos, $progressoPrecisao))));

        $faltamAcertos = max(0, $minimoAcertos - $acertos);
        $precisaoBaixa = $precisao < $piso;
        $pisoPct = (int) round($piso * 100);

        $dica = match (true) {
            $faltamAcertos > 0 => "Faltam {$faltamAcertos} p/ {$proximo['rotulo']}"
                .($precisaoBaixa ? " · precisão ≥{$pisoPct}%" : ''),
            $precisaoBaixa => 'Precisão '.(int) round($precisao * 100)."% → suba p/ {$pisoPct}% e vire {$proximo['rotulo']}",
            default => "Quase {$proximo['rotulo']}",
        };

        return ['rotulo' => $proximo['rotulo'], 'pct' => $pct, 'dica' => $dica];
    }
}
