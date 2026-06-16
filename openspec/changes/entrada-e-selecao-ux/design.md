## Camada MVC

Mudança quase toda na camada de **View** (`app/views/`) e nos estilos
(`public/css/style.css`), com um único ajuste de **Controller**
(`AuthController::logout`). Nenhum Model, Service ou alteração de banco.

## Decisões

- **Splash × home (deduplicação):** o overlay `splash.php` duplicava a `home-entrada`
  (mesmo logo + "Entrar no mundo"). Em vez de manter os dois, a `home-entrada` — mais rica e
  com os 5 mestres — vira a **única** entrada; o overlay é removido. A cena do splash
  (`splash-cena`) é preservada como **fundo** da home.
- **Fundo via `<img>` e não `background-image`:** `background-image: url(.webp)` falha como
  fundo CSS em hosts que servem WebP como `octet-stream` e em mobile com `background-
  attachment: fixed`. Um `<img>` posicionado (z-index atrás do conteúdo) é confiável e é o
  padrão já usado em mapa (`.cena-bioma-img`) e no antigo splash. `.pagina-home` ganha
  `background: var(--bg)` para nunca renderizar branco.
- **Popup em vez de `<details>`:** o `<details>` mudava a altura do card; com o grid
  esticando a linha (`align-items: stretch`), isso desalinhava todos os cards. O modal não
  altera o fluxo dos cards. Dados de lore vão num objeto JS (`window.LORE_CLASSES`) e um único
  modal é populado no clique.
- **Imagem do popup:** reaproveita o retrato ilustrado `hud-*` (`retratoHud()` →
  `srcImagem()`), que **combina** com as cartas; pixel art tosca destoaria das ilustrações.
- **Grid `auto-fit` em vez de breakpoints manuais:** elimina o "2 colunas no celular" e o
  estouro horizontal via `minmax(min(100%, 270px), 1fr)`, sem depender de larguras fixas de
  dispositivo. A altura igual dos cards (premium no desktop) é mantida; no celular cada linha
  tem 1 card, então não há esticamento.
- **Fonte dos títulos:** `var(--titulo)` (Pixelify Sans) já é carregada via `@import` em
  `style.css`, então funciona na página de criação mesmo sem `<link>` de fontes no `<head>`.
