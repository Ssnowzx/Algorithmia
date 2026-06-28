## ADDED Requirements

### Requirement: Medidor visual de Reputação

O perfil SHALL exibir a reputação do herói como um medidor no eixo
🤖 Singularidade (negativo) ↔ Disciplina (positivo), com um marcador na posição
`(reputacao + 100) / 2`%. É apresentação do dado existente — NÃO SHALL alterar o valor da
reputação nem qualquer regra.

#### Scenario: Marcador reflete a reputação

- **WHEN** o jogador abre o perfil com reputação 0
- **THEN** o marcador aparece no centro do medidor, rotulado "Aprendiz Neutro"

### Requirement: Progresso parcial das conquistas (endowed progress)

O perfil SHALL mostrar o progresso "X/Y" das conquistas CONTÁVEIS (Colecionador = itens/8,
Aprendiz Veterano = nível/5, Lenda Viva = nível/10), calculado on-the-fly a partir das
tabelas existentes, usando os MESMOS alvos da concessão (single source of truth) com clamp
atual≤alvo. A barra SHALL aparecer SOMENTE em conquista não-obtida e não-secreta. Conquistas
secretas NÃO SHALL vazar nenhum progresso. NÃO SHALL haver migration nem mudança de dado.

#### Scenario: Conquista contável bloqueada mostra X/Y

- **WHEN** o herói tem 2 itens distintos e a conquista "Colecionador" (8 itens) não foi obtida
- **THEN** o card mostra uma barra com "2/8"

#### Scenario: Conquista secreta não vaza progresso

- **WHEN** uma conquista é secreta e ainda não foi obtida
- **THEN** ela aparece como "??? (Secreta)" sem barra de progresso

#### Scenario: Conquista já obtida não mostra barra

- **WHEN** a conquista já foi desbloqueada
- **THEN** o card aparece normal, sem barra de progresso

### Requirement: Recap semanal no perfil

O perfil SHALL exibir um painel "Sua semana" com a atividade dos últimos 7 dias (desafios
respondidos, % de acerto, fases concluídas, usos de IA), agregada read-only de
`respostas_log`/`progresso_fases` por data. Quando não houver atividade na janela, SHALL
exibir um fallback amigável com link para começar uma fase. NÃO SHALL alterar dado/regra.

#### Scenario: Semana com atividade

- **WHEN** o herói respondeu desafios nos últimos 7 dias
- **THEN** o painel mostra os contadores (desafios, % acerto, fases, usos de IA)

#### Scenario: Semana sem atividade

- **WHEN** não houve nenhuma resposta/fase nos últimos 7 dias
- **THEN** o painel mostra o fallback convidando a começar uma fase
