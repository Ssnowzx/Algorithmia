<?php

declare(strict_types=1);

namespace Tests;

use App\Dominio\Tenancy\ContextoDoTenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->definirContextoDoTenantPadrao();
    }

    /**
     * Um teste não é uma requisição HTTP: não tem `Host`, e portanto não passa pelo
     * `ResolverTenant`. Sem contexto, as 13 tabelas do jogo ficam invisíveis e nenhuma
     * inserção passa pelo `NOT NULL` de `tenant_id`.
     *
     * O contexto é o do tenant padrão, criado pela migration a partir de `APP_URL` — o
     * mesmo que o `ResolverTenant` escolheria para o host `localhost` dos testes HTTP.
     *
     * A leitura vem da conexão do DONO porque um teste roda sem contexto, e é mais
     * honesto não depender de `tenants` ter ficado sem RLS.
     */
    private function definirContextoDoTenantPadrao(): void
    {
        if (! config('tenancy.ativo') || config('database.default') !== 'pgsql') {
            return;
        }

        if (DB::transactionLevel() === 0) {
            return; // teste sem RefreshDatabase: não toca o banco
        }

        $id = DB::connection('pgsql_dono')->table('tenants')->where('slug', 'padrao')->value('id');

        if ($id !== null) {
            app(ContextoDoTenant::class)->definirNaTransacao((int) $id);
        }
    }
}
