# Governanca e Inventario do Dominio

## Contexto
O Algorithmia e um RPG educacional em PHP MVC legado.
A equipe definiu uma migracao incremental para Laravel + PostgreSQL + Redis com multitenancy segura.
Antes de reescrever o sistema, e necessario entender o dominio real implementado hoje.

## Problema
O legado contem regras de jogo, conteudo pedagogico e persistencia de progresso espalhados entre controllers, services, models, views, seeds e scripts.
Sem inventario verificavel, uma migração poderia perder regras, introduzir regressao ou expor dados entre instituicoes.

## Objetivo
Documentar o estado atual do sistema com rastreabilidade suficiente para orientar as proximas fases da migracao.

## Escopo
- Inventario de modulos, rotas, tabelas e services.
- Mapeamento de regras de jogo e seus arquivos de evidencia.
- Inventario de dados e riscos de migracao.
- Riscos de seguranca e multitenancy.
- ADRs da estrategia alvo.
- Plano de regressao do legado.

## Fora de escopo
- Implementar Laravel.
- Alterar banco legado.
- Introduzir multitenancy real.
- Criar migrations novas.
- Criar testes de producao nesta fase.

## Criterios de aceite
- Modulos e regras critic as mapeados com evidencia real.
- Lacunas e inconsistencias registradas sem suposicao.
- Riscos classificados por prioridade.
- Inventario de dados pronto para orientacao da modelagem futura.
- ADRs e OpenSpec desta fase escritos e vinculados.

## Riscos
- Lacunas de conhecimento sobre regras do legado.
- Esquecimento de regra escondida em service ou seed.
- Mistura de conteudo global com estado do jogador.
- Vazamento entre tenants na arquitetura futura se o inventario for incompleto.

## Dependencias
- `docs/migracao/00-governanca-e-inventario.md`
- `docs/migracao/00-mapa-modulos-legado.md`
- `docs/migracao/00-matriz-rastreabilidade-regras.md`
- `docs/migracao/00-plano-regressao-legado.md`
- `docs/migracao/00-riscos-inconsistencias-seguranca.md`
- `docs/migracao/00-inventario-dados-legado.md`
- `docs/migracao/00-glossario-dominio.md`
- `docs/adr/ADR-001-estrategia-multitenancy.md`
- `docs/adr/ADR-002-resolucao-contexto-tenant.md`
- `docs/adr/ADR-003-uso-postgresql-rls.md`
- `docs/adr/ADR-004-versionamento-conteudo.md`
- `docs/adr/ADR-005-estrategia-api-e-integracao.md`

## Entregaveis
- Base documental da Fase 0.
- Mapa dos modulos legados.
- Matriz de rastreabilidade das regras.
- Plano de regressao funcional.
- Inventario de dados.
- Glossario de dominio.
- ADRs da arquitetura alvo.

## Impacto nas fases futuras
- Fase 1: define o esqueleto tecnico do Laravel a partir de regras reais.
- Fase 2: orienta a modelagem PostgreSQL e os tipos de dados.
- Fase 3: sustenta tenant resolver, policies e RLS.
- Fase 4: reduz risco de mover regra de negocio errada.
- Fase 5: vira referencia para testes de regressao.

