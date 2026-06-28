# Design System — Algorithmia

> **Fonte única de verdade** para direção visual, assets e regras de UI do jogo.
> Use este arquivo ao gerar arte, CSS ou pedir ajuda à IA. Não invente estilo paralelo.

---

## 1. Identidade

**Algorithmia** = RPG educativo onde *programar é magia*. Tom épico com humor ácido; fantasia tech (código como runas).

**Estética oficial — Cyber-Fantasia Ilustrada:**
- Ilustração digital detalhada (estilo card game / JRPG moderno)
- Noite crepuscular, navy/teal profundo, **ouro** nos detalhes, **ciano elétrico** nas runas de código
- Símbolos de programação integrados ao cenário (`</>`, `{ }`, SQL, POO, etc.) — nunca colados sem contexto
- **Não** usar pixel art nos mestres, fundos de região e ícones do mapa

**Camada legado — Pixel art (mantida):**
- Sprites de heróis em batalha, inimigos e itens em inventário/loja (`tools/arte/pixelart.py`, `tools/arte/bestiario.py`, `tools/arte/itens.py`)
- `image-rendering: pixelated` **somente** nesses sprites — ver `public/css/style.css`

---

## 2. Paleta e tipografia

Usar **apenas** tokens de `:root` em `public/css/style.css`:

| Token | Uso |
|-------|-----|
| `--primaria` / `--primaria-2` | Acento mágico, links, brilho UI |
| `--cor-regiao` (inline por card) | Anéis de nó, borda do card, glow do bioma |
| `--xp` | Fase atual no mapa |
| `--sucesso` | Fase concluída |
| `--hp` / `--mp` / `--ouro` | HUD de batalha |
| `--texto` / `--texto-fraco` | Títulos e legendas |

**Fontes:** Pixelify Sans (títulos/HUD) · Rubik (corpo) · mono (código). Não adicionar fontes sem motivo.

---

## 3. Estrutura de assets (`public/img/`)

```
public/img/
├── mestres/          # Retratos ilustrados dos 5 mestres (~1024×1536, 2:3) — NÃO ALTERAR sem pedido
├── fundos/           # Cenários ativos no jogo (ver §4)
├── fundos_novos/     # Rascunhos/exports alternativos; produção usa fundos/
├── mapas/            # Ícones de fase e emblemas de região (512×512, ver §5) — **só mapa, nunca diálogo**
├── atores/           # Retratos de palco: NPCs e mestres com alpha nativo (ver §5.2)
├── herois/           # Cartas HUD (`hud-*`) + sprites pixel art (`heroi-*`)
├── inimigos/         # Pixel art — bestiário (diálogo + batalha, alpha nativo)
├── itens/            # Pixel art — inventário/loja
└── ui/               # Logos, splash, molduras, botões e ícones de sistema
```

**Helper PHP:** `svg('pasta/arquivo')` em `app/core/helpers.php` → `<img>` de `public/img/{slug}.png`.

**Mapas de região/fase:** `fundoRegiao()`, `iconeFaseMapa()`, `iconeRegiaoMapa()` no mesmo arquivo.

**Status visual recente:** ver também `docs/processo/STATUS-UI-ATUAL.md` para splash/home, seleção de personagem, cartas e assets aprovados.

---

## 4. Cenários de região (`fundos/`)

| Arquivo | Região | Formato | Card no mapa |
|---------|--------|---------|--------------|
| `fundo-vila.png` | Terras de Hello World (início) | **1200×800** paisagem 3:2 | Curto (~3 fases) |
| `fundo-porto.png` | Porto da Sintaxe (Willen) | **1200×800** paisagem 3:2 | Alto (~6 fases) |
| `fundo-cidadela.png` | Cidadela dos Objetos (Clayton) | 1200×800 | Alto |
| `fundo-floresta.png` | Floresta das Estruturas (Marcelo) | 1200×800 | Alto |
| `fundo-montanha.png` | Montanha do Cálculo (Cesar) | 1200×800 | Alto |
| `fundo-torre.png` | Torre das Conexões (Cassandro) | 1200×800 | Alto |
| `fundo-abismo.png` | O Fim da Jornada | **1200×600** paisagem 2:1 | Curto (~2 fases) |
| `fundo-batalha.png` | Arena genérica | 1200×600 | — |
| `fundo-mapa.png` | Pano de fundo da página do mapa (void, trilha dourada, abismo) | **1536×1024** | — (body `.pagina-mapa`) |

