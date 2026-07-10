<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * O pipeline de importação, exercitado contra um "legado" de mentira em SQLite.
 *
 * O MySQL real não existe na CI, e depender dele para testar tornaria estes
 * testes um privilégio da máquina de quem escreveu o comando. O que importa aqui
 * não é o dialeto de origem — é a preservação de IDs, a conversão de tipos, o
 * ensaio que não grava e a recusa em sobrescrever dados sem permissão.
 */
final class ImportacaoDoLegadoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.connections.legado' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => false,
        ]]);
        DB::purge('legado');

        $this->montarLegado();
    }

    #[Test]
    public function o_ensaio_exercita_tudo_e_nao_grava_nada(): void
    {
        // ARRANGE + ACT
        $this->artisan('algorithmia:importar --dry-run')
            ->expectsOutputToContain('ENSAIO')
            ->assertSuccessful();

        // ASSERT: as chaves estrangeiras e a conversão de tipos foram exercitadas,
        // mas o destino continua vazio.
        $this->assertSame(0, DB::table('desafios')->count());
        $this->assertSame(0, DB::table('usuarios')->count());
    }

    #[Test]
    public function deve_preservar_os_ids_das_fases_secundarias(): void
    {
        // Sem isto, a conquista `arquivista_do_vazio` fica inalcançável em silêncio:
        // ServicoDeConquistas referencia essas fases por ID fixo.

        // ACT
        $this->artisan('algorithmia:importar')->assertSuccessful();

        // ASSERT
        /** @var list<int> $secundarias */
        $secundarias = config('jogo.fases_secundarias');
        foreach ($secundarias as $id) {
            $this->assertSame('secundaria', DB::table('fases')->where('id', $id)->value('tipo'));
        }
    }

    #[Test]
    public function deve_converter_tinyint_em_boolean_e_json_em_jsonb(): void
    {
        // ACT
        $this->artisan('algorithmia:importar')->assertSuccessful();

        // ASSERT: o 0/1 do MySQL não entra numa coluna boolean do PostgreSQL.
        $this->assertTrue((bool) DB::table('itens')->where('id', 1)->value('compravel'));
        $this->assertFalse((bool) DB::table('itens')->where('id', 2)->value('compravel'));

        // E o jsonb aceita consulta estruturada, o que um campo `text` não faria.
        $this->assertSame(5, DB::scalar("SELECT (efeito->>'ataque')::int FROM itens WHERE id = 1"));
        $this->assertSame('array', DB::scalar('SELECT jsonb_typeof(resposta) FROM desafios WHERE id = 2'));
        $this->assertSame('boolean', DB::scalar('SELECT jsonb_typeof(resposta) FROM desafios WHERE id = 3'));
    }

    #[Test]
    public function deve_reposicionar_as_sequencias_para_nao_colidir_com_os_ids_importados(): void
    {
        // ARRANGE + ACT
        $this->artisan('algorithmia:importar')->assertSuccessful();

        // ASSERT: os IDs vieram explícitos, então a sequência ficaria em 1.
        $novoId = DB::table('mestres')->insertGetId(
            ['nome' => 'Novo', 'titulo' => 't', 'disciplina' => 'd', 'regiao' => 'r', 'svg_slug' => 's'], 'id'
        );
        $this->assertGreaterThan(5, $novoId, 'a sequência precisa começar depois do maior ID importado');
    }

    #[Test]
    public function deve_recusar_sobrescrever_um_destino_com_dados(): void
    {
        // ARRANGE
        $this->artisan('algorithmia:importar')->assertSuccessful();

        // ACT + ASSERT
        $this->artisan('algorithmia:importar')
            ->expectsOutputToContain('já tem dados')
            ->assertFailed();
    }

    #[Test]
    public function com_truncar_substitui_o_destino_sem_duplicar(): void
    {
        // ARRANGE
        $this->artisan('algorithmia:importar')->assertSuccessful();

        // ACT
        $this->artisan('algorithmia:importar --truncar')->assertSuccessful();

        // ASSERT
        $this->assertSame(3, DB::table('desafios')->count());
        $this->assertSame(5, DB::table('fases')->count());
    }

    #[Test]
    public function deve_reconstruir_a_auto_referencia_das_fases(): void
    {
        // ARRANGE: no legado, a fase 32 exige a 20 — e a 20 é criada depois da 8.
        // ACT
        $this->artisan('algorithmia:importar')->assertSuccessful();

        // ASSERT
        $this->assertSame(20, DB::table('fases')->where('id', 32)->value('requisito_fase_id'));
        $this->assertNull(DB::table('fases')->where('id', 8)->value('requisito_fase_id'));
    }

    #[Test]
    public function deve_abortar_e_desfazer_quando_o_legado_tem_referencia_orfa(): void
    {
        // ARRANGE: um desafio apontando para uma fase que não existe.
        DB::connection('legado')->table('desafios')->insert([
            'id' => 99, 'fase_id' => 777, 'ordem' => 0, 'tipo' => 'multipla', 'assunto' => 'php',
            'pergunta' => 'Órfão', 'opcoes' => '[]', 'resposta' => '0', 'explicacao' => 'x', 'dificuldade' => 1,
        ]);

        // ACT
        $this->artisan('algorithmia:importar')
            ->expectsOutputToContain('abortada e desfeita')
            ->assertFailed();

        // ASSERT: nem as tabelas que já haviam sido copiadas sobreviveram.
        $this->assertSame(0, DB::table('usuarios')->count());
        $this->assertSame(0, DB::table('fases')->count());
    }

    /** Um legado em miniatura, com a mesma forma de colunas do MySQL real. */
    private function montarLegado(): void
    {
        $schema = Schema::connection('legado');

        $schema->create('usuarios', function (Blueprint $t): void {
            $t->integer('id')->primary();
            $t->string('nome');
            $t->string('email');
            $t->string('senha_hash');
            $t->string('papel')->default('jogador');
            $t->string('criado_em')->nullable();
        });
        $schema->create('mestres', function (Blueprint $t): void {
            $t->integer('id')->primary();
            $t->string('nome');
            $t->string('titulo');
            $t->string('disciplina');
            $t->string('regiao');
            $t->text('historia')->nullable();
            $t->text('personalidade')->nullable();
            $t->string('bordao')->nullable();
            $t->string('svg_slug');
            $t->string('cor_tema')->default('#7c5cff');
            $t->integer('ordem')->default(0);
        });
        $schema->create('itens', function (Blueprint $t): void {
            $t->integer('id')->primary();
            $t->string('nome');
            $t->text('descricao')->nullable();
            $t->string('tipo');
            $t->text('efeito')->nullable();
            $t->integer('preco')->default(0);
            $t->string('svg_slug');
            $t->string('raridade')->default('comum');
            $t->integer('compravel')->default(1); // TINYINT(1) no MySQL
        });
        $schema->create('conquistas', function (Blueprint $t): void {
            $t->integer('id')->primary();
            $t->string('codigo');
            $t->string('nome');
            $t->string('descricao');
            $t->string('svg_slug')->default('conquista-generica');
            $t->integer('secreta')->default(0);
        });
        $schema->create('personagens', function (Blueprint $t): void {
            $t->integer('id')->primary();
            $t->integer('usuario_id');
            $t->string('nome');
            $t->string('classe');
            $t->integer('nivel')->default(1);
            $t->integer('xp')->default(0);
            $t->integer('hp_max')->default(100);
            $t->integer('hp_atual')->default(100);
            $t->integer('mp_max')->default(40);
            $t->integer('mp_atual')->default(40);
            $t->integer('ouro')->default(50);
            $t->integer('reputacao')->default(0);
            $t->integer('capitulo')->default(0);
            $t->string('criado_em')->nullable();
            $t->string('atualizado_em')->nullable();
        });
        $schema->create('fases', function (Blueprint $t): void {
            $t->integer('id')->primary();
            $t->integer('mestre_id')->nullable();
            $t->integer('ordem_global');
            $t->string('nome');
            $t->string('tipo')->default('licao');
            $t->text('descricao')->nullable();
            $t->string('inimigo_nome')->nullable();
            $t->string('inimigo_svg')->nullable();
            $t->integer('inimigo_hp')->default(60);
            $t->integer('inimigo_ataque')->default(10);
            $t->integer('xp_recompensa')->default(50);
            $t->integer('ouro_recompensa')->default(20);
            $t->integer('item_drop_id')->nullable();
            $t->integer('requisito_fase_id')->nullable();
        });
        $schema->create('desafios', function (Blueprint $t): void {
            $t->integer('id')->primary();
            $t->integer('fase_id');
            $t->integer('ordem')->default(0);
            $t->string('tipo');
            $t->string('assunto');
            $t->text('pergunta');
            $t->text('codigo')->nullable();
            $t->text('opcoes')->nullable();
            $t->text('resposta');
            $t->text('explicacao');
            $t->integer('dificuldade')->default(1);
        });
        $schema->create('dialogos', function (Blueprint $t): void {
            $t->integer('id')->primary();
            $t->integer('fase_id');
            $t->string('momento')->default('antes');
            $t->string('variante')->default('padrao');
            $t->integer('ordem')->default(0);
            $t->string('falante');
            $t->string('svg_slug')->nullable();
            $t->text('texto');
        });
        $schema->create('inventario', function (Blueprint $t): void {
            $t->integer('id')->primary();
            $t->integer('personagem_id');
            $t->integer('item_id');
            $t->integer('quantidade')->default(1);
            $t->integer('equipado')->default(0);
        });
        $schema->create('progresso_fases', function (Blueprint $t): void {
            $t->integer('id')->primary();
            $t->integer('personagem_id');
            $t->integer('fase_id');
            $t->integer('estrelas')->default(0);
            $t->integer('acertos')->default(0);
            $t->integer('erros')->default(0);
            $t->integer('usou_ia')->default(0);
            $t->string('concluida_em')->nullable();
        });
        $schema->create('conquistas_personagem', function (Blueprint $t): void {
            $t->integer('personagem_id');
            $t->integer('conquista_id');
            $t->string('obtida_em')->nullable();
        });
        $schema->create('escolhas', function (Blueprint $t): void {
            $t->integer('id')->primary();
            $t->integer('personagem_id');
            $t->string('codigo');
            $t->string('valor');
            $t->string('criado_em')->nullable();
        });
        $schema->create('respostas_log', function (Blueprint $t): void {
            $t->integer('id')->primary();
            $t->integer('personagem_id');
            $t->integer('desafio_id');
            $t->integer('correta');
            $t->integer('usou_ia')->default(0);
            $t->string('respondido_em')->nullable();
        });

        $this->popularLegado();
    }

    private function popularLegado(): void
    {
        $legado = DB::connection('legado');
        $agora = '2026-07-08 12:00:00';

        $legado->table('usuarios')->insert([
            ['id' => 1, 'nome' => 'Ana', 'email' => 'ana@x.test', 'senha_hash' => 'h', 'papel' => 'jogador', 'criado_em' => $agora],
            ['id' => 7, 'nome' => 'Boss', 'email' => 'boss@x.test', 'senha_hash' => 'h', 'papel' => 'mestre', 'criado_em' => $agora],
        ]);

        $legado->table('mestres')->insert([
            ['id' => 5, 'nome' => 'Willen', 'titulo' => 'o Arquiteto', 'disciplina' => 'PHP', 'regiao' => 'Porto', 'svg_slug' => 'mestre-willen', 'cor_tema' => '#7c5cff', 'ordem' => 1],
        ]);

        $legado->table('itens')->insert([
            ['id' => 1, 'nome' => 'Espada', 'tipo' => 'arma', 'efeito' => '{"ataque": 5}', 'preco' => 10, 'svg_slug' => 'item-espada', 'raridade' => 'comum', 'compravel' => 1],
            ['id' => 2, 'nome' => 'Fragmento', 'tipo' => 'especial', 'efeito' => null, 'preco' => 0, 'svg_slug' => 'item-fragmento-ia', 'raridade' => 'lendario', 'compravel' => 0],
        ]);

        $legado->table('conquistas')->insert([
            ['id' => 3, 'codigo' => 'arquivista_do_vazio', 'nome' => 'Arquivista', 'descricao' => 'Segredo', 'svg_slug' => 'c', 'secreta' => 1],
        ]);

        $legado->table('personagens')->insert([
            ['id' => 2, 'usuario_id' => 1, 'nome' => 'Snows', 'classe' => 'mago', 'nivel' => 3, 'xp' => 300, 'hp_max' => 80, 'hp_atual' => 80, 'mp_max' => 60, 'mp_atual' => 60, 'ouro' => 120, 'reputacao' => -10, 'capitulo' => 1, 'criado_em' => $agora, 'atualizado_em' => $agora],
        ]);

        // As 4 secundárias do jogo real, pelos IDs que ServicoDeConquistas espera,
        // mais a 32 exigindo a 20 (auto-referência).
        $legado->table('fases')->insert([
            ['id' => 8, 'mestre_id' => 5, 'ordem_global' => 8, 'nome' => 'O Baú do SELECT', 'tipo' => 'secundaria', 'inimigo_hp' => 60, 'inimigo_ataque' => 10, 'xp_recompensa' => 50, 'ouro_recompensa' => 20, 'item_drop_id' => 1, 'requisito_fase_id' => null],
            ['id' => 14, 'mestre_id' => 5, 'ordem_global' => 14, 'nome' => 'Interfaces Secretas', 'tipo' => 'secundaria', 'inimigo_hp' => 60, 'inimigo_ataque' => 10, 'xp_recompensa' => 50, 'ouro_recompensa' => 20, 'item_drop_id' => null, 'requisito_fase_id' => 8],
            ['id' => 20, 'mestre_id' => 5, 'ordem_global' => 20, 'nome' => 'O Atalho', 'tipo' => 'secundaria', 'inimigo_hp' => 60, 'inimigo_ataque' => 10, 'xp_recompensa' => 50, 'ouro_recompensa' => 20, 'item_drop_id' => null, 'requisito_fase_id' => 14],
            ['id' => 25, 'mestre_id' => 5, 'ordem_global' => 25, 'nome' => 'Chefe', 'tipo' => 'chefe', 'inimigo_hp' => 200, 'inimigo_ataque' => 18, 'xp_recompensa' => 200, 'ouro_recompensa' => 80, 'item_drop_id' => null, 'requisito_fase_id' => null],
            ['id' => 32, 'mestre_id' => 5, 'ordem_global' => 32, 'nome' => 'O Pacote Perdido', 'tipo' => 'secundaria', 'inimigo_hp' => 60, 'inimigo_ataque' => 10, 'xp_recompensa' => 50, 'ouro_recompensa' => 20, 'item_drop_id' => null, 'requisito_fase_id' => 20],
        ]);

        $legado->table('desafios')->insert([
            ['id' => 1, 'fase_id' => 8, 'ordem' => 0, 'tipo' => 'multipla', 'assunto' => 'sql', 'pergunta' => 'Qual?', 'opcoes' => '["a", "b"]', 'resposta' => '1', 'explicacao' => 'x', 'dificuldade' => 1],
            ['id' => 2, 'fase_id' => 8, 'ordem' => 1, 'tipo' => 'ordenar', 'assunto' => 'sql', 'pergunta' => 'Ordene', 'opcoes' => '["a", "b"]', 'resposta' => '[1, 0]', 'explicacao' => 'x', 'dificuldade' => 2],
            ['id' => 3, 'fase_id' => 14, 'ordem' => 0, 'tipo' => 'vf', 'assunto' => 'poo', 'pergunta' => 'Certo?', 'opcoes' => null, 'resposta' => 'true', 'explicacao' => 'x', 'dificuldade' => 1],
        ]);

        $legado->table('dialogos')->insert([
            ['id' => 1, 'fase_id' => 8, 'momento' => 'antes', 'variante' => 'padrao', 'ordem' => 0, 'falante' => 'Willen', 'svg_slug' => null, 'texto' => 'Olá.'],
        ]);

        $legado->table('inventario')->insert([
            ['id' => 4, 'personagem_id' => 2, 'item_id' => 1, 'quantidade' => 1, 'equipado' => 1],
        ]);

        $legado->table('progresso_fases')->insert([
            ['id' => 6, 'personagem_id' => 2, 'fase_id' => 8, 'estrelas' => 3, 'acertos' => 4, 'erros' => 0, 'usou_ia' => 0, 'concluida_em' => $agora],
        ]);

        $legado->table('conquistas_personagem')->insert([
            ['personagem_id' => 2, 'conquista_id' => 3, 'obtida_em' => $agora],
        ]);

        $legado->table('respostas_log')->insert([
            ['id' => 9, 'personagem_id' => 2, 'desafio_id' => 1, 'correta' => 1, 'usou_ia' => 0, 'respondido_em' => $agora],
            ['id' => 10, 'personagem_id' => 2, 'desafio_id' => 3, 'correta' => 1, 'usou_ia' => 1, 'respondido_em' => $agora],
        ]);
    }
}
