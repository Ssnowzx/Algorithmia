# PROMPT MESTRE — Jogo SVG completo, ambientado e em tela cheia

> **Algorithmia (este repo):** use [`docs/PROMPT-EVOLUCAO-VISUAL.md`](PROMPT-EVOLUCAO-VISUAL.md) como design system oficial. Este prompt abaixo é um template genérico SVG, não a direção visual do jogo.

> Prompt de nível "estúdio" para construir um jogo **vetorial (SVG)** com a sensação de um
> título completo (benchmark: **Super Mario World**): mundo coeso, profundidade de cena,
> uso de **toda a tela**, e técnica de SVG na raiz (filtros, camadas, máscaras, câmera por
> viewBox). Encapsula princípios de design de gênios (Miyamoto + mestres do pixel/vetor) e
> técnicas pesquisadas. Preencha o bloco de contexto e cole como instrução para a IA.

---

# 0. CONTEXTO (preencher)
- **Jogo / projeto:** [ex: Algorithmia — evolução vetorial / novo jogo]
- **Gênero & câmera:** [ex: plataforma 2D com scroll lateral / overworld + fases / arena]
- **Tema & humor:** [ex: fantasia tech "programar é magia", épico-divertido com humor ácido]
- **Stack:** [ex: HTML+CSS+JS vanilla, SVG inline, sem dependências / React / dentro do PHP MVC existente]
- **O que preservar:** [mecânicas, regras, arquitetura que NÃO podem quebrar]
- **Alvo de plataforma:** desktop + mobile (touch), navegadores atuais, 60fps.

---

# 1. FILOSOFIA DE DESIGN (o "porquê" antes do "como" — conhecimento dos gênios)
Aplique estes princípios a CADA tela e fase, não só ao código:

1. **Legibilidade acima de tudo (Miyamoto).** O jogador precisa ler o espaço em 0,2s.
   Garanta separação clara de **3 planos** por *valor* e *contraste*: fundo (baixo
   contraste, dessaturado, claro/desfocado) → meio → frente/entidades (alto contraste,
   saturado, nítido, contorno). O herói e os elementos interativos são sempre os de maior
   contraste na tela. Nada importante se confunde com decoração.
2. **Ensinar jogando (lição do World 1-1).** Introduza cada mecânica num espaço seguro
   onde errar não pune; o layout deve "convidar" a ação certa sem texto. Recompense cedo
   (satisfação imediata — o jogador "cresce"/ganha algo nos primeiros segundos).
3. **Ritmo: tensão e descanso.** Alterne desafios com "respiros". Estruture a fase em
   **kishōtenketsu**: introduz a ideia → desenvolve → vira/surpreende com uma reviravolta
   da mesma ideia → conclui. Coerência de desafio a desafio.
4. **Game feel / "juice".** Toda ação devolve resposta imediata (movimento, partícula,
   luz, som, micro-shake). Antecipação → ação → reação (squash & stretch, recuo, overshoot).
5. **Coesão "da mesma mão".** Um único sistema visual: mesma espessura de contorno, mesma
   fonte de luz, mesma paleta-token em cenário, itens, personagens e UI.

---

# 2. DIREÇÃO DE ARTE VETORIAL
- **Estilo único** [ex: flat vector com contorno grosso e sombra chapada / gradiente suave].
  Justifique em 2 linhas por que casa com o tema.
- **Paleta = tokens fixos** (5–9 cores) declarados uma vez (CSS custom properties **e/ou**
  `<linearGradient>`/`<radialGradient>` em `<defs>`). Cenário, itens, herói e UI usam SOMENTE
  esses tokens. Cor nova só como token derivado.
- **Profundidade por perspectiva atmosférica:** camadas distantes ficam mais claras, mais
  dessaturadas e levemente desfocadas (`feGaussianBlur` fraco); camadas próximas, saturadas
  e nítidas. Isso, somado a escala e sobreposição, cria volume sem 3D.
- **Tipografia:** 1 fonte de título + 1 de HUD/corpo, com fallback web-safe.

---

# 3. ARQUITETURA SVG (a base de tudo — faça assim)
- **Um SVG raiz** ocupando a tela inteira, com **`viewBox` em unidades de mundo** fixas
  (ex.: `viewBox="0 0 1920 1080"`), `width/height: 100%`, e `preserveAspectRatio` escolhido
  pelo papel:
  - **Cenário de fundo full-bleed:** `preserveAspectRatio="xMidYMid slice"` (preenche a tela
    e corta o excesso — equivale a `background-size: cover`). **Nunca** use `none` em arte
    (distorce); reserve `none` só para divisores decorativos.
  - **Conteúdo jogável:** mantenha tudo importante dentro de uma **"safe zone"** central,
    porque o `slice` corta as bordas em proporções extremas (ultrawide/retrato).
