# Auditoria: Organização, Docs e Assets — Algorithmia

> Data: 2026-06-18 | Branch: `refactor/auditoria-qualidade-producao` | Status: READ-ONLY

---

## Sumário Executivo

- **5 arquivos mortos confirmados** em `public/img/atores/mestre-*.png` (1.6–1.9 KB cada, lixo pixel art residual) — a pasta `atores/` serve apenas NPCs, mas guardou duplicatas inúteis dos mestres que já vivem em `mestres/` com 3–4 MB cada.
- **`docs/galeria/` é duplicata exata** de `docs/evolucao-visual/v1-pixel-art/_galeria-contact-sheets/` (4 arquivos, MD5 idêntico) — pasta fantasma que sobrou antes da reorganização em `evolucao-visual/`.
- **9 docs com nomes em SCREAMING_CASE** (`HANDOFF-SESSAO.md`, `REGRAS-DO-JOGO.md` etc.) contra a regra do projeto (`lowercase-com-hífen`), enquanto `briefing-arte-v2.md` segue corretamente.
- **23 inimigos + 19 itens + 6 heróis sem `.webp`** — a otimização foi aplicada só em parte do catálogo; `inimigos/`, `itens/` e metade de `herois/` servem somente PNG pesados.
- **`Xiax-Plano-de-Produto-EdTech-v1.1.pdf` solto na raiz** (419 KB) — documento de produto EdTech sem relação com o código; deveria ir para `docs/` ou ser removido.

---

## CRITICO — Ação Imediata

### 1. Assets mortos: `public/img/atores/mestre-*.png`

Cinco PNGs de 1.6–1.9 KB na pasta `public/img/atores/`:

```
mestre-cassandro.png  1822 B
mestre-cesar.png      1657 B
mestre-clayton.png    1806 B
mestre-marcelo.png    1685 B
mestre-willen.png     1907 B
```

São resíduos da era pixel-art que nunca foram deletados. Os equivalentes de produção vivem em `public/img/mestres/` (3–4 MB cada, ilustrações v2). Nenhum código PHP em `app/` faz referência a `atores/mestre-*` — a busca em `helpers.php` usa `atores/` dinamicamente apenas para `npc-*` (anciao, fragmento, narrador).

Os `.webp` de produção em `atores/` (8 KB cada) são igualmente inúteis pois o conteúdo da imagem não tem utilidade visual. São `<img>` que nunca serão renderizados com sentido.

**Ação:** Deletar `public/img/atores/mestre-cassandro.{png,webp}`, `mestre-cesar.{png,webp}`, `mestre-clayton.{png,webp}`, `mestre-marcelo.{png,webp}`, `mestre-willen.{png,webp}` (10 arquivos, ~55 KB).

---

### 2. Pasta duplicata: `docs/galeria/`

`docs/galeria/` contém 4 PNGs com MD5 idêntico a `docs/evolucao-visual/v1-pixel-art/_galeria-contact-sheets/`:

| Arquivo | MD5 |
|---|---|
| `cenarios.png` | `4e8f85f341e3722351c544e869136f9f` (ambos) |
| `inimigos.png` | idêntico |
| `itens.png` | idêntico |
| `personagens.png` | idêntico |

A pasta `galeria/` sobrou como resíduo de antes da reorganização em `evolucao-visual/`. Não há referências a `docs/galeria/` no código ou docs.

**Ação:** Deletar `docs/galeria/` inteiro (4 arquivos, ~63 KB).

---

### 3. PDF fora de lugar na raiz: `Xiax-Plano-de-Produto-EdTech-v1.1.pdf`

Documento de produto EdTech (419 KB) na raiz do repositório — quebra a limpeza da raiz e não pertence ao código. O nome não segue `lowercase-com-hífen` e o prefixo `Xiax-` não é terminologia do projeto.

**Ação:** Mover para `docs/` (ou descartar se for referência externa já absorvida).

---

## ALTO — Naming / Organização

### 4. Nomes SCREAMING_CASE em `docs/`

O projeto define `lowercase-com-hífen` para arquivos. Violações em `docs/`:

