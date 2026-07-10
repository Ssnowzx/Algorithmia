<?php

declare(strict_types=1);

namespace App\Dominio\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

/**
 * Qual instituição já tem o conteúdo do jogo — e por que só uma pode ter.
 *
 * **Os ids do conteúdo são GLOBAIS.** `fases.id = 8` pertence a uma instituição só, porque
 * a chave primária é `id`, e não `(tenant_id, id)`. O `algorithmia:importar` preserva os ids
 * do legado de propósito: `config('jogo.fases_secundarias')` referencia as fases secundárias
 * pelos números 8, 14, 20 e 32, e o `ServicoDeConquistas` as procura assim. Com ids novos, a
 * conquista `arquivista_do_vazio` ficaria inalcançável — em silêncio, e para sempre.
 *
 * Somando as duas coisas: importar o mesmo conteúdo para uma segunda instituição colide na
 * chave primária, e importá-lo com ids novos quebra a conquista. **Hoje o port serve uma
 * instituição com conteúdo.**
 *
 * Isso não é um descuido da tenancy — é o `content_packages` do roteiro v1 (Fase 3), que o
 * `design.md §5` deixou de fora de propósito, por ser uma mudança de modelo de conteúdo. O
 * que era descuido é o runbook mandar rodar um comando que não pode funcionar. Ver
 * `design.md §11`.
 *
 * Esta classe existe para que a falha chegue como frase, e cedo — e não como violação de
 * chave primária no meio de uma importação de 1.306 linhas.
 */
final class ConteudoPorInstituicao
{
    public function __construct(private readonly ContextoDoTenant $contexto) {}

    /**
     * A primeira instituição que já tem conteúdo, ignorando `$exceto`.
     *
     * A varredura entra no contexto de cada instituição, uma de cada vez — não há leitura
     * através do RLS aqui, como não há em `PainelDeInstituicoes`.
     */
    public function instituicaoComConteudo(?int $exceto = null): ?Tenant
    {
        foreach (Tenant::query()->orderBy('id')->get() as $tenant) {
            if ($tenant->id === $exceto) {
                continue;
            }

            // `fases` basta: sem ela não há lição, não há desafio, não há jogo.
            $tem = $this->contexto->usar($tenant->id, fn (): bool => DB::table('fases')->exists());

            if ($tem === true) {
                return $tenant;
            }
        }

        return null;
    }

    /** Uma linha, para o `error()` — que quebra e emoldura o texto que recebe. */
    public function resumo(Tenant $dona): string
    {
        return sprintf('A instituição "%s" já tem o conteúdo do jogo, e ele não pode ser copiado para outra.', $dona->slug);
    }

    /**
     * A explicação que o operador precisa ler, no terminal, no meio da operação.
     *
     * Vai por `line()`, e não por `error()`: o `error()` do Laravel emoldura e reflui o
     * texto, e um bloco de oito linhas sai picado, sem os nomes que se quer procurar depois.
     */
    public function porQueSoUma(Tenant $dona): string
    {
        return implode("\n", [
            sprintf('  Os ids do conteúdo são GLOBAIS: a fase de id 8 pertence à "%s", e só a ela.', $dona->slug),
            '',
            '  O importador os preserva de propósito: `config(\'jogo.fases_secundarias\')` referencia',
            '  as fases secundárias pelos números 8, 14, 20 e 32, e o `ServicoDeConquistas` as',
            '  procura assim. Copiá-las colidiria em `fases_pkey`; copiá-las com ids novos deixaria',
            '  a conquista `arquivista_do_vazio` inalcançável, em silêncio.',
            '',
            '  Hoje o port serve UMA instituição com conteúdo. Dar conteúdo a uma segunda exige o',
            '  content_packages do roteiro v1 (Fase 3) — mudança de modelo de conteúdo, com proposta',
            '  própria. Ver RUNBOOK §11.5 e design.md §11.',
        ]);
    }
}
