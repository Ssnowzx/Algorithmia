# Pesquisa — base de evidências da auditoria de produto

_Deep Research de nível consultoria (multi-fonte, validação cruzada adversarial 3-votos, afirmações
rotuladas fato/consenso/boa-prática/hipótese/opinião). Conduzido em **passadas iterativas** via loop:
cada passada vai fundo num conjunto de domínios, identifica lacunas e alimenta a próxima._

## Passadas

| # | Domínios | Estado | Arquivo |
|---|----------|--------|---------|
| 1 | Navegação/IA + Motion/View Transitions | ✅ concluída | [01-navegacao-e-motion.md](01-navegacao-e-motion.md) |
| 2 | Motion timing/easing + reduced-motion/WCAG + nativo-vs-biblioteca + thumb-zone/casos reais | ✅ concluída | [02-timing-motion-libs-casos.md](02-timing-motion-libs-casos.md) |
| 3 | Gamificação & retenção em edtech | ✅ concluída | [03-gamificacao-retencao.md](03-gamificacao-retencao.md) |
| 4 | Design tokens / design systems sem build | ✅ concluída | [04-design-tokens-css.md](04-design-tokens-css.md) |
| 5 | Arquitetura front-end & performance (imagens, PE) | ✅ concluída | [05-arquitetura-performance.md](05-arquitetura-performance.md) |

> ✅ **Pesquisa completa (P1–P5).** ~500 agentes, ~12,7M tokens de subagente, ~3.700 buscas/fetches no
> total; cada afirmação factual passou por verificação adversarial 3-votos. Próxima fase: **consolidar a
> auditoria** (`../`, por dimensão + roadmap + referências) e **construir o protótipo** (`../../prototipo/`).

## Como ler
Cada relatório tem, por domínio: achados rotulados + evidência citada + **decisão recomendada p/ o
Algorithmia**, afirmações **refutadas** (transparência), respostas aos **5 critérios de parada**
(estado da arte · práticas dos líderes · decisão+porquê · riscos · o que falta validar na prática) e
as **lacunas** que viram a próxima passada.

## Critério de encerramento do loop de pesquisa
Encerrar quando os 5 domínios responderem aos 5 critérios de parada com evidência cruzada e não
restar lacuna relevante. Então a pesquisa vira a **auditoria** (`../`) e o **protótipo**
(`../../prototipo/`).
