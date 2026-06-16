## Why

A entrada do jogo tinha **duas telas de início sobrepostas**: a `home-entrada` (a landing
real — logo, botão, link de login e a grade dos 5 mestres) e um **overlay de splash** que
duplicava "logo + Entrar no mundo" sobre a mesma cena. O splash cobria a home (escondendo os
mestres), era reincluído em home/login/header, e o **logout o forçava** via `?splash=1` —
então "Sair" caía nesse overlay duplicado, sem os mestres. Outros pontos da entrada e da
seleção de personagem também estavam quebrados:

- A home ficava **branca/sem fundo** (o shorthand `background:` não definia cor e
  `background-attachment: fixed` não pinta em iOS/Android).
- A **trilha de som de fundo** tocava na entrada.
- Em "Origem do povo", um `<details>` **expandia inline** e, com o grid esticando a linha,
  **desalinhava todos os cards**.
- Os títulos "Nome do herói" e "Escolha sua classe" não usavam a fonte do jogo e ficavam
  mal posicionados.
- O grid de classes **forçava 2 colunas no celular**, esticando os cards.

## What Changes

- **Tela de início única.** Remove o overlay `splash.php` (de home, login e header) e o
  arquivo. A `home-entrada` passa a ser a única landing. O **logout volta para a home limpa**
  (sem `?splash=1`).
- **Fundo confiável da home.** A cena vem de um `<img class="home-entrada-bg">` (padrão já
  usado em mapa/splash), que renderiza em mobile e independe do MIME do WebP; `.pagina-home`
  recebe **cor de fundo sólida** (nunca branco).
- **Som de fundo removido.** A entrada não dispara mais o pad ambiente; ficam só os efeitos
  de clique.
- **Lore em popup.** "Origem do povo" abre um **modal estilo carta** (retrato ilustrado +
  espécie + lore), fechável por ✕ / clique fora / Esc — no lugar do `<details>`.
- **Títulos com a fonte do jogo.** "Nome do herói" e "Escolha sua classe" usam `var(--titulo)`
  (Pixelify Sans), centralizados como títulos de seção.
- **Grid responsivo.** `repeat(auto-fit, minmax(min(100%, 270px), 1fr))`: 1 card por linha no
  celular, 2 no tablet, 3 no desktop, sem estouro horizontal.

## Capabilities

### New Capabilities
- `tela-de-inicio`: landing única do jogo (home-entrada) — sem overlay duplicado, fundo via
  `<img>` com cor de fallback, sem trilha de fundo, e o logout retornando a ela.
- `selecao-de-personagem`: criação de herói — popup de lore, títulos na fonte do jogo e grid
  de classes responsivo.

### Modified Capabilities
<!-- Nenhuma regra de domínio muda: classes, stats, lore, validação e narrativa permanecem
     idênticas. Esta change só reorganiza a apresentação da entrada e da seleção. -->

## Impact

- **Front-end apenas.** Arquivos:
  - `app/views/home/index.php`, `app/views/auth/login.php`, `app/views/layout/header.php`
    (remoção do splash; `<img>` de fundo na home; sem disparo de ambiente).
  - `app/views/auth/criar-personagem.php` (botão + modal de lore; títulos; input centralizado).
  - `app/controllers/AuthController.php` (logout → home).
  - `public/css/style.css` (fundo da home, `.home-entrada-bg`, `.lore-modal-*`,
    `.classe-lore-btn`, títulos `.titulo-escolha-classe`, grid `.classes-grid` responsivo).
  - **Removido:** `app/views/layout/splash.php`.
- **Sem mudanças** em domínio, banco, rotas, mecânica de batalha, segurança (PDO/CSRF/`e()`).
- **Fora de escopo:** responsividade das demais telas (mapa, batalha, loja, inventário,
  perfil, ranking, painel do mestre); refação da pixel art de batalha (a cargo do autor).
