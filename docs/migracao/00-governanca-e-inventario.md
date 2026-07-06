# Fase 0 - Governanca Tecnica e Inventario do Dominio

## Objetivo
Construir uma base de conhecimento verificavel do estado atual do Algorithmia antes da migracao incremental para Laravel + PostgreSQL + Redis.
Esta fase nao altera regras de producao; ela apenas documenta o legado, suas dependencias, riscos e pontos que exigirao regressao formal.

## Escopo analisado
- Estrutura do MVC legado em `app/`.
- Configuracao central em `config/`.
- Schema, seeds, migracoes e scripts de dados em `database/`.
- Views, JS, CSS e assets em `public/` e `app/views/`.
- Documentacao canonica e de processo em `docs/`, `.cursor/`, `.codex/` e `openspec/`.
- Scripts auxiliares em `tools/`.

## Limitacoes da analise
- Nao havia `rg` disponivel no ambiente; a inspecao foi feita com comandos nativos do PowerShell e leitura direta de arquivos.
- Nao existe suite automatizada de testes no repositorio; a propria documentacao confirma isso.
- Nao foi executado browser automation nem alteracao de banco, seeds ou codigo de producao nesta fase.
- O legado ainda usa MySQL/InnoDB e SQL especifico de MySQL; a migracao para PostgreSQL e uma decisao futura, nao uma condicao atual.

## Versao analisada
- Commit atual identificado: `1be0c422925c639875080c2eaf946b653cc51ff4`

## Tecnologias e dependencias encontradas
- PHP MVC customizado com front controller em `index.php`.
- Router proprio em `app/core/Router.php`.
- PDO em `config/db.php`.
- Sessao PHP tradicional.
- HTML/PHP server-side, CSS puro e JavaScript vanilla em `public/js/`.
- Banco relacional atual em MySQL/InnoDB.
- OpenSpec para governanca de mudancas em `openspec/`.
- Scripts auxiliares em Python e shell em `tools/`.
- Nao foram encontrados `composer.json` ou `package.json` na raiz.

## Mapa resumido da arquitetura atual
1. `index.php` recebe a requisicao.
2. `app/core/Router.php` resolve `?url=controlador/metodo`.
3. Controllers orquestram a navegacao e chamam services/models.
4. Models fazem consultas SQL.
5. Services concentram regras de jogo, progressao, batalha, conquista e reputacao.
6. Views renderizam HTML com helpers em `app/core/helpers.php`.

## Principais modulos identificados
- Autenticacao, sessao e cadastro.
- Personagem e perfil.
- Mapa, fases e desbloqueio.
- Desafios e respostas.
- Batalha.
- Inventario e loja.
- Conquistas, reputacao e progresso.
- Dialogos e escolhas narrativas.
- Ranking.
- Painel mestre / administracao de conteudo.
- Banco, seeds e scripts de manutencao.
- Maestria por materia, missoes semanais e onboarding.

## Fluxos criticos de negocio
- Cadastro, login, logout e criacao de personagem.
- Liberacao de fase por progresso.
- Dialogo de abertura e conclusao de fase.
- Batalha com resposta, combo, especial, poção, Fragmento da IA, vitoria e derrota.
- Recompensa com XP, ouro, itens, progresso, reputacao e conquistas.
- Inventario, compra, venda, equipar e descarte.
- Ranking por nivel e XP.
- Final narrativo com escolhas e epilogo.
- CRUD de mestre para fases, desafios e itens.

## Descobertas principais
- O dominio do jogo esta bastante concentrado em `BatalhaService`, `RecompensaService`, `ProgressaoService`, `ConquistaService` e `ReputacaoService`.
- O legado ja separa bem regra de negocio de views em varios pontos, mas ainda existem trechos acoplados a SQL MySQL e a ids/valores magicos.
- O sistema possui varios fluxos ativos nao triviais alem do combate: maestria, missoes, onboarding, dominio por regioes e narrativa final.
- A base de dados possui relacoes importantes, mas tambem pontos fragilizados por ausencia de algumas FKs e por uso de chaves implícitas em textos.

## Riscos prioritarios
- Ausencia de suite automatizada de regressao.
- Dependencia de SQL especifico de MySQL.
- Endpoints mutaveis e rotas de administracao que precisam de revisao de seguranca.
- Falta de `tenant_id` no legado e inexistencia de isolamento multitenant.
- Risco de concorrencia em inventario, escolhas e atualizacoes monetarias.
- Uso de ids hardcoded em regras de progresso e conquista.

## Fases futuras impactadas
- Fase 1: criacao do nucleo Laravel e infraestrutura local.
- Fase 2: modelagem PostgreSQL e adaptacao do schema.
- Fase 3: introducao de tenant resolver, policies e contexto controlado.
- Fase 4: portabilidade dos services de dominio.
- Fase 5: cobertura de regressao automatizada.

## Criterios para considerar a Fase 0 concluida
- Rotas relevantes mapeadas ou marcadas como desconhecidas.
- Regras criticas ligadas a arquivos, metodos e tabelas reais.
- Lacunas e inconsistencias documentadas sem suposicoes.
- Riscos de seguranca e multitenancy priorizados.
- ADRs e OpenSpec desta fase produzidos.
- Inventario de dados e plano de regressao prontos para orientar a migracao.

## Evidencias usadas
- `README.md`
- `CODEX.md`
- `AGENTS.md`
- `.cursor/rules/mapa-pagina-aprovada.mdc`
- `docs/canon/REGRAS-DO-JOGO.md`
- `docs/canon/ROTEIRO-NARRATIVO.md`
- `docs/desenvolvimento/qa-e-testes.md`
- `docs/processo/FLUXO-OPENSPEC.md`
- `app/`, `config/`, `database/`, `public/`, `tools/`, `openspec/`
