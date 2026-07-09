<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nome
 * @property string $tipo
 * @property array<string,int>|null $efeito
 * @property int $preco
 * @property string $svg_slug
 * @property string $raridade
 * @property bool $compravel
 */
final class Item extends Model
{
    protected $table = 'itens';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = ['nome', 'descricao', 'tipo', 'efeito', 'preco', 'svg_slug', 'raridade', 'compravel'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return ['efeito' => 'array', 'preco' => 'integer', 'compravel' => 'boolean'];
    }

    /** Valor de um efeito ('ataque', 'defesa', 'cura_hp', 'cura_mp'), ou 0. */
    public function efeito(string $chave): int
    {
        return (int) ($this->efeito[$chave] ?? 0);
    }

    public function ehPocao(): bool
    {
        return $this->tipo === 'pocao';
    }

    /** O Fragmento da IA Ancestral é resolvido pelo slug, nunca pelo nome exibido. */
    public static function fragmentoDaIa(): ?self
    {
        return self::query()->where('svg_slug', config('jogo.item_fragmento_ia'))->first();
    }
}