### Regras de composição
- **Cards altos (mestres):** composição vertical; cúpulas/torres/cidade no **terço superior**; `background-position: center top`
- **Cards curtos (início/fim):** composição horizontal centrada; `background-position: center center`
- **Nunca** esticar a última linha de pixels para preencher altura — exportar na proporção real
- Overlay escuro do card: gradiente em `.cena-bioma-img-wrap::after` (`mapa.css`) — não remover

### CSS (`public/css/mapa.css`)
- Wrapper: `.cena-bioma-img-wrap.cena-bioma-retrato` | `.cena-bioma-paisagem`
- View: `app/views/mapa/index.php`

### Gerar novo cenário
1. Referência visual: `public/img/mestres/mestre-{nome}.png` + cenário existente do bioma
2. Prompt: cyber-fantasia, código integrado, **sem personagens**, composição para card alto ou curto
3. Exportar **1200×800** (mestres) ou **1200×600** (curto/abismo/batalha)
4. Salvar em `public/img/fundos/` (e opcionalmente `fundos_novos/`)

---

## 5. Mapa — nós e emblemas (`mapas/`)

### Nós com arte (`.no-bolha-arte`)
**Todas as 35 fases** têm ícone em `public/img/mapas/`, mapeadas por `ordem_global` em `iconeFaseMapa()`.

| Região | ordem_global | Slugs |
|--------|--------------|-------|
| Hello World | 1–3 | `fase-prologo-despertar`, `fase-primeiros-passos`, `fase-bug-primordial` |
| Porto da Sintaxe | 4–9 | `fase-porto-chegada` … `fase-parse-error-kraken` |
| Cidadela dos Objetos | 10–15 | `fase-cidadela-chegada` … `fase-god-class` |
| Floresta das Estruturas | 16–21 | `fase-floresta-chegada` … `fase-hidra-recursiva` |
| Montanha do Cálculo | 22–27 | `fase-montanha-chegada` … `fase-limite-colosso` |
| Torre das Conexões | 28–33 | `fase-torre-chegada` … `fase-ddos-enxame` |
| Abismo | 34–35 | `fase-abismo-devnull`, `fase-lorde-segfault` |

Lista completa de slugs: ver `$mapa` em `app/core/helpers.php` → `iconeFaseMapa()`.

### Emblemas de região (`iconeRegiaoMapa()`)
| Chave | Arquivo |
|-------|---------|
| `inicio` | `regiao-hello-world.png` |
| `fim` | `regiao-abismo.png` |

### Spec do ícone de fase
- **512×512**, sujeito centralizado, zoom ~72–85% (crop no export) para preencher círculo
- Quadrado fonte OK; no UI vira **círculo cheio** via CSS
- Classe: `.no-bolha-arte` + `.icone-fase`
- Imagem: `position:absolute; inset:0; width:100%; height:100%; object-fit:cover; border-radius:50%`
- Anel: `border-color: color-mix(in srgb, var(--cor-regiao) 55%, #eef0fb)`
- Overlay suave: `::after` com vignette — opacidade ~0.9 na arte, não 1.0

### ⚠️ Conflito CSS crítico
`public/css/style.css` define tamanho fixo para `.no-bolha img` (42px/56px).
**Sempre** excluir nós com arte: `.no-bolha:not(.no-bolha-arte) img`.
Ilustrações usam `image-rendering: auto` (não pixelated).

---

## 5.2 Palco de diálogo — personagem + cenário ⚠️

> **Dois assets distintos.** O fundo do palco vem de `fundos/` (região). O personagem vem de sprite com alpha — **nunca** de `mapas/fase-*.png`.

### Camadas do palco (`app/views/historia/dialogo.php`)

| Camada | Asset | Regra |
|--------|-------|-------|
| **Cenário** | `fundos/fundo-vila.png`, `fundo-porto.png`, … | Sempre visível via `fundoRegiao()` + overlay escuro |
| **Personagem** | `inimigos/`, `atores/`, `mestres/` | PNG com **alpha nativo** — gerado sem fundo, não recortado depois |

### De onde vem cada personagem

