## Why

O jogador recém-criado chega ao perfil sem um norte claro de "o que fazer agora", e a jornada
parece começar do zero. O efeito **endowed progress** (Nunes & Drèze, 2006) mostra que exibir
um progresso **já iniciado** aumenta a taxa de conclusão. Falta um onboarding leve que (a)
oriente os primeiros marcos e (b) dê a sensação de que a lenda **já começou**.

É a 4ª etapa de gamificação (read-only), focada em **ativação do novato**.

## What Changes

Painel **"🌟 Primeiros passos"** no topo do perfil, com um checklist de 4 marcos iniciais e o
primeiro — **"Forjar seu herói"** — **já marcado** (o jogador acabou de criar o personagem):
é o *head start* dotado. Barra "X/4".

- Visível **só para novatos** (`nível <= ONBOARDING_NIVEL_MAX`) e **enquanto houver passo
  incompleto** — some quando o jogador cresce ou completa o checklist (não polui veteranos).
- **Read-only**, derivado de dados que já existem (`progresso_fases`, `inventario`,
  `conquistas_personagem`). Sem recompensa, sem persistência, sem migration.

## Fora de escopo

- Não concede recompensa, não grava nada, não muda o fluxo de criação de personagem nem regra.
- Não altera a entrada/seleção já existente (`entrada-e-selecao-ux`); é um painel do perfil.
