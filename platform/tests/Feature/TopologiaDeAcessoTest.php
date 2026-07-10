<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * A fundação multitenant começa aqui, e não numa tabela `tenants`.
 *
 * Medido em 2026-07-10, no banco de produção local: o papel `algorithmia` é
 * superusuário, tem `rolbypassrls` e é dono de todas as tabelas. Com `ENABLE` +
 * `FORCE ROW LEVEL SECURITY` e uma policy `USING (false)`, ele continuava vendo todas
 * as linhas. São três razões independentes para o RLS ser inerte.
 *
 * Ligar RLS sem trocar de papel entregaria a pior coisa que uma barreira de segurança
 * pode entregar: a sensação de que existe.
 */
final class TopologiaDeAcessoTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_aplicacao_nao_conecta_como_superusuario(): void
    {
        // ARRANGE + ACT
        $papel = DB::selectOne(
            'SELECT rolsuper, rolbypassrls FROM pg_roles WHERE rolname = current_user'
        );

        // ASSERT
        $this->assertNotNull($papel);
        $this->assertFalse((bool) $papel->rolsuper, 'superusuário ignora RLS, sempre');
        $this->assertFalse((bool) $papel->rolbypassrls, 'rolbypassrls é um segundo bypass');
    }

    #[Test]
    public function a_aplicacao_nao_e_dona_de_nenhuma_tabela(): void
    {
        // ARRANGE + ACT: o dono ignora as policies sem `FORCE ROW LEVEL SECURITY`, e
        // depender de lembrar do FORCE em cada tabela é depender da memória de alguém.
        $donas = DB::select(
            "SELECT tablename FROM pg_tables WHERE schemaname = 'public' AND tableowner = current_user"
        );

        // ASSERT
        $this->assertSame([], $donas);
    }

    #[Test]
    public function o_papel_da_aplicacao_e_diferente_do_papel_das_migrations(): void
    {
        // ARRANGE + ACT
        $daAplicacao = DB::connection('pgsql')->selectOne('SELECT current_user AS u')->u;
        $doDono = DB::connection('pgsql_dono')->selectOne('SELECT current_user AS u')->u;

        // ASSERT
        $this->assertNotSame($doDono, $daAplicacao);
    }

    /**
     * A prova de que a barreira existe: uma tabela com RLS e uma policy que depende de
     * `app.tenant_id`. Sem contexto, o resultado é vazio — e não um erro. A policy
     * filtra, não recusa.
     */
    #[Test]
    public function com_o_papel_de_execucao_a_policy_de_rls_realmente_se_aplica(): void
    {
        // ARRANGE: a tabela é criada e povoada pelo DONO.
        //
        // Ela não é derrubada ao final, de propósito. Um `DROP TABLE` pela conexão do
        // dono precisa de ACCESS EXCLUSIVE, e a conexão da aplicação ainda tem aberta a
        // transação do `RefreshDatabase`, que leu esta tabela: o drop esperaria para
        // sempre. O `migrate:fresh` da próxima execução a leva junto com as outras.
        $dono = DB::connection('pgsql_dono');
        $dono->statement('DROP TABLE IF EXISTS ensaio_rls');
        $dono->statement('CREATE TABLE ensaio_rls (id int, tenant text)');
        $dono->statement("INSERT INTO ensaio_rls VALUES (1, 'a'), (2, 'b')");
        $dono->statement('ALTER TABLE ensaio_rls ENABLE ROW LEVEL SECURITY');
        $dono->statement('ALTER TABLE ensaio_rls FORCE ROW LEVEL SECURITY');
        $dono->statement(
            "CREATE POLICY por_tenant ON ensaio_rls USING (tenant = current_setting('app.tenant_id', true))"
        );
        $dono->statement(sprintf('GRANT SELECT ON ensaio_rls TO "%s"', (string) config('database.papel_da_aplicacao')));

        $app = DB::connection('pgsql');

        // ACT + ASSERT: sem contexto, nada. E não é erro — a policy filtra, não recusa.
        $this->assertSame(0, (int) $app->selectOne('SELECT count(*) AS n FROM ensaio_rls')->n);

        // `SET LOCAL`, e não `SET`: o php-fpm reaproveita a conexão, e um contexto que
        // sobreviva ao fim do pedido entregaria os dados da instituição A à requisição
        // seguinte, que pode ser da B. Aqui ele morre com a transação do teste.
        $app->statement("SET LOCAL app.tenant_id = 'a'");
        $this->assertSame(1, (int) $app->selectOne('SELECT count(*) AS n FROM ensaio_rls')->n);

        $app->statement("SET LOCAL app.tenant_id = 'b'");
        $this->assertSame(0, (int) $app->selectOne("SELECT count(*) AS n FROM ensaio_rls WHERE tenant = 'a'")->n);

        // …e o dono, esse continua vendo tudo. É exatamente por isso que ele não serve.
        $this->assertSame(2, (int) $dono->selectOne('SELECT count(*) AS n FROM ensaio_rls')->n);
    }
}
