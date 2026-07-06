# ADR-001 - Estrategia de Multitenancy

## Status
Aceita

## Contexto
O Algorithmia evoluira para atender contextos institucionais distintos sem misturar dados entre organizacoes.
O legado nao possui multitenancy, mas a arquitetura alvo exige isolamento forte e verificavel.

## Decisao
Adotar `shared database, shared schema`, com `tenant_id` obrigatorio para dados institucionais, e isolamento em camadas:
- resolucao de tenant no servidor;
- policies e escopos de aplicacao;
- PostgreSQL Row-Level Security;
- testes de isolamento e de acesso cruzado.

## Alternativas consideradas
- `database per tenant`.
- `schema per tenant`.
- isolamento apenas por aplicacao.

## Consequencias
- Reduz custo operacional frente a multiplos bancos.
- Exige disciplina rigorosa em todas as consultas tenant-scoped.
- Requer contexto de tenant em toda a request relevante.
- Facilita evolucao gradual do legado para o novo backend.

## Riscos
- Vazamento cross-tenant se o contexto nao for aplicado.
- Consultas sem filtro podem retornar dados indevidos sem RLS.
- Rotas administrativas precisam de contexto global separado.

## Criterios de revisao futura
- Se o volume de dados por tenant exigir isolamento fisico.
- Se houver necessidade juridica de separacao por banco.
- Se o custo de RLS/escopos se tornar impeditivo.

## Referencias internas
- `docs/migracao/00-governanca-e-inventario.md`
- `docs/migracao/00-riscos-inconsistencias-seguranca.md`
- `docs/migracao/00-inventario-dados-legado.md`

