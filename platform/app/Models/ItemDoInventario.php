<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Linha do inventário de um personagem.
 *
 * @property int $id
 * @property int $personagem_id
 * @property int $item_id
 * @property int $quantidade
 * @property bool $equipado
 */
final class ItemDoInventario extends Model
{
    protected $table = 'inventario';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = ['personagem_id', 'item_id', 'quantidade', 'equipado'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return ['quantidade' => 'integer', 'equipado' => 'boolean'];
    }

    public static function quantidade(int $personagemId, int $itemId): int
    {
        return (int) self::query()
            ->where('personagem_id', $personagemId)
            ->where('item_id', $itemId)
            ->value('quantidade');
    }

    /**
     * Soma uma unidade ao item, criando a linha se ainda não existir.
     * `ON CONFLICT` substitui o `INSERT ... ON DUPLICATE KEY UPDATE` do MySQL.
     */
    public static function adicionar(int $personagemId, int $itemId, int $qtd = 1): void
    {
        DB::statement(
            'INSERT INTO inventario (personagem_id, item_id, quantidade, equipado)
             VALUES (?, ?, ?, false)
             ON CONFLICT ON CONSTRAINT uq_inv
             DO UPDATE SET quantidade = inventario.quantidade + EXCLUDED.quantidade',
            [$personagemId, $itemId, $qtd]
        );
    }

    /** Remove unidades; a linha some quando zera, como no legado. */
    public static function remover(int $personagemId, int $itemId, int $qtd = 1): void
    {
        $linha = self::query()
            ->where('personagem_id', $personagemId)
            ->where('item_id', $itemId)
            ->first();

        if ($linha === null) {
            return;
        }

        if ($linha->quantidade <= $qtd) {
            $linha->delete();

            return;
        }

        $linha->decrement('quantidade', $qtd);
    }

    /**
     * Bônus somados de todos os itens equipados.
     *
     * @return array{ataque:int,defesa:int}
     */
    public static function bonusEquipados(int $personagemId): array
    {
        $itens = self::query()
            ->where('personagem_id', $personagemId)
            ->where('equipado', true)
            ->join('itens', 'itens.id', '=', 'inventario.item_id')
            ->pluck('itens.efeito');

        $ataque = 0;
        $defesa = 0;
        foreach ($itens as $efeitoJson) {
            /** @var array<string,int> $efeito */
            $efeito = json_decode((string) $efeitoJson, true) ?: [];
            $ataque += (int) ($efeito['ataque'] ?? 0);
            $defesa += (int) ($efeito['defesa'] ?? 0);
        }

        return ['ataque' => $ataque, 'defesa' => $defesa];
    }

    public static function itensDistintos(int $personagemId): int
    {
        return self::query()->where('personagem_id', $personagemId)->count();
    }
}
