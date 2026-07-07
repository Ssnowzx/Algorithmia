## ADDED Requirements

### Requirement: Autenticação global por senha
O sistema SHALL autenticar `users` como identidades globais usando e-mail normalizado em
minúsculas e senha hash moderna do Laravel. O login SHALL ocorrer apenas no domínio do
tenant resolvido e SHALL falhar de forma genérica para credenciais inválidas, usuário
inexistente, membership ausente ou membership suspensa.

#### Scenario: Login bem-sucedido em tenant ativo
- **WHEN** o usuário informa credenciais válidas em um tenant com membership ativa
- **THEN** o sistema SHALL autenticar a sessão naquele domínio
- **AND** SHALL renovar o identificador de sessão

#### Scenario: Login não enumera usuário
- **WHEN** o e-mail não existe, a senha está incorreta ou a membership não está ativa
- **THEN** o sistema SHALL responder com erro genérico idêntico
- **AND** SHALL not reveal whether the user exists

#### Scenario: Login depende do tenant resolvido
- **WHEN** a requisição de login chega em host global, domínio desconhecido, domínio inativo
  ou tenant suspenso
- **THEN** o sistema SHALL negar a rota conforme as regras de tenant resolution

### Requirement: Contexto autenticado institucional
O sistema SHALL expor uma rota técnica autenticada que confirme sessão ativa, tenant
resolvido e membership ativa sem expor dados sensíveis, IDs internos ou papel.

#### Scenario: Rota técnica autenticada retorna payload genérico
- **WHEN** a requisição chega autenticada com membership ativa
- **THEN** a rota SHALL responder com payload genérico de sucesso

#### Scenario: Visitante não acessa contexto autenticado
- **WHEN** a requisição chega sem sessão autenticada
- **THEN** a rota SHALL ser bloqueada pelo middleware de autenticação
