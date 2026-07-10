<?php

declare(strict_types=1);

namespace App\Dominio\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

/**
 * O que o operador da plataforma precisa saber sobre cada instituição. Etapa E.2/E.3.
 *
 * **Ler as métricas de todas as escolas NÃO é furar o RLS**, e a diferença importa. Este
 * serviço não usa a conexão do dono, nem um papel com `BYPASSRLS`. Ele entra em cada
 * instituição, uma de cada vez, pelo `ContextoDoTenant::usar()` — o mesmo caminho que uma
 * requisição HTTP percorre. Toda leitura acontece dentro de um contexto declarado, sujeita
 * às mesmas policies. O `FORCE ROW LEVEL SECURITY` continua de pé.
 *
 * O que o RLS garante é que **um pedido não enxerga fora do seu contexto**. Ele nunca
 * prometeu que o servidor não pode escolher o contexto — é o servidor que o escolhe, a
 * cada requisição, a partir do `Host`. O que o `roteiro v1` chamava de `platform_admin` e
 * a Etapa D.2 recusou era outra coisa: um papel de banco que lê **através** das
 * instituições, de uma vez, sem contexto nenhum. Esse continua não existindo.
 *
 * O custo é uma transação por instituição. Com seis escolas, seis. Quando forem seiscentas,
 * isto vira um `materialized view` — e aí haverá medição para justificá-lo.
 */
final class PainelDeInstituicoes
{
    public function __construct(
        private readonly ContextoDoTenant $contexto,
        private readonly Flags $flags,
    ) {}

    /**
     * Todas as instituições — inclusive as desligadas, que são justamente as que o
     * operador acabou de provisionar e precisa enxergar.
     *
     * @return list<array{
     *   tenant: Tenant,
     *   host: string|null,
     *   flags: array<string,array{ativa:bool,padrao:bool,sobrescrita:bool,descricao:string}>,
     *   metricas: array{contas:int,herois:int,ativacao:int|null,fases_concluidas:int,estrelas:int,respostas:int,precisao:int|null,ultima_atividade:string|null}
     * }>
     */
    public function instituicoes(): array
    {
        return Tenant::query()
            ->with('dominios')
            ->orderBy('id')
            ->get()
            ->map(fn (Tenant $tenant): array => [
                'tenant' => $tenant,
                'host' => $tenant->dominios->sortByDesc('primario')->first()?->host,
                'flags' => $this->flags->todas($tenant),
                'metricas' => $this->metricas($tenant),
            ])
            ->all();
    }

    /**
     * Ativação e uso, na linguagem do roteiro v1 §9.
     *
     * **`ativacao` é a única métrica que responde a uma pergunta de produto**: de cada cem
     * contas criadas, quantas chegaram a criar um herói? Uma escola com trezentas contas e
     * quarenta heróis não tem um problema de adoção — tem um problema na tela de criação
     * de personagem. Contar contas sozinho esconderia isso.
     *
     * @return array{contas:int,herois:int,ativacao:int|null,fases_concluidas:int,estrelas:int,respostas:int,precisao:int|null,ultima_atividade:string|null}
     */
    public function metricas(Tenant $tenant): array
    {
        /** @var array{contas:int,herois:int,ativacao:int|null,fases_concluidas:int,estrelas:int,respostas:int,precisao:int|null,ultima_atividade:string|null} $metricas */
        $metricas = $this->contexto->usar($tenant->id, function (): array {
            $contas = DB::table('usuarios')->count();
            $herois = DB::table('personagens')->count();

            $progresso = DB::table('progresso_fases')->selectRaw(
                'count(*) as fases, coalesce(sum(estrelas), 0) as estrelas'
            )->first();

            $respostas = DB::table('respostas_log')->selectRaw(
                'count(*) as total, count(*) filter (where correta) as corretas, max(respondido_em) as ultima'
            )->first();

            $total = (int) ($respostas->total ?? 0);

            return [
                'contas' => $contas,
                'herois' => $herois,
                'ativacao' => $this->percentual($herois, $contas),
                'fases_concluidas' => (int) ($progresso->fases ?? 0),
                'estrelas' => (int) ($progresso->estrelas ?? 0),
                'respostas' => $total,
                'precisao' => $this->percentual((int) ($respostas->corretas ?? 0), $total),
                'ultima_atividade' => $respostas->ultima === null ? null : (string) $respostas->ultima,
            ];
        });

        return $metricas;
    }

    /** Sem denominador não há percentual — e 0% mentiria sobre uma escola que nunca abriu. */
    private function percentual(int $parte, int $total): ?int
    {
        return $total === 0 ? null : (int) round($parte / $total * 100);
    }
}
