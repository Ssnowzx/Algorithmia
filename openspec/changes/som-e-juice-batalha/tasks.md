## 1. Módulo de som (public/js/som.js)

- [x] 1.1 Criar `public/js/som.js` com `AudioContext` único, `masterGain` e roteamento `osc → [filtro] → gain → masterGain → destination`; documentar no topo a tabela de receitas por evento (freq/dur/envelope/filtro)
- [x] 1.2 Implementar desbloqueio do contexto (`SOM.unlock`): listeners `click`/`keydown`/`touchstart`/`touchend`, `resume()` via Promise, remoção única dos listeners; re-`resume()` se `state==='suspended'`
- [x] 1.3 Implementar mute/volume persistentes em `localStorage` (`SOM.mudo`, `SOM.volume`) com restauração do volume anterior ao desmutar
- [x] 1.4 Implementar funções de envelope anti-click (helper interno) e os timbres: `acerto`, `erro`, `danoHeroi`, `danoInimigo`, `combo(n)`, `especial`, `pocao`, `ouro`, `nivel`, `vitoria`, `derrota`, `fragmento`, `clique`, `modalAbrir`, `modalFechar`, `tic` (typewriter com polifonia limitada ~6 vozes)
- [x] 1.5 Expor `window.SOM` e garantir que toda chamada seja segura quando o contexto ainda não foi destravado (sem erro, sem áudio)

## 2. Sistema de juice (public/js/juice.js)

- [x] 2.1 Criar `public/js/juice.js` (`window.JUICE`) com canvas de partículas na arena, `devicePixelRatio` correto e object pool fixo (~200 slots)
- [x] 2.2 Implementar loop rAF com delta-time `clamp(dt,0.05)` que **para de reagendar** quando `activeCount===0 && trauma===0` e religa no próximo spawn/addTrauma
- [x] 2.3 Implementar emissores: faíscas de impacto (8–15), explosão do inimigo (20–40), coleta de ouro (6–12) voando ao HUD; com teto global ~150–200 (descartar excedente)
- [x] 2.4 Implementar screen-shake trauma-based no `.campo-batalha` (`offset=MAX*trauma²*rand`, MAX 6–12px / 1.5–3°, decay 1.0–1.5/s); `addTrauma(v)` por evento (hit 0.3–0.4, crítico 0.6, fim 0.8–1.0)
- [x] 2.5 Gating de `prefers-reduced-motion` via `matchMedia` no spawn e `addTrauma`, com listener de `change` para runtime

## 3. Estilos (public/css)

- [x] 3.1 Em `batalha.css`: melhorar `.flutuante` (arco via wrapper+filho, pop com back-out, fade nos últimos ~38%, variante `.critico` maior); usar só tokens de `:root`
- [x] 3.2 Em `batalha.css`: brilho do Especial e posicionamento/camada do `<canvas>` de partículas sobre a arena (pointer-events:none)
- [x] 3.3 Em `style.css`: estilo do controle de som/mute no header; bloco `@media (prefers-reduced-motion: reduce)` desligando shake/partículas/flutuações e mantendo fades essenciais

## 4. Integração nas views e disparos

- [x] 4.1 `footer.php`: incluir `<script src=js/som.js>` (global); `batalha/arena.php`: incluir `<script src=js/juice.js>`
- [x] 4.2 `header.php`: adicionar botão de mute/volume (acessível, com `aria-label`) ligado a `SOM.mudo`/`SOM.volume`
- [x] 4.3 `batalha.js`: disparar sons + juice em `tratarTurno` (acerto/erro, danoHeroi/danoInimigo + faíscas/flutuante, combo), Especial (som+brilho), poção (cura), Fragmento (timbre dissonante), e `mostrarResultado` (vitória/derrota: jingle + shake + explosão; ouro/nível: partículas) — apenas linhas aditivas, sem alterar o fluxo de turnos
- [x] 4.4 `ui.js`: tocar `clique`/`modalAbrir`/`modalFechar` ao abrir/fechar modal e em botões; `dialogo.js`: tocar `tic` por caractere (respeitando limite de polifonia); `app.js`/global: `SOM.unlock()` no 1º gesto e cliques de UI
- [x] 4.5 Guardas `window.SOM && ...` / `window.JUICE && ...` em todos os disparos para não quebrar páginas sem os módulos

## 5. Verificação

- [ ] 5.1 Rodar `php -S localhost:8001` e testar: áudio destrava no 1º clique, cada evento toca seu timbre, mute persiste entre páginas
- [ ] 5.2 Testar juice na arena (flutuantes, faíscas, explosão, shake) a 60fps; conferir que o loop rAF para quando a arena está parada
- [ ] 5.3 Ativar `prefers-reduced-motion` e confirmar que shake/partículas/flutuações desligam mantendo feedback essencial
- [ ] 5.4 Confirmar zero regressão: regras de batalha, combo, especial, poção, fugir, reputação, Fragmento e ordem vitória/derrota intactos; nenhuma cor fora dos tokens de `:root`
