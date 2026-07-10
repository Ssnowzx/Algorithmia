<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dominio\Combate\CorretorDeRespostas;
use App\Models\Desafio;
use App\Models\Fase;
use App\Models\Item;
use App\Models\Personagem;
use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * O Painel do Mestre.
 *
 * A validação por tipo de desafio não é burocracia: um "ordenar" sem opções deixa
 * o jogador sem nada para mover, e um índice de resposta fora da faixa faz o
 * corretor recusar a resposta certa para sempre. O legado salvava as duas coisas.
 */
final class PainelDoMestreTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mundo = new Mundo;
    }

    #[TestWith(['mestre.painel'])]
    #[TestWith(['mestre.desafios'])]
    #[TestWith(['mestre.fases'])]
    #[TestWith(['mestre.itens'])]
    #[Test]
    public function um_jogador_comum_recebe_403_no_painel(string $rota): void
    {
        // ARRANGE: 403, e não um redirect silencioso — esconder a URL não protege nada.
        $this->entrarComo('jogador');

        // ACT + ASSERT
        $this->get(route($rota))->assertForbidden();
    }

    #[Test]
    public function um_visitante_e_mandado_ao_login(): void
    {
        $this->get(route('mestre.painel'))->assertRedirect(route('login'));
    }

    #[Test]
    public function o_mestre_ve_os_totais_do_reino(): void
    {
        // ARRANGE
        $this->entrarComo('mestre');
        $fase = $this->mundo->fase();
        $this->mundo->desafio($fase->id);

        // ACT + ASSERT
        $this->get(route('mestre.painel'))->assertOk()->assertSee('Painel do Mestre');
    }

    #[Test]
    public function criar_um_desafio_de_multipla_escolha_valido(): void
    {
        // ARRANGE
        $this->entrarComo('mestre');
        $fase = $this->mundo->fase();

        // ACT
        $this->post(route('mestre.desafio.criar'), $this->formulario($fase, [
            'tipo' => 'multipla',
            'opcoes' => "certa\nerrada\ntambém errada",
            'resposta' => '0',
        ]))->assertRedirect(route('mestre.desafios'));

        // ASSERT: o gabarito grava como inteiro, que é o que o corretor espera.
        $desafio = Desafio::query()->firstOrFail();
        $this->assertSame(0, $desafio->resposta);
        $this->assertSame(['certa', 'errada', 'também errada'], $desafio->opcoes);
        $this->assertTrue((new CorretorDeRespostas)->acertou($desafio->toArray(), 0));
    }

    #[Test]
    public function o_gabarito_fora_da_faixa_das_opcoes_e_recusado(): void
    {
        // Salvar isto tornaria a pergunta impossível: nenhuma opção casaria.

        // ARRANGE
        $this->entrarComo('mestre');
        $fase = $this->mundo->fase();

        // ACT + ASSERT
        $this->post(route('mestre.desafio.criar'), $this->formulario($fase, [
            'tipo' => 'multipla', 'opcoes' => "a\nb", 'resposta' => '5',
        ]))->assertSessionHasErrors('resposta');

        $this->assertSame(0, Desafio::query()->count());
    }

    #[Test]
    public function multipla_escolha_com_uma_opcao_so_e_recusada(): void
    {
        // ARRANGE
        $this->entrarComo('mestre');
        $fase = $this->mundo->fase();

        // ACT + ASSERT
        $this->post(route('mestre.desafio.criar'), $this->formulario($fase, [
            'tipo' => 'multipla', 'opcoes' => 'única', 'resposta' => '0',
        ]))->assertSessionHasErrors('resposta');
    }

    #[Test]
    public function ordenar_exige_uma_permutacao_completa_dos_indices(): void
    {
        // ARRANGE
        $this->entrarComo('mestre');
        $fase = $this->mundo->fase();

        // ACT + ASSERT: faltando o índice 2.
        $this->post(route('mestre.desafio.criar'), $this->formulario($fase, [
            'tipo' => 'ordenar', 'opcoes' => "a\nb\nc", 'resposta' => '1, 0',
        ]))->assertSessionHasErrors('resposta');

        // E agora a permutação certa.
        $this->post(route('mestre.desafio.criar'), $this->formulario($fase, [
            'tipo' => 'ordenar', 'opcoes' => "a\nb\nc", 'resposta' => '2, 0, 1',
        ]))->assertRedirect(route('mestre.desafios'));

        $this->assertSame([2, 0, 1], Desafio::query()->firstOrFail()->resposta);
    }

    #[Test]
    public function verdadeiro_ou_falso_grava_um_booleano(): void
    {
        // ARRANGE
        $this->entrarComo('mestre');
        $fase = $this->mundo->fase();

        // ACT
        $this->post(route('mestre.desafio.criar'), $this->formulario($fase, [
            'tipo' => 'vf', 'opcoes' => '', 'resposta' => 'verdadeiro',
        ]))->assertRedirect(route('mestre.desafios'));

        // ASSERT: o corretor compara booleanos, não a string.
        $desafio = Desafio::query()->firstOrFail();
        $this->assertTrue($desafio->resposta);
        $this->assertTrue((new CorretorDeRespostas)->acertou($desafio->toArray(), 'true'));
    }

    #[Test]
    public function completar_aceita_varias_alternativas(): void
    {
        // ARRANGE
        $this->entrarComo('mestre');
        $fase = $this->mundo->fase();

        // ACT
        $this->post(route('mestre.desafio.criar'), $this->formulario($fase, [
            'tipo' => 'completar', 'opcoes' => '', 'resposta' => 'echo, print',
        ]))->assertRedirect(route('mestre.desafios'));

        // ASSERT
        $desafio = Desafio::query()->firstOrFail();
        $this->assertSame(['echo', 'print'], $desafio->resposta);
        $this->assertTrue((new CorretorDeRespostas)->acertou($desafio->toArray(), 'print'));
    }

    #[Test]
    public function completar_sem_resposta_e_recusado(): void
    {
        // ARRANGE
        $this->entrarComo('mestre');
        $fase = $this->mundo->fase();

        // ACT + ASSERT
        $this->post(route('mestre.desafio.criar'), $this->formulario($fase, [
            'tipo' => 'completar', 'opcoes' => '', 'resposta' => '',
        ]))->assertSessionHasErrors('resposta');
    }

    #[Test]
    public function relacionar_nao_pode_ser_criado_por_este_formulario(): void
    {
        // O formato {itens, alvos} não sai de um textarea; criar por aqui geraria
        // um desafio insolúvel.

        // ARRANGE
        $this->entrarComo('mestre');
        $fase = $this->mundo->fase();

        // ACT + ASSERT
        $this->post(route('mestre.desafio.criar'), $this->formulario($fase, [
            'tipo' => 'arrastar', 'opcoes' => "a\nb", 'resposta' => '0, 1',
        ]))->assertSessionHasErrors('resposta');
    }

    #[Test]
    public function excluir_um_desafio_exige_post(): void
    {
        // ARRANGE
        $this->entrarComo('mestre');
        $fase = $this->mundo->fase();
        $desafio = $this->mundo->desafio($fase->id);

        // ACT + ASSERT
        $this->get("/mestre/desafios/{$desafio->id}/excluir")->assertMethodNotAllowed();
        $this->assertSame(1, Desafio::query()->count());

        $this->post(route('mestre.desafio.excluir', $desafio))->assertRedirect(route('mestre.desafios'));
        $this->assertSame(0, Desafio::query()->count());
    }

    #[Test]
    public function o_formulario_de_novo_desafio_nao_e_confundido_com_um_id(): void
    {
        // Sem `whereNumber`, `/mestre/desafios/novo` procuraria o desafio "novo".

        // ARRANGE
        $this->entrarComo('mestre');
        $this->mundo->fase();

        // ACT + ASSERT
        $this->get(route('mestre.desafio.novo'))->assertOk()->assertSee('Novo desafio');
    }

    #[Test]
    public function uma_fase_nao_pode_exigir_a_si_mesma(): void
    {
        // Ela nunca destravaria: o requisito jamais estaria concluído.

        // ARRANGE
        $this->entrarComo('mestre');
        $fase = $this->mundo->fase();

        // ACT + ASSERT
        $this->post(route('mestre.fase.atualizar', $fase), [
            'nome' => 'Laço', 'ordem_global' => 1, 'tipo' => 'licao',
            'inimigo_hp' => 60, 'inimigo_ataque' => 10,
            'xp_recompensa' => 50, 'ouro_recompensa' => 20,
            'requisito_fase_id' => $fase->id,
        ])->assertSessionHasErrors('requisito_fase_id');
    }

    #[Test]
    public function excluir_uma_fase_leva_seus_desafios_junto(): void
    {
        // ARRANGE
        $this->entrarComo('mestre');
        $fase = $this->mundo->fase();
        $this->mundo->desafio($fase->id);
        $this->mundo->desafio($fase->id, ordem: 1);

        // ACT
        $this->post(route('mestre.fase.excluir', $fase))->assertRedirect(route('mestre.fases'));

        // ASSERT: cascade no banco, não no PHP.
        $this->assertSame(0, Fase::query()->count());
        $this->assertSame(0, Desafio::query()->count());
    }

    #[Test]
    public function criar_um_item_monta_o_efeito_e_omite_os_zeros(): void
    {
        // ARRANGE
        $this->entrarComo('mestre');

        // ACT
        $this->post(route('mestre.item.criar'), [
            'nome' => 'Lâmina do Kernel', 'tipo' => 'arma', 'preco' => 200,
            'svg_slug' => 'item-lamina', 'raridade' => 'epico', 'compravel' => '1',
            'ef_ataque' => 9, 'ef_defesa' => 0, 'ef_cura_hp' => 0, 'ef_cura_mp' => 0,
        ])->assertRedirect(route('mestre.itens'));

        // ASSERT: efeito só com o que não é zero.
        $item = Item::query()->firstOrFail();
        $this->assertSame(['ataque' => 9], $item->efeito);
        $this->assertTrue($item->compravel);
    }

    #[Test]
    public function um_item_sem_efeito_algum_grava_null_e_nao_um_objeto_vazio(): void
    {
        // ARRANGE
        $this->entrarComo('mestre');

        // ACT
        $this->post(route('mestre.item.criar'), [
            'nome' => 'Pedra', 'tipo' => 'acessorio', 'preco' => 1,
            'svg_slug' => 'item-pedra', 'raridade' => 'comum',
        ]);

        // ASSERT
        $this->assertNull(Item::query()->firstOrFail()->efeito);
    }

    #[Test]
    public function a_caixa_desmarcada_torna_o_item_indisponivel_na_loja(): void
    {
        // Um checkbox desmarcado não é enviado: sem o `?? false`, o item ficaria
        // com o valor antigo ao ser editado.

        // ARRANGE
        $this->entrarComo('mestre');
        $item = $this->mundo->item('arma', ['ataque' => 3], ['compravel' => true]);

        // ACT
        $this->post(route('mestre.item.atualizar', $item), [
            'nome' => $item->nome, 'tipo' => 'arma', 'preco' => 10,
            'svg_slug' => $item->svg_slug, 'raridade' => 'comum',
            'ef_ataque' => 3,
        ])->assertRedirect(route('mestre.itens'));

        // ASSERT
        $this->assertFalse($item->refresh()->compravel);
    }

    /**
     * @param  array<string,mixed>  $sobrescritas
     * @return array<string,mixed>
     */
    private function formulario(Fase $fase, array $sobrescritas = []): array
    {
        return array_merge([
            'fase_id' => $fase->id,
            'ordem' => 0,
            'tipo' => 'multipla',
            'assunto' => 'php',
            'pergunta' => 'Qual das opções está certa?',
            'explicacao' => 'Porque sim.',
            'dificuldade' => 1,
            'opcoes' => "a\nb",
            'resposta' => '0',
        ], $sobrescritas);
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
