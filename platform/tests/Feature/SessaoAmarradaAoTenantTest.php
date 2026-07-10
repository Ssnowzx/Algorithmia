<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dominio\Tenancy\ContextoDoTenant;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * Uma sessão vale numa instituição, e só nela.
 *
 * **O vazamento.** `sessions` não tem `tenant_id` — e não pode ter: o `StartSession` do Laravel
 * roda **antes** do `ResolverTenant` (é a ordem da lista de prioridade), então a sessão é lida e
 * gravada sem contexto de tenant. Uma policy ali deixaria o site sem sessão nenhuma.
 *
 * **O que NÃO era possível, e é bom saber por quê.** Assumir a identidade de outra pessoa não
 * era: a sessão guarda o id numérico do usuário, e `usuarios.id` é chave primária **global** —
 * o usuário 3 nunca existe em duas escolas. Sob o RLS da escola B, o id 3 da A simplesmente não
 * está lá, e o `Authenticate` devolve um visitante. A chave primária global, que causa o
 * problema do conteúdo, aqui protege por acidente.
 *
 * **O que atravessava.** Todo o resto da sessão: o estado da batalha — que carrega o gabarito —,
 * as mensagens de flash, o `intended`, o token de CSRF. E atravessava sem ataque nenhum no
 * cenário mais natural que existe: um `SESSION_DOMAIN=.exemplo.com`, que é o que se escreve
 * quando as escolas são subdomínios. O navegador manda o mesmo cookie para todas elas.
 *
 * A correção: o `ResolverTenant` **amarra a sessão à instituição** e descarta a que vier de
 * outra. Ele roda depois do `StartSession` e antes do `Authenticate` — a única janela em que
 * isso é possível.
 */
final class SessaoAmarradaAoTenantTest extends TestCase
{
    use RefreshDatabase;

    private int $escolaB;

    protected function setUp(): void
    {
        parent::setUp();

        $dono = DB::connection('pgsql_dono');

        $this->escolaB = (int) $dono->table('tenants')->insertGetId([
            'nome' => 'Escola B', 'slug' => 'escola-b', 'ativo' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $dono->table('tenant_dominios')->insert([
            'tenant_id' => $this->escolaB, 'host' => 'b.algorithmia.test', 'primario' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        $dono = DB::connection('pgsql_dono');
        $ids = $dono->table('tenants')->where('slug', 'escola-b')->pluck('id');

        $dono->table('personagens')->whereIn('tenant_id', $ids)->delete();
        $dono->table('usuarios')->whereIn('tenant_id', $ids)->delete();
        $dono->table('tenant_dominios')->whereIn('tenant_id', $ids)->delete();
        $dono->table('tenants')->whereIn('id', $ids)->delete();

        DB::beginTransaction();

        parent::tearDown();
    }

    /**
     * A sessão de uma escola, apresentada no host de outra, não autentica ninguém.
     *
     * `actingAs()` não passa por HTTP nem por sessão — ele não veria isto. Aqui a sessão é
     * escrita à mão, como um cookie replicado por `SESSION_DOMAIN` compartilhado seria.
     */
    #[Test]
    public function uma_sessao_de_outra_escola_nao_autentica_ninguem(): void
    {
        // ARRANGE
        $daPadrao = Usuario::create(['nome' => 'Ana', 'email' => 'ana@a.test', 'senha_hash' => Hash::make('x')]);

        /** @var \Illuminate\Auth\SessionGuard $guard */
        $guard = Auth::guard('web');

        // ACT: a sessão da padrão, levada ao host da escola B. A chave é a que o `SessionGuard`
        // usa — pedimos a ele, em vez de adivinhar.
        $this->withSession([
            'tenant_id' => 1,
            $guard->getName() => $daPadrao->id,
        ])->get('http://b.algorithmia.test/historia')->assertOk();

        // ASSERT
        $this->assertGuest();
        $this->assertSame($this->escolaB, session('tenant_id'), 'a sessão não foi remarcada');
        $this->assertNull(session($guard->getName()), 'a credencial da outra escola sobreviveu');
    }

    /**
     * Um usuário com o id de outra escola nem existe. `usuarios.id` é chave primária GLOBAL,
     * e o RLS esconde a linha da outra instituição — não há como colidir.
     *
     * Isto é registro, não celebração: a proteção é acidental, e some no dia em que os ids
     * virarem `(tenant_id, id)`. É por isso que a sessão é amarrada, e não só os ids.
     */
    #[Test]
    public function nao_existe_um_usuario_com_o_mesmo_id_em_duas_escolas(): void
    {
        // ARRANGE
        $daPadrao = Usuario::create(['nome' => 'Ana', 'email' => 'ana@a.test', 'senha_hash' => 'x']);

        // ACT + ASSERT
        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->expectExceptionMessageMatches('/usuarios_pkey/');

        app(ContextoDoTenant::class)->usar($this->escolaB, function () use ($daPadrao): void {
            DB::table('usuarios')->insert([
                'id' => $daPadrao->id, 'nome' => 'Bruno', 'email' => 'bruno@b.test', 'senha_hash' => 'y',
            ]);
        });
    }

    /** O estado da batalha carrega o gabarito. Ele não pode acompanhar o navegador até outra escola. */
    #[Test]
    public function os_dados_da_sessao_nao_atravessam_para_a_outra_escola(): void
    {
        // ARRANGE
        $this->withSession(['tenant_id' => 1, 'batalha' => ['gabarito' => 'da escola padrao']]);

        // ACT
        $this->get('http://b.algorithmia.test/historia')->assertOk();

        // ASSERT
        $this->assertNull(session('batalha'));
    }

    /** A sessão do próprio host continua funcionando — a correção não pode deslogar todo mundo. */
    #[Test]
    public function a_sessao_da_propria_escola_continua_valendo(): void
    {
        // ARRANGE
        Usuario::create(['nome' => 'Ana', 'email' => 'ana@a.test', 'senha_hash' => Hash::make('segredo123')]);

        // ACT: login de verdade, por HTTP, no host da padrão.
        $this->post('http://localhost/entrar', ['email' => 'ana@a.test', 'password' => 'segredo123'])
            ->assertSessionHasNoErrors();

        // ASSERT: a próxima requisição, no mesmo host, continua autenticada.
        $this->assertAuthenticated();
        $this->get('http://localhost/mapa')->assertRedirect('http://localhost/personagem/criar');
    }

    /** A marca da instituição entra na sessão no primeiro pedido, sem exigir login. */
    #[Test]
    public function o_tenant_e_gravado_na_sessao_do_visitante(): void
    {
        // ARRANGE + ACT
        $this->get('http://b.algorithmia.test/historia')->assertOk();

        // ASSERT
        $this->assertSame($this->escolaB, session('tenant_id'));
    }

    /** Voltar ao host de origem não pode manter a sessão da escola que se acabou de visitar. */
    #[Test]
    public function cada_troca_de_host_remarca_a_sessao(): void
    {
        // ARRANGE + ACT + ASSERT
        $this->get('http://localhost/historia')->assertOk();
        $padrao = session('tenant_id');

        $this->get('http://b.algorithmia.test/historia')->assertOk();
        $this->assertSame($this->escolaB, session('tenant_id'));

        $this->get('http://localhost/historia')->assertOk();
        $this->assertSame($padrao, session('tenant_id'));
    }
}
