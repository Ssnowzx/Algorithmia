# Referências — Auditoria de Produto (UX)

Bibliografia das **fontes primárias** citadas nos achados, agrupada por tema. Para cada fonte indicamos
o(s) relatório(s) de deep research em [`pesquisa/`](pesquisa/README.md) que a embasam:
**P01** = `01-navegacao-e-motion.md` · **P02** = `02-timing-motion-libs-casos.md` ·
**P03** = `03-gamificacao-retencao.md` · **P04** = `04-design-tokens-css.md` ·
**P05** = `05-arquitetura-performance.md`.

> Cada afirmação factual foi rotulada por força de evidência e passou por verificação adversarial 3-votos
> nos relatórios de pesquisa. Use os relatórios para o contexto completo (incluindo afirmações refutadas).

---

## Navegação / Arquitetura da Informação

- Material 3 — Navigation bar guidelines (barra inferior em compact; 3–5 destinos; nunca rail + bar juntos) — P01, P02
  <https://m3.material.io/components/navigation-bar/guidelines>
- NN/g — Hamburger/hidden menus (queda de descoberta >20%; ≥39% mais lento no desktop) — P01, Fluxo
  <https://www.nngroup.com/articles/hamburger-menus/>
- Apple HIG — Tab bars ("use a tab bar to support navigation, not to provide actions") — P01
  <https://developer.apple.com/design/human-interface-guidelines/tab-bars>
- Creative Bloq — Caso Spotify "Create" na bottom-nav (memória muscular / mistaps) — P01, P02
  <https://www.creativebloq.com/web-design/ux-ui/spotifys-latest-ui-design-change-is-driving-people-crazy>
- Laws of UX — Lei de Fitts (alvos maiores/espaçados reduzem tempo e erro) — P01, P02
  <https://lawsofux.com/fittss-law/>
- W3C WAI-ARIA APG — Breadcrumb pattern (orientação "você está aqui") — P01
  <https://www.w3.org/WAI/ARIA/apg/patterns/breadcrumb/>
- Duolingo (Deconstructor of Fun) — Leagues / cohorts ~20–30 — P03
  <https://duolingo.deconstructoroffun.com/mechanics/leagues>
- Trophy.so — Duolingo gamification (camadas hábito→progressão→competição; ligas ~30) — P02, P03
  <https://trophy.so/blog/duolingo-gamification-case-study>

## Motion / View Transitions

- Material 3 — Easing & duration tokens (Short 50–200 / Medium 250–400 / Long 450–600ms; `cubic-bezier(0.2,0,0,1)`) — P02
  <https://m3.material.io/styles/motion/easing-and-duration/tokens-specs>
- NN/g — Animation duration (feedback ~100ms; mudanças 200–300ms; >400ms já é "lenta"; ease-out/ease-in) — P02
  <https://www.nngroup.com/articles/animation-duration/>
- web.dev — prefers-reduced-motion (opt-in `no-preference` para ligar movimento) — P02
  <https://web.dev/articles/prefers-reduced-motion>
- CSS-Tricks — Nuking motion with prefers-reduced-motion (catch-all `0.01ms` + `iteration-count:1`) — P02
  <https://css-tricks.com/nuking-motion-with-prefers-reduced-motion/>
- Motion.dev — Web animation performance tier list (transform/opacity/filter = S-Tier; box-shadow = C-Tier) — P02
  <https://motion.dev/magazine/web-animation-performance-tier-list>
- Chrome for Developers — View Transitions cross-document (`@view-transition { navigation: auto }`, só-CSS) — P01
  <https://developer.chrome.com/docs/web-platform/view-transitions/cross-document>
- MDN — `@view-transition` (opt-in, same-origin; fallback grátis em navegador sem suporte) — P01
  <https://developer.mozilla.org/en-US/docs/Web/CSS/@view-transition>
- Emil Kowalski — Great animations (o que faz um modal parecer premium; assimetria entrada/saída) — P02
  <https://emilkowal.ski/ui/great-animations>
- Vercel — Design guidelines (linguagem de movimento consistente) — P02
  <https://vercel.com/design/guidelines>
