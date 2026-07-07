## Why

A Etapa 1 estabilizou a nova plataforma Laravel em `/platform`, mas o isolamento entre
tenants, a resolução por domínio e o modelo de identidade ainda não têm contrato formal.
Sem um desenho explícito, a implementação posterior corre risco de misturar tenant atual,
autorização e isolamento de dados de forma insegura.

## What Changes

- Define o modelo compartilhado por linha para `tenants`, `tenant_domains`, `users` e `tenant_memberships`.
- Formaliza a resolução de tenant por host confiável, antes de qualquer consulta tenant-aware.
- Especifica o contrato de `CurrentTenant` imutável para o restante da requisição.
- Define a estratégia de Row Level Security no PostgreSQL com falha fechada e sem bypass por scope global.
- Documenta identidade global, memberships por tenant e autorização em camadas `plataforma` e `tenant`.
- Cria ADRs para tenancy compartilhada por linha, resolução por domínio e identidade global com memberships.

## Capabilities

### New Capabilities
- `tenancy`: resolução de tenant, contexto atual imutável e isolamento por linha com RLS.
- `identity`: usuários globais, autenticação web, verificação e recuperação de acesso.
- `authorization`: autorização em camada de plataforma e por tenant com papéis e memberships.

### Modified Capabilities
- 

## Impact

Impacta o desenho futuro da plataforma Laravel em `/platform`, principalmente app, banco
PostgreSQL, middleware, políticas, autenticação e contratos de domínio. Não altera o legado
PHP/MySQL em `/home/algorithmia/public_html` nesta etapa.
