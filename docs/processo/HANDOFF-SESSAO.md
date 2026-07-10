# Handoff de Sessão — Algorithmia

> Documento de transição para retomar o trabalho numa **sessão nova do zero**.

---

## 🗓️ Sessão 2026-07-10 — Etapa E: o piloto · branch `feature/etapa-e-piloto`

**A `fundacao-multitenant` está completa (A–E).** O corte em produção continua NÃO feito —
depende da VPS nova. **Esta sessão não fez deploy.**

8 commits, na branch `feature/etapa-e-piloto` (`855e135`), **não pushados**.

### Suítes

| | Antes | Agora |
|---|---|---|
| Port (`platform/`) | 269 | **377** |
| Legado (raiz) | 53 | 53 (intacto) |

`pint` e `phpstan` nível 6 limpos.

### O que foi feito

- **E.1 — flags por instituição.** Catálogo em `config/flags.php`; o banco (`tenants.flags`)
  guarda só as exceções. Chave desconhecida **levanta exceção** — um `false` calado desligaria
  a funcionalidade em produção para sempre. Cada flag é aplicada na **rota** (`flag:<chave>`,
  404) e no **menu** (`@flag`). `turmas` nasce OFF; `ranking`, ON.
- **E.2 — provisionamento com portão.** `algorithmia:tenant:novo|ativar|desativar|flag|listar`.
  `ativar` **roda o smoke da instituição antes de ligá-la** e recusa a injogável. Sem
  `--forcar`: o escape é um `UPDATE` à mão.
- **E.3 — o smoke vai até o relatório.** Resolução do host do `APP_URL`, domínio da
  instituição, persistência do progresso (com o `tenant_id` conferido) e o relatório de turma.
  Nada fica no banco, e há teste contando as linhas.
- **Console do operador** (pedido do usuário). `operadores` é catálogo global com guard
  próprio. Sem `CONSOLE_HOST`, **as rotas não são registradas**. Métricas cross-tenant **sem
  furar o RLS**: um contexto por instituição.

### Achados que valem mais que o código — todos corrigidos

Nenhum tinha teste. Todos vieram de exercitar a Etapa E contra um banco real.

1. **`usuarios.email` era único global, e explorável.** O RLS escondia a conta da outra escola,
   o `registrar` concluía "e-mail livre", o `INSERT` estourava: **500 numa rota pública**, e o
   visitante aprendia que o e-mail existe em outra instituição. Índice virou
   `(tenant_id, lower(email))`. Estava no `tasks.md` como "pergunta aberta, aceita" — não era
   consequência, era bug.
2. **`conquistas.codigo` era único global**: `arquivista_do_vazio` só podia existir numa escola.
3. **`auditoria` não era tenant-scoped.** Policy com `IS NOT DISTINCT FROM`: `NULL` significa
   "a plataforma", e é assim que a linha do operador convive com as das escolas.
4. **A sessão não estava amarrada à instituição.** Impersonação não era possível (PK global de
   `usuarios` protege por acidente), mas o estado da batalha — com o gabarito — atravessava com
   um `SESSION_DOMAIN` compartilhado.
5. **A segunda escola não podia ter conteúdo.** `fases.id` é PK global e o importador preserva
   os ids; o RUNBOOK §10.6b mandava rodar o comando que colide. Resolvido com
   `algorithmia:tenant:semear` (copia conteúdo, não pessoas, com ids novos) e com
   `arquivista_do_vazio` deixando de referenciar as fases por id.
6. **`ContextoDoTenant` não era singleton**; **o catálogo era lido pela conexão do dono** (uma
   escola criada pela aplicação era invisível ao smoke que devia aprová-la); e **o `finally` de
   `usar()` mascarava a exceção original** (25P02 no lugar do erro real).

**`IntegridadeDaTenancyTest`** é a sentinela que impede a próxima: não lista tabelas, pergunta
ao PostgreSQL quais têm `tenant_id` e exige RLS + FORCE, policy, FK e nenhum índice único que a
ignore. Teria pego 1, 2 e 3 no dia em que nasceram.

### Verificação

Além das suítes: um clone descartável do banco **pré-tenancy** (955 desafios reais) foi migrado
para frente pelas 11 migrations, e nele rodaram o smoke completo, o ciclo do piloto, o console
por HTTP (login, painel, recusa de ativação, flag, auditoria) e a **cópia de conteúdo para uma
segunda escola**: 35 fases e 955 desafios copiados, zero ids compartilhados, 34 requisitos e 11
`item_drop_id` religados dentro da escola certa, zero contas copiadas, e as duas escolas
passando no smoke completo. O banco de ensaio foi destruído depois; o stack de produção local
não foi tocado.

