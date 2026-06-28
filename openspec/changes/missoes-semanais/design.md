# Design — Missões da semana

## Camada MVC

| Camada | Arquivo | Papel |
|--------|---------|-------|
| Config | `config/config.php` | `MISSOES_SEMANAIS` (pool) + `MISSOES_POR_SEMANA` |
| Model | `app/models/RespostaLog.php`, `ProgressoFase.php` | métricas da **semana ISO** |
| Service | `app/services/MissaoService.php` | seleção + avaliação (lógica **pura**), `daSemana()` defensivo |
| Controller | `app/controllers/PerfilController.php` | injeta `$missoes` |
| View / CSS | `app/views/perfil/index.php`, `public/css/style.css` | painel "🎯 Missões da semana" |

## Janela: semana ISO (sem cron)

Progresso medido na semana ISO corrente: `YEARWEEK(coluna, 3) = YEARWEEK(NOW(), 3)`
(modo 3 = ISO, semana começa na segunda). A janela **reseta sozinha** na virada da semana —
nenhum job/cron necessário. É **distinta do recap** (7 dias móvel) de propósito: a missão é a
**meta da semana corrente**; o recap é a **retrospectiva pessoal** dos últimos 7 dias.

## Rotação determinística

`indiceSemana = (int)date('o') * 53 + (int)date('W')` (ano ISO + semana ISO) → inteiro único e
crescente por semana. Seleciona-se `K = MISSOES_POR_SEMANA` (3) missões consecutivas do pool
(módulo N), sem estado:

```
base = ((indice % N) + N) % N
selecao = [ pool[(base+0)%N], pool[(base+1)%N], pool[(base+2)%N] ]
```

`selecionar(indice)` é **pura** (testável com índices fixos); só `indiceSemanaAtual()` lê a data.

## Métricas (2 queries, read-only)

- `RespostaLog::metricasSemana()` — 1 query agregada: `respostas`, `acertos`,
  `respostas_sem_ia`, `acertos_sem_ia`, `materias` (DISTINCT assunto via JOIN desafios).
- `ProgressoFase::fasesSemana()` — `COUNT(*)` de fases concluídas na semana ISO.

## Avaliação

Cada missão tem `metrica` + `alvo`. A maioria é **contável** (`atual = metricas[metrica]`,
`completa = atual >= alvo`), com `pct = clamp(atual/alvo*100, 0, 100)`. A métrica `precisao`
é caso especial: gate de **volume mínimo** (`min`) e **piso de precisão** — enquanto o volume
não chega, a barra mede o volume; depois mede a precisão (`completa` exige ambos).

## Robustez / não-regressão

- `MissaoService::daSemana()` em `try/catch` → `[]`; a view só desenha o painel quando há
  missões. Nada quebra o perfil.
- **Sem dado novo, sem migration, sem persistência** — funciona em qualquer instalação.
- Read-only: não concede recompensa nem altera economia/regra.
