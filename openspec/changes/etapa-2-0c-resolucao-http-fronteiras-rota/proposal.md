## Why

A etapa 2.0B formalizou tenancy, identidade global, memberships e RLS no PostgreSQL.
Falta tornar o contrato HTTP explicito: quais hosts sao globais, quais rotas exigem tenant
e como evitar vazamento de contexto entre requisicoes.

## What Changes

- Define a resolucao segura de tenant por `Host` HTTP.
- Separa rotas globais de plataforma e rotas tenant-scoped.
- Introduz middleware para inicializar `CurrentTenant` e contexto transacional por rota.
- Mantem `/healthz` global e fora da resolucao de tenant.
- Adiciona documentacao e testes para os codigos HTTP e para o isolamento entre requisicoes.

## Capabilities

### New Capabilities
- `tenancy-http`: normalizacao de host, resolucao por dominio ativo e fronteiras de rota.

### Modified Capabilities
- `tenancy`: passa a ter fronteira HTTP explicita para `CurrentTenant` e `app.tenant_id`.

## Impact

Impacta apenas o nucleo Laravel em `/platform`, sobretudo routes, middleware, configuracao
de trusted proxies, documentacao e testes. Nao altera o legado PHP/MySQL nem o schema da
fundacao 2.0B.
