<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

final class Tenant extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'tenants';

    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

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
        return $this->hasMany(TenantDomain::class);
    }

    /**
     * @return HasMany<TenantMembership, self>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(TenantMembership::class);
    }
}
