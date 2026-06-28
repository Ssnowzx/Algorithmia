# Pesquisa — Passada 2: Timing/Easing · Reduced-motion/WCAG · Nativo-vs-Biblioteca · Thumb-zone · Casos reais

> **Parte da auditoria de produto do Algorithmia.** Método: workflow próprio de _deep research_
> (5 pesquisadores em paralelo → **verificação adversarial 3-votos** com lentes diversas
> [fonte-primária / desatualização / supergeneralização] nas afirmações falsificáveis → síntese
> citada). **39 afirmações coletadas → 22 confirmadas, 6 refutadas/inconclusivas, 11 não-falsificáveis.**
> Rótulos: **[Fato validado]** (sobreviveu à verificação) · **[Consenso de mercado]** · **[Boa prática]** ·
> **[Hipótese]** · **[Opinião]**. Nenhuma afirmação sem fonte. O que **não** sobreviveu está na seção de
> transparência ao final.
>
> **Já coberto na passada 1 (não repetido):** doutrina bottom-nav/rail, limite 3–5, anti-padrão de
> navegação escondida, alvos ≥44px + `aria-current`, suporte/mecânica de View Transitions cross-document.

---

## Alvo 1 — Timing & easing de motion

### Confirmadas

**[Fato validado] Os 3 limites de tempo de resposta de Nielsen** (de _Usability Engineering_, 1993):
**0,1 s** → reação instantânea (sem feedback especial); **1,0 s** → limite do fluxo de pensamento
ininterrupto (usuário nota o atraso mas não perde contexto); **10 s** → limite de atenção (acima
disso, vai fazer outra coisa).
Fonte: <https://www.nngroup.com/articles/response-times-3-important-limits/>

**[Fato validado, com ressalva] Doherty Threshold = <400 ms.** Produtividade dispara abaixo de
**400 ms** (substituiu o padrão anterior de 2.000 ms, atribuído a Robert B. Miller). **Ressalva
(1/3):** o documento original de Walter J. Doherty & Arvind J. Thadani (nov/1982) é o relatório
técnico IBM _"The Economic Value of Rapid Response Time"_, **GE20-0752** — não "IBM Systems
Journal"; "addicting" é interpretação secundária. O núcleo (400 ms; autores; 1982; predecessor de 2 s) confere.
Fonte: <https://lawsofux.com/doherty-threshold/>

**[Fato validado] Tokens de DURAÇÃO do Material 3 (ms):** Short **50/100/150/200** · Medium
**250/300/350/400** · Long **450/500/550/600** · Extra-Long **700/800/900/1000**.
Fontes: <https://m3.material.io/styles/motion/easing-and-duration/tokens-specs> ·
<https://github.com/material-components/material-components-android/blob/master/docs/theming/Motion.md>

**[Fato validado] Curvas (cubic-bezier) do Material 3:** Standard `(0.2,0,0,1)` · Standard
Decelerate `(0,0,0,1)` · Standard Accelerate `(0.3,0,1,1)` · Emphasized Decelerate `(0.05,0.7,0.1,1)`
· Emphasized Accelerate `(0.3,0,0.8,0.15)` · Linear `(0,0,1,1)`.
⚠️ A _Emphasized_ completa é curva de **dois segmentos** — **não** representável num único
`cubic-bezier()` CSS (exigiria `linear()` ou JS).
Fontes: <https://m3.material.io/styles/motion/easing-and-duration/tokens-specs> · (Motion.md acima)

### Não-falsificáveis

**[Boa prática] Bandas de duração da NN/g (Page Laubheimer):** feedback simples ≈ **100 ms**;
mudanças substanciais **200–300 ms**; "a maioria" em **100–500 ms**, com **100–400 ms** apropriado
(400 ms já é "muito lenta"). Fonte: <https://www.nngroup.com/articles/animation-duration/>

**[Boa prática] Regra de easing:** **ease-out** para entradas (rápido→desacelera = sensação de
resposta), **ease-in** para saídas; evitar linear ("looks weird and unnatural"). Convergência NN/g +
M3 + Emil Kowalski (que recomenda animações de UI **<~300 ms**).
Fontes: <https://www.nngroup.com/articles/animation-duration/> · <https://emilkowal.ski/ui/great-animations>

