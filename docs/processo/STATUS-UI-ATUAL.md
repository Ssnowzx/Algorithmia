# Status Visual Atual — Algorithmia

Este documento registra o estado atual das telas e assets visuais aprovados durante a evolução recente da UI.

---

## 1. Tela inicial / Home

### Objetivo visual
- A entrada do jogo deve usar a estética **cyber-fantasia ilustrada**: cenário escuro, castelos/código ao fundo, logo grande e botão ornamental.
- Não voltar para a cena SVG antiga de montanhas/cartoon na home.

### Arquivos principais
| Elemento | Arquivo |
|---|---|
| View da home | `app/views/home/index.php` |
| CSS global/home | `public/css/style.css` |
| Cena de fundo (`<img>`) | `public/img/ui/splash-cena.png` (servida em `.webp`) |
| Botão de entrada | `public/img/ui/botoes/botao-entrar-mundo.png` |
| Logo grande | `public/img/ui/logos/logo-marca-ilustrado.png` |
| Logo compacta do header | `public/img/ui/logos/logo-header.png` |

### Regras
- `marcaHtml('hero')` e `marcaHtml('auth')` usam `logo-marca-ilustrado.png` (evita blur);
  `marcaHtml('header')`/`marcaHtml('rodape')` usam `logo-header.png` (espaços compactos).
- **Tela de início única:** não há mais overlay de splash (`splash.php` foi removido). A
  `home-entrada` é a única entrada — logo, botão, link de login e a grade dos 5 mestres. O
  **logout volta para a home limpa** (sem `?splash=1`). Ver a change OpenSpec `entrada-e-selecao-ux`.
- A cena de fundo vem de um `<img class="home-entrada-bg">` (não `background-image`), e
  `body.pagina-home` tem **cor de fundo sólida** (`var(--bg)`) — a home nunca fica branca.
- A entrada **não toca trilha de fundo**; apenas efeitos de clique.

---

## 2. Seleção de personagem

### Objetivo visual
- A página deve parecer uma seleção de cartas premium de RPG.
- Cada personagem é uma carta com moldura integrada.
- O painel inteiro tem uma moldura maior envolvendo campo de nome, grid de cartas e botão final.

### Arquivos principais
| Elemento | Arquivo |
|---|---|
| View da página | `app/views/auth/criar-personagem.php` |
| CSS dos cards/painel | `public/css/style.css` |
| Moldura do painel inteiro | `public/img/ui/molduras/moldura-selecao-classes.png` |
| Fundo da placa de texto | `public/img/ui/molduras/card-texto-bg.png` |
| Botão final | `public/img/ui/botoes/botao-que-comece-sofrimento.png` |

### Classes CSS importantes
| Classe | Papel |
|---|---|
| `.conteudo-criar-heroi` | Largura máxima da página de criação |
| `.painel-selecao-classes` | Moldura grande do painel inteiro |
| `.campo-nome-heroi` | Campo compacto e destacado para nome do herói |
| `.classes-grid` | Grid das 6 cartas (responsivo: `auto-fit` — 1/2/3 colunas) |
| `.classe-card .corpo` | Corpo de cada carta |
| `.classe-retrato-wrap` / `.classe-retrato` | Área da imagem do personagem |
| `.classe-info` / `.classe-texto` | Área textual integrada à carta |
| `.titulo-escolha-classe` | Título "Escolha sua classe" (fonte do jogo) |
| `.classe-lore-btn` | Botão que abre o popup de lore |
| `.lore-modal-card` | Carta do popup de lore (retrato + espécie + lore) |
| `.botao-sofrimento` | Botão ornamental final |

### Regras de layout
- A moldura externa não pode ser cortada nem coberta pelas cartas.
- Para isso, manter bastante `padding` em `.painel-selecao-classes`, principalmente topo e laterais.
- O campo de nome deve ser visível e opaco, não transparente demais.
- O botão final deve parecer clicável: grande, centralizado, com brilho/pulso.
- Não usar botão roxo padrão nessa tela; usar `botao-que-comece-sofrimento.png`.
- Os títulos "Nome do herói" e "Escolha sua classe" usam a **fonte do jogo** (`var(--titulo)`), centralizados.
- "Origem do povo" abre um **popup** (`.lore-modal-*`) com o retrato `hud-*` — não expande inline. Não voltar ao `<details>` (esticava/desalinhava os cards).
- O grid (`.classes-grid`) é **responsivo** via `auto-fit`: 1 card no celular, 2 no tablet, 3 no desktop. Testar nos três tamanhos.

