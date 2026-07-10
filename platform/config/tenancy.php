<?php

declare(strict_types=1);

return [

    /**
     * Ligado na Etapa C.2, junto com o RLS nas 13 tabelas do jogo. Desligá-lo agora não
     * é uma opção de conveniência: sem contexto, o `DEFAULT` de `tenant_id` devolve NULL,
     * o `NOT NULL` derruba toda inserção, e as policies escondem toda leitura. O jogo
     * simplesmente não roda.
     *
     * A variável existe para o caminho inverso: um banco anterior à C.2, num rollback.
     *
     * Ver `openspec/changes/fundacao-multitenant/`.
     */
    'ativo' => (bool) env('TENANCY_ATIVA', true),

    /**
     * O nome da variável de sessão do PostgreSQL que carrega o tenant da requisição.
     * As policies de RLS a leem com `current_setting(..., true)` — o `true` faz a
     * função devolver NULL em vez de erro quando ela não está definida, e NULL não
     * casa com `tenant_id` nenhum. Ausência de contexto vira "nenhuma linha", e não
     * "todas as linhas".
     */
    'variavel_de_sessao' => 'app.tenant_id',

];
