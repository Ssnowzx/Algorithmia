<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Falas de uma fase. A variante muda conforme o alinhamento do herói: quem cede
 * ao Fragmento da IA ouve outra versão da mesma cena.
 *
 * @property int $id
 * @property int $fase_id
 * @property string $momento
 * @property string $variante
 * @property int $ordem
 * @property string $falante
 * @property string|null $svg_slug
 * @property string $texto
 */
final class Dialogo extends Model
{
    protected $table = 'dialogos';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = ['fase_id', 'momento', 'variante', 'ordem', 'falante', 'svg_slug', 'texto'];

    /**
     * Falas de um momento, na variante pedida.
     *
     * Cai para 'padrao' quando a variante 'ia' não foi escrita para esta cena —
     * sem isso, um herói de reputação baixa veria a fase muda.
     *
     * @return Collection<int,self>
     */
    public static function paraMomento(int $faseId, string $momento, string $variante): Collection
    {
        $falas = self::query()
            ->where('fase_id', $faseId)
            ->where('momento', $momento)
            ->where('variante', $variante)
            ->orderBy('ordem')
            ->get();

        if ($falas->isEmpty() && $variante !== 'padrao') {
            return self::paraMomento($faseId, $momento, 'padrao');
        }

        return $falas;
    }
}