- caniuse — Web Animations API (~96% de cobertura, 0 KB) — P02
  <https://caniuse.com/web-animation>
- Motion.dev — Framer Motion vs Motion One (custo de bundle sem tree-shaking; Motion mini ~2,3 KB) — P02
  <https://motion.dev/magazine/should-i-use-framer-motion-or-motion-one>

## Acessibilidade / WCAG

- W3C — Understanding SC 2.5.5 Target Size (AAA, "at least 44 by 44 CSS pixels") — P01
  <https://www.w3.org/WAI/WCAG21/Understanding/target-size.html>
- W3C — Understanding SC 2.5.8 Target Size (Minimum) (AA, 24×24px) — P01
  <https://www.w3.org/WAI/WCAG22/Understanding/target-size-minimum.html>
- MDN — `aria-current` (`page` = current page within a set of pages) — P01
  <https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Reference/Attributes/aria-current>
- W3C — Understanding SC 2.4.7 Focus Visible — P01
  <https://www.w3.org/WAI/WCAG21/Understanding/focus-visible.html>
- W3C — Technique C39 (reduzir movimento via `prefers-reduced-motion`) — P02
  <https://www.w3.org/WAI/WCAG22/Techniques/css/C39>
- W3C — Understanding SC 1.4.3 Contrast (Minimum) (4,5:1 normal / 3:1 grande) — P01
  <https://www.w3.org/WAI/WCAG21/Understanding/contrast-minimum.html>
- W3C WAI — Images decision tree (`alt` informativo vs `alt=""` decorativo) — P05
  <https://www.w3.org/WAI/tutorials/images/decision-tree/>

## Gamificação / Retenção

- NN/g — Autonomy, relatedness, competence (SDT; feedback de competência sustenta motivação intrínseca) — P03
  <https://www.nngroup.com/articles/autonomy-relatedness-competence/>
- Deci, Koestner & Ryan (1999) — meta-análise de recompensas extrínsecas (~128 experimentos) — P03
  <https://depts.washington.edu/techdocs/papers/deciExtrinsicRewardsAndIntrinsicMotivation99.pdf>
- Deci/Koestner/Ryan — Review of Educational Research (recompensas tangíveis esperadas minam interesse) — P03
  <https://journals.sagepub.com/doi/10.3102/00346543071001001>
- Chicago Booth Review — Going for the goal (goal-gradient; cartão pré-preenchido ~18% mais rápido) — P03
  <https://www.chicagobooth.edu/review/going-goal>
- Coglode — Endowed Progress Effect — P03
  <https://www.coglode.com/nuggets/endowed-progress-effect>
- Nunes & Drèze (2006), JCR — Endowed progress (34% vs 19% de conclusão) — P03
  <https://academic.oup.com/jcr/article-pdf/32/4/504/17928623/32-4-504.pdf>
- Kivetz, Urminsky & Zheng (2006), JMR — Goal-gradient (12,7 vs 15,6 dias) — P03
  <https://journals.sagepub.com/doi/abs/10.1509/jmkr.43.1.39>
- Irrational Labs — When progress bars backfire (saliência pode sair pela culatra; metas obscuras) — P03
  <https://irrationallabs.com/blog/knowledge-cuts-both-ways-when-progress-bars-backfire/>
- Ian Bogost — Gamification is bullshit (pointsification; incentivos vazios) — P03
  <https://bogost.com/writing/blog/gamification_is_bullshit/>
- Yu-kai Chou — Effective leaderboards (segmentar; mostrar subida pessoal, não posição crua) — P03
  <https://yukaichou.com/advanced-gamification/how-to-design-effective-leaderboards-boosting-motivation-and-engagement/>
- Sailer & Homner (2020), Springer — meta-análise de gamificação (efeito cognitivo g≈0,49) — P03
  <https://link.springer.com/article/10.1007/s10648-019-09498-w>
- The Behavioral Scientist — Review de "Hooked" (Nir Eyal) — P03
  <https://www.thebehavioralscientist.com/articles/an-incomplete-loop-a-review-of-nir-eyals-hooked>
