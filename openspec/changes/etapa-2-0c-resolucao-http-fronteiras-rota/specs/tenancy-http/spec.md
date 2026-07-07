## ADDED Requirements

### Requirement: Host HTTP confiavel e normalizado
O sistema SHALL usar exclusivamente o host efetivo da requisicao HTTP para resolver tenant,
normalizando para minusculas, removendo um ponto final legitimo e aceitando porta apenas como
parte de desenvolvimento, sem persistir a porta como dominio. O sistema SHALL rejeitar URL
completa, protocolo, caminho, query string, fragmento, host vazio, host malformado e entrada
com `tenant_id`, `tenant`, `slug` ou headers equivalentes como fonte de tenant.

#### Scenario: Host ativo com porta de desenvolvimento
- **WHEN** a requisicao chega com `Host: tenant-a.algorithmia.test:8080`
- **THEN** o sistema SHALL normalizar o host para `tenant-a.algorithmia.test`
- **AND** SHALL seguir para resolucao de tenant sem armazenar a porta como dominio

#### Scenario: Host malformado e rejeitado
- **WHEN** o host contem protocolo, caminho, query string ou fragmento
- **THEN** o sistema SHALL responder `400 Bad Request`
- **AND** SHALL nao expor detalhes internos

#### Scenario: Header arbitario nao define tenant
- **WHEN** a requisicao envia `tenant_id`, `tenant`, `slug`, `X-Tenant-ID` ou cookie equivalente
- **THEN** o tenant resolvido SHALL permanecer inalterado

### Requirement: Tenant resolvido somente por dominio ativo
O sistema SHALL resolver tenant apenas para dominio ativo associado a tenant ativo.
Dominio inexistente, inativo, suspenso ou host global tentando rota tenant-scoped SHALL
resultar em `404 Not Found` sem revelar se o tenant existe.

#### Scenario: Resolver tenant por dominio ativo
- **WHEN** o host corresponde a um dominio ativo de tenant ativo
- **THEN** o sistema SHALL inicializar `CurrentTenant`
- **AND** SHALL permitir o processamento da rota tenant-scoped

#### Scenario: Rejeitar dominio inexistente
- **WHEN** o host nao possui dominio cadastrado
- **THEN** o sistema SHALL responder `404 Not Found`

#### Scenario: Rejeitar dominio inativo
- **WHEN** o host esta vinculado a dominio inativo
- **THEN** o sistema SHALL responder `404 Not Found`

#### Scenario: Rejeitar tenant suspenso
- **WHEN** o dominio aponta para tenant suspenso
- **THEN** o sistema SHALL responder `404 Not Found`

### Requirement: Contexto CurrentTenant e tenant_id por requisicao
O sistema SHALL disponibilizar `CurrentTenant` imutavel para a requisicao tenant-scoped e
SHALL manter `app.tenant_id` definido apenas durante o contexto transacional da rota.
O contexto SHALL ser limpo ao final da requisicao, inclusive em excecoes, e SHALL nao vazar
para requisicoes subsequentes no mesmo processo.

#### Scenario: Rota tenant-scoped disponibiliza contexto
- **WHEN** uma rota tenant-scoped e executada com dominio valido
- **THEN** `CurrentTenant` SHALL estar disponivel
- **AND** `app.tenant_id` SHALL estar definido na conexao ativa durante a execucao

#### Scenario: Excecao limpa contexto
- **WHEN** uma rota tenant-scoped lanca excecao
- **THEN** o sistema SHALL fazer rollback
- **AND** SHALL limpar `CurrentTenant` e o contexto transacional

#### Scenario: Requisicoes consecutivas nao vazam contexto
- **WHEN** duas requisicoes tenant-scoped distintas sao processadas no mesmo worker
- **THEN** o segundo request SHALL nao herdar `CurrentTenant` nem `app.tenant_id` do primeiro

