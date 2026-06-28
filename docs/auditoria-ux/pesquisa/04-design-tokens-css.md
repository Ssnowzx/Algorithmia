# Pesquisa — Passada 4: Design System sem build (tokens · CSS vars · arquitetura · theming · governança)

> **Parte da auditoria de produto do Algorithmia.** Método: workflow próprio de _deep research_
> (5 pesquisadores → **verificação adversarial 3-votos** [fonte-primária / desatualização /
> supergeneralização-sem-build] → síntese citada). **40 afirmações → 30 confirmadas, 1
> refutada, 9 não-falsificáveis.** Rótulos: `[Fato validado]` / `[Consenso de mercado]` /
> `[Boa prática]` / `[Hipótese]` / `[Opinião]`. Nenhuma afirmação sem fonte.
>
> **Contexto:** PHP MVC server-rendered, **sem build** (sem Sass/PostCSS/bundler). CSS atual:
> `tokens.css` (aditivo, namespace `--cor-*` + tokens novos) e `style.css` (~1472 linhas, `:root`
> com namespace curto `--bg`/`--primaria`). Duplicação `--bg` vs `--cor-bg` → decisão: curto
> **canônico**, `--cor-*` vira **alias** (`var(--bg)`).

---

## Eixo 1 — Arquitetura de tokens

**[Consenso de mercado] Modelo de 3 tiers é o padrão:** primitivo → semântico/alias → componente.
Brad Frost (raw → theme → component); Material 3 (**ref → sys → comp**).
Fontes: <https://bradfrost.com/blog/post/the-many-faces-of-themeable-design-systems/> · <https://m3.material.io/foundations/design-tokens>

**[Consenso de mercado] A camada semântica DESACOPLA tema/contexto do uso** — trocar tema vira "token
swap" (repontar `color-action: blue→green` muda tudo, primitivos intactos). Valida `--hud-cor`/
`--cor-regiao` como **semânticos que recalculam por contexto**.
Fonte: <https://m3.material.io/foundations/design-tokens>

**[Fato validado] DTCG (W3C) atingiu 1ª versão ESTÁVEL em out/2025** — JSON com `$value`/`$type` e
**aliases por referência** `{grupo.token}`, que mapeiam 1:1 em CSS (`--text-primary: var(--color-black)`).
Fontes: <https://www.designtokens.org/tr/drafts/format/> · <https://www.w3.org/community/design-tokens/2025/10/28/design-tokens-specification-reaches-first-stable-version/>

**[Fato validado] Não há ordem "canônica" universal de níveis** (`color-bg` vs `bg-color`); a regra é
**escolher UMA ordem e aplicá-la consistentemente** (Nathan Curtis/EightShapes).
Fonte: <https://medium.com/eightshapes-llc/naming-tokens-in-design-systems-9e86c7444676>

**[Fato validado] Features CSS nativas úteis (suporte confirmado):** `@layer` Baseline desde **mar/2022**;
CSS Nesting Baseline **2023**; `light-dark()` Baseline **mai/2024**; `@property` Baseline **jul/2024**
(os três últimos = progressive enhancement com fallback).
Fontes: <https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/At-rules/@layer> · <https://developer.mozilla.org/en-US/docs/Web/CSS/@property>

**[Boa prática] Para projeto pequeno, 2 tiers (primitivo + semântico) bastam**; tokens de componente só
quando há controle fino. **[Boa prática] Taxonomia posicional** (namespace→categoria→conceito→propriedade→
variante), só os níveis necessários. **[Boa prática] Tornar o curto canônico e `--cor-*` alias é o padrão
DTCG em CSS puro** (custom properties Baseline desde ~2017 → seguro).

### ✅ Decisão p/ Algorithmia
**Arquitetura de 2,5 tiers, nomenclatura posicional fixa:** (1) **Primitivos** — paleta crua de
`style.css` (navy `#0b0c1d`, roxo `#7c5cff/#9d83ff`, ouro `#ffce47`, ciano `#8ce6ff`), opcionalmente
`--palette-*` declarados 1×; (2) **Semânticos/uso = CANÔNICO** — `--bg`/`--primaria` (fonte de verdade,
consumida pelos componentes); (3) **tematização contextual** — `--hud-cor`/`--cor-regiao`/raridade (já
corretos); (4) **`--cor-*` = ALIAS** (`--cor-bg: var(--bg)`, nunca literal). Documentar a ordem de
nomes no topo do `tokens.css`. **Riscos:** retrabalho se primitivos vierem tarde; custo de renomear se a
ordem mudar.

