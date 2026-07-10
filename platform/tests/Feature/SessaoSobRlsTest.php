<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Middleware\ResolverTenant;
use App\Models\Usuario;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Route as RotaDoLaravel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
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
     * A garantia estrutural, e ela olha o pipeline **reunido** — não a lista de `append`.
     *
     * A ordem em `bootstrap/app.php` é sugestão: o Laravel reordena tudo pela lista de
     * prioridade, e `Authenticate` tem lugar fixo nela. Foi assim que o resolvedor foi
     * parar depois dele, e o jogador virou visitante. Um teste que lesse a lista de
     * `append` teria dito que estava tudo certo.
     */
    #[Test]
    public function o_resolvedor_de_tenant_roda_antes_do_authenticate(): void
    {
        // ARRANGE
        app(Kernel::class); // sincroniza os grupos do kernel para o router

        $rota = collect(Route::getRoutes()->getRoutes())
            ->first(fn (RotaDoLaravel $r): bool => $r->getName() === 'mapa');

        $this->assertNotNull($rota);

        // ACT: é exatamente o que a requisição vai executar.
        $pipeline = Route::gatherRouteMiddleware($rota);
        $posicao = fn (string $classe): int|false => array_search($classe, $pipeline, true);

        // ASSERT
        $resolvedor = $posicao(ResolverTenant::class);
        $autenticador = $posicao(Authenticate::class);

        $this->assertIsInt($resolvedor, 'ResolverTenant sumiu do pipeline');
        $this->assertIsInt($autenticador, 'Authenticate sumiu do pipeline');
        $this->assertLessThan(
            $autenticador,
            $resolvedor,
            'Authenticate consulta `usuarios`, que é tenant-scoped: sem contexto, o RLS o cega'
        );
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
