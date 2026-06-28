## ADDED Requirements

### Requirement: Domínio por região derivado da perfeição da jornada

O perfil SHALL exibir, para cada região (mestre que governa fases), um estado de domínio
derivado das fases **jogáveis** daquela região e das estrelas obtidas: **A explorar** (nenhuma
fase concluída), **Em jornada** (algumas), **Conquistada** (todas concluídas) e **Dominada**
(todas com 3 estrelas). O cálculo SHALL ser read-only (de `fases` + `progresso_fases`) e SHALL
considerar apenas os mestres efetivamente referenciados por fases (robusto à duplicação do
catálogo de mestres).

#### Scenario: Região não iniciada

- **WHEN** o herói não concluiu nenhuma fase de uma região
- **THEN** a região aparece como "A explorar"

#### Scenario: Região em progresso

- **WHEN** o herói concluiu algumas (não todas) as fases jogáveis da região
- **THEN** a região aparece como "Em jornada" e indica quantas fases faltam

#### Scenario: Região conquistada mas não perfeita

- **WHEN** todas as fases jogáveis estão concluídas, mas nem todas com 3 estrelas
- **THEN** a região aparece como "Conquistada" e orienta a perfeccionar para dominar

#### Scenario: Região dominada

- **WHEN** todas as fases jogáveis estão concluídas com 3 estrelas (sem erros e sem IA)
- **THEN** a região aparece como "Dominada"

### Requirement: Resumo de domínio e título culminante

O painel SHALL resumir quantas regiões estão dominadas (de 5) e SHALL conceder, de forma
apenas visual, o título culminante ("Mestre dos Cinco") quando todas as regiões estão
dominadas.

#### Scenario: Contagem de dominadas

- **WHEN** o herói domina 2 das 5 regiões
- **THEN** o cabeçalho mostra "2/5 dominadas"

#### Scenario: Título ao dominar tudo

- **WHEN** todas as 5 regiões estão dominadas
- **THEN** o perfil exibe o título "Mestre dos Cinco"

### Requirement: Domínio de regiões é read-only e não regride o perfil

A feature SHALL ser apresentação derivada: NÃO concede as conquistas de mestre, NÃO escreve no
banco e NÃO exige migration. Se o cálculo falhar, o perfil SHALL omitir o painel sem quebrar o
restante da página.

#### Scenario: Sem escrita ao exibir

- **WHEN** o painel de domínio é renderizado
- **THEN** nenhuma conquista é concedida e nenhum dado é gravado

#### Scenario: Falha não quebra o perfil

- **WHEN** o serviço de domínio de regiões fica indisponível
- **THEN** o painel é omitido e o restante do perfil continua normal
