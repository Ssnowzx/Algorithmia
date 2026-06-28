# Pesquisa — Passada 5: Arquitetura front-end & Performance (imagens · loading · CWV · PE)

> **Parte da auditoria de produto do Algorithmia.** Método: workflow próprio de _deep research_
> (5 pesquisadores → **verificação adversarial 3-votos** [fonte-primária / desatualização /
> supergeneralização-sem-build] → síntese). **40 afirmações → 34 confirmadas, 1 refutada, 5
> não-falsificáveis.** A verificação chegou a inspecionar o código (R2.1/R5.1/R5.2). Nenhuma
> afirmação sem fonte; suporte de navegador citado com fonte.
>
> **Contexto:** PHP MVC server-rendered, sem build, **pesado em imagens** (artes 1024–1536px em
> slots 50–270px = desperdício 5–30×; ~48 PNGs sem `.webp` pela auditoria técnica). Helper
> `srcImagem()` prefere `.webp` por existência de arquivo e emite `<img src>` único.

---

## Eixo 1 — Imagens responsivas (`srcset`/`sizes`/`<picture>`)

- **[Fato validado] `srcset` com width descriptors (`w`) + `sizes`** deixa o browser escolher a
  variante (lê tela/DPR/slot → baixa a menor que cobre). Ex. MDN: 480px=63KB vs 800px=128KB (~51%).
  Mecanismo exato para artes grandes em slots pequenos. <https://developer.mozilla.org/en-US/docs/Web/HTML/Guides/Responsive_images>
- **[Fato validado] `sizes` = largura de EXIBIÇÃO do slot** (não viewport); não aceita %; o browser
  ignora tudo após a 1ª media condition verdadeira. **Erro nº1:** `100vw` num slot de 270px.
- **[Fato validado] Não misturar `1x/2x` (density) com `w` (width);** `sizes` só vale com `w`. Itens
  de tamanho fixo → density; artes que variam → `w`+`sizes`.
- **[Fato validado] `srcset`/`sizes` = sugestão; `<source media>` em `<picture>` = comando** (art
  direction). <https://web.dev/learn/design/picture-element>
- **[Fato validado] Troca de FORMATO via `<picture>` `<source type>` AVIF→WebP→`<img>` PNG.** Fecha o
  gap dos ~48 PNGs com negociação **real** de suporte (não só troca de extensão).
- **[Fato validado] Suporte:** WebP ~96% (Safari 14+), AVIF ~93% (Safari 16.4+) — seguros **com**
  fallback `<img>` PNG. <https://caniuse.com/webp> · <https://caniuse.com/avif>
- **[Boa prática] Gerar variantes OFFLINE** (script Python/`cwebp`/`avifenc`), poucas larguras
  escalonadas; PHP só monta markup. **Sempre `width`/`height`.**

### ✅ Decisão p/ Algorithmia
**(1)** Right-size offline (prioridade nº1): itens/ícones → density 64/128px (sem `sizes`); artes
(mestre/herói/fundo) → 3–4 larguras `w` (300/600/900/1200) + `sizes` por slot. **(2)** Evoluir
`srcImagem()`/`svg()` para emitir `<picture>` AVIF→WebP→PNG. **(3)** `sizes` correto por slot (não
`100vw`). **Porquê:** o desperdício 5–30× domina o LCP; é a única alavanca, 100% no HTML.

---

## Eixo 2 — Formatos modernos (WebP/AVIF)

- **[Fato validado] Cadeia `<picture>` AVIF→WebP→PNG** (browser usa o 1º `type` suportado). HTML
  puro, sem JS/build. <https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/picture>
- **[Consenso] Economia:** WebP ~25–34% < JPEG / ~26% < PNG lossless; AVIF ~50% < JPEG (~20–30% < WebP).
- **[Consenso] ⚠️ Arte ILUSTRADA: AVIF nem sempre ganha** — em cor chapada/UI/texto pode comprimir
  pior que PNG/WebP lossless. **Testar caso a caso e deixar o `<picture>` escolher.**
- **[Fato validado] Transparência (itens/heróis):** WebP lossless ~26% < PNG; WebP lossy chega a
  60–70% de redução. **[Consenso] Encode AVIF é ~10–100× mais lento** → só offline.

