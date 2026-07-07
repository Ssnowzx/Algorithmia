## Why

A etapa 2.0C consolidou a resolução de tenant por host e as fronteiras HTTP, mas ainda
faltava a camada de identidade e sessão. Sem autenticação global vinculada ao tenant
resolvido, a plataforma ficaria exposta a sessão reutilizada fora do contexto correto,
enumeração de usuário e acesso institucional sem membership ativa.

## What Changes

- Define autenticação por senha com identidade global de `users`.
- Vincula login, logout e contexto autenticado ao tenant resolvido pelo `Host`.
- Exige membership ativa no tenant atual para rotas institucionais autenticadas.
- Mantém `tenant_id` fora de sessão, query string, body e headers arbitrários.
- Introduz rate limiting de login por tenant, IP e e-mail normalizado.
- Documenta sessão host-only, cookies seguros e limpeza de contexto após logout ou revogação.

## Capabilities

### New Capabilities
- `authentication`: login por senha e sessão web no domínio do tenant.
- `session`: cookies host-only, rotação no login e invalidação no logout.
- `membership`: vínculo recorrente ao tenant e bloqueio quando o vínculo estiver ausente ou suspenso.

### Modified Capabilities
- `tenancy`: a resolução por host permanece como pré-requisito do ciclo autenticado.

## Impact

Impacta apenas a plataforma Laravel em `/platform`, seus middlewares, rotas, testes e
documentação. Não altera o legado PHP/MySQL nem introduz autorização fina por papel,
SSO, tokens, recuperação de senha ou cadastro público.
