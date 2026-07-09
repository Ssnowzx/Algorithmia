<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Conquista;
use App\Models\Escolha;
use App\Models\Fase;
use App\Models\Personagem;
use App\Models\ProgressoFase;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * Os três desfechos.
 *
 * A escolha diante da IA Ancestral pesa mais que a reputação — mas não a apaga:
 * quem manda destruir o Fragmento sem nunca ter recusado sua ajuda recebe o final
 * de equilíbrio, não o de mestre. A disciplina de uma vida não se compra num clique.
 */
final class FinaisTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mundo = new Mundo;
    }

    #[Test]
    public function quem_nao_venceu_o_confronto_final_nao_alcanca_a_tela(): void
    {
        // ARRANGE
        $this->entrarComHeroi();
        $this->confrontoFinal();

        // ACT + ASSERT
        $this->get(route('historia.final'))->assertRedirect(route('mapa'));
        $this->post(route('historia.escolher'), ['escolha' => 'fundir'])->assertRedirect(route('mapa'));

        $this->assertSame(0, DB::table('escolhas')->count());
    }

    #[Test]
    public function vencido_o_confronto_a_escolha_aparece(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi();
        $this->venceuOConfronto($heroi);

        // ACT + ASSERT
        $this->get(route('historia.final'))->assertOk()
            ->assertSee('O Destino de Algorithmia')
            ->assertSee('Destruir a IA Ancestral')
            ->assertSee('Fundir-se à IA')
            ->assertSee('Reescrever a IA');
    }

    #[Test]
    public function uma_escolha_inventada_e_recusada(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi();
        $this->venceuOConfronto($heroi);

        // ACT + ASSERT
        $this->from(route('historia.final'))
            ->post(route('historia.escolher'), ['escolha' => 'virar-o-jogo'])
            ->assertSessionHasErrors('escolha');

        $this->assertNull(Escolha::valor($heroi->id, 'final'));
    }

    /** @return array<string,array{0:string,1:int,2:string,3:string}> [escolha, reputação, final, título] */
    public static function desfechos(): array
    {
        return [
            'fundir sempre leva à singularidade' => ['fundir', 100, 'singularidade', 'A Singularidade'],
            'destruir com disciplina dá o sexto mestre' => ['destruir', 40, 'mestre', 'O Sexto Mestre'],
            'destruir sem disciplina dá equilíbrio' => ['destruir', 39, 'equilibrio', 'O Copiloto'],
            'reescrever sempre dá equilíbrio' => ['reescrever', 100, 'equilibrio', 'O Copiloto'],
        ];
    }

    #[Test]
    #[DataProvider('desfechos')]
    public function a_escolha_e_a_reputacao_decidem_o_epilogo(string $escolha, int $reputacao, string $final, string $titulo): void
    {
        // ARRANGE
        $this->mundo->conquistas('final_mestre', 'final_singularidade', 'final_equilibrio');
        $heroi = $this->entrarComHeroi(['reputacao' => $reputacao]);
        $this->venceuOConfronto($heroi);

        // ACT
        $this->post(route('historia.escolher'), ['escolha' => $escolha])->assertOk()->assertSee($titulo);

        // ASSERT: a conquista secreta do desfecho é concedida.
        $codigo = config("jogo.conquistas_de_final.{$final}");
        $conquista = Conquista::porCodigo($codigo);
        $this->assertNotNull($conquista);
        $this->assertTrue(
            DB::table('conquistas_personagem')
                ->where('personagem_id', $heroi->id)->where('conquista_id', $conquista->id)->exists()
        );
    }

    #[Test]
    public function sem_escolha_explicita_a_reputacao_decide_sozinha(): void
    {
        // ARRANGE: reputação bem negativa, nenhuma escolha registrada.
        $this->mundo->conquistas('final_singularidade');
        $heroi = $this->entrarComHeroi(['reputacao' => -40]);
        $this->venceuOConfronto($heroi);

        // ACT: a escolha existe, mas com valor não previsto — cai no alinhamento.
        Escolha::definir($heroi->id, 'final', 'nenhuma');

        // ASSERT
        $this->get(route('historia.final'))->assertOk()->assertSee('A Singularidade');
    }

    #[Test]
    public function reabrir_o_epilogo_mostra_o_mesmo_final_e_nao_reconcede_a_conquista(): void
    {
        // ARRANGE
        $this->mundo->conquistas('final_equilibrio');
        $heroi = $this->entrarComHeroi(['reputacao' => 0]);
        $this->venceuOConfronto($heroi);

        // ACT
        $this->post(route('historia.escolher'), ['escolha' => 'reescrever'])->assertOk();
        $this->get(route('historia.final'))->assertOk()->assertSee('O Copiloto');
        $this->get(route('historia.final'))->assertOk();

        // ASSERT: chave primária composta — a conquista entra uma vez só.
        $this->assertSame(1, DB::table('conquistas_personagem')->where('personagem_id', $heroi->id)->count());
        $this->assertSame(1, DB::table('escolhas')->where('personagem_id', $heroi->id)->count());
    }

    #[Test]
    public function mudar_de_ideia_substitui_a_escolha_sem_duplicar(): void
    {
        // ARRANGE: a tabela `escolhas` não tem chave única — o delete é a guarda.
        $this->mundo->conquistas('final_equilibrio', 'final_singularidade');
        $heroi = $this->entrarComHeroi(['reputacao' => 0]);
        $this->venceuOConfronto($heroi);

        // ACT
        $this->post(route('historia.escolher'), ['escolha' => 'reescrever']);
        $this->post(route('historia.escolher'), ['escolha' => 'fundir'])->assertSee('A Singularidade');

        // ASSERT
        $this->assertSame(1, DB::table('escolhas')->where('personagem_id', $heroi->id)->count());
        $this->assertSame('fundir', Escolha::valor($heroi->id, 'final'));
    }

    #[Test]
    public function a_escolha_do_final_nao_acontece_por_get(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi();
        $this->venceuOConfronto($heroi);

        // ACT + ASSERT: GET /final mostra a tela; quem decide é o POST.
        $this->get(route('historia.final'))->assertOk();
        $this->assertNull(Escolha::valor($heroi->id, 'final'));
    }

    #[Test]
    public function a_lore_e_publica(): void
    {
        // ARRANGE + ACT + ASSERT: é a vitrine da história, não exige conta.
        $this->get(route('lore'))->assertOk()->assertSee('Algorithmia', escape: false);
    }

    #[Test]
    public function concluir_a_cena_anterior_ao_confronto_emenda_direto_nele(): void
    {
        // O legado comparava `ordem_global === 34`. Aqui a próxima fase se declara.

        // ARRANGE
        $this->entrarComHeroi();
        $vespera = $this->mundo->fase(['tipo' => 'historia', 'ordem_global' => 34, 'nome' => 'O Abismo']);
        $confronto = $this->mundo->fase([
            'tipo' => 'chefe_final', 'ordem_global' => 35,
            'requisito_fase_id' => $vespera->id, 'nome' => 'Lorde Segfault',
        ]);

        // ACT + ASSERT
        $this->post(route('historia.concluir', $vespera))
            ->assertRedirect(route('historia.ver', $confronto));
    }

    private function confrontoFinal(): Fase
    {
        return $this->mundo->fase(['tipo' => 'chefe_final', 'ordem_global' => 35, 'nome' => 'Lorde Segfault']);
    }

    private function venceuOConfronto(Personagem $heroi): Fase
    {
        $confronto = $this->confrontoFinal();
        ProgressoFase::registrar($heroi->id, $confronto->id, 3, 6, 0, usouIa: false);

        return $confronto;
    }

    /** @param  array<string,mixed>  $sobrescritas */
    private function entrarComHeroi(array $sobrescritas = []): Personagem
    {
        $heroi = $this->mundo->heroi('mago', $sobrescritas);
        $this->actingAs(Usuario::query()->findOrFail($heroi->usuario_id));

        return $heroi;
    }
}
