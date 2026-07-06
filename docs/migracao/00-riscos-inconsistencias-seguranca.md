# Riscos, Inconsistencias e Seguranca

## R-001
**Descricao:** Requisicoes mutaveis precisam de protecao consistente contra repeticao.
**Evidencia:** batalhas e operacoes de loja/inventario usam transacoes e marcas de estado, mas ha multiplos pontos de mutacao no frontend e no backend.
**Arquivos envolvidos:** `app/controllers/BatalhaController.php`, `app/controllers/LojaController.php`, `app/controllers/InventarioController.php`, `app/services/RecompensaService.php`.
**Impacto:** recompensa, consumo de item ou alteracao de ouro podem duplicar.
**Probabilidade:** Media.
**Prioridade:** Critica.
**Recomendacao:** idempotencia server-side, locks e testes de repeticao.
**Fase sugerida para tratamento:** Fase 1 e Fase 5.

## R-002
**Descricao:** Acesso a recursos por id previsivel pode abrir risco de IDOR.
**Evidencia:** varias rotas recebem `id` ou `faseId` em URL e dependem de validacao de posse/progresso.
**Arquivos envolvidos:** controllers de historia, batalha, inventario, loja e mestre.
**Impacto:** acesso indevido a fase, item, desafio ou registro administrativo.
**Probabilidade:** Alta.
**Prioridade:** Critica.
**Recomendacao:** policies, ownership checks e contexto de tenant controlado no servidor.
**Fase sugerida para tratamento:** Fase 1, Fase 3 e Fase 5.

## R-003
**Descricao:** O legado ainda nao possui separacao multitenant.
**Evidencia:** nao existe `tenant_id` em schema, services ou controllers lidos.
**Arquivos envolvidos:** `database/schema.sql`, `app/`, `config/`.
**Impacto:** vazamento entre instituicoes na futura plataforma.
**Probabilidade:** Certa na migracao se nada for feito.
**Prioridade:** Critica.
**Recomendacao:** adicionar `tenant_id`, resolver tenant por servidor e aplicar RLS.
**Fase sugerida para tratamento:** Fase 1 a Fase 3.

## R-004
**Descricao:** Gabarito e respostas corretas devem permanecer server-side.
**Evidencia:** `desafios.resposta` existe no banco e o estado publico da batalha filtra resposta.
**Arquivos envolvidos:** `app/services/BatalhaService.php`, `database/schema.sql`, `app/views/batalha/arena.php`.
**Impacto:** vazamento de resposta correta destrói a integridade pedagógica.
**Probabilidade:** Media.
**Prioridade:** Critica.
**Recomendacao:** contrato de API nunca expor `resposta` nem explicacao sensivel antes da resolucao.
**Fase sugerida para tratamento:** Fase 1 e Fase 5.

## R-005
**Descricao:** CSRF em endpoints mutaveis precisa ser auditado por metodo e canal.
**Evidencia:** o core possui protecao, mas o repositório documenta historico de risco em endpoints AJAX e rotas mutaveis.
**Arquivos envolvidos:** `app/core/Controller.php`, `app/controllers/*`, `app/views/*`.
**Impacto:** acao mutavel disparada por terceiro.
**Probabilidade:** Media.
**Prioridade:** Alta.
**Recomendacao:** validar token em POST e AJAX, testar GET mutavel e recusar qualquer mutacao via GET.
**Fase sugerida para tratamento:** Fase 1 e Fase 5.

## R-006
**Descricao:** Nao ha rate limiting de login visivel no legado.
**Evidencia:** `AuthController` faz login e o material de auditoria aponta ausencia de limitacao.
**Arquivos envolvidos:** `app/controllers/AuthController.php`, `app/core/Auth.php`, `docs/auditoria/seguranca.md`.
**Impacto:** forca bruta e abuso de credenciais fracas.
**Probabilidade:** Alta.
**Prioridade:** Alta.
**Recomendacao:** rate limit, bloqueio progressivo e logs de autenticao.
**Fase sugerida para tratamento:** Fase 1 e Fase 5.

