<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Conta de acesso. Mapeia a tabela `usuarios` do legado, preservando os nomes
 * das colunas para que a importação de dados seja uma cópia, e não uma tradução.
 *
 * Três desvios da convenção do Laravel, todos herdados do schema legado:
 * - a senha vive em `senha_hash`, não em `password`;
 * - existe `criado_em`, mas não há coluna de atualização;
 * - não existe `remember_token`, então o "lembrar de mim" fica desligado.
 *
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $senha_hash
 * @property string $papel
 */
final class Usuario extends Authenticatable
{
    protected $table = 'usuarios';

    public const CREATED_AT = 'criado_em';

    public const UPDATED_AT = null;

    /** @var list<string> */
    protected $fillable = ['nome', 'email', 'senha_hash', 'papel'];

    /** @var list<string> */
    protected $hidden = ['senha_hash'];

    public function getAuthPassword(): string
    {
        return $this->senha_hash;
    }

    /**
     * Sem isto, o Laravel grava o rehash da senha numa coluna `password` que não
     * existe. Ele reidrata o hash sempre que o custo do bcrypt gravado difere do
     * configurado — e os hashes vindos do legado quase sempre diferem.
     */
    public function getAuthPasswordName(): string
    {
        return 'senha_hash';
    }

    /** Nome vazio desliga o "lembrar de mim": a tabela não tem a coluna. */
    public function getRememberTokenName(): string
    {
        return '';
    }

    public function ehMestre(): bool
    {
        return $this->papel === 'mestre';
    }
}
