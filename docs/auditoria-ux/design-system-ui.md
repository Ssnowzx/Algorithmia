# Auditoria — Design System & UI

Esta dimensão cobre a fundação visual do Algorithmia: tokens de design, arquitetura de CSS sem build, tematização e estados de interação. O estado atual é um sistema funcional mas com **dois namespaces de token convivendo** — `style.css` (`:root` curto: `--bg`/`--primaria`, ~1472 linhas) e `tokens.css` (aditivo: `--cor-*` + tokens novos) — além de dezenas de hex cravados fora de token, estilos inline em views, ausência de tokens de motion e foco de teclado incompleto. Nada está quebrado, mas a base está pronta para consolidar antes de escalar a arte e o theming. As recomendações abaixo são todas viáveis em PHP MVC server-rendered, sem build e sem novas dependências.

---

### 1. Dois namespaces de token com literais duplicados (sem fonte única de verdade)
- **Descrição:** O mesmo valor conceitual é declarado como literal em dois arquivos: `style.css:9` define `--bg: #0b0c1d` e `tokens.css:21` redeclara `--cor-bg: #0b0c1d` (mesma duplicação em painel/borda/texto/primária — 30 hex literais em `tokens.css` espelhando o `:root` de `style.css`). Hoje `var(--cor-bg)` é consumido **zero vezes** no projeto (grep em `public/` e `app/` retorna 0), então `--cor-*` é um alias dormente de documentação — mas mantido como literal, ele é um ponto de divergência latente: mudar `--bg` sem mudar `--cor-bg` faz os dois sentidos do "mesmo" token divergirem silenciosamente.
- **Impacto na UX:** Indireto hoje (alias não consumido), porém é o risco-mestre que sabota qualquer evolução futura de cor/tema: bugs visuais sutis (um componente novo pega `--cor-bg` desatualizado), inconsistência entre telas e retrabalho. Bloqueia tematização confiável.
- **Evidência / boa prática:** [Fato validado] O risco real não é ordem de carregamento, é **definição duplicada do mesmo token** — se dois arquivos dão valor literal ao mesmo conceito, o último carregado vence silenciosamente. [Fato validado] Aliasar (`--cor-bg: var(--bg)`) resolve em *computed-value time*, é seguro independente da ordem dos `<link>` e tem **zero diff visual**. [Fato validado] O alias deve ser **estritamente unidirecional** (alias→canônico); um ciclo (`--bg: var(--cor-bg)` + `--cor-bg: var(--bg)`) invalida ambos. [Consenso, com ressalva 1/3] é "token drift" — aqui ainda latente, não consumado, porque os tokens vivos estão em `style.css`.
- **Referência:** pesquisa/04-design-tokens-css.md (Eixos 2 e 5) · <https://www.w3.org/TR/css-variables-1/> · <https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_cascading_variables>
- **Solução proposta:** Eleger o namespace **curto como canônico** (`--bg`/`--primaria`, onde vivem os tokens). Em `tokens.css`, trocar cada literal `--cor-X: #hex` por **alias unidirecional** `--cor-X: var(--X)` (ex.: `--cor-bg: var(--bg)`) e marcar o bloco como `deprecated` em comentário. Documentar no topo de `tokens.css` uma tabela "canônico → propósito → alias deprecated". Critério de remoção do alias: quando os call-sites `--cor-*` chegarem a zero (grep confirma que já é ~zero hoje). Sem renomeação em massa, sem diff visual.
- **Prioridade:** Alta
- **Dificuldade:** Baixa
- **Impacto esperado:** Principalmente manutenibilidade/DX e prevenção de bugs visuais futuros; habilita tematização confiável. Impacto direto no jogador é indireto (consistência), mas é o pré-requisito barato das demais melhorias.

---

