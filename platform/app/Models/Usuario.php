<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    public function ehProfessor(): bool
    {
        return $this->papel === 'professor';
    }

    /**
     * O mestre administra a escola: ele vê todas as turmas dela. O professor vê as que
     * leciona — e isso não se decide aqui, decide-se na `TurmaPolicy`.
     */
    public function podeVerRelatorios(): bool
    {
        return $this->ehMestre() || $this->ehProfessor();
    }

    /**
     * `withTimestamps()` sem argumentos herda os nomes das colunas do modelo PAI, e este
     * usa `criado_em` (e nenhum `updated_at`) porque o legado assim os batizou. O pivô,
     * que é tabela nova, usa `created_at`/`updated_at`. Sem dizer isso aqui, o Eloquent
     * procura `turma_professores.criado_em` e o banco responde que ela não existe.
     *
     * @return BelongsToMany<Turma, $this>
     */
    public function turmasQueLeciona(): BelongsToMany
    {
        return $this->belongsToMany(Turma::class, 'turma_professores', 'usuario_id', 'turma_id')
            ->withTimestamps('created_at', 'updated_at');
    }

    /** @return BelongsToMany<Turma, $this> */
    public function turmasEmQueEstuda(): BelongsToMany
    {
        return $this->belongsToMany(Turma::class, 'matriculas', 'usuario_id', 'turma_id')
            ->withTimestamps('created_at', 'updated_at');
    }

    /** @return HasOne<Personagem,$this> */
    public function personagem(): HasOne
    {
        return $this->hasOne(Personagem::class, 'usuario_id');
    }
}
