<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $nome
 * @property string $svg_slug
 * @property int $ordem
 */
final class Mestre extends Model
{
    protected $table = 'mestres';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = ['nome', 'titulo', 'disciplina', 'regiao', 'historia', 'personalidade', 'bordao', 'svg_slug', 'cor_tema', 'ordem'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return ['ordem' => 'integer'];
    }

    /**
     * Progresso de cada região: fases principais, concluídas, perfeitas e estrelas.
     *
     * Só lição e chefe contam — as secundárias são opcionais e não podem impedir o
     * domínio de uma região. O `SUM(pf.estrelas = 3)` do legado somava um booleano
     * como inteiro, o que o PostgreSQL recusa; vira `COUNT(*) FILTER`.
     *
     * @return list<object{regiao:string,titulo:string,cor_tema:string,svg_slug:string,total:int,concluidas:int,estrelas:int,perfeitas:int}>
     */
    public static function progressoPorRegiao(int $personagemId): array
    {
        return DB::table('fases')
            ->join('mestres', 'mestres.id', '=', 'fases.mestre_id')
            ->leftJoin('progresso_fases', function ($juncao) use ($personagemId): void {
                $juncao->on('progresso_fases.fase_id', '=', 'fases.id')
                    ->where('progresso_fases.personagem_id', '=', $personagemId);
            })
            ->whereNotNull('fases.mestre_id')
            ->whereIn('fases.tipo', ['licao', 'chefe'])
            ->groupBy('mestres.id', 'mestres.ordem', 'mestres.regiao', 'mestres.titulo', 'mestres.cor_tema', 'mestres.svg_slug')
            ->orderBy('mestres.ordem')
            ->get([
                'mestres.regiao', 'mestres.titulo', 'mestres.cor_tema', 'mestres.svg_slug',
                DB::raw('COUNT(fases.id) AS total'),
                DB::raw('COUNT(progresso_fases.fase_id) AS concluidas'),
                DB::raw('COALESCE(SUM(progresso_fases.estrelas), 0) AS estrelas'),
                DB::raw('COUNT(*) FILTER (WHERE progresso_fases.estrelas = 3) AS perfeitas'),
            ])
            ->all();
    }

    /** Código da conquista de "discípulo" desta região, se houver. */
    public function conquistaDaRegiao(): ?string
    {
        $codigo = config("jogo.regioes_mestre.{$this->svg_slug}.conquista");

        return is_string($codigo) ? $codigo : null;
    }
}