### Pendências

1. **Push e merge** da branch.
2. **O corte.** Quando a VPS existir: `bash bin/checar-host.sh`, depois `RUNBOOK §10`.
3. `content_packages` (roteiro Fase 3) — conteúdo **diferente** por instituição. Hoje toda
   escola começa com uma cópia do mesmo mundo, e o mestre dela o edita. Basta para o piloto.
4. Conta **global** de usuário (roteiro v1 §5) — identidade que atravessa instituições. Sem
   demanda. O bug de e-mail que se escondia atrás dessa "pergunta aberta" já foi corrigido.

---

## 🗓️ Sessão 2026-07-09/10 — Segurança, operação e multitenancy · na `main`

**O port é multitenant. O corte em produção continua NÃO feito** — depende de uma VPS nova.

33 commits, todos pushados (`c1067c9`). Cada um foi deployado e verificado contra o stack
local de produção, não só contra os testes.

### Suítes

| | Antes | Agora |
|---|---|---|
| Port (`platform/`) | 196 | **269** |
| Legado (raiz) | 38 | **53** (38 vetores-ouro + 15 de banco) |

`pint` e `phpstan` nível 6 limpos. Os 38 vetores-ouro seguem com **os mesmos números**.

### O que foi feito

**Fase 8 do roteiro v1 (segurança e operação):** rate limiting no login (dois limites — o
de e-mail+IP não vê o *password spraying*), CSP com nonce emitido pelo PHP (o nginx não
conhece o nonce), fontes da marca hospedadas por nós, trilha de auditoria e logs
estruturados com `request_id`.

**Fundação multitenant** (`openspec/changes/fundacao-multitenant/`), Etapas **A, B, C e D**:

- **A** — a aplicação deixa de conectar com o papel que ignora o RLS. Medido antes de
  escrever: `algorithmia` é superusuário, tem `rolbypassrls` **e** é dono das tabelas — três
  razões independentes para as policies serem inertes. Nasce `algorithmia_app`.
- **B** — `tenants`, `tenant_dominios`, `convites`; resolução por `Host`; contexto por
  `SET LOCAL` numa transação por requisição.
- **C** — `tenant_id` nas 13 tabelas do jogo, com um `DEFAULT` que lê o contexto: nenhuma
  linha do código de inserção mudou. **A ordem foi invertida em relação ao plano** — ver
  `design.md §4`: os três deploys são regra de coexistência, e antes do corte não há código
  velho no ar.
- **D** — turmas, matrículas, professor, `RelatorioDeTurma`, `TurmaPolicy`.

### Bugs que só apareceram jogando o jogo

Nenhum teste os pegaria. Ficam aqui porque voltam:

1. **`migrate.php` destruía a conta de administrador a cada deploy** — reescrevia a senha
   (para a que está publicada no repo) e apagava progresso, inventário e conquistas.
2. **O port tinha perdido as fontes da marca** em todas as telas menos a de lore.
3. **O `Authenticate` rodava antes do resolvedor de tenant.** O RLS o cegava, e o jogador
   logava para cair na tela de login. `actingAs()` não passa por sessão nem por HTTP. A
   correção não é reordenar o grupo `web` (o Laravel reordena pela lista de prioridade) —
   é `prependToPriorityList`, usando a **interface** `AuthenticatesRequests`.
4. **O smoke quebraria todo deploy a partir da segunda escola**, porque resolvia o "tenant
   único". Ele é o portão do `deploy.sh`.
5. **`/healthz` passou a devolver 500 em vez de 503** com o banco fora, até sair do resolvedor.

### O que fazer a seguir

1. **Etapa E** — piloto: feature flags por tenant, provisionamento documentado, smoke de
   resolução de tenant. É a última da proposta.
2. **O corte.** Quando a VPS existir: `bash bin/checar-host.sh`, e então `RUNBOOK §10`
   (VPS dedicada, rollback por DNS). O `§8` continua valendo para coexistência na mesma
   máquina. O `§10` já foi ensaiado ponta a ponta **com tenancy** — um ensaio sem RLS não
   provaria mais nada.
