## ADDED Requirements

### Requirement: Síntese de áudio sem dependências

O jogo SHALL gerar todo o áudio proceduralmente com a Web Audio API (osciladores +
`GainNode` + `BiquadFilterNode`), sem bibliotecas e sem arquivos de áudio externos. O
módulo SHALL ser exposto como `window.SOM` em `public/js/som.js` e usar um único
`AudioContext` para toda a sessão.

#### Scenario: Nenhuma dependência nova adicionada

- **WHEN** o módulo de som é carregado
- **THEN** ele usa apenas APIs nativas do navegador (`AudioContext`/`webkitAudioContext`)
- **AND** não há nenhum `<script>` de terceiros, pacote npm ou arquivo `.mp3`/`.wav`/`.ogg`
  adicionado ao projeto

#### Scenario: Um único contexto reaproveitado

- **WHEN** vários sons são disparados ao longo da sessão
- **THEN** todos compartilham o mesmo `AudioContext` e o mesmo `GainNode` mestre
- **AND** cada som cria seu próprio oscilador, que é descartado com `stop()` após tocar

### Requirement: Desbloqueio do contexto no primeiro gesto

Como os navegadores iniciam o `AudioContext` em estado `suspended`, o módulo SHALL
retomá-lo (`resume()`) no primeiro gesto do usuário e remover os listeners de desbloqueio
apenas após a Promise de `resume()` resolver.

#### Scenario: Áudio destravado ao interagir

- **WHEN** o usuário realiza o primeiro `click`, `keydown`, `touchstart` ou `touchend`
- **THEN** o `AudioContext` é retomado para o estado `running`
- **AND** os listeners de desbloqueio são removidos uma única vez

#### Scenario: Nenhum som antes do gesto

- **WHEN** uma chamada de som ocorre antes de qualquer gesto do usuário
- **THEN** ela não lança erro e simplesmente não produz áudio audível

### Requirement: Volume e mute persistentes

O jogo SHALL oferecer um controle de mute (e volume) por meio de um `GainNode` mestre,
com o estado persistido em `localStorage` para valer entre as páginas PHP.

#### Scenario: Mute persiste entre páginas

- **WHEN** o usuário ativa o mute e navega para outra página
- **THEN** o som permanece mudo na nova página
- **AND** o estado de mute é lido de `localStorage` ao carregar

#### Scenario: Mute zera a saída sem destruir o contexto

- **WHEN** o mute está ativo
- **THEN** o ganho do `GainNode` mestre vai a zero
- **AND** o `AudioContext` e os nós continuam existindo (desmutar restaura o volume anterior)

### Requirement: Mapa de eventos do jogo para timbres

O módulo SHALL expor funções nomeadas que tocam um timbre curto e distinto para cada
evento do jogo: acerto, erro, dano recebido, dano causado, combo, especial, poção/cura,
coletar ouro, subir de nível, vitória, derrota, Fragmento da IA, clique de UI,
abrir/fechar modal e tic do diálogo.

#### Scenario: Combo sobe de tom a cada acerto encadeado

- **WHEN** o som de combo é tocado com um índice de combo crescente
- **THEN** a frequência do timbre sobe a cada acerto encadeado
- **AND** respeita um teto de variação de tom

#### Scenario: Fragmento da IA tem timbre dissonante

- **WHEN** o usuário cede ao Fragmento da IA
- **THEN** um timbre dissonante/corrompido é tocado, distinto do som de acerto normal

#### Scenario: Tic do diálogo é barato e limitado

- **WHEN** o efeito de máquina de escrever digita caracteres em alta frequência
- **THEN** o tic é um som muito curto e de baixo volume
- **AND** a polifonia é limitada para não saturar a CPU (descartando disparos excedentes)
