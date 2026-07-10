<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * Trava a tradução do schema MySQL → PostgreSQL.
 *
 * Cada asserção aqui corresponde a uma linha da tabela de riscos do
 * docs/migracao/PLANO.md §5. São coisas que o MySQL dava de graça e que o
 * PostgreSQL só faz se alguém pedir.
 */
final class SchemaTest extends TestCase
{
    use RefreshDatabase;

    private const TABELAS = [
        'usuarios', 'mestres', 'itens', 'personagens', 'fases', 'desafios',
        'inventario', 'progresso_fases', 'conquistas', 'conquistas_personagem',
        'dialogos', 'escolhas', 'respostas_log',
    ];

    #[Test]
    public function as_treze_tabelas_do_legado_existem(): void
    {
        foreach (self::TABELAS as $tabela) {
            $this->assertTrue(Schema::hasTable($tabela), "faltou a tabela {$tabela}");
        }
    }

    #[Test]
    public function os_nove_enums_viraram_constraints_check(): void
    {
        // ARRANGE + ACT
        $checks = DB::table('pg_constraint')
            ->where('contype', 'c')
            ->whereIn(DB::raw('conrelid::regclass::text'), ['usuarios', 'itens', 'personagens', 'fases', 'desafios', 'dialogos'])
            ->count();

        // ASSERT: papel, tipo(itens), raridade, classe, tipo(fases),
        //         tipo(desafios), assunto, momento, variante.
        $this->assertSame(9, $checks);
    }

    #[Test]
    public function o_check_rejeita_valor_fora_do_enum(): void
    {
        // As linhas precisam ser válidas em tudo o mais: no PostgreSQL o NOT NULL
        // dispara antes do CHECK, e um insert incompleto testaria a constraint errada.
        $this->assertViolaCheck('usuarios_papel_check', fn () => DB::table('usuarios')->insert([
            'nome' => 'Ana', 'email' => 'ana@algorithmia.test', 'senha_hash' => 'x', 'papel' => 'admin',
        ]));

        $this->assertViolaCheck('fases_tipo_check', fn () => DB::table('fases')->insert([
            'ordem_global' => 1, 'nome' => 'Fase', 'tipo' => 'cinematica',
        ]));

        $usuarioId = DB::table('usuarios')->insertGetId(
            ['nome' => 'Bia', 'email' => 'bia@algorithmia.test', 'senha_hash' => 'x'], 'id'
        );
        $this->assertViolaCheck('personagens_classe_check', fn () => DB::table('personagens')->insert([
            'usuario_id' => $usuarioId, 'nome' => 'Herói', 'classe' => 'necromante',
        ]));
    }

    /**
     * Executa a inserção e exige que o PostgreSQL a rejeite pela constraint nomeada.
     *
     * O savepoint não é zelo excessivo: no PostgreSQL um statement que falha aborta
     * a transação inteira (SQLSTATE 25P02), e todo comando seguinte é recusado até o
     * rollback. Como o RefreshDatabase já abriu uma transação em volta do teste, sem
     * o savepoint só a primeira violação seria observável. O MySQL era leniente aqui.
     */
    private function assertViolaCheck(string $constraint, callable $insercao): void
    {
        DB::beginTransaction(); // aninhado → SAVEPOINT
        $mensagem = null;

        try {
            $insercao();
        } catch (QueryException $e) {
            $mensagem = $e->getMessage();
        } finally {
            DB::rollBack(); // → ROLLBACK TO SAVEPOINT, transação externa segue viva
        }

        $this->assertNotNull($mensagem, "esperava violação de {$constraint}, mas a inserção passou");
        $this->assertStringContainsString($constraint, $mensagem);
    }

    #[Test]
    public function o_email_continua_unico_ignorando_maiusculas(): void
    {
        // ARRANGE: no MySQL, a collation utf8mb4_unicode_ci fazia isto sozinha.
        DB::table('usuarios')->insert([
            'nome' => 'Ana', 'email' => 'ana@algorithmia.test', 'senha_hash' => 'x',
        ]);

        // ACT + ASSERT
        $this->expectExceptionMessageMatches('/usuarios_email_unique/');
        DB::table('usuarios')->insert([
            'nome' => 'Outra', 'email' => 'Ana@Algorithmia.TEST', 'senha_hash' => 'x',
        ]);
    }

    #[Test]
    public function os_campos_json_sao_jsonb_e_aceitam_consulta_estruturada(): void
    {
        // ARRANGE: `efeito` é lido a cada golpe para somar os bônus do equipamento.
        DB::table('itens')->insert([
            'nome' => 'Espada de Teste', 'tipo' => 'arma',
            'efeito' => json_encode(['ataque' => 5]),
        ]);

        // ACT: operador de jsonb — falharia num campo `text`.
        $ataque = DB::scalar("SELECT (efeito->>'ataque')::int FROM itens LIMIT 1");

        // ASSERT
        $this->assertSame(5, $ataque);
    }

    #[Test]
    public function o_on_conflict_substitui_o_on_duplicate_key_update_do_progresso(): void
    {
        // ARRANGE: ProgressoFase::registrar dependia de ON DUPLICATE KEY UPDATE.
        $usuarioId = DB::table('usuarios')->insertGetId(
            ['nome' => 'Ana', 'email' => 'ana@algorithmia.test', 'senha_hash' => 'x'], 'id'
        );
        $personagemId = DB::table('personagens')->insertGetId(
            ['usuario_id' => $usuarioId, 'nome' => 'Herói', 'classe' => 'mago'], 'id'
        );
        $faseId = DB::table('fases')->insertGetId(
            ['ordem_global' => 1, 'nome' => 'Fase', 'tipo' => 'licao'], 'id'
        );

        // ACT: grava duas vezes; a segunda deve preservar o melhor desempenho.
        foreach ([['estrelas' => 1], ['estrelas' => 3]] as $tentativa) {
            DB::statement(
                'INSERT INTO progresso_fases (personagem_id, fase_id, estrelas)
                 VALUES (?, ?, ?)
                 ON CONFLICT ON CONSTRAINT uq_prog
                 DO UPDATE SET estrelas = GREATEST(progresso_fases.estrelas, EXCLUDED.estrelas)',
                [$personagemId, $faseId, $tentativa['estrelas']]
            );
        }

        // ASSERT
        $this->assertSame(1, DB::table('progresso_fases')->count(), 'a chave única evitou a duplicata');
        $this->assertSame(3, DB::table('progresso_fases')->value('estrelas'));
    }
}
