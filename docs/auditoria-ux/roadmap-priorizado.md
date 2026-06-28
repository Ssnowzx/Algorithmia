# Roadmap priorizado — Auditoria de Produto (UX)

Todos os **46 achados** das 7 dimensões, agrupados por prioridade. Cada item traz dimensão, dificuldade e
impacto esperado em uma linha. A implementação no jogo vivo só ocorre **após aprovação do protótipo**
(`../../prototipo/`).

Legenda de risco: 🟢 **seguro/baixo-risco** (CSS/PHP aditivo, zero ou pouco diff visual) · 🟡 **muda
markup/layout** (risco moderado, validar no protótipo) · 🔴 **muda arquitetura/pipeline** (esforço alto,
depende de validação ou de outro item).

---

## Ordem de execução sugerida (pragmática)

ROI primeiro: quick wins seguros antes de estrutura, estrutura antes de arquitetura.

### Onda 0 — Quick wins de alto ROI (🟢 seguros, CSS/PHP, zero/baixo diff)
1. **Tokens alias unidirecional** (DS#1) — `--cor-X: var(--X)`, zero diff visual; destrava theming.
2. **Remover `@import` de fontes duplicado** (DS#3) — ganho de FCP medível, uma linha.
3. **`:focus-visible` global tokenizado** (Acess#1 + DS#4) — remove barreira de teclado no CTA e na nav.
4. **`aria-current="page"` no item de nav ativo** (Acess#2 + Nav#4) — orientação + leitor de tela.
5. **`width`/`height` intrínsecos nas imagens** (Perf#2 + Acess#5) — mata CLS com esforço mínimo.
6. **Conquistas órfãs ligadas ao gatilho** (Gam#3) — bug de design; reusa `conceder()` idempotente.
7. **`itemLendario` sob `no-preference` + `opacity`** (Motion#2) — respeita reduced-motion e tira paint.
8. **Catch-all canônico de `prefers-reduced-motion`** (Acess#4 + Motion#3) — rede de segurança 0.01ms.
9. **Alvos de toque `min-height:44px`** (Nav#2 + Acess#3) — menos mistaps no mobile.
10. **Escala de tokens de motion `--dur`/`--ease`** (Motion#1 + DS#7) — substitui timings hardcoded.
11. **`color-mix(in oklab)`** (DS#6) — acentos sem tons lavados (revisar contraste).
12. **Performance budget p75 documentado** (Perf#5) — guarda-corpo de QA, sem código.

### Onda 1 — Estrutura de médio impacto (🟡 muda markup/layout, validar no protótipo)
- **Barra de navegação inferior (mobile) / rail (desktop)** (Nav#1 + Fluxo#6) — partial único, dois renders por CSS.
- **Tirar ações (som/sair/painel) da nav** (Nav#3) — manter só os 5 destinos.
- **Medidor visual de Reputação + dotar onboarding** (Gam#1 + Fluxo#1 + Fluxo#2) — a maior oportunidade; nunca iniciar em 0% cru.
- **Objetivos de fase explícitos** (Fluxo#3) — pré-condição do goal-gradient.
- **Progresso saliente perto do fim** (Gam#2 + Fluxo#4) — "2 de 3 estrelas", barra por região.
- **View Transitions cross-document** (Motion#4) — PE puro-CSS, fallback grátis.
- **Unificar coreografia de modal** (Motion#5) — uma classe scale+fade compartilhada.
- **`components.css` + tirar `style=` inline** (DS#5) — camada de componentes.
- **`check-tokens.sh` + consolidar hex** (DS#2) — KPI de drift em pre-commit.
- **Generalizar `JUICE` para fora da arena** (Motion#6) — celebração no mapa/perfil/loja.
- **Migrar helper para `<picture>`** (Perf#3) — corrige imagem quebrada; pré-requisito do right-size.
- **`loading="eager"`/`fetchpriority` no LCP** (Perf#4) — telas iniciais "fecham" mais rápido.
- **`alt` significativo / `alt=""` decorativo** (Acess#7) — menos ruído de leitor de tela.
- **Contraste de texto derivado** (Acess#6) — auditar `--texto-fraco` com color-mix/opacity.
- **Primeira vitória rápida / reduzir carga inicial** (Fluxo#5) — time-to-first-win curto.
- **Cache em camadas + Brotli** (Perf#6) — visitas recorrentes mais rápidas (sem `immutable`).
- **CSS por rota + `<script defer>`** (Perf#7) — render inicial mais leve em telas simples.

### Onda 2 — Arquitetura / depende de validação (🔴 esforço alto ou medir antes)
- **Right-size offline das imagens** (Perf#1) — script `gerar_variantes.py`; depende de `<picture>` (Perf#3).
- **Ligas/cohorts no ranking** (Gam#4 + Nav#5) — pools de ~20–30, delta pessoal, opt-out; evidência correlacional.
- **Maestria horizontal pós-nível 10** (Gam#5) — 3★/modos difíceis/selos de mestre; decisão de design.
- **Streak opcional com perdão** (Gam#7) — só LEVE + opt-out, medindo prática real; efeito causal pequeno.
- **Guardrail de recompensa previsível / zero loot box** (Gam#6) — regra de produto preventiva.
- **Regra "motion nativo, só S-Tier"** (Motion#7) — guarda de arquitetura, sem libs.

---

## Prioridade ALTA

| # | Achado | Dimensão | Dificuldade | Impacto (1 linha) |
|---|--------|----------|-------------|-------------------|
| Nav#1 | Mobile sem padrão de navegação — topo só reflui | Navegação & IA | Média | 🟡 Destrava velocidade/descoberta na tarefa mais repetida no mobile (NN/g: ≥39% mais lento sem nav visível). |
| Nav#2 | Alvos de toque dos links não garantem 44px | Navegação & IA | Baixa | 🟢 Menos mistaps no polegar; só CSS. |
| Nav#3 | Faixa de nav mistura ações e passa de 3–5 destinos | Navegação & IA | Média | 🟡 Protege a memória muscular da troca de setor (lição Spotify); só 5 destinos. |
| DS#1 | Dois namespaces de token com literais duplicados | Design System & UI | Baixa | 🟢 Elimina risco de token drift; alias zero-diff habilita theming confiável. |
| DS#2 | Dezenas de cores cravadas fora de token | Design System & UI | Média | 🟡 KPI de drift barato (grep em pre-commit); previne regressões cromáticas. |
| DS#3 | `@import` de fontes no caminho crítico + duplicado | Design System & UI | Baixa | 🟢 FCP medível melhor no 1º acesso (caso real −33%); remove requisição extra. |
| Gam#1 | Reputação invisível como competência/identidade | Gamificação & Retenção | Média | 🟡 Torna saliente a competência-alvo; "maior oportunidade perdida" (feedback informacional, SDT). |
| Gam#2 | Progresso pouco saliente perto do fim + 0% cru | Gamificação & Retenção | Média | 🟡 Acelera conclusão perto da meta (goal-gradient ~18%); nunca iniciar em 0% cru. |
| Gam#3 | Conquistas órfãs: medalhas de mestre sem gatilho | Gamificação & Retenção | Baixa | 🟢 Conserta bug de design; restaura confiança no sistema de progressão. |
| Motion#1 | Tokens de motion existem mas não são usados | Motion & Microinterações | Média | 🟡 Movimento consistente e responsivo; substitui ~30 timings hardcoded. |
| Motion#2 | `itemLendario`: loop infinito de box-shadow | Motion & Microinterações | Baixa | 🟢 Respeita reduced-motion de verdade e tira paint contínuo na loja/inventário. |
| Motion#3 | `prefers-reduced-motion` espalhado em ~11 blocos | Motion & Microinterações | Média | 🟡 Cobertura robusta de reduced-motion (opt-in + catch-all); menos regressão. |
| Fluxo#1 | Reputação invisível e nunca explicada no início | Fluxo & Onboarding | Média | 🟡 Acende o gancho narrativo (Disciplina vs IA) quando deveria fisgar. |
| Fluxo#2 | Onboarding sem "progresso dotado" (0% cru) | Fluxo & Onboarding | Baixa | 🟢 Retenção do funil inicial (endowed progress, 34% vs 19% causal). |
| Fluxo#3 | Objetivos de cada fase não são claros | Fluxo & Onboarding | Média | 🟡 Dá meta percebível (pré-condição do goal-gradient); estrelas deixam de ser ruído. |
| Fluxo#4 | Progresso pouco saliente, sobretudo perto do fim | Fluxo & Onboarding | Baixa | 🟢 Mostra o "quase lá" (★★☆ 2/3, barra por região) que mais motiva. |
| Acess#1 | Foco visível ausente em quase todos os interativos | Acessibilidade | Baixa | 🟢 Remove barreira de teclado em toda a navegação e no mapa. |
| Acess#2 | `aria-current="page"` ausente no item de nav ativo | Acessibilidade | Baixa | 🟢 Leitor de tela anuncia "onde estou"; reforço visual de ativo. |
| Acess#3 | Alvos de toque abaixo de 44px na nav e botões | Acessibilidade | Baixa | 🟢 Reduz erros de toque no público mobile-first. |
| Acess#4 | `prefers-reduced-motion` fragmentado sem rede canônica | Acessibilidade | Baixa | 🟢 Respeito à preferência robusto por padrão (catch-all 0.01ms). |
| Perf#1 | Artes oversize servidas em slots minúsculos | Performance & Entrega | Alta | 🔴 Maior alavanca de LCP/peso; reduz abandono nas telas iniciais (mobile/3G). |
| Perf#2 | Imagens sem `width`/`height` causam CLS | Performance & Entrega | Baixa | 🟢 Elimina saltos de layout e cliques perdidos; CLS direto. |
| Perf#3 | `<img src>` único quebra sem WebP (migrar `<picture>`) | Performance & Entrega | Média | 🟡 Corrige imagem quebrada (bug real) e destrava AVIF + srcset. |
| Perf#4 | `loading="lazy"` aplicado a tudo, inclusive ao LCP | Performance & Entrega | Média | 🟡 LCP mais rápido (fetchpriority alto reduziu 2,6→1,9s em caso real). |

## Prioridade MÉDIA

| # | Achado | Dimensão | Dificuldade | Impacto (1 linha) |
|---|--------|----------|-------------|-------------------|
| Nav#4 | Sem indicação de "você está aqui" (estado ativo) | Navegação & IA | Baixa | 🟢 Âncora de orientação básica; reaproveita o sublinhado dourado para o ativo. |
| Nav#5 | Ranking 100% global, sem recorte por cohort | Navegação & IA | Média | 🟡 Cohorts pequenos rendem mais que ranking único; evidência correlacional, validar. |
| DS#4 | `.botao` (CTA primário) sem `:focus-visible` | Design System & UI | Baixa | 🟢 Foco visível no CTA mais usado; tokeniza o padrão para todo componente. |
| DS#5 | Estilos inline espalhados, sem camada de componentes | Design System & UI | Média | 🟡 Menos divergência entre telas; `components.css` aditivo (`@layer` opcional). |
| DS#6 | Acentos com `color-mix(in srgb)` em vez de OkLab | Design System & UI | Baixa | 🟢 Acentos por classe/região mais nítidos; revisar contraste das derivações. |
| DS#7 | Sem tokens de motion (`--dur`/`--ease`) | Design System & UI | Média | 🟡 Centraliza o "feel" do movimento; converge com Motion#1. |
| Gam#4 | Ranking global único → ligas de pares com delta | Gamificação & Retenção | Média | 🟡 Engajamento social sem expor o jogador fraco; ganhos das ligas são correlacionais. |
| Gam#5 | Maestria horizontal após o nível 10 | Gamificação & Retenção | Média | 🟡 Dá objetivo ao veterano (3★/selos de mestre); decisão de design, não causal. |
| Gam#6 | Núcleo de recompensa previsível + zero loot box | Gamificação & Retenção | Baixa | 🟢 Guardrail ético; evita overjustification e proíbe loot box/gacha/pay-to-win. |
| Motion#4 | Navegação corte seco — sem View Transitions | Motion & Microinterações | Baixa | 🟢 Continuidade tipo-app; PE puro-CSS que degrada para o comportamento atual. |
| Motion#5 | Três coreografias de modal divergentes | Motion & Microinterações | Média | 🟡 Coesão de produto; unifica em uma classe scale+fade. |
| Motion#6 | `JUICE` no-op fora de `.campo-batalha` | Motion & Microinterações | Média | 🟡 Celebração em conquista/compra/level-up fora do combate; reforço de progressão. |
| Fluxo#5 | Primeira vitória demora; carga cognitiva inicial alta | Fluxo & Onboarding | Média | 🟡 Time-to-first-win curto; reduz atrito antes do 1º reforço positivo. |
| Fluxo#6 | Falta de navegação persistente prejudica a jornada | Fluxo & Onboarding | Média | 🟡 Restaura o "fio condutor"; barra inferior persistente + View Transitions (converge com Nav#1). |
| Acess#5 | Imagens sem `width`/`height` → layout shift (CLS) | Acessibilidade | Média | 🟡 Estabilidade visual; reduz mistaps por reflow (converge com Perf#2). |
| Acess#6 | Contraste de texto derivado perto do limite AA | Acessibilidade | Baixa | 🟢 Legibilidade para baixa visão; medir cada uso de `--texto-fraco`. |
| Acess#7 | `alt` genérico/redundante nas imagens | Acessibilidade | Média | 🟡 Remove ruído real de áudio em leitor de tela (caminhos/nome repetido). |
| Perf#5 | Sem orçamento de performance p75 mobile | Performance & Entrega | Baixa | 🟢 Guarda-corpo que mantém a velocidade ganha (LCP≤2,5s, INP≤200ms, CLS≤0,1). |
| Perf#6 | Cache em camadas + Brotli (sem `immutable`) | Performance & Entrega | Média | 🟡 Visitas recorrentes mais rápidas e payload menor, sem servir versão velha. |

## Prioridade BAIXA

| # | Achado | Dimensão | Dificuldade | Impacto (1 linha) |
|---|--------|----------|-------------|-------------------|
| Gam#7 | Streak opcional ancorado em prática, com perdão | Gamificação & Retenção | Média | 🔴 Retenção marginal (efeito causal pequeno); só LEVE + perdão + opt-out, medindo prática real. |
| Motion#7 | Confirmar stack nativo; evitar props não-compositáveis | Motion & Microinterações | Baixa | 🟢 fps em mobile e guarda de arquitetura; preferir S-Tier, não adicionar libs. |
| Perf#7 | CSS global em vez de por rota + `<script>` sem `defer` | Performance & Entrega | Média | 🟡 Render inicial mais leve em telas simples; sem bloqueio de parser. |
