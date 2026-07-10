<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dominio\Tenancy\ContextoDoTenant;
use App\Dominio\Tenancy\InstituicaoInjogavel;
use App\Dominio\Tenancy\ProvisionamentoDeInstituicoes;
use App\Dominio\Tenancy\ProvisionamentoInvalido;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * Etapa E.2: provisionar uma instituição piloto.
 *
 * A regra que este arquivo existe para travar: **provisionar e semear são dois atos, e
 * entre eles a escola é injogável.** Uma escola ativa e vazia reprova o `algorithmia:smoke`,
 * que é o portão do `bin/deploy.sh` — e portanto reprova **todo deploy**, até alguém
 * desconfiar. Foi o que aconteceu no ensaio do corte (C.6), com um build correto.
 *
 * O `DEFAULT false` da coluna impede o primeiro erro. O smoke dentro de `ativar()` impede
 * o segundo. A ordem deixa de ser prosa no runbook e passa a ser código que não cede.
 */
final class ProvisionamentoDeInstituicoesTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mundo = new Mundo;

        // O smoke checa `APP_DEBUG` e o driver de sessão, e o ambiente de teste tem os
        // dois "errados". Aqui interessa o portão da ativação, não essas duas.
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
    }

    private function provisionamento(): ProvisionamentoDeInstituicoes
    {
        return app(ProvisionamentoDeInstituicoes::class);
    }

    // ------------------------------------------------------------------ provisionar

    #[Test]
    public function uma_instituicao_nova_nasce_desligada_e_com_dominio(): void
    {
        // ARRANGE + ACT
        $tenant = $this->provisionamento()->provisionar('Escola Piloto', null, 'piloto.exemplo.com');

        // ASSERT
        $this->assertFalse($tenant->ativo, 'uma escola sem conteúdo não pode nascer no ar');
        $this->assertSame('escola-piloto', $tenant->slug);
        $this->assertSame('piloto.exemplo.com', $tenant->hostPrimario());
    }

    #[Test]
    public function o_slug_pode_ser_dado_a_mao(): void
    {
        $tenant = $this->provisionamento()->provisionar('Escola Piloto', 'piloto-1', 'piloto1.exemplo.com');

        $this->assertSame('piloto-1', $tenant->slug);
    }

    #[Test]
    public function um_host_com_esquema_ou_porta_e_recusado(): void
    {
        // ARRANGE: `tenant_dominios.host` guarda só o host. Um `https://` aqui nunca
        // casaria com o `getHost()` do resolvedor — 404 com o banco cheio.
        $this->expectException(ProvisionamentoInvalido::class);

        $this->provisionamento()->provisionar('Escola Piloto', 'piloto-2', 'https://piloto.exemplo.com');
    }

    #[Test]
    public function um_host_com_porta_e_recusado(): void
    {
        $this->expectException(ProvisionamentoInvalido::class);

        $this->provisionamento()->provisionar('Escola Piloto', 'piloto-3', 'piloto.exemplo.com:8443');
    }

    #[Test]
    public function um_host_ja_tomado_e_recusado_mesmo_em_outra_caixa(): void
    {
        // ARRANGE: o índice único do banco é sobre `lower(host)`.
        $this->provisionamento()->provisionar('Escola Piloto', 'piloto-4', 'piloto.exemplo.com');

        // ACT + ASSERT
        $this->expectException(ProvisionamentoInvalido::class);

        $this->provisionamento()->provisionar('Outra', 'piloto-5', 'PILOTO.exemplo.COM');
    }

    #[Test]
    public function um_slug_repetido_e_recusado(): void
    {
        $this->provisionamento()->provisionar('Escola Piloto', 'piloto-6', 'p6.exemplo.com');

        $this->expectException(ProvisionamentoInvalido::class);

        $this->provisionamento()->provisionar('Outra', 'piloto-6', 'p7.exemplo.com');
    }

    /** Uma instituição sem domínio é inalcançável, e o resolvedor a esconderia sem dizer por quê. */
    #[Test]
    public function o_tenant_e_o_dominio_nascem_na_mesma_transacao(): void
    {
        // ARRANGE + ACT + ASSERT: o host inválido derruba a validação ANTES de qualquer
        // escrita, e nenhum tenant órfão fica para trás.
        try {
            $this->provisionamento()->provisionar('Escola Piloto', 'piloto-7', 'host invalido');
        } catch (ProvisionamentoInvalido) {
            // esperado
        }

        $this->assertFalse(Tenant::query()->where('slug', 'piloto-7')->exists());
    }

    // ------------------------------------------------------------------ o portão

    #[Test]
    public function ativar_uma_instituicao_vazia_e_recusado(): void
    {
        // ARRANGE: provisionada, jamais semeada. É o estado em que o C.6 travou os deploys.
        $tenant = $this->provisionamento()->provisionar('Escola Piloto', 'piloto-8', 'p8.exemplo.com');

        // ACT + ASSERT
        $this->expectException(InstituicaoInjogavel::class);
        $this->expectExceptionMessageMatches('/algorithmia:importar --tenant=piloto-8/');

        try {
            $this->provisionamento()->ativar($tenant);
        } finally {
            // …e ela continua desligada. Falhar no meio não pode deixá-la meio ligada.
            $this->assertFalse($tenant->refresh()->ativo);
        }
    }

    #[Test]
    public function a_excecao_carrega_a_saida_do_smoke(): void
    {
        // ARRANGE
        $tenant = $this->provisionamento()->provisionar('Escola Piloto', 'piloto-9', 'p9.exemplo.com');

        // ACT
        try {
            $this->provisionamento()->ativar($tenant);
            $this->fail('a instituição vazia foi ativada');
        } catch (InstituicaoInjogavel $erro) {
            // ASSERT: o operador tem de ver QUAL verificação caiu, sem rodar o smoke à mão.
            $this->assertStringContainsString('Conteúdo importado', $erro->saidaDoSmoke);
        }
    }

    #[Test]
    public function uma_instituicao_semeada_e_ativada(): void
    {
        // ARRANGE
        $tenant = $this->provisionamento()->provisionar('Escola Piloto', 'piloto-10', 'p10.exemplo.com');
        $this->semearMundoMinimo($tenant);

        // ACT
        $this->provisionamento()->ativar($tenant);

        // ASSERT
        $this->assertTrue($tenant->refresh()->ativo);
    }

    #[Test]
    public function ativar_o_que_ja_esta_ativo_nao_roda_o_smoke_nem_falha(): void
    {
        // ARRANGE: a instituição padrão está ativa, e no ambiente de teste está vazia —
        // o smoke a reprovaria. Ativar de novo não pode reprovar.
        $padrao = Tenant::query()->where('slug', 'padrao')->firstOrFail();
        $this->assertTrue($padrao->ativo);

        // ACT + ASSERT: não levanta.
        $this->provisionamento()->ativar($padrao);

        $this->assertTrue($padrao->refresh()->ativo);
    }

    // ------------------------------------------------------------------ desligar

    #[Test]
    public function desligar_nunca_roda_o_smoke(): void
    {
        // ARRANGE: uma escola quebrada é justamente a que se quer tirar do ar. Exigir que
        // ela esteja jogável para poder desligá-la seria trancá-la por dentro.
        $padrao = Tenant::query()->where('slug', 'padrao')->firstOrFail();

        // ACT
        $this->provisionamento()->desativar($padrao);

        // ASSERT
        $this->assertFalse($padrao->refresh()->ativo);
    }

    #[Test]
    public function desligar_uma_instituicao_nao_toca_nas_outras(): void
    {
        // ARRANGE
        $piloto = $this->provisionamento()->provisionar('Escola Piloto', 'piloto-11', 'p11.exemplo.com');
        $this->semearMundoMinimo($piloto);
        $this->provisionamento()->ativar($piloto);

        // ACT
        $this->provisionamento()->desativar($piloto);

        // ASSERT: o requisito de rollback do piloto (roteiro v1 §9).
        $this->assertFalse($piloto->refresh()->ativo);
        $this->assertTrue(Tenant::query()->where('slug', 'padrao')->firstOrFail()->ativo);
    }

    // ------------------------------------------------------------------ os comandos

    #[Test]
    public function o_comando_novo_exige_host(): void
    {
        $this->artisan('algorithmia:tenant:novo', ['nome' => 'Escola Piloto'])
            ->expectsOutputToContain('Falta `--host`')
            ->assertFailed();
    }

    #[Test]
    public function o_comando_novo_ensina_a_ordem_dos_proximos_passos(): void
    {
        $this->artisan('algorithmia:tenant:novo', ['nome' => 'Escola Piloto', '--host' => 'p12.exemplo.com', '--slug' => 'piloto-12'])
            ->expectsOutputToContain('DESLIGADA')
            ->expectsOutputToContain('algorithmia:importar --tenant=piloto-12')
            ->expectsOutputToContain('algorithmia:tenant:ativar piloto-12')
            ->assertSuccessful();
    }

    #[Test]
    public function o_comando_ativar_reprova_a_instituicao_vazia(): void
    {
        // ARRANGE
        $this->provisionamento()->provisionar('Escola Piloto', 'piloto-13', 'p13.exemplo.com');

        // ACT + ASSERT
        $this->artisan('algorithmia:tenant:ativar', ['slug' => 'piloto-13'])->assertFailed();

        $this->assertFalse(Tenant::query()->where('slug', 'piloto-13')->firstOrFail()->ativo);
    }

    #[Test]
    public function o_comando_ativar_recusa_um_slug_inexistente(): void
    {
        $this->artisan('algorithmia:tenant:ativar', ['slug' => 'nao-existe'])->assertFailed();
    }

    #[Test]
    public function o_comando_de_flag_lista_e_muda(): void
    {
        // ARRANGE + ACT + ASSERT
        $this->artisan('algorithmia:tenant:flag', ['slug' => 'padrao'])
            ->expectsOutputToContain('turmas')
            ->assertSuccessful();

        $this->artisan('algorithmia:tenant:flag', ['slug' => 'padrao', 'chave' => 'turmas', '--ligar' => true])
            ->assertSuccessful();

        $this->assertSame(['turmas' => true], Tenant::query()->where('slug', 'padrao')->firstOrFail()->flags);
    }

    #[Test]
    public function o_comando_de_flag_recusa_duas_intencoes_ao_mesmo_tempo(): void
    {
        $this->artisan('algorithmia:tenant:flag', [
            'slug' => 'padrao', 'chave' => 'turmas', '--ligar' => true, '--desligar' => true,
        ])->assertFailed();
    }

    #[Test]
    public function o_comando_de_flag_recusa_uma_chave_desconhecida(): void
    {
        // ARRANGE + ACT + ASSERT: erro de digitação vira frase, não desliga nada.
        $this->artisan('algorithmia:tenant:flag', ['slug' => 'padrao', 'chave' => 'turmsa', '--ligar' => true])
            ->expectsOutputToContain('turmsa')
            ->assertFailed();
    }

    #[Test]
    public function o_comando_listar_mostra_as_desligadas_e_a_ativacao(): void
    {
        // ARRANGE: a desligada é justamente a que o operador acabou de criar.
        $this->provisionamento()->provisionar('Escola Piloto', 'piloto-14', 'p14.exemplo.com');

        // ACT + ASSERT
        $this->artisan('algorithmia:tenant:listar')
            ->expectsOutputToContain('piloto-14')
            ->expectsOutputToContain('padrao')
            ->assertSuccessful();
    }

    /**
     * O mínimo que o smoke exige: um mestre, uma lição com desafio, um item, uma conquista,
     * o Fragmento da IA, o confronto final e as fases secundárias de id fixo.
     */
    private function semearMundoMinimo(Tenant $tenant): void
    {
        app(ContextoDoTenant::class)->usar($tenant->id, $this->mundo->mundoDoSmoke(...));
    }
}
