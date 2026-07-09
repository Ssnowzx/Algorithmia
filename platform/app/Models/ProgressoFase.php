<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property int $personagem_id
 * @property int $fase_id
 * @property int $estrelas
 * @property int $acertos
 * @property int $erros
 * @property bool $usou_ia
 */
final class ProgressoFase extends Model
{
    protected $table = 'progresso_fases';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = ['personagem_id', 'fase_id', 'estrelas', 'acertos', 'erros', 'usou_ia'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return ['estrelas' => 'integer', 'acertos' => 'integer', 'erros' => 'integer', 'usou_ia' => 'boolean'];
    }

    /**
     * Registra (ou melhora) o resultado de uma fase.
     *
     * Só as ESTRELAS acumulam o melhor de sempre. `acertos`, `erros` e `usou_ia`
     * refletem a última partida — é o que o legado faz, e não é acidente: rejogar
     * uma fase limpa apaga a mancha do Fragmento da IA, e isso é o que permite
     * reconquistar "Puro de Coração" depois de ter cedido à tentação. Redenção é
     * uma regra do jogo, não um bug.
     *
     * `ON CONFLICT` substitui o `INSERT ... ON DUPLICATE KEY UPDATE` do MySQL, e
     * `EXCLUDED.x` substitui `VALUES(x)`.
     */
    public static function registrar(int $personagemId, int $faseId, int $estrelas, int $acertos, int $erros, bool $usouIa): void
    {
        DB::statement(
            'INSERT INTO progresso_fases (personagem_id, fase_id, estrelas, acertos, erros, usou_ia)
             VALUES (?, ?, ?, ?, ?, ?)
             ON CONFLICT ON CONSTRAINT uq_prog
             DO UPDATE SET
                 estrelas     = GREATEST(progresso_fases.estrelas, EXCLUDED.estrelas),
                 acertos      = EXCLUDED.acertos,
                 erros        = EXCLUDED.erros,
                 usou_ia      = EXCLUDED.usou_ia,
                 concluida_em = NOW()',
            [$personagemId, $faseId, $estrelas, $acertos, $erros, $usouIa]
        );
    }

    /** @return array<int,self> progresso do personagem, chaveado por fase_id */
    public static function mapaDoPersonagem(int $personagemId): array
    {
        return self::query()->where('personagem_id', $personagemId)->get()->keyBy('fase_id')->all();
    }

    public static function concluiu(int $personagemId, int $faseId): bool
    {
        return self::query()->where('personagem_id', $personagemId)->where('fase_id', $faseId)->exists();
    }
}
