# ADR-009: Identidade global e memberships por tenant

## Contexto

A nova plataforma precisa permitir que uma mesma pessoa participe de múltiplos tenants
sem duplicar identidade. Ao mesmo tempo, a autorização precisa reconhecer papéis
por tenant e uma camada restrita de plataforma.

## Decisão

Adotar `users` como identidades globais da plataforma e `tenant_memberships` como vínculo
entre usuário e tenant. A autenticação inicial será web, com hash moderno do Laravel,
e a evolução para API ficará para a implementação futura.

## Alternativas rejeitadas

- Usuário por tenant: impediria participação múltipla e aumentaria o custo de migração.
- Permissões apenas no cliente: inseguro e não auditável.
- Um único papel global para tudo: não atende o isolamento de tenants.

## Consequências

- E-mail deve ser único globalmente.
- Papéis `owner`, `admin`, `manager` e `member` passam a existir por tenant.
- O último owner ativo não pode ser removido.
- Autenticação e autorização devem ser auditáveis e resistentes a enumeração.

## Riscos

- Regras de membership podem ser aplicadas fora de ordem se a arquitetura não separar
  identidade, tenant e autorização.
- Se a aplicação confiar apenas em papel, pode haver acesso indevido entre tenants.

## Estratégia de reversibilidade

Caso a modelagem de memberships precise ser revista, a reversão deve ocorrer antes das
tabelas de domínio futuro, preservando a identidade global já adotada.

## Impactos na migração do legado

Nenhum impacto no MySQL legado. O legado continua independente e sem bridge com a plataforma.

## Critérios de aceite

- Um usuário pode pertencer a múltiplos tenants.
- A autorização depende de membership + tenant atual.
- O último owner ativo é protegido contra remoção.
