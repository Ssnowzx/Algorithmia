# Pesquisa — Passada 1: Navegação/IA + Motion/View Transitions

> **Parte da auditoria de produto do Algorithmia.** Base de evidências para decisões de
> arquitetura e produto. Método: harness de _deep research_ (fan-out de buscas → fetch de
> 25 fontes → extração de 119 afirmações → **verificação adversarial 3-votos** → síntese).
> **23/25 afirmações confirmadas**, 2 refutadas (registradas abaixo por transparência).
> Cada afirmação é rotulada: **[Fato validado]** · **[Consenso de mercado]** · **[Boa prática]** ·
> **[Hipótese]** · **[Opinião]**. Fontes primárias priorizadas (W3C/WCAG, MDN, web.dev,
> developer.chrome.com, Apple HIG, m3.material.io, developer.android.com, WebKit).
>
> ⚠️ **Validade temporal:** fatos de suporte de navegador (Domínio B) verificados em jun/2026 —
> reconferir em caniuse.com antes do lançamento (Firefox cross-document/types eram os atrasados).

---

## Domínio A — Navegação & Arquitetura da Informação

### A1. Bottom nav no mobile → navigation rail no desktop (continuidade; nunca os dois juntos)
**[Fato validado]** (verificação 3-0, fundido de 4 afirmações unânimes)

Material 3: janelas _compact_ (<600dp) **"should always use a navigation bar"** (barra inferior);
para janelas _expanded_/_extra-large_, **"Don't use navigation bars for desktop layouts. Instead,
use a navigation rail or tabs."** M3 também manda: **"Never use the navigation rail and navigation
bar simultaneously"**, prescrevendo a troca barra↔rail por breakpoint. A tabela oficial de
navegação responsiva do Android mapeia: _compact_ → bottom nav (poucos itens) / drawer (muitos);
_medium_ → rail; _expanded_ → rail ou drawer persistente. (Atual em M3 Expressive, mai/2025.)

> **Decisão p/ Algorithmia:** barra inferior persistente no mobile e os **mesmos** destinos
> migrando para um **rail à esquerda** no desktop; **nunca** mostrar os dois ao mesmo tempo.