## R-007
**Descricao:** `Escolha::definir()` apaga e reinsere, criando janela de inconsistencia.
**Evidencia:** `DELETE` seguido de `INSERT`.
**Arquivos envolvidos:** `app/models/Escolha.php`.
**Impacto:** perda temporaria de dado e race condition.
**Probabilidade:** Media.
**Prioridade:** Alta.
**Recomendacao:** upsert transacional com chave unica por codigo/personagem.
**Fase sugerida para tratamento:** Fase 2 e Fase 5.

## R-008
**Descricao:** `Inventario::adicionar()` e `Inventario::remover()` podem sofrer concorrencia.
**Evidencia:** leem quantidade e depois atualizam.
**Arquivos envolvidos:** `app/models/Inventario.php`.
**Impacto:** duplicacao ou perda de item.
**Probabilidade:** Media.
**Prioridade:** Alta.
**Recomendacao:** bloqueio transacional/row lock e teste de concorrencia.
**Fase sugerida para tratamento:** Fase 2 e Fase 5.

## R-009
**Descricao:** Atualizacao de ouro e inventario na loja pode duplicar saldo sem protecao adicional.
**Evidencia:** compra/venda atualizam personagem e inventario em passos distintos.
**Arquivos envolvidos:** `app/controllers/LojaController.php`, `app/models/Inventario.php`.
**Impacto:** economia do jogo comprometida.
**Probabilidade:** Media.
**Prioridade:** Alta.
**Recomendacao:** encapsular em transacao e testar repeticao de requisicao.
**Fase sugerida para tratamento:** Fase 2 e Fase 5.

## R-010
**Descricao:** `ProgressoFase::registrar()` usa sintaxe `VALUES()` especifica de MySQL.
**Evidencia:** upsert em `ON DUPLICATE KEY UPDATE ... VALUES(...)`.
**Arquivos envolvidos:** `app/models/ProgressoFase.php`.
**Impacto:** quebra de portabilidade para PostgreSQL.
**Probabilidade:** Certa na migracao.
**Prioridade:** Critica.
**Recomendacao:** reescrever para `ON CONFLICT ... DO UPDATE`.
**Fase sugerida para tratamento:** Fase 2.

## R-011
**Descricao:** Banco atual usa enums e JSON MySQL que exigirao equivalencia explicita em PostgreSQL.
**Evidencia:** schema de `usuarios`, `itens`, `personagens`, `fases`, `desafios`, `dialogos`.
**Arquivos envolvidos:** `database/schema.sql`.
**Impacto:** divergencias de tipo e validacao.
**Probabilidade:** Certa.
**Prioridade:** Alta.
**Recomendacao:** mapear enums para check constraints ou lookup tables.
**Fase sugerida para tratamento:** Fase 2.

## R-012
**Descricao:** Dados institucionais futuros estao espalhados em tabelas sem separacao de contexto.
**Evidencia:** progresso, inventario, respostas, escolhas e conquistas estao atrelados ao personagem, nao ao tenant.
**Arquivos envolvidos:** `database/schema.sql`, `app/models/*`.
**Impacto:** vazamento entre instituicoes na futura arquitetura.
**Probabilidade:** Certa sem mudanca.
**Prioridade:** Critica.
**Recomendacao:** `tenant_id`, resolver por servidor e RLS nas tabelas tenant-scoped.
**Fase sugerida para tratamento:** Fase 1 a Fase 3.

## R-013
**Descricao:** Rotas administrativas precisam de contexto global separado e auditado.
**Evidencia:** painel mestre opera sobre conteudo global do jogo.
**Arquivos envolvidos:** `app/controllers/MestreController.php`, `app/core/Auth.php`.
**Impacto:** alteracao indevida de conteudo global.
**Probabilidade:** Media.
**Prioridade:** Alta.
**Recomendacao:** trilha de auditoria, permissao explicitamente separada e eventual admin global.
**Fase sugerida para tratamento:** Fase 3 e Fase 5.

