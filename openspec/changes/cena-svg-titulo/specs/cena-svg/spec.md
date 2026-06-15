## ADDED Requirements

### Requirement: Cena SVG full-screen responsiva

O jogo SHALL renderizar cenas como um SVG raiz que ocupa a tela inteira
(`width:100vw; height:100dvh`) com `viewBox` em unidades de mundo fixas e
`preserveAspectRatio="xMidYMid slice"` para arte full-bleed. O conteúdo essencial (título,
botões) SHALL permanecer dentro de uma safe-zone central.

#### Scenario: Cena preenche qualquer proporção

- **WHEN** a cena é exibida em telas de proporções diferentes (largo, retrato)
- **THEN** a arte preenche a viewport sem distorcer (slice corta o excesso)
- **AND** título e botões permanecem visíveis dentro da safe-zone

### Requirement: Camadas de parallax e câmera por viewBox

A cena SHALL organizar a arte em grupos `<g>` empilhados por profundidade e SHALL animar a
câmera interpolando o `viewBox` via `requestAnimationFrame`, com camadas movidas por
`transform` em velocidades diferentes (parallax). Apenas `transform`/`opacity` são animados.

#### Scenario: Deriva de câmera com parallax

- **WHEN** a cena de título está ativa e o movimento é permitido
- **THEN** a câmera deriva suavemente e as camadas se deslocam em velocidades distintas
- **AND** nenhuma propriedade que cause reflow (x/y/width geométrico) é animada por frame

#### Scenario: Loop para quando ocioso ou fora de viewport

- **WHEN** a cena sai da viewport ou não há nada a animar
- **THEN** o loop de `requestAnimationFrame` deixa de ser reagendado
- **AND** volta a rodar quando a cena reentra na viewport

### Requirement: Defs compartilhados e reuso

Gradientes, filtros, máscaras e `<symbol>`s SHALL ser definidos uma vez em um bloco `<defs>`
compartilhado e instanciados por referência (`url(#...)`, `<use href>`). O container de defs
NÃO SHALL usar `display:none` (que quebra a renderização de gradientes/filtros referenciados).

#### Scenario: Defs referenciados renderizam

- **WHEN** uma cena referencia um gradiente/filtro/símbolo do bloco de defs
- **THEN** ele é renderizado corretamente
- **AND** o container de defs não está em `display:none`

### Requirement: Atmosfera por filtros com orçamento de performance

A cena SHALL usar filtros SVG (névoa via `feTurbulence`, profundidade via `feGaussianBlur`,
glow via blur+`feMerge`, luz via gradiente+`mask`) com região de filtro limitada e
`numOctaves` baixo, SEM animar `baseFrequency` de filtro full-screen por frame.

#### Scenario: Névoa em movimento sem custo por frame

- **WHEN** a névoa precisa parecer viva
- **THEN** o movimento vem de transladar uma camada texturizada (ou animação lenta)
- **AND** não de animar `baseFrequency` de um filtro que cobre a tela a cada frame

### Requirement: Legibilidade em três planos e tokens de cor

A cena SHALL manter três planos legíveis (fundo dessaturado/desfocado → meio → frente nítida
e saturada), com título e botões como os elementos de maior contraste. Todo estilo SHALL usar
somente os tokens de `:root` (gradientes SVG via `stop-color: var(--token)`).

#### Scenario: Título é o maior contraste

- **WHEN** a cena de título é exibida
- **THEN** o título e os botões de entrada têm o maior contraste da tela
- **AND** nenhuma cor fora dos tokens de `:root` é introduzida

### Requirement: Acessibilidade de movimento

Sob `prefers-reduced-motion`, a cena SHALL desligar parallax, deriva de câmera, névoa e
flutuações, mantendo a arte estática e legível. A preferência SHALL ser respeitada em CSS e no
loop JS.

#### Scenario: Movimento reduzido deixa a cena estática

- **WHEN** `prefers-reduced-motion: reduce` está ativo
- **THEN** parallax, câmera e atmosfera animada não são executados
- **AND** a cena permanece visível e legível