### ✅ Decisão p/ Algorithmia
Tokens de duração + easing em **CSS custom properties** (`:root`), espelhando M3 mas enxuto:
- **Microfeedback** (hover/toggle/clique): **~100 ms**, ease-out.
- **UI padrão** (cards de fase, tooltips, abrir painel): **200–300 ms**, `cubic-bezier(0.2,0,0,1)` ou Emphasized Decelerate.
- **Transições de tela / View Transitions** (combate, cenário): **300–400 ms**; **teto ~500 ms** (não estourar 1 s de Nielsen com encadeamentos).
- **Meta dura: <400 ms** (Doherty) para qualquer ação iniciada pelo jogador.

**Porquê:** 0 KB (puro CSS), auditável, alinhado aos 3 pilares (Nielsen/Doherty/M3). Ficar em curvas
de **um segmento** (Emphasized completa exige JS/`linear()` — evitar no MVC sem build).

**Critérios de parada:** (1) SoTA = M3 tokens ancorados em limiares de percepção. (2) Líderes: Google
M3, NN/g (100–500ms), Kowalski (<300ms) convergem em 100–400ms. (3) Decisão: tokens CSS enxutos,
ease-out p/ entradas, teto ~400ms. (4) Riscos: Emphasized de 2 segmentos; encadeamento >1s. (5)
Validar: tempo real percebido em device low-end (server-render + rede pode estourar 400ms).

---

## Alvo 2 — `prefers-reduced-motion` + WCAG 2.3.3

### Confirmadas

**[Fato validado] WCAG 2.3.3 (AAA), texto literal:** _"Motion animation triggered by interaction can
be disabled, unless the animation is essential…"_ → exige **opt-out** só para movimento que **o
usuário dispara** (ex.: parallax ao rolar). **Não proíbe** animação.
Fonte: <https://www.w3.org/WAI/WCAG21/Understanding/animation-from-interactions.html>

**[Fato validado] Escopo de "motion animation":** só **ilusão de movimento** (posição/tamanho/forma).
**Mudança pura de cor/opacidade (fade) NÃO é "motion animation"** → fora do escopo de 2.3.3.
Fonte: (W3C 2.3.3 acima)

**[Fato validado] 2.3.3 (AAA) vs 2.2.2 Pause/Stop/Hide (A) — distinção pelo GATILHO:** 2.2.2 =
animação **auto-iniciada** (>5 s); 2.3.3 = **disparada por interação**. Uma animação pode falhar nas duas.
Fontes: (W3C 2.3.3 acima) · <https://web.dev/learn/accessibility/motion>

**[Fato validado] Técnica C39:** envolver o CSS em `@media (prefers-reduced-motion: reduce){}` para
desligar, **ou** `@media (prefers-reduced-motion: no-preference){}` para **só ligar**.
Fonte: <https://www.w3.org/WAI/WCAG22/Techniques/css/C39>

**[Fato validado] Semântica da media feature:** `no-preference` → FALSE; `reduce` → TRUE; sem valor
≡ `reduce`. Fonte: <https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/At-rules/@media/prefers-reduced-motion>

**[Consenso de mercado] Desligar vs manter sob `reduce`:** **Desligar** escala/translação de objetos
grandes (parallax, slide-ins, zoom) — gatilhos vestibulares reais. **Manter** feedback sem movimento
(fade de opacidade, cor). MDN demonstra trocar `transform: scale()` por `@keyframes` de `opacity`.
Fontes: (MDN acima) · (web.dev acima) · <https://css-tricks.com/nuking-motion-with-prefers-reduced-motion/>

**[Consenso de mercado] Cuidado de implementação:** "nuke" a `0.01ms` pode **quebrar layout**
(transforms que terminam antes do render → elemento invisível). Mais seguro é **opt-in**: base sem
movimento, animação só em `@media (no-preference)`.
Fontes: <https://web.dev/articles/prefers-reduced-motion> · <https://stuffandnonsense.co.uk/blog/how-i-fixed-my-reduced-motion-broke-my-layout-problem/> · (C39 acima)

