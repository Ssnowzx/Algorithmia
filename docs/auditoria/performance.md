# Auditoria de Performance — Algorithmia
_Data: 2026-06-18 | Branch: `refactor/auditoria-qualidade-producao` | Auditor: read-only_

---

## Tabela-Resumo

| # | Severidade | Categoria | Achado | Impacto estimado | Risco de regressão |
|---|-----------|-----------|--------|------------------|--------------------|
| 1 | CRITICO | Assets | 48 PNGs sem .webp (inimigos, itens, mapas, fundos, herois) | +40–70 MB trafegados | SEGURO |
| 2 | CRITICO | Assets | Mestre/inimigo PNGs 1024–1536px servidos em slots de 52–270px | +10–15 MB desnecessários por load | SEGURO |
| 3 | ALTO | CSS/JS | Google Fonts carregado 2x (link + @import) em toda requisição | +100–300ms TTFB; render-blocking | SEGURO |
| 4 | ALTO | CSS/JS | Todos os 4 CSS (108 KB) carregados globalmente; batalha/cena só usam em rotas específicas | +50ms parse em páginas sem batalha | SEGURO |
| 5 | ALTO | DB | `respostas_log` sem índice composto em `(personagem_id, usou_ia)`; `fases.ordem_global` sem índice explícito | table scan à medida que a base cresce | ARRISCADO |
| 6 | MEDIO | DB | `PerfilController`: 5 queries separadas para estatísticas que podem virar 1–2 | +5 round-trips por page load | ARRISCADO |
| 7 | MEDIO | DB | `ConquistaService::avaliarAposFase`: até 4 SELECTs via `concluiu()` em loop por aquisição de "arquivista_do_vazio" | +4 queries a cada vitória em fase secundária | ARRISCADO |
| 8 | MEDIO | Assets | `public/img/mapas/` — 37 PNGs 512×512 sem .webp, todos carregados no mapa (25 MB na pasta) | +2–5 MB por load do mapa | SEGURO |
| 9 | MEDIO | Assets | `docs/evolucao-visual/` — 170 MB em disco (PNGs de arquivo); não servido pela web mas infla o repo/deploy | +170 MB no clone/VPS | SEGURO (não toca código) |
| 10 | BAIXO | JS | `som.js` (20 KB) carregado em todas as páginas via `footer.php` | +20 KB desnecessário em páginas estáticas | SEGURO |
| 11 | BAIXO | DB | `Inventario::adicionar/remover`: 2 queries sequenciais (pegar + update/create) — pode usar UPSERT | +1 query por operação de inventário | ARRISCADO |
| 12 | BAIXO | PHP | `assetV()` chama `filemtime()` por arquivo CSS/JS a cada request; helpers chamam `is_file()` repetidamente sem cache entre helpers | syscalls desnecessários | SEGURO |

---

## CRITICO

### 1. 48 PNGs sem equivalente .webp — `public/img/`

**Diretórios afetados:**
- `public/img/inimigos/` — 23 PNGs, **0 .webp** (2–3 MB cada, 1536×1024 px)
- `public/img/itens/` — 25 PNGs, **0 .webp** (2 MB cada, 1024×1024 px)
- `public/img/mapas/` — 37 PNGs, **0 .webp** (512×512 px, ~600 KB cada)
- `public/img/fundos/` — 9 PNGs, **0 .webp** (1200×800 px, 1–2 MB cada)
- `public/img/herois/heroi-*.png` — 6 PNGs, **0 .webp** (2 MB cada)

**Como identificado:** `srcImagem()` em `app/core/helpers.php:70` prefere .webp mas cai para .png quando não existe. O .htaccess já serve o MIME correto e tem `Expires 1 year` para .webp. O helper está pronto — faltam apenas os arquivos.

**Impacto:** A cada load da tela de mapa, batalha ou inventário, o navegador baixa PNGs de 2–3 MB em vez de .webp equivalentes de 200–400 KB. Estimativa conservadora: 40–70 MB economizados por sessão completa.