| Contexto | Asset | Pasta |
|----------|-------|-------|
| **Palco de diálogo** | `fase-primeiros-passos`, `npc-narrador`, `mestre-willen` | `atores/` — ilustração cyber-fantasia, **alpha nativo** |
| **Batalha** | `inimigo-slime`, … | `inimigos/` — pixel art (`tools/arte/bestiario.py`) |
| **Mapa (nó circular)** | `fase-primeiros-passos`, … | `mapas/` — cenário incluso, **nunca no palco** |

> ⚠️ **Pixel art no palco = erro.** `inimigos/` é só para a tela de batalha.

### Retratos de palco (`atores/fase-*.png`) — opcional, manual

O código **não gera nem recorta** arte. Só usa o arquivo se você colocar em `public/img/atores/`.

1. Exporte PNG com **alpha real** (Photoshop, Procreate, Midjourney com fundo transparente, etc.).
2. Mesmo personagem do ícone do mapa, **sem cenário**.
3. Nome: `fase-{slug}.png` (ex.: `fase-primeiros-passos.png`).
4. Enquanto o arquivo não existir, o palco usa o sprite pixel de `inimigos/` (fallback).

> **Não usar:** gerador de imagem do Cursor, recorte automático de `mapas/`, nem `tools/arte/personagens_fase.py`.

### Pixel art (batalha + fallback no diálogo)

- `tools/arte/bestiario.py` → `public/img/inimigos/`

### Código

- `svgAtor()`, `personagemFase()`, `slugAtorDialogo()` em `app/core/helpers.php`
- CSS do palco: `public/css/mapa.css` (`.palco-cena`, `.ator-pixel`, `.ator-mestre`, `.ator-npc`)

---

## 5.1 Página do mapa-múndi — UI aprovada ⚠️

> **Status: finalizado e aprovado.** Não remover nem substituir por padrões genéricos (emoji, fundo sólido, cards opacos) sem pedido explícito do usuário.
> Regra Cursor: `.cursor/rules/mapa-pagina-aprovada.mdc`

### Três camadas visuais (não confundir)

| Camada | Asset | Onde |
|--------|-------|------|
| **Fundo da página** | `public/img/fundos/fundo-mapa.png` | `body.pagina-mapa` — void escuro, ilhas, trilha dourada, vórtice `/dev/null` |
| **Ícone do título** | `public/img/ui/icones/icone-mapa.png` | Cabeçalho `<h1>` — pergaminho mágico cyber-fantasia (**não** emoji 🗺️) |
| **Arte dentro de cada card** | `fundo-vila.png`, `fundo-porto.png`, … | `.cena-bioma-img-wrap` por região — independente do pano |

### Contrato de código

| O quê | Arquivo |
|-------|---------|
| `bodyClass: 'pagina-mapa'` | `app/controllers/MapaController.php` |
| `svg('ui/icones/icone-mapa', 'icone-mapa-titulo')` no `<h1>` | `app/views/mapa/index.php` |
| Fundo fixo + cards translúcidos | `public/css/mapa.css` (blocos `body.pagina-mapa`) |

### Cards translúcidos (`.pagina-mapa .regiao`)

- Fundo do card quase transparente + `::before` com `backdrop-filter: blur(16px)` (no pseudo-elemento, **não** no `.regiao` — `overflow: hidden` quebra o blur).
- Arte do bioma (`.cena-bioma-img-wrap`): `opacity: 0.58` para o void aparecer atrás.
- Conteúdo (cabeçalho, trilha, nós): `z-index: 2` — permanece legível.
- Overlay interno (`.cena-bioma-img-wrap::after`) mais leve que o padrão global.

### Regenerar assets

- **`fundo-mapa.png`:** tom escuro, narrativa (amnésia, Fragmento, queda ao abismo). Ilustração, 1536×1024.
- **`icone-mapa.png`:** pergaminho + runas + bússola. Ilustração, ~512px no UI (52×52 CSS).

---

## 6. Mestres no card

- Retrato: `public/img/mestres/mestre-{slug}.png` (~1024×1536)
- CSS: `.retrato-mestre:has(img)` — máscara radial, flutuação suave
- **Não** colocar card/borda ao redor; retrato funde no cenário
- Regiões sem mestre usam `.retrato-icone` (emblema circular 80px)

---

## 7. Animação e acessibilidade

- Animar só `transform` e `opacity` (60fps)
- Mapa: pulso na fase atual (`--xp`), hover scale nos nós
- `prefers-reduced-motion`: desligar flutuações e partículas; manter estados estáticos legíveis

Som: ver specs em `openspec/changes/som-e-juice-batalha/` (Web Audio procedural).