---

## 3. Personagens jogáveis

As classes atuais são 6:

| Chave | Nome | Espécie | Asset de carta | Sprite de batalha |
|---|---|---|---|---|
| `mago` | Mago do Backend | Humano | `public/img/herois/hud-mago.png` | `heroi-mago.png` |
| `guerreiro` | Guerreiro do Frontend | Humano | `public/img/herois/hud-guerreiro.png` | `heroi-guerreiro.png` |
| `ranger` | Ranger Fullstack | Humano | `public/img/herois/hud-ranger.png` | `heroi-ranger.png` |
| `xeno` | Xeno do DevOps | Xenoíde Insectoide | `public/img/herois/hud-xeno.png` | `heroi-xeno.png` |
| `elfo` | Elfo da UX | Elfo | `public/img/herois/hud-elfo.png` | `heroi-elfo.png` |
| `draconato` | Draconato do Kernel | Draconato | `public/img/herois/hud-draconato.png` | `heroi-draconato.png` |

### Direção dos personagens
- Apenas uma mulher na seleção atual: **Mago do Backend**.
- Ranger deve ser homem negro e sério, sem sorriso.
- Personagens devem ter postura de guerreiros, com armas/equipamentos visíveis.
- Alien deve ser claramente não humano: atualmente **xenoíde insectoide**.
- Cartas devem ter moldura integrada na própria imagem, combinando com o jogo.

---

## 4. Banco e código ligados às classes

| Responsabilidade | Arquivo |
|---|---|
| Definições das classes, stats, lore e cores | `config/config.php` |
| Validação da classe na criação | `app/controllers/AuthController.php` |
| ENUM do schema | `database/schema.sql` |
| Migração para banco existente | `database/migrations/20250616-novas-classes.sql` |
| Mapeamento de retrato HUD/carta | `app/core/helpers.php` (`retratoHud()`) |
| Sprites pixel art de batalha | `tools/arte/pixelart.py` |

### Banco
Para banco já existente, aplicar:

```bash
mysql -u SEU_USUARIO -p algorithmia < database/migrations/20250616-novas-classes.sql
```

---

## 5. Assets gerados nesta etapa

### UI
- `public/img/ui/splash-cena.png`
- `public/img/ui/botoes/botao-entrar-mundo.png`
- `public/img/ui/logos/logo-marca-ilustrado.png`
- `public/img/ui/logos/logo-header.png`
- `public/img/ui/molduras/moldura-selecao-classes.png`
- `public/img/ui/molduras/card-texto-bg.png`
- `public/img/ui/botoes/botao-que-comece-sofrimento.png`

### Personagens
- `public/img/herois/hud-mago.png`
- `public/img/herois/hud-guerreiro.png`
- `public/img/herois/hud-ranger.png`
- `public/img/herois/hud-xeno.png`
- `public/img/herois/hud-elfo.png`
- `public/img/herois/hud-draconato.png`

> **Formato servido:** as ilustrações (ui, mestres, fundos, atores, `hud-*`) são entregues em
> **WebP** otimizado por `tools/imagens/otimizar-imagens.sh`; os PNG acima são a fonte/fallback. A
> pixel art de batalha (`heroi-*`, `inimigos/`, `itens/`) permanece em PNG. O helper
> `srcImagem()` prefere o `.webp` quando existe.

---

## 6. Cuidados para próximas alterações

- Não recortar fundo dos cards de personagem automaticamente; isso destrói brilho/moldura.
- Não esticar logo pequena na splash; usar sempre a marca grande.
- Não duplicar o fundo da home em duas camadas visíveis.
- Não trocar a moldura externa por um painel opaco simples.
- Ao mexer na seleção de personagem, testar em **desktop (3 colunas), tablet (2) e celular (1 card por linha)**.
- Não reintroduzir o overlay de splash; a `home-entrada` é a única tela de início.
- Não voltar a lore para `<details>` inline — usar o popup `.lore-modal-*`.
