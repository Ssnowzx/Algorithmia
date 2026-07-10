<?php

declare(strict_types=1);

namespace App\Dominio\Tenancy;

use Illuminate\Support\Facades\DB;
use LogicException;
use RuntimeException;
use Throwable;

/**
 * Define, para o PostgreSQL, de qual instituição é a requisição.
 *
 * O ponto de vazamento não é a policy — é o **reaproveitamento de conexão**. O php-fpm
 * mantém a conexão viva entre requisições; um `SET app.tenant_id` que sobreviva ao fim
 * do pedido entrega os dados da instituição A ao pedido seguinte, que pode ser da B. Em
 * silêncio, e sem erro nenhum no log.
 *
 * Por isso o contexto é `SET LOCAL`, dentro de uma transação: ele morre com ela — no
 * commit, no rollback, e também se o processo for morto no meio.
 */
final class ContextoDoTenant
{
    private ?int $tenantId = null;

    /**
     * Roda o trecho com o contexto do tenant, numa transação.
     *
     * @template T
     *
     * @param  callable():T  $trecho
     * @return T
     */
    public function usar(int $tenantId, callable $trecho): mixed
    {
        return DB::transaction(function () use ($tenantId, $trecho) {
            // Restaurar o anterior, e não limpar.
            //
            // `DB::transaction()` aninhada vira SAVEPOINT, e um `SET LOCAL` feito dentro
            // dele **sobrevive** ao `RELEASE`: o escopo do `SET LOCAL` é a transação, não
            // o savepoint. Se este método apenas limpasse ao sair, tudo o que rodasse
            // depois dele — dentro da mesma transação — ficaria cego. Fora de aninhamento
            // o anterior é vazio, e restaurar equivale a limpar.
            $anterior = $this->doBanco();

            $this->definirNaTransacao($tenantId);

            try {
                $resultado = $trecho();
            } catch (Throwable $erro) {
                // **Nenhum SQL no caminho de erro.** Se `$trecho()` levantou por causa de um
                // erro do PostgreSQL, a transação está abortada e TODO comando seguinte
                // responde 25P02. Um `SET LOCAL` de restauro num `finally` seria esse
                // comando: ele estouraria, e a sua exceção tomaria o lugar da original —
                // o operador leria "current transaction is aborted" em vez do erro real.
                //
                // O banco não precisa de restauro aqui: o `DB::transaction()` fará ROLLBACK
                // (ou ROLLBACK TO SAVEPOINT), e o `SET LOCAL` feito depois do savepoint
                // morre junto. Só a cópia em memória precisa voltar, ou `atual()` mentiria.
                $this->tenantId = $anterior;

                throw $erro;
            }

            $anterior === null
                ? $this->limparNaTransacao()
                : $this->definirNaTransacao($anterior);

            return $resultado;
        });
    }

    /** O tenant que a conexão carrega agora, segundo o próprio PostgreSQL. */
    private function doBanco(): ?int
    {
        $variavel = (string) config('tenancy.variavel_de_sessao');
        $valor = DB::selectOne('SELECT NULLIF(current_setting(?, true), \'\') AS v', [$variavel])?->v;

        return $valor === null ? null : (int) $valor;
    }

    /** `''` vira NULL na policy (`NULLIF`), e NULL não casa com `tenant_id` nenhum. */
    public function limparNaTransacao(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::statement(sprintf("SET LOCAL %s = ''", (string) config('tenancy.variavel_de_sessao')));
        }

        $this->tenantId = null;
    }

    /**
     * Só dentro de uma transação já aberta. `SET LOCAL` fora de transação é um no-op
     * silencioso no PostgreSQL — o pior modo de falha possível para esta função.
     */
    public function definirNaTransacao(int $tenantId): void
    {
        if (DB::transactionLevel() === 0) {
            throw new LogicException(
                'ContextoDoTenant exige uma transação aberta: `SET LOCAL` fora dela não faz nada.'
            );
        }

        $variavel = (string) config('tenancy.variavel_de_sessao');

        // `SET LOCAL` não aceita placeholder — o valor é literal na gramática. Por isso o
        // id é convertido para inteiro antes de entrar na string.
        DB::statement(sprintf("SET LOCAL %s = '%d'", $variavel, $tenantId));

        $this->tenantId = $tenantId;
    }

    public function atual(): ?int
    {
        return $this->tenantId;
    }

    /**
     * O catálogo de instituições, para os comandos de console — que não têm `Host` de onde
     * deduzir o tenant.
     *
     * **As três consultas abaixo usam a conexão da APLICAÇÃO, e não a do dono.** Elas liam
     * pelo dono, "para não depender de `tenants` ter ficado sem RLS". O argumento não se
     * sustentava: o `ResolverTenant` lê essas mesmas duas tabelas pela conexão da aplicação
     * a cada requisição — se um dia elas ganharem RLS, o jogo inteiro para, muito antes de
     * um comando de console. A segunda conexão não comprava isolamento nenhum.
     *
     * E cobrava caro: o `ProvisionamentoDeInstituicoes` cria a instituição pela conexão da
     * aplicação, e o `algorithmia:smoke` — que `ativar()` roda para decidir se pode ligá-la
     * — a procurava pela do dono. Uma escola recém-criada era invisível ao comando que
     * deveria aprová-la. Fora de uma transação isso passa despercebido, porque cada escrita
     * comita na hora; dentro de uma, as duas conexões enxergam mundos diferentes.
     */

    /**
     * Todas as instituições ativas, para comandos que precisam varrê-las.
     *
     * @return list<object{id:int,slug:string}>
     */
    public function tenantsAtivos(): array
    {
        return DB::table('tenants')
            ->where('ativo', true)
            ->orderBy('id')
            ->get(['id', 'slug'])
            ->map(fn (object $t): object => (object) ['id' => (int) $t->id, 'slug' => (string) $t->slug])
            ->all();
    }

    public function tenantPorSlug(string $slug): int
    {
        $id = DB::table('tenants')->where('slug', $slug)->value('id');

        if ($id === null) {
            throw new RuntimeException(sprintf('Não existe instituição com slug "%s".', $slug));
        }

        return (int) $id;
    }

    /**
     * Numa instalação de uma instituição só — que é o caso do Algorithmia hoje — a resposta
     * é óbvia. Com duas ou mais não há resposta óbvia, e adivinhar seria escolher em nome do
     * operador de qual escola apagar os dados.
     */
    public function tenantUnico(): int
    {
        $tenants = DB::table('tenants')->orderBy('id')->pluck('id');

        return match ($tenants->count()) {
            0 => throw new RuntimeException('Nenhum tenant cadastrado. Rode as migrations.'),
            1 => (int) $tenants->first(),
            default => throw new RuntimeException(sprintf(
                'Há %d tenants. Um comando de console não escolhe por você — passe --tenant.',
                $tenants->count()
            )),
        };
    }
}
