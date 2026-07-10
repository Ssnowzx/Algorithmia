<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Personagem;
use App\Models\ProgressoFase;
use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
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
        $this->get(route('ranking'))->assertOk()->assertSee($heroi->nome);
        $this->get(route('loja'))->assertOk();
        $this->get(route('inventario'))->assertOk()->assertSee('Espada');
        $this->get(route('historia.ver', $fase))->assertOk();
        $this->get(route('batalha.iniciar', $fase))->assertOk();

        // O perfil monta cinco painéis derivados; todos precisam aparecer.
        $this->get(route('perfil'))->assertOk()
            ->assertSee('Maestria por matéria')
            ->assertSee('Domínio das regiões')
            ->assertSee('Missões da semana')
            ->assertSee('Primeiros passos')
            ->assertSee('Conquistas');
    }

    #[Test]
    public function o_visitante_ve_o_splash_e_o_jogador_vai_direto_ao_mapa(): void
    {
        // ARRANGE
        $this->mundo->mestre(['nome' => 'Willen Leolatto Carneiro', 'regiao' => 'Porto da Sintaxe']);

        // ACT + ASSERT: vitrine para quem não tem conta.
        $this->get(route('home'))->assertOk()
            ->assertSee('O Reino de Algorithmia')
            ->assertSee('Willen Leolatto Carneiro')
            ->assertSee('Lorde Segfault');

        // Quem já joga não passa pela vitrine.
        $this->entrarComo('jogador');
        $this->get(route('home'))->assertRedirect(route('mapa'));
    }

    #[Test]
    public function quem_tem_conta_mas_nao_tem_heroi_e_levado_a_forja(): void
    {
        // ARRANGE
        $usuario = Usuario::create(['nome' => 'Nova', 'email' => 'nova2@algorithmia.test', 'senha_hash' => 'x']);
        $this->actingAs($usuario);

        // ACT + ASSERT
        $this->get(route('home'))->assertRedirect(route('personagem.criar'));
    }

    #[Test]
    public function a_arena_mostra_a_lore_do_bestiario_e_a_leitura_do_inimigo(): void
    {
        // ARRANGE: inimigo resistente E violento dispara a intel mais dura.
        $this->entrarComo('jogador');
        $fase = $this->mundo->fase([
            'inimigo_svg' => 'inimigo-slime', 'inimigo_hp' => 200, 'inimigo_ataque' => 20,
        ]);
        $this->mundo->desafio($fase->id);

        // ACT + ASSERT
        $arena = $this->get(route('batalha.iniciar', $fase))->assertOk();
        $arena->assertSee('Resistente e violento');
        $arena->assertSee(config('bestiario.inimigo-slime.titulo'));
    }

    #[Test]
    public function um_inimigo_comum_nao_recebe_leitura_alguma(): void
    {
        // Um aviso em toda fase não avisa nada.

        // ARRANGE
        $this->entrarComo('jogador');
        $fase = $this->mundo->fase(['inimigo_hp' => 60, 'inimigo_ataque' => 10]);
        $this->mundo->desafio($fase->id);

        // ACT + ASSERT
        $this->get(route('batalha.iniciar', $fase))->assertOk()->assertDontSee('intel-inimigo');
    }

    #[Test]
    public function o_mapa_avisa_o_que_levar_apenas_contra_inimigos_ameacadores(): void
    {
        // ARRANGE
        $this->entrarComo('jogador');
        $this->mundo->fase(['ordem_global' => 1, 'nome' => 'Slime', 'inimigo_hp' => 60, 'inimigo_ataque' => 10]);
        $this->mundo->fase(['ordem_global' => 2, 'nome' => 'Colosso', 'inimigo_hp' => 300, 'inimigo_ataque' => 25]);
        $this->mundo->fase(['ordem_global' => 3, 'nome' => 'Bruto', 'inimigo_hp' => 60, 'inimigo_ataque' => 25]);

        // ACT + ASSERT
        $mapa = $this->get(route('mapa'))->assertOk();
        $mapa->assertSee('⚠ Leve ataque e defesa');  // resistente E violento
        $mapa->assertSee('🛡 Leve defesa');            // só brutal
        $this->assertSame(2, substr_count((string) $mapa->getContent(), 'no-tatica'), 'o Slime não recebe chip');
    }

    #[Test]
    public function uma_conquista_secreta_nao_revela_o_nome_antes_de_ser_obtida(): void
    {
        // Spoiler é o oposto de recompensa.

        // ARRANGE
        $this->entrarComo('jogador');
        \App\Models\Conquista::create([
            'codigo' => 'arquivista_do_vazio', 'nome' => 'Arquivista do Vazio',
            'descricao' => 'Recuperou todos os Logs do Zero.', 'secreta' => true,
        ]);

        // ACT + ASSERT
        $this->get(route('perfil'))->assertOk()
            ->assertSee('Conquista secreta')
            ->assertDontSee('Arquivista do Vazio')
            ->assertDontSee('Recuperou todos os Logs do Zero.');
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