**Recomendação (SEGURO):** Rodar `tools/otimizar-imagens.sh` (já existe no repo) ou gerar via `cwebp -q 85 *.png` nas pastas listadas. Sem mudança de código.

---

### 2. Imagens de alta resolução exibidas em slots pequenos — `public/img/mestres/`, `public/img/inimigos/`

| Arquivo | Dimensão real | Dimensão exibida | Tamanho |
|---------|--------------|-----------------|---------|
| `mestres/mestre-clayton.png` | 1024×1536 px | 116 px (mapa) / 220 px (diálogo) | 3.7 MB |
| `mestres/mestre-cesar.png` | 1024×1536 px | 116 px / 220 px | 3.4 MB |
| `inimigos/inimigo-quimera.png` | 1536×1024 px | 270 px (arena) | 3 MB |
| `inimigos/inimigo-godclass.png` | 1536×1024 px | 270 px | 3 MB |
| `ui/splash-cena.png` | 1920×1080 px | fundo fullscreen | 3.1 MB |
| `ui/molduras/ficha-fundo.png` | 1536×1024 px | modal background | 2.2 MB |

**Impacto:** Browser baixa 3–3.7 MB para exibir em um slot de 270 px ou menos — fator de desperdício 5–30×. Em conexão 3G (1.5 Mbps), cada mestre leva ~20s para carregar.

**Recomendação (SEGURO):** Gerar versões redimensionadas (ex.: mestres a 400 px de largura, inimigos a 600 px) e convertê-las para .webp. O `srcImagem()` já usa o .webp automaticamente; o código não muda. Os PNGs originais ficam como fonte de verdade em `docs/evolucao-visual/`.

---

## ALTO

### 3. Google Fonts carregado duas vezes — `app/views/layout/header.php:26` e `public/css/style.css:6`

```
# header.php linha 26:
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Pixelify+Sans...">

# style.css linha 6:
@import url('https://fonts.googleapis.com/css2?family=Pixelify+Sans...');
```

**Impacto:** 2 requisições externas render-blocking para o mesmo recurso. Além disso, o browser deve baixar `style.css` antes de descobrir o `@import`, bloqueando o render em 2 RTTs seguidos. Estimativa: +100–300 ms no LCP em servidores distantes.

**Recomendação (SEGURO):** Remover o `@import` da linha 6 de `style.css` — o `<link>` no `header.php` já faz a requisição. Zero mudança de comportamento visual.

---

### 4. Todos os 4 CSS carregados em todas as rotas — `app/views/layout/header.php:27–30`

```php
<link rel="stylesheet" href="<?= assetV('css/style.css') ?>">   // 64 KB
<link rel="stylesheet" href="<?= assetV('css/cena.css') ?>">    // 8 KB
<link rel="stylesheet" href="<?= assetV('css/mapa.css') ?>">    // 24 KB
<link rel="stylesheet" href="<?= assetV('css/batalha.css') ?>"> // 12 KB
```

`cena.css` e `batalha.css` só são relevantes nas rotas `/historia` e `/batalha`. `mapa.css` (24 KB) só é relevante em `/mapa`.

**Impacto:** 108 KB de CSS parse desnecessário em páginas como login, inventário e ranking. Render-blocking em cada load.

**Recomendação (SEGURO):** Incluir CSS específico no final de cada view (ou via variável `$cssExtra` passada ao layout) em vez de no header global. A refatoração envolve apenas views PHP sem risco de quebra de lógica.

---

## ALTO (Banco de Dados)

### 5. Índices ausentes em `respostas_log` e `fases.ordem_global` — `database/schema.sql`

