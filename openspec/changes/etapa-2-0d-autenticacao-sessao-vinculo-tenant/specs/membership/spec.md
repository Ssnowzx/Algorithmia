## ADDED Requirements

### Requirement: Membership ativa por requisição institucional
O sistema SHALL exigir membership ativa em toda rota institucional autenticada. A membership
SHALL ser revalidada em cada request e SHALL falhar fechadamente quando ausente, suspensa ou
revogada.

#### Scenario: Membership ativa permite acesso
- **WHEN** o usuário autenticado possui membership ativa no tenant resolvido
- **THEN** a rota institucional SHALL responder normalmente

#### Scenario: Membership revogada bloqueia próxima requisição
- **WHEN** a membership é revogada após o login
- **THEN** a próxima requisição institucional SHALL ser bloqueada
- **AND** o contexto local SHALL ser limpo

#### Scenario: Membership em tenant diferente não autoriza acesso
- **WHEN** a sessão autenticada pertence a outro tenant
- **THEN** o sistema SHALL negar o acesso sem trocar o tenant resolvido pelo host
