<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Decisões narrativas do jogador. Hoje só existe uma, `final`, e ela decide qual
 * dos três epílogos o jogador vê.
 *
 * @property int $personagem_id
 * @property string $codigo
 * @property string $valor
 */
final class Escolha extends Model
{
    protected $table = 'escolhas';

    public const CREATED_AT = 'criado_em';

    public const UPDATED_AT = null;

    /** @var list<string> */
    protected $fillable = ['personagem_id', 'codigo', 'valor'];

    /**
     * Grava, substituindo a anterior de mesmo código.
     *
     * A tabela não tem chave única em (personagem_id, codigo) — o legado apagava
     * antes de inserir, e mantemos o comportamento. Um clique duplo criaria duas
     * linhas se não fosse o delete.
     */
    public static function definir(int $personagemId, string $codigo, string $valor): void
    {
        self::query()->where('personagem_id', $personagemId)->where('codigo', $codigo)->delete();
        self::create(['personagem_id' => $personagemId, 'codigo' => $codigo, 'valor' => $valor]);
    }

    public static function valor(int $personagemId, string $codigo): ?string
    {
        $valor = self::query()
            ->where('personagem_id', $personagemId)
            ->where('codigo', $codigo)
            ->value('valor');

        return is_string($valor) ? $valor : null;
    }
}
