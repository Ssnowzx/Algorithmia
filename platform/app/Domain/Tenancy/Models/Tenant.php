<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string $status
 */
final class Tenant extends Model
{
    use HasUuids;

    protected $table = 'tenants';

    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    /** @return Attribute<string, string> */
    protected function slug(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => mb_strtolower($value),
        );
    }

    /**
     * @return HasMany<TenantDomain, self>
     */
    public function domains(): HasMany
    {
        /** @var HasMany<TenantDomain, self> $relation */
        $relation = $this->hasMany(TenantDomain::class);

        return $relation;
    }

    /**
     * @return HasMany<TenantMembership, self>
     */
    public function memberships(): HasMany
    {
        /** @var HasMany<TenantMembership, self> $relation */
        $relation = $this->hasMany(TenantMembership::class);

        return $relation;
    }
}
