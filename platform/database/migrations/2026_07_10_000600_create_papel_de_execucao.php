<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * O papel com que a aplicação conecta ao banco.
 *
 * Medido em 2026-07-10: o papel `algorithmia` é superusuário, tem `rolbypassrls` e é
 * dono de todas as tabelas. São três razões independentes para as policies de RLS não
 * se aplicarem a ele. Verificado no banco, com `ENABLE` + `FORCE ROW LEVEL SECURITY` e
 * uma policy `USING (false)`: ele continuava vendo todas as linhas.
 *
 * Ligar RLS sem trocar de papel entregaria a pior coisa que uma barreira de segurança
 * pode entregar — a sensação de que existe. Por isso a fundação multitenant começa
 * aqui, e não numa tabela `tenants`.
 *
 * Esta migration roda como o DONO (conexão `pgsql_dono`); criar papel exige isso.
 *
 * É aditiva e idempotente: cria o papel se faltar, e apenas concede. Nada é revogado —
 * revogar privilégio do dono quebraria as próprias migrations.
 */
return new class extends Migration
{
    /** A migration roda pela conexão do dono; o `CREATE ROLE` exige superusuário. */
    protected $connection = 'pgsql_dono';

    public function up(): void
    {
        $papel = (string) config('database.papel_da_aplicacao');
        $senha = (string) config('database.senha_da_aplicacao');
        $banco = (string) DB::connection($this->connection)->getDatabaseName();

        if ($papel === '' || $this->ehODono($papel)) {
            // Sem papel separado configurado, não há o que criar. O `.env` ainda não
            // foi migrado, e o comportamento antigo segue valendo.
            return;
        }

        $existe = DB::connection($this->connection)
            ->selectOne('SELECT 1 FROM pg_roles WHERE rolname = ?', [$papel]) !== null;

        if (! $existe) {
            // O nome do papel é identificador: não aceita placeholder, e aspas duplas
            // não escapam aspas duplas. A validação está em `config/database.php`.
            DB::connection($this->connection)->statement(sprintf(
                'CREATE ROLE %s LOGIN NOSUPERUSER NOCREATEDB NOCREATEROLE NOBYPASSRLS PASSWORD %s',
                '"'.$papel.'"',
                DB::connection($this->connection)->getPdo()->quote($senha)
            ));
        }

        $p = '"'.$papel.'"';

        foreach ([
            sprintf('GRANT CONNECT ON DATABASE "%s" TO %s', $banco, $p),
            sprintf('GRANT USAGE ON SCHEMA public TO %s', $p),
            sprintf('GRANT SELECT, INSERT, UPDATE, DELETE, TRUNCATE ON ALL TABLES IN SCHEMA public TO %s', $p),
            sprintf('GRANT USAGE, SELECT, UPDATE ON ALL SEQUENCES IN SCHEMA public TO %s', $p),

            // Sem isto, toda migration futura criaria tabelas invisíveis à aplicação, e
            // o erro apareceria só no deploy seguinte.
            sprintf('ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT SELECT, INSERT, UPDATE, DELETE, TRUNCATE ON TABLES TO %s', $p),
            sprintf('ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT USAGE, SELECT, UPDATE ON SEQUENCES TO %s', $p),
        ] as $comando) {
            DB::connection($this->connection)->statement($comando);
        }
    }

    public function down(): void
    {
        // Não se apaga o papel: outras sessões podem estar conectadas com ele, e um
        // rollback de código não deve derrubar quem está jogando.
    }

    private function ehODono(string $papel): bool
    {
        return $papel === (string) config('database.connections.pgsql_dono.username');
    }
};
