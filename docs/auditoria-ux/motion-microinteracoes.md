# Auditoria — Motion & Microinteracoes

Esta dimensao cobre o vocabulario de movimento do jogo: tokens de duracao/easing, transicoes entre telas, coreografia de modais, feedback de "juice" e acessibilidade de movimento (`prefers-reduced-motion`). O estado atual e fragmentado: `tokens.css` define aliases de transicao que **nenhum** componente usa (os timings sao hardcoded em ~30 lugares: 15x `.15s`, 7x `.18s`, etc.), a navegacao entre paginas e corte seco (zero View Transitions), existem 3 coreografias divergentes de modal, o `prefers-reduced-motion` esta espalhado em ~11 blocos com duplicatas e uma animacao infinita que nunca desliga, e o `JUICE` (particulas/shake) e no-op fora de `.campo-batalha`. As bases (`juice.js`/`som.js` ja honram reduced-motion; stack nativo sem build) sao boas — falta padronizar.

---

### 1. Tokens de motion existem mas nao sao usados; faltam escala de duracao e curvas de easing
- **Descricao:** `public/css/tokens.css:206-209` define apenas 4 aliases (`--transicao-rapida: .12s ease`, `--transicao-padrao: .18s ease`, `--transicao-suave: .25s ease`, `--transicao-modal: .22s ease`), todos com easing `ease` generico e nenhum deles referenciado pelos componentes. Na pratica os timings sao hardcoded e divergentes (em `style.css`: 15x `.15s`, 7x `.18s`, 4x `.2s`, alem de `.22s`, `.25s`, `.26s`, `.35s`, `.5s`). Nao ha tokens de **easing** (ease-out/ease-in) nem uma escala semantica de duracao por categoria de interacao.
- **Impacto na UX:** movimento inconsistente entre telas (cada componente "inventa" seu tempo/curva), sensacao menos responsiva e maior custo de manutencao; sem ease-out as entradas nao transmitem a sensacao de resposta imediata que o usuario espera.
- **Evidencia / boa pratica:** [Fato validado] Tokens de duracao do Material 3 (Short 50-200ms / Medium 250-400ms / Long 450-600ms) e curvas (Standard `cubic-bezier(0.2,0,0,1)`). [Fato validado, com ressalva] Doherty Threshold = <400ms (produtividade dispara abaixo disso). [Boa prática] NN/g: feedback simples ~100ms, mudancas substanciais 200-300ms, faixa util 100-400ms (400ms ja e "lenta"); ease-out para entradas, ease-in para saidas, evitar linear.
- **Referencia:** `pesquisa/02-timing-motion-libs-casos.md` (Alvo 1) — <https://m3.material.io/styles/motion/easing-and-duration/tokens-specs> · <https://www.nngroup.com/articles/animation-duration/>
- **Solucao proposta:** em `tokens.css :root`, criar a escala canonica enxuta: `--dur-micro: 100ms; --dur-ui: 240ms; --dur-tela: 360ms` (teto ~500ms para encadeamentos), `--ease-out: cubic-bezier(0.2,0,0,1)` (entradas) e `--ease-in: cubic-bezier(0.3,0,1,1)` (saidas). Substituir os valores hardcoded de `style.css`/`batalha.css`/`mapa.css` por esses tokens. Puro CSS, 0 KB, sem build.
- **Prioridade:** Alta
- **Dificuldade:** Media
- **Impacto esperado:** usabilidade e percepcao de qualidade (responsividade consistente); manutencao. Ganho qualitativo — os limiares de tempo sao validados, mas o efeito de retencao e indireto.

---

