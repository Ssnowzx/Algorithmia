<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dominio\Relatorios\RelatorioDeTurma;
use App\Models\Turma;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * O relatório é a razão de a turma existir. Uma tela bonita sobre aritmética errada é
 * pior que nenhuma tela: o professor age com base nela.
 */
final class RelatorioDeTurmaTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mundo = new Mundo;
    }

    /**
     * @return array{
     *   alunos: list<array<string,mixed>>,
     *   por_assunto: list<array<string,mixed>>,
     *   turma: array{nome:string,codigo:string,alunos:int}
     * }
     */
    private function relatorio(Turma $turma): array
    {
        return app(RelatorioDeTurma::class)->gerar($turma);
    }

    #[Test]
    public function conta_fases_estrelas_precisao_e_uso_do_fragmento(): void
    {
        // ARRANGE
        $turma = Turma::create(['nome' => '9º A', 'codigo' => '9A']);
        $heroi = $this->mundo->heroi();
        $usuario = Usuario::query()->findOrFail($heroi->usuario_id);
        $turma->alunos()->attach($usuario->id);

        $fase = $this->mundo->fase();
        DB::table('progresso_fases')->insert([
            ['personagem_id' => $heroi->id, 'fase_id' => $fase->id, 'estrelas' => 3, 'acertos' => 4, 'erros' => 1, 'usou_ia' => false],
        ]);

        // Três respostas: duas certas, uma delas com o Fragmento da IA.
        $desafio = $this->mundo->desafio($fase->id);
        DB::table('respostas_log')->insert([
            ['personagem_id' => $heroi->id, 'desafio_id' => $desafio->id, 'correta' => true, 'usou_ia' => false],
            ['personagem_id' => $heroi->id, 'desafio_id' => $desafio->id, 'correta' => true, 'usou_ia' => true],
            ['personagem_id' => $heroi->id, 'desafio_id' => $desafio->id, 'correta' => false, 'usou_ia' => false],
        ]);

        // ACT
        $aluno = $this->relatorio($turma)['alunos'][0];

        // ASSERT
        $this->assertSame(1, $aluno['fases_concluidas']);
        $this->assertSame(3, $aluno['estrelas']);
        $this->assertSame(3, $aluno['tentativas']);
        $this->assertSame(67, $aluno['precisao']); // 2 de 3
        $this->assertSame(1, $aluno['respostas_com_ia']);
    }

    /**
     * Um aluno matriculado que nunca criou herói aparece no relatório, zerado. Sumir com
     * ele esconderia o problema — e o problema é que ele nunca jogou.
     */
    #[Test]
    public function um_aluno_que_nunca_jogou_aparece_zerado_e_sem_precisao(): void
    {
        // ARRANGE
        $turma = Turma::create(['nome' => '9º A', 'codigo' => '9A']);
        $usuario = Usuario::create(['nome' => 'Ausente', 'email' => 'ausente@a.test', 'senha_hash' => 'x']);
        $turma->alunos()->attach($usuario->id);

        // ACT
        $aluno = $this->relatorio($turma)['alunos'][0];

        // ASSERT: `null`, e não `0%` — zero por cento mentiria sobre quem nunca tentou.
        $this->assertSame('Ausente', $aluno['nome']);
        $this->assertNull($aluno['personagem_id']);
        $this->assertSame(0, $aluno['tentativas']);
        $this->assertNull($aluno['precisao']);
    }

    #[Test]
    public function a_precisao_por_assunto_mostra_onde_a_turma_pediu_ajuda(): void
    {
        // ARRANGE
        $turma = Turma::create(['nome' => '9º A', 'codigo' => '9A']);
        $heroi = $this->mundo->heroi();
        $turma->alunos()->attach($heroi->usuario_id);

        $fase = $this->mundo->fase();
        $php = $this->mundo->desafio($fase->id, sobrescritas: ['assunto' => 'php']);
        $sql = $this->mundo->desafio($fase->id, ordem: 1, sobrescritas: ['assunto' => 'sql']);

        DB::table('respostas_log')->insert([
            ['personagem_id' => $heroi->id, 'desafio_id' => $php->id, 'correta' => true, 'usou_ia' => false],
            ['personagem_id' => $heroi->id, 'desafio_id' => $php->id, 'correta' => true, 'usou_ia' => false],
            ['personagem_id' => $heroi->id, 'desafio_id' => $sql->id, 'correta' => false, 'usou_ia' => true],
            ['personagem_id' => $heroi->id, 'desafio_id' => $sql->id, 'correta' => true, 'usou_ia' => true],
        ]);

        // ACT
        $porAssunto = [];
        foreach ($this->relatorio($turma)['por_assunto'] as $linha) {
            $porAssunto[(string) $linha['assunto']] = $linha;
        }

        // ASSERT
        $this->assertSame(100, $porAssunto['php']['precisao']);
        $this->assertSame(0, $porAssunto['php']['com_ia']);

        $this->assertSame(50, $porAssunto['sql']['precisao']);
        $this->assertSame(2, $porAssunto['sql']['com_ia'], 'é aqui que o conteúdo está difícil');
    }

    #[Test]
    public function o_relatorio_nao_conta_alunos_de_outra_turma(): void
    {
        // ARRANGE
        $minha = Turma::create(['nome' => '9º A', 'codigo' => '9A']);
        $outra = Turma::create(['nome' => '9º B', 'codigo' => '9B']);

        $meu = $this->mundo->heroi();
        $alheio = $this->mundo->heroi();
        $minha->alunos()->attach($meu->usuario_id);
        $outra->alunos()->attach($alheio->usuario_id);

        // ACT
        $relatorio = $this->relatorio($minha);

        // ASSERT
        $this->assertCount(1, $relatorio['alunos']);
        $this->assertSame(1, $relatorio['turma']['alunos']);
    }

    #[Test]
    public function uma_turma_vazia_nao_quebra_e_nao_consulta_o_banco_a_toa(): void
    {
        // ARRANGE
        $turma = Turma::create(['nome' => 'Vazia', 'codigo' => 'V1']);

        // ACT: sem alunos, não há `whereIn` com lista vazia — que no PostgreSQL vira
        // `IN ()` e é erro de sintaxe. As agregações saem cedo.
        $relatorio = $this->relatorio($turma);

        // ASSERT
        $this->assertSame([], $relatorio['alunos']);
        $this->assertSame([], $relatorio['por_assunto']);
        $this->assertSame(0, $relatorio['turma']['alunos']);
    }
}
