<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Models;

use App\Domain\Identity\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $tenant_id
 * @property string $user_id
 * @property string $role
 * @property string $status
 */
final class TenantMembership extends Model
{
    use HasUuids;

    protected $table = 'tenant_memberships';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'role',
        'status',
    ];

    /** @return Attribute<string, string> */
    protected function role(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => mb_strtolower($value),
        );
    }

    /** @return Attribute<string, string> */
    protected function status(): Attribute
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

    /**
     * @return BelongsTo<User, self>
     */
    public function user(): BelongsTo
    {
        /** @var BelongsTo<User, self> $relation */
        $relation = $this->belongsTo(User::class);

        return $relation;
    }
}