Fontes: [m3 navigation-rail](https://m3.material.io/components/navigation-rail/guidelines) ·
[m3 navigation-bar](https://m3.material.io/components/navigation-bar/guidelines) ·
[Android responsive nav](https://developer.android.com/develop/ui/views/layout/build-responsive-navigation) ·
[Android adaptive nav](https://developer.android.com/develop/ui/compose/layouts/adaptive/build-adaptive-navigation)

### A2. Limite de 3–5 destinos na barra inferior
**[Fato validado]** (3-0, fundido de 3 afirmações)

M3: _"Navigation bars provide access to three to five destinations"_; **"For products with more
than five navigation items, don't use a navigation bar; the elements may collide and there likely
won't be enough space for translated text."** Apple HIG: 3–5 abas no iOS, _"Use the minimum number
of tabs required… Each additional tab increases the complexity."_

> **Decisão p/ Algorithmia:** limitar a barra a **5 destinos de topo** (os 5 setores atuais:
> Mapa, Inventário, Loja, Perfil, Ranking encaixam perfeitamente). Empurrar o resto **um nível
> abaixo** — **não** criar aba "Mais".

Fontes: [m3 navigation-bar](https://m3.material.io/components/navigation-bar/guidelines) ·
[Apple HIG Tab Bars](https://developer.apple.com/design/human-interface-guidelines/tab-bars) ·
[Flutter NavigationBar](https://api.flutter.dev/flutter/material/NavigationBar-class.html)

### A3. Tab bar é navegação, não ação
**[Fato validado]** (3-0)

Apple HIG: **"Use a tab bar to support navigation, not to provide actions… If you need to provide
controls that act on elements in the current view, use a toolbar instead."**

> **Decisão p/ Algorithmia:** só **seções persistentes** entram na barra. Verbos ("atacar",
> "comprar", "responder") ficam em toolbar/ação de tela, nunca na nav.

Fontes: [Apple HIG Tab Bars](https://developer.apple.com/design/human-interface-guidelines/tab-bars) ·
[Apple HIG Toolbars](https://developer.apple.com/design/human-interface-guidelines/toolbars)

### A4. Navegação escondida reduz descoberta e atrasa o usuário (o anti-padrão)
**[Fato validado]** (3-0, fundido de 4 afirmações)

Estudo empírico NN/g (179 participantes, 6 sites, desktop+mobile, 2015–2016, **re-validado em
2024–2025** em _"Beyond the Hamburger"_): navegação escondida **"is less discoverable than visible
or partially visible navigation"**; usada em só **27%** dos casos no desktop vs **48% visível / 50%
combo**; **">20% drop in discoverability"**; **"21% increase"** em dificuldade; **"at least 39%
slower"** no desktop e **"15% slower"** no mobile vs. combo. Apple HIG corrobora: _"The More tab
makes it harder for people to reach and notice content… Avoid overflow tabs whenever possible."_

> **Decisão p/ Algorithmia:** **eliminar** o padrão atual (setores espremidos que somem no mobile)
> e **não** adotar hambúrguer/drawer como navegação primária — manter todos os destinos visíveis na
> barra/rail. Este é o argumento central, com número, contra "esconder" os setores.

Fontes: [NN/g Hamburger Menus](https://www.nngroup.com/articles/hamburger-menus/) ·
[NN/g Hidden Nav Methodology](https://www.nngroup.com/articles/hidden-navigation-methodology/) ·
[Apple HIG Tab Bars](https://developer.apple.com/design/human-interface-guidelines/tab-bars)

### A5. Alvo de toque ≥ 44×44 px CSS (WCAG 2.5.5 AAA; 24×24 no AA do WCAG 2.2)
**[Fato validado]** (3-0, fundido de 2 afirmações)

W3C SC 2.5.5: _"The size of the target for pointer inputs is at least 44 by 44 CSS pixels"_ — nível
**AAA**. WCAG 2.2 SC 2.5.8 (nível **AA**) baixa o piso para **24×24** px CSS.

> **Decisão p/ Algorithmia:** alvos da nav inferior e dos controles de batalha **≥44px** (alinhado
> ao AAA, ideal para o polegar); **nunca** abaixo do piso AA de 24px. ⚠️ Não apresentar 44px como
> "requisito legal" — é AAA; o piso prático AA da web é 24px.

Fontes: [WCAG 2.1 SC 2.5.5](https://www.w3.org/WAI/WCAG21/Understanding/target-size.html) ·
[WCAG 2.2 SC 2.5.8](https://www.w3.org/WAI/WCAG22/Understanding/target-size-minimum.html)

### A6. `aria-current="page"` no item de nav ativo
**[Fato validado]** (3-0, fundido de 2 afirmações)

MDN: `aria-current` _"indicates that this element represents the current item within a container…"_;
valor `page` = _"the current page within a set of pages."_ Nota: em `role="tab"` estrito usa-se
`aria-selected`; para barras de **links** de navegação, `aria-current="page"` é o correto.

> **Decisão p/ Algorithmia:** marcar a aba/link ativo com `aria-current="page"` **além** do estilo
> visual (cor/sublinhado), para leitores de tela.

Fontes: [MDN aria-current](https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Reference/Attributes/aria-current) ·
[W3C APG Breadcrumb](https://www.w3.org/WAI/ARIA/apg/patterns/breadcrumb/)

---

## Domínio B — Motion / Microinterações + View Transitions

### B1. View Transitions cross-document (MPA): opt-in só-CSS, same-origin, degradação graciosa
**[Fato validado]** (3-0, fundido de 3 afirmações) — **o achado mais importante para um app PHP**

Chrome docs: opt-in **"with the @view-transition at-rule in CSS"**, descritor `navigation: auto`, e
**"do not require calling document.startViewTransition()."** Limitado a navegações same-origin;
ambos os documentos precisam optar. Artigo de "misconceptions": _"Whether your website is single or
multi-page… Browsers that don't support them will ignore the CSS opt-in."_ É exatamente o caso
PHP MPA / full-reload.

```css
@view-transition { navigation: auto; }
```

> **Decisão p/ Algorithmia:** adotar View Transitions cross-document como **progressive enhancement
> puro-CSS** — zero JS, zero dependência, alinhado à restrição "sem build". Dá continuidade tipo-SPA
> entre páginas server-rendered.

Fontes: [Chrome cross-document VT](https://developer.chrome.com/docs/web-platform/view-transitions/cross-document) ·
[MDN @view-transition](https://developer.mozilla.org/en-US/docs/Web/CSS/@view-transition) ·
[Chrome VT misconceptions](https://developer.chrome.com/blog/view-transitions-misconceptions) ·
[W3C CSS View Transitions 2](https://www.w3.org/TR/css-view-transitions-2/)

### B2. Suporte de navegador (jun/2026): same-document é Baseline; cross-document ainda não
**[Fato validado]** (3-0, fundido de 2 afirmações)

same-document VT: **"Baseline Newly available as of October 14, 2025"** — Chrome/Edge 111+, Opera
97+, Samsung 23+, Safari 18+, **Firefox 144+**. Cross-document: **Chrome/Edge 126+** (jun/2024) e
**Safari 18.2+** (dez/2024); **Firefox cross-document ainda atrasado** → cross-document **não** é
Baseline ainda. Navegadores sem suporte fazem navegação normal (corte seco).

> **Decisão p/ Algorithmia:** tratar como progressive enhancement; a experiência é **100% funcional
> sem** a API. Sem risco.

Fontes: [Chrome VT in 2025](https://developer.chrome.com/blog/view-transitions-in-2025) ·
[Chrome cross-document VT](https://developer.chrome.com/docs/web-platform/view-transitions/cross-document) ·
[WebKit 16967](https://webkit.org/blog/16967/) ·
[Firefox 144 release notes](https://developer.mozilla.org/en-US/docs/Mozilla/Firefox/Releases/144) ·
[caniuse view-transitions](https://caniuse.com/view-transitions)

### B3. Firefox não tem _view-transition types_ → código dependente de tipo exige PE
**[Consenso de mercado]** (2-1, menor confiança; 2 fontes oficiais)

Chrome blog: _"Firefox's initial implementation of same-document view transitions does not include
view transition types"_; recomenda usar `transitionHelper` para progressive enhancement.

> **Decisão p/ Algorithmia:** manter a lógica de transição **independente de tipo** ou usar
> feature-detection; não depender de _types_ como único mecanismo.

Fontes: [Chrome VT in 2025](https://developer.chrome.com/blog/view-transitions-in-2025) ·
[web.dev Baseline VT](https://web.dev/blog/baseline-view-transitions)

### B4. Snapshots vêm do compositor (sem layout/repaint extra para capturar)
**[Fato validado]** (3-0)

Chrome DevRel (Bramus): _"the data for the snapshots is taken directly from the compositor, so there
are no extra layout or repaint steps… to get the snapshot data."_ Escopo preciso: não há
layout/repaint extra **para capturar** — não é alegação de "custo zero" total.

> **Decisão p/ Algorithmia:** animar **transform/opacity** (compositor-friendly), não propriedades de
> layout; manter regiões com `view-transition-name` modestas em páginas pesadas (ex.: arena).

Fontes: [Chrome VT misconceptions](https://developer.chrome.com/blog/view-transitions-misconceptions) ·
[motion.dev](https://motion.dev/)

---

## Afirmações REFUTADAS (transparência — não usar)

1. **"Rail recolhido deve ter 3–7 itens; >5 destinos → rail modal expandido"** (m3 rail guidelines,
   voto **1-2**) — não substanciado. **Não** confiar num número específico de itens para o rail.
2. **"View transitions cross-document não quebram renderização incremental / são seguras no
   carregamento inicial (FCP)"** (Chrome misconceptions, voto **1-2**) — a segurança de FCP **não**
   foi confirmada. **Não** assumir impacto-zero no load inicial sem medir (vale para a arena pesada).

---

## Respostas aos 5 critérios de parada (até onde a passada 1 evidencia)

### Domínio A — Navegação/IA
1. **Estado da arte:** barra de navegação inferior persistente (3–5 destinos) no mobile,
   migrando para navigation rail no desktop; navegação sempre visível.
2. **Líderes:** Material 3 e Apple HIG convergem (3–5 destinos, só navegação, nunca esconder o
   primário). _(Casos nomeados — Duolingo/Spotify/Instagram — ficam para a passada 2.)_
3. **Decisão p/ Algorithmia:** ver A1–A6 — barra inferior mobile + rail desktop, 5 setores, alvos
   ≥44px, `aria-current`, **sem** hambúrguer/aba "Mais".
4. **Riscos remanescentes:** aplicar guideline _nativo_ (M3/HIG) à **web responsiva** é
   interpretação consolidada, não mandato web; "should always" do M3 é recomendação forte, não regra.
5. **Falta validação prática:** quantificação de thumb-zone (Hoober), Fitts/Hick, e casos reais
   nomeados com métricas → **passada 2**.

### Domínio B — Motion/View Transitions
1. **Estado da arte:** View Transitions cross-document como PE só-CSS para apps multi-página.
2. **Líderes:** documentação oficial (Chrome/WebKit/MDN) consolida o opt-in `@view-transition` +
   nomear elementos persistentes; animar no compositor.
3. **Decisão p/ Algorithmia:** adotar VT cross-document (zero JS/deps), animar transform/opacity,
   lógica independente de _type_, fallback = navegação normal.
4. **Riscos remanescentes:** suporte fragmentado (Firefox cross-document/types); FCP-safety não
   confirmada na arena pesada.
5. **Falta validação prática:** medir INP/LCP/memória na arena real; **+** timing/easing de motion,
   WCAG 2.3.3/`prefers-reduced-motion` e decisão nativo-vs-biblioteca → **passada 2**.

---

## Lacunas → Passada 2 (sinalizadas pelo próprio harness)

O harness confirmou fortemente a **doutrina de navegação** e o **suporte/mecânica de View
Transitions**, mas estes itens do brief **ficaram fortes na busca/fetch mas não entraram no top-10
verificado** — serão alvo da passada 2, com o mesmo rigor 3-votos:

- **Timing & easing de motion** (NN/g 100–500ms / limiares 0.1s·1s·10s; Doherty ~400ms; tokens de
  duração/easing do Material; ease-out para entradas) — o que Linear/Stripe/Vercel usam de fato.
- **`prefers-reduced-motion` + WCAG 2.3.3** (Animation from Interactions): obrigações; manter
  feedback essencial vs. desligar decorativo.
- **Nativo vs biblioteca** (View Transitions+WAAPI+CSS+Canvas próprio _vs_ Motion One ~2.3kb / GSAP /
  Framer Motion): bundle, performance, manutenção — com benchmark.
- **Thumb-zone / ergonomia** (Hoober 49% uma-mão / 36% _cradled_ / 15% duas-mãos; Fitts; Hick).
- **Casos reais nomeados** (Duolingo, Khan Academy, Sololearn, Instagram, Spotify, Linear, Stripe) e
  **por quê** funcionou.

---

## Fontes primárias desta passada (qualidade · ângulo)
- m3.material.io (navigation-rail, navigation-bar, motion tokens) — _primary_
- developer.apple.com HIG (Tab Bars, Toolbars) — _primary_
- developer.android.com (responsive/adaptive navigation) — _primary_
- nngroup.com (hamburger-menus, hidden-navigation-methodology, response-times, animation-duration) — _primary/secondary_
- w3.org/WAI (SC 2.5.5, SC 2.5.8, Animation from Interactions, técnica C39) — _primary_
- developer.mozilla.org (aria-current, @view-transition, View Transition API, Firefox 144) — _primary_
- developer.chrome.com / web.dev (cross-document VT, VT in 2025, misconceptions, Baseline) — _primary_
- webkit.org/blog/16967 — _primary_
- motion.dev/docs/gsap-vs-motion, emilkowal.ski/ui/great-animations, every.to (Invisible Details) — _primary/blog_
- caniuse.com/view-transitions — _primary_