**`respostas_log`:**
- A tabela tem FK implícita em `personagem_id` e `desafio_id` (InnoDB cria índices para FKs).
- A query `totalUsosIa()` filtra por `personagem_id AND usou_ia = 1` — sem índice composto, escaneia todas as respostas do personagem.
- A query `idsVistos()` faz JOIN `respostas_log → desafios` filtrando por `personagem_id AND d.fase_id` — sem índice composto em `(personagem_id, desafio_id)`, o plano pode ser ineficiente com logs grandes.

**`fases`:**
- `fases.ordem_global` é filtrado/ordenado em todas as queries de mapa mas não tem índice explícito (só PK em `id`).

**`dialogos`:**
- `dialogos.fase_id` tem FK implícita — OK.

**Recomendação (ARRISCADO — altera schema):**
```sql
ALTER TABLE respostas_log
  ADD INDEX idx_rl_pid_ia (personagem_id, usou_ia),
  ADD INDEX idx_rl_pid_did (personagem_id, desafio_id);

ALTER TABLE fases
  ADD INDEX idx_fases_ordem (ordem_global);
```
Risco: baixo em produção com poucos dados; deve ser executado em manutenção breve.

---

## MEDIO

### 6. 5 queries separadas no `PerfilController` — `app/controllers/PerfilController.php:13–32`

```php
$logModel->estatisticasPorAssunto($id)   // 1 query (JOIN)
$conquistaModel->findAll('id ASC')       // SELECT * conquistas
$conquistaModel->obtidasIds($id)         // SELECT conquista_id
$atributos = BatalhaService::atributosCombate($heroi)  // 1 query equipados
$logModel->totalRespostas($id)           // COUNT(*)
$logModel->totalUsosIa($id)             // COUNT(*) WHERE usou_ia=1
(new ProgressoFase())->totalEstrelas($id) // SUM(estrelas)
```

Total: **7 queries** para uma única página de perfil. `totalRespostas`, `totalUsosIa` e `totalEstrelas` podem ser combinados em 1–2 queries com `SUM(CASE WHEN ...)`.

**Recomendação (ARRISCADO — muda model):** Adicionar método `estatisticasCompletas(int $personagemId)` em `RespostaLog` que retorne total, usosIa e totalEstrelas de `progresso_fases` em uma única query com subselect.

---

### 7. Queries em loop no `ConquistaService` — `app/services/ConquistaService.php:64–65`

```php
foreach ($secundarias as $faseId) {
    if (!$this->progresso->concluiu($id, $faseId)) { // SELECT a cada iteração
```

Ao verificar "arquivista_do_vazio" em fase secundária, o código executa até 4 SELECTs individuais em `progresso_fases`. Com o índice `UNIQUE KEY uq_prog (personagem_id, fase_id)` existente, cada query é rápida — mas ainda são 4 round-trips.

**Recomendação (ARRISCADO — muda service):** Substituir por `mapaDoPersonagem()` já carregado (disponível no `BatalhaController` via `$estado`) e verificar no array em memória. Evita todos os 4 SELECTs.

---

### 8. `mapas/` — 37 PNGs 512×512 sem .webp, carregados no mapa — `public/img/mapas/`

A pasta `mapas/` tem 25 MB de PNGs (37 arquivos × ~600 KB médios). Eles aparecem como ícones de fase no mapa em slots de **52–68 px**. WebPs equivalentes teriam ~20–30 KB cada.

**Recomendação (SEGURO):** Gerar .webp para todos os arquivos em `mapas/` via `cwebp -q 85`. O `srcImagem()` já prefere .webp automaticamente. Economia estimada: ~24 MB por load completo do mapa.

---

### 9. `docs/evolucao-visual/` — 170 MB em disco — `docs/evolucao-visual/`

A pasta contém histórico de arte versionado (v1-pixel-art, v2-ilustrações) com 170 MB de PNGs curados. Não é servida pela web, mas infla o clone do repositório e o deploy para VPS.

**Recomendação (SEGURO, sem tocar código):** Mover para Git LFS ou manter em storage externo (Google Drive / S3) e referenciar via link. A regra permanente do CLAUDE.md de não apagar versões anteriores se mantém — apenas o mecanismo de armazenamento muda.

