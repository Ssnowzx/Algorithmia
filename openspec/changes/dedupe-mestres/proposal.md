## Why

A tabela `mestres` está **duplicada** em bancos que rodaram `seeds.sql` mais de uma vez:
10 registros = 5 reais (ids 1–5, referenciados pelas fases) + 5 cópias órfãs (ids 6–10, sem
nenhuma fase) — o **mesmo bug** que a loja teve com `itens`. As cópias não quebram nada
diretamente, mas poluem `Mestre::todosOrdenados()` e qualquer leitura do catálogo de mestres.

## What Changes

Migration idempotente `database/migrations/20260628-dedupe-mestres.sql` que mantém o **menor
id por `svg_slug`** (identificador estável) e remove as cópias, **repointando as fases** para
o id mantido antes de apagar (defensivo). Aplicada pelo `migrate.php`, que a registra em
`migracoes_aplicadas` e a roda uma única vez.

## Fora de escopo

- **Não** altera `seeds.sql` nem adiciona `UNIQUE(svg_slug)` — espelha o dedupe da loja: limpa
  o estado existente; *prevenir* nova duplicação seria um passo à parte.
- Não toca fases, conquistas, regras nem qualquer outra tabela.
