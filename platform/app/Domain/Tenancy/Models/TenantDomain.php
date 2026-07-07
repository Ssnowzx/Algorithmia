<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $tenant_id
 * @property string $host
 * @property bool $is_primary
 * @property bool $is_active
 * @property string|null $verified_at
 */
final class TenantDomain extends Model
{
    use HasUuids;

    protected $table = 'tenant_domains';

    protected $fillable = [
        'tenant_id',
        'host',
        'is_primary',
        'is_active',
        'verified_at',
    ];

    protected $casts = [
        'is_primary' => 'bool',
        'is_active' => 'bool',
        'verified_at' => 'datetime',
    ];

    /** @return Attribute<string, string> */
    protected function host(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => mb_strtolower($value),
        );
    }

    /**
     * @return BelongsTo<Tenant, self>
     */
    public function tenant(): BelongsTo
    {
        /** @var BelongsTo<Tenant, self> $relation */
        $relation = $this->belongsTo(Tenant::class);

        return $relation;
    }
}
