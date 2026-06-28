## Why

A auditoria de produto (deep research em `docs/auditoria-ux/`) apontou que o jogo, embora
visualmente bonito, deixava valor de UX/retenção e performance na mesa, **sem precisar mexer
em nenhuma regra/dinâmica**:

- **Navegação** ficava só numa faixa horizontal no topo; sem o padrão de app (rail no
  desktop / barra inferior no mobile) que acelera a descoberta (NN/g) e dá sensação de app.
- **Sem transições** entre páginas (navegação "dura"); animações esparsas e timings/cores
  cravados, dois namespaces de design token, foco de teclado invisível, imagens sem
  `width/height` (CLS).
- **Reputação invisível** (eixo Disciplina↔IA existia só como número), conquistas multi-passo
  100% binárias ("incentivos vazios"), progresso da jornada sem barra/objetivo explícito.
- **Loja exibia itens duplicados** (catálogo semeado 2× em alguns bancos).
- **Imagens ilustradas servidas a 1280–1536px** em slots de 88–320px (5–30× maiores) — maior
  alavanca de peso/LCP; itens eram PNG de ~1,8 MB sem WebP.

## What Changes

Apenas **apresentação / acessibilidade / performance** — não toca em perguntas, batalha,
XP, ouro, reputação (valor) nem em qualquer regra de jogo. Tudo verificado tela a tela.

- **Design system (Onda 0):** tokens unificados (alias unidirecional), `:focus-visible`
  global, `aria-current` no menu, alvos de toque 44px, `color-mix` em oklab, tokens de
  motion + catch-all de `prefers-reduced-motion`, `width/height` intrínsecos nas imagens
  (anti-CLS), dedup do `@import` de fontes.
- **Navegação app:** RAIL lateral à esquerda no desktop (≥1000px) + BARRA inferior no
  mobile (<1000px); top bar full-width preservando HUD/som/ficha; **View Transitions**
  cross-document entre páginas.
- **Perfil & gamificação (sem dinâmica):** medidor visual de Reputação (Disciplina↔
  Singularidade); progresso parcial das conquistas contáveis (endowed progress / X/Y);
  recap semanal "Sua semana"; stats em tiles.
- **Mapa:** barra de progresso da jornada + chip "Próximo objetivo"; barras que "enchem"
  ao aparecer; celebração de confete fora da arena (conquista/compra/level-up).
- **Loja:** correção da duplicação de itens (migration idempotente que vale p/ produção).
- **Performance:** right-size das imagens (`tools/right_size_imagens.py`) — ≈ −46 MB no que
  é servido, **mesma arte**; PNGs originais preservados em `docs/evolucao-visual`.
