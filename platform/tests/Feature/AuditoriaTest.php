<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Middleware\ContextoDoPedido;
use App\Models\Fase;
use App\Models\Personagem;
use App\Models\RegistroDeAuditoria;
use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * Um mestre exclui uma fase — e, na cascata, todos os seus desafios — sem deixar
 * rastro de quem foi, quando, e o que sumiu. O conteúdo do jogo foi criado à mão pela
 * equipe, fora do git: uma exclusão errada não tem de onde voltar.
 */
final class AuditoriaTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mundo = new Mundo;
    }

    private function comoMestre(): Usuario
    {
        $heroi = $this->mundo->heroi();
        /** @var Personagem $heroi */
        $usuario = Usuario::query()->findOrFail($heroi->usuario_id);
        $usuario->update(['papel' => 'mestre']);
        $this->actingAs($usuario);

        return $usuario;
    }

    #[Test]
    public function excluir_uma_fase_guarda_o_que_se_perdeu_incluindo_a_cascata(): void
    {
        // ARRANGE
        $mestre = $this->comoMestre();
        $fase = $this->mundo->fase(['nome' => 'Porto da Sintaxe', 'tipo' => 'licao']);
        $this->mundo->desafio($fase->id);
        $this->mundo->desafio($fase->id);
        $this->mundo->desafio($fase->id);

        // ACT
        $this->post(route('mestre.fase.excluir', $fase))->assertRedirect();

        // ASSERT
        $registro = RegistroDeAuditoria::query()->where('acao', 'fase.excluir')->sole();

        $this->assertSame($mestre->id, $registro->autor_id);
        $this->assertSame($mestre->email, $registro->autor_email);
        $this->assertSame($fase->id, $registro->alvo_id);
        $this->assertSame('Porto da Sintaxe', $registro->resumo['nome']);

        // Contar depois do delete devolveria zero, e o registro diria que nada se perdeu.
        $this->assertSame(3, $registro->resumo['desafios_em_cascata']);
    }

    #[Test]
    public function o_registro_sobrevive_ao_alvo_e_ao_autor(): void
    {
        // ARRANGE
        $mestre = $this->comoMestre();
        $fase = $this->mundo->fase();
        $this->post(route('mestre.fase.excluir', $fase))->assertRedirect();

        // ACT: a conta de quem agiu some. Uma FK teria levado o registro junto.
        Personagem::query()->where('usuario_id', $mestre->id)->delete();
        $mestre->delete();

        // ASSERT
        $registro = RegistroDeAuditoria::query()->where('acao', 'fase.excluir')->sole();
        $this->assertSame($mestre->email, $registro->autor_email);
        $this->assertDatabaseMissing('fases', ['id' => $fase->id]);
    }

    #[Test]
    public function excluir_um_item_registra_quantos_inventarios_foram_afetados(): void
    {
        // ARRANGE
        $this->comoMestre();
        $item = $this->mundo->item('pocao', ['cura' => 10]);
        $this->mundo->darItem($this->mundo->heroi(), $item, 2);
        $this->mundo->darItem($this->mundo->heroi(), $item, 1);

        // ACT
        $this->post(route('mestre.item.excluir', $item))->assertRedirect();

        // ASSERT
        $registro = RegistroDeAuditoria::query()->where('acao', 'item.excluir')->sole();
        $this->assertSame(2, $registro->resumo['inventarios_afetados']);
    }

    #[Test]
    public function o_gabarito_nao_entra_no_registro_de_auditoria(): void
    {
        // ARRANGE
        $this->comoMestre();
        $fase = $this->mundo->fase();
        $desafio = $this->mundo->desafio($fase->id);

        // ACT
        $this->post(route('mestre.desafio.excluir', $desafio))->assertRedirect();

        // ASSERT: auditoria não é backup, e espalhar a resposta por mais uma tabela
        // amplia a superfície de vazamento sem responder a nenhuma pergunta.
        $registro = RegistroDeAuditoria::query()->where('acao', 'desafio.excluir')->sole();
        $this->assertArrayNotHasKey('resposta', $registro->resumo);
        $this->assertArrayHasKey('pergunta', $registro->resumo);
    }

    #[Test]
    public function criar_e_atualizar_tambem_deixam_rastro(): void
    {
        // ARRANGE
        $this->comoMestre();
        $fase = $this->mundo->fase(['nome' => 'Antes']);

        // ACT
        $this->post(route('mestre.fase.atualizar', $fase), [
            'ordem_global' => $fase->ordem_global,
            'nome' => 'Depois',
            'tipo' => $fase->tipo,
            'inimigo_hp' => $fase->inimigo_hp ?? 10,
            'inimigo_ataque' => $fase->inimigo_ataque ?? 1,
            'xp_recompensa' => $fase->xp_recompensa ?? 0,
            'ouro_recompensa' => $fase->ouro_recompensa ?? 0,
        ])->assertSessionHasNoErrors();

        // ASSERT: o registro guarda o estado ANTERIOR, que é o que ninguém mais tem.
        $registro = RegistroDeAuditoria::query()->where('acao', 'fase.atualizar')->sole();
        $this->assertSame('Antes', $registro->resumo['antes']['nome']);
    }

    #[Test]
    public function o_request_id_da_auditoria_e_o_mesmo_do_cabecalho(): void
    {
        // ARRANGE
        $this->comoMestre();
        $fase = $this->mundo->fase();

        // ACT
        $resposta = $this->post(route('mestre.fase.excluir', $fase));

        // ASSERT: é isto que liga a linha de log ao que aconteceu no banco.
        $doCabecalho = $resposta->headers->get(ContextoDoPedido::CABECALHO);
        $this->assertNotNull($doCabecalho);

        $registro = RegistroDeAuditoria::query()->where('acao', 'fase.excluir')->sole();
        $this->assertSame($doCabecalho, $registro->request_id);
    }

    #[Test]
    public function cada_requisicao_tem_o_seu_proprio_identificador(): void
    {
        // ARRANGE + ACT
        $primeiro = $this->get('/')->headers->get(ContextoDoPedido::CABECALHO);
        $segundo = $this->get('/')->headers->get(ContextoDoPedido::CABECALHO);

        // ASSERT
        $this->assertNotNull($primeiro);
        $this->assertNotSame($primeiro, $segundo);
    }

    #[Test]
    public function o_identificador_nunca_vem_do_cliente(): void
    {
        // ARRANGE: um cliente tentando forjar a trilha, ou colidir com a de outro.
        $forjado = '00000000-0000-4000-8000-000000000000';

        // ACT
        $resposta = $this->withHeader(ContextoDoPedido::CABECALHO, $forjado)->get('/');

        // ASSERT
        $this->assertNotSame($forjado, $resposta->headers->get(ContextoDoPedido::CABECALHO));
    }

    /** O painel é do mestre; um aluno não gera registro porque não passa do middleware. */
    #[Test]
    public function um_aluno_nao_consegue_excluir_nem_auditar(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi();
        $this->actingAs(Usuario::query()->findOrFail($heroi->usuario_id));
        $fase = $this->mundo->fase();

        // ACT
        $this->post(route('mestre.fase.excluir', $fase))->assertForbidden();

        // ASSERT
        $this->assertSame(0, RegistroDeAuditoria::query()->count());
        $this->assertDatabaseHas('fases', ['id' => $fase->id]);
    }
}