3. **Pergunta aberta**, registrada na migration `create_turmas`: `usuarios` é tenant-scoped,
   então **duas escolas não podem ter o mesmo e-mail**.

### Cuidados

- `platform/.env.producao` da máquina local tem valores de **ensaio**
  (`APP_URL=https://escola.ensaio.test:8443`, `LEGADO_DB_HOST=legado`). Na VPS, partir de
  `.env.producao.exemplo`.
- **O `APP_URL` precisa estar certo antes do primeiro `bin/deploy.sh`**: a migration cria o
  tenant padrão com o host dele. Errado → o site responde 404 com o banco cheio e o
  healthcheck verde.
- **Uma instituição nasce desligada.** Provisionar → semear → `UPDATE tenants SET ativo = true`.
- **`TRUNCATE` ignora RLS.** `algorithmia:importar --truncar` recusa rodar com mais de uma escola.

---

## 🗓️ Sessão 2026-07-08/09 — Migração para Laravel 13 + PostgreSQL 18 · na `main`

**O port está completo. O corte em produção NÃO foi feito.**

### O que existe agora

O repositório tem duas bases: o **legado** (raiz, PHP puro + MySQL, em produção) e o
**port** (`platform/`, Laravel 13 + PostgreSQL 18). Ambas com suíte verde: 38
vetores-ouro no legado, 196 testes no port. A CI roda as duas, mais um job que
constrói a imagem de produção.

Seis fases, todas na `main` (`7e516eb`..`55f3857`):

| Fase | O que entregou |
|---|---|
| 0 | 38 vetores-ouro sobre o **legado** — o contrato do motor |
| 1 | Laravel 13 + PostgreSQL 18; 13 tabelas traduzidas, com teste de cada tradução |
| 2 | Motor em `app/Dominio/`; recompensa virou idempotente por chave no banco |
| 3 | `algorithmia:importar` — 1.306 linhas, IDs preservados, `--dry-run` real |
| 4 + 4b | Camada web completa; toda escrita virou POST |
| 5 | Imagem, `bin/deploy.sh` com rollback automático, backup com ensaio, `algorithmia:smoke` |

### O que fazer a seguir

**Host confirmado:** VPS com root, rodando Docker (cPanel/RHEL com root — o `httpd` do
legado e os containers do port convivem na mesma máquina).

1. Criar `platform/.env.producao`. **Definir `TRUSTED_PROXIES`** com o IP do `httpd`:
   sem isso o cookie `secure` nunca é enviado e o jogador cai na tela de login para
   sempre. Ver [`RUNBOOK.md`](../operacao/RUNBOOK.md) §9.
2. Subir o port em porta alta (o `httpd` já é dono da 80) e verificá-lo pela porta,
   sem tocar no domínio.
3. Pôr o legado em somente leitura. `algorithmia:importar --dry-run`, depois sem a
   flag. `bin/deploy.sh`. `algorithmia:smoke`.
4. Trocar o vhost do `httpd` para proxy reverso. **Entrar no jogo você mesmo** — o
   smoke não testa a sessão atrás do proxy.
5. Deixar o legado de pé, em leitura, durante a janela de coexistência — **ele é o
   plano de rollback de verdade** nos primeiros dias.

### O que NÃO fazer

- **Não "ajuste" um vetor-ouro para fazer o port passar.** Os números foram derivados
  à mão das constantes. Se um falha, o port está errado.
- **Não conserte** o fato de `ProgressoFase::registrar` sobrescrever `usou_ia`.
  Rejogar limpo apaga a mancha da IA de propósito: é o que permite reconquistar
  "Puro de Coração". Redenção é regra do jogo.
- **Não cite `docs/auditoria/`** sem ler o código. É um retrato de 2026-06-18 e os
  dois débitos críticos que ela aponta já foram corrigidos.
- **Não rode `php database/migrate.php` com `DB_NAME` customizado.** O `schema.sql`
  tem `CREATE DATABASE algorithmia` e `USE algorithmia` fixos e ignora o ambiente.
- **Não suba os containers de produção à mão.** Use `bin/deploy.sh`.

### Armadilhas que custaram tempo (não repita)

**Do port:**
- `?Model $x = null` num controller: o Laravel injeta um model **vazio**, não `null`.
  Separe criar/editar.
- `GET /batalha/{fase}` engole `GET /batalha/responder` → `->whereNumber('fase')`.
- O skeleton traz `shouldRenderJsonWhen($request->is('api/*'))`. Rotas AJAX fora de
  `api/` recebem HTML no erro.
