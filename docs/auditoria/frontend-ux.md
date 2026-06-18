# Auditoria Frontend & UX — Algorithmia
**Versão auditada:** branch `refactor/auditoria-qualidade-producao`
**Data:** 2026-06-18
**Auditor:** Claude Sonnet 4.6 (read-only, zero edições)

---

## Índice
1. [🔴 Crítico](#-crítico)
2. [🟠 Alto](#-alto)
3. [🟡 Médio](#-médio)
4. [🟢 Baixo / Oportunidade](#-baixo--oportunidade)
5. [🧩 Proposta de Design System Unificado](#-proposta-de-design-system-unificado)
6. [Tabela-resumo](#tabela-resumo)

---

## 🔴 Crítico

### C-1: Fonts carregadas em DUPLICATA (Google Fonts) — impacto de performance
**Arquivo:** `app/views/layout/header.php:26` + `app/views/auth/login.php:9` + `app/views/home/index.php:10`

**Problema:** A tag `<link>` do Google Fonts para Pixelify Sans + Rubik está em três lugares diferentes: no `header.php` (layout global), em `login.php` e em `home/index.php`. Como login e home têm `<!DOCTYPE html>` próprios (não usam o layout), o carregamento duplo acontece somente para essas telas. Porém `registro.php` **não inclui** a tag de fonts — logo naquela tela as fontes podem falhar em cache frio, causando FOUT visível nos títulos.

**Impacto:** Flash de texto sem estilo no registro (`app/views/auth/registro.php:7-8` tem apenas `style.css`, sem `<link rel="preconnect">` nem font-face). Degradação visual perceptível.

**Recomendação:** Extrair um `<head>` parcial compartilhado para todas as views que têm `<!DOCTYPE html>` próprio (home, login, registro). Alternativamente, mover todos para o layout global e converter as views de auth para usar `header.php`.

**Risco de regressão:** BAIXO — apenas adição de `<link>` em registro.php.

---

### C-2: `prefers-reduced-motion` duplicado no mesmo arquivo CSS
**Arquivo:** `public/css/style.css:1251-1261` e `public/css/style.css:1258-1261`

**Problema:** Há **dois blocos `@media (prefers-reduced-motion: reduce)`** sobrepostos dentro do mesmo arquivo, separados por apenas 4 linhas. O segundo sobrescreve parcialmente o primeiro e ambos tratam seletores diferentes sem comentário claro. Isso é um erro de manutenção: quem editar o primeiro bloco pode não perceber o segundo.

```css
/* linha 1251 — bloco 1 */
@media (prefers-reduced-motion: reduce) {
    .cena-ambiente::before, .cena-ambiente::after { animation: none !important; opacity: .25; }
}

/* linha 1258 — bloco 2 (4 linhas abaixo) */
@media (prefers-reduced-motion: reduce) {
    body::before { animation: none; }
    .toast { transition: opacity .2s; }
}
```

**Impacto:** Não causa bug visual, mas quebra a clareza do sistema de motion-safe e pode resultar em duplicação involuntária futura.

**Recomendação:** Mesclar os dois blocos em um único `@media (prefers-reduced-motion: reduce)` na seção de acessibilidade do CSS.

**Risco de regressão:** BAIXO — merge semântico, sem alterar comportamento.

---

### C-3: Listener global `keydown` no modal nunca removido — vazamento de evento
**Arquivo:** `public/js/ui.js:36-45`

**Problema:** `document.addEventListener('keydown', ...)` é registrado dentro de `montar()`, que roda somente na primeira chamada (tem guard `if (overlay) return`). Isso significa que há exatamente **um listener persistente** no documento para toda a vida da página — tecnicamente correto, sem vazamento real.

**Porém:** em `app/views/layout/header.php:154`, o script inline do modal de ficha do herói também registra `document.addEventListener('keydown', ...)` para fechar com Escape. Esse segundo listener fica ativo **mesmo quando o modal está fechado** e verifica `modal.classList.contains('aberto')` a cada tecla pressionada em qualquer contexto — um custo desnecessário.

**Recomendação:** Converter o handler de Escape da ficha para `{once: false}` com remoção condicional, ou usar o padrão do `modal-overlay` (que checa classe antes de agir).

**Risco de regressão:** BAIXO — refactor pontual, sem mudança de comportamento.

---

### C-4: Injeção de HTML sem sanitização no resultado da batalha
**Arquivo:** `public/js/batalha.js:351-378`

**Problema:** A função `mostrarResultado(r)` constrói HTML via concatenação de strings e usa `escapeHtml()` corretamente para `rec.item_drop.nome` e conquistas. **Porém**, `rec.redirect_final` (linha 372) e `urls.mapa` / `urls.reiniciar` (linhas 374-375) são injetados diretamente em `href="..."` sem escape:

```javascript
html += '<a class="botao" href="' + rec.redirect_final + '">🌌 Ver o Desfecho</a>';
html += '<a class="botao" href="' + urls.mapa + '">🗺️ Voltar ao Mapa</a>';
```

`urls` vem de `window.BATALHA.urls` que é gerado via `json_encode` no PHP (seguro). `rec.redirect_final` vem da resposta da API — se o backend for comprometido ou houver path-traversal, essa URL poderia conter `javascript:` ou dados maliciosos.

**Impacto:** Risco de XSS por URL caso o backend devolva URLs arbitrárias (ex.: ataque interno ou erro de validação no servidor).

**Recomendação:** Usar `escapeHtml(rec.redirect_final)` ou validar que o valor começa com `/` antes de inserir.

**Risco de regressão:** BAIXO — apenas adicionar `escapeHtml()` ou uma guard de `/^\//.test(url)`.

---

## 🟠 Alto

### A-1: Estilos inline massivos no inventário e painel do mestre — inconsistência com design system
**Arquivos:** `app/views/inventario/index.php:22-32`, `app/views/mestre/index.php:5-10`, `app/views/mestre/fases.php:2-4`, `app/views/mestre/desafios.php:2-4`

**Problema:** O inventário usa `<div style="flex:1">`, `<span style="color:var(--xp);font-size:.8rem">`, `<div style="display:flex;gap:.4rem;margin-top:.6rem;flex-wrap:wrap">` todos inline, quebrando o padrão `.item-card` definido em `style.css`. A loja usa `.carta-loja__efeitos` com `<span style="color:var(--xp)">` para efeitos inline. O mestre usa `<div class="grade-itens" style="margin-bottom:1.4rem">` e nos painéis stat: `<div style="font-size:2rem;color:var(--primaria-2)">`.

**Impacto:** O inventário em especial tem ~6 estilos inline por card, tornando manutenção impossível sem busca global. A loja tem menos, mas mistura `.carta-loja__efeitos` (classe) com spans coloridos inline. O painel do mestre usa `grade-itens` para stats — classe pensada para itens, não métricas.

**Recomendação (ver seção Design System):** Criar classes `.item-efeito--atk`, `.item-efeito--def`, `.item-efeito--hp`, `.item-efeito--mp` e `.mestre-stat` (número grande de dashboard). Mover para CSS.

**Risco de regressão:** MÉDIO — requer criação de classes + remoção de inline em múltiplos arquivos.

---

### A-2: Dois padrões de card de item — `.carta-loja` vs `.item-card` — sem unificação
**Arquivos:** `public/css/style.css:977-1079` (ambos os componentes)

**Problema:** O projeto tem **dois sistemas visuais de card de item** que coexistem sem herança:

| Característica       | `.item-card` (inventário)         | `.carta-loja` (loja)            |
|----------------------|-----------------------------------|---------------------------------|
| Layout               | `display:flex; gap:.8rem`         | `flex-direction:column`         |
| Imagem               | `56×56px` num quadrado            | `aspect-ratio: 2/3` full-width  |
| Hover                | `translateY(-3px)` + glow roxo    | `translateY(-4px)` + glow roxo  |
| Raridade no ícone    | aura no `.icone-item`             | badge `position:absolute`       |
| Animação lendário    | `itemLendario` (duplicada!)       | `itemLendario` (mesma keyframe) |

A animação `@keyframes itemLendario` está **literalmente duplicada** no mesmo arquivo (`style.css:1021-1024` e `style.css:1078-1079` referencia a mesma keyframe, definida uma vez, mas os dois seletores que a disparam estão a 55 linhas de distância sem comentário de "shared"). Não é duplicata de definição, mas a estrutura confunde manutenção.

**Impacto:** Nenhum bug visual, mas qualquer alteração no estilo de card precisa ser feita em dois lugares. Novos tipos de item precisarão escolher entre os dois padrões.

**Recomendação (ver seção Design System):** Proposta de `--card-base` como token compartilhado.

**Risco de regressão:** ALTO se refatorado sem cuidado — os dois layouts são intencionalmente diferentes (lista vs. carta colecionável). A unificação deve ser via tokens CSS, não colapsar em um só componente.

---

### A-3: `color-mix()` sem fallback — quebra no Safari 15 e Firefox < 113
**Arquivo:** `public/css/style.css` — 47 ocorrências de `color-mix(in srgb, ...)`

**Problema:** `color-mix()` foi suportado no Chrome 111 (março 2023), Firefox 113 (maio 2023) e Safari 16.2 (dezembro 2022). Sem fallback, usuários em iOS 15/Safari 15 verão **fundo transparente** nos elementos HUD (`.hud-placa`, `.hud-avatar`), nas bordas das cartas de classe e nos cards do mapa. O suporte global é ~93% (caniuse), mas o público educacional pode incluir dispositivos mais antigos.

**Exemplo de código sem fallback:**
```css
.hud-placa {
    border: 1px solid color-mix(in srgb, var(--hud-cor, var(--primaria)) 40%, rgba(255,255,255,.1));
}
```

**Recomendação:** Adicionar fallback com valor fixo antes do `color-mix`:
```css
border: 1px solid var(--borda-luz); /* fallback */
border: 1px solid color-mix(in srgb, var(--hud-cor, var(--primaria)) 40%, rgba(255,255,255,.1));
```

**Risco de regressão:** BAIXO — fallbacks são ignorados por browsers que entendem `color-mix`.

---

### A-4: Tela de resultado da batalha usa `innerHTML` + `style` inline extenso
**Arquivo:** `public/js/batalha.js:350-381`

**Problema:** A função `mostrarResultado()` constrói toda a tela de vitória/derrota com 30 linhas de HTML concatenado incluindo `style` inline:
```javascript
html += '<p style="color:var(--primaria-2);font-weight:800">⬆ Subiu para o nível ' + rec.nivel + '!</p>';
html += '<div style="margin-top:1.2rem;display:flex;gap:.6rem;justify-content:center;flex-wrap:wrap">';
```

**Impacto:** Estilos inline redundantes que deveriam estar em `.tela-resultado` (já existe). Dificulta manutenção e a aplicação de temas. A `.tela-resultado` em `batalha.css:166-173` é subutilizada — as variantes de XP, ouro e nível-up não têm classes.

**Recomendação:** Criar `.resultado-nivel-up`, `.resultado-acoes` e `.resultado-recompensa-xp/ouro` em `batalha.css` e usar o `el.innerHTML` apenas para estrutura, sem `style=""`.

**Risco de regressão:** MÉDIO — requer coordenação JS + CSS.

---

### A-5: Ausência de `role="main"` / `aria-label` no `<main>` + navegação sem `aria-label`
**Arquivo:** `app/views/layout/header.php:163`

**Problema:** O `<main class="conteudo">` não tem `aria-label`, e o `<nav class="navegacao topo-nav">` não tem `aria-label="Navegação principal"`. O modal de ficha do herói tem `role="dialog" aria-modal="true"` correto, mas o `aria-labelledby="fichaNome"` aponta para `id="fichaNome"` que existe **dentro** do dialog (correto), porém o overlay externo `id="fichaModal"` tem `aria-hidden="true"` mudando para `"false"` via JS — isso está correto.

**Problema real:** O `<nav>` de navegação não tem label, portanto leitores de tela anunciarão "navegação" sem contexto. Com múltiplos `<nav>` na página (header + possíveis futuros), isso cria ambiguidade.

**Recomendação:** Adicionar `aria-label="Navegação principal"` ao `<nav class="navegacao topo-nav">`. Adicionar `aria-label="Conteúdo principal"` ao `<main>`.

**Risco de regressão:** ZERO — atributos ARIA são aditivos.

---

### A-6: Imagens sem `alt` descritivo na loja e inventário
**Arquivos:** `app/views/loja/index.php:22` (`svgSlug($it['svg_slug'], 'carta-loja__img')`) e `app/views/inventario/index.php:21` (`svgSlug($it['svg_slug'])`)

**Problema:** A função `svgSlug()` gera elementos `<img>` ou `<svg>` para ícones de itens. Não há `alt` com nome do item — o `alt` é presumivelmente vazio ou derivado do slug. Em `loja/index.php`, a imagem é a arte principal da carta (2:3), não decorativa. Um leitor de tela não saberá qual item está sendo exibido a não ser pelo `<h4 class="carta-loja__nome">` próximo.

**Recomendação:** Verificar se `svgSlug()` aceita parâmetro `alt`. Se não, passar `alt={$it['nome']}` e garantir que a função o use. Para ícones meramente decorativos (inventário ao lado do nome), `alt=""` é correto — confirmar qual caso se aplica.

**Risco de regressão:** BAIXO — mudança no helper PHP.

---

## 🟡 Médio

### M-1: Todos os 4 CSSs carregados em TODAS as páginas — overhead desnecessário
**Arquivo:** `app/views/layout/header.php:27-30`

**Problema:** `header.php` carrega `style.css`, `cena.css`, `mapa.css` e `batalha.css` em absolutamente todas as rotas, incluindo perfil, ranking, loja, inventário e painel do mestre. `batalha.css` tem ~181 linhas; `mapa.css` tem ~556 linhas. Em produção, isso soma ~50KB extra de CSS em páginas onde esses estilos nunca serão usados.

**Recomendação:** Usar uma variável PHP `$cssExtra = []` definida nas views que precisam e incluída no `<head>` do header:
```php
// arena.php: $cssExtra = ['batalha'];
// mapa/index.php: $cssExtra = ['mapa'];
```

**Risco de regressão:** MÉDIO — requer coordenação entre views e header.

---

### M-2: Seletor `.mestre-card .retrato` com `aspect-ratio: 2/3` vs imagens reais
**Arquivo:** `public/css/style.css:552`

**Problema:**
```css
.mestre-card .retrato { width: 100%; aspect-ratio: 2 / 3; height: auto; ... }
```
O `aspect-ratio: 2/3` forçado no container do retrato do mestre na home pode distorcer/cortar imagens que não sejam proporção 2:3. O `object-fit: cover` no filho corrige visualmente, mas se o arquivo SVG tiver viewBox quadrado, haverá espaço vazio ou corte.

**Impacto:** Visual menor em home (`home/index.php:44-52`), onde `.mestre-card .retrato` usa `svg()` — SVGs são escaláveis e se adaptam.

**Recomendação:** Documentar o aspect-ratio esperado para arte de mestres, ou mudar para `aspect-ratio: 3/4` que é mais comum para retratos ilustrados.

**Risco de regressão:** BAIXO.

---

### M-3: Botão "Fugir" com `style="margin-left:auto"` inline na arena
**Arquivo:** `app/views/batalha/arena.php:48`

```php
<button class="botao botao-sm botao-fantasma" id="btnFugir" style="margin-left:auto">🏃 Fugir</button>
```

**Problema:** Estilo de layout inline num elemento de UI que já deveria ter regra em `batalha.css` (`.barra-acoes-secundarias`). O `margin-left:auto` empurra o botão para a direita — comportamento intencional mas que deveria estar em `.acoes-batalha > :last-child` ou `.btn-fugir` em CSS.

**Risco de regressão:** BAIXO.

---

### M-4: Painel do mestre usa `.grade-itens` como grid de estatísticas
**Arquivo:** `app/views/mestre/index.php:5`

```php
<div class="grade-itens" style="margin-bottom:1.4rem">
    <div class="painel" style="text-align:center">...
```

**Problema:** `.grade-itens` é definido em `style.css:977` com `grid-template-columns: repeat(auto-fill, minmax(220px, 1fr))` — pensado para cards de item com imagem. Usado aqui para 5 blocos de stat numérico com `style="text-align:center"` inline. Semanticamente incorreto e frágil: mudar `minmax` de `.grade-itens` para itens menores quebraria o dashboard.

**Recomendação:** Criar `.grade-stats` ou `.dashboard-grid` para este padrão.

**Risco de regressão:** BAIXO.

---

### M-5: Timer `elTexto._timer` armazenado como propriedade DOM — antipadrão
**Arquivo:** `public/js/dialogo.js:47`

```javascript
elTexto._timer = timer;
```

**Problema:** Guardar estado em propriedades DOM (`_timer`) é um antipadrão que pode causar conflito com atributos futuros e dificulta a limpeza de memória. O timer é usado para `clearInterval` no `avancar()`.

**Recomendação:** Usar variável de closure no escopo do módulo:
```javascript
var timerDigitar = null;
// no digitar(): timerDigitar = setInterval(...)
// no avancar(): clearInterval(timerDigitar);
```
A variável `textoCompleto` já é de escopo do módulo — o timer deveria ser também.

**Risco de regressão:** BAIXO — refactor interno sem mudança de comportamento.

---

### M-6: `h2` com `style="font-size:1.15rem"` inline no inventário
**Arquivo:** `app/views/inventario/index.php:16`

```php
<h2 class="titulo-secao" style="font-size:1.15rem;margin-top:1.4rem"><?= $rotulo ?></h2>
```

**Problema:** `.titulo-secao` define `font-size: 1.55rem` em CSS. A sobreposição via `style=""` cria um sub-nível de título sem classe própria. Semanticamente um `<h3>` seria mais correto para subtítulos de seção dentro de uma página já com `<h1>`.

**Recomendação:** Criar `.titulo-secao--sm { font-size: 1.15rem; }` e usar `<h2 class="titulo-secao titulo-secao--sm">` ou trocar para `<h3>` com classe própria.

**Risco de regressão:** BAIXO.

---

### M-7: `image-rendering: pixelated` na `.juice-canvas` — sem necessidade real
**Arquivo:** `public/css/batalha.css:93`

```css
.juice-canvas { ... image-rendering: pixelated; }
```

**Problema:** `image-rendering: pixelated` em `<canvas>` é tecnicamente válido mas raramente tem efeito sobre o canvas 2D (o rendering mode do canvas é definido pelo contexto, não pelo CSS). A propriedade é relevante para `<img>`. Comentário de código sugere intenção correta (pixel art de partículas), mas o efeito CSS aqui é decorativo sem impacto.

**Risco de regressão:** ZERO — remover não muda nada visualmente.

---

### M-8: Formulário de criação de desafio sem `for` associado nos `<label>`
**Arquivo:** `app/views/mestre/desafio-form.php:29-32`

```php
<div class="campo">
    <label>Fase</label>
    <select name="fase_id" required>
```

**Problema:** Os `<label>` de Fase, Tipo, Assunto, Dificuldade e outros campos **não têm `for`** apontando para o `id` do controle. O `<input>` e `<select>` correspondentes também não têm `id`. Isso quebra acessibilidade: clique no label não foca o campo, e leitores de tela não associam label ao controle.

**Impacto:** Afeta CRUD do mestre, usado por professores/administradores. Menor para o jogador final, mas relevante para usuários com deficiência visual que acessam o painel.

**Recomendação:** Adicionar `id="campo-fase"` no select e `for="campo-fase"` no label, para cada campo do form.

**Risco de regressão:** ZERO — apenas adicionar atributos.

---

### M-9: Modal de ficha do herói não restaura o foco ao fechar
**Arquivo:** `app/views/layout/header.php:149-155`

```javascript
function fechar() { modal.classList.remove('aberto'); modal.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; }
```

**Problema:** Ao fechar o modal de ficha do herói, o foco não retorna para o elemento que o abriu (`.hud-placa-click`). O padrão WCAG 2.4.3 (Focus Order) requer que o foco retorne ao elemento disparador.

**Recomendação:**
```javascript
function fechar() {
    modal.classList.remove('aberto');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    abre.focus(); // retorna foco ao disparador
}
```

**Risco de regressão:** ZERO.

---

### M-10: `body.style.overflow = 'hidden'` no modal — afeta scroll em iOS
**Arquivo:** `app/views/layout/header.php:148`

**Problema:** `document.body.style.overflow = 'hidden'` ao abrir o modal da ficha não funciona em iOS Safari para prevenir scroll da página por trás. O padrão correto é adicionar `position: fixed` ao body com `top: -${scrollY}px` e restaurar ao fechar.

**Impacto:** Em iOS, o usuário pode rolar a página enquanto o modal da ficha está aberto, causando jump de posição ao fechar.

**Risco de regressão:** BAIXO — fix específico para iOS.

---

## 🟢 Baixo / Oportunidade

### B-1: Seletor `.no-rotulo` em `style.css:83` — usado?
**Arquivo:** `public/css/style.css:83`

```css
.recurso, .hud-nivel, .nivel-num, .barra-label, .no-rotulo { font-family: var(--hud); }
```

**Problema:** `.hud-nivel` e `.nivel-num` não aparecem em nenhum arquivo de view lido. Podem ser resquícios de uma versão anterior do HUD. `.no-rotulo` é usado em `mapa.css:312` — esse é correto.

**Recomendação:** Verificar com grep se `.hud-nivel` e `.nivel-num` têm uso; se não, remover da regra.

**Risco de regressão:** ZERO se os seletores não estiverem em uso.

---

### B-2: `.barra-fill` com `border-radius: 8px 0 0 8px` — borda direita plana no 100%
**Arquivo:** `public/css/style.css:378`

**Problema:** O `border-radius: 8px 0 0 8px` arredonda apenas o lado esquerdo da barra preenchida. Quando a barra está em 100% (HP máximo), o lado direito fica plano e diferente do container arredondado, causando leve inconsistência visual.

**Recomendação:** Usar `border-radius: 8px` e controlar via `overflow: hidden` no container `.barra` (que já tem `overflow: hidden`).

**Risco de regressão:** BAIXO.

---

### B-3: `.flash` sem `role="alert"` — não anunciado por leitores de tela
**Arquivo:** `app/views/layout/header.php:160`

```php
<div class="flash flash-<?= e($flash['tipo']) ?>"><?= e($flash['mensagem']) ?></div>
```

**Problema:** Mensagens de feedback de formulário (ex.: "Login realizado") não têm `role="alert"` ou `aria-live="polite"`, portanto leitores de tela não as anunciarão automaticamente.

**Recomendação:** Adicionar `role="alert"` para erros e `aria-live="polite"` para sucesso.

**Risco de regressão:** ZERO.

---

### B-4: `.splash-marca` e `.logo-marca-splash` — dois seletores idênticos
**Arquivo:** `public/css/style.css:1343-1349`

```css
.splash-marca,
.logo-marca-splash {
    width: min(90vw, 760px); ...
```

**Problema:** Os dois seletores estão combinados numa mesma regra. Verificar se ambas as classes são realmente usadas ou se uma é legado do período pré-imagem ilustrada.

**Risco de regressão:** ZERO se apenas uma for usada.

---

### B-5: `cenaSeta` animation não definida — classe `.seta-rolar` quebrada
**Arquivo:** `public/css/style.css:632` e home/index.php

**Problema:**
```css
.home-entrada .seta-rolar {
    ...
    animation: cenaSeta 1.8s ease-in-out infinite;
}
```
A keyframe `@keyframes cenaSeta` **não está definida** em nenhum arquivo CSS lido (style.css, mapa.css, batalha.css, cena.css). Se existir em `cena.css` não lido completamente, está no lugar errado. Se não existir, a seta de rolar na home não anima.

**Recomendação:** Verificar `cena.css` para a definição; se ausente, adicionar:
```css
@keyframes cenaSeta { 0%, 100% { transform: translateX(-50%) translateY(0); } 50% { transform: translateX(-50%) translateY(6px); } }
```

**Risco de regressão:** BAIXO — elemento puramente decorativo.

---

### B-6: UX — Sem indicação visual de loading durante fetch na batalha
**Arquivo:** `public/js/batalha.js:196-197`

```javascript
req(urls.responder, { resposta: resposta }).then(function (r) { tratarTurno(r, elemento); });
```

**Problema:** Após o jogador responder, há um intervalo entre o clique e o `tratarTurno()` sem nenhum indicador de carregamento. O `bloquearZona()` desabilita os botões mas não mostra spinner ou estado visual de "aguardando". Em conexões lentas, o jogador não tem feedback de que a requisição está em andamento.

**Recomendação:** Adicionar classe `.carregando` ao `#painelDesafio` durante o fetch com CSS:
```css
.painel-desafio.carregando { opacity: .6; }
.painel-desafio.carregando::after { content: "…"; animation: ...; }
```

**Risco de regressão:** BAIXO.

---

### B-7: `font-family: var(--pixel)` referencia `JetBrains Mono` — não carregada
**Arquivo:** `public/css/style.css:31`

```css
--pixel: 'JetBrains Mono', 'Courier New', monospace;
```

**Problema:** `JetBrains Mono` não é carregada pelo Google Fonts (o `@import` no topo carrega apenas Pixelify Sans e Rubik). O fallback `Courier New` será usado em todos os browsers. Não é um bug visível, mas é documentação de intenção incorreta.

**Recomendação:** Adicionar JetBrains Mono ao `@import` do Google Fonts, ou mudar o token para apenas `'Courier New', monospace`.

**Risco de regressão:** ZERO.

---

### B-8: `.topo-moldurado::after` usa `border-image` mas o PNG pode não existir
**Arquivo:** `app/views/layout/header.php:33-34`

```php
$temMolduraBarra = is_file(...'moldura-barra-fina.png');
<header class="topo topo-jogo<?= $temMolduraBarra ? ' topo-moldurado' : '' ?>">
```

**Problema:** Corretamente guardado com `is_file()`. Porém a class do CSS usa `moldura-barra-fina.webp` no `border-image` (`style.css:119`), enquanto o PHP verifica `.png`. Se o PNG existir mas não o WebP, a classe é adicionada mas a moldura não renderiza.

**Recomendação:** Verificar consistência: o `is_file()` deve checar `.webp` (o que o CSS referencia) ou fornecer um `srcImagem()` que retorne o formato disponível.

**Risco de regressão:** BAIXO — pode ser que ambos existam atualmente.

---

## 🧩 Proposta de Design System Unificado

### Tokens CSS faltantes (adicionar em `:root`)

```css
:root {
    /* Tokens de card */
    --card-radius: 14px;          /* .item-card usa 14px, .carta-loja usa 16px → unificar */
    --card-hover-lift: -3px;      /* translateY para hover de cards */
    --card-hover-glow: rgba(124, 92, 255, .2);

    /* Tokens de botão */
    --btn-radius: 11px;           /* já existe implicitamente */
    --btn-sm-radius: 8px;

    /* Tokens de grade */
    --grade-item-min: 220px;      /* minmax para grade de itens */
    --grade-carta-min: 200px;     /* minmax para grade de cartas (loja) */
}
```

### Classes componente a criar (SEGURAS — apenas adições, sem remover)

```css
/* Dashboard do Mestre */
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

/* Item — efeitos no inventário e loja */
.item-efeito {
    font-size: .8rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: .25rem;
}
.item-efeito--atk { color: var(--xp); }
.item-efeito--def { color: var(--mp); }
.item-efeito--hp  { color: var(--hp); }
.item-efeito--mp  { color: var(--mp); }

/* Ações de item (inventário) */
.item-acoes {
    display: flex;
    gap: .4rem;
    margin-top: .6rem;
    flex-wrap: wrap;
}

/* Sub-título de seção (inventário por tipo) */
.titulo-secao--sm {
    font-size: 1.15rem;
    margin-top: 1.4rem;
}

/* Resultado da batalha — suplemento */
.resultado-nivel-up {
    color: var(--primaria-2);
    font-weight: 800;
}
.resultado-acoes {
    margin-top: 1.2rem;
    display: flex;
    gap: .6rem;
    justify-content: center;
    flex-wrap: wrap;
}
```

### Padrão de botões — já consistente, apenas documentar

O `.botao` / `.botao-sm` / `.botao-fantasma` / `.botao-perigo` / `.botao-ouro` são bem definidos e usados de forma consistente. O único desvio é `.btn-ia` definido em `batalha.css:163` sem equivalente no design system global — mover para `style.css` como `.botao-arcano`.

---

## Tabela-resumo

| ID  | Severidade | Arquivo principal                              | Tipo        | Seguro? |
|-----|------------|------------------------------------------------|-------------|---------|
| C-1 | 🔴 Crítico | `auth/registro.php`                            | Bug visual  | ✅ Sim  |
| C-2 | 🔴 Crítico | `style.css:1251-1261`                          | CSS morto   | ✅ Sim  |
| C-3 | 🔴 Crítico | `header.php:154` / `ui.js:36`                  | JS listener | ✅ Sim  |
| C-4 | 🔴 Crítico | `batalha.js:372-375`                           | Segurança   | ✅ Sim  |
| A-1 | 🟠 Alto    | `inventario/index.php` + `mestre/*.php`        | Inline CSS  | ✅ Sim  |
| A-2 | 🟠 Alto    | `style.css:977-1079`                           | Design sys  | ⚠️ Médio|
| A-3 | 🟠 Alto    | `style.css` (47 ocorrências `color-mix`)       | Compat.     | ✅ Sim  |
| A-4 | 🟠 Alto    | `batalha.js:350-381`                           | Inline+JS   | ⚠️ Médio|
| A-5 | 🟠 Alto    | `header.php:74, 163`                           | ARIA        | ✅ Sim  |
| A-6 | 🟠 Alto    | `loja/index.php:22`, `inventario/index.php:21` | Alt text    | ✅ Sim  |
| M-1 | 🟡 Médio   | `header.php:27-30`                             | Performance | ⚠️ Médio|
| M-2 | 🟡 Médio   | `style.css:552`                                | Layout      | ✅ Sim  |
| M-3 | 🟡 Médio   | `batalha/arena.php:48`                         | Inline CSS  | ✅ Sim  |
| M-4 | 🟡 Médio   | `mestre/index.php:5`                           | Semântica   | ✅ Sim  |
| M-5 | 🟡 Médio   | `dialogo.js:47`                                | JS padrão   | ✅ Sim  |
| M-6 | 🟡 Médio   | `inventario/index.php:16`                      | Semântica   | ✅ Sim  |
| M-7 | 🟡 Médio   | `batalha.css:93`                               | CSS morto   | ✅ Sim  |
| M-8 | 🟡 Médio   | `mestre/desafio-form.php:29-96`                | ARIA/a11y   | ✅ Sim  |
| M-9 | 🟡 Médio   | `header.php:149-155`                           | Foco/WCAG   | ✅ Sim  |
| M-10| 🟡 Médio   | `header.php:148`                               | iOS scroll  | ✅ Sim  |
| B-1 | 🟢 Baixo   | `style.css:83`                                 | CSS morto   | ✅ Sim  |
| B-2 | 🟢 Baixo   | `style.css:378`                                | Visual      | ✅ Sim  |
| B-3 | 🟢 Baixo   | `header.php:160`                               | ARIA        | ✅ Sim  |
| B-4 | 🟢 Baixo   | `style.css:1343`                               | CSS legado  | ✅ Sim  |
| B-5 | 🟢 Baixo   | `style.css:632`                                | Anim. falta | ✅ Sim  |
| B-6 | 🟢 Baixo   | `batalha.js:196`                               | UX          | ✅ Sim  |
| B-7 | 🟢 Baixo   | `style.css:31`                                 | Font token  | ✅ Sim  |
| B-8 | 🟢 Baixo   | `header.php:33` + `style.css:119`              | PNG/WebP    | ✅ Sim  |

**Total:** 4 críticos · 6 altos · 7 médios · 8 baixos = **25 achados**

---

*Auditoria read-only — nenhum arquivo foi alterado.*
