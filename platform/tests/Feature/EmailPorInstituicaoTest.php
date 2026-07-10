<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dominio\Tenancy\ContextoDoTenant;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * O e-mail é único **dentro** da instituição, e não no banco inteiro.
 *
 * **O defeito que isto conserta — e ele era explorável de fora.** O índice
 * `usuarios_email_unique` era global, sobre `lower(email)`. Medido no banco, como
 * `algorithmia_app`, no contexto da escola B:
 *
 * ```
 * SELECT count(*) FROM usuarios WHERE lower(email) = 'ana@escola-a.test';   -->  0
 * INSERT INTO usuarios (nome, email, senha_hash) VALUES ('Outra', 'ana@escola-a.test', 'y');
 * ERROR:  duplicate key value violates unique constraint "usuarios_email_unique"
 * ```
 *
 * O `AutenticacaoController::registrar` faz exatamente essas duas coisas, nessa ordem. Sob RLS
 * a checagem devolvia "e-mail livre"; o `INSERT` estourava. O visitante recebia **500 em vez de
 * um erro de validação**, e aprendia que aquele e-mail existe **em outra instituição**.
 * Enumeração de contas entre escolas, numa rota pública, de graça.
 *
 * O índice virou `(tenant_id, lower(email))`. Isto **não** é a conta global do roteiro v1 §5 —
 * aquela exige resolver a identidade antes de saber o tenant, e continua sem demanda. Aqui só
 * se conserta a contradição entre a barreira e o índice.
 */
final class EmailPorInstituicaoTest extends TestCase
{
    use RefreshDatabase;

    private const EMAIL = 'ana@escola-a.test';

    private int $escolaB;

    protected function setUp(): void
    {
        parent::setUp();

        $dono = DB::connection('pgsql_dono');

        $this->escolaB = (int) $dono->table('tenants')->insertGetId([
            'nome' => 'Escola B', 'slug' => 'escola-b', 'ativo' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $dono->table('tenant_dominios')->insert([
            'tenant_id' => $this->escolaB, 'host' => 'b.algorithmia.test', 'primario' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        $dono = DB::connection('pgsql_dono');
        $ids = $dono->table('tenants')->where('slug', 'escola-b')->pluck('id');

        $dono->table('personagens')->whereIn('tenant_id', $ids)->delete();
        $dono->table('usuarios')->whereIn('tenant_id', $ids)->delete();
        $dono->table('tenant_dominios')->whereIn('tenant_id', $ids)->delete();
        $dono->table('tenants')->whereIn('id', $ids)->delete();

        DB::beginTransaction();

        parent::tearDown();
    }

    private function contexto(): ContextoDoTenant
    {
        return app(ContextoDoTenant::class);
    }

    // ------------------------------------------------------------------ o banco

    #[Test]
    public function duas_escolas_podem_ter_o_mesmo_email(): void
    {
        // ARRANGE: a mesma pessoa estuda nas duas. Antes, o banco recusava a segunda conta.
        Usuario::create(['nome' => 'Ana', 'email' => self::EMAIL, 'senha_hash' => 'x']);

        // ACT
        $naEscolaB = $this->contexto()->usar($this->escolaB, function (): int {
            Usuario::create(['nome' => 'Ana', 'email' => self::EMAIL, 'senha_hash' => 'y']);

            return Usuario::query()->whereRaw('lower(email) = ?', [self::EMAIL])->count();
        });

        // ASSERT: são duas linhas, e cada escola enxerga exatamente uma — a sua.
        $this->assertSame(1, $naEscolaB);
        $this->assertSame(1, Usuario::query()->whereRaw('lower(email) = ?', [self::EMAIL])->count());
    }

    #[Test]
    public function o_email_continua_unico_dentro_da_mesma_escola(): void
    {
        // ARRANGE: afrouxar entre escolas não pode afrouxar dentro de uma.
        Usuario::create(['nome' => 'Ana', 'email' => self::EMAIL, 'senha_hash' => 'x']);

        // ACT + ASSERT
        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->expectExceptionMessageMatches('/usuarios_email_unique/');

        Usuario::create(['nome' => 'Outra', 'email' => 'ANA@escola-a.test', 'senha_hash' => 'y']);
    }

    // ------------------------------------------------------------------ a rota pública

    /**
     * O sintoma que o visitante via: **500**. E o que ele aprendia: que o e-mail existe em
     * outra escola. Agora ele apenas cria a conta dele, como qualquer visitante.
     */
    #[Test]
    public function registrar_com_um_email_usado_em_outra_escola_funciona(): void
    {
        // ARRANGE: a conta existe na escola padrão.
        Usuario::create(['nome' => 'Ana', 'email' => self::EMAIL, 'senha_hash' => Hash::make('segredo123')]);

        // ACT: um visitante do domínio da escola B se registra com o mesmo e-mail.
        $resposta = $this->post('http://b.algorithmia.test/registrar', [
            'nome' => 'Ana',
            'email' => self::EMAIL,
            'password' => 'segredo123',
            'password_confirmation' => 'segredo123',
        ]);

        // ASSERT
        $resposta->assertRedirect('http://b.algorithmia.test/personagem/criar');
        $resposta->assertSessionHasNoErrors();

        $naEscolaB = $this->contexto()->usar(
            $this->escolaB,
            fn (): int => Usuario::query()->whereRaw('lower(email) = ?', [self::EMAIL])->count(),
        );

        $this->assertSame(1, $naEscolaB);
    }

    #[Test]
    public function registrar_com_um_email_ja_usado_na_propria_escola_da_erro_de_validacao(): void
    {
        // ARRANGE
        Usuario::create(['nome' => 'Ana', 'email' => self::EMAIL, 'senha_hash' => 'x']);

        // ACT + ASSERT: erro de validação, e não 500.
        $this->post('http://localhost/registrar', [
            'nome' => 'Outra',
            'email' => self::EMAIL,
            'password' => 'segredo123',
            'password_confirmation' => 'segredo123',
        ])->assertSessionHasErrors('email');
    }

    /** O login continua achando a conta certa: ele roda dentro do contexto do host. */
    #[Test]
    public function o_login_de_cada_escola_encontra_a_conta_daquela_escola(): void
    {
        // ARRANGE: mesma pessoa, senhas diferentes nas duas escolas.
        Usuario::create(['nome' => 'Ana', 'email' => self::EMAIL, 'senha_hash' => Hash::make('senha-padrao')]);

        $this->contexto()->usar($this->escolaB, fn (): Usuario => Usuario::create([
            'nome' => 'Ana', 'email' => self::EMAIL, 'senha_hash' => Hash::make('senha-da-b'),
        ]));

        // ACT + ASSERT: a senha da escola B não abre a conta da padrão…
        $this->post('http://localhost/entrar', ['email' => self::EMAIL, 'password' => 'senha-da-b'])
            ->assertSessionHasErrors('email');

        // …e abre a da escola B.
        $this->post('http://b.algorithmia.test/entrar', ['email' => self::EMAIL, 'password' => 'senha-da-b'])
            ->assertSessionHasNoErrors();

        $this->assertAuthenticated();
    }
}