### 2. `itemLendario`: loop infinito de box-shadow nunca desligado e anima propriedade nao-compositavel
- **Descricao:** `public/css/style.css:983-986` define `@keyframes itemLendario` e `:1068-1070` aplica `animation: itemLendario 2.2s ease-in-out infinite` nas cartas lendarias. O comentario em `style.css:1104-1106` declara explicitamente que as animacoes de item "tocam mesmo com prefers-reduced-motion" — ou seja, esse loop infinito **nunca** e desligado. Alem disso ele anima `box-shadow`, que forca paint (fora do compositor).
- **Impacto na UX:** movimento perpetuo na tela para quem pediu reduzir movimento (potencial gatilho vestibular e distracao), e custo de paint continuo numa grade de cartas — pior em mobile/low-end.
- **Evidencia / boa pratica:** [Fato validado] WCAG 2.2.2 trata animacao auto-iniciada (>5s) como passivel de pausa; o consenso de reduced-motion e desligar movimento e manter fade/cor. [Boa prática] tier list de propriedades: `box-shadow`/`background-color` sao C-Tier (forcam paint); `transform`/`opacity`/`filter` sao S-Tier (so compositor). [Fato validado] mudanca pura de opacidade/cor esta fora do escopo de "motion animation" (2.3.3).
- **Referencia:** `pesquisa/02-timing-motion-libs-casos.md` (Alvos 2 e 3) — <https://www.w3.org/WAI/WCAG22/Techniques/css/C39> · <https://motion.dev/magazine/web-animation-performance-tier-list>
- **Solucao proposta:** mover o `animation: itemLendario ... infinite` para dentro de `@media (prefers-reduced-motion: no-preference)` (sob `reduce` fica so a borda/glow dourado estatico). Trocar o pulso de `box-shadow` por um pseudo-elemento `::after` animando `opacity` (S-Tier) para o mesmo efeito sem forcar paint.
- **Prioridade:** Alta
- **Dificuldade:** Baixa
- **Impacto esperado:** acessibilidade (conformidade real com reduced-motion) e desempenho na loja/inventario. Ganho concreto e verificavel.

---

### 3. `prefers-reduced-motion` espalhado em ~11 blocos com duplicatas; falta um bloco canonico opt-in
- **Descricao:** existem ~11 blocos `@media (prefers-reduced-motion: reduce)` espalhados: `style.css:1274,1281,1422`, `batalha.css:66,110,248`, `mapa.css:144,163,455,542`, `cena.css:81`. Ha **duplicata adjacente** em `style.css:1274` e `1281` (dois blocos `reduce` colados). A abordagem e sempre "desligar caso a caso" (frageis), e itens como o `itemLendario` (achado 2) escapam da rede. Nao ha um bloco canonico nem um catch-all de seguranca.
- **Impacto na UX:** cobertura incompleta e inconsistente — alguns movimentos persistem para quem pediu reduzir; manutencao dificil (toda nova animacao precisa lembrar de criar seu proprio opt-out, e alguem sempre esquece).
- **Evidencia / boa pratica:** [Fato validado] Tecnica C39: usar `@media (prefers-reduced-motion: no-preference){}` para **so ligar** movimento (opt-in) ou `reduce{}` para desligar. [Consenso de mercado] opt-in (base sem movimento) e mais seguro que "nuke" — evita o bug do elemento invisivel quando um transform termina antes do render. [Boa prática] catch-all com `0.01ms !important` + `animation-iteration-count: 1 !important` como 2a linha de defesa; manter fade/cor.
- **Referencia:** `pesquisa/02-timing-motion-libs-casos.md` (Alvo 2) — <https://web.dev/articles/prefers-reduced-motion> · <https://css-tricks.com/nuking-motion-with-prefers-reduced-motion/>
- **Solucao proposta:** consolidar em UM padrao: (a) embrulhar todo movimento significativo (slides de combate, parallax/ambiente do mapa, ken-burns do splash, glow infinito) em `@media (prefers-reduced-motion: no-preference)`; (b) manter fade/cor sempre ligados; (c) um unico catch-all global `@media (reduce)` com `0.01ms !important` + `iteration-count: 1` como rede de seguranca. Remover a duplicata `style.css:1274/1281`.
- **Prioridade:** Alta
- **Dificuldade:** Media
- **Impacto esperado:** acessibilidade (AAA 2.3.3) e manutencao; reduz risco de regressao. Honesto: e correcao de robustez, nao alavanca de engajamento.

---

