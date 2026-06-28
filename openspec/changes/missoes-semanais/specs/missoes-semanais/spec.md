## ADDED Requirements

### Requirement: Missões semanais rotativas

O perfil SHALL exibir um conjunto de missões da semana (quantidade `MISSOES_POR_SEMANA`)
selecionadas de um pool (`MISSOES_SEMANAIS`) de forma **determinística pela data**, sem cron
nem reset manual. A mesma semana SHALL produzir sempre o mesmo conjunto; a virada da semana
SHALL trocar o conjunto e reiniciar o progresso.

#### Scenario: Conjunto estável na mesma semana

- **WHEN** o perfil é aberto duas vezes na mesma semana
- **THEN** as mesmas missões são exibidas, na mesma ordem

#### Scenario: Rotação entre semanas

- **WHEN** a semana muda
- **THEN** o conjunto de missões muda de acordo com o índice da semana

### Requirement: Progresso e conclusão derivados da semana corrente

Cada missão SHALL exibir progresso (atual/alvo + barra) calculado *on-the-fly* a partir da
atividade da **semana ISO corrente** (`respostas_log` / `progresso_fases`), e SHALL marcar-se
concluída quando o alvo é atingido. O painel SHALL resumir quantas missões foram concluídas.

#### Scenario: Missão contável em progresso

- **WHEN** uma missão pede 20 respostas e o herói respondeu 8 nesta semana
- **THEN** ela mostra 8/20 e não está concluída

#### Scenario: Missão concluída

- **WHEN** a atividade da semana atinge ou ultrapassa o alvo da missão
- **THEN** a missão é marcada como concluída (✓) e conta no resumo "X/N concluídas"

#### Scenario: Missão de precisão exige volume mínimo

- **WHEN** uma missão pede 80% de acerto com mínimo de respostas e o volume ainda não foi atingido
- **THEN** ela não está concluída, mesmo que a precisão atual seja alta

### Requirement: Missões são read-only, sem economia nem persistência

As missões SHALL ser apresentação derivada: NÃO concedem recompensa (ouro/XP/itens), NÃO
persistem estado e NÃO exigem migration. Se a avaliação falhar, o perfil SHALL omitir o painel
sem quebrar o resto da página.

#### Scenario: Sem recompensa ao concluir

- **WHEN** o herói conclui uma missão da semana
- **THEN** nenhum ouro/XP/item é creditado e nenhum dado é gravado por causa da missão

#### Scenario: Falha não quebra o perfil

- **WHEN** o serviço de missões fica indisponível
- **THEN** o painel de missões é omitido e o restante do perfil continua normal
