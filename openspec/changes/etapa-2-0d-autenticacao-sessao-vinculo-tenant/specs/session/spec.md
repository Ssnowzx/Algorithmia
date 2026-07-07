## ADDED Requirements

### Requirement: Sessão web segura e host-only
O sistema SHALL usar sessão web baseada em cookie host-only por padrão, sem `SESSION_DOMAIN`
amplo, com `HttpOnly` ativo e `SameSite=Lax`. O identificador SHALL ser renovado no login e
a sessão SHALL ser invalidada no logout.

#### Scenario: Cookie não é compartilhado entre domínios
- **WHEN** a sessão é criada em um host institucional
- **THEN** o cookie SHALL permanecer restrito ao host atual

#### Scenario: Logout invalida sessão
- **WHEN** o usuário executa logout
- **THEN** a sessão SHALL ser invalidada
- **AND** o token CSRF SHALL ser regenerado

### Requirement: Rate limiting contextual de login
O sistema SHALL limitar tentativas de login por tenant resolvido, IP e e-mail normalizado.
Tentativas excedidas SHALL resultar em `429 Too Many Requests` sem afetar outro tenant.

#### Scenario: Tenant diferente não compartilha bloqueio
- **WHEN** um tenant excede o limite de tentativas
- **THEN** outro tenant com o mesmo e-mail SHALL continuar com sua própria janela de rate limit
