<?php

declare(strict_types=1);

return [

    /**
     * Enquanto for `false`, o resolvedor de tenant não faz nada e o jogo roda como
     * hoje: uma instituição só, sem `app.tenant_id`, sem transação por requisição.
     *
     * Ligar isto é trabalho da **Etapa C** — quando as 13 tabelas do jogo tiverem
     * `tenant_id` e RLS. Ligar antes disso não quebraria nada, e também não protegeria
     * nada: as tabelas do jogo continuariam abertas.
     *
     * Ver `openspec/changes/fundacao-multitenant/`.
     */
    'ativo' => (bool) env('TENANCY_ATIVA', false),

    /**
     * O nome da variável de sessão do PostgreSQL que carrega o tenant da requisição.
     * As policies de RLS a leem com `current_setting(..., true)` — o `true` faz a
     * função devolver NULL em vez de erro quando ela não está definida, e NULL não
     * casa com `tenant_id` nenhum. Ausência de contexto vira "nenhuma linha", e não
     * "todas as linhas".
     */
    'variavel_de_sessao' => 'app.tenant_id',

];
