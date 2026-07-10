<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dominio\Tenancy\Flags;
use App\Models\Personagem;
use App\Models\Tenant;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * Etapa E.1: liberação progressiva por instituição.
 *
 * O que estes testes protegem, e que uma leitura do código não garante:
 *
 * 1. **A rota é gateada, e não só o menu.** Esconder o link e deixar a URL aberta é a
 *    versão de apresentação do erro que a Etapa A encontrou no banco — uma barreira que dá
 *    a sensação de existir. Aqui se bate na URL direto.
 * 2. **Uma chave desconhecida explode.** Se `ativa('turmsa')` devolvesse `false`, um erro
 *    de digitação desligaria a funcionalidade em produção, para sempre, e em silêncio.
 * 3. **A flag de uma escola não vaza para a outra.** É o ponto todo da Etapa E.
 */
final class FlagsTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mundo = new Mundo;
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

    private function flags(): Flags
    {
        return app(Flags::class);
    }

    /** As rotas gateadas ficam atrás do middleware `personagem`: sem herói, redirecionam. */
    private function entrarComo(string $papel): Personagem
    {
        $heroi = $this->mundo->heroi('mago');
        $usuario = Usuario::query()->findOrFail($heroi->usuario_id);
        $usuario->update(['papel' => $papel]);
        $this->actingAs($usuario);

        return $heroi;
    }

    /** @param  array<string,bool>  $flags */
    private function criarRival(string $slug, string $host, array $flags): int
    {
        $dono = DB::connection('pgsql_dono');

        $id = (int) $dono->table('tenants')->insertGetId([
            'nome' => 'Rival', 'slug' => $slug, 'ativo' => true,
            'flags' => json_encode($flags),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $dono->table('tenant_dominios')->insert([
            'tenant_id' => $id, 'host' => $host, 'primario' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return $id;
    }

    // ------------------------------------------------------------------ o catálogo

    #[Test]
    public function uma_chave_desconhecida_levanta_excecao_em_vez_de_devolver_falso(): void
    {
        // ARRANGE + ACT + ASSERT: um `false` calado desligaria a funcionalidade para sempre.
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/turmsa/');

        $this->flags()->ativa('turmsa');
    }

    #[Test]
    public function sem_sobrescrita_vale_o_padrao_declarado_no_codigo(): void
    {
        // ARRANGE: o tenant padrão nasce com `flags = {}`.
        // ACT + ASSERT
        $this->assertFalse($this->flags()->ativa('turmas'));
        $this->assertTrue($this->flags()->ativa('ranking'));
    }

    #[Test]
    public function limpar_a_sobrescrita_devolve_a_instituicao_ao_padrao(): void
    {
        // ARRANGE
        $tenant = Tenant::query()->where('slug', 'padrao')->firstOrFail();
        $this->flags()->definirNoTenant($tenant, 'turmas', true);
        $this->assertTrue($this->flags()->ativa('turmas'));

        // ACT: `null` REMOVE a chave — não grava o valor do padrão.
        $this->flags()->definirNoTenant($tenant, 'turmas', null);

        // ASSERT: a coluna voltou a `{}`, e a instituição volta a acompanhar o código.
        $this->assertFalse($this->flags()->ativa('turmas'));
        $this->assertSame([], $tenant->refresh()->flags);
    }

    #[Test]
    public function todas_relata_padrao_e_sobrescrita_separadamente(): void
    {
        // ARRANGE: gravar `false` sobre um padrão `false` NÃO é o mesmo que não opinar —
        // no dia em que o padrão virar `true`, uma escola acompanha e a outra não.
        $tenant = Tenant::query()->where('slug', 'padrao')->firstOrFail();
        $this->flags()->definirNoTenant($tenant, 'turmas', false);

        // ACT
        $estado = $this->flags()->todas($tenant);

        // ASSERT
        $this->assertFalse($estado['turmas']['ativa']);
        $this->assertTrue($estado['turmas']['sobrescrita'], 'a escola opinou, e isso tem de aparecer');
        $this->assertFalse($estado['ranking']['sobrescrita']);
        $this->assertTrue($estado['ranking']['ativa']);
    }

    // ------------------------------------------------------------------ a rota

    #[Test]
    public function a_rota_de_turmas_devolve_404_com_a_flag_desligada(): void
    {
        // ARRANGE: sem `ligarFlag`. 404 e não 403: a página não existe para esta escola.
        $this->entrarComo('mestre');

        // ACT + ASSERT
        $this->get(route('turmas.index'))->assertNotFound();
    }

    #[Test]
    public function a_rota_de_turmas_responde_com_a_flag_ligada(): void
    {
        // ARRANGE
        $this->ligarFlag('turmas');
        $this->entrarComo('mestre');

        // ACT + ASSERT
        $this->get(route('turmas.index'))->assertOk();
    }

    #[Test]
    public function desligar_o_ranking_fecha_a_rota_que_hoje_esta_aberta(): void
    {
        // ARRANGE
        $this->entrarComo('jogador');
        $this->get(route('ranking'))->assertOk();

        // ACT
        $this->ligarFlag('ranking', false);

        // ASSERT
        $this->get(route('ranking'))->assertNotFound();
    }

    // ------------------------------------------------------------------ o menu

    #[Test]
    public function o_menu_segue_a_flag(): void
    {
        // ARRANGE
        $this->entrarComo('mestre');

        // ACT + ASSERT: desligada, o link não está lá.
        $this->get(route('mapa'))->assertOk()->assertDontSee('>Turmas<', false);

        // ACT + ASSERT: ligada, está.
        $this->ligarFlag('turmas');

        $this->get(route('mapa'))->assertOk()->assertSee('>Turmas<', false);
    }

    #[Test]
    public function um_aluno_nao_ve_turmas_no_menu_nem_com_a_flag_ligada(): void
    {
        // ARRANGE: a flag é da instituição; o papel é do usuário. As duas fronteiras valem.
        $this->ligarFlag('turmas');
        $this->entrarComo('jogador');

        // ACT + ASSERT
        $this->get(route('mapa'))->assertOk()->assertDontSee('>Turmas<', false);
    }

    // ------------------------------------------------------------------ o isolamento

    #[Test]
    public function a_flag_de_uma_instituicao_nao_vaza_para_a_outra(): void
    {
        // ARRANGE: a rival liga `turmas`; a padrão não.
        $rival = $this->criarRival('rival', 'rival.algorithmia.test', ['turmas' => true]);

        // ACT + ASSERT
        $this->assertTrue($this->flags()->todas(Tenant::query()->find($rival))['turmas']['ativa']);
        $this->assertFalse($this->flags()->ativa('turmas'), 'a flag da rival vazou para a padrão');
    }

    /**
     * O `Flags` é singleton, e o php-fpm reaproveita o processo entre requisições. Se a
     * instância guardasse o tenant do pedido anterior, a segunda escola veria as flags da
     * primeira — o mesmo modo de falha do `SET LOCAL` que a Etapa B resolveu.
     *
     * O herói nasce ANTES da requisição à rival: sob `RefreshDatabase` o `SET LOCAL` do
     * `ResolverTenant` acontece num SAVEPOINT, e **sobrevive** ao `RELEASE` dele. Um
     * `Usuario::create()` depois da visita à rival nasceria dentro da rival.
     */
    #[Test]
    public function duas_requisicoes_seguidas_leem_as_flags_de_cada_host(): void
    {
        // ARRANGE
        $this->criarRival('rival-2', 'rival2.algorithmia.test', ['ranking' => false]);
        $this->entrarComo('jogador');

        // ACT: a lore não exige conta e não redireciona quem já entrou — mas passa pelo
        // `ResolverTenant`, que é o que importa aqui: ele entrega a rival ao `Flags`.
        $this->get('http://rival2.algorithmia.test/historia')->assertOk();

        // ASSERT: a requisição seguinte, no host padrão, não herda `ranking = false`.
        //
        // A URL é escrita à mão, e não com `route()`. O gerador de URLs do Laravel toma a
        // raiz da ÚLTIMA requisição do teste: depois da visita acima, `route('ranking')`
        // devolveria `http://rival2.algorithmia.test/ranking`, e este teste estaria
        // medindo o isolamento de heróis em vez do de flags. Ele passaria — pelo motivo
        // errado, e por um 302 do `ExigirPersonagem`.
        $this->get('http://localhost/ranking')->assertOk();
    }
}
