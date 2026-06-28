## ADDED Requirements

### Requirement: Navegação em rail e barra inferior

O jogo SHALL exibir os 5 setores principais (Mapa, Inventário, Loja, Perfil, Ranking) como um
RAIL lateral à esquerda no desktop (≥1000px) e como uma BARRA inferior fixa no mobile
(<1000px) — nunca os dois ao mesmo tempo. A top bar (HUD do herói, som, ficha) SHALL ser
preservada; apenas os setores migram para o `.nav-app`. As páginas standalone (auth/home)
NÃO SHALL receber rail/barra.

#### Scenario: Desktop mostra o rail

- **WHEN** um jogador logado abre uma página de jogo em viewport ≥1000px
- **THEN** os 5 setores aparecem num rail vertical à esquerda, abaixo da top bar full-width
- **AND** não há barra inferior

#### Scenario: Mobile mostra a barra inferior

- **WHEN** a mesma página é aberta em viewport <1000px
- **THEN** os 5 setores aparecem numa barra inferior fixa (zona do polegar), com alvos ≥44px
- **AND** não há rail lateral

#### Scenario: Setor atual destacado

- **WHEN** o jogador está numa seção (ex.: Loja)
- **THEN** o item correspondente recebe `aria-current="page"` e o destaque visual

### Requirement: Transição suave entre páginas

O jogo SHALL aplicar uma transição cross-fade do conteúdo ao navegar entre páginas quando o
navegador suportar View Transitions e o usuário não pedir menos movimento, mantendo a top bar
e o rail/barra PERSISTENTES (sem piscar). Onde a API não existir, a navegação normal SHALL
valer.

#### Scenario: Cross-fade ao trocar de seção

- **WHEN** o jogador navega de uma seção para outra (movimento permitido, browser compatível)
- **THEN** o conteúdo faz um cross-fade enquanto a top bar e o rail/barra permanecem no lugar

#### Scenario: Reduced-motion desliga a transição

- **WHEN** o usuário tem `prefers-reduced-motion: reduce`
- **THEN** a troca de página é instantânea, sem animação de transição