- `Auth::attempt` regrava a senha na coluna de `getAuthPasswordName()` (`password`).
- MySQL→PG: `YEARWEEK(x,3)` → `date_trunc('week',x)`; `SUM(bool)` → `COUNT(*) FILTER`;
  `FIELD(...)` → `array_position(...)`.

**Do deploy** (cinco defeitos que o ensaio expôs, todos corrigidos):
- `grep -q healthy` casa com **`unhealthy`**. O gate era decorativo.
- O `wget` do BusyBox resolve `localhost` como `::1`; o nginx só escuta IPv4.
- `set -o pipefail` + `| grep -q`: o grep fecha o cano, o produtor morre de SIGPIPE
  (141), o pipefail propaga. A condição nunca é verdadeira.
- O rollback automático saía sem esperar prontidão → 502 silencioso.
- `docker compose` interpola a imagem do serviço `app` mesmo em comandos que só tocam
  o postgres → `bin/backup.sh` no cron falharia.

E o `algorithmia:smoke` se pagou antes de existir produção: na primeira execução
denunciou que a migration `recompensas_batalha` nunca fora aplicada no banco de
desenvolvimento.

### Onde ler

[`docs/migracao/PLANO.md`](../migracao/PLANO.md) ·
[`docs/migracao/INVENTARIO.md`](../migracao/INVENTARIO.md) ·
[`docs/operacao/RUNBOOK.md`](../operacao/RUNBOOK.md) ·
[`openspec/changes/migracao-laravel-postgresql/`](../../openspec/changes/migracao-laravel-postgresql/)

> A tentativa anterior do Codex (73 commits com multitenancy e RLS) foi descartada e
> preservada nos refs `codex/fundacao-multitenant` e `validate-etapa-2-0e` (`44fe948`).
> Ela construiu exatamente o que a decisão de escopo removeu, e nunca portou o motor.

---

## 🗓️ Sessão 2026-06-28 (cont.) — Gamificação de MECÂNICA · na `main`

Quatro etapas de gamificação de **mecânica** (a apresentação já estava pronta), todas
**mergeadas e pushadas na `main`**, no padrão read-only que vinha dando certo: **sem
migration, sem cron, sem mexer em perguntas/economia/regra**. Cada uma com spec-driven
(`openspec/changes/…`, validada) + teste CLI puro (o projeto não tem PHPUnit).

- **Maestria por matéria** (`feat/maestria-por-materia` → merge `16fff06`). Evolui o painel
  "📚 Domínio por matéria" do perfil de % bruta → **faixas** (Não iniciado→Iniciante→Aprendiz
  →Praticante→Especialista→Mestre) por **volume de acertos + precisão sustentada** (2/2=100%
  não vira domínio); **barra goal-gradient** ao próximo selo (reflete o fator mais atrasado);
  resumo "X/8 dominadas". `config MAESTRIA_FAIXAS`/`MAESTRIA_TIER_DOMINADA`,
  `app/services/MaestriaService.php`, `tools/diagnostico/verificar_maestria.php` (32 asserts).
- **Missões da semana** (`feat/missoes-semanais` → merge `37704bd`). Painel "🎯 Missões da
  semana" no perfil: **3 metas rotativas/semana** (pool sorteado determinístico pela data),
  progresso na **semana ISO** corrente (`YEARWEEK(col,3)`), ✓ dourado ao concluir, "X/3
  concluídas". `config MISSOES_SEMANAIS`/`MISSOES_POR_SEMANA`, `MissaoService` (seleção/
  avaliação puras), `RespostaLog::metricasSemana` + `ProgressoFase::fasesSemana`,
  `tools/diagnostico/verificar_missoes.php` (33 asserts). **NÃO dá recompensa material** (de propósito:
  recompensa exigiria persistência/migration/economia).
