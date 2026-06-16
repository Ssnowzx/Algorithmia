## 1. Tela de início única

- [x] 1.1 Remover o `require` de `splash.php` em `app/views/home/index.php`,
  `app/views/auth/login.php` e `app/views/layout/header.php`
- [x] 1.2 Apagar `app/views/layout/splash.php`
- [x] 1.3 `AuthController::logout()` redireciona para a home limpa (`$this->redirect('')`,
  sem `&splash=1`)

## 2. Fundo confiável da home

- [x] 2.1 Adicionar `<img class="home-entrada-bg">` (cena via `srcImagem('ui/splash-cena')`)
  como primeiro filho de `.home-entrada`
- [x] 2.2 `.pagina-home` com `background: var(--bg)` (cor sólida, nunca branco) e `.home-entrada-bg`
  posicionada (`object-fit: cover`, atrás do overlay/conteúdo)
- [x] 2.3 `AddType image/webp .webp` no `.htaccess` (corrige WebP como fundo CSS em outros usos)

## 3. Som de fundo

- [x] 3.1 Remover o disparo de `window.SOM.ambienteIniciar()` na home; manter os efeitos de clique

## 4. Popup de lore (origem do povo)

- [x] 4.1 Trocar o `<details class="classe-lore">` por `<button class="classe-lore-btn" data-lore-classe>`
- [x] 4.2 Emitir `window.LORE_CLASSES` (nome, espécie, lore, cor, retrato) e um único modal `#loreModal`
- [x] 4.3 JS de abrir/fechar (clique no botão, ✕, clique fora, Esc) populando o modal por classe
- [x] 4.4 CSS `.classe-lore-btn` e `.lore-modal-*` (carta com a cor da classe, retrato `hud-*`, lore)

## 5. Títulos com a fonte do jogo

- [x] 5.1 "Nome do herói" e "Escolha sua classe" com `var(--titulo)`, centralizados; remover estilo inline
  (label vira `.titulo-escolha-classe`); centralizar o input do nome

## 6. Grid de classes responsivo

- [x] 6.1 `.classes-grid` → `repeat(auto-fit, minmax(min(100%, 270px), 1fr))` com `gap: clamp(1rem,3vw,2rem)`;
  remover os overrides de coluna nos `@media` (mantendo só os de padding do painel)
