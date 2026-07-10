<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dominio\Tenancy\ContextoDoTenant;
use App\Dominio\Tenancy\ProvisionamentoDeInstituicoes;
use App\Dominio\Tenancy\SemeadorDeConteudo;
use App\Models\Conquista;
use App\Models\Desafio;
use App\Models\Fase;
use App\Models\Mestre;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * Uma segunda instituição recebe o conteúdo do jogo — com ids novos.
 *
 * **O defeito que isto conserta.** `fases.id` é chave primária global, e o
 * `algorithmia:importar` preserva os ids do legado (o progresso que ele traz aponta para
 * eles). Importar duas vezes colide em `fases_pkey`. Verificado no banco:
 *
 * ```
 * SET LOCAL app.tenant_id = '2';
 * INSERT INTO fases (id, …) VALUES (8, …);
 * ERROR:  duplicate key value violates unique constraint "fases_pkey"
 * ```
 *
 * O RUNBOOK §10.6b mandava rodar exatamente esse comando. O ensaio do corte (C.6) criou uma
 * segunda instituição e provou o *isolamento*, mas nunca tentou *semeá-la*.
 *
 * Duas amarras tiveram de cair antes de a cópia ser possível, e as duas têm teste aqui:
 * `arquivista_do_vazio` deixou de referenciar as fases 8/14/20/32 por id, e
 * `conquistas.codigo` deixou de ser único global.
 */