| Arquivo atual | Destino proposto |
|---|---|
| `docs/HANDOFF-SESSAO.md` | `docs/handoff-sessao.md` |
| `docs/ARQUITETURA-IMAGENS.md` | `docs/arquitetura-imagens.md` |
| `docs/DEPLOY.md` | `docs/deploy.md` |
| `docs/FLUXO-OPENSPEC.md` | `docs/fluxo-openspec.md` |
| `docs/PROMPT-EVOLUCAO-VISUAL.md` | `docs/prompt-evolucao-visual.md` |
| `docs/PROMPT-MESTRE-SVG-JOGO-COMPLETO.md` | `docs/prompt-mestre-svg-jogo-completo.md` |
| `docs/REGRAS-DO-JOGO.md` | `docs/regras-do-jogo.md` |
| `docs/ROTEIRO-NARRATIVO.md` | `docs/roteiro-narrativo.md` |
| `docs/STATUS-UI-ATUAL.md` | `docs/status-ui-atual.md` |
| `docs/Fases-e-Topicos-por-Mestre.pdf` | `docs/fases-e-topicos-por-mestre.pdf` |

`briefing-arte-v2.md` e `briefing-arte-v3.md` ja seguem o padrao correto.

**Observacao:** `CLAUDE.md` e `README.md` sao excecoes convencionais do ecosistema (GitHub e Claude Code os reconhecem por nome exato) — nao renomear.

---

### 5. `public/img/ui/` — arquivos fora das subpastas

Dois arquivos estao diretamente na raiz de `public/img/ui/` em vez de numa subpasta semantica:

| Arquivo | Subpasta proposta |
|---|---|
| `public/img/ui/placeholder.png` | `public/img/ui/misc/placeholder.png` |
| `public/img/ui/splash-cena.png` | `public/img/ui/fundos/splash-cena.png` (ou `public/img/fundos/`) |

As mesmas imagens aparecem soltas na raiz de `docs/evolucao-visual/v2-ilustracoes/ui/` tambem.

---

### 6. `public/img/mestres/` — PNGs originais enormes servidos publicamente

Os 5 mestres em `public/img/mestres/` sao PNGs de 3.4–3.9 MB cada (total ~18 MB servidos publicamente). O par `.webp` (156–241 KB) e o correto para producao. Manter ambos no diretorio publico é certo (o `.webp` é servido via `AddType`), mas os PNGs originais de ~3.5 MB deveriam idealmente estar no historico (`docs/evolucao-visual/`) e nao expostos em `public/`.

**Observacao:** Ja existem copias em `docs/evolucao-visual/v2-ilustracoes/atores/`. Analisar se `public/img/mestres/*.png` podem ser movidos para o historico e servir apenas `.webp` em producao.

---

## MEDIO — Consolidacao de Docs

### 7. `docs/HANDOFF-SESSAO.md` esta desatualizado

O handoff documenta o estado em `2026-06-16` e menciona "sem push nem PR (tudo local)". O branch ja foi pushado e o handoff refere a branch `refactor/auditoria-qualidade-producao` com 47 commits — o documento sera confuso em sessoes futuras.

**Acao:** Sobrescrever com handoff atualizado ao fechar esta sessao, ou arquivar em `docs/handoffs/` com data no nome (`handoff-2026-06-16.md`) e criar novo `HANDOFF-SESSAO.md` vazio/atual.

---

### 8. Tres changes do OpenSpec implementadas e nao arquivadas

As seguintes changes em `openspec/changes/` tem todas as tasks marcadas `[x]` mas nao foram arquivadas:

| Change | Status real |
|---|---|
| `som-e-juice-batalha` | Totalmente implementada (ver memoria do projeto) |
| `entrada-e-selecao-ux` | Totalmente implementada (todas tasks `[x]`) |
| `cena-svg-titulo` | Totalmente implementada (todas tasks `[x]`) |

`cenarios-parallax-regioes` nao foi avaliada (tasks nao inspecionadas).

**Acao:** Rodar `/openspec-archive-change` para `som-e-juice-batalha`, `entrada-e-selecao-ux`, e `cena-svg-titulo`.

---

### 9. `docs/STATUS-UI-ATUAL.md` tem sobreposicao com `docs/ARQUITETURA-IMAGENS.md`

Ambos documentam caminhos de imagens e regras de uso. `STATUS-UI-ATUAL.md` e mais narrativo (estado das telas); `ARQUITETURA-IMAGENS.md` e mais estrutural (mapeamento pasta/uso). Nao sao duplicatas, mas a separacao poderia ser mais clara — considerar fusao em `docs/arquitetura-ui.md`.

---

## BAIXO — Melhorias

### 10. `tools/` nao tem README

A pasta `tools/` contem 19 scripts Python e 2 shell scripts mas nenhum `README.md`. Um desenvolvedor novo nao saberá o que cada script faz, a ordem de execucao nem as dependencias (`rembg`, `Pillow` etc.).

**Acao:** Criar `tools/README.md` documentando: proposito de cada script, dependencias, ambiente (`.venv-rembg`), e comandos de uso.

