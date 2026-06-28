# 🗂️ Arquitetura de Imagens — Algorithmia

Como os assets visuais são **organizados em pasta** e **resolvidos por slug** no
código. Leia antes de adicionar/mover qualquer imagem.

---

## 1. Mapa de pastas (`public/img/`)

| Pasta | Conteúdo | Estilo | Servido como |
|---|---|---|---|
| `atores/` | Retratos de diálogo (mestres + NPCs, com alfa) | Ilustrado | `.webp` (fallback `.png`) |
| `mestres/` | Retrato do mestre na **home** e no **mapa** | Ilustrado | `.webp` |
| `fundos/` | Cenários de batalha/regiões (`fundo-*`) | Ilustrado | `.webp` |
| `mapas/` | Ícones de fase/região do mapa (`fase-*`, `regiao-*`) | Ilustrado | `.webp` |
| `herois/` | Cartas de criação (`hud-*`), carta (`card-*`) **e** sprites de batalha (`heroi-*`) | hud/card ilustrado · heroi pixel | hud/card `.webp` · heroi `.png` |
| `inimigos/` | Sprites de batalha (`inimigo-*`) | Pixel art | `.png` |
| `itens/` | Ícones de item/loja (`item-*`) | Pixel art | `.png` |
| `ui/` | Interface — **subdividida** (ver §2) | Misto | `.webp`/`.png` |

> `atores/mestre-*` **não é duplicata** de `mestres/mestre-*`: são artes
> distintas (ator de diálogo com alfa vs. retrato da home/mapa). Ambas usadas.

### `ui/` (subpastas)

| Subpasta | Conteúdo |
|---|---|
| `ui/logos/` | `logo-header`, `logo-marca-ilustrado` |
| `ui/botoes/` | `botao-entrar-mundo`, `botao-que-comece-sofrimento` |
| `ui/molduras/` | `moldura-selecao-classes`, `moldura-status`, `card-status-heroi`, `card-texto-bg`, `ficha-fundo`, (`moldura-barra-fina` quando existir) |
| `ui/icones/` | `icone-ouro`, `icone-mapa`, `icone-ia`, `icone-coracao`, `icone-estrela`, `icone-nivel`, `icone-bau`, `icone-final` |
| `ui/trofeus/` | `troxeu-bronze/prata/ouro`, `conquista-generica` |
| `ui/` (raiz) | `splash-cena`, `placeholder` |

---

## 2. Como o código resolve um asset

Tudo passa por helpers em **`app/core/helpers.php`**. Não monte caminhos à mão.

| Origem do slug | Função | Regra |
|---|---|---|
| **Banco** (`svg_slug`: `inimigo-bug`, `item-espada`, `troxeu-ouro`, `icone-ia`, `conquista-generica`…) | `caminhoSvg()` / `svgSlug()` | Mapa de prefixo → pasta. `icone-`→`ui/icones/`, `troxeu-`→`ui/trofeus/`, `conquista-`→`ui/trofeus/`, `mestre-`→`mestres/`, `inimigo-`/`npc-`→`inimigos/`, `item-`→`itens/`, `heroi-`→`herois/`. Sem prefixo conhecido → `ui/`. |
| **Diálogo** (ator em cena) | `svgAtor()` / `caminhoAtor()` | `mestre-`/`npc-`/`fase-` → `atores/` (com fallback a `mestres/`). |
| **Logo** (header/hero/auth/rodapé/splash) | `marcaHtml()` | Sempre `ui/logos/`. |
| **Caminho explícito** numa view/CSS | `svg('ui/icones/icone-ouro')`, `srcImagem('ui/molduras/...')` | Passe o caminho **com a subpasta**. |

`srcImagem()` recebe o slug **com a subpasta** (ex.: `ui/icones/icone-ouro`),
prefere o `.webp`, cai para `.png`, e versiona por `?v=filemtime`.

---

## 3. Adicionar um asset novo (checklist)

1. Escolha a pasta certa pela tabela acima (UI vai numa subpasta de `ui/`).
2. Coloque o `.png` fonte; gere o `.webp` com **`bash tools/imagens/otimizar-imagens.sh`**
   (anda recursivo, então pega as subpastas; **não** toca pixel art).
3. Referencie sempre por **slug com subpasta** (`svg('ui/icones/...')`) — ou, se
   vier do banco, garanta que o prefixo esteja no mapa de `caminhoSvg()`.
4. Pixel art (`heroi-*`, `inimigo-*`, `item-*`) permanece **só em PNG**.

---

## 4. Geradores em `tools/` (pixel art autoral)

| Script | Saída |
|---|---|
| `tools/arte/ui.py` | `ui/icones/*`, `ui/trofeus/*`, `ui/placeholder` |
| `tools/fundo/remover_fundo_logo.py` | `ui/logos/logo-marca-ilustrado`, `ui/botoes/botao-entrar-mundo`, `favicon.png` |
| `tools/arte/bestiario.py`, `tools/arte/itens.py`, `tools/arte/pixelart.py` | `inimigos/*`, `itens/*`, `herois/heroi-*` |
| `tools/imagens/otimizar-imagens.sh` | `.webp` ao lado de cada ilustração |

> **Legado:** `tools/arte/marca.py` gerava `ui/logo.png` e `ui/logo-marca.png`, que
> foram **aposentados** (a marca viva é `ui/logos/logo-header` e
> `ui/logos/logo-marca-ilustrado`). Não rode `marca.py` sem repensar a saída.

---

## 5. Aposentados nesta reorganização

- `ui/logo.png` / `ui/logo-marca.png` — logos legados sem uso (a `historia.html`
  agora aponta para `ui/logos/logo-header.png`).
- `inimigos/npc-anciao.png` / `inimigos/npc-narrador.png` — versões pixel
  legadas; os diálogos usam `atores/npc-anciao` e `atores/npc-narrador`.
- `ui/molduras/moldura-barra.png` — tampas ornamentais das laterais do título do
  mapa; removidas a pedido. O título do mapa agora é **limpo** (só o ícone do
  pergaminho + texto), sem `border-image` no `.mapa-cabecalho h1`.
