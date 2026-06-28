## ADDED Requirements

### Requirement: Catálogo de mestres sem duplicatas

A migração SHALL garantir que o catálogo `mestres` tenha **um registro por `svg_slug`** (o de
menor id), removendo cópias geradas por seed repetido e **repointando as fases** para o id
mantido. SHALL ser idempotente.

#### Scenario: Banco duplicado é limpo

- **WHEN** a migração roda num banco com `mestres` duplicado (seeds aplicados 2×)
- **THEN** sobra exatamente um mestre por `svg_slug` (o de menor id)
- **AND** todas as fases continuam vinculadas ao mestre mantido

#### Scenario: Banco já limpo não muda

- **WHEN** a migração roda num banco sem duplicatas
- **THEN** nenhuma linha é apagada nem alterada (idempotente)
