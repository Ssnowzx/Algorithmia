<?php
/**
 * Maestria por matéria: traduz (total, acertos) de uma matéria numa FAIXA de
 * domínio + o progresso rumo à próxima (goal-gradient). Lógica PURA e read-only
 * — não toca banco; consome as estatísticas que o perfil já calcula
 * (RespostaLog::estatisticasPorAssunto). A escada de faixas vem de
 * MAESTRIA_FAIXAS (config.php), fonte única compartilhada com a view.
 *
 * Domínio = volume de acertos + precisão sustentada (não a % bruta), para que
 * "Mestre" signifique competência consistente, e não sorte com poucas respostas.
 */
class MaestriaService
{
    /**
     * Faixa de maestria de UMA matéria.
     *
     * @return array{
     *   tier:int, rotulo:string, icone:string, cor:string,
     *   total:int, acertos:int, precisao:int, maximo:bool, dominada:bool,
     *   proximo: array{rotulo:string, pct:int, dica:string}|null
     * }
     */
    public static function faixaDe(int $total, int $acertos): array
    {
        $faixas   = MAESTRIA_FAIXAS;
        $total    = max(0, $total);
        $acertos  = max(0, min($acertos, $total));      // acertos nunca > total
        $precisao = $total > 0 ? $acertos / $total : 0.0;

        // Maior tier cujos gates (mín. de acertos E piso de precisão) são
        // satisfeitos. total==0 => tier 0; total>=1 garante ao menos tier 1.
        // Como min_acertos e piso são monotônicos não-decrescentes, basta subir
        // enquanto passa: ao falhar numa faixa, as seguintes (mais exigentes)
        // também falhariam.
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

        $atual  = $faixas[$tier];
        $maximo = $tier >= (count($faixas) - 1);

        return [
            'tier'     => $tier,
            'rotulo'   => $atual['rotulo'],
            'icone'    => $atual['icone'],
            'cor'      => $atual['cor'],
            'total'    => $total,
            'acertos'  => $acertos,
            'precisao' => (int) round($precisao * 100),
            'maximo'   => $maximo,
            'dominada' => $tier >= MAESTRIA_TIER_DOMINADA,
            // tier 0 (nada respondido): Iniciante não exige acertos, então a meta
            // é simplesmente começar — a fórmula de acertos/precisão não se aplica.
            'proximo'  => $maximo
                ? null
                : ($tier === 0
                    ? ['rotulo' => $faixas[1]['rotulo'], 'pct' => 0, 'dica' => 'Responda para começar']
                    : self::proximo($faixas, $tier, $acertos, $precisao)),
        ];
    }

    /**
     * Progresso (0..100) e dica rumo ao PRÓXIMO tier. A barra reflete o fator
     * mais ATRASADO entre acumular acertos e sustentar precisão, para nunca
     * "encher" enganando (ex.: muitos acertos mas precisão abaixo do piso).
     *
     * @param list<array{rotulo:string,icone:string,cor:string,min_acertos:int,piso:float}> $faixas
     * @return array{rotulo:string, pct:int, dica:string}
     */
    private static function proximo(array $faixas, int $tier, int $acertos, float $precisao): array
    {
        $prox  = $faixas[$tier + 1];
        $minAc = (int) $prox['min_acertos'];
        $piso  = (float) $prox['piso'];

        $progAc   = $minAc > 0 ? $acertos / $minAc : 1.0;
        $progPrec = $piso  > 0 ? $precisao / $piso : 1.0;
        $pct = (int) round(100 * max(0.0, min(1.0, min($progAc, $progPrec))));

        $faltamAc  = max(0, $minAc - $acertos);
        $precFalta = $precisao < $piso;
        $pisoPct   = (int) round($piso * 100);

        if ($faltamAc > 0) {
            $dica = "Faltam {$faltamAc} p/ {$prox['rotulo']}";
            if ($precFalta) {
                $dica .= " · precisão ≥{$pisoPct}%";
            }
        } elseif ($precFalta) {
            $precPct = (int) round($precisao * 100);
            $dica = "Precisão {$precPct}% → suba p/ {$pisoPct}% e vire {$prox['rotulo']}";
        } else {
            $dica = "Quase {$prox['rotulo']}";
        }

        return ['rotulo' => $prox['rotulo'], 'pct' => $pct, 'dica' => $dica];
    }

    /**
     * Maestria de TODAS as matérias (ASSUNTOS), na ordem do catálogo, a partir
     * das estatísticas já agregadas pelo perfil:
     *   ['php' => ['total'=>x,'acertos'=>y], ...]  (matérias sem resposta omitidas)
     * As ausentes entram como 0/0 (Não iniciado). Defensivo: qualquer falha
     * devolve [] e a view cai no fallback (texto atual), nunca quebra o perfil.
     *
     * @param array<string,array{total:int,acertos:int}> $estatisticas
     * @return array<string,array{rotulo:string, faixa:array}>
     */
    public static function porMateria(array $estatisticas): array
    {
        try {
            $out = [];
            foreach (ASSUNTOS as $chave => $rotulo) {
                $st = $estatisticas[$chave] ?? [];
                $out[$chave] = [
                    'rotulo' => $rotulo,
                    'faixa'  => self::faixaDe((int) ($st['total'] ?? 0), (int) ($st['acertos'] ?? 0)),
                ];
            }
            return $out;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /** Quantas matérias já contam como "dominadas" (tier >= MAESTRIA_TIER_DOMINADA). */
    public static function totalDominadas(array $maestria): int
    {
        $n = 0;
        foreach ($maestria as $m) {
            if (!empty($m['faixa']['dominada'])) {
                $n++;
            }
        }
        return $n;
    }
}
