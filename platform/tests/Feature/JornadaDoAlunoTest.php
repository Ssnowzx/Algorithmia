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
 * O critério de aceite da Fase 4, exercitado pela HTTP de verdade: um aluno
 * entra, cria personagem, joga uma fase, recebe o feedback, conclui e consulta o
 * progresso.
 *
 * Os turnos passam pelos mesmos endpoints que o `public/js/batalha.js` chama.
 */
final class JornadaDoAlunoTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mundo = new Mundo;
    }

    #[Test]
    public function um_aluno_novo_registra_cria_heroi_vence_uma_fase_e_ve_o_progresso(): void
    {
        // ARRANGE: uma fase com dois desafios de múltipla escolha (gabarito = 0).
        $this->mundo->conquistas('primeiro_passo', 'sem_falhas');
        $fase = $this->mundo->fase(['nome' => 'Variáveis e Eco', 'inimigo_hp' => 30, 'xp_recompensa' => 50]);
        $this->mundo->desafio($fase->id, dificuldade: 1, ordem: 0);
        $this->mundo->desafio($fase->id, dificuldade: 1, ordem: 1);

        // ACT + ASSERT — registro
        $this->post(route('registro'), [
            'nome' => 'Aluna Nova',
            'email' => 'aluna@algorithmia.test',
            'password' => 'segredo123',
            'password_confirmation' => 'segredo123',
        ])->assertRedirect(route('personagem.criar'));

        $this->assertAuthenticated();

        // Sem herói, o mapa devolve para a criação — a guarda é middleware.
        $this->get(route('mapa'))->assertRedirect(route('personagem.criar'));

        // ACT + ASSERT — criação do herói
        $this->post(route('personagem.salvar'), ['nome' => 'Sonda', 'classe' => 'mago'])
            ->assertRedirect(route('mapa'));

        $heroi = Personagem::query()->firstOrFail();
        $this->assertSame('mago', $heroi->classe);
        $this->assertSame(80, $heroi->hp_max, 'atributos vêm de config/jogo.php, não do formulário');

        // ACT + ASSERT — o mapa mostra a fase
        $this->get(route('mapa'))->assertOk()->assertSee('Variáveis e Eco');

        // ACT + ASSERT — a arena abre e não entrega o gabarito
        $arena = $this->get(route('batalha.iniciar', $fase))->assertOk();
        $arena->assertSee('Bug Selvagem');
        $arena->assertDontSee('"resposta"', escape: false);
        $arena->assertDontSee('Porque sim.', escape: false); // a explicação também não

        // ACT — dois acertos: 17 + 21 = 38 de dano, contra 30 de HP
        $primeiro = $this->postJson(route('batalha.responder'), ['resposta' => 0]);
        $primeiro->assertOk()->assertJsonPath('correto', true)->assertJsonPath('dano_inimigo', 17);

        $segundo = $this->postJson(route('batalha.responder'), ['resposta' => 0]);

        // ASSERT — vitória, feedback e recompensa
        $segundo->assertOk()
            ->assertJsonPath('resultado', 'vitoria')
            ->assertJsonPath('recompensa.estrelas', 3)
            ->assertJsonPath('recompensa.xp', 50)
            ->assertJsonPath('resumo.erros', 0);

        // ASSERT — o progresso ficou gravado
        $this->assertSame(3, ProgressoFase::mapaDoPersonagem($heroi->id)[$fase->id]->estrelas);

        $this->get(route('perfil'))->assertOk()
            ->assertSee('Sonda')
            ->assertSee('Mago do Backend');
    }

    #[Test]
    public function errar_devolve_a_explicacao_pedagogica_e_custa_vida(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi();
        $fase = $this->mundo->fase(['inimigo_hp' => 1000, 'inimigo_ataque' => 10]);
        $this->mundo->desafio($fase->id);
        $this->mundo->desafio($fase->id, ordem: 1);
        $this->get(route('batalha.iniciar', $fase));

        // ACT: 1 é a opção errada.
        $resposta = $this->postJson(route('batalha.responder'), ['resposta' => 1]);

        // ASSERT: o erro ensina — e só ao errar a explicação aparece.
        $resposta->assertOk()
            ->assertJsonPath('correto', false)
            ->assertJsonPath('explicacao', 'Porque sim.')
            ->assertJsonPath('dano_heroi', 8)
            ->assertJsonPath('estado.heroi_hp', 72);
    }

    #[Test]
    public function uma_fase_trancada_devolve_ao_mapa(): void
    {
        // ARRANGE
        $this->entrarComHeroi();
        $anterior = $this->mundo->fase(['ordem_global' => 1]);
        $trancada = $this->mundo->fase(['ordem_global' => 2, 'requisito_fase_id' => $anterior->id]);
        $this->mundo->desafio($trancada->id);

        // ACT + ASSERT
        $this->get(route('batalha.iniciar', $trancada))
            ->assertRedirect(route('mapa'))
            ->assertSessionHas('erro');
    }

    #[Test]
    public function uma_fase_sem_desafios_nao_abre_a_arena(): void
    {
        // ARRANGE
        $this->entrarComHeroi();
        $vazia = $this->mundo->fase();

        // ACT + ASSERT
        $this->get(route('batalha.iniciar', $vazia))
            ->assertRedirect(route('mapa'))
            ->assertSessionHas('erro');
    }

    #[Test]
    public function o_fragmento_da_ia_cobra_reputacao_e_derruba_as_estrelas(): void
    {
        // ARRANGE
        $this->mundo->conquistas('primeiro_passo', 'tentacao');
        $heroi = $this->entrarComHeroi();
        $fragmento = $this->mundo->item('especial', [], ['svg_slug' => config('jogo.item_fragmento_ia')]);
        $this->mundo->darItem($heroi, $fragmento);

        $fase = $this->mundo->fase(['inimigo_hp' => 17]); // um acerto derruba
        $this->mundo->desafio($fase->id);
        $this->get(route('batalha.iniciar', $fase));

        // ACT
        $resposta = $this->postJson(route('batalha.fragmento'));

        // ASSERT: acerta sozinho, mas a conta chega.
        $resposta->assertOk()
            ->assertJsonPath('via_ia', true)
            ->assertJsonPath('resultado', 'vitoria')
            ->assertJsonPath('reputacao', -10)
            ->assertJsonPath('recompensa.estrelas', 1);
    }

    private function entrarComHeroi(string $classe = 'mago'): Personagem
    {
        $heroi = $this->mundo->heroi($classe);
        $this->actingAs(Usuario::query()->findOrFail($heroi->usuario_id));

        return $heroi;
    }
}
