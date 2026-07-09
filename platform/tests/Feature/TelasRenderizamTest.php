<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Personagem;
use App\Models\ProgressoFase;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * Toda tela renderiza sem estourar.
 *
 * Um erro de Blade — uma variável que não chega, um `->links()` sem paginador, um
 * `config()` com chave errada — só aparece quando a página é montada de verdade.
 * Testar o controller sem renderizar a view deixa esse buraco aberto.
 */
final class TelasRenderizamTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mundo = new Mundo;
    }

    #[Test]
    public function as_telas_publicas_e_de_visitante_abrem(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Entrar no reino');
        $this->get(route('registro'))->assertOk()->assertSee('Nova conta');
        $this->get(route('lore'))->assertOk();
        $this->get(route('healthz'))->assertOk();
    }

    #[Test]
    public function as_telas_do_jogador_abrem(): void
    {
        // ARRANGE
        $heroi = $this->entrarComo('jogador');
        $mestre = $this->mundo->mestre();
        $fase = $this->mundo->fase(['mestre_id' => $mestre->id, 'nome' => 'Porto da Sintaxe']);
        $this->mundo->desafio($fase->id);
        $this->mundo->darItem($heroi, $this->mundo->item('arma', ['ataque' => 3], ['nome' => 'Espada']), equipado: true);
        $this->mundo->darItem($heroi, $this->mundo->item('pocao', ['cura_hp' => 20], ['nome' => 'Poção']));
        ProgressoFase::registrar($heroi->id, $fase->id, 2, 3, 1, usouIa: false);

        // ACT + ASSERT
        $this->get(route('mapa'))->assertOk()->assertSee('Porto da Sintaxe');
        $this->get(route('perfil'))->assertOk()->assertSee('Desempenho por matéria');
        $this->get(route('ranking'))->assertOk()->assertSee($heroi->nome);
        $this->get(route('loja'))->assertOk();
        $this->get(route('inventario'))->assertOk()->assertSee('Espada');
        $this->get(route('historia.ver', $fase))->assertOk();
        $this->get(route('batalha.iniciar', $fase))->assertOk();
    }

    #[Test]
    public function a_criacao_de_personagem_lista_as_seis_classes(): void
    {
        // ARRANGE: usuário sem herói.
        $usuario = Usuario::create(['nome' => 'Nova', 'email' => 'nova@algorithmia.test', 'senha_hash' => 'x']);
        $this->actingAs($usuario);

        // ACT
        $tela = $this->get(route('personagem.criar'))->assertOk();

        // ASSERT
        foreach (config('jogo.classes') as $classe) {
            $tela->assertSee($classe['nome']);
        }
    }

    #[Test]
    public function as_telas_do_mestre_abrem_inclusive_a_paginacao_de_desafios(): void
    {
        // ARRANGE: mais desafios que o tamanho da página não seria realista aqui,
        // mas `->links()` já explode com a lista vazia se o paginador não vier.
        $this->entrarComo('mestre');
        $fase = $this->mundo->fase();
        $desafio = $this->mundo->desafio($fase->id);
        $item = $this->mundo->item('arma', ['ataque' => 2]);

        // ACT + ASSERT
        $this->get(route('mestre.painel'))->assertOk();
        $this->get(route('mestre.desafios'))->assertOk()->assertSee($fase->nome);
        $this->get(route('mestre.desafio.novo'))->assertOk();
        $this->get(route('mestre.desafio.editar', $desafio))->assertOk();
        $this->get(route('mestre.fases'))->assertOk();
        $this->get(route('mestre.fase.nova'))->assertOk();
        $this->get(route('mestre.fase.editar', $fase))->assertOk();
        $this->get(route('mestre.itens'))->assertOk();
        $this->get(route('mestre.item.novo'))->assertOk();
        $this->get(route('mestre.item.editar', $item))->assertOk();
    }

    #[Test]
    public function a_barra_de_navegacao_so_mostra_o_painel_a_um_mestre(): void
    {
        // ARRANGE + ACT + ASSERT
        $this->entrarComo('jogador');
        $this->get(route('mapa'))->assertOk()->assertDontSee('Painel do Mestre');

        $this->entrarComo('mestre');
        $this->get(route('mapa'))->assertOk()->assertSee('Painel do Mestre');
    }

    #[Test]
    public function a_tela_de_desafio_reconstroi_opcoes_e_gabarito_ao_editar(): void
    {
        // O formulário guarda texto; o banco guarda jsonb. A volta precisa casar.

        // ARRANGE
        $this->entrarComo('mestre');
        $fase = $this->mundo->fase();
        $ordenar = $this->mundo->desafio($fase->id, sobrescritas: [
            'tipo' => 'ordenar', 'opcoes' => ['ferver', 'servir', 'passar'], 'resposta' => [0, 2, 1],
        ]);

        // ACT + ASSERT
        $this->get(route('mestre.desafio.editar', $ordenar))->assertOk()
            ->assertSee("ferver\nservir\npassar", escape: false)
            ->assertSee('value="0, 2, 1"', escape: false);
    }

    private function entrarComo(string $papel): Personagem
    {
        $heroi = $this->mundo->heroi('mago');
        $usuario = Usuario::query()->findOrFail($heroi->usuario_id);
        $usuario->update(['papel' => $papel]);
        $this->actingAs($usuario);

        return $heroi;
    }
}