- **Domínio das regiões** (`feat/dominio-regioes` → merge `3f94c40`). Painel "🏰 Domínio das
  Regiões" no perfil — maestria **HORIZONTAL**: perfeição da jornada por mestre, 4 estados
  (A explorar→Em jornada→Conquistada→**Dominada** = todas as fases com 3★), barra estrelas/máx,
  "X/5 dominadas" + título **"Mestre dos Cinco"** (5/5). `config REGIAO_FAIXAS`/
  `REGIAO_TITULO_LENDA`, `Mestre::progressoPorRegiao` (query parte de FASES → robusta à
  duplicação de `mestres`; só fases não-história), `RegiaoService`, `tools/diagnostico/verificar_regioes.php`
  (28 asserts). Ajuste posterior (`2070eef`): o painel conta só `tipo IN ('licao','chefe')`
  (secundárias opcionais), alinhando com o critério do jogo de "concluir região".
  *(As conquistas `mestre_X` NÃO estão órfãs — são concedidas ao derrotar o chefe em
  `BatalhaController::concederConquistaDeRegiao`; afirmar o contrário foi erro meu, já corrigido.)*
- **Onboarding "Primeiros passos"** (`feat/onboarding-primeiros-passos` → merge `edd2af6`).
  Painel de boas-vindas no topo do perfil (endowed progress) com o 1º marco JÁ feito ("Forjar
  seu herói") + 3 read-only (batalha/item/conquista); barra X/4; aparece só até
  `ONBOARDING_NIVEL_MAX=3` e some quando o novato evolui/completa. `OnboardingService` (montar
  pura + primeirosPassos defensivo), `Inventario::temEquipado`, `tools/diagnostico/verificar_onboarding.php`
  (14 asserts).

**Também nesta sessão (correções de dados/infra):**
- **Dedupe da tabela `mestres`** (`fix/dedupe-mestres` → merge `7ad5f03`): migration idempotente
  `database/migrations/20260628-dedupe-mestres.sql` (mantém menor id por `svg_slug`, repointa
  fases; 10→5) — resolve o único débito de dados que restava (era o mesmo seed-2× da loja).
- **`worker/` (ponte Telegram) versionado** (`a8a8206`): bridge, scripts e `.env.example`; trava
  anti-segredo confirmou `.env`/`.state.json`/logs fora do commit.

**Para retomar / pendências:**
- **Deploy:** as 4 features de gamificação + o dedupe de `mestres` NÃO publicados em produção
  (algorithmia.tars.art.br). O deploy precisa rodar `php database/migrate.php` — agora aplica o
  dedupe da loja (rodada anterior) **e** o dedupe de `mestres` (idempotentes, registrados).
- **Verificação dos painéis** exige dados na conta; foi feita injetando respostas/progresso de
  fases no personagem demo (pers5 = masterboss/Boss Explorer) e **revertendo** — banco intacto.
- **Próximas opções de mecânica:** ligas/cohorts (exigem cron/reset — bloqueio técnico real).
  Streak segue desaconselhado. (Onboarding e o dedupe de `mestres` já foram feitos nesta sessão.)
- Memória viva: `~/.claude/projects/-Users-snows-AntiGravity-TrabalhoWillen2/memory/auditoria-ux-andamento.md`.

---

## 🗓️ Sessão 2026-06-28 — Evolução de UX (apresentação) · na `main`

**PR #1** (15 commits) **mergeado** na `main` (`576c5f5`); **PR #2** (recap semanal) na sequência.
Spec-driven: change `openspec/changes/evolucao-ux-apresentacao/` (proposal/design/tasks/specs, validada).

**O que entrou — só apresentação/perf (nada de perguntas, batalha, XP, ouro, reputação/regra):**
- **Navegação:** rail (desktop ≥1000px) + barra inferior (mobile), top bar full-width, **View Transitions** entre páginas.
- **Design system:** tokens unificados, `:focus-visible`, `aria-current`, `color-mix` oklab, tokens de motion, `width/height` anti-CLS, dedup de fontes.
- **Perfil/gamificação:** medidor de Reputação (Disciplina↔Singularidade), progresso parcial das conquistas (X/Y), recap "Sua semana", stats em tiles, inventário em grade.
- **Mapa:** barra de progresso + chip "Próximo objetivo"; barras animadas; **confete** (conquista/compra/level-up, `public/js/celebracao.js`).
- **Loja:** dedupe de itens — migration idempotente `database/migrations/20260628-dedupe-itens.sql`.
- **Performance:** right-size de imagens (`tools/imagens/right_size_imagens.py`) ≈ **−46 MB servidos**, mesma arte; PNGs de itens movidos só p/ `docs/evolucao-visual`.

**Para retomar / pendências:**
- **Deploy:** ainda NÃO publicado em produção (algorithmia.tars.art.br). Ao deployar, rodar `php database/migrate.php` p/ aplicar a migration de dedupe da loja lá.
- As **animações** (View Transitions/confete/barras) só aparecem em browser **sem "reduzir movimento"**.
- **Próxima etapa** (já é gamificação de mecânica): ligas/cohorts no ranking (exige reset semanal/cron) ou streak (risco documentado de ansiedade — desaconselhado). Decidir explicitamente.
- Memória viva da rodada: `~/.claude/projects/-Users-snows-AntiGravity-TrabalhoWillen2/memory/auditoria-ux-andamento.md`.

---

## Sessão anterior (histórico)

**Branch:** `refactor/auditoria-qualidade-producao` (47 commits a partir de `main`).
**Status:** sem push nem PR (tudo local). **Há mudanças não commitadas** desta
sessão — a reorganização das imagens e a remoção da moldura do título (ver §1.7).

---

## 1. O que foi feito e aprovado

### 1.1 Segurança (auditoria de produção)
- **Scripts de banco** (`database/*.php`) só rodam via CLI (guarda `PHP_SAPI === 'cli'`); credenciais demo por env. *(ff080f4)*
- **`.htaccess`** bloqueia `database/`, `config/`, `app/`, dumps `.sql/.md/.sh/.py` e dotfiles; adiciona `Expires` + `DEFLATE` + `AddType image/webp`. *(369079f)*
- **Cookie de sessão** com `HttpOnly`/`SameSite=Lax`/`Secure` + headers `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`. *(513d854)*
- **Erro de banco** genérico em produção (detalhe só com `APP_ENV=dev`, sempre no `error_log`). *(32779cd)*
- **`Model`**: whitelist de coluna/`ORDER BY`. **`Router`**: só roteia métodos públicos (não os `protected` herdados). *(4d2951a)*

### 1.2 Performance
- **Ilustrações em WebP** (≈65 MB de PNG → ≈11 MB). Pixel art (`herois/heroi-*`, `inimigos/`, `itens/`) fica em PNG. Helper `srcImagem()` prefere `.webp`, versiona por `?v=filemtime` e memoiza. *(b06f6e2)*
- **`assetV()`**: cache-busting de **CSS e JS** (`?v=filemtime`) — mudanças aparecem sem hard-reload. *(d7cc6bf)*
- **`Auth::usuario()`/`personagem()`** memoizados por requisição. *(4e61b14)*
- **Splash** emitido 1× por sessão (depois removido — ver 1.4). *(88ec198)*

### 1.3 Bugs / refactor
- Fragmento da IA não é consumido sem batalha ativa. *(09a30a8)*
- Remove código morto (`ajustarVida`, stub `atualizarBotoesItens`). *(f38f2cf)*
- DRY: `REGIOES_MESTRE` (fundo + conquista por svg_slug). *(e154aa2)*

### 1.4 UI/UX — Entrada e seleção
- **Tela de início única**: removido o overlay `splash.php` (era duplicado); logout volta para a home limpa. *(faf77e6)*
- **Home** não fica branca: cena via `<img class="home-entrada-bg">` + cor de fallback; **sem trilha de fundo** (só efeitos de clique). *(843d127, 81d3fdd)*
- **Lore da classe** abre em **popup** (não quebra mais o grid). *(a1feace)*
- **Títulos** da criação na fonte do jogo (Pixelify) e centralizados. *(7c4b94c)*
- **Grid de classes responsivo** (`auto-fit` — 1 card no celular). *(a90eb17)*

### 1.5 UI/UX — Diálogo, itens, animações
- **Card de diálogo** premium (placa de nome na fonte do jogo, cor da região). *(339ca1a)*
- **Fragmento da IA** ilustrado no diálogo (`atores/npc-fragmento`, com fallback ao pixel). *(30c4a3a, 49c20e9)*
- **Itens interativos** na loja/inventário (hover, brilho por raridade). *(9ab032a)*
- **Personagens do diálogo animados** (entrada + aura). *(288550b)*
- Animações **sutis** liberadas sob `prefers-reduced-motion` (as fortes seguem desligadas). *(34820ab)*

### 1.6 Barra de topo, popup e cartas
- **Logo maior + menu gamificado** (Pixelify, hover). *(9246a0c)*
- **Status do topo clicável** → **popup da ficha do herói** (fecha por ✕/fora/Esc). *(1141e38)*
- **`ficha-fundo`** cobre o card inteiro do popup. *(1da520d, d9c6a40)*
- **Popup usa a `card-status-heroi`** (personagem no topo + stats nos slots medidos). *(11ecc6f)* — **aprovado: o card está correto.**
- **Avatar da barra e popup usam a MESMA carta** do herói (`$cardHeroi`). *(b977bcd)* — **aprovado.**
- **Vitrine do Perfil** com `moldura-status` (slots medidos: avatar 10.5%/50%, recursos 90.7%/37%/62%, nome+barras no miolo). *(3bfcebd)*

### 1.7 Arquitetura de imagens (reorg) + título do mapa
- **`public/img/ui/` subdividida** em `logos/`, `botoes/`, `molduras/`, `icones/`,
  `trofeus/` (raiz só com `splash-cena` e `placeholder`). A resolução por slug foi
  atualizada em `helpers.php` (`caminhoSvg`/`marcaHtml`) + views/CSS + geradores
  `tools/`. **Doc canônica: [`docs/arte/ARQUITETURA-IMAGENS.md`](../arte/ARQUITETURA-IMAGENS.md).**
- **Removidos (mortos):** `ui/logo.png`, `ui/logo-marca.png`,
  `inimigos/npc-anciao.png`, `inimigos/npc-narrador.png` (diálogos usam `atores/`).
- **Título do mapa LIMPO:** removida a moldura ornamental (`border-image` do
  `.mapa-cabecalho h1`) e o asset `ui/molduras/moldura-barra` — fica só o ícone do
  pergaminho + texto. **Resolve a antiga Seção 3.**

---

## 2. Pipeline de assets (importante para a sessão nova)

As imagens geradas no GPT vêm em **1536×1024, opacas, com xadrez de falsa-transparência** (ou fundo preto). O fluxo aplicado:
1. **Remover fundo** → alfa real:
   - Xadrez (claro/neutro): chave de cor global `min(r,g,b) ≥ 224 && (max-min) ≤ 20`.
   - Fundo preto: flood-fill das bordas (`max(r,g,b) ≤ 26`), preservando detalhes internos.
2. **Recortar** ao bbox do sujeito.
3. **Gerar `.webp`** otimizado (cwebp `-q 82..90`); o helper `srcImagem()` serve o webp.
4. **Slots/medidas** de molduras detectados por script Python (flood-fill dos furos / faixas opacas) e usados como `%` no CSS (container queries `cqw`).

**Assets de UI desta sessão:** `ui/molduras/ficha-fundo`, `ui/molduras/card-status-heroi`, `ui/molduras/moldura-status`, `herois/card-draconato`, `atores/npc-fragmento`. Há `tools/imagens/otimizar-imagens.sh` para WebP em lote (não toca pixel art).

---

## 3. Título do mapa — ✅ RESOLVIDO

Tela: **Mapa de Algorithmia** (`app/views/mapa/index.php` → `.mapa-cabecalho h1`).

- **Moldura ornamental removida** (a pedido do usuário): tirado o `border-image`
  com `moldura-barra` do `.mapa-cabecalho h1` e **apagado o asset**
  `ui/molduras/moldura-barra.png`/`.webp`. O título ficou **limpo** — só o ícone do
  pergaminho (`ui/icones/icone-mapa`) + texto.
- **Ícone do pergaminho:** renderiza limpo no último screenshot (sem o "quadrado
  escuro" que se via antes em 52px). Não há `background` no CSS
  (`.mapa-cabecalho h1 .icone-mapa-titulo` só tem width/height/object-fit/
  drop-shadow). Se reaparecer, é **cache do navegador** — `assetV()` versiona o
  CSS, então um reload resolve.
- **Barra de topo:** permanece **LIMPA**. O gate
  `is_file(ui/molduras/moldura-barra-fina.png)` religa uma moldura fina **se**
  existir uma arte de **bordas finas e uniformes** (proporção ~12:1).

---

## 4. Como continuar (sessão nova)
- Branch já tem tudo: `git checkout refactor/auditoria-qualidade-producao`.
- **Há mudanças não commitadas** desta sessão (reorg de imagens + título do mapa).
  Decidir com o usuário se commita (sugestão: um commit para a reorg de assets,
  outro para a remoção da moldura) e se faz **push/PR**.
- Arquitetura de imagens: **`docs/arte/ARQUITETURA-IMAGENS.md`** (mapa de pastas +
  resolução slug→caminho). Estado de UI: `docs/processo/STATUS-UI-ATUAL.md` e
  `openspec/changes/entrada-e-selecao-ux/`.
