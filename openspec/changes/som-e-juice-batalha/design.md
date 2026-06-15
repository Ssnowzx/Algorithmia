## Context

Algorithmia é um RPG educativo em PHP MVC com front-end vanilla (HTML/CSS/JS) e pixel art
PNG. O jogo já tem tokens de cor/tipografia em `:root` (`public/css/style.css`), animações
de batalha (idle, investida, tremor, flash, números flutuantes simples), modal/toast
(`ui.js`), efeito de máquina de escrever (`dialogo.js`) e a lógica de turnos (`batalha.js`).
**Não existe áudio.** Esta change é 100% front-end e mexe nas camadas **view** (layout
header/footer) e nos **assets públicos** (`public/css`, `public/js`); nenhuma camada
model/service/controller PHP é tocada.

Dois guias técnicos (síntese Web Audio procedural e juice a 60fps) embasam os parâmetros
abaixo.

## Goals / Non-Goals

**Goals:**
- Camada de som procedural (`window.SOM`) sem dependências, com desbloqueio do contexto,
  master gain, mute/volume persistentes e o mapa evento→timbre.
- Reforçar o juice de batalha: números flutuantes melhores, partículas, brilho do especial,
  screen-shake contido.
- Respeitar `prefers-reduced-motion` em CSS e JS.
- Zero regressão em mecânica, segurança e arquitetura; uso só dos tokens de `:root`.

**Non-Goals:**
- Parallax/cenários das 5 regiões; microanimações de itens (loja/inventário); estados de
  vitória/derrota dos sprites; trilha ambiente em loop (fases seguintes).
- Qualquer mudança em PHP de domínio, banco, rotas ou validação no servidor.

## Decisions

### Camada afetada: apenas **view** + assets públicos
- **`public/js/som.js`** (novo) → `window.SOM`, carregado no `footer.php` (global).
- **`public/js/juice.js`** (novo) → `window.JUICE`, sistema de partículas/shake; carregado
  só onde há arena (incluído na view `batalha/arena.php`, que já carrega `batalha.js`).
- Edições: `batalha.js`, `dialogo.js`, `ui.js`, `app.js` (disparos), `style.css` e
  `batalha.css` (estilos), `header.php` (botão de som), `footer.php` (incluir `som.js`).
- **Por quê:** mantém a separação MVC; o padrão do projeto já expõe globals
  (`window.UI`, `window.BATALHA`). `som.js` é global porque UI/diálogo/batalha tocam som;
  `juice.js` é só da arena para não carregar canvas onde não há batalha.

### Som: arquitetura `osc → [filtro] → gain(envelope) → masterGain → destination`
- Um `AudioContext` único; um `masterGain` para volume/mute. Cada som cria oscilador(es)
  efêmero(s) com envelope anti-click (ancorar com `setValueAtTime`, **nunca**
  `exponentialRamp` para 0 — usar `0.0001` e finalizar com `stop()`).
- **Desbloqueio:** listeners `click`/`keydown`/`touchstart`/`touchend`; `resume()` retorna
  Promise — remover os listeners só quando ela resolver (fallback iOS via `touchend`).
- **Mute/volume:** `masterGain.gain` ; estado em `localStorage` (`som_mute`, `som_vol`).
  Escolhido sobre cookie por ser puramente client-side e não precisar de round-trip ao PHP.
- **Receitas (do guia):** square p/ blips/acerto/combo; sawtooth p/ erro/especial/derrota/
  fragmento; triangle/sine p/ cura/grave; combo = `base * 2^(n/12)` com teto; typewriter
  com polifonia limitada (~6 vozes) e volume baixíssimo. Parâmetros numéricos concretos
  (freq/dur/envelope/filtro por evento) ficam documentados no topo de `som.js`.
- **API:** `SOM.acerto()`, `SOM.erro()`, `SOM.danoHeroi()`, `SOM.danoInimigo()`,
  `SOM.combo(n)`, `SOM.especial()`, `SOM.pocao()`, `SOM.ouro()`, `SOM.nivel()`,
  `SOM.vitoria()`, `SOM.derrota()`, `SOM.fragmento()`, `SOM.clique()`, `SOM.modalAbrir()`,
  `SOM.modalFechar()`, `SOM.tic()`, `SOM.mudo(bool)`, `SOM.volume(0..1)`, `SOM.unlock()`.

### Juice: CSS declarativo para estados, rAF só para partículas/shake
- **Números flutuantes:** melhorados via CSS @keyframes (arco com wrapper+filho, pop de
  escala com back-out, fade só nos últimos ~38%, crítico maior). JS só cria/posiciona/
  remove no `animationend`. Sem rAF — evita jank.
- **Partículas:** `<canvas>` único na arena, object pooling (pool fixo ~200), delta-time
  com `clamp(dt, 0.05)`, teto simultâneo ~150–200, `fillRect` quadradinhos (estética pixel),
  `devicePixelRatio` correto. Faísca 8–15, explosão 20–40, ouro 6–12.
- **Shake:** trauma-based no contêiner `.campo-batalha` (**não** no body), `offset =
  MAX(6–12px) * trauma² * rand(-1,1)`, decay 1.0–1.5/s; hit +0.3–0.4, crítico +0.6,
  fim de batalha 0.8–1.0. Roda no mesmo rAF das partículas; **para de reagendar** quando
  `activeCount===0 && trauma===0`.
- **reduced-motion:** CSS `@media (prefers-reduced-motion: reduce)` para declarativos +
  gating JS via `window.matchMedia(...).matches` no spawn e `addTrauma`, com listener de
  `change` para runtime.
- **Cores:** só tokens de `:root` (`--hp`, `--xp`, `--sucesso`, `--primaria`, `--mp`...).

## Risks / Trade-offs

- **iOS Safari pode re-suspender o áudio ao voltar de background** → checar
  `ctx.state === 'suspended'` antes de tocar e re-`resume()` se preciso. O modo silencioso
  do iOS bloqueia Web Audio (decisão do SO, sem contorno) — aceitável.
- **rAF rodando à toa drena bateria** → mitigado pelo desligamento do loop quando não há
  partículas nem trauma (não basta um boolean; é preciso parar de reagendar).
- **Sub-pixel/escala fracionária borra pixel art** → shake usa offsets contidos; sprites
  permanecem em escala inteira; números flutuantes (texto) podem escalar livremente.
- **Disparos de som fora da arena (UI/diálogo)** dependem de `window.SOM` existir → todas
  as chamadas via guarda `window.SOM && SOM.x()` para não quebrar páginas sem o módulo.
- **Risco de mexer demais em `batalha.js`** → disparos de som/juice entram como linhas
  aditivas nos pontos já existentes (`tratarTurno`, especial, poção, fragmento, fugir,
  `mostrarResultado`), sem alterar o fluxo de turnos nem a ordem vitória/derrota.
