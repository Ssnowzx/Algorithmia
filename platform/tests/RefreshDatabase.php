<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase as RefreshDatabaseDoLaravel;

/**
 * O `RefreshDatabase` do Laravel, com as migrations rodando pelo DONO do banco.
 *
 * A aplicação — e portanto a suíte — conecta com o papel de execução, que é sujeito às
 * policies de RLS. Esse papel não pode derrubar tabelas de que não é dono, nem criar
 * papéis. `migrate:fresh` precisa do dono.
 *
 * Isto é um trait, e não um método em `TestCase`, porque um método vindo de trait vence
 * o método herdado da classe-pai: um `migrateFreshUsing()` em `TestCase` seria
 * silenciosamente ignorado por toda classe que usasse `RefreshDatabase`.
 */
trait RefreshDatabase
{
    use RefreshDatabaseDoLaravel {
        migrateFreshUsing as private migrateFreshUsingDoLaravel;
    }

    /** @return array<string,mixed> */
    protected function migrateFreshUsing()
    {
        return array_merge($this->migrateFreshUsingDoLaravel(), ['--database' => 'pgsql_dono']);
    }
}
