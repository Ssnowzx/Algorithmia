<?php

declare(strict_types=1);

namespace App\Dominio\Progressao;

use App\Models\ProgressoFase;
use App\Models\RespostaLog;
use Illuminate\Support\Facades\Date;

/**
 * Missões da semana: um conjunto rotativo do pool, avaliado contra a atividade da
 * semana ISO corrente.
 *
 * Read-only. Não concede recompensa, não persiste nada, não precisa de cron: a
 * seleção é determinística pela semana, e o progresso é derivado do log de
 * respostas na hora de mostrar.
 *
 * @phpstan-type Missao array{codigo:string,titulo:string,icone:string,metrica:string,alvo:int,desc:string,min?:int}
 * @phpstan-type Metricas array{respostas:int,acertos:int,respostas_sem_ia:int,acertos_sem_ia:int,materias:int,fases:int}
 */
final class ServicoDeMissoes
{
    /** Índice único e crescente por semana ISO. Mesma semana, mesmo conjunto. */
    public static function indiceSemanaAtual(): int
    {
        $agora = Date::now();

        return $agora->isoWeekYear * 53 + $agora->isoWeek;
    }

    /**
     * Escolhe as missões da semana de forma determinística. Pura.
     *
     * @return list<Missao>
     */
    public static function selecionar(int $indiceSemana): array
    {
        /** @var list<Missao> $pool */
        $pool = config('jogo.missoes_semanais');
        $n = count($pool);
        if ($n === 0) {
            return [];
        }

        $quantas = min((int) config('jogo.missoes_por_semana'), $n);
        // O módulo duplo mantém a base não-negativa mesmo com índice negativo.
        $base = (($indiceSemana % $n) + $n) % $n;

        $selecionadas = [];
        for ($i = 0; $i < $quantas; $i++) {
            $selecionadas[] = $pool[($base + $i) % $n];
        }

        return $selecionadas;
    }

    /**
     * Avalia uma missão contra as métricas da semana. Pura.
     *
     * `precisao` é o caso especial: enquanto o volume não chega ao mínimo, a barra
     * mede o volume; depois passa a medir a precisão. Uma barra de precisão cheia
     * com duas respostas não mediria nada.
     *
     * @param  Missao  $missao
     * @param  Metricas  $metricas
     * @return array{codigo:string,titulo:string,desc:string,icone:string,atual:int,alvo:int,unidade:string,pct:int,completa:bool,nota:string}
     */
    public static function avaliar(array $missao, array $metricas): array
    {
        if ($missao['metrica'] === 'precisao') {
            return self::avaliarPrecisao($missao, $metricas);
        }

        $alvo = $missao['alvo'];
        $atual = (int) ($metricas[$missao['metrica']] ?? 0);
        $pct = $alvo > 0 ? (int) round(min(100, max(0, $atual / $alvo * 100))) : 0;

        return self::resultado($missao, $atual, $alvo, '', $pct, $atual >= $alvo, '');
    }

    /**
     * As missões da semana de um personagem.
     *
     * @return list<array<string,mixed>>
     */
    public static function daSemana(int $personagemId): array
    {
        $metricas = RespostaLog::metricasSemana($personagemId);
        $metricas['fases'] = ProgressoFase::fasesSemana($personagemId);

        return array_map(
            static fn (array $missao): array => self::avaliar($missao, $metricas),
            self::selecionar(self::indiceSemanaAtual())
        );
    }

    /** @param  list<array<string,mixed>>  $missoes */
    public static function totalCompletas(array $missoes): int
    {
        return count(array_filter($missoes, static fn (array $m): bool => (bool) $m['completa']));
    }

    /**
     * @param  Missao  $missao
     * @param  Metricas  $metricas
     * @return array<string,mixed>
     */
    private static function avaliarPrecisao(array $missao, array $metricas): array
    {
        $minimo = $missao['min'] ?? 1;
        $respostas = $metricas['respostas'];
        $acertos = $metricas['acertos'];
        $precisao = $respostas > 0 ? (int) round($acertos / $respostas * 100) : 0;

        if ($respostas < $minimo) {
            $atual = $respostas;
            $alvo = $minimo;
            $unidade = 'respostas';
            $completa = false;
            $nota = "responda {$minimo} p/ valer";
        } else {
            $atual = $precisao;
            $alvo = $missao['alvo'];
            $unidade = '%';
            $completa = $precisao >= $alvo;
            $nota = '';
        }

        $pct = $alvo > 0 ? (int) round(min(100, $atual / $alvo * 100)) : 0;

        return self::resultado($missao, $atual, $alvo, $unidade, $pct, $completa, $nota);
    }

    /**
     * @param  Missao  $missao
     * @return array<string,mixed>
     */
    private static function resultado(array $missao, int $atual, int $alvo, string $unidade, int $pct, bool $completa, string $nota): array
    {
        return [
            'codigo' => $missao['codigo'],
            'titulo' => $missao['titulo'],
            'desc' => $missao['desc'],
            'icone' => $missao['icone'],
            'atual' => $atual,
            'alvo' => $alvo,
            'unidade' => $unidade,
            'pct' => $pct,
            'completa' => $completa,
            'nota' => $nota,
        ];
    }
}
