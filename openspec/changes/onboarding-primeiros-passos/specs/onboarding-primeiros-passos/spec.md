## ADDED Requirements

### Requirement: Checklist de primeiros passos com progresso dotado

O perfil SHALL exibir um checklist de marcos iniciais cujo **primeiro item já vem concluído**
("Forjar seu herói", pois o personagem existe), dando ao recém-chegado a sensação de jornada
já iniciada (endowed progress). Os demais passos SHALL ser derivados read-only do estado do
jogador (fases concluídas, item equipado, conquista obtida).

#### Scenario: Head start no jogador novo

- **WHEN** um jogador recém-criado (sem fases, sem item equipado, sem conquista) abre o perfil
- **THEN** o checklist mostra 1 de 4 concluído (apenas "Forjar seu herói")

#### Scenario: Passos avançam com o jogo

- **WHEN** o jogador vence a primeira batalha, equipa um item e ganha uma conquista
- **THEN** os marcos correspondentes ficam marcados e a contagem reflete o avanço

### Requirement: Painel some para quem não é mais novato

O painel SHALL aparecer apenas enquanto o jogador é novato (`nível <= ONBOARDING_NIVEL_MAX`) e
houver passo incompleto, e SHALL desaparecer quando o jogador evolui de nível ou conclui todos
os passos. SHALL ser read-only (não concede recompensa nem grava nada) e, em falha, ser
omitido sem quebrar o perfil.

#### Scenario: Veterano não vê o painel

- **WHEN** o jogador está acima de `ONBOARDING_NIVEL_MAX`
- **THEN** o painel de primeiros passos não é exibido

#### Scenario: Checklist completo some

- **WHEN** o novato concluiu os 4 marcos
- **THEN** o painel não é mais exibido
