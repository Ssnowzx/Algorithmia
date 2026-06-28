# Auditoria — Acessibilidade

Esta dimensão cobre operabilidade por teclado, leitores de tela, movimento reduzido, contraste e
estabilidade visual (CLS) do Algorithmia — um RPG educacional PHP MVC server-rendered, mobile+web,
sem build e com dependências mínimas. O estado atual tem boa base de motion responsivo, mas falhas
pontuais que excluem teclado/leitor de tela e violam recomendações WCAG: navegação sem estado
programático (`aria-current`), foco visível restrito a um único componente, `prefers-reduced-motion`
fragmentado em 12 blocos sem rede de segurança canônica, e imagens sem dimensões intrínsecas (CLS).

---

### 1. Foco visível (focus ring) ausente em quase todos os interativos
- **Descrição:** Só `.hud-placa-click` tem regra de foco (`public/css/style.css:232` — `:focus-visible { outline: 2px solid … }`). Os links da navegação (`.navegacao a`, `style.css:347`), os botões (`.botao`/`button.botao`, `style.css:488`), o controle de som (`.btn-som`, `style.css:335`) e os nós do mapa (`.no-bolha`, `public/css/mapa.css:192`) não definem `:focus-visible`. Como vários têm `border: 1px solid transparent` e dependem só de `:hover`, o usuário de teclado não enxerga onde está o foco.
- **Impacto na UX:** Quem navega por Tab (teclado, switch, leitor de tela com foco visual) fica "cego" dentro do menu e do mapa — não sabe qual fase ou aba está prestes a ativar, o que inviabiliza jogar sem mouse/toque preciso.
- **Evidência / boa prática:** A pesquisa ancora a doutrina de acessibilidade no W3C/WAI (alvos, `aria-current`) — a mesma família de critérios inclui foco operável. [Boa prática] Um anel de foco consistente e tokenizado (uma `:root` var de espessura/cor) é o padrão para apps server-rendered, alinhado ao chrome de marca já existente em `.hud-placa-click`.
- **Referência:** `pesquisa/01-navegacao-e-motion.md` (Domínio A, A5/A6 — ancoragem WCAG via W3C/WAI) · <https://www.w3.org/WAI/WCAG21/Understanding/focus-visible.html>
- **Solução proposta:** Criar um token (`--anel-foco: 0 0 0 2px var(--ouro), 0 0 0 4px rgba(0,0,0,.6)` ou `outline`) e uma regra global enxuta `:where(a, button, .botao, .no-bolha, [tabindex]):focus-visible { outline: 2px solid var(--ouro); outline-offset: 2px; }` em `style.css`, próximo da regra existente. Zero JS, 100% CSS.
- **Prioridade:** Alta
- **Dificuldade:** Baixa
- **Impacto esperado:** Usabilidade para teclado/leitor de tela; remove uma barreira de operabilidade hoje presente em toda a navegação.

---

### 2. `aria-current="page"` ausente no item de navegação ativo
- **Descrição:** A nav (`app/views/layout/header.php:80-92`) renderiza links planos (`<a href="…">Mapa</a>` etc.) sem nenhum `aria-current`. Um `grep -rn "aria-current" app public` não retorna nada — o estado "página atual" não é exposto nem visual nem programaticamente.
- **Impacto na UX:** Leitores de tela não anunciam em qual seção o jogador está; sem reforço visual de "ativo", também há ambiguidade para quem enxerga. O jogador perde a noção de localização dentro do app.
- **Evidência / boa prática:** [Fato validado] MDN: `aria-current` "indicates that this element represents the current item within a container"; valor `page` = "the current page within a set of pages". Para barras de **links** de navegação, `aria-current="page"` é o atributo correto (em `role="tab"` estrito seria `aria-selected`).
- **Referência:** `pesquisa/01-navegacao-e-motion.md` (A6, voto 3-0) · <https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Reference/Attributes/aria-current>
- **Solução proposta:** No `header.php`, comparar a rota atual com cada destino e emitir `aria-current="page"` no link correspondente (ex.: `<?= $rota==='mapa' ? 'aria-current="page"' : '' ?>`). Adicionar seletor CSS `.navegacao a[aria-current="page"]` reaproveitando o estilo de `:hover` (sublinhado/cor) para reforço visual. Puro PHP+CSS.
- **Prioridade:** Alta
- **Dificuldade:** Baixa
- **Impacto esperado:** Usabilidade e orientação; melhora a leitura por tecnologia assistiva e a clareza de "onde estou".

---

