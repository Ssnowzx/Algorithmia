<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Quem administra a plataforma. Não pertence a instituição nenhuma.
 *
 * Catálogo global, como `tenants`: sem `tenant_id`, sem RLS. Ver a migration
 * `create_operadores` para a razão de ele não ser um papel em `usuarios`.
 *
 * Ele não ganha poder de banco: a aplicação continua conectando com `algorithmia_app`,
 * sujeito às policies. O que ele pode é escolher em qual instituição entrar, uma de cada
 * vez — o mesmo que uma requisição HTTP faz a partir do `Host`.
 *
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $senha_hash
 * @property bool $ativo
 */
final class Operador extends Authenticatable
{
    protected $table = 'operadores';

    /** @var list<string> */
    protected $fillable = ['nome', 'email', 'senha_hash', 'ativo'];

    /** @var list<string> */
    protected $hidden = ['senha_hash'];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return ['ativo' => 'boolean', 'ultimo_acesso_em' => 'datetime'];
    }

    public function getAuthPassword(): string
    {
        return $this->senha_hash;
    }

    /** Sem isto, o Laravel grava o rehash numa coluna `password` que não existe. */
    public function getAuthPasswordName(): string
    {
        return 'senha_hash';
    }

    /** A tabela não tem a coluna: o "lembrar de mim" fica desligado, e é bom que fique. */
    public function getRememberTokenName(): string
    {
        return '';
    }
}
