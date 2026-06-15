## 1. Defs compartilhados e CSS de cena

- [x] 1.1 `app/views/layout/svg-defs.php`: `<svg 0x0 aria-hidden>` com `<defs>` — gradientes (céu noturno arcano, lua/glow), filtros `#dof`/`#glow`/`#nevoa`, `mask #vinheta`, `<symbol>`s reutilizáveis; usar `stop-color: var(--token, #hex)`; nunca `display:none`
- [x] 1.2 `public/css/cena.css`: `.cena-svg` full-bleed (`100vw`/`100dvh`, slice), `.cena-camada`, `.cena-safe` (safe-zone), `.cena-hud` (`env(safe-area-inset-*)`), estados de entrada/glow/pulso, e `@media (prefers-reduced-motion)` desligando movimento

## 2. Motor de cena (public/js/cena.js)

- [x] 2.1 `window.CENA` com `CENA.iniciar(svgEl, opcoes)`: loop rAF que interpola o `viewBox` (deriva ambiente + alvo de ponteiro/giroscópio) e aplica `transform` por camada via `data-parallax`
- [x] 2.2 Partículas pooled (conjunto fixo de `<use>/<circle>`) para motas mágicas; escritas no DOM em lote por frame
- [x] 2.3 `IntersectionObserver` pausa/retoma o loop conforme a cena entra/sai da viewport; `matchMedia('(prefers-reduced-motion)')` desliga deriva/parallax/partículas (cena estática)

## 3. Pad ambiente (public/js/som.js)

- [x] 3.1 `SOM.ambienteIniciar(perfil)`: 2–3 osciladores detunados + LFO lento + lowpass num `ambienteGain` próprio → `masterGain` (herda MUTE); `SOM.ambienteParar()` com fade-out; `SOM.ambienteVolume(v)`
- [x] 3.2 Sob `prefers-reduced-motion`, sem LFO perceptível (só tom base); adicionar SFX `pressStart`
- [x] 3.3 Atualizar o `stub()` de `som.js` com os novos métodos (no-op) para páginas sem Web Audio

## 4. Cena de prova: splash cinemático

- [x] 4.1 `app/views/layout/splash.php`: trocar o logo pixel pela `.cena-svg` curta (céu arcano + lua/glow + título subindo) + incluir `svg-defs.php`; manter auto-dismiss por `sessionStorage`
- [x] 4.2 Iniciar `CENA.iniciar` com perfil "push-in" (leve zoom de câmera por `viewBox`); garantir `cena.css`/`cena.js` carregados onde o splash aparece

## 5. Cena de prova: título da home

- [x] 5.1 `home/index.php`: substituir o `.hero` pela `.cena-svg` full-screen (camadas: céu+lua+estrelas → distantes c/ DOF → cidadela média → colinas/Vila → FX névoa/light-shaft/motas → vinheta) com logo/título/botões de entrada em HTML na `.cena-safe`
- [x] 5.2 Incluir `svg-defs.php`, `cena.css`, `som.js`, `cena.js`; iniciar `CENA.iniciar` + `SOM.ambienteIniciar` ao carregar e `ambienteParar`/desbloqueio no 1º gesto; manter história e Mestres ao rolar
- [x] 5.3 `public/css/style.css`: ajustar `.hero`/`.splash-*` para hospedar o SVG sem conflito (remover o que ficou redundante do splash pixel)

## 6. Verificação

- [x] 6.1 `php -l` nas views alteradas e `node --check` nos JS; servir `php -S localhost:8001`
- [ ] 6.2 Home: cena ocupa a tela em desktop largo e mobile/retrato (devtools), 3 planos legíveis, parallax/atmosfera/vinheta visíveis, câmera suave ~60fps
- [ ] 6.3 Splash cinemático aparece e some uma vez por sessão; áudio destrava no 1º gesto, pad ambiente toca baixo, clique/hover soam, MUTE silencia e persiste
- [ ] 6.4 `prefers-reduced-motion`: parallax/câmera/névoa/LFO desligam, cena estática e legível
- [ ] 6.5 Zero regressão: Fases 1 e 2 intactas; nenhuma cor fora dos tokens; nenhuma dependência nova
