# ADR-003 - Uso de PostgreSQL Row-Level Security

## Status
Aceita

## Contexto
O futuro backend usara PostgreSQL como banco principal e precisa de isolamento adicional alem de policies de aplicacao.

## Decisao
- Aplicar RLS em tabelas tenant-scoped.
- Setar `SET LOCAL app.tenant_id` por transacao.
- Usar role de aplicacao sem `BYPASSRLS`.
- Limpar contexto em conexoes reutilizadas.
- Testar acesso cruzado mesmo quando a query nao traz filtro explicito.

## Alternativas consideradas
- Confiar apenas em escopo de aplicacao.
- Usar views filtradas.
- Confiar apenas em audit logs.

## Consequencias
- Defesa em profundidade.
- Menor risco de erro humano em consultas novas.
- Mais complexidade de operacao e teste.

## Riscos
- Contexto de tenant vazando entre requests em pool reutilizado.
- Query administrativa executada com contexto errado.
- Consultas antigas sem filtro quebrando com erro de policy.

## Criterios de revisao futura
- Caso o custo de RLS seja alto demais para fluxo especifico.
- Caso exista modulo global com regras excepcionais claramente documentadas.

## Referencias internas
- `docs/adr/ADR-001-estrategia-multitenancy.md`
- `docs/adr/ADR-002-resolucao-contexto-tenant.md`
- `docs/migracao/00-riscos-inconsistencias-seguranca.md`