final class SemeadorDeConteudoTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mundo = new Mundo;
        config(['app.debug' => false, 'session.driver' => 'database']);

        // A instituição padrão recebe o conteúdo, como se tivesse vindo do legado.
        $this->mundo->mundoDoSmoke();
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        $dono = DB::connection('pgsql_dono');
        $ids = $dono->table('tenants')->where('slug', 'like', 'nova%')->pluck('id');

        foreach (['dialogos', 'desafios', 'fases', 'itens', 'conquistas', 'mestres'] as $tabela) {
            $dono->table($tabela)->whereIn('tenant_id', $ids)->delete();
        }

        $dono->table('tenant_dominios')->whereIn('tenant_id', $ids)->delete();
        $dono->table('tenants')->whereIn('id', $ids)->delete();

        DB::beginTransaction();

        parent::tearDown();
    }

    private function novaEscola(string $slug = 'nova'): Tenant
    {
        return app(ProvisionamentoDeInstituicoes::class)
            ->provisionar('Escola Nova', $slug, "{$slug}.exemplo.com");
    }

    private function semeador(): SemeadorDeConteudo
    {
        return app(SemeadorDeConteudo::class);
    }

    private function padrao(): Tenant
    {
        return Tenant::query()->where('slug', 'padrao')->firstOrFail();
    }

    /**
     * @template T
     *
     * @param  callable():T  $trecho
     * @return T
     */
    private function dentroDe(Tenant $tenant, callable $trecho): mixed
    {
        return app(ContextoDoTenant::class)->usar($tenant->id, $trecho);
    }

    // ------------------------------------------------------------------ a cópia

    #[Test]
    public function o_conteudo_e_copiado_com_ids_novos(): void
    {
        // ARRANGE
        $nova = $this->novaEscola();
        $idsDaPadrao = $this->dentroDe($this->padrao(), fn (): array => Fase::query()->pluck('id')->all());

        // ACT
        $copiadas = $this->semeador()->semear($this->padrao(), $nova);

        // ASSERT
        $this->assertSame(count($idsDaPadrao), $copiadas['fases']);

        $idsDaNova = $this->dentroDe($nova, fn (): array => Fase::query()->pluck('id')->all());

        $this->assertCount(count($idsDaPadrao), $idsDaNova);
        $this->assertSame([], array_intersect($idsDaPadrao, $idsDaNova), 'as duas escolas compartilham uma linha de fase');
    }

    #[Test]
    public function as_pessoas_nao_sao_copiadas(): void
    {
        // ARRANGE: um aluno na padrão. Uma escola nova não herda os alunos de outra.
        $this->mundo->heroi('mago');
        $nova = $this->novaEscola();

        // ACT
        $this->semeador()->semear($this->padrao(), $nova);

        // ASSERT
        $this->dentroDe($nova, function (): void {
            $this->assertSame(0, DB::table('usuarios')->count());
            $this->assertSame(0, DB::table('personagens')->count());
            $this->assertSame(0, DB::table('progresso_fases')->count());
        });
    }

    /** Uma fase copiada tem de apontar para o mestre e o requisito DA SUA escola. */
    #[Test]
    public function as_chaves_estrangeiras_sao_reescritas_para_o_destino(): void
    {
        // ARRANGE: uma fase que exige outra, na padrão.
        $this->dentroDe($this->padrao(), function (): void {
            $primeira = Fase::query()->where('tipo', 'licao')->orderBy('ordem_global')->firstOrFail();
            Fase::query()->where('tipo', 'chefe_final')->update(['requisito_fase_id' => $primeira->id]);
        });

        $nova = $this->novaEscola();

        // ACT
        $this->semeador()->semear($this->padrao(), $nova);

        // ASSERT: sob o RLS da escola nova, todas as referências resolvem.
        $this->dentroDe($nova, function (): void {
            $chefe = Fase::query()->where('tipo', 'chefe_final')->firstOrFail();

            $this->assertNotNull($chefe->requisito_fase_id);
            $this->assertTrue(
                Fase::query()->whereKey($chefe->requisito_fase_id)->exists(),
                'o requisito aponta para uma fase que a escola nova não enxerga',
            );

            $this->assertTrue(Mestre::query()->whereKey($chefe->mestre_id)->exists());

            $orfaos = Desafio::query()->whereNotIn('fase_id', Fase::query()->select('id'))->count();
            $this->assertSame(0, $orfaos, 'um desafio ficou apontando para a fase da outra escola');
        });
    }

    #[Test]
    public function a_padrao_nao_e_tocada(): void
    {
        // ARRANGE
        $antes = $this->dentroDe($this->padrao(), fn (): array => [
            'fases' => Fase::query()->count(),
            'desafios' => Desafio::query()->count(),
        ]);

        $nova = $this->novaEscola();

        // ACT
        $this->semeador()->semear($this->padrao(), $nova);

        // ASSERT
        $depois = $this->dentroDe($this->padrao(), fn (): array => [
            'fases' => Fase::query()->count(),
            'desafios' => Desafio::query()->count(),
        ]);

        $this->assertSame($antes, $depois);
    }

    // ------------------------------------------- as duas amarras que tiveram de cair

    /**
     * `conquistas.codigo` era único GLOBAL. Com ele, `arquivista_do_vazio` só podia existir em
     * uma escola no banco inteiro — e a cópia estouraria no índice.
     */
    #[Test]
    public function duas_escolas_podem_ter_a_mesma_conquista(): void
    {
        // ARRANGE
        $nova = $this->novaEscola();

        // ACT
        $this->semeador()->semear($this->padrao(), $nova);

        // ASSERT
        $daPadrao = $this->dentroDe($this->padrao(), fn (): ?Conquista => Conquista::porCodigo('primeiro_passo'));
        $daNova = $this->dentroDe($nova, fn (): ?Conquista => Conquista::porCodigo('primeiro_passo'));

        $this->assertNotNull($daPadrao);
        $this->assertNotNull($daNova);
        $this->assertNotSame($daPadrao->id, $daNova->id, 'as duas escolas dividem a MESMA linha de conquista');
    }

    /**
     * As secundárias mudam de id na cópia. Se `arquivista_do_vazio` ainda as procurasse pelos
     * números 8, 14, 20 e 32, ela seria inalcançável na escola nova — em silêncio, e para
     * sempre. É o modo de falha que o `ConquistaService` do legado carregava.
     */
    #[Test]
    public function as_fases_secundarias_sobrevivem_a_copia_com_outros_ids(): void
    {
        // ARRANGE
        $nova = $this->novaEscola();

        // ACT
        $this->semeador()->semear($this->padrao(), $nova);

        // ASSERT
        /** @var list<int> $legado */
        $legado = config('jogo.fases_secundarias');

        $this->dentroDe($nova, function () use ($legado): void {
            $secundarias = Fase::query()->where('tipo', 'secundaria')->pluck('id')->all();

            $this->assertCount(count($legado), $secundarias);
            $this->assertSame([], array_intersect($legado, $secundarias), 'a escola nova ficou com os ids do legado');
        });
    }

    /** O portão da ativação aprova a escola semeada — é o teste de ponta a ponta do conserto. */
    #[Test]
    public function a_escola_semeada_passa_no_smoke_e_e_ativada(): void
    {
        // ARRANGE
        $nova = $this->novaEscola();
        $this->semeador()->semear($this->padrao(), $nova);

        // ACT
        app(ProvisionamentoDeInstituicoes::class)->ativar($nova);

        // ASSERT
        $this->assertTrue($nova->refresh()->ativo);
    }

    // ------------------------------------------------------------------ as recusas

    #[Test]
    public function semear_por_cima_de_uma_escola_que_ja_tem_conteudo_e_recusado(): void
    {
        // ARRANGE: duas cópias de tudo dariam um mapa com dois mundos sobrepostos.
        $nova = $this->novaEscola();
        $this->semeador()->semear($this->padrao(), $nova);

        // ACT + ASSERT
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/já tem conteúdo/');

        $this->semeador()->semear($this->padrao(), $nova);
    }

    #[Test]
    public function semear_de_uma_escola_vazia_e_recusado(): void
    {
        // ARRANGE
        $vazia = $this->novaEscola('nova-vazia');
        $destino = $this->novaEscola('nova-destino');

        // ACT + ASSERT
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/não tem conteúdo a copiar/');

        $this->semeador()->semear($vazia, $destino);
    }

    #[Test]
    public function semear_de_uma_escola_para_ela_mesma_e_recusado(): void
    {
        $this->expectException(RuntimeException::class);

        $this->semeador()->semear($this->padrao(), $this->padrao());
    }

    /** Uma cópia interrompida não pode deixar meia escola: a reconciliação desfaz tudo. */
    #[Test]
    public function uma_falha_no_meio_nao_deixa_conteudo_pela_metade(): void
    {
        // ARRANGE: a escola nova recebe UMA fase à mão. O semeador vai recusar, e o que ele
        // já tiver escrito tem de desaparecer junto.
        $nova = $this->novaEscola();

        $this->dentroDe($nova, fn (): Fase => $this->mundo->fase(['nome' => 'Intrusa']));

        // ACT
        try {
            $this->semeador()->semear($this->padrao(), $nova);
            $this->fail('o semeador aceitou escrever por cima');
        } catch (RuntimeException) {
            // esperado
        }

        // ASSERT: só a intrusa continua lá. Nenhum mestre, nenhum desafio copiado sobrou.
        $this->dentroDe($nova, function (): void {
            $this->assertSame(1, Fase::query()->count());
            $this->assertSame(0, Mestre::query()->count());
            $this->assertSame(0, Desafio::query()->count());
        });
    }

    // ------------------------------------------------------------------ o comando

    #[Test]
    public function o_comando_copia_e_ensina_o_proximo_passo(): void
    {
        // ARRANGE
        $this->novaEscola();

        // ACT + ASSERT
        $this->artisan('algorithmia:tenant:semear', ['para' => 'nova'])
            ->expectsOutputToContain('padrao')
            ->expectsOutputToContain('algorithmia:tenant:ativar nova')
            ->assertSuccessful();
    }

    #[Test]
    public function o_comando_recusa_um_slug_inexistente(): void
    {
        $this->artisan('algorithmia:tenant:semear', ['para' => 'nao-existe'])->assertFailed();
    }

    #[Test]
    public function o_comando_exige_de_quando_ha_duas_escolas_com_conteudo(): void
    {
        // ARRANGE: adivinhar a origem seria escolher em nome do operador.
        $primeira = $this->novaEscola('nova-a');
        $this->semeador()->semear($this->padrao(), $primeira);

        $this->novaEscola('nova-b');

        // ACT + ASSERT
        $this->artisan('algorithmia:tenant:semear', ['para' => 'nova-b'])
            ->expectsOutputToContain('Diga de qual copiar')
            ->assertFailed();

        $this->artisan('algorithmia:tenant:semear', ['para' => 'nova-b', '--de' => 'padrao'])
            ->assertSuccessful();
    }
}
