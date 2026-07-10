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

    /**
     * O host do console do operador. **Vazio por padrão, e as rotas nem são registradas.**
     *
     * O console lê e administra todas as instituições. Publicá-lo por acidente no domínio
     * de uma escola daria a qualquer aluno a tela de login da plataforma para atacar. Um
     * middleware que checasse o host seria uma linha de código que alguém pode remover; uma
     * rota que não existe não tem como ser alcançada.
     *
     * Ele **não** entra em `tenant_dominios`: o console não é uma instituição, e o
     * `ResolverTenant` é retirado dessas rotas de propósito — não há tenant a resolver.
     *
     * Ponha um host dedicado (`console.algorithmia.exemplo.com`), com o mesmo certificado, e
     * de preferência atrás de um filtro de IP no nginx. Ver `RUNBOOK §11`.
     */
    'host_do_console' => (string) env('CONSOLE_HOST', ''),

];