---

## BAIXO

### 10. `som.js` (20 KB) carregado em todas as páginas — `app/views/layout/footer.php:11`

`window.SOM` e `window.JUICE` são inicializados no footer de todas as páginas, mesmo em telas onde não há áudio (ranking, perfil, loja). O peso individual é baixo, mas representa parse JS desnecessário.

**Recomendação (SEGURO):** Mover `som.js` e `ui.js` para carregamento condicional por rota, ou usar `defer` attribute. Atualmente já estão no fim do body, o que mitiga o blocking — impacto real é baixo.

---

### 11. `Inventario::adicionar/remover` — 2 queries por operação — `app/models/Inventario.php`

```php
public function adicionar(...): void {
    $existente = $this->pegar($personagemId, $itemId); // SELECT
    if ($existente) {
        $this->update(...)                             // UPDATE
    } else {
        $this->create(...)                             // INSERT
    }
}
```

**Recomendação (ARRISCADO):** Substituir por `INSERT INTO inventario ... ON DUPLICATE KEY UPDATE quantidade = quantidade + :qtd` — 1 query em vez de 2. O `UNIQUE KEY uq_inv (personagem_id, item_id)` já existe para suportar a sintaxe.

---

### 12. `is_file()` e `filemtime()` sem cache entre requisições — `app/core/helpers.php`

`srcImagem()` tem `static $cache` por slug, mas `assetV()` (linha 39) chama `filemtime()` a cada request para CSS/JS, e várias funções helper (`svgAtor`, `personagemFase`, `svgAtorDialogo`) chamam `is_file()` para slugs que não passam por `srcImagem()`.

**Recomendação (SEGURO):** Extrair um `estatFile(string $path): bool|int` com `static $cache` e usar em todas as chamadas de `is_file()` nos helpers. Alternativa: ativar `opcache.enable=1` no PHP (se ainda não estiver).

---

## O que esta auditoria NÃO encontrou

- Queries N+1 em laço sobre entidades (todos os loops de fase usam dados já carregados em memória no `MapaController`).
- `SELECT *` em views críticas: as queries principais fazem JOIN com colunas explícitas (ex.: `Inventario::doPersonagem`, `Personagem::ranking`). O `Model::findAll/findById` usa `SELECT *`, mas opera sobre tabelas pequenas (conquistas, itens, mestres).
- Ausência de cache HTTP nos assets: `.htaccess` já configura `Expires 1 year` para PNG/WebP/CSS/JS e `?v=filemtime` no helper provê cache-busting.
- Render-blocking de JS: todos os scripts estão no fim do `<body>` (footer.php).
- CSRF e segurança: corretamente implementados; fora do escopo desta auditoria de performance.

---

## Plano de ação priorizado

| Prioridade | Ação | Esforço | Risco |
|-----------|------|---------|-------|
| 1 | Gerar .webp para inimigos, itens, mapas, fundos, herois | 1h (script) | SEGURO |
| 2 | Remover `@import` duplicado de `style.css` | 1 linha | SEGURO |
| 3 | Gerar versões redimensionadas dos mestres/inimigos (400–600px) | 1h (script) | SEGURO |
| 4 | Adicionar índices em `respostas_log` e `fases` | 1 migration | ARRISCADO (baixo) |
| 5 | Incluir CSS de batalha/cena/mapa por rota, não globalmente | 2h | SEGURO |
| 6 | Consolidar 3 COUNT queries do perfil em 1 | 1h | ARRISCADO (médio) |
| 7 | Substituir loop `concluiu()` por lookup no mapa já carregado | 30min | ARRISCADO (baixo) |
| 8 | `Inventario::adicionar/remover` com UPSERT | 30min | ARRISCADO (médio) |
| 9 | Mover `docs/evolucao-visual/` para LFS/storage externo | 1h | SEGURO |
