<?php

declare(strict_types=1);

namespace App\Domain\Identity\Models;

use App\Domain\Tenancy\Models\TenantMembership;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 */
final class User extends Authenticatable
{
    use HasUuids;
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /** @return Attribute<string, string> */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => mb_strtolower($value),
        );
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
