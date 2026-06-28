# Design — Maestria por matéria

## Camada MVC (onde cada coisa entra)

| Camada | Arquivo | Papel |
|--------|---------|-------|
| Config (dados) | `config/config.php` | `const MAESTRIA_FAIXAS` — fonte única das faixas |
| Service | `app/services/MaestriaService.php` | lógica **pura** (sem I/O) de faixa + goal-gradient |
| Controller | `app/controllers/PerfilController.php` | injeta `$maestria` **reusando** `$estatisticas` (zero query nova) |
| View / CSS | `app/views/perfil/index.php`, `public/css/style.css` | apresentação (selo, cor, barra) |

A escolha espelha o padrão já aprovado de `ConquistaService::progressoParcial()` e
`RespostaLog::resumoSemana()`: **read-only, derivado, defensivo**.

## Modelo de maestria

Para cada matéria, a partir de `total` (respostas na matéria) e `acertos`:

```
precisao = total > 0 ? acertos / total : 0
```

Faixas (tier crescente; escolhe-se o **maior** tier cujos gates são satisfeitos):

| tier | rótulo | mín. acertos | piso de precisão | regra |
|------|--------|--------------|------------------|-------|
| 0 | Não iniciado | — | — | `total == 0` |
| 1 | Iniciante | 0 | 0% | `total ≥ 1` |
| 2 | Aprendiz | 3 | 0% | `acertos ≥ 3` |
| 3 | Praticante | 6 | 60% | `acertos ≥ 6 e precisao ≥ 60%` |
| 4 | Especialista | 10 | 75% | `acertos ≥ 10 e precisao ≥ 75%` |
| 5 | Mestre | 15 | 85% | `acertos ≥ 15 e precisao ≥ 85%` |

"Dominada" (para o resumo `X/8`) = **tier ≥ 4** (Especialista ou Mestre).

### Por que volume **e** precisão (não a % bruta)
*Mastery learning* (Bloom): domínio = competência **consistente** (~80%+) sobre uma amostra
**suficiente**. Os alvos de acerto são modestos (3/6/10/15) de propósito: funcionam tanto
para matérias de pool grande (`estruturas`=30 questões) quanto pequeno (`sql`=5 — os acertos
acumulam por **resposta**, não por questão distinta). Os pisos de precisão (60/75/85%)
impedem "maestria de sorte" por volume com muitos erros.

## Goal-gradient (barra do próximo selo)

Para `tier < 5`, calcula-se o progresso rumo ao **próximo** tier como o **fator mais
atrasado** entre acertos e precisão:

```
progAcertos = min_acertos[prox] > 0 ? acertos / min_acertos[prox] : 1
progPrec    = piso[prox]       > 0 ? precisao / piso[prox]        : 1
pct         = round(100 * clamp(min(progAcertos, progPrec), 0, 1))
```

Assim a barra nunca "engana": se a precisão é o gargalo, ela não enche só por acumular
acertos. A **dica textual** prioriza o gargalo:

- faltam acertos → `"Faltam N p/ {próximo}"` (+ `"· precisão ≥X%"` se a precisão também falta);
- só precisão → `"Precisão Y% → suba p/ X% e vire {próximo}"`.

`tier == 5` (Mestre): maestria máxima, sem barra.

## Robustez / não-regressão

- `MaestriaService::porMateria()` roda em `try/catch` → devolve `[]` em qualquer falha; a
  view então cai no **texto/painel atual** (fallback), nunca quebra o perfil.
- Nenhuma dependência de dado novo: funciona em instalações **sem** migration.
- Ordem das matérias permanece a de `ASSUNTOS` (estável, comparável entre visitas).
