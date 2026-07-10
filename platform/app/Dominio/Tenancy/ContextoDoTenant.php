<?php

declare(strict_types=1);

namespace App\Dominio\Tenancy;

use Illuminate\Support\Facades\DB;
use LogicException;

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
            $this->definirNaTransacao($tenantId);

            try {
                return $trecho();
            } finally {
                // Limpar explicitamente, e não confiar no fim da transação.
                //
                // `DB::transaction()` aninhada vira SAVEPOINT, e um `SET LOCAL` feito
                // dentro dele **sobrevive** ao `RELEASE`: o escopo do `SET LOCAL` é a
                // transação, não o savepoint. Sem esta linha, um trecho aninhado
                // deixaria o contexto de A ligado para o código que vem depois dele.
                $this->limparNaTransacao();
            }
        });
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
}