- **Camadas como grupos `<g>`** empilhados (ordem = profundidade), cada uma um alvo de
  parallax independente:
  `céu → montanhas distantes → floresta média → chão próximo → entidades → partículas/FX → HUD`.
- **Reuso com `<defs>` + `<symbol>` + `<use>`:** defina cada sprite/tile/prop UMA vez como
  `<symbol>` e instancie com `<use href="#...">`. Gradientes, filtros, máscaras e clipPaths
  também moram em `<defs>`. (Atenção: `display:none` num container de defs pode impedir a
  renderização de gradientes/filtros referenciados — use posição fora de tela ou
  `width/height:0`, não `display:none`.)
- **HUD/menus** podem ser HTML/CSS sobre o SVG (mais fáceis para texto/acessibilidade) OU
  uma camada SVG própria — escolha e justifique.

---

# 4. CÂMERA E "USAR TODA A TELA" (truque-chave)
- **Câmera barata = animar o `viewBox`.** Mover/!aproximar a câmera é só interpolar
  `x y w h` do `viewBox` por `requestAnimationFrame` (ou `setAttribute`). Pan lateral, zoom
  em chefe, "câmera que segue o herói" — tudo sem mexer em cada elemento. É o equivalente
  vetorial do scroll de Mario.
- **Tela cheia de verdade:** SVG a `100vw/100vh`, sem margens; use `100svh`/`100dvh` no
  mobile para lidar com a barra do navegador. HUD ancorado às bordas com `env(safe-area-inset-*)`.
- **Responsivo sem retrabalho:** como o mundo está em unidades de viewBox, a arte escala
  sozinha em qualquer resolução; você só ajusta a safe zone e o tamanho do HUD por breakpoint.
- **Mobile:** controles touch sobrepostos (d-pad/botões) como camada própria, com áreas de
  toque generosas; suporte a gestos onde fizer sentido.

---

# 5. ANIMAÇÃO — QUAL TÉCNICA USAR (decisão com justificativa obrigatória)
Priorize SEMPRE animar só **`transform` e `opacity`** (acelerados por GPU); evite animar
propriedades que causam *reflow/repaint* (x/y/width/largura geométrica, filtros pesados por frame).

- **CSS animations/transitions** → para **estados declarativos e loops** (idle flutuando,
  pulso, brilho, transições de UI 150–300ms ease-out, hover). O navegador otimiza e roda
  fora da main thread. Use para 80% das animações.
- **`requestAnimationFrame` (JS)** → para o **game loop**: física, input, câmera (viewBox),
  parallax dirigido por posição, spawn/atualização de partículas, IA. Sincroniza com o
  repaint (~16,7ms/frame). Use quando o valor depende de estado do jogo a cada frame.
- **SMIL (`<animate>`)** → **evite** em produção: suporte inconsistente/descontinuado em
  Chromium/Edge. Prefira CSS ou JS. (Cite SMIL só se houver fallback.)
- **Squash & stretch / overshoot** com `transform: scale()` + easing para dar peso (pulo,
  aterrissagem, coleta). Antecipação antes da ação, reação depois.

---

# 6. AMBIENTAÇÃO COM FILTROS SVG (o que faz parecer "vivo")
Use com parcimônia (filtros são caros — veja §8), mas são o que separa "ok" de "estúdio":

- **Nuvens / névoa / fumaça:** `<feTurbulence type="fractalNoise">` (padrão suave e
  difuso). Anime `baseFrequency` ou `seed` lentamente (via JS/CSS) para nuvens que
  respiram. Combine com máscara de gradiente para dissolver nas bordas.
- **Água / ondas / calor:** `<feTurbulence type="turbulence">` alimentando um
  `<feDisplacementMap>` que distorce a camada — superfície d'água ondulando, miragem de
  calor na montanha do deserto, portal mágico. Anime a turbulência para movimento contínuo.
- **Profundidade (DOF):** `<feGaussianBlur>` fraco nas camadas distantes; nitidez total na
  frente. Reforça a perspectiva atmosférica.
- **Glow / brilho arcano:** `feGaussianBlur` + `feMerge` (blur por baixo, original por
  cima) para auras de poder, projéteis mágicos, itens lendários.
- **Iluminação:** `<radialGradient>` para fontes de luz/halo; `<linearGradient>` para o céu
  (interpole as cores para **dia↔noite**); **light shafts** (raios de sol) = retângulo com
  gradiente recortado por `mask`.

---

# 7. MÁSCARAS, CLIPPING, GRADIENTES E MORPHING
- **`clipPath`** = recorte com **borda dura vetorial** (ex.: silhueta, recorte de tile).
  Não aceita gradiente/suavidade.
