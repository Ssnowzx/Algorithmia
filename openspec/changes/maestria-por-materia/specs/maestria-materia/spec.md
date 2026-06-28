## ADDED Requirements

### Requirement: Faixa de maestria por matéria

O perfil SHALL classificar cada matéria de `ASSUNTOS` numa faixa de maestria
(Não iniciado → Iniciante → Aprendiz → Praticante → Especialista → Mestre),
derivada do **volume de acertos** E de um **piso de precisão sustentada** — não
da porcentagem bruta — calculada on-the-fly a partir de `respostas_log`, sem dado
novo nem migration. A faixa escolhida SHALL ser a maior cujos limites (mínimo de
acertos e piso de precisão) são satisfeitos.

#### Scenario: Baixo volume com 100% não vira domínio

- **WHEN** uma matéria tem 2 respostas, ambas corretas (100%)
- **THEN** a faixa é Iniciante
- **AND** não é Mestre nem Especialista

#### Scenario: Volume e precisão elevam a faixa

- **WHEN** uma matéria tem 16 respostas e 15 acertos (94%)
- **THEN** a faixa é Mestre

#### Scenario: Alto volume com baixa precisão fica travado

- **WHEN** uma matéria tem 20 respostas e 8 acertos (40%)
- **THEN** a faixa é Aprendiz (não sobe a Praticante só por acumular respostas)

#### Scenario: Matéria sem prática

- **WHEN** o herói nunca respondeu desafios de uma matéria
- **THEN** a faixa é Não iniciado

### Requirement: Progresso rumo ao próximo selo

Para faixas abaixo de Mestre, o perfil SHALL exibir uma barra e uma dica do que
falta para o próximo selo, refletindo o **fator mais atrasado** entre acumular
acertos e sustentar precisão (a barra não enche apenas por acertos quando a
precisão é o gargalo). A faixa Mestre SHALL indicar "Maestria máxima" sem barra
de progresso.

#### Scenario: Falta de acertos

- **WHEN** uma matéria está em Aprendiz com 3 acertos
- **THEN** a dica indica "Faltam 3 p/ Praticante"

#### Scenario: Precisão é o gargalo

- **WHEN** uma matéria tem acertos suficientes mas precisão abaixo do piso do próximo selo
- **THEN** a dica orienta a subir a precisão (ex.: "Precisão 40% → suba p/ 60% e vire Praticante")
- **AND** a barra reflete a precisão, não fica cheia só pelos acertos

#### Scenario: Faixa máxima

- **WHEN** uma matéria está em Mestre
- **THEN** o perfil mostra "Maestria máxima" e nenhuma barra de próximo selo

### Requirement: Maestria é apresentação read-only sem regressão

A maestria SHALL ser derivada e read-only: não cria nem altera dados, regra, XP,
ouro, reputação ou conquistas. O cabeçalho do painel SHALL resumir quantas
matérias estão dominadas (Especialista ou acima). Se o cálculo de maestria
falhar, o perfil SHALL cair no painel de domínio anterior (acertos/total) sem
quebrar.

#### Scenario: Resumo de dominadas

- **WHEN** o herói tem matérias em Especialista ou acima
- **THEN** o cabeçalho do painel mostra "X/8 dominadas"

#### Scenario: Sem prática registrada

- **WHEN** o herói ainda não respondeu nenhum desafio
- **THEN** o painel exibe a mensagem de incentivo para começar a praticar

#### Scenario: Fallback em falha

- **WHEN** o serviço de maestria fica indisponível
- **THEN** o perfil exibe o painel cru de domínio (acertos/total por matéria) sem erro
