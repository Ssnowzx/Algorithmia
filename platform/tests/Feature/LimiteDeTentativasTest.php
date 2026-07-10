<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * O `/entrar` aceitava quantas senhas por segundo o atacante conseguisse enviar.
 * O jogo tem contas de aluno e uma conta `mestre` com painel administrativo.
 *
 * São dois limites, e um só não bastaria: o de e-mail+IP não vê o atacante que
 * troca de e-mail a cada tentativa (password spraying); o de IP não vê o atacante
 * distribuído, mas encarece o caso comum.
 */
final class LimiteDeTentativasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // O cache de teste é `array`, e sobrevive entre testes do mesmo processo.
        // Sem limpar, o segundo teste herda os acertos do primeiro.
        RateLimiter::clear('entrar');
        $this->app->make('cache')->store()->flush();
    }

    private function conta(string $email = 'aluno@algorithmia.test'): Usuario
    {
        return Usuario::create([
            'nome' => 'Aluno',
            'email' => $email,
            'senha_hash' => Hash::make('senha-correta'),
        ]);
    }

    /** @return TestResponse<Response> */
    private function tentar(string $email, string $senha): TestResponse
    {
        return $this->post(route('login'), ['email' => $email, 'password' => $senha]);
    }

    #[Test]
    public function seis_tentativas_erradas_no_mesmo_email_batem_no_limite(): void
    {
        // ARRANGE
        $this->conta();

        // ACT: as cinco primeiras são recusadas por credencial…
        for ($i = 1; $i <= 5; $i++) {
            $this->tentar('aluno@algorithmia.test', 'errada')
                ->assertSessionHasErrors('email');
            $this->flushSession();
        }

        // ASSERT: a sexta nem chega ao Auth::attempt.
        $this->tentar('aluno@algorithmia.test', 'errada')->assertStatus(429);
    }

    #[Test]
    public function trocar_de_email_nao_escapa_do_limite_por_ip(): void
    {
        // ARRANGE: password spraying — uma senha, muitas contas. O limite por
        // e-mail+IP nunca dispararia, porque cada e-mail tem uma tentativa só.

        // ACT: 20 e-mails distintos consomem o limite por IP.
        for ($i = 1; $i <= 20; $i++) {
            $this->tentar("alvo{$i}@algorithmia.test", 'senha123');
            $this->flushSession();
        }

        // ASSERT
        $this->tentar('alvo21@algorithmia.test', 'senha123')->assertStatus(429);
    }

    #[Test]
    public function o_limite_nao_atrapalha_quem_sabe_a_senha(): void
    {
        // ARRANGE
        $this->conta();

        // ACT: quatro erros, abaixo do teto.
        for ($i = 1; $i <= 4; $i++) {
            $this->tentar('aluno@algorithmia.test', 'errada');
            $this->flushSession();
        }

        // ASSERT: a quinta, correta, entra.
        $this->tentar('aluno@algorithmia.test', 'senha-correta')
            ->assertRedirect(route('mapa'));
        $this->assertAuthenticated();
    }

    #[Test]
    public function o_registro_tambem_tem_teto(): void
    {
        // ARRANGE + ACT: cinco registros no mesmo IP.
        for ($i = 1; $i <= 5; $i++) {
            $this->post(route('registro'), [
                'nome' => "Novo {$i}",
                'email' => "novo{$i}@algorithmia.test",
                'password' => 'senha-forte-123',
                'password_confirmation' => 'senha-forte-123',
            ]);
            $this->flushSession();
        }

        // ASSERT
        $this->post(route('registro'), [
            'nome' => 'Novo 6',
            'email' => 'novo6@algorithmia.test',
            'password' => 'senha-forte-123',
            'password_confirmation' => 'senha-forte-123',
        ])->assertStatus(429);
    }
}
