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
- [x] B.8 Auditoria de mudança de membership — **adiada aqui, entregue na Etapa D** (ver
      abaixo): não existia ainda nenhuma rota que alterasse membership, e escrever a
      auditoria de uma ação que ninguém pode fazer é escrever código que nenhum teste
      exercita.

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

- [x] D.0 Desbloqueada: C concluída
- [x] D.1 `turmas`, `matriculas`, `turma_professores` — todas tenant-scoped, com `FORCE RLS`
- [x] D.2 Papéis em `usuarios.papel`: `jogador` (aluno), `professor`, `mestre` (administra a
      escola). **`tenant_membros` removida**: com `usuarios` tenant-scoped desde a C, ela era
      1:1 com o usuário — redundante, e uma segunda fonte de verdade para o papel.
      **`platform_admin` NÃO entra**: ele lê através das instituições, e o RLS existe para
      impedir exatamente isso. Se um dia for preciso, será um papel de banco, não de aplicação.
- [x] D.3 `RelatorioDeTurma`: fases, estrelas, tentativas, precisão global e por assunto, uso
      do Fragmento da IA. Três agregações, não uma consulta por aluno.
- [x] D.4 Acesso cruzado: professor × outra turma da mesma escola (403), qualquer coisa de
      outra escola (**404**, não 403 — um 403 revelaria que existe), aluno (403), professor
      tentando administrar (403), matricular aluno de outra escola (recusado pelo `Rule::exists`
      sob RLS).
- [x] B.8 Auditoria de vínculo professor–turma, matrícula e criação. Entregue aqui, como
      previsto: só faz sentido auditar uma ação que alguém pode fazer.

### Pergunta aberta — metade dela era um bug, e foi corrigida

`usuarios` é tenant-scoped, divergindo do roteiro v1 §5, que os queria globais.

> **Registrado aqui como "duas escolas não podem ter o mesmo e-mail", e aceito como
> consequência.** Não era consequência: era um **defeito**, e explorável. O índice
> `usuarios_email_unique` era global, mas o RLS escondia a linha da outra escola — o
> `registrar` concluía "e-mail livre" e o `INSERT` estourava. O visitante recebia **500 numa
> rota pública**, e aprendia que o e-mail existe em outra instituição. Corrigido na F.2: o
> índice virou `(tenant_id, lower(email))`, e **duas escolas podem ter o mesmo e-mail**.

O que **continua aberto** é outra coisa, e bem menor: a *conta global* que o roteiro queria —
uma identidade que atravessa instituições. Ela exige resolver quem é a pessoa antes de saber o
tenant. Sem demanda.

## E. Piloto (Fase 9 do roteiro v1)

- [x] E.0 Desbloqueada: D concluída
- [x] E.1 **Feature flags por tenant.** Catálogo em `config/flags.php`; o banco
      (`tenants.flags` JSONB) guarda só as exceções. Chave desconhecida **levanta exceção**,
      e não devolve `false` — um erro de digitação não pode desligar a funcionalidade em
      silêncio, para sempre. Duas flags, e as duas têm o estado "desligada" seguro:
      `turmas` (padrão OFF) e `ranking` (padrão ON). Cada uma é aplicada na **rota**
      (`flag:<chave>` → 404) e no **menu** (`@flag`); o teste bate na URL sem passar pelo menu.
      **`registro_aberto` foi considerada e recusada:** sem fluxo de convite, fechar o registro
      deixaria a escola sem como matricular o primeiro aluno. Flag que tranca a instituição
      não é liberação progressiva.
- [x] E.2 **Provisionamento, e o portão da ativação.** `algorithmia:tenant:novo` cria a escola
      desligada, com domínio, na mesma transação. `algorithmia:tenant:ativar` **roda o smoke
      daquela instituição antes de ligá-la** e recusa uma escola injogável — a ordem
      provisionar → semear → ligar deixa de ser prosa no runbook. Sem `--forcar`: o escape é
      um `UPDATE` à mão. `desativar` nunca roda o smoke (trancaria por dentro a escola
      quebrada que se quer tirar do ar). `tenant:listar` e `tenant:flag` completam. Runbook
      §10.6b reescrito, §11 novo.
- [x] E.3 **O smoke vai da resolução do tenant ao relatório.** Quatro verificações novas: o
      host do `APP_URL` resolve uma instituição ativa (o 404-com-banco-cheio do §10.1, agora
      em código); a instituição tem domínio primário (ativa e inalcançável reprova o deploy);
      o progresso persiste **e nasce com o `tenant_id` certo** (prova o `DEFAULT` que lê
      `current_setting`); o relatório de turma responde. Tudo em savepoints que voltam, com
      teste contando as linhas antes e depois.
- [x] E.4 **Console do operador** (pedido do usuário; ver `design.md §7`). `operadores` é
      catálogo global com guard próprio; sem `CONSOLE_HOST` as rotas **não são registradas**.
      Métricas cross-tenant sem furar o RLS: um contexto por instituição, via
      `ContextoDoTenant::usar()`. `auditoria.autor_tipo` porque os ids de `usuarios` e
      `operadores` colidem.

### Defeitos de fundo achados construindo a Etapa E

Nenhum deles tinha teste, e nenhum era visível pela leitura.

