# Design — Dedupe de `mestres`

## Camada
Apenas **database** — uma migration SQL incremental (`database/migrations/`), aplicada pelo
`migrate.php` (mecanismo `migracoes_aplicadas`, roda uma vez por arquivo).

## Estratégia (espelha o dedupe de `itens`)

1. **Repointar** `fases.mestre_id` da cópia (id maior) para o id mantido (menor id do mesmo
   `svg_slug`) — defensivo; no banco observado nenhuma fase aponta para as cópias, mas outros
   bancos podem diferir.
2. **Apagar** as cópias via self-join (`k.svg_slug = d.svg_slug AND k.id < d.id`).

`svg_slug` é a chave de agrupamento por ser o identificador estável do mestre (usado em
`REGIOES_MESTRE`), e não o nome de exibição.

## Robustez

- **Idempotente:** num banco já limpo, nenhum par casa no self-join → nada é apagado; o UPDATE
  não encontra divergência → no-op.
- A **única FK** para `mestres` é `fases.mestre_id` (`ON DELETE SET NULL`), repointada antes do
  DELETE — nenhuma fase fica órfã por engano.