### ✅ Decisão p/ Algorithmia
Cadeia `<picture>` AVIF→WebP→PNG (PNG sempre como `<img>` fallback); encode por tipo de arte (pintada
grande → AVIF lossy+WebP; UI/ícone/transparência → WebP/AVIF lossless, **pulando AVIF se ficar maior**);
tudo num script offline `tools/gerar_variantes.py`. **[Boa prática] O maior ganho é o REDIMENSIONAMENTO
(~80–95%), não o formato (~25–50%) — fazer os dois, nessa ordem.**

> ⚠️ **REFUTADA R2.1 (1/3):** a frase "PNG como fallback cobre os ~4%" **não vale no código atual** —
> `srcImagem()` (app/core/helpers.php ~63–90) seleciona server-side por existência de arquivo e
> `svg()` emite `<img src>` único; **não há** `<picture>`/`<source>`/`srcset`/negociação por `Accept`.
> Como o `.webp` quase sempre existe, navegadores sem WebP recebem `<img src="...webp">` **quebrado**.
> **A recomendação só vale APÓS migrar o helper para `<picture>`.** (Fonte: leitura do código + caniuse.)

---

## Eixo 3 — Estratégia de carregamento

- **[Fato validado] NUNCA `loading="lazy"` no LCP / above-the-fold** — anti-padrão (perde o preload
  scanner). A/B web.dev: lazy em tudo regrediu LCP; excluir above-the-fold reverteu (−18%). Artes do
  topo = **eager**. <https://web.dev/articles/lcp-lazy-loading>
- **[Fato validado] `loading="lazy"` SÓ abaixo da dobra** (inimigos fora de tela, grids de loja).
  Maior ROI sem build. Suporte: Chrome 77+/FF 75+/Safari 15.4+ (mar/2022).
- **[Fato validado] `fetchpriority="high"` no LCP** (imagens são Low por padrão). Google Flights:
  LCP 2,6→1,9s (~−27%). Baseline out/2024. **Não usar em >1–2 imagens** (anula o efeito).
  <https://web.dev/articles/fetch-priority>
- **[Fato validado] `width`+`height` (ou `aspect-ratio`) em TODA `<img>`** → reserva espaço → mata CLS
  (meta ≤0,1). Dimensão intrínseca nos atributos; CSS controla exibição. <https://web.dev/articles/optimize-cls>
- **[Consenso] `<link rel=preload as=image>` NÃO é 1ª escolha em server-render** (HTML já tem o `<img>`);
  reservar para LCP escondido em `background-image` CSS. **[Consenso] `decoding="async"`** é barato mas
  não move o LCP.
- **[Boa prática] `content-visibility:auto` + `contain-intrinsic-size`** em páginas longas (bestiário/loja/
  fases) — pula layout/paint off-screen (~7×). Baseline 2024. Testar find-in-page/âncoras.

### ✅ Decisão p/ Algorithmia
Política por posição no helper: above-the-fold → `eager` + (LCP) `fetchpriority="high"`; below-the-fold
→ `lazy`. `width`/`height` intrínsecos sempre; `decoding="async"` default; `content-visibility` nas
seções longas. **Medir o LCP real por tela antes de marcar `fetchpriority`.**
_(Correção R3.1: Safari 15.4 = mar/2022, não set/2022 — só ajuste de doc; técnica intacta.)_

---

## Eixo 4 — Core Web Vitals & performance budget

- **[Fato validado] Limiares 2026 (p75):** LCP ≤2,5s, **INP ≤200ms** (substituiu FID em 12/03/2024),
  CLS ≤0,1; "ruim": LCP >4s, INP >500ms, CLS >0,25. <https://web.dev/articles/defining-core-web-vitals-thresholds>
- **[Fato validado] CWV avaliados no p75 de campo (CrUX)** → otimizar p75 mobile, não média de lab.
- **[Fato validado] Imagem sem `width`/`height` = causa clássica de CLS.**
- **[Fato validado] Budget de peso:** página mediana ~2,3–2,9MB, imagens ~37–40% (~0,9–1MB) e ~16–18
  requests. **Alvo: abaixo da mediana — ≤1MB de imagem e ≤15 requests por tela.** <https://almanac.httparchive.org/en/2025/page-weight>
- **[Consenso] Maior ganho = redimensionamento (≥80%), não formato (25–50%).**

