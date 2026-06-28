## Why

A progressão do jogo é **vertical** (XP/nível, sem teto), e a maestria por matéria cobre o
domínio do **conteúdo** (assuntos das perguntas). Falta uma progressão **horizontal**: razões
para o jogador **voltar e dominar a jornada** — não "mais um nível", mas a **perfeição das
regiões** já visitadas.

As 5 regiões (uma por mestre) têm fases com **estrelas** (3 = sem erros e sem IA), mas o jogo
não mostra o **grau de domínio por região** nem reconhece a perfeição. As conquistas
"Discípulo do mestre X" existem no catálogo, mas estão **órfãs** (sem gatilho) — ou seja, o
conceito de "dominar uma região" hoje não tem representação visível.

É a **3ª etapa de gamificação de mecânica** — a **maestria horizontal pós-nível**.

## What Changes

Painel **"🏰 Domínio das Regiões"** no perfil: para cada região (mestre), o grau de perfeição
da jornada (fases concluídas + estrelas), em **4 estados**:
**A explorar → Em jornada → Conquistada** (todas concluídas) **→ Dominada** (todas com 3
estrelas). Resumo **"X/5 dominadas"** e o título **"Mestre dos Cinco"** ao dominar todas.

- Read-only, derivado de `fases` + `progresso_fases` (estrelas) — **sem migration, sem cron**.
- `Mestre::progressoPorRegiao()` (1 query, partindo das **fases** → robusta à duplicação de
  `mestres`) + `RegiaoService` (faixa pura, testável; defensivo → `[]`).
- Considera apenas fases **jogáveis** (não-história), que são as que têm estrelas.

## Fora de escopo

- **Não** concede as conquistas órfãs `mestre_X` nem escreve no banco — só exibe o domínio
  derivado (read-only).
- **Não** corrige a **duplicação de `mestres`** (10 registros = 5 reais + 5 órfãos sem fases,
  seed-2×) — é débito separado (exigiria migration, como o dedupe da loja). A feature apenas a
  **contorna** (parte das fases, que referenciam só os 5 reais).
- Não altera fases, estrelas, batalha, XP, economia ou qualquer regra.
