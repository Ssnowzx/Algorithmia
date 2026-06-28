# Tasks — Evolução de UX (apresentação)

> Todas concluídas e mergeadas na `main` (PR #1, 15 commits) + recap (PR #2).

## 1. Design system / Onda 0  *(707c0b8)*
- [x] 1.1 Unificar tokens: `--cor-*`/sombras como alias de `--*` (fonte única em style.css)
- [x] 1.2 Tokens de motion (`--dur-*`/`--ease-*`) + `--cor-foco`
- [x] 1.3 `:focus-visible` global tokenizado (teclado) sem duplicar anel nos campos
- [x] 1.4 `aria-current="page"` no nav (header.php) com destaque consistente
- [x] 1.5 Alvos de toque `min-height:44px` no mobile
- [x] 1.6 `width/height` intrínseco via `dimensoesImagem()` em `svg()`/`marcaHtml()` (anti-CLS)
- [x] 1.7 `color-mix` `srgb`→`oklab`; catch-all `prefers-reduced-motion`
- [x] 1.8 Dedup do `@import` de fontes (remove de style.css; `<link>` em registro/criar-personagem)

## 2. Navegação app  *(39a017d, 2c88e00, 8a33ca2)*
- [x] 2.1 `public/css/shell.css`: rail (desktop ≥1000px) + barra inferior (mobile)
- [x] 2.2 `.nav-app` no header.php (5 setores, ícones SVG, aria-current); setores saem da top bar
- [x] 2.3 `body` vira grid via `body:has(.nav-app)`; top bar full-width; rail abaixo (sticky `--topo-h` medido por JS)
- [x] 2.4 Logo maior (clamp 48–68px; remove o teto do mapa.css); rail transparente sem borda
- [x] 2.5 Barra mobile translúcida (frosted)

## 3. Animação (Fase B)  *(f9e8bda, 551f99c, 4f78b8c)*
- [x] 3.1 View Transitions cross-document (`@view-transition`) + `view-transition-name` em `.topo`/`.nav-app`
- [x] 3.2 `public/js/celebracao.js`: confete global (`window.celebrar`), reduce-aware, auto-limpo
- [x] 3.3 Auto-disparo em flash de conquista/compra; confete de level-up no batalha.js
- [x] 3.4 Barras "enchem" (scaleX 0→1, fail-safe; HUD excluído)

## 4. Perfil & gamificação (sem dinâmica)  *(cf4e655, 3b876ec, 0571d1e)*
- [x] 4.1 Medidor visual de Reputação (Disciplina↔Singularidade) + stats em tiles
- [x] 4.2 Inventário em grade de tiles compactos (não "em lista")
- [x] 4.3 `ConquistaService::progressoParcial()` + barra X/Y nas conquistas contáveis não-secretas
- [x] 4.4 Recap "Sua semana" (`RespostaLog::resumoSemana` + `ProgressoFase::resumoSemana`), read-only, com fallback

## 5. Mapa  *(71c4fed, 4f78b8c)*
- [x] 5.1 Barra de progresso da jornada (goal-gradient)
- [x] 5.2 Chip "🎯 Próximo: <fase>" (1ª fase liberada não concluída)

## 6. Loja  *(85324b6)*
- [x] 6.1 Migration idempotente `20260628-dedupe-itens.sql` (mantém menor id; repointa refs; sem órfãos)

## 7. Performance de imagens  *(f86093f, b97642e, bfcf8db)*
- [x] 7.1 `tools/imagens/right_size_imagens.py`: right-size das webp ilustradas (−12 MB)
- [x] 7.2 Gera webp dos itens a partir do PNG (loja/inventário; −34 MB servidos)
- [x] 7.3 Remove PNGs de itens de public/img (source preservado em docs/evolucao-visual; −35 MB repo)
