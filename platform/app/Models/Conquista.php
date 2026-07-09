<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $codigo
 * @property string $nome
 * @property string $svg_slug
 * @property bool $secreta
 */
final class Conquista extends Model
{
    protected $table = 'conquistas';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = ['codigo', 'nome', 'descricao', 'svg_slug', 'secreta'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return ['secreta' => 'boolean'];
    }

    public static function porCodigo(string $codigo): ?self
    {
        return self::query()->where('codigo', $codigo)->first();
    }

    /**
     * Concede a conquista ao personagem. Devolve true só na primeira vez.
     *
     * `ON CONFLICT DO NOTHING` substitui o `INSERT IGNORE` do MySQL; o legado lia
     * `rowCount()` para a mesma decisão. Sem a chave primária composta, um clique
     * duplo concederia a conquista duas vezes.
     */
    public function concederA(int $personagemId): bool
    {
        $inseridas = DB::affectingStatement(
            'INSERT INTO conquistas_personagem (personagem_id, conquista_id)
             VALUES (?, ?)
             ON CONFLICT DO NOTHING',
            [$personagemId, $this->id]
        );

        return $inseridas > 0;
    }
}
