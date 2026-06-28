# Criacao de Assets — Algorithmia

[← Inicio](README.md)

## Estrutura de `public/img/`

```
public/img/
  atores/       -- Retratos ilustrados para o palco de dialogo (fase-*.png, npc-*.png)
  fundos/       -- Cenarios de fundo por regiao (fundo-porto.png, etc.)
  herois/       -- Sprites de heroi (heroi-mago.png) + retratos HUD (hud-mago.png)
  inimigos/     -- Sprites de inimigo (inimigo-kraken.png) -- recorte transparente
  itens/        -- Arte dos itens -- SEM moldura, objeto transparente
  mapas/        -- Icones ilustrados das 35 fases (fase-bug-primordial.png)
  mestres/      -- Cards/retratos dos 5 mestres
  ui/
    icones/     -- Icones de UI (icone-bau.png, icone-mapa.png)
    logos/      -- logo-header.png, logo-marca-ilustrado.png, emblema-algorithmia.svg
    trofeus/    -- Icones de conquista/trofeu
```

O helper `caminhoSvg()` em `helpers.php` mapeia prefixo do slug para a subpasta certa.
`srcImagem()` prefere `.webp` sobre `.png` automaticamente.

## Pipeline de arte — passo a passo

### 1. Briefing

Antes de gerar, consulte o briefing de arte vigente:

- `docs/briefing-arte-v2.md` — estilo geral, bib de paleta, tipografia proibida.
- `docs/briefing-arte-v3.md` — correcao especifica dos itens (objeto transparente, sem moldura).

Regra de ouro: **fantasia epica fundida com tecnologia arcana**. Paleta:
navy `#0b0c1d`, roxo `#7c5cff`/`#9d83ff`, ouro `#ffce47`, runa ciano `#8ce6ff`.
Sem pixel art, sem texto/letras na imagem (exceto glifos runicos decorativos).

### 2. Geracao via IA

A IA deve ter acesso ao projeto para consultar slugs, cores e referencias visuais
existentes (ex.: `public/img/inimigos/inimigo-kraken.png` como referencia de recorte).

### 3. Recorte de fundo (rembg)

Para sprites que precisam de fundo transparente (inimigos, herois, icones, trofeus):

```bash
# Ativar o venv dedicado (cria uma vez)
python3.11 -m venv tools/.venv-rembg
tools/.venv-rembg/bin/pip install rembg onnxruntime pillow

# Recortar todos os grupos de uma vez
tools/.venv-rembg/bin/python tools/fundo/recortar_rembg.py --grupos

# Ou arquivos especificos
tools/.venv-rembg/bin/python tools/fundo/recortar_rembg.py public/img/inimigos/inimigo-novo.png
```

O script usa o modelo `isnet-general-use` com `post_process_mask=True` (bordas
anti-aliased, sem sombra de contato). Nao redimensiona — preserva o tamanho original.

**Nao use** `tools/fundo/remover_fundo.py` para sprites — ele so apara margens. Use apenas
para cartas/molduras onde o fundo nao e transparente.

### 4. Conversao para WebP

Apos o recorte, converter para WebP (o jogo prefere `.webp` via `srcImagem()`):

```bash
# cwebp (homebrew: brew install webp)
cwebp -q 90 public/img/inimigos/inimigo-novo.png -o public/img/inimigos/inimigo-novo.webp

# Ou usando o script de otimizacao do projeto
bash tools/imagens/otimizar-imagens.sh
```

O PNG original deve ser mantido como fonte.

### 5. Verificar no jogo

Use `svgSlug('inimigo-novo')` ou o helper `svg('inimigos/inimigo-novo')` em uma
view de teste. O marcador `[square]` aparece quando o asset nao e encontrado.

### 6. Snapshot no historico visual

Quando um conjunto de assets representa um marco visual relevante (nova versao
de arte, novo estilo, novo conjunto de personagens):

1. Crie a subpasta: `docs/evolucao-visual/v3-<nome>/`.
2. Copie os PNGs curados (sem `.webp`, sem rascunhos, sem near-duplicates).
3. Atualize a tabela de versoes em `docs/evolucao-visual/README.md`.
4. Regenere o PDF-galeria:
   ```bash
   python3 tools/pdf/gerar_galeria_evolucao.py
   ```

**Regra de curadoria** (permanente): so imagens que representam um marco real.
Nunca salvar rascunhos, testes ou near-duplicates no historico.

## Itens (regra especial — briefing v3)

Os 19 itens em `public/img/itens/` devem ser **objeto transparente — sem moldura**.
A loja (`app/views/loja/`) e o inventario (`app/views/inventario/`) ja desenham
a carta/borda em CSS. Colocar moldura na imagem cria "carta dentro de carta".

Referencia de como deve ser: `public/img/inimigos/inimigo-kraken.png` (objeto recortado).
Referencia do que **nao** fazer: item antigo com moldura de ouro sobre fundo escuro.

## Logos e identidade visual dos PDFs

A identidade dos PDFs e centralizada em `tools/pdf/marca_pdf.py`. Todo PDF novo deve
importa-lo — nunca recriar emblema, paleta ou fontes do zero:

```python
import sys, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'tools'))
import marca_pdf as marca

fontes = marca.garantir_fontes()   # baixa Cinzel, Pixelify Sans, Rubik se necessario
```

Logos:
- Capa/splash: `public/img/ui/logos/logo-marca-ilustrado.png` (via `marcaHtml('splash')`).
- Header: `public/img/ui/logos/logo-header.png` (via `marcaHtml('header')`).
- Fallback vetorial: `public/img/ui/logos/emblema-algorithmia.svg`.
