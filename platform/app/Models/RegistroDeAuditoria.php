<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Uma linha da trilha de auditoria. Só se insere; nunca se atualiza.
 *
 * @property int $id
 * @property int|null $autor_id
 * @property string|null $autor_tipo
 * @property string|null $autor_email
 * @property string $acao
 * @property string $alvo_tipo
 * @property int|null $alvo_id
 * @property array<string,mixed>|null $resumo
 * @property string|null $ip
 * @property string|null $request_id
 * @property int|null $tenant_id NULL significa "a plataforma", e não "esqueceram de preencher"
 */
final class RegistroDeAuditoria extends Model
{
    protected $table = 'auditoria';

    /** `created_at` vem do banco (`useCurrent`), e `updated_at` não existe. */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'autor_id',
        // Os ids de `usuarios` e `operadores` colidem: o aluno 3 e o operador 3 são
        // pessoas diferentes. Sem esta coluna, a linha não sabe dizer quem agiu.
        'autor_tipo',
        'autor_email',
        'acao',
        'alvo_tipo',
        'alvo_id',
        'resumo',
        'ip',
        'request_id',
    ];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return ['resumo' => 'array'];
    }
}
