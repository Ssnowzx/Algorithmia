<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dominio\Tenancy\ContextoDoTenant;
use App\Dominio\Tenancy\PainelDeInstituicoes;
use App\Dominio\Tenancy\ProvisionamentoDeInstituicoes;
use App\Models\Operador;
use App\Models\RegistroDeAuditoria;
use App\Models\Tenant;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * O console do operador da plataforma. Etapa E, Fase 9 do roteiro v1.
 *
 * As três propriedades que este arquivo existe para travar:
 *
 * 1. **O console é uma porta separada.** Um mestre de escola não entra nele, e um operador
 *    não entra no jogo. São guards diferentes sobre tabelas diferentes, e `usuarios` é
 *    tenant-scoped desde a Etapa C — um "admin da plataforma" ali dentro seria o aluno de
 *    alguma escola com poder sobre as outras. É o `platform_admin` que a D.2 recusou.
 *
 * 2. **Ele não fura o RLS.** As métricas de todas as escolas são lidas entrando numa
 *    instituição de cada vez, pelo `ContextoDoTenant::usar()` — o mesmo caminho de uma
 *    requisição HTTP. Nenhuma conexão do dono, nenhum `BYPASSRLS`.
 *
 * 3. **Desligar uma escola não toca nas demais.** É o requisito de rollback do piloto.
 */
final class ConsoleDoOperadorTest extends TestCase
{
    use RefreshDatabase;

    private const HOST = 'console.algorithmia.test';

    private Mundo $mundo;

    protected function setUp(): void
    {
        // As rotas do console só são registradas se `CONSOLE_HOST` existir, e o registro
        // acontece quando a aplicação sobe — dentro de `parent::setUp()`. Depois é tarde.
        $this->definirHostDoConsole(self::HOST);

        parent::setUp();

        $this->mundo = new Mundo;

        config(['app.debug' => false, 'session.driver' => 'database']);
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        $dono = DB::connection('pgsql_dono');
        $ids = $dono->table('tenants')->where('slug', 'like', 'piloto%')->pluck('id');

        $dono->table('tenant_dominios')->whereIn('tenant_id', $ids)->delete();
        $dono->table('tenants')->whereIn('id', $ids)->delete();

        DB::beginTransaction();

        parent::tearDown();

        $this->definirHostDoConsole(null);
    }

    private function definirHostDoConsole(?string $host): void
    {
        if ($host === null) {
            putenv('CONSOLE_HOST');
            unset($_ENV['CONSOLE_HOST'], $_SERVER['CONSOLE_HOST']);

            return;
        }

        putenv("CONSOLE_HOST={$host}");
        $_ENV['CONSOLE_HOST'] = $host;
        $_SERVER['CONSOLE_HOST'] = $host;
    }

    private function operador(bool $ativo = true): Operador
    {
        return Operador::create([
            'nome' => 'Operadora', 'email' => 'op@algorithmia.test',
            'senha_hash' => Hash::make('senha-de-doze-ou-mais'), 'ativo' => $ativo,
        ]);
    }

    private function url(string $caminho = '/instituicoes'): string
    {
        return 'http://'.self::HOST.'/console'.$caminho;
    }

    /** A raiz do console, sem barra final: é o que `route('console.entrar')` gera. */
    private function urlDoLogin(): string
    {
        return 'http://'.self::HOST.'/console';
    }

    // ------------------------------------------------------------------ a porta

    #[Test]
    public function o_console_nao_atende_no_dominio_de_uma_escola(): void
    {
        // ARRANGE: `localhost` é o host do tenant padrão.
        $this->actingAs($this->operador(), 'operador');

        // ACT + ASSERT: a rota só existe no domínio do console.
        $this->get('http://localhost/console/instituicoes')->assertNotFound();
    }

    #[Test]
    public function sem_sessao_o_operador_cai_no_login_do_console_e_nao_no_do_jogo(): void
    {
        // ARRANGE + ACT + ASSERT: o login do jogo, no host do console, não resolveria
        // instituição nenhuma — devolveria 404 a quem só queria entrar.
        $this->get($this->url())->assertRedirect($this->urlDoLogin());
    }