## R-014
**Descricao:** Falta de testes automatizados torna regressao manual a unica barreira atual.
**Evidencia:** docs de QA afirmam ausencia de suite automatizada.
**Arquivos envolvidos:** `docs/desenvolvimento/qa-e-testes.md`, `docs/processo/HANDOFF-SESSAO.md`.
**Impacto:** regressao silenciosa nas regras de jogo.
**Probabilidade:** Alta.
**Prioridade:** Critica.
**Recomendacao:** criar suite de services e feature tests na fase futura.
**Fase sugerida para tratamento:** Fase 5.

## R-015
**Descricao:** Segredos ou contas padrao nao devem ser reproduzidos em docs.
**Evidencia:** ha scripts de seed e conta demo; o conteudo foi observado, mas nao e reproduzido aqui.
**Arquivos envolvidos:** `database/seed-conta-demo.php`, `database/seed_remote.sh`.
**Impacto:** exposicao operacional.
**Probabilidade:** Media.
**Prioridade:** Alta.
**Recomendacao:** remover credenciais do repositório e usar variaveis de ambiente/secret store.
**Fase sugerida para tratamento:** Fase 1.

## R-016
**Descricao:** Campos textuais usados como identificadores podem gerar duplicidade logica.
**Evidencia:** `codigo` em escolhas e conquistas, `svg_slug` e `ordem_global` em conteudo.
**Arquivos envolvidos:** `database/schema.sql`, `app/models/Escolha.php`, `app/models/Conquista.php`.
**Impacto:** conflitos e consistencia fraca.
**Probabilidade:** Media.
**Prioridade:** Media.
**Recomendacao:** unicos explicitos e lookup keys mais fortes.
**Fase sugerida para tratamento:** Fase 2.

## R-017
**Descricao:** Datas usam `DATETIME` sem timezone padronizado.
**Evidencia:** `criado_em`, `respondido_em`, `concluida_em`, `obtida_em`.
**Arquivos envolvidos:** `database/schema.sql`.
**Impacto:** ambiguidade de horario e auditoria.
**Probabilidade:** Alta.
**Prioridade:** Media.
**Recomendacao:** migrar para `timestamp with time zone` e padrao UTC.
**Fase sugerida para tratamento:** Fase 2.

## R-018
**Descricao:** Pode haver registros obsoletos ou orfaos se seeds e migracoes nao ficarem sincronizadas.
**Evidencia:** o proprio processo de migracao usa schema + seeds + scripts incrementais.
**Arquivos envolvidos:** `database/migrate.php`, `database/seeds.sql`, `database/migrations/*.sql`.
**Impacto:** inconsistencias entre ambiente novo e legado.
**Probabilidade:** Media.
**Prioridade:** Media.
**Recomendacao:** inventario de dados, checks de integridade e scripts idempotentes.
**Fase sugerida para tratamento:** Fase 2.

## R-019
**Descricao:** O legado nao possui protecao de contexto de tenant em queries.
**Evidencia:** nenhuma query lida filtra por tenant, pois o tenant nao existe ainda.
**Arquivos envolvidos:** toda a camada de models e services.
**Impacto:** vazamento cross-tenant quando a plataforma virar multitenant.
**Probabilidade:** Certa sem mudanca.
**Prioridade:** Critica.
**Recomendacao:** policies, escopo global e RLS desde o inicio do backend novo.
**Fase sugerida para tratamento:** Fase 1 a Fase 3.

## Classificacao geral
- **Critico:** R-001, R-002, R-003, R-004, R-010, R-012, R-014, R-019.
- **Alto:** R-005, R-006, R-007, R-008, R-009, R-011, R-013, R-015.
- **Medio/Baixo:** R-016, R-017, R-018.

