<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Uma turma de uma instituição. Tenant-scoped: o RLS a esconde das outras escolas.
 *
 * Que ela seja invisível a outra escola é tenancy. Que ela seja invisível a um professor
 * da mesma escola que não a leciona é **autorização** — e isso mora na `TurmaPolicy`.
 *
 * @property int $id
 * @property int $tenant_id
 * @property string $nome
 * @property string $codigo
 * @property bool $ativa
 */
final class Turma extends Model
{
    protected $table = 'turmas';

    /** @var list<string> */
    protected $fillable = ['nome', 'codigo', 'ativa'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return ['ativa' => 'boolean'];
    }

    /** @return BelongsToMany<Usuario, $this> */
    public function alunos(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'matriculas', 'turma_id', 'usuario_id')
            ->withTimestamps('created_at', 'updated_at');
    }

    /** @return BelongsToMany<Usuario, $this> */
    public function professores(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'turma_professores', 'turma_id', 'usuario_id')
            ->withTimestamps('created_at', 'updated_at');
    }
}
