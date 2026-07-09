<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nome
 * @property string $svg_slug
 * @property int $ordem
 */
final class Mestre extends Model
{
    protected $table = 'mestres';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = ['nome', 'titulo', 'disciplina', 'regiao', 'historia', 'personalidade', 'bordao', 'svg_slug', 'cor_tema', 'ordem'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return ['ordem' => 'integer'];
    }

    /** Código da conquista de "discípulo" desta região, se houver. */
    public function conquistaDaRegiao(): ?string
    {
        $codigo = config("jogo.regioes_mestre.{$this->svg_slug}.conquista");

        return is_string($codigo) ? $codigo : null;
    }
}