### Não-falsificáveis

**[Boa prática] Snippet catch-all** dentro de `@media (prefers-reduced-motion: reduce)`:
`animation-duration: 0.01ms !important; animation-iteration-count: 1 !important;
transition-duration: 0.01ms !important; scroll-behavior: auto !important;` — usa **0.01ms** (não 0,
que alguns navegadores tratam como nulo) e `iteration-count:1` para `animationend` ainda disparar.
Fonte: <https://css-tricks.com/nuking-motion-with-prefers-reduced-motion/>

### ✅ Decisão p/ Algorithmia
**Padrão opt-in (default reduzido)** + "nuke" como rede de segurança:
1. Base **sem** transform/movimento; embrulhar todo motion significativo (slides de combate, zoom,
   parallax do mapa) em `@media (prefers-reduced-motion: no-preference)`.
2. **Fade/cor sempre ligados** (fora do escopo de 2.3.3; feedback sem risco vestibular).
3. Snippet catch-all global como 2ª linha de defesa.

**Porquê:** o jogo tem muito _juice_ de batalha (escala/shake/slide) — exatamente os gatilhos
vestibulares; o opt-in evita o bug do "elemento invisível pós-nuke" e preserva microinteração premium
sem violar AAA. 100% CSS no MVC.

**Critérios de parada:** (1) SoTA = 2.3.3 + C39 + media feature; fade/cor fora de escopo. (2) Líderes:
MDN/web.dev/CSS-Tricks → opt-in > nuke; scale→opacity. (3) Decisão: opt-in + fade preservado +
catch-all. (4) Riscos: nuke quebra layouts dependentes de transform; libs externas ignoram a feature.
(5) Validar: se alguma mecânica de combate **depende** da animação p/ comunicar estado (aí é
"essencial" → usar ~1ms, não desligar).

---

## Alvo 3 — Nativo vs. biblioteca de animação

### Confirmadas

**[Fato validado, com ressalva] Motion (motion.dev) `animate()`:** **"mini" = 2.3 kb** (WAAPI pura);
**"hybrid" ≈ 18 kb** (springs, sequências, motion values, etc.). **Ressalva (1/3):** "menor lib para
React" refere-se ao hook `useAnimate` mini; 2 de 3 fontes citam a hybrid como **17 kb**. Núcleo
(mini 2.3 kb + WAAPI) correto.
Fontes: <https://motion.dev/docs/animate> · <https://motion.dev/guides/waapi-improvements>

**[Fato validado] GSAP core** (sem plugins): **~68,95 KB min** e **~26,71 KB min+gzip** — ~12× o
Motion mini. Fontes: <https://bundlephobia.com/package/gsap> · <https://www.pkgpulse.com/compare/framer-motion-vs-gsap>

**[Fato validado] Framer Motion / motion-react:** `<motion>` não cai abaixo de **~34 kb**; LazyMotion
chega a **~4,6 kb** inicial (+15kb domAnimation / +25kb domMax); pacote completo **~60,4 KB gzip**.
Fontes: <https://motion.dev/docs/react-reduce-bundle-size> · (pkgpulse acima)

**[Fato validado] WAAPI tem cobertura ~96%** (Chrome/Edge 84+, Safari 13.1+, Firefox 75+). Para
projeto pequeno sem build, **WAAPI nativa cobre o público com 0 KB.**
Fontes: <https://caniuse.com/web-animation> · <https://developer.mozilla.org/en-US/docs/Web/API/Web_Animations_API>

**[Consenso de mercado] Quando biblioteca se justifica:** spring physics com interrupção no meio,
timelines/sequências complexas, layout/FLIP automático. A própria Motion recomenda mini "só se você
está num orçamento estrito de bundle e só usa features de animação"; Motion One **não** faz layout
animations nem acessa valores por frame.
Fontes: <https://motion.dev/magazine/should-i-use-framer-motion-or-motion-one> · <https://www.reactlibraries.com/blog/framer-motion-vs-motion-one-mobile-animation-performance-in-2025>

### Não-falsificáveis

