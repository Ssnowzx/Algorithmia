## ADDED Requirements

### Requirement: Rotas globais e tenant-scoped sao distintas
O sistema SHALL manter rotas globais fora da resolucao de tenant e SHALL definir um grupo
tenant-scoped que exige resolucao previa por host. `/healthz` SHALL permanecer global e
independente de tenant.

#### Scenario: `/healthz` permanece global
- **WHEN** a requisicao acessa `GET /healthz`
- **THEN** o sistema SHALL responder sem exigir dominio institucional
- **AND** SHALL nao criar `CurrentTenant`

#### Scenario: Rota tenant-scoped exige tenant
- **WHEN** a requisicao acessa uma rota tenant-scoped com host global
- **THEN** o sistema SHALL responder `404 Not Found`

### Requirement: Resposta institucional minima
O sistema SHALL expor ao menos uma rota interna tenant-scoped de prova tecnica que permita
verificar o fluxo HTTP sem expor `tenant_id`, memberships ou dados sensiveis. O retorno
SHALL ser genérico e nao indicar identificadores internos.

#### Scenario: Rota interna retorna sucesso generico
- **WHEN** uma requisicao tenant-scoped valida e processada
- **THEN** a rota SHALL responder com `200 OK`
- **AND** SHALL retornar apenas um payload generico de sucesso

