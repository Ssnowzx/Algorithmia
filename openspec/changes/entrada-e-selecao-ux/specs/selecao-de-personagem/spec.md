## ADDED Requirements

### Requirement: Lore da classe em popup (sem quebrar o grid)

A origem do povo de cada classe SHALL ser exibida em um modal popup acionado por um botão,
NÃO por expansão inline. O modal SHALL conter o retrato ilustrado da classe (`hud-*`), o nome,
a espécie e o texto de lore, e SHALL ser fechável por botão ✕, clique fora ou tecla Esc. A
abertura do popup NÃO SHALL alterar a altura nem o alinhamento dos cards.

#### Scenario: Abrir a lore não desalinha os cards

- **WHEN** o jogador clica em "Origem do povo" de uma classe
- **THEN** um modal estilo carta abre com retrato, espécie e a lore daquela classe
- **AND** os cards do grid permanecem na mesma posição e altura

#### Scenario: Fechar o popup

- **WHEN** o jogador clica em ✕, clica fora do card ou aperta Esc
- **THEN** o modal fecha e o scroll da página é restaurado

### Requirement: Títulos da criação na fonte do jogo

Os rótulos "Nome do herói" e "Escolha sua classe" SHALL usar a fonte de título do jogo
(`var(--titulo)`, Pixelify Sans), centralizados como títulos de seção, sem estilo inline.

#### Scenario: Títulos com a tipografia do jogo

- **WHEN** a página de criação de herói é exibida
- **THEN** "Nome do herói" e "Escolha sua classe" aparecem em Pixelify Sans, centralizados

### Requirement: Grid de classes responsivo

A grade de classes SHALL ser responsiva via `auto-fit`/`minmax(min(100%, 270px), 1fr)`,
exibindo 1 card por linha no celular, 2 no tablet e 3 no desktop, sem estouro horizontal e sem
esticar os cards no celular.

#### Scenario: Um card por linha no celular

- **WHEN** a página é aberta em um celular
- **THEN** as cartas aparecem uma por linha, sem esticamento nem barra de rolagem horizontal

#### Scenario: Mais colunas em telas largas

- **WHEN** a largura disponível comporta 2 ou 3 cartas
- **THEN** o grid passa a 2 (tablet) ou 3 (desktop) colunas automaticamente