**[Boa prática] Tier list de propriedades (Motion):** **S-Tier** (só compositor, fora da main thread)
`transform`, `opacity`, `filter`, `clip-path`; **C-Tier** (forçam paint) `background-color`,
`border-radius`, CSS vars; **D-Tier** (forçam layout) `width`, `margin`. Atualizar 1 CSS var em 1300+
elementos custou **8 ms/frame**.
Fontes: <https://motion.dev/magazine/web-animation-performance-tier-list> · <https://motion.dev/docs/performance>

### ✅ Decisão p/ Algorithmia
**100% nativo:** CSS transitions/`@keyframes` + WAAPI (`element.animate()`) + View Transitions.
**Animar só propriedades S-Tier.** **Não** adicionar GSAP (~27 kb gzip) nem Framer (34 kb+) — sem
build não há tree-shaking, entraria o pacote inteiro. Teto barato, **só** se surgir necessidade real
(spring interrompível / timeline / FLIP): **Motion mini ~2,3 kb via CDN**.

**Porquê:** alinhado às regras (deps mínimas/zero, sem build); 0 KB, 96% de cobertura, compositor
garante 60 fps no juice de batalha.

**Critérios de parada:** (1) SoTA = WAAPI madura (~96%) + View Transitions; libs reservadas a
spring/timeline/FLIP. (2) Líderes: a própria Motion recomenda nativo/mini p/ orçamento estrito. (3)
Decisão: nativo (0 KB), Motion mini via CDN só se necessário. (4) Riscos: WAAPI só é "S-Tier" p/
props compositáveis; sem tree-shaking qualquer lib entra inteira. (5) Validar: confirmar que nenhuma
microinteração planejada exige spring interrompível real (ex.: arrastar cartas).

---

## Alvo 4 — Thumb-zone & ergonomia

### Confirmadas

**[Fato validado] Estudo de Steven Hoober (2013):** **1.333 observações** (780 tocando a tela); split
de pega **49% uma mão / 36% cradled / 15% duas mãos**. Números batem entre UXmatters e A List Apart.
Fontes: <https://www.uxmatters.com/mt/archives/2013/02/how-do-users-really-hold-mobile-devices.php> · <https://alistapart.com/article/how-we-hold-our-gadgets/>

**[Fato validado] Geografia do polegar:** só **~1/3 da tela** (base, lado oposto ao polegar) é
território sem esforço; polegares conduzem **75%** das interações. Sustenta nav/CTA na **faixa
inferior**, não no topo.
Fontes: (A List Apart acima) · <https://www.smashingmagazine.com/2016/09/the-thumb-zone-designing-for-mobile-users/>

**[Fato validado] Lei de Fitts (1954):** tempo ↑ com distância, ↓ com tamanho do alvo. Forma Shannon
(ISO 9241-9): `MT = a + b·log2(A/W + 1)`.
Fontes: <https://www.yorku.ca/mack/hhci2018.html> · <https://lawsofux.com/fittss-law/>

**[Fato validado] Lei de Hick (1952/1953):** tempo de decisão ↑ logaritmicamente com nº de opções:
`T = b·log2(n+1)`. Fontes: <https://en.wikipedia.org/wiki/Hick's_law> · <https://lawsofux.com/hicks-law/>

### Não-falsificáveis

**[Boa prática] Fitts → alvos de toque:** grandes, espaçados, em áreas fáceis. Justifica **44–48 px**
+ faixa inferior. Fonte: <https://lawsofux.com/fittss-law/>

**[Boa prática] Hick → navegação:** reduzir itens, fatiar tarefas, destacar a opção recomendada. Base
teórica do limite de **3–5 itens**. Fonte: <https://lawsofux.com/hicks-law/>

**[Opinião] CAVEAT (questionando a própria conclusão):** os 49/36/15 são retrato de **2013**; em >40%
das observações o usuário só lia; telas ≥5,5" mudam a pega ("choking up"). Usar a **doutrina** (base =
zona fácil), **não** os percentuais como dogma.
Fontes: (UXmatters acima) · <https://www.scotthurff.com/posts/how-to-design-for-thumbs-in-the-era-of-huge-screens/>