- **`mask`** = revelação por **luminância** (branco mostra, preto esconde, cinza = parcial)
  e **aceita gradiente** → use para: vinheta nas bordas, *fog of war*, desvanecer a
  superfície da água, transições de fase (íris/limpa-tela), feixes de luz suaves.
- **Gradientes** como tokens reutilizáveis em `<defs>` (céu, metal, poção, barra de HP).
- **Morphing de path:** anime o atributo `d` (ou use transforms) para movimento orgânico —
  bandeira tremulando, chama, líquido, "blob" de slime. Mantenha o mesmo número de pontos
  entre os estados para interpolar suave.
- **Pattern tiling:** `<pattern>` para texturas repetidas (grama, tijolos, água) sem inchar
  o DOM.

---

# 8. PERFORMANCE (60fps com cena cheia — regra de ouro)
- Anime só `transform`/`opacity`. Mova a **camada `<g>` inteira**, não elemento por elemento.
- **Filtros são o maior risco:** limite a **região do filtro** (`x/y/width/height` no
  `<filter>`), use `numOctaves` baixo, e **não** anime `baseFrequency` de um filtro que
  cobre a tela toda a cada frame — prefira animação lenta ou pré-renderize.
- **Object pooling** de partículas: reaproveite um conjunto fixo de `<use>`/`<circle>` em vez
  de criar/destruir nós. Se a contagem for alta (centenas), considere **`<canvas>`** para a
  camada de partículas e SVG para o resto.
- `will-change: transform` com moderação só nos elementos que realmente animam.
- Faça **escritas no DOM em lote** dentro do rAF; evite ler layout no meio do loop.
- **`prefers-reduced-motion`:** desligue parallax, turbulência, shake e flutuações;
  mantenha só transições essenciais.

---

# 9. SOM (imersão — combina com o ritmo de game feel)
- **Web Audio API**, desbloqueada no 1º gesto. Mapeie evento→som para TODA ação (pulo,
  coletar, dano, acerto, vitória, derrota, clique de UI). Timbres curtos e distintos.
- **Volume + MUTE persistente.** Trilha ambiente opcional, em loop, baixa, separada dos efeitos.
- Sem arquivos externos pesados sem autorização (gere por osciladores ou sprite de áudio).

---

# 10. ESTRUTURA DE "JOGO COMPLETO" (como Mario World)
Entregue o ciclo inteiro, não uma cena solta:
- **Tela de título** (arte full-bleed + entrada).
- **Overworld/mapa** navegável → **fases** com início/meio/fim e ritmo (kishōtenketsu).
- **HUD que usa as bordas** da tela (vida, recursos, progresso) sem tampar a ação.
- **Transições** entre telas (íris/wipe via máscara), **vitória/derrota**, continuar.
- **Estados de pausa**, áudio, e acessibilidade.

---

# 11. PROCESSO
1. **Antes de codar:** apresente (a) a proposta de direção de arte — estilo + tokens em
   hex/gradientes + as camadas de parallax; (b) o layout do SVG raiz e da câmera por viewBox;
   (c) a lista de estados de animação e o mapa evento→som. **Espere meu OK.**
2. **Depois do OK:** entregue o código **completo, sem trechos truncados**, pronto para rodar.
3. **Explique** cada decisão técnica (por que CSS vs rAF em cada caso; por que cada filtro;
   por que cada escolha de `preserveAspectRatio`) e **como testar** cada novidade.

# 12. DEFINIÇÃO DE "PRONTO"
- Cena legível em 3 planos; herói/itens sempre o maior contraste.
- Ocupa a tela inteira em qualquer proporção (desktop/mobile), com safe zone respeitada.
- Câmera por viewBox fluida; parallax e atmosfera (turbulência/blur/luz) presentes.
- Toda ação tem feedback visual E sonoro.
- 60fps com a cena cheia; `prefers-reduced-motion` e MUTE funcionando.
- Mecânicas/arquitetura originais preservadas (zero regressão).

---

