## ADDED Requirements

### Requirement: Partículas ambientais por bioma

Cada região do mapa e a arena de batalha SHALL exibir partículas ambientais coerentes com o
bioma (ex.: folhas na Floresta, brasas na Montanha, pacotes/luz na Torre), implementadas em
CSS declarativo (sem `requestAnimationFrame` permanente), atrás do conteúdo interativo.

#### Scenario: Bioma define o efeito ambiental

- **WHEN** uma região/arena de um bioma é exibida
- **THEN** as partículas ambientais correspondem ao tema daquele bioma
- **AND** ficam atrás dos nós/combatentes e não interceptam cliques

### Requirement: Profundidade e parallax leve no mapa

As seções de região SHALL ter profundidade por camadas (fundo + névoa/brilho) com movimento
leve, animando apenas `transform`/`opacity`.

#### Scenario: Região tem camadas com profundidade

- **WHEN** uma região é renderizada no mapa
- **THEN** ela apresenta camadas de profundidade (fundo + brilho/névoa) sobre o fundo do bioma
- **AND** nenhuma propriedade que cause reflow (top/left/width) é animada

### Requirement: Transição visível ao percorrer regiões

Ao rolar o mapa, cada região SHALL ser revelada com uma transição suave quando entra na
viewport, via `IntersectionObserver`.

#### Scenario: Região revela ao entrar na tela

- **WHEN** o usuário rola o mapa e uma região entra na viewport
- **THEN** ela é revelada com uma transição suave (uma única vez)

### Requirement: Fundo da arena por bioma

A arena de batalha SHALL usar o fundo da região da fase (derivado do mestre via
`fundoRegiao()`), mantendo o chão em perspectiva e o brilho arcano já existentes. Quando a
fase não tiver mestre/região, SHALL recair para um fundo padrão.

#### Scenario: Arena reflete a região da fase

- **WHEN** uma batalha de uma fase com mestre/região inicia
- **THEN** o fundo da arena é o do bioma daquela região
- **AND** o chão em perspectiva e o brilho arcano permanecem visíveis

#### Scenario: Fase sem região usa fundo padrão

- **WHEN** uma fase não está associada a um mestre/região
- **THEN** a arena usa um fundo padrão sem erro

### Requirement: Acessibilidade e tokens de cor

Sob `prefers-reduced-motion`, o jogo SHALL desligar parallax, partículas ambientais e a
transição de região, mantendo os fundos estáticos legíveis. Todo estilo novo SHALL usar
somente os tokens de `:root` e a `--cor-regiao` já existentes.

#### Scenario: Movimento reduzido desliga ambientação

- **WHEN** `prefers-reduced-motion: reduce` está ativo
- **THEN** parallax, partículas ambientais e revelação de região não são executados
- **AND** os fundos por bioma continuam visíveis e legíveis

#### Scenario: Nenhuma cor nova hardcoded

- **WHEN** os cenários são estilizados
- **THEN** as cores vêm dos tokens de `:root` e de `--cor-regiao`
- **AND** nenhum hex solto fora dos tokens é introduzido