### 2. Dezenas de cores cravadas fora de token (drift sem KPI de aderência)
- **Descrição:** Há cor hardcoded espalhada nas folhas de estilo em vez de referenciar tokens: `grep -oE '#[0-9a-fA-F]{3,8}'` encontra ~159 ocorrências de hex fora de `tokens.css` (99 em `style.css`, 41 em `mapa.css`, 19 em `batalha.css`), além de literais dentro de botões (`style.css:498` `#ff8585`, `style.css:499` `#ffe27a`/`#4a3b00`) e fallbacks repetidos como `var(--cor-regiao, #7c5cff)` em `mapa.css`. Não existe nenhum mecanismo (lint/grep) que meça essa aderência — o drift cresce sem alarme.
- **Impacto na UX:** Inconsistência cromática entre telas e dificuldade de manter contraste/identidade quando a paleta evolui; um ajuste de marca exige caça manual a literais, aumentando a chance de telas "fora do tom".
- **Evidência / boa prática:** [Boa prática] A aderência ao design system se **mede com lint/grep de hex hardcoded** — é o KPI de drift mais barato e, sem build, cabe num grep `#[0-9a-f]{3,6}|rgb\(|hsl\(` em git pre-commit/CI. [Boa prática] Não criar tokens demais cedo; o Algorithmia já tem o conjunto certo — o trabalho é **consolidar** os literais nos tokens existentes.
- **Referência:** pesquisa/04-design-tokens-css.md (Eixo 5) · <https://designtokens.substack.com/p/common-mistakes-in-design-tokens> · <https://www.nngroup.com/articles/lean-design-system-teams/>
- **Solução proposta:** (1) Substituir literais por tokens existentes onde houver equivalência exata (ex.: `#0b0c1d`→`var(--bg)`); manter cravado só o que é genuinamente único (stops de gradiente, rgba com alfa) e, se recorrente, promovê-lo a token. (2) Adicionar um script `tools/check-tokens.sh` (grep de `#hex`/`rgb(`/`hsl(` fora de `tokens.css`+`style.css :root`) e plugá-lo num git pre-commit; commitar a contagem-base (~159) como linha de regressão para que o número só caia.
- **Prioridade:** Alta
- **Dificuldade:** Média
- **Impacto esperado:** Usabilidade/consistência visual a médio prazo; principal ganho é de manutenibilidade e prevenção de regressões cromáticas. KPI honesto e barato, sem toolchain.

---

### 3. `@import` de fontes no caminho crítico + carga duplicada
- **Descrição:** `style.css:6` faz `@import url('https://fonts.googleapis.com/...Pixelify+Sans...Rubik...')` **dentro do CSS**, e `app/views/layout/header.php:26` carrega exatamente as mesmas famílias via `<link rel="stylesheet">`. Ou seja, as fontes são pedidas duas vezes e, pior, via `@import` encadeado no caminho crítico de renderização.
- **Impacto na UX:** Atraso de First Contentful Paint e risco maior de FOUT/FOIT (texto pulando) na primeira carga — sentido sobretudo em mobile e redes lentas, exatamente o público-alvo do jogo. A duplicata desperdiça uma requisição.
- **Evidência / boa prática:** [Fato validado] Dividir CSS em `<link>` paralelos, **nunca encadear via `@import`** — `@import` força downloads sequenciais no caminho crítico; caso real mediu FCP P80 mobile caindo **2782ms→1872ms (~33%)** ao remover `@import`. As custom properties resolvem em computed-value time, então a ordem entre `<link>`s já não importa para os tokens.
- **Referência:** pesquisa/04-design-tokens-css.md (Eixo 3) · <https://csswizardry.com/2018/11/css-and-network-performance/> · <https://www.debugbear.com/blog/avoid-css-import>
- **Solução proposta:** Remover a linha `@import` de `style.css:6` e manter **apenas** o `<link>` de fontes no `header.php` (de preferência com `preconnect` para `fonts.gstatic.com` e `display=swap`, já presente). Os CSS do projeto já são carregados como `<link>`s paralelos no header (linhas 27-31) — manter assim, sem `@import` entre eles.
- **Prioridade:** Alta
- **Dificuldade:** Baixa
- **Impacto esperado:** Usabilidade/percepção de velocidade no primeiro acesso (mobile-first); correção pontual e barata com ganho de FCP mensurável.

---