---

## Eixo 2 — CSS custom properties em escala

**[Fato validado] Aliasar `--cor-bg: var(--bg)` funciona independente da ordem** de declaração e de
estarem em `<link>`s diferentes — `var()` resolve em **computed-value time** (depois da cascata). **A
migração é segura sem restrição de ordem.** Fonte: <https://www.w3.org/TR/css-variables-1/>

**[Fato validado] O risco real NÃO é ordem, é DEFINIÇÃO DUPLICADA do mesmo token** — se dois arquivos
derem valor literal a `--cor-bg`, o último carregado vence silenciosamente. **Cada token lógico = uma
fonte de verdade.** Fonte: <https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_cascading_variables>

**[Fato validado] Alias CÍCLICO quebra ambas** (`--bg: var(--cor-bg)` + `--cor-bg: var(--bg)` → invalid
at computed-value time → herdado/inicial). **Alias estritamente unidirecional** (alias→canônico).
Fonte: <https://www.w3.org/TR/css-variables-1/>

**[Consenso de mercado] Tematizar sobrescrevendo a custom property num escopo mais estreito é idiomático —
e o projeto já faz certo** (`--cor-regiao` por região, `--hud-cor` por classe; defaults no `:root`).
Fonte: <https://developer.mozilla.org/en-US/docs/Web/CSS/Using_CSS_custom_properties>

**[Fato validado] Performance:** mudar variável herdada no `:root` recalcula a subárvore (~164ms/50k nós,
Chrome 83) — ok para troca **infrequente** (tema). Para valores que mudam muito (HUD via `setProperty`,
animação) declarar no elemento mais profundo. Fonte: <https://blogs.igalia.com/jfernandez/2020/08/13/improving-css-custom-properties-performance/>

**[Fato validado] `@property` torna tokens type-safe (`syntax`), dá `initial-value` (fallback contra typo)
e, com `inherits:false`, corta o recálculo** de tokens que não precisam herdar (raridade, animados).
Baseline jul/2024 → usar com `@supports`/`initial-value`. Fonte: <https://web.dev/blog/at-property-performance>

**[Consenso de mercado] `@layer` para formalizar ordenação** (`@layer tokens, base, components, regions,
utilities`) — doma especificidade sem tooling, mapeia no ITCSS informal do projeto. Baseline ~mar/2022.

### ✅ Decisão p/ Algorithmia
**Unificar `--bg` (canônico) ↔ `--cor-bg` (alias) sob 3 invariantes:** (1) **fonte única** — literais só
nos nomes curtos; cada `--cor-*` é estritamente `var(--curto)` (auditar com grep `--cor-…:\s*#|rgb|hsl`);
(2) **alias unidirecional** — nunca `--bg: var(--cor-bg)`; (3) **escopo de mutação** — tokens dinâmicos
(HUD, raridade animada) no elemento mais específico, idealmente `@property{inherits:false}` com `@supports`.
A ordem dos `<link>` deixa de importar.

---

## Eixo 3 — Arquitetura de CSS sem build

**[Fato validado] `@layer` controla especificidade pela ORDEM DAS CAMADAS, não por especificidade/fonte**
— regra de baixa especificidade numa camada posterior vence alta especificidade numa anterior.
`@layer reset, default, themes, patterns, layouts, components, utilities;`. **GOTCHA:** estilos **fora**
de camada vencem todas as camadas — não deixar metade do CSS solto.
Fontes: <https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/At-rules/@layer> · <https://css-tricks.com/css-cascade-layers/>

**[Consenso de mercado] ITCSS (Harry Roberts) mapeia ~1:1 em cascade layers** (settings→tools→generic→
elements→objects→components→utilities; do genérico ao localizado, baixa→alta especificidade).
Fontes: <https://css-tricks.com/css-cascade-layers/> · <https://www.xfive.co/blog/itcss-scalable-maintainable-css-architecture>

**[Fato validado] Dividir CSS em múltiplos `<link>` (paralelo), NUNCA encadear partials com `@import`** —
`@import` força downloads sequenciais no caminho crítico (caso real: FCP P80 mobile 2782ms→1872ms, ~33%,
ao remover `@import`). Fontes: <https://csswizardry.com/2018/11/css-and-network-performance/> · <https://www.debugbear.com/blog/avoid-css-import>