### 3. Alvos de toque abaixo do ideal de 44px na nav e em botões pequenos
- **Descrição:** Os links da nav (`style.css:347-352`) usam `padding: .4rem .72rem` com `font-size: .98rem` → altura efetiva ~28px; `.botao-sm` (`style.css:500`) tem `padding: .4rem .75rem`. Esses alvos ficam **acima** do piso AA (24px) mas **abaixo** do alvo confortável de 44px. Os nós do mapa (`.no-bolha svg`, `mapa.css:200` = 52px) já estão adequados.
- **Impacto na UX:** Em mobile (público mobile-first), alvos ~28px geram mistaps na faixa superior — agravado pela nav estar no **topo** (`topo-nav`), fora da zona confortável do polegar.
- **Evidência / boa prática:** [Fato validado] W3C SC 2.5.5 (AAA): alvo "at least 44 by 44 CSS pixels"; SC 2.5.8 (AA, WCAG 2.2) baixa o piso para 24×24px. ⚠️ Não tratar 44px como "requisito legal" — é AAA; o piso prático AA é 24px. [Fato validado] Geografia do polegar: ~1/3 inferior da tela é a zona sem esforço; polegares conduzem ~75% das interações.
- **Referência:** `pesquisa/01-navegacao-e-motion.md` (A5) + `pesquisa/02-timing-motion-libs-casos.md` (Alvo 4, thumb-zone) · <https://www.w3.org/WAI/WCAG22/Understanding/target-size-minimum.html>
- **Solução proposta:** Garantir `min-height: 44px` + `display: inline-flex; align-items: center` nos links da nav e em `.botao-sm`/`.btn-som`, sem mexer no `font-size`. Avaliar mover a nav para faixa inferior-central no mobile (média prioridade, escopo de navegação). CSS apenas.
- **Prioridade:** Alta
- **Dificuldade:** Baixa
- **Impacto esperado:** Usabilidade em toque; reduz erros de toque na navegação, especialmente em telas grandes.

---

### 4. `prefers-reduced-motion` fragmentado (12 blocos) sem rede de segurança canônica
- **Descrição:** Há 12 blocos `@media (prefers-reduced-motion: reduce)` espalhados (`style.css` ×4, `batalha.css` ×3, `mapa.css` ×4, `cena.css` ×1), cada um desligando animações específicas, **sem** um catch-all global. Um `grep "0.01ms"` não acha nada — qualquer animação nova nasce sem proteção. O `splashBotaoPulso`/`.splash-entrar-img` já está coberto (`style.css:1422-1424`), mas `itemLendario` (`style.css:1069`, `animation: itemLendario 2.2s … infinite`) é **auto-disparada, infinita e deixada ligada de propósito** (comentário em `style.css:1104-1106`).
- **Impacto na UX:** Para usuários com sensibilidade vestibular, depender de N blocos manuais é frágil: basta uma animação futura escapar para reintroduzir movimento. O pulso infinito do item lendário também pisca continuamente para quem pediu menos movimento.
- **Evidência / boa prática:** [Fato validado] Técnica C39: desligar movimento dentro de `@media (reduce)`. [Consenso de mercado] Desligar translação/escala (gatilhos vestibulares) e **manter fade/cor** (fora do escopo de 2.3.3). [Boa prática] Snippet catch-all com `animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important` — usa 0.01ms (não 0) e `iteration-count:1` para `animationend` ainda disparar. O pulso de `box-shadow` não é "motion animation" por 2.3.3, mas é auto-iniciado e infinito → território de SC 2.2.2.
- **Referência:** `pesquisa/02-timing-motion-libs-casos.md` (Alvo 2) · <https://www.w3.org/WAI/WCAG22/Techniques/css/C39> · <https://css-tricks.com/nuking-motion-with-prefers-reduced-motion/>
- **Solução proposta:** Adicionar **um** bloco canônico global no fim de `style.css` com o catch-all (0.01ms + iteration-count:1 + scroll-behavior:auto), preservando explicitamente fade/cor; incluir `.carta-loja.item-rar-lendario { animation: none !important; }` nesse bloco. Manter os blocos específicos só onde precisam ajustar (ex.: trocar slide por fade), evitando duplicação.
- **Prioridade:** Alta
- **Dificuldade:** Baixa
- **Impacto esperado:** Usabilidade/conforto para usuários sensíveis a movimento; torna o respeito à preferência robusto por padrão em vez de caso a caso.

---

