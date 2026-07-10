<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ItemDoInventario;
use App\Models\Personagem;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * As regressões que a Fase 4 se propôs a fechar.
 *
 * O inventário do legado (`docs/migracao/INVENTARIO.md` §1) achou que
 * `historia/concluir` gravava progresso e XP por GET, e que os cinco endpoints
 * AJAX de batalha validavam o token CSRF mas aceitavam qualquer método. Estes
 * testes existem para que ninguém reintroduza isso sem perceber.
 */
final class SegurancaWebTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mundo = new Mundo;
    }

    #[TestWith(['batalha.responder'])]
    #[TestWith(['batalha.fragmento'])]
    #[TestWith(['batalha.especial'])]
    #[TestWith(['batalha.pocao'])]
    #[TestWith(['batalha.fugir'])]
    #[Test]
    public function nenhum_endpoint_de_turno_aceita_get(string $rota): void
    {
        // ARRANGE
        $this->entrarComHeroi();

        // ACT + ASSERT: um <img src="..."> não pode gastar um turno nem uma poção.
        $this->get(route($rota))->assertMethodNotAllowed();
    }

    #[Test]
    public function sair_nao_acontece_por_get(): void
    {
        // ARRANGE
        $this->entrarComHeroi();

        // ACT + ASSERT
        $this->get('/sair')->assertMethodNotAllowed();
        $this->assertAuthenticated();
    }

    #[Test]
    public function nenhuma_escrita_do_jogo_acontece_por_get(): void
    {
        // No legado, todas estas eram alcançáveis por `<img src>`: comprar gastava
        // ouro, vender esvaziava a mochila, descartar destruía o item para sempre,
        // e concluir gravava progresso e XP.

        // ARRANGE
        $heroi = $this->entrarComHeroi();
        $item = $this->mundo->item('arma', ['ataque' => 3], ['preco' => 10]);
        $this->mundo->darItem($heroi, $item);
        $fase = $this->mundo->fase(['tipo' => 'historia', 'xp_recompensa' => 99]);

        $escritas = [
            "/loja/{$item->id}/comprar",
            "/loja/{$item->id}/vender",
            "/inventario/{$item->id}/equipar",
            "/inventario/{$item->id}/desequipar",
            "/inventario/{$item->id}/usar",
            "/inventario/{$item->id}/descartar",
            "/historia/{$fase->id}/concluir",
            '/final',
        ];

        // ACT + ASSERT
        foreach ($escritas as $uri) {
            $resposta = $this->get($uri);

            // `/final` tem um GET legítimo (a tela de escolha) que não escreve nada;
            // as demais nem sequer respondem a GET.
            if ($uri !== '/final') {
                $this->assertSame(405, $resposta->status(), "{$uri} respondeu a GET");
            }
        }

        // Nada mudou de estado.
        $heroi->refresh();
        $this->assertSame(50, $heroi->ouro);
        $this->assertSame(0, $heroi->xp);
        $this->assertSame(1, ItemDoInventario::quantidade($heroi->id, $item->id));
        $this->assertSame(0, DB::table('progresso_fases')->count());
    }

    #[Test]
    public function nenhuma_escrita_do_painel_do_mestre_acontece_por_get(): void
    {
        // `mestre/excluirFase/5` apagava a fase e seus desafios em cascata, por GET.

        // ARRANGE
        $heroi = $this->mundo->heroi('mago');
        $usuario = Usuario::query()->findOrFail($heroi->usuario_id);
        $usuario->update(['papel' => 'mestre']);
        $this->actingAs($usuario);

        $fase = $this->mundo->fase();
        $desafio = $this->mundo->desafio($fase->id);
        $item = $this->mundo->item('arma', ['ataque' => 1]);

        // ACT + ASSERT
        foreach ([
            "/mestre/desafios/{$desafio->id}/excluir",
            "/mestre/fases/{$fase->id}/excluir",
            "/mestre/itens/{$item->id}/excluir",
        ] as $uri) {
            $this->assertSame(405, $this->get($uri)->status(), "{$uri} respondeu a GET");
        }

        $this->assertSame(1, DB::table('fases')->count());
        $this->assertSame(1, DB::table('desafios')->count());
        $this->assertSame(1, DB::table('itens')->count());
    }

    #[Test]
    public function um_post_sem_token_csrf_e_recusado(): void
    {
        // ARRANGE: o middleware de CSRF se auto-desliga quando o app está em
        // 'testing' (ValidateCsrfToken::runningUnitTests). Fingimos outro ambiente
        // para que ele realmente rode — senão o teste passaria sem provar nada.
        $this->entrarComHeroi();
        $this->app['env'] = 'local';

        // ACT + ASSERT
        $this->post(route('batalha.responder'), ['resposta' => 0])->assertStatus(419);
    }

    #[Test]
    public function visitante_nao_alcanca_o_jogo(): void
    {
        // ACT + ASSERT
        $this->get(route('mapa'))->assertRedirect(route('login'));
        $this->get(route('perfil'))->assertRedirect(route('login'));
        $this->postJson(route('batalha.responder'), ['resposta' => 0])->assertUnauthorized();
    }

    #[Test]
    public function o_gabarito_nunca_aparece_na_resposta_de_um_turno(): void
    {
        // ARRANGE
        $this->entrarComHeroi();
        $fase = $this->mundo->fase(['inimigo_hp' => 1000]);
        $this->mundo->desafio($fase->id);
        $this->mundo->desafio($fase->id, ordem: 1);
        $this->get(route('batalha.iniciar', $fase));

        // ACT
        $resposta = $this->postJson(route('batalha.responder'), ['resposta' => 0]);

        // ASSERT: o próximo desafio vem sem gabarito e sem explicação.
        $resposta->assertOk();
        $this->assertArrayNotHasKey('resposta', $resposta->json('estado.desafio'));
        $this->assertArrayNotHasKey('explicacao', $resposta->json('estado.desafio'));
        $this->assertArrayNotHasKey('desafios', $resposta->json('estado'));
    }

    #[Test]
    public function repetir_o_post_da_vitoria_nao_credita_a_recompensa_de_novo(): void
    {
        // O legado se protegia com um flag de sessão. Um replay do POST vencedor,
        // ou duas requisições concorrentes, creditavam de novo.

        // ARRANGE
        $this->mundo->conquistas('primeiro_passo', 'sem_falhas');
        $heroi = $this->entrarComHeroi();
        $fase = $this->mundo->fase(['inimigo_hp' => 17, 'xp_recompensa' => 50, 'ouro_recompensa' => 20]);
        $this->mundo->desafio($fase->id);
        $this->get(route('batalha.iniciar', $fase));

        // ACT: vence, e depois repete o mesmo POST.
        $this->postJson(route('batalha.responder'), ['resposta' => 0])
            ->assertJsonPath('resultado', 'vitoria')
            ->assertJsonPath('recompensa.xp', 50);

        $replay = $this->postJson(route('batalha.responder'), ['resposta' => 0]);

        // ASSERT: a batalha já acabou, e nada foi creditado de novo.
        $replay->assertJsonPath('erro', 'Nenhuma batalha ativa.');

        $heroi->refresh();
        $this->assertSame(50, $heroi->xp);
        $this->assertSame(70, $heroi->ouro, '50 iniciais + 20 da fase, uma vez só');
        $this->assertSame(1, DB::table('recompensas_batalha')->count());
    }

    #[Test]
    public function um_jogador_nao_alcanca_a_batalha_de_outro(): void
    {
        // A batalha vive na sessão de quem a iniciou. Trocar de usuário na mesma
        // sessão não pode herdar o estado alheio.

        // ARRANGE
        $ana = $this->entrarComHeroi();
        $fase = $this->mundo->fase(['inimigo_hp' => 1000]);
        $this->mundo->desafio($fase->id);
        $this->get(route('batalha.iniciar', $fase));

        // ACT: outro jogador, sessão limpa.
        $bia = $this->mundo->heroi('elfo');
        $this->app['auth']->logout();
        $this->flushSession();
        $this->actingAs(Usuario::query()->findOrFail($bia->usuario_id));

        $resposta = $this->postJson(route('batalha.responder'), ['resposta' => 0]);

        // ASSERT
        $resposta->assertJsonPath('erro', 'Nenhuma batalha ativa.');
        $this->assertSame(0, DB::table('respostas_log')->where('personagem_id', $ana->id)->count());
    }

    #[Test]
    public function a_classe_do_heroi_nao_pode_ser_inventada_pelo_formulario(): void
    {
        // ARRANGE: usuário sem herói.
        $usuario = Usuario::create([
            'nome' => 'Ana', 'email' => 'ana@algorithmia.test', 'senha_hash' => 'x',
        ]);
        $this->actingAs($usuario);

        // ACT + ASSERT
        $this->post(route('personagem.salvar'), ['nome' => 'Trapaça', 'classe' => 'semideus'])
            ->assertSessionHasErrors('classe');

        $this->assertSame(0, Personagem::query()->count());
    }

    #[Test]
    public function um_usuario_nao_cria_dois_herois(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi();

        // ACT
        $this->post(route('personagem.salvar'), ['nome' => 'Clone', 'classe' => 'elfo'])
            ->assertRedirect(route('mapa'));

        // ASSERT
        $this->assertSame(1, Personagem::query()->where('usuario_id', $heroi->usuario_id)->count());
    }

    #[Test]
    public function o_email_e_unico_ignorando_maiusculas_no_registro(): void
    {
        // ARRANGE: a collation do MySQL dava isto de graça; no PostgreSQL, não.
        Usuario::create(['nome' => 'Ana', 'email' => 'ana@algorithmia.test', 'senha_hash' => 'x']);

        // ACT + ASSERT: sem a checagem, o índice sobre lower(email) daria um 500.
        $this->post(route('registro'), [
            'nome' => 'Outra', 'email' => 'ANA@Algorithmia.TEST',
            'password' => 'segredo123', 'password_confirmation' => 'segredo123',
        ])->assertSessionHasErrors('email');

        $this->assertSame(1, Usuario::query()->count());
    }

    private function entrarComHeroi(string $classe = 'mago'): Personagem
    {
        $heroi = $this->mundo->heroi($classe);
        $this->actingAs(Usuario::query()->findOrFail($heroi->usuario_id));

        return $heroi;
    }
}
