<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Uma instituição. Catálogo global: sem RLS, porque o resolvedor precisa lê-la antes
 * de existir contexto de tenant. Ver a migration `create_tenancy_tables`.
 *
 * @property int $id
 * @property string $nome
 * @property string $slug
 * @property bool $ativo
 */
final class Tenant extends Model
{
    protected $table = 'tenants';

    /** @var list<string> */
    protected $fillable = ['nome', 'slug', 'ativo'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }

    /** @return HasMany<TenantDominio, $this> */
    public function dominios(): HasMany
    {
        return $this->hasMany(TenantDominio::class);
    }
}
