<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Cada resposta dada, honesta ou comprada com o Fragmento da IA.
 * Alimenta a maestria por matéria, as missões e o anti-repetição do sorteio.
 *
 * @property int $personagem_id
 * @property int $desafio_id
 * @property bool $correta
 * @property bool $usou_ia
 */
final class RespostaLog extends Model
{
    protected $table = 'respostas_log';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = ['personagem_id', 'desafio_id', 'correta', 'usou_ia'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return ['correta' => 'boolean', 'usou_ia' => 'boolean'];
    }

    public static function registrar(int $personagemId, int $desafioId, bool $correta, bool $usouIa): void
    {
        self::query()->create([
            'personagem_id' => $personagemId,
            'desafio_id' => $desafioId,
            'correta' => $correta,
            'usou_ia' => $usouIa,
        ]);
    }

    /**
     * Total e acertos por matéria, de sempre.
     *
     * O `SUM(r.correta)` do legado somava 0/1; no PostgreSQL `correta` é boolean e
     * não se soma. `COUNT(*) FILTER (WHERE ...)` diz a mesma coisa, e melhor.
     *
     * @return array<string,array{total:int,acertos:int}>
     */
    public static function estatisticasPorAssunto(int $personagemId): array
    {
        // Query builder, e não Eloquent: `assunto`, `total` e `acertos` vêm do
        // join e da agregação, e não existem como colunas deste model.
        $linhas = DB::table('respostas_log')
            ->join('desafios', 'desafios.id', '=', 'respostas_log.desafio_id')
            ->where('respostas_log.personagem_id', $personagemId)
            ->groupBy('desafios.assunto')
            ->get([
                'desafios.assunto',
                DB::raw('COUNT(*) AS total'),
                DB::raw('COUNT(*) FILTER (WHERE respostas_log.correta) AS acertos'),
            ]);

        $estatisticas = [];
        foreach ($linhas as $linha) {
            $estatisticas[(string) $linha->assunto] = [
                'total' => (int) $linha->total,
                'acertos' => (int) $linha->acertos,
            ];
        }

        return $estatisticas;
    }

    /**
     * Métricas da semana ISO corrente, para as missões.
     *
     * O legado comparava `YEARWEEK(x, 3) = YEARWEEK(NOW(), 3)`, que não existe no
     * PostgreSQL. `date_trunc('week', …)` começa na segunda-feira, que é exatamente
     * o que o modo 3 do MySQL significa.
     *
     * @return array{respostas:int,acertos:int,respostas_sem_ia:int,acertos_sem_ia:int,materias:int}
     */
    public static function metricasSemana(int $personagemId): array
    {
        $linha = DB::table('respostas_log')
            ->join('desafios', 'desafios.id', '=', 'respostas_log.desafio_id')
            ->where('respostas_log.personagem_id', $personagemId)
            ->whereRaw("date_trunc('week', respostas_log.respondido_em) = date_trunc('week', now())")
            ->first([
                DB::raw('COUNT(*) AS respostas'),
                DB::raw('COUNT(*) FILTER (WHERE respostas_log.correta) AS acertos'),
                DB::raw('COUNT(*) FILTER (WHERE NOT respostas_log.usou_ia) AS respostas_sem_ia'),
                DB::raw('COUNT(*) FILTER (WHERE NOT respostas_log.usou_ia AND respostas_log.correta) AS acertos_sem_ia'),
                DB::raw('COUNT(DISTINCT desafios.assunto) AS materias'),
            ]);

        return [
            'respostas' => (int) ($linha->respostas ?? 0),
            'acertos' => (int) ($linha->acertos ?? 0),
            'respostas_sem_ia' => (int) ($linha->respostas_sem_ia ?? 0),
            'acertos_sem_ia' => (int) ($linha->acertos_sem_ia ?? 0),
            'materias' => (int) ($linha->materias ?? 0),
        ];
    }
}