### ✅ Decisão p/ Algorithmia — budget numérico (p75 mobile)
| Métrica | Meta |
|---|---|
| LCP | ≤ 2,5s (alvo interno ≤ 2,0s) |
| INP | ≤ 200ms |
| CLS | ≤ 0,1 |
| TTFB | ≤ 800ms |
| Peso de imagem / tela | ≤ 1MB |
| Requests de imagem / tela | ≤ 15 |

Ações: right-size offline; `width`/`height` sempre; `fetchpriority` só no LCP; lazy abaixo da dobra;
TTFB via cache/compressão. **Risco:** lab×campo divergem; INP depende do JS de batalha, não das imagens.

---

## Eixo 5 — Progressive enhancement & performance server-rendered

- **[Consenso] PE (HTML semântico → CSS → JS) é a arquitetura natural** do conteúdo/forms do jogo.
- **[Fato validado] Cache em camadas:** assets com fingerprint → `max-age=31536000` (+ `immutable` **só**
  se a URL muda a cada conteúdo); HTML do PHP → `no-cache`. <https://csswizardry.com/2019/03/cache-control-for-civilians/>
- **[Fato validado] Brotli (fallback gzip)** p/ HTML/CSS/JS: ~15–25% < gzip; pré-comprimir offline ou
  on-the-fly. **[Fato validado] `defer` em todo `<script>` próprio** (som→juice→batalha), em ordem.
- **[Consenso] HTTP/2 dispensa concatenar os 5 CSS** (multiplexing); incluir CSS/JS **por rota** no PHP;
  usar a aba Coverage do DevTools. **[Opinião] Critical CSS não é prioridade aqui** (Harry Roberts).

### ✅ Decisão p/ Algorithmia
Cache em camadas + Brotli + `defer` + CSS/JS por rota + PE no conteúdo/forms. Não investir em Critical CSS.

> ⚠️ **REFUTADA R5.1 (3/3):** `immutable` **não** é "seguro sem risco" — em URL **estável** (`style.css`,
> logo), browsers que o honram servem conteúdo obsoleto por todo o `max-age` sem revalidar. O projeto
> versiona por **query-string `?v=filemtime`**, não por hash no nome → usar só `max-age=31536000`;
> adicionar `immutable` **somente** com fingerprint consistente em 100% das referências.
>
> ⚠️ **REFUTADA R5.2 (1/3):** PE total **não se aplica à BATALHA** — `arena.php` deixa o painel de
> perguntas vazio (preenchido por JS), e `responder/especial/pocao/fragmento/fugir` retornam **JSON**.
> Sem JS, a batalha não funciona. **Aplicar PE ao conteúdo/forms transacionais (login/registro/nav/códex);
> tratar a batalha como aplicação JS deliberada — não re-arquitetar o loop.** (Fonte: leitura do código.)

---

## Síntese executiva — ordem de prioridade global (maior ROI → menor)

1. **Right-size offline** das ~48+ artes oversize → ataca LCP + peso (≥80–95% dos bytes).
2. **`width`/`height` em toda `<img>`** → mata CLS.
3. **`lazy` abaixo da dobra** (puro atributo).
4. **Migrar helper para `<picture>`** → fecha gap dos PNGs **e** dá fallback real (R2.1).
5. **`fetchpriority="high"` no LCP** (só 1–2).
6. **Brotli + cache headers** (`max-age` longo em asset; `no-cache` no HTML).
7. **`defer` + CSS/JS por rota.**

## Insumos diretos para a auditoria / protótipo
1. Script offline `tools/gerar_variantes.py` (larguras + WebP/AVIF, preservando alpha, pulando AVIF se maior).
2. Evoluir `srcImagem()`/`svg()` → `<picture>` AVIF→WebP→PNG com `srcset`/`sizes`/`width`/`height`/`loading`/`fetchpriority` (pré-requisito do Eixo 2 — R2.1).
3. Tabela de slots por tela (auditar CSS de cada slot → `sizes` correto).
4. Identificar o LCP real por rota antes de `fetchpriority`; tratar `background-image` LCP.
5. Cache: confirmar `?v=filemtime` em todas as referências antes de cogitar `immutable`.
6. Brotli: verificar se já ativo em produção.
7. `defer` + includes condicionais por rota; rodar Coverage.
8. Budget de CI/QA (tabela do Eixo 4).
9. Escopo do PE: conteúdo/forms sem JS; batalha = app JS deliberada (R5.2).
10. Correções de doc: Safari 15.4 = mar/2022 (R3.1); `immutable` no Edge só EdgeHTML 15–18 (R5.1).