**[Fato validado] CSS nesting nativo ~90% de suporte** (Chrome/Edge 120+, FF 117+, Safari 17.2+) — usar
para **estrutura/estado**, não para gerar nomes (não monta `.block__element` de `&__element`). **GOTCHA:**
`&` calcula especificidade como `:is()` (herda a mais alta da lista) → combinar com `@layer` neutraliza.
Fonte: <https://caniuse.com/css-nesting>

**[Boa prática/Opinião] Para o `components.css`: nomes BEM (à prova de colisão global)** + filosofia CUBE
CSS (apoiar na cascata/camadas, blocos finos, variações por `data-attribute`).
Fontes: <https://cube.fyi/> · <https://piccalil.li/blog/cube-css/>

### ✅ Decisão p/ Algorithmia
**`@layer` no topo do `style.css` com ordem ITCSS + dividir CSS em poucos `<link>` paralelos (sem
`@import`):** `@layer reset, tokens, base, components, regions, utilities;`. `tokens.css`→`tokens`;
esqueleto→`base`; blocos→`components`; tematização→`regions`; trumps→`utilities`. Nesting para
estrutura/estado; nomes de classe completos (BEM); variações por `data-attribute`.

> ⚠️ **REFUTADO 3/3 — o `@layer` NÃO "degrada graciosamente":** em motor sem suporte, **todo o conteúdo
> dentro de blocos `@layer{}` é DESCARTADO** (página ficaria sem estilo), não "aplicado como se não
> houvesse camada". Remédio = duplicar fora ou polyfill PostCSS (= o build que se quer evitar). Adotar
> `@layer` é seguro **hoje** só porque o suporte é universal há 4+ anos (~96%+), **não** por degradação.
> Manter CSS **crítico** fora de camadas se a analytics do público mostrar navegadores antigos.
> Fontes: <https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/At-rules/@layer> · <https://caniuse.com/css-cascade-layers>

---

## Eixo 4 — Theming, dark mode e tema por-componente

**[Fato validado] Dark mode em CSS puro:** `color-scheme: light dark` no `:root` + `light-dark(claro,
escuro)` nos valores — elimina blocos duplicados de `@media (prefers-color-scheme)`. Baseline **2024**
(prover fallback). Toggle manual sobrescreve `color-scheme` num escopo, não a função.
Fonte: <https://developer.mozilla.org/en-US/docs/Web/CSS/color_value/light-dark>

**[Consenso de mercado] Tematização por-componente trocando UMA custom property num escopo é idiomática**
(não anti-pattern): o componente define `--color` local e **deriva** o resto; variante = redefinir
`--color`. Exato `--hud-cor`/`--cor-regiao`. Fonte: <https://developer.mozilla.org/en-US/blog/color-palettes-css-color-mix/>

**[Fato validado] Para escalar acentos SEM explosão de tokens, derive variações (hover/borda/alpha) de
UMA cor-base com `color-mix()`** (Baseline 2023 → seguro). **[Consenso] Interpolar em OkLab/OkLCH**
(`color-mix(in oklab, …)`), não sRGB/HSL — evita tons "lavados" e mantém contraste previsível.
Fontes: <https://developer.mozilla.org/en-US/blog/color-palettes-css-color-mix/> · <https://caniuse.com/css-color-mix>

**[Fato validado] Relative color syntax** (`oklch(from var(--cor) l c calc(h+120))`) dá controle de canal
fino, mas é mais nova (Baseline 2024) — preferir `color-mix()` por compatibilidade; relative só onde
justifica, com fallback. Fonte: <https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_colors/Relative_colors>

### ✅ Decisão p/ Algorithmia
**Manter tematização escopada e padronizar a DERIVAÇÃO de acentos via `color-mix(in oklab, …)`** a partir
de `--hud-cor`/`--cor-regiao`/raridade: cada classe/região define **só a cor-base**; estados/bordas/
superfícies/alphas são derivados. Dark mode (se/quando): `color-scheme` + `light-dark()` com fallback.
**Riscos:** features 2024 exigem fallback; **validar contraste WCAG** das cores derivadas caso a caso.

---

## Eixo 5 — Pitfalls, consolidação & governança

