<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Vínculo entre um usuário global e uma instituição. Tenant-scoped: protegido por RLS.
 *
 * @property int $id
 * @property int $tenant_id
 * @property int $usuario_id
 * @property string $papel
 */
final class TenantMembro extends Model
{
    protected $table = 'tenant_membros';

    /** @var list<string> */
    protected $fillable = ['tenant_id', 'usuario_id', 'papel'];
}