---

## 7.1 Splash, home e seleção de personagens

### Splash / Home
- Fundo aprovado: `public/img/ui/splash-cena.png`.
- Botão de entrada: `public/img/ui/botoes/botao-entrar-mundo.png`.
- Logo grande: `public/img/ui/logos/logo-marca-ilustrado.png`; não esticar `logo-header.png` na splash.
- Logout força `?splash=1` para exibir a splash novamente e limpar `sessionStorage.splashVisto`.
- A home usa `body.pagina-home`; evitar duas imagens de fundo visíveis ao mesmo tempo.

### Seleção de personagens
- View: `app/views/auth/criar-personagem.php`.
- CSS: bloco “Seleção de classe” em `public/css/style.css`.
- Painel externo: `public/img/ui/molduras/moldura-selecao-classes.png`.
- Fundo da placa de texto: `public/img/ui/molduras/card-texto-bg.png`.
- Botão final: `public/img/ui/botoes/botao-que-comece-sofrimento.png`.
- Retratos/cartas: `public/img/herois/hud-*.png`.
- Sprites de batalha: `public/img/herois/heroi-*.png`.

### Direção visual das classes
- 6 classes atuais: `mago`, `guerreiro`, `ranger`, `xeno`, `elfo`, `draconato`.
- As cartas devem ter moldura integrada na própria imagem.
- Personagens em pose séria/guerreira, com arma/equipamento visível.
- Ranger: homem negro, sério, sem sorriso.
- Mago: única mulher da seleção atual.
- Xeno: alien claramente não humano, xenoíde insectoide.
- Não remover fundo/moldura automaticamente dos cards; recorte pós-geração destrói brilho e bordas.

---

## 8. O que NÃO quebrar

- Mecânicas de batalha, reputação, Fragmento da IA, 3 finais
- MVC PHP, validação no servidor, CSRF, `e()` em views
- Mestres já aprovados — não regenerar sem pedido explícito
- **UI da página do mapa (§5.1):** `fundo-mapa.png`, `icone-mapa.png`, `bodyClass pagina-mapa`, cards translúcidos
- **Seleção de personagem (§7.1):** cartas com moldura integrada, painel externo ornamentado e botão final em imagem
- Fluxo OpenSpec para mudanças grandes (`docs/processo/FLUXO-OPENSPEC.md`)

---

## 9. Checklist rápido (nova sessão de IA)

- [ ] Mestres/fundos/mapas = **ilustração cyber-fantasia**, não pixel art
- [ ] Cenário de mestre = 1200×800, `center top` no card alto
- [ ] Ícone de fase = preenche círculo inteiro (`.no-bolha-arte`)
- [ ] Cores = tokens `:root` + `--cor-regiao` do card
- [ ] `style.css` não sobrescreve `.no-bolha-arte img`
- [ ] Assets em `public/img/fundos/` e `public/img/mapas/`
- [ ] Registrar slug em `helpers.php` se nova fase/região especial
- [ ] Mapa (§5.1): manter `pagina-mapa`, `icone-mapa.png`, `fundo-mapa.png`, cards translúcidos
- [ ] Diálogo (§5.2): palco usa `fundos/` + sprite com alpha (`inimigos/` ou `atores/`) — **nunca** `mapas/fase-*.png`
- [ ] Splash/home/personagens (§7.1): seguir `docs/processo/STATUS-UI-ATUAL.md`

---

## 10. Referências no código

| O quê | Onde |
|-------|------|
| Tokens, pixel art global | `public/css/style.css` |
| Cards, fundos, nós | `public/css/mapa.css` |
| View do mapa | `app/views/mapa/index.php` |
| Slugs de fundo/ícone | `app/core/helpers.php` |
| UI página mapa (§5.1) | `MapaController.php`, `mapa.css` (`body.pagina-mapa`), `ui/icones/icone-mapa.png`, `fundos/fundo-mapa.png` |
| Status visual atual | `docs/processo/STATUS-UI-ATUAL.md` |
| Seleção de personagem | `app/views/auth/criar-personagem.php`, `public/css/style.css`, `public/img/ui/molduras/moldura-selecao-classes.png`, `public/img/herois/hud-*.png` |
| Geradores pixel art legado | `tools/arte/pixelart.py`, `tools/arte/cenarios.py` |
| Regras de jogo | `docs/canon/REGRAS-DO-JOGO.md` |