---

### 11. Inimigos, itens e herois sem `.webp` — otimizacao incompleta

A otimizacao WebP foi aplicada a `fundos/`, `atores/`, `mestres/`, `mapas/`, `ui/` mas nao a:

| Diretorio | PNGs sem .webp |
|---|---|
| `public/img/inimigos/` | 23 de 23 (100%) |
| `public/img/itens/` | 19 de 19 (100%) |
| `public/img/herois/` (so `heroi-*`) | 6 de 6 (heroi-elfo, heroi-ranger, heroi-guerreiro, heroi-draconato, heroi-mago, heroi-xeno) |

**Acao:** Rodar `tools/imagens/otimizar-imagens.sh` sobre `inimigos/`, `itens/` e os `heroi-*` restantes em `herois/`.

---

### 12. `historia.html` na raiz

`historia.html` (281 linhas) esta na raiz do repositorio. Nao e uma view PHP servida pelo router (sem referencia em `.htaccess` nem em controllers). Parece ser um arquivo de prototipo/referencia narrativa.

**Acao:** Mover para `docs/historia.html` ou converter para `.md` se for documento de referencia, ou deletar se for protótipo obsoleto.

---

### 13. `.DS_Store` nao rastreados mas presentes no disco

Ha 4 arquivos `.DS_Store` no disco (raiz, `docs/`, `public/`, `public/img/`). Ja estao no `.gitignore` e nao estao rastreados pelo git. Nenhuma acao necessaria no git, mas podem ser deletados localmente.

---

## Exclusoes Seguras

| Arquivo / Pasta | Justificativa | Status |
|---|---|---|
| `public/img/atores/mestre-cassandro.{png,webp}` | Pixel art morta (~1.8KB PNG), sem referencia no codigo | SEGURO |
| `public/img/atores/mestre-cesar.{png,webp}` | Idem | SEGURO |
| `public/img/atores/mestre-clayton.{png,webp}` | Idem | SEGURO |
| `public/img/atores/mestre-marcelo.{png,webp}` | Idem | SEGURO |
| `public/img/atores/mestre-willen.{png,webp}` | Idem | SEGURO |
| `docs/galeria/` (4 arquivos) | MD5 identico a `evolucao-visual/v1-pixel-art/_galeria-contact-sheets/` | SEGURO |
| `Xiax-Plano-de-Produto-EdTech-v1.1.pdf` | Documento externo sem relacao com o codigo | PRECISA-CONFIRMAR |
| `historia.html` | Possivel prototipo obsoleto — verificar se ha valor narrativo | PRECISA-CONFIRMAR |
| `docs/HANDOFF-SESSAO.md` (arquivar, nao deletar) | Informacao historica — versionar em `docs/handoffs/` | PRECISA-CONFIRMAR |

---

## Arvore-Alvo Proposta

