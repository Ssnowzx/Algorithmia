<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $mestre_id
 * @property int $ordem_global
 * @property string $nome
 * @property string $tipo
 * @property string|null $inimigo_nome
 * @property string|null $inimigo_svg
 * @property int $inimigo_hp
 * @property int $inimigo_ataque
 * @property int $xp_recompensa
 * @property int $ouro_recompensa
 * @property int|null $item_drop_id
 * @property int|null $requisito_fase_id
 * @property-read Mestre|null $mestre
 */
final class Fase extends Model
{
    protected $table = 'fases';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'mestre_id', 'ordem_global', 'nome', 'tipo', 'descricao', 'inimigo_nome',
        'inimigo_svg', 'inimigo_hp', 'inimigo_ataque', 'xp_recompensa',
        'ouro_recompensa', 'item_drop_id', 'requisito_fase_id',
    ];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return [
            'inimigo_hp' => 'integer', 'inimigo_ataque' => 'integer',
            'xp_recompensa' => 'integer', 'ouro_recompensa' => 'integer',
        ];
    }

    /** @return BelongsTo<Mestre,$this> */
    public function mestre(): BelongsTo
    {
        return $this->belongsTo(Mestre::class, 'mestre_id');
    }

    public function ehChefe(): bool
    {
        return in_array($this->tipo, ['chefe', 'chefe_final'], true);
    }

    /**
     * O confronto derradeiro. Derivado do tipo, e não do id 35 que o legado
     * carrega fixo em HistoriaController — um seeder diferente moveria o id, e a
     * tela de finais ficaria inalcançável em silêncio.
     */
    public static function confrontoFinal(): ?self
    {
        return self::query()->where('tipo', 'chefe_final')->orderBy('ordem_global')->first();
    }

    /** A fase imediatamente seguinte no mapa, se houver. */
    public function proxima(): ?self
    {
        return self::query()->where('ordem_global', '>', $this->ordem_global)
            ->orderBy('ordem_global')->first();
    }

    /** @return Collection<int,self> */
    public static function todasOrdenadas(): Collection
    {
        return self::query()->orderBy('ordem_global')->get();
    }

    /**
     * Fases da região de um mestre, em ordem de mapa.
     *
     * @return Collection<int,self>
     */
    public static function doMestre(int $mestreId): Collection
    {
        return self::query()->where('mestre_id', $mestreId)->orderBy('ordem_global')->get();
    }
}
