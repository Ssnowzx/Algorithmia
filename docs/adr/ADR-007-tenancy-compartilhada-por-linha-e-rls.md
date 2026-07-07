# ADR-007: Tenancy compartilhada por linha e RLS

## Contexto

A nova plataforma Laravel em `/platform` precisa suportar múltiplos tenants sem duplicar
o banco por cliente. O modelo aprovado para a Etapa 2 usa `tenant_id` em tabelas de domínio
e Row Level Security no PostgreSQL como barreira obrigatória de isolamento.

## Decisão

Adotar tenancy compartilhada por linha, com RLS nativo do PostgreSQL, contexto de tenant
por requisição/transação e falha fechada quando o contexto não existir.

## Alternativas rejeitadas

- Banco por tenant: aumenta custo operacional e dificulta manutenção.
- Isolamento apenas por `where tenant_id = ...`: depende demais da disciplina da aplicação.
- Uso de `withoutGlobalScopes()` como bypass: não atende o requisito de segurança.

## Consequências

- As tabelas tenant-aware precisam carregar `tenant_id`.
- O runtime da aplicação não deve ter `SUPERUSER`, `BYPASSRLS` nem ser proprietário das tabelas.
- Operações administrativas e de bootstrap exigem contexto e usuário separados.

## Riscos

- RLS mal configurado pode bloquear fluxos legítimos.
- Consultas sem contexto podem falhar cedo e exigir disciplina na ordem dos middlewares.

## Estratégia de reversibilidade

Se a estratégia se mostrar inviável, a reversão deve acontecer antes de criar as tabelas de
domínio, mantendo a plataforma em modo single-tenant técnico enquanto o legado continua
intocado.

## Impactos na migração do legado

Nenhum impacto imediato no MySQL legado. A decisão vale apenas para `/platform`.

## Critérios de aceite

- Toda consulta tenant-aware depende de contexto de tenant válido.
- Não existe bypass de RLS por scope global.
- O isolamento entre tenants é verificável por testes positivos e negativos.
