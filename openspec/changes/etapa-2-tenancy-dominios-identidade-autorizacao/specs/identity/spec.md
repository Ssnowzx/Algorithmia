## ADDED Requirements

### Requirement: Identidade global de usuários
O sistema SHALL tratar `users` como identidades globais da plataforma, com e-mail único
global e senha armazenada exclusivamente com hash moderno do Laravel. A identidade SHALL
ser independente de tenant.

#### Scenario: Usuário pertence a dois tenants
- **WHEN** uma mesma pessoa possui memberships em dois tenants
- **THEN** a identidade global SHALL permanecer única
- **AND** os vínculos SHALL ser representados por memberships separadas

### Requirement: Fluxos de autenticação seguros
O sistema SHALL prever autenticação web com proteção contra enumeração de usuários,
session fixation e tentativas repetidas de login. A plataforma SHALL também prever
verificação de e-mail, redefinição de senha e sessão web segura.

#### Scenario: Credenciais inválidas não enumeram usuário
- **WHEN** um login falha por e-mail ou senha incorretos
- **THEN** a resposta SHALL ser segura e não revelar se o usuário existe

#### Scenario: Login renova a sessão
- **WHEN** um login é bem-sucedido
- **THEN** a sessão SHALL ser regenerada para evitar session fixation

#### Scenario: Tentativas repetidas sofrem limitação
- **WHEN** a mesma origem excede o limite de tentativas de login
- **THEN** o sistema SHALL aplicar throttling ou bloqueio temporário

### Requirement: Verificação e recuperação de acesso
O sistema SHALL prever verificação de e-mail e redefinição de senha sem expor segredos
em respostas ou logs. Eventos relevantes de autenticação SHALL ser auditáveis.

#### Scenario: E-mail precisa ser verificado
- **WHEN** uma conta é criada ou recuperada sem e-mail verificado
- **THEN** o sistema SHALL manter o estado de verificação pendente até confirmação

#### Scenario: Redefinição de senha é segura
- **WHEN** o usuário solicita redefinição de senha
- **THEN** o sistema SHALL emitir fluxo seguro sem revelar credenciais ou tokens brutos

#### Scenario: Eventos de autenticação são auditáveis
- **WHEN** ocorrem login, logout, falha de autenticação ou redefinição de senha
- **THEN** o sistema SHALL permitir auditoria desses eventos

### Requirement: Seleção de abordagem Laravel para web e API futura
O sistema SHALL adotar uma abordagem Laravel que favoreça web primeiro, com espaço para API
futura sem acoplar autenticação à resolução de tenant. A decisão SHALL ser documentada
antes da implementação.

#### Scenario: Abordagem suporta web e futuro API
- **WHEN** a arquitetura de identidade é revisada
- **THEN** a solução SHALL cobrir sessão web agora e permitir evolução para API depois