```
TrabalhoWillen2/
├── .claude/
├── .codex/
├── .cursor/
├── .gitignore
├── .htaccess
├── CLAUDE.md                          (manter — convencao Claude Code)
├── README.md                          (manter — convencao GitHub)
├── index.php
├── deploy.sh
│
├── app/                               (OK — MVC limpo)
│   ├── controllers/
│   ├── core/
│   ├── models/
│   ├── services/
│   └── views/
│
├── config/                            (OK)
│   ├── bestiario.php
│   ├── config.php
│   └── db.php
│
├── database/                          (OK)
│   ├── banco-questoes/
│   ├── migrations/
│   ├── migrate.php
│   ├── patch-narrativa.php
│   ├── schema.sql
│   ├── seed-banco-questoes.php
│   ├── seed-conta-demo.php
│   ├── seed_remote.sh
│   └── seeds.sql
│
├── docs/
│   ├── handoffs/                      (NOVO — arquivar handoffs por data)
│   │   └── handoff-2026-06-16.md
│   ├── codex/
│   ├── evolucao-visual/
│   │   ├── v1-pixel-art/
│   │   └── v2-ilustracoes/
│   ├── materias/
│   ├── auditoria/                     (NOVO — este arquivo)
│   ├── arquitetura-imagens.md         (renomear de ARQUITETURA-IMAGENS.md)
│   ├── deploy.md                      (renomear de DEPLOY.md)
│   ├── fluxo-openspec.md              (renomear de FLUXO-OPENSPEC.md)
│   ├── handoff-sessao.md              (renomear de HANDOFF-SESSAO.md)
│   ├── prompt-evolucao-visual.md      (renomear)
│   ├── prompt-mestre-svg-jogo-completo.md (renomear)
│   ├── regras-do-jogo.md              (renomear de REGRAS-DO-JOGO.md)
│   ├── roteiro-narrativo.md           (renomear de ROTEIRO-NARRATIVO.md)
│   ├── status-ui-atual.md             (renomear de STATUS-UI-ATUAL.md)
│   ├── briefing-arte-v2.md            (OK)
│   ├── briefing-arte-v3.md            (OK)
│   ├── fases-e-topicos-por-mestre.pdf (renomear de Fases-e-Topicos-por-Mestre.pdf)
│   └── Evolucao-Visual.pdf            (em evolucao-visual/ — OK)
│   [REMOVER: galeria/]
│
├── openspec/
│   ├── config.yaml
│   └── changes/
│       ├── cenarios-parallax-regioes/ (verificar status)
│       [ARQUIVAR: som-e-juice-batalha/]
│       [ARQUIVAR: entrada-e-selecao-ux/]
│       [ARQUIVAR: cena-svg-titulo/]
│
├── public/
│   ├── favicon.png
│   ├── css/
│   ├── js/
│   └── img/
│       ├── atores/
│       │   ├── npc-anciao.{png,webp}      (manter)
│       │   ├── npc-fragmento.{png,webp}   (manter)
│       │   ├── npc-narrador.{png,webp}    (manter)
│       │   [REMOVER: mestre-*.{png,webp}]
│       ├── fundos/                        (OK — todos com .webp)
│       ├── herois/                        (pendente: gerar webp para heroi-*)
│       ├── inimigos/                      (pendente: gerar .webp para todos)
│       ├── itens/                         (pendente: gerar .webp para todos)
│       ├── mapas/                         (OK — todos com .webp)
│       ├── mestres/                       (OK — considerar remover PNG ~3.5MB)
│       └── ui/
│           ├── botoes/
│           ├── icones/
│           ├── logos/
│           ├── molduras/
│           ├── trofeus/
│           ├── placeholder.png            (mover para subpasta misc/ ou fundos/)
│           └── splash-cena.png            (mover para subpasta fundos/)
│
└── tools/
    ├── README.md                          (CRIAR)
    ├── marca_pdf.py
    ├── marca.py
    ├── gerar_pdf_fases.py
    ├── gerar_galeria_evolucao.py
    ├── pixelart.py
    ├── ...
    ├── .cache/                            (gitignored — OK)
    └── .venv-rembg/                       (gitignored — OK)
```

---

## Inconsistencias de Nomenclatura

| Arquivo atual | Violacao | Regra aplicavel |
|---|---|---|
| `docs/HANDOFF-SESSAO.md` | SCREAMING_CASE | `lowercase-com-hífen` |
| `docs/ARQUITETURA-IMAGENS.md` | SCREAMING_CASE | `lowercase-com-hífen` |
| `docs/DEPLOY.md` | SCREAMING_CASE | `lowercase-com-hífen` |
| `docs/FLUXO-OPENSPEC.md` | SCREAMING_CASE | `lowercase-com-hífen` |
| `docs/PROMPT-EVOLUCAO-VISUAL.md` | SCREAMING_CASE | `lowercase-com-hífen` |
| `docs/PROMPT-MESTRE-SVG-JOGO-COMPLETO.md` | SCREAMING_CASE | `lowercase-com-hífen` |
| `docs/REGRAS-DO-JOGO.md` | SCREAMING_CASE | `lowercase-com-hífen` |
| `docs/ROTEIRO-NARRATIVO.md` | SCREAMING_CASE | `lowercase-com-hífen` |
| `docs/STATUS-UI-ATUAL.md` | SCREAMING_CASE | `lowercase-com-hífen` |
| `docs/Fases-e-Topicos-por-Mestre.pdf` | PascalCase misturado | `lowercase-com-hífen` |
| `Xiax-Plano-de-Produto-EdTech-v1.1.pdf` | PascalCase + raiz | `lowercase-com-hífen` + deve ir para `docs/` |
| `database/seed_remote.sh` | `snake_case` | `lowercase-com-hífen` → `seed-remote.sh` |

### Conformes (exemplos positivos)

| Arquivo | Status |
|---|---|
| `docs/briefing-arte-v2.md` | Correto |
| `docs/briefing-arte-v3.md` | Correto |
| `database/seed-banco-questoes.php` | Correto |
| `database/seed-conta-demo.php` | Correto |
| Todos os assets em `public/img/**/*.{png,webp}` | Corretos (lowercase-hífen) |

---

*Gerado por auditoria automatizada em 2026-06-18. Nenhuma alteracao foi feita — documento apenas de analise.*
