<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pergunta de uma fase. `opcoes` e `resposta` são jsonb.
 *
 * O gabarito (`resposta`) nunca sai do servidor: EstadoDeBatalha::paraCliente()
 * o remove antes de qualquer serialização.
 *
 * @property int $id
 * @property int $fase_id
 * @property int $ordem
 * @property string $tipo
 * @property string $assunto
 * @property string $pergunta
 * @property string|null $codigo
 * @property mixed $opcoes
 * @property mixed $resposta
 * @property string $explicacao
 * @property int $dificuldade
 */
final class Desafio extends Model
{
    protected $table = 'desafios';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'fase_id', 'ordem', 'tipo', 'assunto', 'pergunta', 'codigo',
        'opcoes', 'resposta', 'explicacao', 'dificuldade',
    ];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return [
            // `json` e não `array`: o gabarito de 'vf' é booleano e o de
            // 'multipla' é inteiro — forçar array corromperia ambos.
            'opcoes' => 'json',
            'resposta' => 'json',
            'dificuldade' => 'integer',
            'ordem' => 'integer',
        ];
    }

    /** @return BelongsTo<Fase,$this> */
    public function fase(): BelongsTo
    {
        return $this->belongsTo(Fase::class, 'fase_id');
    }

    /**
     * Pool completo da fase. A fase guarda MAIS desafios do que entram numa
     * batalha; o sorteio escolhe um subconjunto a cada combate.
     *
     * @return Collection<int,self>
     */
    public static function poolDaFase(int $faseId): Collection
    {
        return self::query()->where('fase_id', $faseId)->orderBy('ordem')->get();
    }

    /**
     * Ids dos desafios desta fase que o personagem já respondeu alguma vez.
     * Alimenta o anti-repetição do sorteio.
     *
     * @return list<int>
     */
    public static function idsVistos(int $personagemId, int $faseId): array
    {
        return RespostaLog::query()
            ->join('desafios', 'desafios.id', '=', 'respostas_log.desafio_id')
            ->where('respostas_log.personagem_id', $personagemId)
            ->where('desafios.fase_id', $faseId)
            ->distinct()
            ->pluck('respostas_log.desafio_id')
            ->map(intval(...))
            ->all();
    }
}
