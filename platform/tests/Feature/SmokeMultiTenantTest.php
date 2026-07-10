<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * O smoke é o portão do deploy: `bin/deploy.sh` reverte sozinho quando ele reprova.
 *
 * Enquanto ele resolvia o "tenant único", **todo deploy quebraria no dia em que a segunda
 * escola entrasse** — o comando abortava dizendo que não sabia escolher. Um portão que só
 * abre para um cliente não é um portão.
 *
 * Descoberto no ensaio do corte (§10) com a tenancy ligada, criando uma segunda
 * instituição no banco de produção local.
 */
final class SmokeMultiTenantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // O smoke checa `APP_DEBUG` e o driver de sessão, e o ambiente de teste tem os
        // dois "errados". Aqui interessa a varredura por instituição, não essas duas.
        config(['app.debug' => false, 'session.driver' => 'database']);
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        $dono = DB::connection('pgsql_dono');
        $ids = $dono->table('tenants')->where('slug', 'like', 'rival%')->pluck('id');

        $dono->table('tenant_dominios')->whereIn('tenant_id', $ids)->delete();
        $dono->table('tenants')->whereIn('id', $ids)->delete();

        DB::beginTransaction();

        parent::tearDown();
    }

    /**
     * O domínio nasce junto, e não por capricho: desde a Etapa E.3 o smoke reprova uma
     * instituição ATIVA sem domínio primário — ela está no ar e ninguém a alcança. Este
     * teste semeava exatamente esse estado impossível.
     */
    private function criarTenant(string $slug, bool $ativo = true, bool $comDominio = true): int
    {
        $dono = DB::connection('pgsql_dono');

        $id = (int) $dono->table('tenants')->insertGetId([
            'nome' => $slug, 'slug' => $slug, 'ativo' => $ativo,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        if ($comDominio) {
            $dono->table('tenant_dominios')->insert([
                'tenant_id' => $id, 'host' => "{$slug}.algorithmia.test", 'primario' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        return $id;
    }

    #[Test]
    public function com_duas_instituicoes_o_smoke_verifica_as_duas(): void
    {
        // ARRANGE
        $this->criarTenant('rival');

        // ACT + ASSERT: estrutural, para não depender de conteúdo importado.
        $this->artisan('algorithmia:smoke --sem-conteudo')
            ->expectsOutputToContain('padrao')
            ->expectsOutputToContain('rival')
            ->assertSuccessful();
    }

    /** Ativa e inalcançável: o deploy não pode ser promovido em cima disso. */
    #[Test]
    public function uma_instituicao_ativa_sem_dominio_reprova_o_deploy(): void
    {
        // ARRANGE
        $this->criarTenant('rival-sem-host', comDominio: false);

        // ACT + ASSERT
        $this->artisan('algorithmia:smoke --sem-conteudo')
            ->expectsOutputToContain('sem domínio primário')
            ->assertFailed();
    }

    #[Test]
    public function uma_instituicao_inativa_nao_bloqueia_o_deploy(): void
    {
        // ARRANGE: uma escola desligada não é uma escola quebrada.
        $this->criarTenant('rival-desligada', ativo: false);

        // ACT + ASSERT
        $this->artisan('algorithmia:smoke --sem-conteudo')->assertSuccessful();
    }

    #[Test]
    public function a_opcao_tenant_restringe_a_verificacao(): void
    {
        // ARRANGE
        $this->criarTenant('rival');

        // ACT + ASSERT: com `--tenant`, o cabeçalho da outra não aparece.
        $this->artisan('algorithmia:smoke --sem-conteudo --tenant=padrao')
            ->doesntExpectOutputToContain('rival')
            ->assertSuccessful();
    }

    #[Test]
    public function um_slug_inexistente_falha_alto(): void
    {
        // ARRANGE + ACT + ASSERT: errar o slug não pode virar "verifiquei nada, tudo bem".
        $this->artisan('algorithmia:smoke --sem-conteudo --tenant=nao-existe')->assertFailed();
    }

    /**
     * Importar para a escola errada é irreversível sem restaurar dump. Com mais de uma
     * instituição, o comando exige que o operador diga qual.
     */
    /**
     * Provisionar e semear são dois atos. Entre eles a escola é injogável — e o smoke,
     * que é o portão do deploy, reprovaria todo build. Ela nasce desligada.
     */
    #[Test]
    public function uma_instituicao_nova_nasce_desligada(): void
    {
        // ARRANGE + ACT: sem passar `ativo`.
        DB::connection('pgsql_dono')->table('tenants')->insert([
            'nome' => 'Rival Nova', 'slug' => 'rival-nova',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // ASSERT
        $ativo = DB::connection('pgsql_dono')->table('tenants')->where('slug', 'rival-nova')->value('ativo');
        $this->assertFalse((bool) $ativo);

        // …e por isso não bloqueia o deploy.
        $this->artisan('algorithmia:smoke --sem-conteudo')->assertSuccessful();
    }

    #[Test]
    public function a_importacao_recusa_adivinhar_a_instituicao(): void
    {
        // ARRANGE
        $this->criarTenant('rival');

        // ACT + ASSERT
        $this->artisan('algorithmia:importar --dry-run')->assertFailed();
    }
}
