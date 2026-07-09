# Migração para Laravel 13 + PostgreSQL 18

> **Nota de honestidade.** Esta proposta é **retroativa**. A mudança nasceu de um
> roteiro externo ([`docs/migracao/roteiro-v1.html`](../../../docs/migracao/roteiro-v1.html)),
> não de uma proposta no OpenSpec — o que contraria a regra de ouro de
> [`FLUXO-OPENSPEC.md`](../../../docs/processo/FLUXO-OPENSPEC.md). Ela é escrita
> agora para reconciliar o registro, e as tarefas já estão concluídas.

## Why

O jogo em PHP puro funciona, mas cobra um preço em cada mudança:

- **Zero testes.** Nenhuma regra do motor de batalha estava travada. Um refator no
  cálculo de dano só seria notado por um jogador.
- **Escritas por GET.** `historia/concluir` gravava progresso e XP, `loja/vender/5`
  transformava um item em ouro, `mestre/excluirFase/5` apagava a fase e seus
  desafios em cascata. Todas alcançáveis por um `<img src>`.
- **Recompensa não idempotente.** A guarda contra duplo-crédito era um flag em
  `$_SESSION['batalha']` — durava o que durava a sessão e não valia nada contra
  duas requisições concorrentes.
- **O MySQL escondia erros.** `FIELD()`, `YEARWEEK()`, `SUM(booleano)` e a
  collation `_ci` do e-mail são conveniências que amarram o projeto ao dialeto.

A restrição acadêmica ("sem framework") foi cumprida e liberada. O ativo que vale
a pena preservar é o **motor** — batalha, progressão, reputação, maestria, missões —
e ele precisa de uma rede de segurança antes de qualquer reescrita.

## What Changes

- **Fase 0 — rede de segurança.** 38 testes de caracterização sobre o **legado**,
  em PHP puro. Os valores esperados são derivados à mão de `config/config.php`, não
  capturados de snapshot. São o contrato que o port reproduz número a número.
- **Fase 1 — núcleo.** Laravel 13 + PostgreSQL 18 em `platform/`. 13 tabelas
  traduzidas: 9 ENUMs viram `CHECK`, `TINYINT(1)` vira `boolean`, `JSON` vira
  `jsonb`, o UNIQUE de e-mail vira índice sobre `lower(email)`.
- **Fase 2 — motor.** `app/Dominio/{Combate,Progressao}`, sem HTTP nem sessão. Os
  vetores-ouro passam com os mesmos números. **Uma divergência deliberada:** a
  recompensa passa a ser idempotente por chave no banco.
- **Fase 3 — dados.** `algorithmia:importar` copia o MySQL preservando IDs — sem
  isso a conquista `arquivista_do_vazio` fica inalcançável em silêncio.
- **Fase 4 — web.** Rotas nomeadas, auth e CSRF do Laravel, sessão em banco. Toda
  escrita vira POST. `public/js/batalha.js` é reaproveitado sem uma linha alterada.
- **Fase 5 — operação.** Imagem, `bin/deploy.sh` com rollback automático, backup com
  ensaio de restauração, e `algorithmia:smoke`, que joga uma fase real dentro de uma
  transação e a desfaz.

O legado permanece de pé, intocado, como plano de rollback do corte.

## Fora de escopo

- **Multitenancy e RLS.** O RLS existia para servir a multitenancy; adiada a
  segunda, cai o primeiro. Voltam quando existir a instituição nº 2.
- **Redis, filas, Horizon, Reverb.** São daemons que existem para trabalho que o
  jogo não tem.
- **A Xiax.** Generalizar o conteúdo para currículo escolar (BNCC) é outro produto:
  as 157 questões de PHP e SQL não servem a um nono ano. O motor é o ativo comum.
- **LGPD e consentimento parental.** Bloqueador da Xiax (público de menores de
  idade), não desta migração.
- **Redesenho visual.** O CSS e o JS do jogo são reaproveitados como estão. Portar
  não é hora de mexer na identidade.
- **O corte em produção.** Os artefatos existem e foram exercitados localmente. O
  deploy na VPS depende de credenciais e da topologia de coexistência descrita em
  [`RUNBOOK.md §9`](../../../docs/operacao/RUNBOOK.md) — o `httpd` do legado já é dono
  da porta 80.
