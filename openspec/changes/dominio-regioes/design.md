# Design — Domínio das Regiões (maestria horizontal)

## Camada MVC

| Camada | Arquivo | Papel |
|--------|---------|-------|
| Config | `config/config.php` | `REGIAO_FAIXAS` (rótulos dos estados) + título lenda |
| Model | `app/models/Mestre.php` | `progressoPorRegiao()` — 1 query agregada |
| Service | `app/services/RegiaoService.php` | `faixaDe()` pura + `dominio()` defensivo |
| Controller | `app/controllers/PerfilController.php` | injeta `$regioes` |
| View / CSS | `app/views/perfil/index.php`, `public/css/style.css` | painel "🏰 Domínio das Regiões" |

## Modelo de domínio (por região)

A partir de `total` (fases jogáveis do mestre), `concluidas`, `perfeitas` (3 estrelas) e
`estrelas` (soma), define-se o **estado** e a barra:

| Estado | Regra | Cor |
|--------|-------|-----|
| A explorar | `concluidas == 0` | apagado (cinza) |
| Em jornada | `0 < concluidas < total` | cor do mestre (`cor_tema`) |
| Conquistada | `concluidas == total && perfeitas < total` | cor do mestre (forte) |
| Dominada | `concluidas == total && perfeitas == total` | ouro |

- **Barra (perfeição)** = `estrelas / (3 * total)` em %, contínua (reflete conclusão *e*
  perfeição num só número).
- **"Dominada"** (resumo `X/5`) exige perfeição total — incentiva voltar e refazer fases sem
  erro e sem IA (o eixo pós-nível).
- **Dica (goal-gradient):** Em jornada → "Faltam N fases"; Conquistada → "Perfeccione N p/
  dominar"; Dominada → "Domínio total".
- Cada região usa a `cor_tema` do mestre como acento (identidade), e o estado modula (Dominada
  ganha brilho dourado; A explorar fica dessaturada).

### Por que só fases jogáveis
Estrelas só existem em combate. Fases de **história** (1–2 por mestre) não têm estrelas, então
entram não como mérito mas como ruído — são excluídas (`tipo <> 'historia'`) do total/perfeição.

## Query (robusta à duplicação de mestres)

`progressoPorRegiao()` parte de **`fases`** (não de `mestres`), de modo que só aparecem os
mestres efetivamente referenciados por fases (os 5 reais; os 5 duplicados órfãos não têm fases):

```sql
SELECT m.id, m.ordem, m.regiao, m.titulo, m.cor_tema, m.svg_slug,
       COUNT(f.id) AS total,
       COUNT(pf.fase_id) AS concluidas,
       COALESCE(SUM(pf.estrelas), 0) AS estrelas,
       COALESCE(SUM(pf.estrelas = 3), 0) AS perfeitas
FROM fases f
JOIN mestres m ON m.id = f.mestre_id
LEFT JOIN progresso_fases pf ON pf.fase_id = f.id AND pf.personagem_id = :p
WHERE f.mestre_id IS NOT NULL AND f.tipo <> 'historia'
GROUP BY m.id, m.ordem, m.regiao, m.titulo, m.cor_tema, m.svg_slug
ORDER BY m.ordem
```

## Robustez / não-regressão

- `RegiaoService::dominio()` em `try/catch` → `[]`; a view só desenha o painel quando há
  regiões. Nada quebra o perfil.
- Read-only: nenhuma escrita, nenhuma conquista concedida, nenhuma migration.
- `faixaDe()` é pura (testável) e independente do banco.
