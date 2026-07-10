<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * O jogador loga e cai na tela de login de novo.
 *
 * Foi o que aconteceu quando o RLS entrou nas 13 tabelas. `ContextoDoPedido` põe o
 * `usuario_id` no contexto do log, e para isso chama `$request->user()` — uma consulta a
 * `usuarios`, que é tenant-scoped. Ele rodava **antes** do `ResolverTenant`: a consulta
 * saía sem contexto, o RLS a devolvia vazia, e a sessão nunca se estabelecia.
 *
 * Nenhum teste pegou. Todos autenticavam com `actingAs()`, que não passa por sessão nem
 * por HTTP. Este passa.
 */
final class SessaoSobRlsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function o_login_por_http_persiste_entre_requisicoes(): void
    {
        // ARRANGE: o `tenant_id` vem do DEFAULT, que lê o contexto posto pelo TestCase.
        Usuario::create([
            'nome' => 'Aluna',
            'email' => 'aluna@algorithmia.test',
            'senha_hash' => Hash::make('senha-correta'),
        ]);

        // ACT: um login de verdade, com sessão, e não `actingAs()`.
        $this->post(route('login'), [
            'email' => 'aluna@algorithmia.test',
            'password' => 'senha-correta',
        ])->assertRedirect(route('mapa'));

        // ASSERT: a requisição SEGUINTE ainda conhece o jogador.
        $this->assertAuthenticated();
        $this->get(route('mapa'))->assertRedirect(route('personagem.criar'));
    }

    /**
     * A garantia estrutural por trás do teste acima: nada consulta o banco antes de o
     * contexto do tenant existir. Se alguém puser um middleware que toque `usuarios`
     * acima do resolvedor, a sessão volta a se perder — em produção, e em silêncio.
     */
    #[Test]
    public function o_resolvedor_de_tenant_e_o_primeiro_middleware_apendado_ao_grupo_web(): void
    {
        // ARRANGE
        $grupo = app(\Illuminate\Contracts\Http\Kernel::class)->getMiddlewareGroups()['web'];

        $nossos = array_values(array_filter(
            $grupo,
            fn (string $m): bool => str_starts_with($m, 'App\\Http\\Middleware\\')
        ));

        // ASSERT
        $this->assertNotEmpty($nossos);
        $this->assertSame(\App\Http\Middleware\ResolverTenant::class, $nossos[0]);
    }

    #[Test]
    public function o_healthz_responde_sem_contexto_de_tenant(): void
    {
        // ARRANGE: o host não pertence a instituição nenhuma.
        // ACT + ASSERT: e ainda assim o endpoint responde — ele existe para dizer se o
        // banco está de pé, não para saber de quem são os dados.
        $this->get('http://ninguem.algorithmia.test/healthz')->assertOk();

        $this->assertSame(1, (int) DB::selectOne('SELECT 1 AS n')->n);
    }
}
