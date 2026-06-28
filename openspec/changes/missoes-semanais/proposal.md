## Why

O recap **"📅 Sua semana"** mostra a **retrospectiva** (o que já foi feito), mas o jogo não
oferece **metas de curto prazo** — objetivos claros que puxam o jogador de volta e direcionam
a prática durante a semana. A pesquisa de UX (`docs/auditoria-ux/`) aponta objetivos semanais
como alavanca de retenção de **baixo risco**.

É a **2ª etapa de gamificação de mecânica** (após a maestria por matéria), escolhida por
combinar valor e segurança: não exige cron (a semana vem da data) nem altera a economia.

## What Changes

Adiciona um painel **"🎯 Missões da semana"** no perfil: **3 objetivos rotativos por semana**
(sorteados de um pool de forma determinística pela data), com progresso medido na **semana ISO
corrente** (segunda→domingo), derivado *on-the-fly* de `respostas_log` / `progresso_fases`.

- **Pool de missões** em `config` (fonte única) + rotação determinística (índice da semana).
- **Métricas da semana ISO** (respostas, acertos, respostas/acertos sem IA, matérias, fases).
- **MissaoService** puro e testável (seleção + avaliação); defensivo → `[]`.
- Painel com **barra de progresso** e **✓ ao concluir**; texto de sabor no tom sarcástico do
  jogo.

## Fora de escopo

- **Sem recompensa** (ouro/XP/itens) e **sem persistência** → **sem migration**, **sem mexer
  na economia**. (Ver "Decisão" abaixo.)
- **Sem cron/reset**: a janela (semana ISO) e a rotação derivam da data atual no request.
- Não altera perguntas, batalha, ranking, reputação ou conquistas.
- **Ligas/cohorts** seguem fora (exigiriam cron) — etapa futura.

## Decisão (default conservador)

Recompensa material por missão exigiria **persistência** (registrar o que já foi pago →
tabela/migration) e mexeria na **economia** — portanto fica **fora desta etapa**, coerente
com "melhorar sem piorar". Pode ser adicionada depois como incremento explícito, se desejado.
