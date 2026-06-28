<?php
/**
 * Domínio das regiões (maestria HORIZONTAL): traduz o progresso de perfeição de
 * cada região (fases concluídas + estrelas) num ESTADO de domínio + barra. A
 * lógica de faixa é PURA (testável); só dominio() faz I/O (lê o progresso). É a
 * contraparte "jornada" da MaestriaService (que é por matéria/conteúdo).
 *
 * "Dominada" exige perfeição total (todas as fases com 3 estrelas = sem erro e
 * sem IA): o objetivo pós-nível de voltar e refazer com excelência. Read-only:
 * não concede as conquistas de mestre nem grava nada.
 */
class RegiaoService
{
    /**
     * Estado de domínio de UMA região. Pura.
     *
     * @return array{chave:string,rotulo:string,cor:string,total:int,concluidas:int,
     *   perfeitas:int,estrelas:int,max_estrelas:int,pct:int,dominada:bool,dica:string}
     */
    public static function faixaDe(int $total, int $concluidas, int $perfeitas, int $estrelas): array
    {
        $total       = max(0, $total);
        $concluidas  = max(0, min($concluidas, $total));
        $perfeitas   = max(0, min($perfeitas, $concluidas));
        $maxEstrelas = 3 * $total;
        $estrelas    = max(0, min($estrelas, $maxEstrelas));
        $pct = $maxEstrelas > 0 ? (int) round(min(100, $estrelas / $maxEstrelas * 100)) : 0;

        if ($concluidas === 0) {
            $chave = 'a_explorar';
            $dica = 'Entre na região';
        } elseif ($concluidas < $total) {
            $chave = 'em_jornada';
            $faltam = $total - $concluidas;
            $dica = "Faltam {$faltam} " . ($faltam === 1 ? 'fase' : 'fases');
        } elseif ($perfeitas < $total) {
            $chave = 'conquistada';
            $faltam = $total - $perfeitas;
            $dica = "Perfeccione {$faltam} p/ dominar";
        } else {
            $chave = 'dominada';
            $dica = 'Domínio total 👑';
        }

        $faixa = REGIAO_FAIXAS[$chave];
        return [
            'chave'        => $chave,
            'rotulo'       => $faixa['rotulo'],
            'cor'          => $faixa['cor'],
            'total'        => $total,
            'concluidas'   => $concluidas,
            'perfeitas'    => $perfeitas,
            'estrelas'     => $estrelas,
            'max_estrelas' => $maxEstrelas,
            'pct'          => $pct,
            'dominada'     => $chave === 'dominada',
            'dica'         => $dica,
        ];
    }

    /**
     * Domínio de todas as regiões de um personagem. Defensivo: qualquer falha
     * devolve [] e a view omite o painel. Read-only.
     *
     * @return array<int,array{regiao:string,titulo:string,cor_tema:string,svg_slug:string,faixa:array}>
     */
    public static function dominio(int $personagemId): array
    {
        try {
            $out = [];
            foreach ((new Mestre())->progressoPorRegiao($personagemId) as $r) {
                $out[] = [
                    'regiao'   => (string) ($r['regiao'] ?? ''),
                    'titulo'   => (string) ($r['titulo'] ?? ''),
                    'cor_tema' => (string) ($r['cor_tema'] ?: '#7c5cff'),
                    'svg_slug' => (string) ($r['svg_slug'] ?? ''),
                    'faixa'    => self::faixaDe(
                        (int) ($r['total'] ?? 0),
                        (int) ($r['concluidas'] ?? 0),
                        (int) ($r['perfeitas'] ?? 0),
                        (int) ($r['estrelas'] ?? 0)
                    ),
                ];
            }
            return $out;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /** Quantas regiões já estão dominadas (perfeição total). */
    public static function totalDominadas(array $dominio): int
    {
        $n = 0;
        foreach ($dominio as $d) {
            if (!empty($d['faixa']['dominada'])) {
                $n++;
            }
        }
        return $n;
    }

    /** Título culminante quando TODAS as regiões estão dominadas; senão ''. */
    public static function tituloLenda(array $dominio): string
    {
        if (empty($dominio)) {
            return '';
        }
        foreach ($dominio as $d) {
            if (empty($d['faixa']['dominada'])) {
                return '';
            }
        }
        return REGIAO_TITULO_LENDA;
    }
}
