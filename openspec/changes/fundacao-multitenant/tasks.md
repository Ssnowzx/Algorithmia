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

## C. `tenant_id` nas 13 tabelas do jogo — **antes do corte**

> **A ordem foi invertida em 2026-07-10.** O plano dizia "só depois do corte, em três
> deploys". Os três deploys são regra de **coexistência**, e antes do corte não há código
> velho no ar com que coexistir. E o risco se inverte: antes do corte, o rollback é trocar
> o DNS de volta ao legado — nada a desfazer; depois, seria uma mudança irreversível sobre
> progresso real de alunos. Ver `design.md §4`.

- [x] C.1 `ADD COLUMN tenant_id BIGINT NULL` + índice nas 13, tenant padrão e backfill
- [x] C.2 `SET NOT NULL`, RLS + policies nas 13, `TENANCY_ATIVA=true`
- [x] C.3 `algorithmia:importar` escreve `tenant_id` e define contexto
- [x] C.4 `algorithmia:smoke` e demais comandos de console definem contexto
- [x] C.5 Os 38 vetores-ouro continuam verdes, com os mesmos números (suíte do legado, 53)
- [x] C.6 **Ensaio do corte (§10) refeito com tenancy ligada.** Banco zerado, domínio real
      (`escola.ensaio.test`), primeiro deploy criando o papel e o tenant padrão, importação
      das 1.306 linhas — **todas com `tenant_id`, sem o importador mencionar a coluna** —,
      smoke completo, campanha e batalha pelo domínio, e o isolamento provado em produção:
      tenant 1 vê 955 desafios, tenant 2 vê zero, sem contexto vê zero.
      Achou dois defeitos: o smoke quebraria todo deploy a partir da segunda escola, e uma
      instituição nascia ativa e vazia — reprovando builds corretos.

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
