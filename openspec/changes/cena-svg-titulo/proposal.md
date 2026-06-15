## Why

Mudança de estratégia visual: o Algorithmia pivota da arte **pixel art PNG** (até aqui
declarada "fixa") para **arte vetorial SVG** de qualidade de estúdio (benchmark Super Mario
World: cena full-screen, câmera por `viewBox`, parallax, atmosfera por filtros, 3 planos
legíveis). Esta change **supera** a regra anterior de pixel-art-fixo. A primeira entrega é a
**tela de título** — maior impacto de primeira impressão e menor risco — mas o núcleo é um
**sistema reutilizável de cena SVG** pensado para cada cena futura do jogo.

## What Changes

- **Sistema de cena SVG reutilizável** (núcleo da entrega):
  - `app/views/layout/svg-defs.php`: bloco `<defs>` único por página (gradientes de céu/glow,
    filtros `#dof`/`#glow`/`#nevoa`, `mask` `#vinheta`, `<symbol>`s), nunca em `display:none`.
  - `public/css/cena.css`: SVG raiz full-bleed (`100vw`/`100dvh`,
    `preserveAspectRatio="xMidYMid slice"`), classes de camada, safe-zone, HUD com
    `env(safe-area-inset-*)`, estados declarativos e `prefers-reduced-motion`.
  - `public/js/cena.js` (`window.CENA`): game-loop de cena via `requestAnimationFrame` —
    câmera por `viewBox` (deriva ambiente + parallax de ponteiro), camadas movidas por
    `transform`, partículas com object pooling. Para de reagendar quando ocioso/fora de
    viewport e sob movimento reduzido.
- **Pad ambiente procedural** em `public/js/som.js` (`SOM.ambienteIniciar`/`ambienteParar`):
  osciladores detunados + LFO + lowpass, ganho próprio (volume independente), respeitando o
  MUTE. Mais um SFX `pressStart`. **Sem arquivos de áudio**.
- **Duas cenas de prova:**
  - **Splash cinemático** (`app/views/layout/splash.php`): cena SVG curta (céu arcano +
    lua/glow + título subindo + push-in de câmera), auto-dismiss mantido.
  - **Título da home** (`app/views/home/index.php`): hero vira cena SVG full-screen com
    parallax/atmosfera; logo, título e botões de entrada em HTML na safe-zone; história e
    Mestres ao rolar. Pad ambiente inicia ao entrar (respeitando mute).
- **Coesão:** paleta = tokens de `:root` (gradientes SVG via `stop-color: var(--token)`);
  nenhum hex novo. Tipografia atual mantida.

## Capabilities

### New Capabilities
- `cena-svg`: sistema reutilizável de cena vetorial full-screen — `<defs>` compartilhados,
  camadas de parallax, câmera por `viewBox`, atmosfera por filtros e acessibilidade.
- `audio-ambiente`: pad ambiente procedural em loop (Web Audio, sem arquivos) com volume
  independente e respeito ao MUTE/`prefers-reduced-motion`.

### Modified Capabilities
<!-- Nenhuma capability de spec existente muda de requisito. O design-de-som da Fase 1 é
     estendido (pad ambiente) de forma aditiva, sem alterar os requisitos já registrados. -->

## Impact

- **Front-end apenas.** Novos: `app/views/layout/svg-defs.php`, `public/css/cena.css`,
  `public/js/cena.js`, e (opcional) `app/views/layout/cena-titulo.php`. Modificados:
  `app/views/layout/splash.php`, `app/views/home/index.php`, `public/js/som.js`,
  `public/css/style.css`.
- **Reuso:** helpers `svg()`/`asset()`, tokens de `:root`, `window.SOM` (Fase 1),
  `IntersectionObserver` (padrão já usado em `app.js`).
- **Sem mudanças** em PHP de domínio, banco, rotas, mecânica, validação ou segurança. Zero
  novas dependências.
- **Fora de escopo (fases seguintes):** migração das demais cenas (arena/mapa) e dos
  personagens para vetor; modo plataforma jogável (física/câmera-segue-herói); ciclo dia↔noite
  dinâmico de gameplay.