### 4. Navegacao entre paginas e corte seco — sem View Transitions cross-document
- **Descricao:** grep por `@view-transition`/`view-transition-name`/`startViewTransition` em `public/` e `app/` retorna vazio. Sendo um app PHP MVC com full-reload, cada navegacao (mapa -> batalha -> loja) e um corte seco; a shell persistente (`<header class="topo">` em `app/views/layout/header.php:35`) e redesenhada do zero a cada carga.
- **Impacto na UX:** quebra de continuidade espacial entre telas; sensacao de "site antigo" em vez de fluxo tipo-app, especialmente no mobile.
- **Evidencia / boa pratica:** [Fato validado] View Transitions cross-document sao opt-in **so-CSS** (`@view-transition { navigation: auto; }`), zero JS, same-origin; navegadores sem suporte ignoram o opt-in e fazem corte seco (fallback gratuito). Same-document virou Baseline em out/2025; cross-document = Chrome/Edge 126+ e Safari 18.2+ (Firefox ainda atrasado) -> tratar como progressive enhancement. Animar `transform`/`opacity` (snapshots vem do compositor).
- **Referencia:** `pesquisa/01-navegacao-e-motion.md` (B1, B2, B4) — <https://developer.chrome.com/docs/web-platform/view-transitions/cross-document> · <https://developer.mozilla.org/en-US/docs/Web/CSS/@view-transition>
- **Solucao proposta:** adicionar `@view-transition { navigation: auto; }` em `tokens.css`/`style.css` e dar `view-transition-name` ao shell persistente (`.topo` e a barra de navegacao), com a animacao baseada nos tokens do achado 1. Manter logica independente de "types" (Firefox nao suporta). Caveat da pesquisa: **nao** nomear regioes pesadas (arena) sem medir FCP/INP.
- **Prioridade:** Media
- **Dificuldade:** Baixa
- **Impacto esperado:** engajamento e percepcao de polimento; risco baixo (PE puro, degrada para o comportamento atual).

---

### 5. Tres coreografias de modal divergentes (transition vs keyframes, beziers diferentes)
- **Descricao:** ha 3 linguagens de abertura de modal: (a) `.modal-overlay` (`style.css:433-447`) usa transition `transform: translateY(16px) scale(.96)` -> `scale(1)` em `.26s cubic-bezier(.2,.9,.3,1.2)` + overlay `opacity .22s`; (b) `.ficha-modal-overlay` (`:240,:251`) usa `animation: loreFade .2s` + `lorePop .24s cubic-bezier(.2,.8,.3,1.2)`; (c) `.lore-modal-overlay` (`:919,:934`) repete loreFade/lorePop. Dois mecanismos (transition vs `@keyframes`), duas curvas (`.9` vs `.8` no 3o ponto) e tres timings.
- **Impacto na UX:** modais "abrem diferente" dependendo de onde voce esta — inconsistencia sutil que corroi a sensacao de produto unico; e triplica a superficie de manutencao.
- **Evidencia / boa pratica:** [Boa prática] o que faz parecer premium: ease-out custom, animar so `transform`/`opacity`, `transform-origin` correto, respeitar reduced-motion; consistencia de linguagem de movimento. (Nota: a assimetria de timing entrada/saida e da NN/g, **nao** de Kowalski — a atribuicao a Kowalski foi refutada na pesquisa.)
- **Referencia:** `pesquisa/02-timing-motion-libs-casos.md` (Alvo 5) — <https://emilkowal.ski/ui/great-animations> · <https://vercel.com/design/guidelines>
- **Solucao proposta:** unificar numa unica coreografia scale+fade (`scale .96 -> 1` + `opacity 0 -> 1`) usando os tokens de duracao/ease do achado 1, aplicada por uma classe utilitaria compartilhada pelas tres overlays; sob `reduce`, manter so o fade. Eliminar `lorePop` e o transition divergente em favor de um unico keyframe.
- **Prioridade:** Media
- **Dificuldade:** Media
- **Impacto esperado:** usabilidade/percepcao de coesao; manutencao. Ganho qualitativo de polimento.

