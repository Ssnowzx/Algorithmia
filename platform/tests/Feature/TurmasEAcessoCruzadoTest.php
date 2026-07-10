<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dominio\Tenancy\ContextoDoTenant;
use App\Models\RegistroDeAuditoria;
use App\Models\Turma;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * Etapa D. Duas fronteiras, e elas não são a mesma coisa:
 *
 * - **Entre instituições**, a fronteira é o RLS. A turma da Escola B nem chega a existir
 *   para uma consulta feita no contexto da Escola A.
 * - **Dentro da instituição**, a fronteira é autorização. Para o banco, as turmas de dois
 *   professores da mesma escola são linhas iguais; quem as separa é a `TurmaPolicy`.
 *
 * Trancar o prédio e deixar as salas abertas daria a sensação de isolamento sem o
 * isolamento. Estes testes cobrem as duas portas.
 */
final class TurmasEAcessoCruzadoTest extends TestCase
{
    use RefreshDatabase;

    private int $escolaB;

    protected function setUp(): void
    {
        parent::setUp();

        // A Etapa E pôs as turmas atrás de `flag:turmas`, desligada por padrão. Sem ligá-la
        // aqui, TODA rota de turma devolveria 404 — e os testes de acesso cruzado, que
        // esperam justamente 404, passariam pelo motivo errado.
        $this->ligarFlag('turmas');

        $this->escolaB = (int) DB::connection('pgsql_dono')->table('tenants')->insertGetId([
            'nome' => 'Escola B', 'slug' => 'escola-b', 'ativo' => true,
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

        // A ordem obedece às chaves: `usuarios` referencia `tenants` com RESTRICT.
        $dono->table('turma_professores')->whereIn('tenant_id', $ids)->delete();
        $dono->table('matriculas')->whereIn('tenant_id', $ids)->delete();
        $dono->table('turmas')->whereIn('tenant_id', $ids)->delete();
        $dono->table('usuarios')->whereIn('tenant_id', $ids)->delete();
        $dono->table('tenants')->whereIn('id', $ids)->delete();

        DB::beginTransaction();

        parent::tearDown();
    }

    private function conta(string $papel, string $email): Usuario
    {
        return Usuario::create(['nome' => ucfirst($papel), 'email' => $email, 'senha_hash' => 'x', 'papel' => $papel]);
    }

    private function turma(string $nome, string $codigo): Turma
    {
        return Turma::create(['nome' => $nome, 'codigo' => $codigo]);
    }

    // ---------------------------------------------------- dentro da mesma instituição

    #[Test]
    public function o_professor_ve_o_relatorio_da_turma_que_leciona(): void
    {
        // ARRANGE
        $professor = $this->conta('professor', 'prof@a.test');
        $turma = $this->turma('9º A', '9A');
        $turma->professores()->attach($professor->id);

        // ACT + ASSERT
        $this->actingAs($professor)->get(route('turmas.ver', $turma))->assertOk();
    }

    #[Test]
    public function o_professor_nao_ve_a_turma_de_outro_professor_da_mesma_escola(): void
    {
        // ARRANGE: para o banco, as duas turmas são linhas do mesmo tenant. O RLS não
        // tem o que fazer aqui — quem separa é a policy.
        $professor = $this->conta('professor', 'prof@a.test');
        $doOutro = $this->turma('9º B', '9B');

        // ACT + ASSERT
        $this->actingAs($professor)->get(route('turmas.ver', $doOutro))->assertForbidden();
    }

    #[Test]
    public function a_listagem_do_professor_traz_apenas_as_turmas_dele(): void
    {
        // ARRANGE
        $professor = $this->conta('professor', 'prof@a.test');
        $minha = $this->turma('9º A', '9A');
        $this->turma('9º B', '9B');
        $minha->professores()->attach($professor->id);

        // ACT + ASSERT
        $this->actingAs($professor)->get(route('turmas.index'))
            ->assertOk()
            ->assertSee('9º A')
            ->assertDontSee('9º B');
    }

    #[Test]
    public function o_mestre_administra_a_escola_inteira(): void
    {
        // ARRANGE
        $mestre = $this->conta('mestre', 'mestre@a.test');
        $this->turma('9º A', '9A');
        $this->turma('9º B', '9B');

        // ACT + ASSERT
        $this->actingAs($mestre)->get(route('turmas.index'))
            ->assertOk()->assertSee('9º A')->assertSee('9º B');
    }

    #[Test]
    public function um_aluno_nao_abre_relatorio_nenhum(): void
    {
        // ARRANGE
        $aluno = $this->conta('jogador', 'aluno@a.test');
        $turma = $this->turma('9º A', '9A');

        // ACT + ASSERT
        $this->actingAs($aluno)->get(route('turmas.index'))->assertForbidden();
        $this->actingAs($aluno)->get(route('turmas.ver', $turma))->assertForbidden();
    }

    #[Test]
    public function um_professor_nao_cria_nem_matricula(): void
    {
        // ARRANGE: criar turma e matricular é administração da escola.
        $professor = $this->conta('professor', 'prof@a.test');
        $turma = $this->turma('9º A', '9A');
        $aluno = $this->conta('jogador', 'aluno@a.test');

        // ACT + ASSERT
        $this->actingAs($professor)->post(route('turmas.criar'), ['nome' => 'X', 'codigo' => 'X1'])
            ->assertForbidden();
        $this->actingAs($professor)->post(route('turmas.matricular', $turma), ['usuario_id' => $aluno->id])
            ->assertForbidden();
    }

    // ----------------------------------------------------------- entre instituições

    #[Test]
    public function uma_turma_de_outra_escola_nem_existe_para_esta(): void
    {
        // ARRANGE: a turma nasce no contexto da Escola B.
        $contexto = app(ContextoDoTenant::class);
        $idDaOutra = $contexto->usar($this->escolaB, fn (): int => $this->turma('Turma da B', 'B1')->id);

        // ACT + ASSERT: de volta ao contexto padrão, o RLS a esconde — 404, e não 403.
        // A diferença importa: 403 revelaria que ela existe.
        $mestre = $this->conta('mestre', 'mestre@a.test');
        $this->actingAs($mestre)->get("/turmas/{$idDaOutra}")->assertNotFound();

        $this->assertNull(Turma::query()->find($idDaOutra));
    }

    #[Test]
    public function nao_se_matricula_um_aluno_de_outra_escola(): void
    {
        // ARRANGE
        $contexto = app(ContextoDoTenant::class);
        $alunoDaOutra = $contexto->usar(
            $this->escolaB,
            fn (): int => $this->conta('jogador', 'aluno@b.test')->id
        );

        $mestre = $this->conta('mestre', 'mestre@a.test');
        $turma = $this->turma('9º A', '9A');

        // ACT + ASSERT: o `Rule::exists` roda sob RLS — aquele id não existe aqui.
        $this->actingAs($mestre)
            ->post(route('turmas.matricular', $turma), ['usuario_id' => $alunoDaOutra])
            ->assertSessionHasErrors('usuario_id');

        $this->assertSame(0, DB::table('matriculas')->count());
    }

    // ----------------------------------------------------------------- B.8: auditoria

    #[Test]
    public function vincular_um_professor_deixa_rastro(): void
    {
        // ARRANGE: quem passou a poder ler o progresso de quais alunos é exatamente o
        // tipo de mudança que ninguém lembra de ter feito, três meses depois.
        $mestre = $this->conta('mestre', 'mestre@a.test');
        $professor = $this->conta('professor', 'prof@a.test');
        $turma = $this->turma('9º A', '9A');

        // ACT
        $this->actingAs($mestre)
            ->post(route('turmas.professor.vincular', $turma), ['usuario_id' => $professor->id])
            ->assertRedirect();

        // ASSERT
        $registro = RegistroDeAuditoria::query()->where('acao', 'turma.professor.vincular')->sole();
        $this->assertSame($mestre->email, $registro->autor_email);
        $this->assertSame($professor->email, $registro->resumo['professor_email']);
        $this->assertSame($turma->id, $registro->alvo_id);
    }
}
