# 🛠️ tools/ — Ferramentas do projeto

Scripts utilitários organizados por finalidade. Cada subpasta agrupa um conjunto
coeso (com seus `import` internos); rode os scripts a partir da raiz do projeto.

| Pasta | O que faz | Principais scripts |
|-------|-----------|--------------------|
| [`arte/`](arte/) | Geração de pixel art, sprites e cenários | `pixelart.py` (base), `cenarios.py`, `cenario_vila_centralizado.py`, `atores.py`, `bestiario.py`, `itens.py`, `ui.py`, `marca.py`, `personagens_fase.py` |
| [`fundo/`](fundo/) | Remoção/recorte de fundo das artes | `recortar_rembg.py`, `remover_fundo.py`, `remover_fundo_logo.py`, `ator_pipeline.py`, `remove_fundo_atores.py` |
| [`imagens/`](imagens/) | Otimização de assets de imagem | `right_size_imagens.py`, `otimizar-imagens.sh` |
| [`pdf/`](pdf/) | PDFs de marca (kit + geradores) | `marca_pdf.py` (kit, base), `gerar_pdf_fases.py`, `gerar_galeria_evolucao.py` |
| [`cinematicas/`](cinematicas/) | Intros em vídeo das fases | `gerar_cinematic_intro.py` + pacote `cinematic_intro/` |
| [`diagnostico/`](diagnostico/) | Verificações de regras do jogo (PHP) | `verificar_maestria.php`, `verificar_missoes.php`, `verificar_onboarding.php`, `verificar_regioes.php` |
| [`deploy/`](deploy/) | Infra / dev local | `setup_vps.sh`, `dev-server-router.php` |

> Pastas ocultas (não versionadas): `.cache/` (fontes baixadas), `.venvs/` (venv do
> Kokoro), `.venv-rembg/` — todas no `.gitignore`.

## Atalhos mais usados

```bash
# Cinemáticas (intros das fases) — ver docs/desenvolvimento/cinematicas.md
python3 tools/cinematicas/gerar_cinematic_intro.py --listar
python3 tools/cinematicas/gerar_cinematic_intro.py <slug>

# PDFs de marca — ver CLAUDE.md (identidade visual)
python3 tools/pdf/gerar_pdf_fases.py
python3 tools/pdf/gerar_galeria_evolucao.py

# Servidor de dev com streaming de vídeo (Range/206; php -S puro não faz)
php -S 127.0.0.1:8000 tools/deploy/dev-server-router.php
```

## Convenção interna (importante ao mover scripts)

- Cada `.py` resolve a **raiz do projeto** subindo da própria localização
  (`tools/<pasta>/x.py` → 3 níveis acima). Ao criar/mover um script, mantenha essa
  contagem coerente com a profundidade.
- Os `import` entre scripts são **relativos à pasta** (ex.: `arte/` tem `pixelart`,
  `marca`, `cenarios` juntos). Mantenha um cluster acoplado **na mesma subpasta**.