    #[Test]
    public function um_mestre_de_escola_nao_entra_no_console(): void
    {
        // ARRANGE: o mestre existe, tem senha, e administra a escola dele.
        Usuario::create([
            'nome' => 'Mestre', 'email' => 'mestre@algorithmia.test',
            'senha_hash' => Hash::make('qwe123'), 'papel' => 'mestre',
        ]);

        // ACT: as credenciais são válidas — no guard do JOGO.
        $resposta = $this->post($this->urlDoLogin(), [
            'email' => 'mestre@algorithmia.test', 'password' => 'qwe123',
        ]);

        // ASSERT: `operadores` e `usuarios` não se misturam.
        $resposta->assertSessionHasErrors('email');
        $this->assertGuest('operador');
    }

    #[Test]
    public function um_operador_nao_entra_no_jogo(): void
    {
        // ARRANGE
        $this->operador();

        // ACT
        $resposta = $this->post('http://localhost/entrar', [
            'email' => 'op@algorithmia.test', 'password' => 'senha-de-doze-ou-mais',
        ]);

        // ASSERT
        $resposta->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function um_operador_desligado_recebe_a_mesma_recusa_de_quem_erra_a_senha(): void
    {
        // ARRANGE: dizer "sua conta foi desativada" confirmaria que o e-mail existe.
        $this->operador(ativo: false);

        // ACT
        $resposta = $this->post($this->urlDoLogin(), [
            'email' => 'op@algorithmia.test', 'password' => 'senha-de-doze-ou-mais',
        ]);

        // ASSERT
        $resposta->assertSessionHasErrors('email');
        $this->assertGuest('operador');
    }

    #[Test]
    public function um_operador_ativo_entra(): void
    {
        // ARRANGE
        $this->operador();

        // ACT
        $resposta = $this->post($this->urlDoLogin(), [
            'email' => 'op@algorithmia.test', 'password' => 'senha-de-doze-ou-mais',
        ]);

        // ASSERT
        $resposta->assertRedirect($this->url());
        $this->assertAuthenticated('operador');
    }

    // ------------------------------------------------------------------ o painel

    #[Test]
    public function o_painel_lista_as_instituicoes_com_as_metricas(): void
    {
        // ARRANGE: dois alunos, um deles com herói. Ativação = 50%.
        $this->mundo->heroi('mago');
        Usuario::create(['nome' => 'Sem herói', 'email' => 'semheroi@algorithmia.test', 'senha_hash' => 'x']);

        $this->actingAs($this->operador(), 'operador');

        // ACT + ASSERT
        $this->get($this->url())
            ->assertOk()
            ->assertSee('padrao')
            ->assertSee('50%');
    }

    /**
     * As métricas de cada escola são lidas dentro do contexto dela. Se o painel lesse sem
     * contexto — ou com a conexão do dono —, os números das duas se somariam.
     */
    #[Test]
    public function as_metricas_de_uma_escola_nao_contam_os_alunos_da_outra(): void
    {
        // ARRANGE: a padrão ganha um herói; a piloto, dois usuários e nenhum.
        $this->mundo->heroi('mago');

        $piloto = $this->provisionarPiloto();

        app(ContextoDoTenant::class)->usar($piloto->id, function (): void {
            Usuario::create(['nome' => 'A', 'email' => 'a@piloto.test', 'senha_hash' => 'x']);
            Usuario::create(['nome' => 'B', 'email' => 'b@piloto.test', 'senha_hash' => 'x']);
        });

        // ACT
        $metricas = app(PainelDeInstituicoes::class)->metricas($piloto);

        // ASSERT: dois usuários, nenhum herói. O da escola padrão não entrou na conta.
        $this->assertSame(2, $metricas['contas']);
        $this->assertSame(0, $metricas['herois']);
        $this->assertSame(0, $metricas['ativacao']);
    }

    // ------------------------------------------------------------------ as ações

    #[Test]
    public function ligar_uma_instituicao_vazia_e_recusado_com_a_instrucao_do_conserto(): void
    {
        // ARRANGE
        $piloto = $this->provisionarPiloto();
        $this->actingAs($this->operador(), 'operador');

        // ACT
        $resposta = $this->from($this->url())->post($this->url("/instituicoes/{$piloto->id}/ativar"));

        // ASSERT
        $resposta->assertRedirect($this->url());
        $this->assertStringContainsString('algorithmia:importar', (string) session('erro'));
        $this->assertFalse($piloto->refresh()->ativo);
    }

    #[Test]
    public function ligar_uma_instituicao_semeada_funciona_e_fica_auditado(): void
    {
        // ARRANGE
        $piloto = $this->provisionarPiloto(semeada: true);
        $operador = $this->operador();
        $this->actingAs($operador, 'operador');

        // Em produção o console roda **sem contexto de tenant**: as rotas dele saem do
        // `ResolverTenant`. O `TestCase` deixa o contexto na instituição padrão, e sem esta
        // linha a auditoria do operador nasceria atribuída a uma escola — o teste afirmaria
        // algo que não acontece em produção.
        app(ContextoDoTenant::class)->limparNaTransacao();

        // ACT
        $this->from($this->url())->post($this->url("/instituicoes/{$piloto->id}/ativar"))
            ->assertRedirect($this->url());

        // ASSERT
        $this->assertTrue($piloto->refresh()->ativo);

        $linha = RegistroDeAuditoria::query()->where('acao', 'tenant.ativar')->firstOrFail();

        // Os ids de `usuarios` e `operadores` colidem: sem `autor_tipo`, esta linha não
        // saberia dizer quem agiu.
        $this->assertSame('operador', $linha->autor_tipo);
        $this->assertSame($operador->id, $linha->autor_id);
        $this->assertSame($operador->email, $linha->autor_email);

        // E ela não pertence a escola nenhuma — o operador não é de nenhuma.
        $this->assertNull($linha->tenant_id);
    }

    /** O requisito de rollback do piloto: desligá-lo não pode derrubar as outras escolas. */
    #[Test]
    public function desligar_uma_instituicao_nao_toca_nas_demais(): void
    {
        // ARRANGE
        $piloto = $this->provisionarPiloto(semeada: true, ativa: true);
        $this->actingAs($this->operador(), 'operador');

        // ACT
        $this->from($this->url())->post($this->url("/instituicoes/{$piloto->id}/desativar"));

        // ASSERT
        $this->assertFalse($piloto->refresh()->ativo);
        $this->assertTrue(Tenant::query()->where('slug', 'padrao')->firstOrFail()->ativo);
    }

    #[Test]
    public function a_flag_muda_so_a_instituicao_escolhida(): void
    {
        // ARRANGE
        $piloto = $this->provisionarPiloto();
        $this->actingAs($this->operador(), 'operador');

        // ACT
        $this->from($this->url())
            ->post($this->url("/instituicoes/{$piloto->id}/flag"), ['chave' => 'turmas', 'estado' => 'ligar']);

        // ASSERT
        $this->assertSame(['turmas' => true], $piloto->refresh()->flags);
        $this->assertSame([], Tenant::query()->where('slug', 'padrao')->firstOrFail()->flags);
    }

    #[Test]
    public function voltar_ao_padrao_remove_a_chave_em_vez_de_gravar_o_valor_de_hoje(): void
    {
        // ARRANGE
        $piloto = $this->provisionarPiloto();
        $this->actingAs($this->operador(), 'operador');
        $this->post($this->url("/instituicoes/{$piloto->id}/flag"), ['chave' => 'turmas', 'estado' => 'ligar']);

        // ACT
        $this->from($this->url())
            ->post($this->url("/instituicoes/{$piloto->id}/flag"), ['chave' => 'turmas', 'estado' => 'padrao']);

        // ASSERT: no dia em que o padrão mudar, esta escola o acompanha.
        $this->assertSame([], $piloto->refresh()->flags);
    }

    #[Test]
    public function uma_flag_forjada_e_recusada(): void
    {
        // ARRANGE: a chave vem de um `<button value>` desenhado pelo próprio console.
        $piloto = $this->provisionarPiloto();
        $this->actingAs($this->operador(), 'operador');

        // ACT + ASSERT
        $this->from($this->url())
            ->post($this->url("/instituicoes/{$piloto->id}/flag"), ['chave' => 'turmsa', 'estado' => 'ligar'])
            ->assertSessionHasErrors('chave');
    }

    #[Test]
    public function um_visitante_sem_sessao_nao_liga_escola_nenhuma(): void
    {
        // ARRANGE
        $piloto = $this->provisionarPiloto(semeada: true);

        // ACT + ASSERT
        $this->post($this->url("/instituicoes/{$piloto->id}/ativar"))->assertRedirect($this->urlDoLogin());

        $this->assertFalse($piloto->refresh()->ativo);
    }

    private function provisionarPiloto(bool $semeada = false, bool $ativa = false): Tenant
    {
        $piloto = app(ProvisionamentoDeInstituicoes::class)
            ->provisionar('Escola Piloto', 'piloto', 'piloto.algorithmia.test');

        if ($semeada) {
            app(ContextoDoTenant::class)
                ->usar($piloto->id, (new Mundo)->mundoDoSmoke(...));
        }

        if ($ativa) {
            $piloto->update(['ativo' => true]);
        }

        return $piloto->refresh();
    }
}
