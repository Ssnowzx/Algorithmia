# Design — Evolução de UX (apresentação)

## Princípios
- **Melhorar sem piorar:** toda mudança é aditiva e de apresentação; no pior caso o indicador
  some, o jogo nunca regride. Nada de mexer em perguntas/batalha/XP/ouro/reputação.
- **Respeita `prefers-reduced-motion`** em todo movimento (catch-all global + opt-in).
- **Degradação graciosa:** features novas usam só APIs amplamente suportadas (o piso já era
  Safari 16.2+ por causa de `color-mix`); onde não houver suporte, a navegação/render normal
  continua valendo.

## Decisões-chave

### Navegação (rail desktop / barra mobile)
- Posições aprovadas no protótipo (`docs/prototipo/`): rail à esquerda no desktop, barra
  inferior no mobile — **nunca os dois juntos**.
- A top bar (`.topo`) com HUD do herói + som + ficha é **preservada**; só os 5 setores
  migram para `.nav-app`. `body` vira CSS grid só quando o rail existe — via
  `body:has(.nav-app)` (sem tocar no PHP do body).
- A top bar é full-width; o rail fica ABAIXO dela e gruda ao rolar usando
  `top: var(--topo-h)`, onde `--topo-h` é a altura real da top bar medida por JS
  (ResizeObserver em `header.php`), com default no CSS — sem flash, robusto a zoom/resize.

### Animação
- **View Transitions cross-document** (`@view-transition { navigation: auto }`). `.topo` e
  `.nav-app` recebem `view-transition-name` para PERSISTIR (só o conteúdo faz cross-fade).
- Barras "enchem" com `scaleX(0→1)` — **CSS puro à prova de falha**: o estado final é o
  `width:X%` (scaleX(1)); se a animação não rodar, a barra já mostra o valor certo. HUD
  persistente fica de fora (não re-anima a cada navegação).
- Confete (`public/js/celebracao.js`) é um canvas full-screen autônomo, auto-limpo
  (com timeout de segurança p/ aba em background), exposto como `window.celebrar()`.

### Tokens / acessibilidade
- `--cor-*` viram alias unidirecional de `--*` (fonte única em `style.css`); motion tokens
  `--dur-*`/`--ease-*`. `color-mix` migrado de `srgb` para `oklab` (mesmo suporte, blends
  menos "lavados"). `:focus-visible` global tokenizado; `aria-current` no nav.

### Anti-CLS (imagens)
- `dimensoesImagem()` (getimagesize memoizado) injeta `width/height` intrínsecos em `svg()`
  e `marcaHtml()`. Intrínseco = natural → reserva a proporção sem mudar o render.

### Right-size de imagens (`tools/imagens/right_size_imagens.py`, idempotente)
- Reduz a RESOLUÇÃO das `.webp` ilustradas ao maior tamanho de exibição real (×~2.3 retina):
  mapas 384 · inimigos 640 · mestres 720 · atores 900 · herois 720. `fundos/` (full-bleed)
  ficam. Itens (PNG sem webp) ganham um webp menor (cap 600). **A arte não muda**; PNGs
  originais ficam em `docs/evolucao-visual` (source/fallback) — repo enxugado.

### Gamificação sem dinâmica
- Medidor de Reputação: posição = `(rep+100)/2`%, eixo 🤖 Singularidade (−) ↔ Disciplina (+).
- Endowed progress: `ConquistaService::progressoParcial()` lê on-the-fly (inventário, nível)
  usando os MESMOS alvos da concessão (single source of truth), com clamp atual≤alvo e
  fallback `[]`; barra só em conquista contável, NÃO-obtida e **NÃO-secreta** (segredos não
  vazam). Recap semanal: `resumoSemana()` agrega `respostas_log`/`progresso_fases` por data
  (7 dias), com fallback amigável.

## Alternativas consideradas (não implementadas)
- **Ligas/cohorts** no ranking (exige reset semanal/cron; ganho correlacional) e **streak**
  (efeito causal pequeno + risco documentado de ansiedade — contraria "melhorar sem piorar").
  Ficam para uma próxima etapa, com decisão explícita.
