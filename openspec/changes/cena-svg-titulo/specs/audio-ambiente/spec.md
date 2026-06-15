## ADDED Requirements

### Requirement: Pad ambiente procedural

O jogo SHALL oferecer uma trilha ambiente em loop gerada proceduralmente (osciladores +
filtro, sem arquivos de áudio), exposta como `SOM.ambienteIniciar(perfil)` e
`SOM.ambienteParar()`, com ganho próprio independente dos efeitos.

#### Scenario: Ambiente toca em loop sem arquivos

- **WHEN** uma cena inicia o ambiente
- **THEN** um pad suave em loop é sintetizado por osciladores (sem arquivos externos)
- **AND** seu volume é controlado por um ganho separado do volume de efeitos

#### Scenario: Ambiente para ao sair da cena

- **WHEN** a cena que iniciou o ambiente é encerrada/abandonada
- **THEN** o ambiente é interrompido sem estourar outros sons

### Requirement: Ambiente respeita MUTE e movimento reduzido

O ambiente SHALL respeitar o MUTE persistente (via o `GainNode` mestre) e SHALL evitar
modulação de movimento sob `prefers-reduced-motion`.

#### Scenario: MUTE silencia o ambiente

- **WHEN** o usuário ativa o MUTE
- **THEN** o ambiente fica inaudível junto com os efeitos
- **AND** desmutar restaura o ambiente

#### Scenario: Movimento reduzido simplifica o ambiente

- **WHEN** `prefers-reduced-motion: reduce` está ativo
- **THEN** o ambiente evita modulação perceptível de movimento (LFO), mantendo só o tom base
