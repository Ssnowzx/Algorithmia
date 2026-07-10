<?php

declare(strict_types=1);

namespace App\Dominio\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

/**
 * Quais instituições já têm o conteúdo do jogo.
 *
 * **Os ids do conteúdo são globais.** `fases.id = 8` pertence a uma instituição só, porque a
 * chave primária é `id`, e não `(tenant_id, id)`. O `algorithmia:importar` preserva os ids do
 * legado de propósito: o progresso que ele importa junto aponta para eles. Isso só funciona
 * uma vez — a segunda importação colide em `fases_pkey`.
 *
 * Isso **não** significa que só uma escola pode ter conteúdo. Significa que a segunda não o
 * recebe do legado, e sim de uma cópia com ids novos: o `algorithmia:tenant:semear`. Ele
 * existe porque duas amarras foram cortadas — `arquivista_do_vazio` deixou de referenciar as
 * fases por id, e `conquistas.codigo` deixou de ser único global.
 *
 * Esta classe existe para que a diferença chegue como frase, e cedo — e não como violação de
 * chave primária no meio de uma importação de 1.306 linhas.
 */
final class ConteudoPorInstituicao
{
    public function __construct(private readonly ContextoDoTenant $contexto) {}

    /**
     * As instituições que já têm conteúdo, ignorando `$exceto`.
     *
     * A varredura entra no contexto de cada uma, uma de cada vez — não há leitura através do
     * RLS aqui, como não há em `PainelDeInstituicoes`.
     *
     * @return list<Tenant>
     */
    public function todasComConteudo(?int $exceto = null): array
    {
        $comConteudo = [];

        foreach (Tenant::query()->orderBy('id')->get() as $tenant) {
            if ($tenant->id === $exceto) {
                continue;
            }

            // `fases` basta: sem ela não há lição, não há desafio, não há jogo.
            if ($this->contexto->usar($tenant->id, fn (): bool => DB::table('fases')->exists()) === true) {
                $comConteudo[] = $tenant;
            }
        }

        return $comConteudo;
    }

    public function instituicaoComConteudo(?int $exceto = null): ?Tenant
    {
        return $this->todasComConteudo($exceto)[0] ?? null;
    }

    /** Uma linha, para o `error()` — que quebra e emoldura o texto que recebe. */
    public function resumo(Tenant $dona): string
    {
        return sprintf('A instituição "%s" já tem o conteúdo do jogo, e o legado não pode ser importado duas vezes.', $dona->slug);
    }

    /**
     * A explicação, e o comando certo. Vai por `line()`, e não por `error()`: o `error()` do
     * Laravel emoldura e reflui o texto, e um bloco de oito linhas sai picado.
     */
    public function porQueSoUma(Tenant $dona, string $destino): string
    {
        return implode("\n", [
            '  O importador preserva os ids do legado — o progresso que ele traz aponta para eles —',
            '  e `fases.id` é chave primária GLOBAL. Uma segunda importação colidiria em `fases_pkey`.',
            '',
            '  Mas uma escola nova não quer os alunos da primeira: quer o CONTEÚDO. Copie-o, com',
            '  ids novos:',
            '',
            sprintf('    php artisan algorithmia:tenant:semear %s --de=%s', $destino, $dona->slug),
            '',
            '  Ver RUNBOOK §11.5.',
        ]);
    }
}
