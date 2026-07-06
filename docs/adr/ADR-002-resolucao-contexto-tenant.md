# ADR-002 - Resolucao e Contexto de Tenant

## Status
Aceita

## Contexto
O tenant nao pode ser informado pelo cliente como fonte de verdade.
A resolucao deve ser feita pelo servidor a partir de dominio, subdominio ou contexto administrativo explicitamente autorizado.

## Decisao
- Resolver tenant no inicio da request.
- Tratar o contexto como imutavel durante toda a request.
- Falhar de forma segura quando nao houver tenant em rota tenant-scoped.
- Nunca aceitar `tenant_id` vindo do cliente como autoridade.

## Alternativas consideradas
- Receber `tenant_id` em parametro de rota.
- Resolver por cabecalho controlado pelo cliente.
- Depender de configuracao local do frontend.

## Consequencias
- O backend permanece como fonte de verdade.
- Evita troca manual de tenant pelo usuario.
- Simplifica auditoria e comportamento previsivel.

## Riscos
- Resolucao incorreta por dominio/subdominio.
- Reuso de contexto entre requests se a infraestrutura nao limpar estado.
- Acesso administrativo precisa de trilha distinta.

## Criterios de revisao futura
- Mudanca no modelo de dominios.
- Necessidade de contexto administrativo mais granular.
- Eventual suporte a whitelists de ambiente.

## Referencias internas
- `docs/adr/ADR-001-estrategia-multitenancy.md`
- `docs/migracao/00-riscos-inconsistencias-seguranca.md`

