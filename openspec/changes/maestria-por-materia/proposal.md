## Why

O perfil já tem um painel **"📚 Domínio por matéria"**, mas ele exibe apenas a taxa de
acerto **bruta** (`acertos/total` + barra). Isso tem dois problemas:

- **Engana com baixo volume:** acertar 2 de 2 (100%) aparece como "domínio total" sem
  demonstrar competência sustentada — incentivo vazio, o mesmo vício que a auditoria de UX
  apontou nas conquistas binárias.
- **Sem progressão nem meta:** a barra é estática (só a % do momento); não comunica "o que
  falta para evoluir", perdendo o efeito **goal-gradient / endowed progress** que já
  adotamos nas conquistas.

A próxima etapa de gamificação combinada é de **mecânica**, mas escolhemos a de maior valor
pedagógico e menor risco: **maestria por matéria**. Em vez de premiar ofensiva (streak —
risco de ansiedade) ou exigir infraestrutura de temporada (ligas/cron, inexistentes), ela
converte o esforço em **domínio visível e honesto** — alinhado a "melhorar sem piorar" e à
natureza de RPG educacional do jogo.

## What Changes

Evolui o painel de domínio por matéria para um sistema de **maestria**, derivado
*on-the-fly* dos dados que **já existem** (`respostas_log` → `desafios.assunto`), **sem
migration, sem cron**, e **sem alterar** regra/perguntas/XP/ouro/reputação.

- **Faixas de maestria** por matéria (Não iniciado → Iniciante → Aprendiz → Praticante →
  Especialista → Mestre), exigindo **volume de acertos E precisão sustentada** — não a %
  bruta.
- **Selo + cor por faixa** e **barra de goal-gradient** rumo ao próximo selo, que mostra o
  fator que falta (mais acertos ou mais precisão).
- **Resumo "X/8 dominadas"** no cabeçalho do painel.
- **Fonte única** em `config` (`MAESTRIA_FAIXAS`); **lógica pura testável**
  (`MaestriaService`); **read-only** no controller (reusa a query que o perfil já faz).

## Fora de escopo

- Nenhuma mudança em perguntas (`database/desafios*`/banco de questões), batalha, XP, ouro,
  reputação ou qualquer regra de jogo.
- **Sem** novas tabelas/colunas/migration; **sem** cron/temporada/reset.
- **Sem** novas conquistas no catálogo (não toca o banco de conquistas) — maestria é
  exibição derivada, não concede recompensa.
- **Não** altera o ranking nem adiciona competição entre jogadores.
