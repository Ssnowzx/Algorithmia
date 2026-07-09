<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
