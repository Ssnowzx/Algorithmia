<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Dois índices únicos **globais** sobre tabelas tenant-scoped. Eles são a mesma classe de
 * defeito da chave primária global: o RLS esconde a linha da outra escola, e o índice a
 * denuncia.
 *
 * ## 1. `usuarios.email` — um oráculo de existência, e um 500 numa rota pública
 *
 * Medido no banco, como `algorithmia_app`, no contexto da escola B:
 *
 * ```
 * SELECT count(*) FROM usuarios WHERE lower(email) = 'ana@escola-a.test';   -->  0
 * INSERT INTO usuarios (nome, email, senha_hash) VALUES ('Outra', 'ana@escola-a.test', 'y');
 * ERROR:  duplicate key value violates unique constraint "usuarios_email_unique"
 * ```
 *
 * O `AutenticacaoController::registrar` faz exatamente essas duas coisas, nessa ordem. Sob
 * RLS a checagem devolve "e-mail livre"; o `INSERT` estoura. O visitante recebe **500 em vez
 * de um erro de validação**, e aprende que aquele e-mail existe **em outra instituição**.
 * Enumeração de contas entre escolas, de graça, numa rota que não exige conta.
 *
 * O índice passa a ser `(tenant_id, lower(email))`. Duas escolas podem ter o mesmo e-mail —
 * que é o que "`usuarios` é tenant-scoped" sempre quis dizer. Isto **não** é a conta global
 * do roteiro v1 §5, e não a impede: aquela exige resolver a identidade antes de saber o
 * tenant, e continua sem demanda. Aqui só se corrige a contradição entre a barreira e o
 * índice.
 *
 * ## 2. `conquistas.codigo` — impede a segunda instituição de ter conteúdo
 *
 * `arquivista_do_vazio` é um código, não um id. Com o único global, só uma escola no banco
 * inteiro pode ter essa conquista no catálogo. O `ServicoDeConquistas` a procura por
 * `codigo` **sob RLS**, ou seja, sempre dentro da instituição — o único global nunca foi
 * necessário, e sempre foi um bloqueio.
 *
 * ## Sobre a segurança de trocar um índice único
 *
 * Isto **afrouxa** uma restrição: tudo o que passava continua passando. Nenhuma linha
 * existente viola o índice novo (ele é mais permissivo), e o código velho — que só conhece
 * uma instituição — não consegue produzir a duplicata que o índice antigo proibia. É
 * seguro rodar contra o código velho ainda no ar.
 *
 * `convites.token_hash` fica global de propósito: um token identifica um convite antes de
 * se saber de quem ele é, e 256 bits de aleatoriedade não colidem.
 */
return new class extends Migration
{
    public function up(): void
    {
        // O índice antigo é sobre `lower(email)`; o novo precisa dizer o mesmo, ou as
        // consultas do `AutenticacaoController` deixam de usá-lo.
        // `usuarios_email_unique` é um índice de expressão (`lower(email)`), criado com
        // `CREATE UNIQUE INDEX`. Não há constraint por trás dele.
        DB::statement('DROP INDEX usuarios_email_unique');
        DB::statement('CREATE UNIQUE INDEX usuarios_email_unique ON usuarios (tenant_id, lower(email))');

        // `conquistas_codigo_unique`, ao contrário, veio do `->unique()` do Laravel: é uma
        // CONSTRAINT com um índice por baixo. `DROP INDEX` nela responde "dependent objects
        // still exist" — quem sai é a constraint, e o índice vai junto.
        DB::statement('ALTER TABLE conquistas DROP CONSTRAINT conquistas_codigo_unique');
        DB::statement('CREATE UNIQUE INDEX conquistas_codigo_unique ON conquistas (tenant_id, codigo)');
    }

    /**
     * O `down()` só funciona se, nesse meio-tempo, ninguém tiver criado a duplicata que o
     * índice global proibia. É a natureza de afrouxar uma restrição, e o motivo de o
     * rollback de verdade ser restaurar o dump.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX conquistas_codigo_unique');
        DB::statement('ALTER TABLE conquistas ADD CONSTRAINT conquistas_codigo_unique UNIQUE (codigo)');

        DB::statement('DROP INDEX usuarios_email_unique');
        DB::statement('CREATE UNIQUE INDEX usuarios_email_unique ON usuarios (lower(email))');
    }
};
