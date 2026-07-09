<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $usuario_id
 * @property string $nome
 * @property string $classe
 * @property int $nivel
 * @property int $xp
 * @property int $hp_max
 * @property int $hp_atual
 * @property int $mp_max
 * @property int $mp_atual
 * @property int $ouro
 * @property int $reputacao
 * @property int $capitulo
 */
final class Personagem extends Model
{
    protected $table = 'personagens';

    public const CREATED_AT = 'criado_em';

    // O MySQL mantinha esta coluna sozinho (ON UPDATE CURRENT_TIMESTAMP). No
    // PostgreSQL não existe construto equivalente: quem a mantém é o Eloquent.
    public const UPDATED_AT = 'atualizado_em';

    /** @var list<string> */
    protected $fillable = [
        'usuario_id', 'nome', 'classe', 'nivel', 'xp', 'hp_max', 'hp_atual',
        'mp_max', 'mp_atual', 'ouro', 'reputacao', 'capitulo',
    ];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return [
            'nivel' => 'integer', 'xp' => 'integer',
            'hp_max' => 'integer', 'hp_atual' => 'integer',
            'mp_max' => 'integer', 'mp_atual' => 'integer',
            'ouro' => 'integer', 'reputacao' => 'integer', 'capitulo' => 'integer',
        ];
    }

    /** @return BelongsTo<Usuario,$this> */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /** Atributos-base da classe, sem os bônus do equipamento. */
    public function ataqueDaClasse(): int
    {
        return (int) config("jogo.classes.{$this->classe}.ataque");
    }

    public function defesaDaClasse(): int
    {
        return (int) config("jogo.classes.{$this->classe}.defesa");
    }
}