- Explore Psychology — Variable-ratio schedule (resistência à extinção; base de slot/loot box) — P03
  <https://www.explorepsychology.com/variable-ratio-schedule/>
- Montiel et al. (2022), PMC — Loot boxes ↔ jogo problemático (correlacional) — P03
  <https://pmc.ncbi.nlm.nih.gov/articles/PMC8794181/>
- Duolingo Blog — Improving the streak (A/B: +3,3% retenção D14, +1% DAU) — P03
  <https://blog.duolingo.com/improving-the-streak/>
- Yu-kai Chou — Streak design (perdoar > punir; notificação de culpa = dark pattern) — P03
  <https://yukaichou.com/gamification-study/master-the-art-of-streak-design-for-short-term-engagement-and-long-term-success/>

## Design tokens / CSS

- W3C — CSS Custom Properties for Cascading Variables (computed-value time) — P04
  <https://www.w3.org/TR/css-variables-1/>
- MDN — Using CSS custom properties (cascading variables) — P04
  <https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_cascading_variables>
- Design Tokens (Substack) — Common mistakes in design tokens (token drift; não criar tokens demais cedo) — P04
  <https://designtokens.substack.com/p/common-mistakes-in-design-tokens>
- NN/g — Lean design system teams (medir aderência; estados de interação consistentes) — P04
  <https://www.nngroup.com/articles/lean-design-system-teams/>
- CSS Wizardry — CSS and network performance (nunca encadear `@import`; `<link>` paralelos) — P04
  <https://csswizardry.com/2018/11/css-and-network-performance/>
- DebugBear — Avoid CSS `@import` (FCP P80 mobile 2782→1872ms, ~33%) — P04
  <https://www.debugbear.com/blog/avoid-css-import>
- Netguru — Design token naming best practices (alias → token semântico único) — P04
  <https://www.netguru.com/blog/design-token-naming-best-practices>
- CSS-Tricks — CSS Cascade Layers (ITCSS ↔ `@layer`) — P04
  <https://css-tricks.com/css-cascade-layers/>
- caniuse — CSS Cascade Layers (Baseline mar/2022; conteúdo descartado sem suporte) — P04
  <https://caniuse.com/css-cascade-layers>
- CUBE CSS — metodologia de organização de CSS — P04
  <https://cube.fyi/>
- MDN Blog — Color palettes with CSS color-mix() (interpolar em OkLab, não sRGB) — P04
  <https://developer.mozilla.org/en-US/blog/color-palettes-css-color-mix/>
- caniuse — CSS `color-mix()` (Baseline 2023) — P04
  <https://caniuse.com/css-color-mix>

## Performance / Imagens

- MDN — Responsive images guide (`<picture>`/`srcset`/`sizes`) — P05
  <https://developer.mozilla.org/en-US/docs/Web/HTML/Guides/Responsive_images>
- HTTP Archive — Web Almanac 2025, Page Weight (mediana ~2,3–2,9MB; imagens ~37–40%) — P05
  <https://almanac.httparchive.org/en/2025/page-weight>
- web.dev — Optimize CLS (`width`/`height` ou `aspect-ratio` em toda `<img>`) — P05
  <https://web.dev/articles/optimize-cls>
- MDN — `<picture>` element (cadeia AVIF→WebP→PNG; PNG como fallback) — P05
  <https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/picture>
- caniuse — WebP (~96% com fallback) — P05
  <https://caniuse.com/webp>
- web.dev — LCP & lazy-loading (nunca `loading="lazy"` no above-the-fold) — P05
  <https://web.dev/articles/lcp-lazy-loading>
- web.dev — Fetch priority (`fetchpriority="high"` no LCP; Google Flights 2,6→1,9s) — P05
  <https://web.dev/articles/fetch-priority>
- web.dev — Defining Core Web Vitals thresholds (LCP≤2,5s, INP≤200ms, CLS≤0,1; p75 campo) — P05
  <https://web.dev/articles/defining-core-web-vitals-thresholds>
- CSS Wizardry — Cache-Control for civilians (cache em camadas; `immutable` só com fingerprint) — P05
  <https://csswizardry.com/2019/03/cache-control-for-civilians/>
