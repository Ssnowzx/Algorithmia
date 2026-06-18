# Design System — Algorithmia
**Criado:** 2026-06-18
**Branch:** `refactor/auditoria-qualidade-producao`
**Escopo:** tokens CSS, componentes carta e botao, padrão de raridade, guia de migração

---

## Índice

1. [Design Tokens](#1-design-tokens)
2. [Componente Carta](#2-componente-carta)
3. [Componente Botao](#3-componente-botao)
4. [Padrão de Raridade](#4-padrão-de-raridade)
5. [Guia de Migração](#5-guia-de-migração)
6. [Checklist de Adoção](#6-checklist-de-adoção)

---

## 1. Design Tokens

### Arquivo

`public/css/tokens.css` — apenas `:root`, zero regras de seletor. Seguro para importar em qualquer ordem; complementa sem sobrescrever `style.css`.

### Como incluir

```html
<!-- Carregar antes (ou depois) de style.css — ambas as ordens funcionam -->
<link rel="stylesheet" href="/css/tokens.css">
<link rel="stylesheet" href="/css/style.css">
```

### Grupos de tokens

| Grupo | Prefixo | Exemplos de tokens |
|---|---|---|
| Cores base | `--cor-*` | `--cor-bg`, `--cor-primaria`, `--cor-ouro` |
| Sombras | `--sombra-*` | `--sombra-card`, `--sombra-carta` |
| Tipografia | `--fonte`, `--titulo`, `--hud`, `--texto-*` | `--texto-sm`, `--texto-2xl` |
| Espaçamentos | `--esp-*` | `--esp-4` (1rem / 16px) |
| Raios de borda | `--raio-*` | `--raio-card` (14px), `--raio-carta` (16px) |
| Card horizontal | `--card-*` | `--card-radius`, `--card-hover-y`, `--card-hover-glow` |
| Carta vertical | `--carta-*` | `--carta-radius`, `--carta-hover-y`, `--carta-arte-ratio` |
| Botao | `--btn-*` | `--btn-radius`, `--btn-pad-y`, `--btn-sombra-primario` |
| Raridade | `--rar-<nivel>-*` | `--rar-epico-borda`, `--rar-lendario-glow` |
| Regioes | `--regiao-*` | `--regiao-floresta`, `--regiao-abismo` |
| Z-index | `--z-*` | `--z-topo`, `--z-modal`, `--z-toast` |
| Transicoes | `--transicao-*` | `--transicao-padrao` (.18s ease) |

### Tokens de raridade (detalhe)

```css
/* Comuns — tons neutros-frios */
--rar-comum-bg:     rgba(154, 160, 201, .2);
--rar-comum-texto:  #c3c8ea;
--rar-comum-borda:  rgba(154, 160, 201, .3);

/* Raro — azul vibrante */
--rar-raro-bg:     rgba(74, 163, 255, .2);
--rar-raro-texto:  #8cc6ff;
--rar-raro-borda:  rgba(74, 163, 255, .4);
--rar-raro-glow:   rgba(74, 163, 255, .18);

/* Epico — roxo arcano */
--rar-epico-bg:    rgba(157, 131, 255, .22);
--rar-epico-texto: #c4b0ff;
--rar-epico-borda: rgba(157, 131, 255, .5);
--rar-epico-glow:  rgba(157, 131, 255, .25);

/* Lendario — ouro brilhante */
--rar-lendario-bg:    rgba(255, 206, 71, .2);
--rar-lendario-texto: var(--cor-xp);
--rar-lendario-borda: rgba(255, 206, 71, .55);
--rar-lendario-glow:  rgba(255, 206, 71, .38);
```

---

## 2. Componente Carta

### Problema atual

O projeto tem dois componentes visuais de item que divergem em layout mas compartilham lógica de raridade e hover:

| Característica | `.item-card` (inventario) | `.carta-loja` (loja) |
|---|---|---|
| Layout | `flex` horizontal, icone 56x56 | `flex-column`, arte 2:3 |
| Hover | `translateY(-3px)` | `translateY(-4px)` |
| Raridade | aura no `.icone-item` | badge `position:absolute` |
| Radius | 14px | 16px |

Os dois sao intencionalmente diferentes (lista de inventario vs. carta colecionavel). A unificacao proposta preserva ambos os layouts via modificador, compartilhando apenas os tokens de interacao.

### Componente unificado proposto: `.carta`

O componente base `.carta` + modificadores `--vertical` / `--horizontal` substitui `.carta-loja` e `.item-card` respectivamente. As classes antigas PERMANECEM no CSS (nao remover) — a migracao e incremental.

```css
/* ---------- Carta — componente base (NOVO, apenas adicionar) ---------- */

.carta {
    background: var(--cor-painel);
    border: 1px solid var(--cor-borda);
    border-radius: var(--carta-radius);      /* 16px — unificado */
    position: relative;
    transition:
        transform var(--transicao-padrao),
        border-color var(--transicao-padrao),
        box-shadow var(--transicao-padrao);
}

/* Hover: igual para ambas as variantes — token compartilhado */
.carta:hover {
    border-color: color-mix(in srgb, var(--cor-primaria) 50%, var(--cor-borda));
    box-shadow: var(--sombra-carta);
}

/* ---- Modificador VERTICAL (substitui .carta-loja) ---- */
.carta--vertical {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.carta--vertical:hover {
    transform: translateY(var(--carta-hover-y));   /* -4px */
}

/* Area de arte 2:3 */
.carta__arte {
    position: relative;
    aspect-ratio: var(--carta-arte-ratio);         /* 2/3 */
    background: var(--cor-bg-2);
    display: grid;
    place-items: center;
    overflow: hidden;
    border-bottom: 1px solid var(--cor-borda);
}
.carta__img {
    width: 100%; height: 100%;
    object-fit: contain;
    image-rendering: auto;
    display: block;
    transition: transform var(--transicao-suave);
}
.carta--vertical:hover .carta__img { transform: scale(1.04); }

.carta__corpo {
    padding: var(--esp-3) var(--esp-4);            /* 12px 16px */
    display: flex;
    flex-direction: column;
    gap: var(--esp-2);                             /* 8px */
    flex: 1;
}
.carta__nome {
    margin: 0;
    font-size: var(--texto-md);                    /* .98rem */
    line-height: 1.2;
}
.carta__descricao {
    margin: 0;
    font-size: var(--texto-sm);                    /* .82rem */
    color: var(--cor-texto-fraco);
}
.carta__rodape {
    margin-top: auto;
    padding-top: var(--esp-2);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--esp-2);
    flex-wrap: wrap;
    border-top: 1px solid var(--cor-borda);
}
.carta__preco { color: var(--cor-ouro); font-size: 1.05rem; font-weight: 700; }
.carta__acoes { display: flex; gap: var(--esp-2); flex-wrap: wrap; }

/* Badge de raridade (posicao absoluta no canto da arte) */
.carta__badge-raridade {
    position: absolute;
    top: var(--esp-2);
    right: var(--esp-2);
    z-index: 2;
    backdrop-filter: blur(2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, .5);
}

/* ---- Modificador HORIZONTAL (substitui .item-card) ---- */
.carta--horizontal {
    display: flex;
    gap: var(--esp-4);                             /* 16px */
    align-items: flex-start;
    padding: var(--esp-4);
    border-radius: var(--card-radius);             /* 14px — fiel ao original */
}
.carta--horizontal:hover {
    transform: translateY(var(--card-hover-y));    /* -3px */
    background: var(--cor-bg-2);
    box-shadow: var(--sombra-card);
}

/* Icone quadrado do item */
.carta__icone {
    width: 56px; height: 56px;
    flex-shrink: 0;
    background: var(--cor-bg-2);
    border-radius: var(--raio-sm);                 /* 8px */
    border: 1px solid rgba(255, 255, 255, .07);
    display: grid;
    place-items: center;
    position: relative;
    transition: box-shadow var(--transicao-suave);
}
.carta__icone svg,
.carta__icone img { width: 50px; height: 50px; }
.carta--horizontal:hover .carta__icone { box-shadow: 0 0 16px rgba(124, 221, 255, .3); }

/* Conteudo textual ao lado do icone */
.carta__conteudo { flex: 1; }
.carta__conteudo h4 { margin: 0 0 .2rem; font-size: var(--texto-md); }
```

### Estados de raridade no componente `.carta`

```css
/* ---------- Raridade — estilo sobre .carta (NOVO) ---------- */

/* Raro: borda azul */
.carta.rar-raro { border-color: var(--rar-raro-borda); }

/* Epico: borda + brilho violeta */
.carta.rar-epico {
    border-color: var(--rar-epico-borda);
    box-shadow: inset 0 0 0 1px var(--rar-epico-glow), 0 0 16px var(--rar-epico-glow);
}

/* Lendario: borda dourada + animacao pulsante */
.carta.rar-lendario {
    border-color: var(--rar-lendario-borda);
    animation: cartaLendaria 2.2s ease-in-out infinite;
}

@keyframes cartaLendaria {
    0%, 100% {
        box-shadow:
            inset 0 0 0 1px rgba(255, 206, 71, .45),
            0 0 12px rgba(255, 206, 71, .22);
    }
    50% {
        box-shadow:
            inset 0 0 0 1px rgba(255, 206, 71, .7),
            0 0 22px rgba(255, 206, 71, .5);
    }
}

/* No icone do card horizontal (efeito aura) */
.carta--horizontal.rar-epico .carta__icone {
    border-color: var(--rar-epico-borda);
    box-shadow: inset 0 0 0 1px var(--rar-epico-glow), 0 0 14px var(--rar-epico-glow);
}
.carta--horizontal.rar-lendario .carta__icone {
    border-color: var(--rar-lendario-borda);
    animation: cartaLendaria 2.2s ease-in-out infinite;
}
```

### HTML de referencia — variante vertical (loja)

```html
<!-- Antes: <div class="carta-loja item-rar-epico"> -->
<div class="carta carta--vertical rar-epico">
    <div class="carta__arte">
        <img src="..." class="carta__img" alt="Nome do Item">
        <span class="carta__badge-raridade raridade raridade-epico">epico</span>
    </div>
    <div class="carta__corpo">
        <h4 class="carta__nome">Espada Lendaria</h4>
        <p class="carta__descricao">Lembra vagamente de um tutorial.</p>
        <div class="carta__efeitos">
            <span class="item-efeito item-efeito--atk">+12</span>
        </div>
        <div class="carta__rodape">
            <strong class="carta__preco">250</strong>
            <div class="carta__acoes">
                <button class="botao botao-sm">Comprar</button>
            </div>
        </div>
    </div>
</div>
```

### HTML de referencia — variante horizontal (inventario)

```html
<!-- Antes: <div class="item-card item-rar-epico"> -->
<div class="carta carta--horizontal rar-epico">
    <div class="carta__icone">
        <img src="..." alt="">  <!-- decorativo: alt vazio -->
    </div>
    <div class="carta__conteudo">
        <h4>Escudo de Bits <span class="tag-equipado">● Equipado</span></h4>
        <span class="raridade raridade-epico">epico</span>
        <p class="carta__descricao">Absorve XSS e injecao SQL com igual desdém.</p>
        <div class="item-efeitos">
            <span class="item-efeito item-efeito--def">+8</span>
        </div>
        <div class="item-acoes">
            <button class="botao botao-sm botao-fantasma">Desequipar</button>
        </div>
    </div>
</div>
```

---

## 3. Componente Botao

### Estado atual (ja consistente)

O sistema de botoes em `style.css` e coerente. Documentado aqui para referencia:

| Classe | Uso | Gradiente / cor |
|---|---|---|
| `.botao` | Acao primaria | roxo `--cor-primaria-2` → `--cor-primaria` |
| `.botao-fantasma` | Acao secundaria / desequipar | fundo transparente, borda `--cor-borda` |
| `.botao-perigo` | Confirmacoes destrutivas | vermelho `--cor-hp` |
| `.botao-ouro` | Usar pocao (consome recurso) | ouro `--cor-ouro` |
| `.botao-sm` | Modificador de tamanho | `--btn-pad-sm-y` / `--btn-pad-sm-x` |

### Estados obrigatorios

```css
/* Hover — ja implementado em style.css */
.botao:hover {
    transform: translateY(-2px);
    filter: brightness(1.08);
}

/* Disabled */
.botao:disabled {
    opacity: .5;
    cursor: not-allowed;
    transform: none;
}

/* Focus-visible (acessibilidade — ADICIONAR ao CSS) */
.botao:focus-visible {
    outline: 2px solid var(--cor-primaria-2);
    outline-offset: 2px;
}
```

### Variante a criar: `.botao-arcano`

Equivalente ao `.btn-ia` de `batalha.css`, mas no design system global:

```css
/* Adicionar em style.css, secao de botoes */
.botao-arcano {
    background: linear-gradient(180deg, var(--cor-ciano), #4aaeff);
    color: #050a1a;
    box-shadow: 0 6px 18px rgba(140, 230, 255, .35);
}
```

### HTML de referencia

```html
<button class="botao">Acao Primaria</button>
<button class="botao botao-sm">Pequeno</button>
<button class="botao botao-fantasma">Secundario</button>
<button class="botao botao-perigo">Descartar</button>
<button class="botao botao-ouro">Usar Pocao</button>
<button class="botao botao-arcano">Habilidade Magica</button>
<button class="botao" disabled>Indisponivel</button>
```

---

## 4. Padrao de Raridade

### Badge `.raridade`

Classe base + modificador. Ja implementado em `style.css:1004-1008`.

```html
<span class="raridade raridade-comum">comum</span>
<span class="raridade raridade-raro">raro</span>
<span class="raridade raridade-epico">epico</span>
<span class="raridade raridade-lendario">lendario</span>
```

### Modificadores de carta por raridade

Os tokens `--rar-*` permitem aplicar aura/brilho por raridade em qualquer componente:

```css
/* Exemplo: reutilizar token de glow em qualquer elemento */
.meu-componente.rar-epico {
    border-color: var(--rar-epico-borda);
    box-shadow: 0 0 16px var(--rar-epico-glow);
}
```

### Hierarquia visual

| Raridade | Cor principal | Efeito extra |
|---|---|---|
| Comum | `#c3c8ea` (cinza-lavanda) | Nenhum |
| Raro | `#8cc6ff` (azul) | Borda azul sutil |
| Epico | `#c4b0ff` (lilas) | Borda violeta + brilho estatico |
| Lendario | `#ffd23f` (ouro) | Borda dourada + pulso animado |

---

## 5. Guia de Migracao

### Principio fundamental

**Nao remover classes antigas.** A migracao e incremental: novas views usam `.carta`, views existentes permanecem com `.carta-loja` / `.item-card` ate serem tocadas por outra razao.

### Passo 0: Ativar tokens.css (imediato, risco zero)

Adicionar ao `header.php`, antes de `style.css`:

```php
<!-- header.php, linha 27 (antes dos outros CSS) -->
<link rel="stylesheet" href="<?= url('css/tokens.css') ?>">
```

Nenhuma view precisa mudar. Os tokens ficam disponiveis globalmente.

### Passo 1: Adicionar CSS do componente `.carta` (aditivo)

Adicionar ao final de `style.css` (ou em novo arquivo `carta.css`):

```css
/* ---------- COMPONENTE .carta — ver docs/desenvolvimento/design-system.md ---------- */
/* [colar aqui o bloco .carta / .carta--vertical / .carta--horizontal da secao 2] */
```

Nenhuma view quebra. As classes antigas coexistem.

### Passo 2: Migrar a Loja (`loja/index.php`)

Troca conservadora: substituir `.carta-loja` por `.carta.carta--vertical`, adicionar `.rar-*` no lugar de `.item-rar-*`.

```php
<!-- Antes -->
<div class="carta-loja item-rar-<?= e($it['raridade']) ?>">
    <div class="carta-loja__arte">
        <?= svgSlug($it['svg_slug'], 'carta-loja__img') ?>
        <span class="carta-loja__raridade raridade raridade-<?= e($it['raridade']) ?>">
            <?= e($it['raridade']) ?>
        </span>
    </div>
    <div class="carta-loja__corpo">
        <h4 class="carta-loja__nome"><?= e($it['nome']) ?></h4>
        <p class="desc-item"><?= e($it['descricao']) ?></p>
        <!-- ... -->
    </div>
</div>

<!-- Depois -->
<div class="carta carta--vertical rar-<?= e($it['raridade']) ?>">
    <div class="carta__arte">
        <?= svgSlug($it['svg_slug'], 'carta__img', $it['nome']) ?>
        <span class="carta__badge-raridade raridade raridade-<?= e($it['raridade']) ?>">
            <?= e($it['raridade']) ?>
        </span>
    </div>
    <div class="carta__corpo">
        <h4 class="carta__nome"><?= e($it['nome']) ?></h4>
        <p class="carta__descricao"><?= e($it['descricao']) ?></p>
        <!-- ... -->
    </div>
</div>
```

### Passo 3: Migrar o Inventario (`inventario/index.php`)

Remover estilos inline, usar classes do design system:

```php
<!-- Antes -->
<div class="item-card item-rar-<?= e($it['raridade']) ?>">
    <div class="icone-item"><?= svgSlug($it['svg_slug']) ?></div>
    <div style="flex:1">
        <h4><?= e($it['nome']) ?></h4>
        <span class="raridade raridade-<?= e($it['raridade']) ?>">...</span>
        <p class="desc-item"><?= e($it['descricao']) ?></p>
        <?php if (!empty($efeito['ataque'])): ?>
            <span style="color:var(--xp);font-size:.8rem">??? +<?= (int)$efeito['ataque'] ?></span>
        <?php endif; ?>
        <div style="display:flex;gap:.4rem;margin-top:.6rem;flex-wrap:wrap">
            <button class="botao botao-sm">Equipar</button>
        </div>
    </div>
</div>

<!-- Depois -->
<div class="carta carta--horizontal rar-<?= e($it['raridade']) ?>">
    <div class="carta__icone"><?= svgSlug($it['svg_slug']) ?></div>
    <div class="carta__conteudo">
        <h4><?= e($it['nome']) ?></h4>
        <span class="raridade raridade-<?= e($it['raridade']) ?>">...</span>
        <p class="carta__descricao"><?= e($it['descricao']) ?></p>
        <?php if (!empty($efeito['ataque'])): ?>
            <span class="item-efeito item-efeito--atk">??? +<?= (int)$efeito['ataque'] ?></span>
        <?php endif; ?>
        <div class="item-acoes">
            <button class="botao botao-sm">Equipar</button>
        </div>
    </div>
</div>
```

### Classes auxiliares a adicionar em `style.css` (junto com `.carta`)

```css
/* Efeitos de item — substitui spans com style inline */
.item-efeito {
    font-size: .8rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: .25rem;
}
.item-efeito--atk { color: var(--cor-xp); }
.item-efeito--def { color: var(--cor-mp); }
.item-efeito--hp  { color: var(--cor-hp); }
.item-efeito--mp  { color: var(--cor-mp); }

/* Acoes de item — substitui div com style inline */
.item-acoes {
    display: flex;
    gap: .4rem;
    margin-top: .6rem;
    flex-wrap: wrap;
}

/* Sub-secao de inventario — substitui h2 com style inline */
.titulo-secao--sm {
    font-size: 1.15rem;
    margin-top: 1.4rem;
}

/* Dashboard do Mestre — substitui .grade-itens para stats */
.grade-stats {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 1rem;
}
.stat-numero {
    font-size: 2rem;
    font-family: var(--hud);
    line-height: 1;
}
```

### Passo 4: Atualizar `svgSlug()` para aceitar `alt`

A funcao em `app/helpers/view-helpers.php` precisa de parametro `$alt = ''` para corrigir A-6 da auditoria:

```php
// Antes (hipotetico)
function svgSlug(string $slug, string $class = ''): string { ... }

// Depois
function svgSlug(string $slug, string $class = '', string $alt = ''): string {
    // ... se gerar <img>, usar $alt
}
```

---

## 6. Checklist de Adocao

```
FASE 0 — Tokens (risco zero, feita imediatamente)
[x] tokens.css criado em public/css/
[ ] Importar tokens.css no header.php antes de style.css

FASE 1 — CSS aditivo (sem tocar views)
[ ] Adicionar .carta / .carta--vertical / .carta--horizontal ao final de style.css
[ ] Adicionar .item-efeito / .item-efeito--* ao final de style.css
[ ] Adicionar .item-acoes ao final de style.css
[ ] Adicionar .titulo-secao--sm ao final de style.css
[ ] Adicionar .grade-stats / .stat-numero ao final de style.css
[ ] Adicionar .botao-arcano ao final de style.css
[ ] Adicionar :focus-visible a todos os .botao em style.css

FASE 2 — Migracao da Loja (view isolada, baixo risco)
[ ] Substituir .carta-loja por .carta.carta--vertical em loja/index.php
[ ] Substituir item-rar-X por rar-X em loja/index.php
[ ] Passar alt={$it['nome']} para svgSlug() na loja (corrige A-6)
[ ] Testar: exibicao, hover, raridade, botoes comprar/vender
[ ] Manter .carta-loja em style.css (nao remover ainda)

FASE 3 — Migracao do Inventario (mais spans inline para limpar)
[ ] Substituir .item-card por .carta.carta--horizontal em inventario/index.php
[ ] Substituir estilos inline por classes .item-efeito--* e .item-acoes
[ ] Substituir h2 com style inline por .titulo-secao--sm
[ ] Testar: grupos de tipo, equipar/desequipar, usar pocao, descartar
[ ] Manter .item-card em style.css (nao remover ainda)

FASE 4 — Cleanup (apenas apos FASE 2 e 3 em producao sem regressao)
[ ] Remover .carta-loja e regras derivadas de style.css
[ ] Remover .item-card e regras derivadas de style.css
[ ] Consolidar @keyframes itemLendario em cartaLendaria (ja unificada no .carta)
[ ] Mesclar os dois @media (prefers-reduced-motion) duplicados (C-2 da auditoria)
```

---

*Design system incremental — nao quebra o que ja funciona.*
