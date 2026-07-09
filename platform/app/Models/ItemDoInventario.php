<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    /**
     * Inventário com os dados do item, na ordem de exibição do jogo.
     *
     * O legado ordenava com `FIELD(i.tipo, 'arma', 'escudo', ...)`, que só existe
     * no MySQL. `array_position` faz o mesmo no PostgreSQL.
     *
     * @return Collection<int,self>
     */
    public static function doPersonagem(int $personagemId): Collection
    {
        /** @var list<string> $ordem */
        $ordem = config('jogo.ordem_tipos_item');
        $lista = "'".implode("','", $ordem)."'";

        return self::query()
            ->with('item')
            ->where('personagem_id', $personagemId)
            ->join('itens', 'itens.id', '=', 'inventario.item_id')
            ->orderByRaw("array_position(ARRAY[{$lista}]::text[], itens.tipo)")
            ->orderBy('itens.nome')
            ->select('inventario.*')
            ->get();
    }

    /** @return BelongsTo<Item,$this> */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Equipa o item, desequipando o que ocupava o mesmo slot.
     *
     * Sem o desequipar em bloco, dois itens do mesmo tipo somariam bônus e o
     * herói viraria uma pilha de espadas.
     */
    public static function equipar(int $personagemId, Item $item): void
    {
        DB::transaction(function () use ($personagemId, $item): void {
            self::query()
                ->where('personagem_id', $personagemId)
                ->where('equipado', true)
                ->whereIn('item_id', Item::query()->where('tipo', $item->tipo)->select('id'))
                ->update(['equipado' => false]);

            self::query()
                ->where('personagem_id', $personagemId)
                ->where('item_id', $item->id)
                ->update(['equipado' => true]);
        });
    }

    public static function desequipar(int $personagemId, int $itemId): void
    {
        self::query()
            ->where('personagem_id', $personagemId)
            ->where('item_id', $itemId)
            ->update(['equipado' => false]);
    }

    /** @return list<string> tipos atualmente equipados (arma, escudo, acessorio) */
    public static function tiposEquipados(int $personagemId): array
    {
        return self::query()
            ->where('inventario.personagem_id', $personagemId)
            ->where('inventario.equipado', true)
            ->join('itens', 'itens.id', '=', 'inventario.item_id')
            ->pluck('itens.tipo')
            ->unique()
            ->values()
            ->all();
    }
}