---

### 6. `JUICE` e no-op fora de `.campo-batalha` — sem celebracao no mapa/perfil/loja
- **Descricao:** `public/js/juice.js:13-14` faz `var arena = document.querySelector('.campo-batalha'); if (!arena) { window.JUICE = stub(); return; }` — fora da arena, `window.JUICE` vira stub (no-op). Logo, vitorias de fase no mapa, subida de nivel no perfil e compras na loja nao recebem nenhum feedback de particulas/celebracao.
- **Impacto na UX:** momentos de conquista (os que mais merecem reforco) passam silenciosos fora do combate, perdendo oportunidade de marcar progresso e recompensa.
- **Evidencia / boa pratica:** [Consenso de mercado] gamificacao em camadas (Duolingo): XP/streak -> conquistas -> ligas, cada camada reforcando um estagio — momentos de progressao merecem feedback. [Boa prática] microinteracoes premium 0 KB: so `transform`/`opacity`, 60fps, respeitar reduced-motion (o `juice.js` ja faz, `:17`).
- **Referencia:** `pesquisa/02-timing-motion-libs-casos.md` (Alvo 5) — <https://trophy.so/blog/duolingo-gamification-case-study> · <https://emilkowal.ski/ui/great-animations>
- **Solucao proposta:** generalizar o `JUICE` para anexar-se a um container overlay no nivel do `document` (ex.: criado pela shell em `footer.php`) em vez de depender de `.campo-batalha`, expondo um `JUICE.celebrar(elemento)` que dispara particulas/confete em conquista/compra/level-up em qualquer pagina. Manter apenas `transform`/`opacity` e o guard de reduced-motion ja existente.
- **Prioridade:** Media
- **Dificuldade:** Media
- **Impacto esperado:** engajamento e reforco de progressao. Honesto: numeros de gamificacao da fonte sao cross-app e correlacionais (nao causais) — tratar como direcional.

---

### 7. Confirmar stack nativo e evitar animar propriedades nao-compositaveis
- **Descricao:** o projeto ja e 100% nativo (CSS + um `juice.js`/`som.js` proprios, sem libs de animacao), o que esta correto. Mas varias transicoes animam propriedades C-Tier (paint): `box-shadow`/`border-color`/`background` aparecem em listas de `transition` (ex.: `style.css:1004` carta-loja anima `box-shadow`+`border-color`; `:1436` ranking anima `background`). Nao ha `transition: all` (bom), mas tambem nao ha diretriz registrada para manter a stack nativa e priorizar S-Tier.
- **Impacto na UX:** custo de paint desnecessario em hover/listas grandes (queda de fps em mobile); e risco futuro de alguem "resolver" com uma lib pesada sem tree-shaking.
- **Evidencia / boa pratica:** [Fato validado] WAAPI tem ~96% de cobertura e cobre o publico com 0 KB; sem build nao ha tree-shaking, entao qualquer lib (GSAP ~27 KB gzip, Framer ~34 KB+) entra inteira. [Boa prática] animar so S-Tier (`transform`/`opacity`/`filter`/`clip-path`); biblioteca so se justifica para spring interrompivel/timeline/FLIP (entao Motion mini ~2,3 KB via CDN).
- **Referencia:** `pesquisa/02-timing-motion-libs-casos.md` (Alvo 3) — <https://caniuse.com/web-animation> · <https://motion.dev/magazine/should-i-use-framer-motion-or-motion-one>
- **Solucao proposta:** registrar como regra de projeto "motion nativo, so S-Tier"; nos hovers de carta/linha, preferir `transform`/`opacity` (ex.: trocar glow animado de `box-shadow` por overlay com `opacity`) e reservar `box-shadow` para estado estatico. Nao adicionar libs; Motion mini via CDN apenas se surgir necessidade real de spring/FLIP.
- **Prioridade:** Baixa
- **Dificuldade:** Baixa
- **Impacto esperado:** desempenho (fps em mobile) e guarda de arquitetura. Ganho marginal mas barato; previne regressao de bundle.
