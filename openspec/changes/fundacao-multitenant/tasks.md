# Tasks — Fundação multitenant

> **A ordem entre a Etapa B e o corte em produção não é negociável.** Ver `design.md §4`.

## A. Topologia de acesso (aditiva — pode ir antes do corte)

- [x] A.1 Provar que o RLS é inerte hoje: teste que falha porque o papel atual vê tudo
- [x] A.2 Papel `algorithmia_app`: `NOSUPERUSER NOBYPASSRLS`, sem `OWNER`, com `GRANT`
- [x] A.3 Conexão `pgsql_dono` para migrations; `deploy.sh` passa `--database=pgsql_dono`.
      **A importação NÃO precisou dela**: bastou tirar o `RESTART IDENTITY` do `TRUNCATE`
      (que exige ser dono da sequência) — `sincronizarSequencias()` já fazia o trabalho.
      Ela volta ao tema na Etapa C, quando o RLS alcançar as tabelas do jogo.
- [x] A.4 `.env.producao.exemplo`: `DB_USERNAME` da aplicação vs `DB_DONO_*`
- [x] A.5 O teste de A.1 passa a exigir zero linhas sem contexto — e passa

## B. Fundação de tenancy (aditiva — pode ir antes do corte)

- [x] B.1 `tenants`, `tenant_dominios`, `tenant_membros`, `convites`
- [x] B.2 `ENABLE` + `FORCE ROW LEVEL SECURITY` e policies por `current_setting('app.tenant_id')`
- [x] B.3 Middleware de resolução por `Host`; host desconhecido devolve 404, não 500
- [x] B.4 `SET LOCAL app.tenant_id` numa transação por requisição; contexto morre com ela
- [x] B.5 `tenant_id` do cliente é ignorado. Não há **nenhum** caminho de código que o
      leia de corpo, parâmetro ou sessão: o contexto vem só do `Host`. O vetor real é o
      cabeçalho `X-Forwarded-Host`, e é ele que o teste ataca.
- [x] B.6 `ProxyReversoTest` vira teste de isolamento: `X-Forwarded-Host` forjado não resolve tenant
- [x] B.7 Testes de acesso cruzado: Eloquent, query crua, comando de console
- [ ] B.8 Auditoria de mudança de membership — **adiada, e não esquecida**: não existe
      ainda nenhuma rota que altere membership. Escrever a auditoria de uma ação que
      ninguém pode fazer é escrever código que nenhum teste exercita. Entra junto com a
      Etapa D, que cria essas rotas.

## C. `tenant_id` nas 13 tabelas do jogo (INVASIVA — **só depois do corte**)

- [ ] C.0 **Bloqueado por:** corte em produção concluído e estável por alguns dias
- [ ] C.1 Deploy 1 — `ADD COLUMN tenant_id BIGINT NULL` + índice, em todas as 13
- [ ] C.2 Deploy 2 — backfill para o tenant padrão; código novo escreve; RLS aceita `NULL`
- [ ] C.3 Reconciliação: nenhuma linha com `tenant_id` nulo, contagem por tabela
- [ ] C.4 Deploy 3 — `SET NOT NULL`; a policy deixa de aceitar `NULL`
- [ ] C.5 Os 38 vetores-ouro continuam verdes, com os mesmos números

## D. Turmas, papéis e relatórios (Fase 5 do roteiro v1)

- [ ] D.0 **Bloqueado por:** C
- [ ] D.1 `turmas`, `matriculas`, vínculo professor-turma
- [ ] D.2 Papéis: `platform_admin`, `tenant_admin`, `professor`, `editor`, `aluno`
- [ ] D.3 Relatórios de progresso, precisão por assunto, maestria, uso de IA
- [ ] D.4 Testes de acesso cruzado entre professores, turmas e tenants

## E. Piloto (Fase 9 do roteiro v1)

- [ ] E.0 **Bloqueado por:** D
- [ ] E.1 Feature flags por tenant
- [ ] E.2 Provisionamento de tenant piloto, documentado no runbook
- [ ] E.3 Smoke que cobre resolução de tenant, batalha, progresso e relatório
