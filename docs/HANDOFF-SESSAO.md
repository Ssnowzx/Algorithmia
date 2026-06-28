# Handoff de Sessão — Algorithmia

> Documento de transição para retomar o trabalho numa **sessão nova do zero**.

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
  `app/services/MaestriaService.php`, `tools/verificar_maestria.php` (32 asserts).
- **Missões da semana** (`feat/missoes-semanais` → merge `37704bd`). Painel "🎯 Missões da
  semana" no perfil: **3 metas rotativas/semana** (pool sorteado determinístico pela data),
  progresso na **semana ISO** corrente (`YEARWEEK(col,3)`), ✓ dourado ao concluir, "X/3
  concluídas". `config MISSOES_SEMANAIS`/`MISSOES_POR_SEMANA`, `MissaoService` (seleção/
  avaliação puras), `RespostaLog::metricasSemana` + `ProgressoFase::fasesSemana`,
  `tools/verificar_missoes.php` (33 asserts). **NÃO dá recompensa material** (de propósito:
  recompensa exigiria persistência/migration/economia).
- **Domínio das regiões** (`feat/dominio-regioes` → merge `3f94c40`). Painel "🏰 Domínio das
  Regiões" no perfil — maestria **HORIZONTAL**: perfeição da jornada por mestre, 4 estados
  (A explorar→Em jornada→Conquistada→**Dominada** = todas as fases com 3★), barra estrelas/máx,
  "X/5 dominadas" + título **"Mestre dos Cinco"** (5/5). `config REGIAO_FAIXAS`/
  `REGIAO_TITULO_LENDA`, `Mestre::progressoPorRegiao` (query parte de FASES → robusta à
  duplicação de `mestres`; só fases não-história), `RegiaoService`, `tools/verificar_regioes.php`
  (28 asserts). Ajuste posterior (`2070eef`): o painel conta só `tipo IN ('licao','chefe')`
  (secundárias opcionais), alinhando com o critério do jogo de "concluir região".
  *(As conquistas `mestre_X` NÃO estão órfãs — são concedidas ao derrotar o chefe em
  `BatalhaController::concederConquistaDeRegiao`; afirmar o contrário foi erro meu, já corrigido.)*
- **Onboarding "Primeiros passos"** (`feat/onboarding-primeiros-passos` → merge `edd2af6`).
  Painel de boas-vindas no topo do perfil (endowed progress) com o 1º marco JÁ feito ("Forjar
  seu herói") + 3 read-only (batalha/item/conquista); barra X/4; aparece só até
  `ONBOARDING_NIVEL_MAX=3` e some quando o novato evolui/completa. `OnboardingService` (montar
  pura + primeirosPassos defensivo), `Inventario::temEquipado`, `tools/verificar_onboarding.php`
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
- **Performance:** right-size de imagens (`tools/right_size_imagens.py`) ≈ **−46 MB servidos**, mesma arte; PNGs de itens movidos só p/ `docs/evolucao-visual`.

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
  `tools/`. **Doc canônica: [`docs/ARQUITETURA-IMAGENS.md`](ARQUITETURA-IMAGENS.md).**
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

**Assets de UI desta sessão:** `ui/molduras/ficha-fundo`, `ui/molduras/card-status-heroi`, `ui/molduras/moldura-status`, `herois/card-draconato`, `atores/npc-fragmento`. Há `tools/otimizar-imagens.sh` para WebP em lote (não toca pixel art).

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
- Arquitetura de imagens: **`docs/ARQUITETURA-IMAGENS.md`** (mapa de pastas +
  resolução slug→caminho). Estado de UI: `docs/STATUS-UI-ATUAL.md` e
  `openspec/changes/entrada-e-selecao-ux/`.