- [x] **`ContextoDoTenant` não era singleton.** Cada `app()` devolvia uma instância nova, e
      `atual()` respondia `null` a quem não o definira: o `ResolverTenant` marcava o tenant
      numa cópia, o resto da aplicação lia outra. Nunca quebrou porque ninguém lia `atual()`
      fora de quem acabara de escrevê-lo — e as flags precisam ler.
- [x] **O catálogo era lido pela conexão do DONO.** "Para não depender de `tenants` ter ficado
      sem RLS" — argumento que não se sustenta, já que o `ResolverTenant` lê essas mesmas
      tabelas pela conexão da aplicação a cada requisição. E cobrava caro: uma escola criada
      pela aplicação era **invisível** ao smoke que deveria aprová-la. As duas conexões
      enxergavam mundos diferentes.
- [x] **O `finally` de `usar()` mascarava a exceção original.** Erro SQL aborta a transação; o
      `SET LOCAL` de restauro é o próximo comando, estoura com 25P02, e a sua exceção toma o
      lugar da real. Agora o caminho de erro não roda SQL nenhum — o rollback já desfaz o
      `SET LOCAL` —, e só a cópia em memória volta.
- [x] **`SmokeMultiTenantTest` semeava um estado impossível**: instituição ativa, sem domínio.
      O fixture foi corrigido, não a verificação.

### F. Quatro defeitos de isolamento, achados exercitando a Etapa E contra dados reais

Nenhum tinha teste. Três eram a mesma classe: uma restrição do banco que ignora `tenant_id`
enquanto o RLS a esconde. Ver `design.md §10`.

- [x] F.1 **`auditoria` não era tenant-scoped.** Ganhou `tenant_id` anulável, RLS e policy com
      `IS NOT DISTINCT FROM` — `NULL` significa "a plataforma", e é assim que a linha do
      operador (que não pertence a escola nenhuma) coexiste com as das escolas, sem que
      nenhuma veja a outra.
- [x] F.2 **`usuarios.email` era único global, e isso era explorável.** Sob RLS o registro não
      via a conta da outra escola, tentava inserir, e o índice a denunciava: **500 numa rota
      pública** e um oráculo de existência de contas entre instituições. Índice virou
      `(tenant_id, lower(email))`.
- [x] F.3 **`conquistas.codigo` era único global**, e por isso `arquivista_do_vazio` só podia
      existir em uma escola no banco inteiro. Índice virou `(tenant_id, codigo)`.
- [x] F.4 **A sessão não estava amarrada à instituição.** `sessions` não pode ter RLS (o
      `StartSession` roda antes do resolvedor). Impersonação não era possível — `usuarios.id` é
      PK global, e o RLS esconde o id da outra escola —, mas o estado da batalha (com o
      gabarito), o flash e o `intended` atravessavam com um `SESSION_DOMAIN` compartilhado. O
      `ResolverTenant` passou a marcar a sessão e a descartar a de outro tenant.
- [x] F.5 **`IntegridadeDaTenancyTest`**, a sentinela. Não lista tabelas: pergunta ao
      PostgreSQL quais têm `tenant_id` e exige RLS + `FORCE`, policy, FK para `tenants` e
      nenhum índice único que a ignore. Uma tabela nova sem `tenant_id` reprova até alguém
      justificá-la no próprio teste. Teria pego F.1, F.2 e F.3 no dia em que nasceram.

### G. O conteúdo da segunda instituição — o defeito que o runbook mandava provocar

`fases.id` é chave primária **global**, e o importador preserva os ids do legado (o progresso
que vem junto aponta para eles). Importar duas vezes colide em `fases_pkey` — e o `RUNBOOK
§10.6b` mandava rodar exatamente esse comando. O ensaio C.6 criou uma segunda instituição e
provou o *isolamento*, mas nunca tentou *semeá-la*.

**A resposta não foi o `content_packages`.** Uma escola nova não quer os alunos da primeira;
quer o conteúdo.

- [x] G.1 `arquivista_do_vazio` deixa de referenciar as fases 8/14/20/32 **por id** e passa a
      usar `tipo = 'secundaria'`. Corrige dois bugs: a conquista era inalcançável numa segunda
      escola, e uma quinta secundária criada pelo mestre não contava. No conteúdo do legado o
      comportamento é idêntico — há teste para isso.
- [x] G.2 `algorithmia:tenant:semear <para> --de=<origem>`: copia mestres, itens, conquistas,
      fases, desafios e diálogos com **ids novos**, reescrevendo as FKs, numa transação, com
      reconciliação por contagem. **Nenhuma pessoa é copiada.**
- [x] G.3 O `Smoke` verifica que as secundárias **existem**, e não que os ids do legado
      sobreviveram — uma escola semeada tem as quatro, com outros ids. A preservação dos ids do
      legado continua verificada onde importa: no `algorithmia:importar`.
- [x] G.4 `importar`, `tenant:novo` e a recusa de `tenant:ativar` passam a apontar o comando
      certo, que depende de já haver conteúdo no banco.
- [x] G.5 Verificado com o conteúdo real: 35 fases e 955 desafios copiados, **zero ids
      compartilhados**, 34 requisitos e 11 `item_drop_id` religados dentro da escola certa,
      zero contas copiadas, e **as duas escolas passando no smoke completo**.

Fica de fora, com proposta própria: conteúdo **diferente** por instituição (`content_packages`,
roteiro Fase 3). Hoje toda escola começa com uma cópia do mesmo mundo e o mestre a edita.
