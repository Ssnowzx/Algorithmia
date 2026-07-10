<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * O host pelo qual uma instituição é alcançada.
 *
 * @property int $id
 * @property int $tenant_id
 * @property string $host
 * @property bool $primario
 */
final class TenantDominio extends Model
{
    protected $table = 'tenant_dominios';

    /** @var list<string> */
    protected $fillable = ['tenant_id', 'host', 'primario'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return ['primario' => 'boolean'];
    }

    /** @return BelongsTo<Tenant, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * `Escola.EDU.br` e `escola.edu.br` são o mesmo host. O índice único do banco é
     * sobre `lower(host)`; a consulta precisa concordar com ele, ou nunca o usa.
     *
     * @param  Builder<TenantDominio>  $consulta
     * @return Builder<TenantDominio>
     */
    public function scopeComHost(Builder $consulta, string $host): Builder
    {
        return $consulta->whereRaw('lower(host) = ?', [mb_strtolower($host)]);
    }
}