### 4. `.botao` (CTA primário) sem `:focus-visible` — token de foco não reutilizado
- **Descrição:** O único anel de foco do projeto está em `style.css:232` (`.hud-placa-click:focus-visible { outline: 2px solid var(--hud-cor, var(--primaria)); ... }`). O botão principal `.botao` (`style.css:488`) só define `:hover` (`style.css:495`) e `:disabled` (`style.css:496`) — não há regra `:focus-visible`. Como o padrão também não está fatorado num token de foco, a inconsistência se repetirá em todo componente novo.
- **Impacto na UX:** Navegação por teclado fica sem indicação visível de onde está o foco no CTA mais usado do jogo (continuar fase, confirmar, comprar) — prejudica quem joga no desktop por teclado e usuários de tecnologia assistiva, e quebra a consistência de estados de interação do design system.
- **Evidência / boa prática:** [Consenso de mercado] Apelidos/estados devem apontar para um token semântico único; estados de interação fazem parte do design system e precisam ser consistentes entre componentes (o foco existe num componente e falta no principal = inconsistência). [Boa prática] Indicador de foco visível é estado de interação básico; reaproveitar o padrão de `.hud-placa-click` evita um *snowflake* por componente.
- **Referência:** pesquisa/04-design-tokens-css.md (Eixos 4 e 5 — derivação/consistência de estados) · <https://www.netguru.com/blog/design-token-naming-best-practices> · <https://www.nngroup.com/articles/lean-design-system-teams/>
- **Solução proposta:** Criar tokens de foco no `:root` (`--foco-cor: var(--primaria-2)`, `--foco-anel: 2px`, `--foco-offset: 2px`) e aplicar uma regra única `.botao:focus-visible { outline: var(--foco-anel) solid var(--hud-cor, var(--foco-cor)); outline-offset: var(--foco-offset); }`, reusando o mesmo token em `.hud-placa-click` e demais alvos clicáveis. Como o foco respeita `--hud-cor`, o anel já herda a cor da classe/região automaticamente.
- **Prioridade:** Média
- **Dificuldade:** Baixa
- **Impacto esperado:** Usabilidade/acessibilidade para jogadores de teclado; consistência de estados no design system. Baixo custo, ganho qualitativo claro.

---

### 5. Estilos inline espalhados, sem camada de componentes
- **Descrição:** Regras visuais vivem hardcoded no markup: `app/views/perfil/index.php` tem 18 atributos `style=` (ex.: linhas 29, 37-38 com `display:flex`, `border-color`, grids) e `app/views/mestre/index.php` tem 10 (ex.: `painel` com `text-align:center` e `font-size:2rem` repetidos, linhas 5-10). Não existe um `components.css` para os blocos reutilizáveis (cards `.carta-loja`, painéis de stats), nem uma ordenação formal de camadas (`@layer`) — a especificidade é controlada por convenção implícita (ITCSS informal).
- **Impacto na UX:** Inconsistência entre instâncias do "mesmo" componente (cada inline diverge um pouco), HTML mais pesado e mudanças visuais que exigem editar PHP em vários lugares — terreno fértil para telas fora do padrão.
- **Evidência / boa prática:** [Consenso de mercado] ITCSS mapeia ~1:1 em cascade layers (`@layer reset, tokens, base, components, regions, utilities`) e doma especificidade sem tooling. [Boa prática] Para o arquivo de componentes, nomes completos estilo BEM (à prova de colisão) + variações por `data-attribute`. ⚠️ [REFUTADO 3/3] `@layer` **não degrada graciosamente**: em motor sem suporte, todo o conteúdo dentro de `@layer{}` é **descartado** (página sem estilo) — adotar `@layer` é seguro hoje só porque o suporte é universal há 4+ anos (~96%+, Baseline mar/2022), **não** por fallback.
- **Referência:** pesquisa/04-design-tokens-css.md (Eixo 3) · <https://css-tricks.com/css-cascade-layers/> · <https://caniuse.com/css-cascade-layers> · <https://cube.fyi/>
- **Solução proposta:** Criar `public/css/components.css` aditivo, carregado **após** `style.css` (novo `<link>` paralelo no `header.php`), extraindo os blocos repetidos (`.painel--stat`, `.carta-loja`, vitrine do perfil) com classes nomeadas; substituir os `style=` inline por essas classes. `@layer` ITCSS é desejável e mapeia o ITCSS informal atual, mas adotar **só** depois de confirmar (analytics do público) que não há navegadores sem suporte — caso haja, manter o CSS crítico fora de camadas.
- **Prioridade:** Média
- **Dificuldade:** Média
- **Impacto esperado:** Manutenibilidade e consistência visual; ganho de UX indireto via menos divergência entre telas. `@layer` é enhancement opcional, não bloqueante.

---

