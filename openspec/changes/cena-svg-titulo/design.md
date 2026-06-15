## Context

Pivô vetorial do Algorithmia (até aqui pixel art PNG). Camadas afetadas: **view** (layout
splash + home) e **assets** (`public/css`, `public/js`); nenhum model/service/controller muda.
Reaproveita: helper `svg()`/`asset()` (`app/core/helpers.php`), tokens de `:root`
(`public/css/style.css`), `window.SOM` da Fase 1 (`public/js/som.js`) e o padrão de
`IntersectionObserver` já usado em `app.js`. A home (`home/index.php`) é standalone (sem
layout) e hoje só carrega `ui.js`; o splash (`layout/splash.php`) é incluído no header e na home.

## Goals / Non-Goals

**Goals:**
- Sistema reutilizável de cena SVG (defs compartilhados, camadas/parallax, câmera viewBox,
  atmosfera, acessível, 60fps) aplicável a qualquer cena futura.
- Duas cenas de prova: splash cinemático + título da home.
- Pad ambiente procedural + SFX de título, reusando `window.SOM`.
- Coesão por tokens de `:root`; zero regressão; sem dependências novas.

**Non-Goals:**
- Migrar arena/mapa/personagens para vetor; modo plataforma jogável; ciclo dia↔noite de
  gameplay. (Fases seguintes.)

## Decisions

### Estrutura reutilizável
- **`svg-defs.php`**: `<svg width=0 height=0 aria-hidden>` com `<defs>` (gradientes de céu,
  lua/sol glow; filtros `#dof`, `#glow`, `#nevoa`; `mask #vinheta`; `<symbol>`s). Incluído uma
  vez por página que usa cena. **Não** usar `display:none` (quebra refs) — usar `0x0`/offscreen.
- **`cena.css`**: `.cena-svg` (SVG raiz full-bleed), `.cena-camada` (grupos), `.cena-safe`
  (safe-zone central), `.cena-hud` (ancorado às bordas com `env(safe-area-inset-*)`), estados
  CSS (entrada/glow/pulso) e `@media (prefers-reduced-motion)`.
- **`cena.js`** (`window.CENA`): `CENA.iniciar(svgEl, opcoes)` → loop rAF que (1) interpola o
  `viewBox` (deriva ambiente + alvo de ponteiro/giroscópio), (2) aplica `transform` por camada
  conforme `data-parallax` (fator), (3) atualiza partículas pooled. `IntersectionObserver`
  pausa o loop fora de tela; `matchMedia('(prefers-reduced-motion)')` desliga movimento.
  Escritas no DOM em lote por frame.

### Por que cada técnica (§5/§8 do prompt)
- **CSS** para estados declarativos/loops (glow, pulso, entrada, hover) — fora da main thread.
- **rAF** só para o que depende de estado por frame (câmera, parallax de ponteiro, partículas).
- **SMIL evitado** (suporte inconsistente em Chromium/Edge).
- **Filtros caros:** região limitada + `numOctaves` baixo; **névoa = `<g>` texturizada
  transladada** (não animar `baseFrequency` por frame); light shaft = gradiente recortado por
  `mask` com sway em CSS; glow = blur+`feMerge`; DOF = `feGaussianBlur` fraco nas camadas
  distantes.

### Câmera por viewBox + parallax
- A câmera é o `viewBox` do SVG raiz; deriva ambiente lenta (respiração) + offset do ponteiro
  (desktop) / giroscópio (mobile, se disponível). As camadas distantes andam menos
  (`data-parallax` baixo), as próximas mais — equivalente vetorial do scroll do Mario.

### Tokens nos gradientes SVG
- Stops usam `stop-color: var(--token)` (suportado em navegadores atuais) → fonte única de
  verdade com o resto do jogo, que ainda usa os tokens. Cor nova só como token derivado.

### Pad ambiente (extensão de som.js)
- `SOM.ambienteIniciar(perfil)`: 2–3 osciladores (ex.: `sine`/`triangle`) detunados formando
  um acorde grave, um LFO lento modulando o ganho/filtro, `BiquadFilter` lowpass; tudo num
  `ambienteGain` próprio → `masterGain` (herdando MUTE). `ambienteParar()` faz fade-out e para
  os osciladores. Sob reduced-motion, sem LFO perceptível. Persistir só on/off é desnecessário;
  o ambiente é por-cena (inicia no título, para ao navegar). Volume independente
  (`SOM.ambienteVolume`).

### Integração das cenas
- **Splash**: `splash.php` passa a renderizar a `.cena-svg` curta + `svg-defs.php`; o JS atual
  (auto-dismiss por `sessionStorage`) é mantido; chama `CENA.iniciar` com um perfil "push-in".
- **Home**: substituir o `.hero` por `.cena-svg` + `.cena-safe` (logo/título/botões em HTML
  sobre o SVG); incluir `svg-defs.php`, `cena.css`, `som.js`, `cena.js`; iniciar
  `CENA.iniciar` + `SOM.ambienteIniciar` ao carregar (e `ambienteParar` ao sair). História e
  Mestres seguem abaixo, ao rolar.

## Risks / Trade-offs

- **Filtros full-screen pesam em mobile** → região limitada, `numOctaves` baixo, névoa por
  translação (não filtro por frame); reduced-motion remove turbulência. Validar FPS em devtools.
- **`stop-color: var(--token)` em SVG** pode falhar em navegadores antigos → fallback: definir
  o hex do token como fallback (`var(--primaria, #7c5cff)`); navegadores-alvo são atuais.
- **Home standalone não tinha `som.js`** → incluir agora; todas as chamadas com guarda
  `window.SOM && ...`. Sem regressão nas páginas que não usam cena.
- **Conflito com `.splash-*` atuais** → o splash novo usa classes `.cena-*`; remover/!substituir
  o markup pixel antigo no `splash.php` para não duplicar.
- **Loop rAF drenando bateria** → `IntersectionObserver` + parada quando ocioso, como na Fase 1.
- **Pivô contradiz memória de pixel-art-fixo** → registrar na memória que o pivô vetorial
  supera aquela regra; pixel art coexiste até a migração das demais cenas.
