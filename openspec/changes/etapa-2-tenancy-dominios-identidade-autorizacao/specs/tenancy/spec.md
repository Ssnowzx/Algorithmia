## ADDED Requirements

### Requirement: Modelo compartilhado por linha para tenants
O sistema SHALL modelar `tenants` como entidade de organização, `tenant_domains` como
identificadores globais de acesso, `users` como identidades globais e `tenant_memberships`
como vínculo entre usuário e tenant. Os registros de domínio SHALL ser globalmente únicos
e SHALL suportar exclusão lógica ou status inativo.

#### Scenario: Tenant possui domínios globais e status
- **WHEN** um tenant é criado com um domínio principal
- **THEN** o domínio deve ser persistido como único e ativo
- **AND** a modelagem deve permitir ativar, inativar ou excluir logicamente o domínio

#### Scenario: Tenant-aware data possui tenant_id
- **WHEN** uma tabela de domínio futura é desenhada para dados por tenant
- **THEN** ela SHALL incluir `tenant_id` como chave de isolamento
- **AND** os índices SHALL contemplar consultas por tenant

### Requirement: Resolução de tenant por host confiável
O sistema SHALL resolver o tenant a partir do host confiável da requisição, normalizado
para minúsculas e sem ponto final. Hosts com protocolo, caminho, query string ou porta
SHALL be rejected. O host inexistente, inativo ou sem vínculo SHALL resultar em resposta
segura de tenant não encontrado.

#### Scenario: Resolver tenant por domínio ativo
- **WHEN** a requisição chega com um host válido e vinculado a um domínio ativo
- **THEN** o resolver SHALL localizar o tenant correspondente
- **AND** o contexto da requisição SHALL receber o tenant encontrado

#### Scenario: Rejeitar domínio inexistente
- **WHEN** o host confiável não possui domínio cadastrado
- **THEN** o sistema SHALL falhar de modo fechado com tenant não encontrado

#### Scenario: Rejeitar domínio inativo
- **WHEN** o host confiável está vinculado a um domínio inativo
- **THEN** o sistema SHALL falhar de modo fechado sem criar fallback para outro tenant

#### Scenario: Rejeitar host malformado
- **WHEN** o host contém protocolo, caminho, query string ou porta
- **THEN** o sistema SHALL rejeitar o host antes da resolução de tenant

### Requirement: Contexto CurrentTenant imutável por requisição
O sistema SHALL disponibilizar um `CurrentTenant` imutável ao restante da requisição após
a resolução bem-sucedida. A resolução SHALL ocorrer antes de qualquer consulta tenant-aware
e SHALL impedir continuidade quando não houver tenant válido. `/healthz` SHALL permanecer
fora da resolução de tenant.

#### Scenario: `/healthz` permanece global
- **WHEN** a aplicação processa `GET /healthz`
- **THEN** a rota SHALL responder sem depender de resolução de tenant

#### Scenario: CurrentTenant ausente bloqueia leitura tenant-aware
- **WHEN** uma operação tenant-aware é executada sem `CurrentTenant`
- **THEN** a requisição SHALL falhar de modo fechado
- **AND** nenhum fallback para todos os registros SHALL ocorrer

#### Scenario: Rotas globais e rotas tenant-aware são distintas
- **WHEN** a aplicação classifica uma rota
- **THEN** rotas globais SHALL ser tratadas fora do tenant
- **AND** rotas tenant-aware SHALL exigir contexto resolvido antes do acesso a dados

### Requirement: Isolamento por contexto de tenant e RLS
O sistema SHALL prever `SET LOCAL app.tenant_id` ou mecanismo equivalente antes de qualquer
consulta tenant-aware. A aplicação SHALL falhar quando o contexto não estiver presente e
SHALL não usar `withoutGlobalScopes()` como bypass de segurança.

#### Scenario: Consulta tenant A não acessa tenant B
- **WHEN** uma consulta tenant-aware tenta ler dados de outro tenant
- **THEN** a política de isolamento SHALL negar o acesso

#### Scenario: Admin global sem bypass automático
- **WHEN** um administrador global executa uma consulta tenant-aware
- **THEN** a consulta SHALL continuar sujeita ao contexto e às políticas RLS

#### Scenario: Falha de contexto fecha o acesso
- **WHEN** o contexto de tenant não foi estabelecido por requisição ou transação
- **THEN** a consulta tenant-aware SHALL falhar sem retornar registros de outros tenants

