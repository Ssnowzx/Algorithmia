<?php
/**
 * Missões da semana: seleciona um conjunto rotativo do pool MISSOES_SEMANAIS e
 * avalia o progresso de cada uma na SEMANA ISO corrente. A seleção e a avaliação
 * são PURAS (testáveis); só daSemana() faz I/O (lê as métricas). Read-only —
 * não concede recompensa nem grava nada.
 */
class MissaoService
{
    /** Índice inteiro, único e crescente por semana ISO (ano ISO * 53 + semana). */
    public static function indiceSemanaAtual(): int
    {
        return ((int) date('o')) * 53 + ((int) date('W'));
    }

    /**
     * Seleciona MISSOES_POR_SEMANA missões do pool de forma determinística pelo
     * índice da semana (mesma semana => mesmo conjunto, mesma ordem). Pura.
     *
     * @return list<array>
     */
    public static function selecionar(int $indiceSemana): array
    {
        $pool = MISSOES_SEMANAIS;
        $n = count($pool);
        if ($n === 0) {
            return [];
        }
        $k = min((int) MISSOES_POR_SEMANA, $n);
        $base = (($indiceSemana % $n) + $n) % $n; // não-negativo mesmo se índice < 0
        $sel = [];
        for ($i = 0; $i < $k; $i++) {
            $sel[] = $pool[($base + $i) % $n];
        }
        return $sel;
    }

    /**
     * Avalia uma missão contra as métricas da semana. Pura.
     *
     * A maioria das métricas é contável (atual = métrica; completa = atual>=alvo).
     * 'precisao' é especial: enquanto o volume não atinge 'min', a barra mede o
     * volume; depois mede a precisão, e a conclusão exige volume E piso.
     *
     * @param array $missao   item de MISSOES_SEMANAIS
     * @param array $metricas metricasSemana()+['fases'=>int]
     * @return array{codigo:string,titulo:string,desc:string,icone:string,
     *               atual:int,alvo:int,unidade:string,pct:int,completa:bool,nota:string}
     */
    public static function avaliar(array $missao, array $metricas): array
    {
        $metrica = $missao['metrica'] ?? 'respostas';

        if ($metrica === 'precisao') {
            $min       = (int) ($missao['min'] ?? 1);
            $respostas = (int) ($metricas['respostas'] ?? 0);
            $acertos   = (int) ($metricas['acertos'] ?? 0);
            $precisao  = $respostas > 0 ? (int) round($acertos / $respostas * 100) : 0;
            if ($respostas < $min) {
                $atual = $respostas; $alvoEf = $min; $unidade = 'respostas';
                $completa = false; $nota = "responda {$min} p/ valer";
            } else {
                $atual = $precisao; $alvoEf = (int) ($missao['alvo'] ?? 0); $unidade = '%';
                $completa = $precisao >= $alvoEf; $nota = '';
            }
            $pct = $alvoEf > 0 ? (int) round(min(100, $atual / $alvoEf * 100)) : 0;
            return self::resultado($missao, $atual, $alvoEf, $unidade, $pct, $completa, $nota);
        }

        // Métricas contáveis.
        $alvo  = (int) ($missao['alvo'] ?? 0);
        $atual = (int) ($metricas[$metrica] ?? 0);
        $pct = $alvo > 0 ? (int) round(min(100, max(0, $atual / $alvo * 100))) : 0;
        return self::resultado($missao, $atual, $alvo, '', $pct, $atual >= $alvo, '');
    }

    /**
     * Missões da semana avaliadas para um personagem. Defensivo: qualquer falha
     * devolve [] e a view omite o painel. Read-only.
     *
     * @return list<array>
     */
    public static function daSemana(int $personagemId): array
    {
        try {
            $metricas = (new RespostaLog())->metricasSemana($personagemId);
            $metricas['fases'] = (new ProgressoFase())->fasesSemana($personagemId);
            $out = [];
            foreach (self::selecionar(self::indiceSemanaAtual()) as $missao) {
                $out[] = self::avaliar($missao, $metricas);
            }
            return $out;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /** Quantas das missões avaliadas já estão completas. */
    public static function totalCompletas(array $missoes): int
    {
        $n = 0;
        foreach ($missoes as $m) {
            if (!empty($m['completa'])) {
                $n++;
            }
        }
        return $n;
    }

    /** Monta o array de resultado da avaliação (forma única). */
    private static function resultado(array $missao, int $atual, int $alvo, string $unidade, int $pct, bool $completa, string $nota): array
    {
        return [
            'codigo'   => (string) ($missao['codigo'] ?? ''),
            'titulo'   => (string) ($missao['titulo'] ?? ''),
            'desc'     => (string) ($missao['desc'] ?? ''),
            'icone'    => (string) ($missao['icone'] ?? '🎯'),
            'atual'    => $atual,
            'alvo'     => $alvo,
            'unidade'  => $unidade,
            'pct'      => $pct,
            'completa' => $completa,
            'nota'     => $nota,
        ];
    }
}
