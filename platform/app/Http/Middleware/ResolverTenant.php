<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Dominio\Tenancy\ContextoDoTenant;
use App\Dominio\Tenancy\Flags;
use App\Models\TenantDominio;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Descobre de qual instituição é a requisição, e só então abre o banco para ela.
 *
 * O tenant vem do `Host`, e de mais lugar nenhum. Aceitá-lo de um parâmetro, corpo,
 * cabeçalho ou sessão seria um IDOR com nome bonito: o cliente escolheria de quem são
 * os dados que quer ler.
 *
 * `$request->getHost()` só considera `X-Forwarded-Host` se o proxy estiver declarado em
 * `TRUSTED_PROXIES`. Isso amarra o isolamento entre instituições à correção do
 * `RUNBOOK §9` — e é por isso que `ProxyReversoTest` passou a ser um teste de
 * isolamento, e não só de geração de URL.
 */
final class ResolverTenant
{
    public function __construct(
        private readonly ContextoDoTenant $contexto,
        private readonly Flags $flags,
    ) {}

    public function handle(Request $requisicao, Closure $proximo): Response
    {
        if (! config('tenancy.ativo')) {
            // O jogo ainda é de uma instituição só. Ligar a resolução aqui não
            // protegeria nada — as 13 tabelas do jogo não têm `tenant_id`. É a Etapa C
            // que liga isto, junto com as policies que dão sentido a ele.
            return $proximo($requisicao);
        }

        // `tenant_dominios` é catálogo global, sem RLS. Tem de ser: esta é a consulta
        // que descobre o contexto, e ela roda antes de haver contexto.
        $dominio = TenantDominio::query()
            ->comHost($requisicao->getHost())
            ->with('tenant')
            ->first();

        abort_if($dominio === null, 404, 'Instituição desconhecida.');
        abort_unless($dominio->tenant?->ativo === true, 404, 'Instituição inativa.');

        // As flags viajam no `Tenant` que acabamos de carregar para resolver o host. Sem
        // esta linha, `Flags` iria buscá-lo de novo — uma consulta a mais por requisição,
        // para ler uma coluna que já está na memória.
        $this->flags->definir($dominio->tenant);

        $this->amarrarSessaoAInstituicao($requisicao, $dominio->tenant_id);

        // Uma transação por requisição. O `SET LOCAL` morre com ela — no commit, no
        // rollback, e se o processo morrer no meio. Um `SET` comum sobreviveria na
        // conexão reaproveitada pelo php-fpm e entregaria os dados desta instituição à
        // requisição seguinte, que pode ser de outra.
        DB::beginTransaction();

        try {
            $this->contexto->definirNaTransacao($dominio->tenant_id);

            $resposta = $proximo($requisicao);

            DB::commit();

            return $resposta;
        } catch (Throwable $erro) {
            DB::rollBack();

            throw $erro;
        }
    }

    /**
     * Uma sessão vale numa instituição, e só nela.
     *
     * **`sessions` não tem `tenant_id`, e não pode ter.** O `StartSession` do Laravel roda
     * antes deste middleware — é a ordem da lista de prioridade, e ela não se inverte: o
     * `ResolverTenant` precisa da sessão para fazer isto aqui. Uma policy de RLS em `sessions`
     * deixaria o site sem sessão nenhuma.
     *
     * Então a barreira mora na própria sessão. Se ela carrega a marca de outra escola, é
     * descartada — id novo, dados fora, CSRF novo.
     *
     * **O que isso protege, e o que já estava protegido.** Autenticar-se como outra pessoa não
     * era possível: o id de `usuarios` é globalmente único, então o usuário 3 nunca existe em
     * duas escolas, e o RLS o esconde da segunda. Mas o **resto** da sessão atravessava: o
     * estado da batalha — que carrega o gabarito —, as mensagens de flash, o `intended`. E
     * atravessava sem ataque nenhum no cenário mais natural que existe: um
     * `SESSION_DOMAIN=.exemplo.com`, que é o que se escreve quando as escolas são subdomínios.
     * O navegador manda o mesmo cookie para todas.
     */
    private function amarrarSessaoAInstituicao(Request $requisicao, int $tenantId): void
    {
        // Nem toda rota tem sessão: o `/healthz` está fora do grupo `web`.
        if (! $requisicao->hasSession()) {
            return;
        }

        $sessao = $requisicao->session();
        $anterior = $sessao->get('tenant_id');

        if ($anterior !== null && (int) $anterior !== $tenantId) {
            $sessao->invalidate();
            $sessao->regenerateToken();
        }

        $sessao->put('tenant_id', $tenantId);
    }
}