### ✅ Decisão p/ Algorithmia
Navegação e CTAs primários na **faixa inferior, região central-inferior** (alcançável por ambos os
polegares), **não em cantos** e **não no topo**: alvos **≥44–48 px** espaçados (Fitts); **3–5 itens**
(Hick), destacando a ação recomendada (ex.: "Continuar fase"); tratar 2013 como **direção, não regra**
(neutra a lado, pois destro/canhoto e telas grandes invertem qual canto é confortável).

**Porquê:** público mobile-first em telas grandes; o canto inferior é onde a pega mais varia — a faixa
**inferior-central** é o ponto robusto sob qualquer pega.

**Critérios de parada:** (1) SoTA = thumb-zone + Fitts + Hick; faixa inferior-central robusta. (2)
Líderes: A List Apart/Smashing/Laws of UX → base alcançável + alvos grandes + poucos itens. (3)
Decisão: nav inferior-central, ≥44px, 3–5. (4) Riscos: dados de 2013; conclusões "por canto" frágeis.
(5) Validar: alcance real do polegar em telas ≥6" do público.

---

## Alvo 5 — Casos reais & microinterações premium

### Confirmadas

**[Fato validado, com ressalva] Duolingo — mecânicas em CAMADAS.** O que faz funcionar é o **design em
camadas** (cada mecânica serve um cohort/estágio), não uma mega-feature. **Ressalva (1/3):** os números
citados (conquista dia-1 → retenção 33,42% vs 20,36%; ligas ~30 alunos) são **dados agregados cross-app
da plataforma Trophy.so**, **correlacionais** (não causais; viés de seleção) e auto-reportados — citar
como "dados de plataforma de gamificação", não cifras do Duolingo. A tese qualitativa (camadas; ligas ~30) tem suporte.
Fonte: <https://trophy.so/blog/duolingo-gamification-case-study>

**[Consenso de mercado] Spotify "Create" (2024) — caso-cautela canônico:** adicionar uma ação de
**baixa frequência** (criação) num slot nobre da bottom-nav **quebrou a memória muscular**, gerou
mistaps e reação suficiente para a Spotify lançar um **ajuste para desabilitar** o botão. Regra:
**nunca deslocar um destino de alta frequência (Library) por um raro.**
Fontes: <https://www.creativebloq.com/web-design/ux-ui/spotifys-latest-ui-design-change-is-driving-people-crazy> · <https://community.spotify.com/t5/Implemented-Ideas/Mobile-Get-rid-of-Create-button-on-mobile-navigation-bar/idi-p/6453037> · <https://dataconomy.com/2025/08/04/spotify-now-lets-users-remove-the-create-button/>

### Não-falsificáveis