**[Consenso de mercado, COM RESSALVA] Dois nomes p/ o mesmo valor é "token drift"; consolidar é higiene.**
**Ressalva (1/3 refutou):** a literatura pressupõe escala/multi-equipe/tooling; na auditoria do código,
`style.css` tem os tokens **vivos** e `var(--cor-bg)` é consumido **zero vezes** → `--cor-bg` é **alias
dormente de documentação** (risco **latente**, não drift consumado). O núcleo (um nome por conceito)
permanece. Fonte: <https://designtokens.substack.com/p/common-mistakes-in-design-tokens>

**[Fato validado] A forma SEGURA de unificar sem mudança visual é aliasar** (`--cor-bg: var(--bg)`) —
computed value idêntico → **zero diff**; permite migração incremental e remoção posterior do alias.
Fonte: <https://css-tricks.com/a-complete-guide-to-custom-properties/>

**[Consenso de mercado] Direção do alias importa:** canônico = token de **propósito/semântico**; apelidos
legados apontam para ele. Nomes por aparência são passivo (rebrand quebra). Fonte: <https://www.netguru.com/blog/design-token-naming-best-practices>

**[Consenso de mercado] Para equipe de 1, governança formal pesada é contraproducente** — leve, por
convenção: "use o que já existe antes de criar"; "não coloque lixo no DS"; "rastreie snowflakes".
Fontes: <https://www.nngroup.com/articles/lean-design-system-teams/> · <https://bradfrost.com/blog/post/a-design-system-governance-process/>

**[Boa prática] Aderência se MEDE com lint/grep de hex hardcoded** (KPI mais barato de drift) — sem build,
um grep `#[0-9a-f]{3,6}|rgb\(|hsl\(` em git pre-commit/CI. **[Boa prática] Documentar cada token** (tabela
"canônico → propósito → alias deprecated" no topo do `tokens.css`). **[Boa prática] Não criar muitos
tokens cedo** (overengineering); o Algorithmia já tem o conjunto certo — o trabalho é **consolidar**.

### ✅ Decisão p/ Algorithmia
**Consolidação por alias + governança leve, 4 passos:** (1) **aliasar, não renomear em massa**
(`--cor-bg: var(--bg)`; `--cor-*` marcado **deprecated** em comentário); (2) **documentar** tabela
canônico→propósito→alias no topo do `tokens.css`; (3) **KPI sem toolchain** — grep de hex/rgb/hsl cru
fora de `tokens.css` em pre-commit/CI; (4) **3 regras** de convenção, sem comitês. **Critério de remoção
do alias:** quando os call-sites `--cor-*` chegarem a zero (grep confirma que já é ~zero hoje).

---

## Síntese executiva

| Eixo | Decisão central | Feature-chave (suporte) | Risco-mestre |
|---|---|---|---|
| 1 Tokens | 2,5 tiers; namespace curto canônico; nomenclatura posicional fixa | custom properties (Baseline) | overengineering vs retrabalho de primitivos |
| 2 CSS vars | fonte-única + alias unidirecional + escopo de mutação | `@property inherits:false` (jul/2024, enhancement) | literal duplicado vence cascata; ciclo invalida |
| 3 CSS sem build | `@layer` ITCSS + `<link>`s paralelos (sem `@import`) | `@layer` (mar/2022, ~96%+) | **blocos `@layer` DESCARTADOS** em motor sem suporte |
| 4 Theming | derivar acentos via `color-mix(in oklab)`; dark via `light-dark()`+fallback | `color-mix()` (2023); `light-dark()` (2024) | features 2024 exigem fallback; contraste das derivações |
| 5 Governança | aliasar+documentar deprecated; grep de hardcode no CI; 3 regras lean | git pre-commit/CI (zero toolchain) | importar cerimônia de escala; alias eterno |

**Fio condutor:** a migração `--bg` (canônico) ↔ `--cor-bg` (alias) é **segura, sem build, sem diff
visual** (resolução em computed-value time); a única armadilha mecânica é literal duplicado/ciclo,
coberta pelas invariantes do Eixo 2. `@layer`/`light-dark()`/`color-mix()`/`@property` são **progressive
enhancement** opcional sobre essa base de risco zero.

### Insumos diretos para o protótipo
- Tokens: namespace curto canônico; `--cor-*` como alias; documentar no topo.
- Acentos por classe/região/raridade: **uma cor-base** + `color-mix(in oklab, …)` para estados/bordas/alpha.
- Organização: `@layer` (ITCSS) só se suporte universal; `<link>`s paralelos; nesting p/ estado; nomes BEM.
- KPI de drift: grep de hex cru fora de `tokens.css`.