## Apêndice — esqueleto de referência (estrutura, não código final)
```html
<svg id="jogo" viewBox="0 0 1920 1080" preserveAspectRatio="xMidYMid slice"
     style="width:100vw;height:100dvh;display:block">
  <defs>
    <linearGradient id="ceu">…dia↔noite…</linearGradient>
    <radialGradient id="sol">…glow…</radialGradient>
    <filter id="nuvem" x="-10%" y="-10%" width="120%" height="120%">
      <feTurbulence type="fractalNoise" baseFrequency="0.004" numOctaves="3"/>
      <feDisplacementMap in="SourceGraphic" scale="20"/>
    </filter>
    <filter id="dof"><feGaussianBlur stdDeviation="2"/></filter>
    <mask id="vinheta">…gradiente radial branco→preto…</mask>
    <symbol id="heroi" viewBox="0 0 64 64">…</symbol>
    <symbol id="moeda" viewBox="0 0 32 32">…</symbol>
  </defs>

  <g id="camada-ceu">…<rect fill="url(#ceu)"/>…</g>
  <g id="camada-distante" filter="url(#dof)">…montanhas…</g>
  <g id="camada-media">…floresta…</g>
  <g id="camada-proxima">…chão (pattern)…</g>
  <g id="camada-entidades"><use href="#heroi"/>…inimigos…</g>
  <g id="camada-fx">…partículas pooled…</g>
  <g id="vinheta-overlay" mask="url(#vinheta)">…</g>
</svg>
<!-- HUD em HTML/CSS por cima, ancorado às bordas com env(safe-area-inset-*) -->
```
```js
// Câmera = interpolar o viewBox no game loop (rAF). Parallax = camadas a velocidades diferentes.
function loop(t){
  atualizarFisica(t);
  const cam = seguirHeroi();                 // alvo de câmera
  svg.setAttribute('viewBox', `${cam.x} ${cam.y} ${cam.w} ${cam.h}`);
  camadaDistante.setAttribute('transform', `translate(${-cam.x*0.2} 0)`); // parallax lento
  camadaMedia.setAttribute('transform',   `translate(${-cam.x*0.5} 0)`);
  requestAnimationFrame(loop);
}
requestAnimationFrame(loop);
```

---

## Fontes das técnicas (pesquisa de raiz)
- Câmera/escala responsiva: [viewBox & preserveAspectRatio (guia)](https://blog.noelcserepy.com/the-interactive-guide-to-svgs-viewport-viewbox-and-preserveaspectratio) · [How to Scale SVG — CSS-Tricks](https://css-tricks.com/scale-svg/) · [preserveAspectRatio — MDN](https://developer.mozilla.org/en-US/docs/Web/SVG/Reference/Attribute/preserveAspectRatio)
- Animação (CSS vs SMIL vs JS) e 60fps: [Métodos comparados — Xyris](https://xyris.app/blog/svg-animation-methods-compared-css-smil-and-javascript/) · [requestAnimationFrame — Medium](https://medium.com/@bdc/gain-motion-superpowers-with-requestanimationframe-ecc6d5b0d9a4) · [Animation performance — MDN](https://developer.mozilla.org/en-US/docs/Web/Performance/Guides/Animation_performance_and_frame_rate) · [Otimizar animações SVG — Zigpoll](https://www.zigpoll.com/content/how-can-i-optimize-svg-animations-to-run-smoothly-on-both-desktop-and-mobile-browsers-without-significant-performance-loss)
- Filtros/atmosfera: [feTurbulence (textura) — Codrops](https://tympanus.net/codrops/2019/02/19/svg-filter-effects-creating-texture-with-feturbulence/) · [SVG Filter Effects — Codrops](https://tympanus.net/codrops/2019/02/26/svg-filter-effects-moving-forward/) · [Água com turbulence — Red Stapler](https://redstapler.co/realistic-water-effect-svg-turbulence-filter/) · [feTurbulence — MDN](https://developer.mozilla.org/en-US/docs/Web/SVG/Reference/Element/feTurbulence)
- Máscaras/clip/reuso: [Masks & clipPaths — Motion Tricks](https://www.motiontricks.com/svg-masks-and-clippaths/) · [Animating clipped elements — Smashing](https://www.smashingmagazine.com/2015/12/animating-clipped-elements-svg/) · [SVG sprites modernos — SVG Genie](https://www.svggenie.com/blog/svg-sprite-modern-guide)
- Parallax/profundidade: [Creating Depth & Immersion — GameMaker](https://gamemaker.io/en/blog/creating-depth-and-immersion-parallax) · [Parallax scrolling — Tuts+](https://code.tutsplus.com/parallax-scrolling-a-simple-effective-way-to-add-depth-to-a-2d-game--cms-21510t)
- Design (Miyamoto/Mario): [Método Super Mario World — Tuts+](https://code.tutsplus.com/how-to-design-levels-with-the-super-mario-world-method--cms-25177a) · [Miyamoto sobre o World 1-1 — HotHardware](https://hothardware.com/news/gaming-legend-shigeru-miyamoto-explains-design-philosophy-behind-super-mario-bros-world-1-1) · [Kishōtenketsu & Hakoniwa — Medium](https://openedsource.medium.com/kish%C5%8Dtenketsu-hakoniwa-dd5a568da169)