**[Boa prática] Easing premium = ease-OUT + cubic-beziers custom** (as curvas embutidas do CSS "não
são fortes o suficiente"). Referências M3: Standard `(0.2,0,0,1)`, Emphasized Decelerate `(0.05,0.7,0.1,1)`.
Fontes: <https://emilkowal.ski/ui/great-animations> · (Motion.md acima)

**[Boa prática] O que faz parecer "premium":** **interrompível, origin-aware, 60 fps em props
compositáveis, suprimida para ações repetidas/teclado**. Kowalski: animar só `transform`/`opacity`,
nunca animar ações de teclado repetidas. Rauno (Freiberg): resposta **imediata** (delta de escala
aplica já, threshold anima depois); gestos **nunca** interrompidos no meio.
Fontes: <https://emilkowal.ski/ui/great-animations> · <https://rauno.me/craft/interaction-design>

**[Boa prática] Vercel Web Interface Guidelines:** nunca `transition: all` (listar `opacity`/`transform`);
`transform-origin` correto; honrar `prefers-reduced-motion`; animações interrompíveis; **optimistic
updates**; **gate de loaders** com show-delay **~150–300 ms** + tempo mínimo visível **~300–500 ms**
(evita flicker). Fonte: <https://vercel.com/design/guidelines>

### ✅ Decisão p/ Algorithmia
**Camadas + bottom-nav disciplinada + microinterações nativas premium:**
1. **Gamificação em camadas (Duolingo):** XP/streak (hábito) → conquistas (progressão) → ligas/ranking
   (competição); cada uma p/ um estágio. Citar números do Duolingo como **direcionais**.
2. **Bottom-nav (Spotify):** **jamais** ação rara em slot primário deslocando destino frequente
   (Mapa/Fases); ações raras vão dentro das telas.
3. **Microinterações 0 KB:** ease-out custom `cubic-bezier(0.2,0,0,1)`, só `transform`/`opacity`, 60 fps;
   `transform-origin` correto; nunca animar feedback de tecla repetida; loaders com show-delay 150–300 ms
   + mínimo 300–500 ms; respeitar `prefers-reduced-motion`.

**Porquê:** Kowalski/Rauno/Vercel dão um checklist implementável **sem JS pesado**, 100% compatível
com PHP server-render; as lições Duolingo/Spotify são de navegação/retenção, sem stack específica.

**Critérios de parada:** (1) SoTA = gamificação em camadas + disciplina de bottom-nav + checklist de
motion premium. (2) Líderes: Vercel codifica regras; Kowalski/Rauno definem interruptível + origin-aware
+ 60 fps. (3) Decisão: camadas + nav disciplinada + microinterações nativas. (4) Riscos: números do
Duolingo são cross-app/correlacionais. (5) Validar: flicker dos loaders no server-render; interrupção
de animações de combate com input rápido.

---

## Refutadas / Inconclusivas (transparência — NÃO usar como base)

1. **[Timing] "Assimetria entrada/saída (~300ms/200ms) atribuída a Emil Kowalski."** A metade NN/g é
   verbatim e correta (popup 300ms entra / 200–250ms sai), mas a **atribuição a Kowalski é falsa** — os
   números são da **NN/g**; o artigo dele só diz "<300 ms". (2/3 refutam.)
2. **[Lib] "WAAPI roda no compositor mesmo com main thread ocupada; Motion 2,5× mais rápido que GSAP."**
   Só vale p/ props **compositáveis**; os números 2,5×/6× são **auto-reportados pela Motion** (parte
   interessada), sem validação independente. (3/3 refutam.)
3. **[Thumb] "67% polegar direito → canto inferior-ESQUERDO é o mais cômodo."** Números corretos, mas a
   **implicação está invertida**: p/ polegar direito a zona cômoda é inferior-**direito**/central; o
   inferior-esquerdo é a zona difícil. (3/3 refutam.)
4. **[Cases] "Streak Freeze alonga streaks: 17,19 vs 11,62 dias."** São **dados cross-app da Trophy.so**,
   correlacionais, não medições do Duolingo; ademais o contador de streak fica **no topo**, não na
   bottom-nav. (3/3 refutam.)
5. **[Cases] "Redesign da Khan Academy (2023) unificou nav de Explore/Search/Bookmarks."** Funde um
   projeto conceitual pessoal não-oficial (~2019, mobile) com o redesign oficial web de 2023; Explore/
   Search/Bookmarks continuam vivos. (2/3 refutam.)
6. **[Cases] "Sweet spot premium converge: <300ms (Kowalski), 200–500ms (Head), M3 50–1000ms."**
   Misquote (Head diz **200–350ms**, não 200–300) + supergeneralização (limites superiores **divergem**;
   chamar 50–1000ms de "convergência" é inconsistente). (2/3 refutam.)

---

## Insumos diretos para o protótipo (consolidado)
- **Tokens de motion (CSS `:root`):** durações ~100 / 200–300 / 300–400 ms (teto 500); easings
  `--ease-out: cubic-bezier(0.2,0,0,1)` (entradas), `--ease-in` (saídas). Meta <400 ms por ação.
- **Reduced-motion:** opt-in (`@media (no-preference)`) p/ todo movimento; fade/cor sempre on; catch-all de segurança.
- **Stack:** nativo (CSS + WAAPI + View Transitions); só `transform`/`opacity`/`filter`/`clip-path`; 0 deps.
- **Nav:** inferior-central, ≥44px, 3–5 itens; nunca ação rara em slot primário.
- **Loaders/feedback:** show-delay 150–300 ms + mínimo 300–500 ms; nunca `transition: all`; `transform-origin` correto.
