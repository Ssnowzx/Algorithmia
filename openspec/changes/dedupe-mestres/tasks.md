# Tasks — Dedupe de `mestres`

- [x] 1. `database/migrations/20260628-dedupe-mestres.sql` (idempotente; repointa fases, mantém menor id por `svg_slug`)
- [x] 2. Aplicar via `php database/migrate.php` e verificar: 5 mestres (ids 1–5), fases por mestre intactas (6 cada), `RegiaoService::dominio` → 5 regiões, mapa sem regressão
- [x] 3. `openspec validate dedupe-mestres` OK