### 6. Acentos derivados com `color-mix(in srgb)` em vez de OkLab
- **Descrição:** O projeto já deriva acentos a partir de uma cor-base (ótimo), mas todas as 110 ocorrências de `color-mix` usam o espaço **sRGB** — ex.: `mapa.css:30/43/45/110/208` (`color-mix(in srgb, var(--hud-cor/--cor-regiao) X%, ...)`). Misturar em sRGB tende a produzir tons "lavados"/sujos no meio do gradiente de cor, com contraste menos previsível entre as classes/regiões.
- **Impacto na UX:** Bordas, brilhos e superfícies tematizadas por classe/região podem sair com luminosidade inconsistente, enfraquecendo a leitura visual da identidade de cada mestre/bioma — efeito sutil mas percebido como "acabamento".
- **Evidência / boa prática:** [Fato validado] Para escalar acentos sem explosão de tokens, derivar variações (hover/borda/alpha) de uma cor-base com `color-mix()` (Baseline 2023, seguro). [Consenso de mercado] **Interpolar em OkLab/OkLCH** (`color-mix(in oklab, …)`), não sRGB/HSL — evita tons lavados e mantém contraste previsível. A tematização escopada por `--hud-cor`/`--cor-regiao` já está correta; falta só o espaço de interpolação.
- **Referência:** pesquisa/04-design-tokens-css.md (Eixo 4) · <https://developer.mozilla.org/en-US/blog/color-palettes-css-color-mix/> · <https://caniuse.com/css-color-mix>
- **Solução proposta:** Trocar `in srgb` por `in oklab` nas misturas de acento (busca/substituição direta nos ~110 call-sites de `color-mix`). Validar **contraste WCAG** caso a caso nas cores derivadas (a mudança de espaço altera ligeiramente a luminância). Manter `color-mix` (Baseline 2023) em vez de *relative color syntax* por compatibilidade.
- **Prioridade:** Média
- **Dificuldade:** Baixa
- **Impacto esperado:** Polimento visual/engajamento (identidade de cor mais nítida por classe/região); risco baixo desde que se revise contraste das derivações.

---

### 7. Sem tokens de motion (`--dur`/`--ease`) — timing de animação cravado e disperso
- **Descrição:** As durações e curvas de animação estão hardcoded por arquivo, sem nenhum token (`grep '--dur'/'--ease'` retorna 0). O `reduced-motion` já é respeitado (`batalha.css:66/110/248`, `mapa.css`, `style.css:1274`), porém no padrão "ligado por default, desligado em `reduce`" — que a pesquisa aponta como mais frágil que o opt-in. Sem tokens centrais, cada animação escolhe seu próprio tempo/curva e o "feel" do jogo fica inconsistente.
- **Impacto na UX:** Microinterações com ritmos divergentes (uns lentos, outros bruscos) reduzem a sensação de polimento; e ajustar a "personalidade" do motion exige editar muitos lugares. O padrão disable-on-`reduce` pode, em transforms, deixar elementos em estado final invisível.
- **Evidência / boa prática:** [Boa prática] Definir tokens de duração/easing no `:root` espelhando Material 3 enxuto: microfeedback ~**100ms**, UI padrão **200–300ms**, transições de tela **300–400ms** (teto ~500ms), com `--ease-out: cubic-bezier(0.2,0,0,1)` para entradas. Meta dura **<400ms** (Doherty) por ação iniciada pelo jogador. [Consenso de mercado] Padrão **opt-in** (`@media (prefers-reduced-motion: no-preference)` para *ligar* o movimento) é mais seguro que "nuke"/disable, evita o bug do elemento invisível pós-transform e mantém fade/cor sempre on (fora do escopo da WCAG 2.3.3).
- **Referência:** pesquisa/02-timing-motion-libs-casos.md (Alvos 1 e 2) · <https://www.nngroup.com/articles/animation-duration/> · <https://www.w3.org/WAI/WCAG22/Techniques/css/C39>
- **Solução proposta:** Declarar no `:root` de `style.css` um conjunto enxuto de tokens (`--dur-rapido: 100ms; --dur-padrao: 240ms; --dur-tela: 360ms; --ease-out: cubic-bezier(.2,0,0,1); --ease-in: cubic-bezier(.3,0,1,1);`) e repontar as animações existentes para eles. Onde houver transform significativo (slides de combate, parallax do mapa), migrar gradualmente para o padrão opt-in (`no-preference`) mantendo fade/cor sempre ativos. 0 KB, puro CSS.
- **Prioridade:** Média
- **Dificuldade:** Média
- **Impacto esperado:** Engajamento/polimento (motion coeso e dentro do limiar de Doherty) e acessibilidade vestibular; ganho qualitativo, sem dependências.
