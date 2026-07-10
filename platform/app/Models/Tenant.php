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
 * @property array<string,bool> $flags
 */
final class Tenant extends Model
{
    protected $table = 'tenants';

    /**
     * `flags` fica de fora de propósito: nenhum `fill()` vindo de requisição pode ligar
     * funcionalidade. Quem as move é `Flags::definirNoTenant()`, e ela audita.
     *
     * @var list<string>
     */
    protected $fillable = ['nome', 'slug', 'ativo'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return ['ativo' => 'boolean', 'flags' => 'array'];
    }

    /** @return HasMany<TenantDominio, $this> */
    public function dominios(): HasMany
    {
        return $this->hasMany(TenantDominio::class);
    }

    /** O host pelo qual a instituição é alcançada. */
    public function hostPrimario(): ?string
    {
        return $this->dominios()->orderByDesc('primario')->orderBy('id')->value('host');
    }
}