### 5. Imagens sem `width`/`height` intrínsecos → layout shift (CLS)
- **Descrição:** Os helpers `svg()` (`app/core/helpers.php:89`) e `srcImagem()`-based `imagem()` (`helpers.php:118`) emitem `<img src … alt … loading>` **sem** atributos `width`/`height`. As dimensões vêm só do CSS (ex.: `style.css:1311-1317`), então o browser não reserva espaço antes de a imagem carregar — clássica fonte de CLS em telas com muitos sprites (mapa, loja, bestiário, arena).
- **Impacto na UX:** Conteúdo "pula" enquanto as artes carregam; em mobile/3G o jogador pode tocar no lugar errado (a fase/botão se desloca sob o dedo).
- **Evidência / boa prática:** [Fato validado] `width`+`height` (ou `aspect-ratio`) em TODA `<img>` reserva espaço e mata CLS (meta ≤0,1). [Fato validado] Imagem sem `width`/`height` é causa clássica de CLS; CWV avaliados no p75 de campo. As dimensões intrínsecas vão nos atributos; o CSS continua controlando a exibição.
- **Referência:** `pesquisa/05-arquitetura-performance.md` (Eixo 3 e Eixo 4) · <https://web.dev/articles/optimize-cls> · <https://web.dev/articles/defining-core-web-vitals-thresholds>
- **Solução proposta:** Evoluir os helpers para aceitar/derivar `width`/`height` (a maioria dos slots já tem tamanho fixo conhecido em CSS — passar como parâmetro ou ler do PNG via `getimagesize()` com cache). Emitir sempre os atributos intrínsecos mantendo o `object-fit: contain` existente. PHP puro, sem build.
- **Prioridade:** Média
- **Dificuldade:** Média
- **Impacto esperado:** Usabilidade/estabilidade visual e CWV; reduz mistaps causados por reflow durante o carregamento.

---

### 6. Contraste de cores de texto derivadas próximo do limite AA
- **Descrição:** `--texto-fraco: #9aa0c9` (`style.css:16`) é usado em textos pequenos (.68–.82rem) e em combinações que **reduzem** o contraste: `color-mix(... 65%, var(--texto-fraco))` (`style.css:209`, `:272`) e `opacity: .8` em `.btn-som.mudo` (`style.css:339`). O par base `#9aa0c9` sobre `--painel-2 #23274a` rende ~5,6:1 (passa AA para texto normal), mas as variantes color-mix/opacity caem em direção ao piso de 4,5:1.
- **Impacto na UX:** Rótulos secundários (stats da ficha, labels de formulário, estado "mudo") podem ficar ilegíveis para baixa visão ou em telas com brilho alto sob sol.
- **Evidência / boa prática:** [Hipótese] (contraste calculado por mim, não medido na pesquisa) o par base passa AA (~5,6:1), porém as derivadas precisam verificação caso a caso contra o mínimo 4,5:1 (texto normal) / 3:1 (texto grande) de WCAG SC 1.4.3 — a pesquisa ancora a acessibilidade no W3C/WAI mas não mediu cores específicas.
- **Referência:** `pesquisa/01-navegacao-e-motion.md` (ancoragem WCAG via W3C/WAI) · <https://www.w3.org/WAI/WCAG21/Understanding/contrast-minimum.html>
- **Solução proposta:** Auditar com ferramenta (axe/Lighthouse/WebAIM) cada uso de `--texto-fraco` com `color-mix`/`opacity`; onde reprovar, definir um token dedicado (ex.: `--texto-fraco-forte: #b6bce0`) para texto pequeno e evitar baixar opacidade de texto. Ajuste só de tokens, sem build.
- **Prioridade:** Média
- **Dificuldade:** Baixa
- **Impacto esperado:** Usabilidade/legibilidade para baixa visão; honesto que só uma medição confirma quais combinações reprovam.

---

### 7. `alt` genérico/redundante nas imagens
- **Descrição:** `svg()` emite `alt="<slug do arquivo>"` (`helpers.php:89`) e o helper de imagem usa `alt="<NOME_JOGO>"` (`helpers.php:118`). Slugs como `ui/icones/icone-ouro` ou o nome do jogo repetido em todo sprite não descrevem a função/conteúdo; ícones puramente decorativos não recebem `alt=""`.
- **Impacto na UX:** Leitores de tela leem caminhos de arquivo ou repetem "Algorithmia" dezenas de vezes (mapa/loja), poluindo a navegação por áudio; informação visual relevante (ex.: raridade do item, tipo de inimigo) não chega.
- **Evidência / boa prática:** [Boa prática] Imagens informativas precisam de `alt` significativo; decorativas devem ter `alt=""` para serem ignoradas. A pesquisa de performance trata as `<img>` como item central de otimização, mas não auditou o texto alternativo — fundamento aqui é a doutrina WAI de imagens.
- **Referência:** `pesquisa/05-arquitetura-performance.md` (Eixo 1 — `<img>` como elemento central) · <https://www.w3.org/WAI/tutorials/images/decision-tree/>
- **Solução proposta:** Adicionar parâmetro `alt` opcional aos helpers (`svg($slug, $classe, $attrs, $alt)`), passando descrição quando a imagem é informativa e `alt=""` (+ `role="presentation"`/`aria-hidden`) quando é decorativa. Migração incremental por tela; sem build.
- **Prioridade:** Média
- **Dificuldade:** Média
- **Impacto esperado:** Usabilidade para leitor de tela; honesto que o ganho é localizado a quem usa tecnologia assistiva, mas remove ruído real de áudio.
