# Auditoria — Navegacao & Arquitetura da Informacao

Esta dimensao cobre como o jogador se move entre os 5 setores do jogo (Mapa, Inventario, Loja, Perfil, Ranking) e como esses destinos se reorganizam entre desktop e mobile. Hoje a navegacao vive inteira num `<nav class="navegacao topo-nav">` no topo (`app/views/layout/header.php:75-93`) que, no mobile, apenas reflui via `grid-template-areas` (`public/css/style.css:1172-1188`): nao ha barra inferior, drawer nem rail — os links viram itens minusculos espremidos ao lado da logo e dominados pelo card do heroi. Faltam estado ativo (`aria-current`), alvos de toque garantidos e separacao entre navegacao e acao. As recomendacoes abaixo se apoiam nas duas passadas de pesquisa em disco.

---

### 1. Mobile nao tem padrao de navegacao — o topo so reflui e encolhe
- **Descricao:** No mobile a navegacao nao muda de padrao; o `<nav class="navegacao topo-nav">` (`app/views/layout/header.php:75-93`) so e remanejado pelo grid (`public/css/style.css:1172-1188`, area `"marca nav"`) para o canto superior direito, com `flex-wrap: wrap` (`style.css:345`). O resultado sao 5+ links minusculos no topo, longe do polegar e abaixo do card do heroi que ocupa a faixa inteira (`grid-area: hud`). Nao existe barra inferior, drawer nem rail.
- **Impacto na UX:** Em telas pequenas os destinos ficam dificeis de acertar e de notar; navegar exige alcancar o topo (zona dificil para o polegar), o que atrita a tarefa mais repetida do jogo (trocar de setor). Em sessoes mobile isso reduz descoberta e velocidade de troca.
- **Evidencia / boa pratica:** [Fato validado] Material 3 manda barra inferior em janelas compact (<600dp) e navigation rail/tabs no desktop, "Never use the navigation rail and navigation bar simultaneously". [Fato validado] Estudo NN/g (179 participantes, re-validado 2024-2025): navegacao escondida/pouco visivel tem ">20% drop in discoverability", "21% increase" em dificuldade e e ">=39% slower" no desktop / "15% slower" no mobile vs. navegacao combinada/visivel. [Fato validado] Thumb-zone (Hoober, 1.333 observacoes; ~75% das interacoes pelo polegar): so ~1/3 inferior da tela e zona sem esforco — sustenta nav na faixa inferior, nao no topo.
- **Referencia:** [`pesquisa/01-navegacao-e-motion.md`](pesquisa/01-navegacao-e-motion.md) (A1, A4) · [`pesquisa/02-timing-motion-libs-casos.md`](pesquisa/02-timing-motion-libs-casos.md) (Alvo 4). URLs: <https://m3.material.io/components/navigation-bar/guidelines> · <https://www.nngroup.com/articles/hamburger-menus/>
- **Solucao proposta:** Extrair os 5 destinos para um partial unico (`app/views/layout/_nav.php`) e renderiza-los duas vezes via CSS, sem JS: no mobile uma barra inferior fixa central (`@media (max-width:760px){ .topo-nav{ position:fixed; left:0; right:0; bottom:0; justify-content:space-around } }` + `padding-bottom` no `body`/`.conteudo` para nao cobrir conteudo); no desktop manter o topo (ou virar rail a esquerda). Garantir que os dois padroes nunca aparecam juntos por breakpoint.
- **Prioridade:** Alta
- **Dificuldade:** Media
- **Impacto esperado:** Usabilidade e engajamento no mobile (publico mobile-first); ganho qualitativo de velocidade/descoberta — coerente com os numeros NN/g, mas medir antes/depois no proprio jogo.

---

### 2. Alvos de toque dos links nao garantem 44px
- **Descricao:** Os links da nav usam `padding: .4rem .72rem` sem `min-height`/`min-width` (`public/css/style.css:347-353`), o que produz alvos com bem menos de 44px de altura; somados ao `flex-wrap` e `gap: .25rem` (`style.css:345`) no mobile, ficam pequenos e colados. O unico controle com area generosa e a placa do heroi, nao a navegacao.
- **Impacto na UX:** Toques imprecisos e mistaps no celular, especialmente com o polegar; itens proximos aumentam erro de selecao. Penaliza justamente a acao mais frequente (trocar de setor).
- **Evidencia / boa pratica:** [Fato validado] WCAG 2.5.5 (AAA): alvo "at least 44 by 44 CSS pixels"; WCAG 2.2 SC 2.5.8 (AA) fixa o piso pratico em 24x24. [Boa prática] Lei de Fitts (forma Shannon `MT = a + b*log2(A/W + 1)`): alvos maiores e mais espacados reduzem tempo/erro — justifica 44-48px na faixa inferior. Nota honesta: 44px e AAA, nao "requisito legal"; o piso AA da web e 24px.
- **Referencia:** [`pesquisa/01-navegacao-e-motion.md`](pesquisa/01-navegacao-e-motion.md) (A5) · [`pesquisa/02-timing-motion-libs-casos.md`](pesquisa/02-timing-motion-libs-casos.md) (Alvo 4, Fitts). URLs: <https://www.w3.org/WAI/WCAG21/Understanding/target-size.html> · <https://lawsofux.com/fittss-law/>
- **Solucao proposta:** Em `.navegacao a` usar `display:inline-flex; align-items:center; justify-content:center; min-height:44px; min-width:44px` e espacamento >=8px entre itens na barra inferior. So CSS, zero deps.
- **Prioridade:** Alta
- **Dificuldade:** Baixa
- **Impacto esperado:** Usabilidade/acessibilidade direta no toque; menos erros de selecao no mobile.

---

### 3. A faixa de navegacao mistura acoes e passa de 3-5 destinos
- **Descricao:** Dentro do mesmo `<nav>` convivem o controle de som (`<button id="btnSom">` + slider de volume, `header.php:76-79`), os 5 setores (`header.php:81-85`), o "Painel" do mestre (`:88`) e "Sair"/logout (`:91`). Som e Sair sao acoes, nao secoes persistentes; com mestre logado a faixa pode chegar a ~8 itens interativos lado a lado.
- **Impacto na UX:** A barra deixa de comunicar "estes sao os lugares do jogo" e vira uma mistura de lugares + verbos; mais itens elevam o custo de decisao e, no mobile, espremem ainda mais os destinos. Colocar acao rara num slot nobre quebra a memoria muscular e gera toques errados.
- **Evidencia / boa pratica:** [Fato validado] Apple HIG: "Use a tab bar to support navigation, not to provide actions… use a toolbar instead." [Fato validado] Material 3: barra de navegacao serve "three to five destinations"; ">5 itens, don't use a navigation bar". [Consenso de mercado] Caso Spotify "Create" (2024): adicionar uma acao de baixa frequencia num slot primario da bottom-nav quebrou a memoria muscular, gerou mistaps e forcou a Spotify a lancar opcao para desabilitar o botao — regra: nunca deslocar destino frequente por acao rara. [Boa prática] Lei de Hick (`T = b*log2(n+1)`): menos opcoes = decisao mais rapida.
- **Referencia:** [`pesquisa/01-navegacao-e-motion.md`](pesquisa/01-navegacao-e-motion.md) (A2, A3) · [`pesquisa/02-timing-motion-libs-casos.md`](pesquisa/02-timing-motion-libs-casos.md) (Alvo 5 — Spotify; Alvo 4 — Hick). URLs: <https://developer.apple.com/design/human-interface-guidelines/tab-bars> · <https://www.creativebloq.com/web-design/ux-ui/spotifys-latest-ui-design-change-is-driving-people-crazy>
- **Solucao proposta:** Manter na barra/rail SO os 5 destinos. Tirar o controle de som para uma barra de acoes/ajustes (ou para o popup da ficha do heroi, que ja existe em `header.php:105-158`); mover "Sair" para dentro do Perfil ou do popup do heroi; "Painel" do mestre como entrada propria fora dos 5 slots (ex.: badge no HUD do mestre). Tudo em PHP/CSS, sem build.
- **Prioridade:** Alta
- **Dificuldade:** Media
- **Impacto esperado:** Usabilidade e clareza de IA; protege a memoria muscular da troca de setor (licao Spotify, qualitativa).

---

### 4. Sem indicacao de "voce esta aqui" (nem `aria-current`, nem estado ativo visual)
- **Descricao:** Os links da nav so tem estilo de `:hover` (`public/css/style.css:354-367`); o sublinhado dourado (`.navegacao a::after`) aparece apenas no hover e nunca marca o setor atual. Nao ha `aria-current` em nenhum item (confirmado por busca) nem classe de estado ativo aplicada conforme a rota.
- **Impacto na UX:** O jogador (e o leitor de tela) nao sabe em qual setor esta a partir da navegacao; perde-se a ancora de orientacao basica, mais critica ainda quando a barra for para o rodape no mobile.
- **Evidencia / boa pratica:** [Fato validado] MDN/W3C: marcar o item ativo com `aria-current="page"` ("the current page within a set of pages") alem do estilo visual, para leitores de tela. Em barras de links e `aria-current="page"` (nao `aria-selected`, que e de `role="tab"`).
- **Referencia:** [`pesquisa/01-navegacao-e-motion.md`](pesquisa/01-navegacao-e-motion.md) (A6). URLs: <https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Reference/Attributes/aria-current> · <https://www.w3.org/WAI/ARIA/apg/patterns/breadcrumb/>
- **Solucao proposta:** No partial da nav, comparar a rota atual com cada destino e emitir `aria-current="page"` no link ativo; estilizar `.navegacao a[aria-current="page"]` reaproveitando o sublinhado dourado (tornar o `::after` permanente para o ativo). Complemento de teclado: adicionar `:focus-visible` aos links da nav (hoje so existe em `.hud-placa-click`, `style.css:232`). Puro PHP+CSS.
- **Prioridade:** Media
- **Dificuldade:** Baixa
- **Impacto esperado:** Usabilidade e acessibilidade (orientacao + leitores de tela); efeito de orientacao perceptivel, dificil de quantificar isoladamente.

---

### 5. Ranking 100% global, sem recorte por cohort
- **Descricao:** O setor Ranking e global (todos os jogadores numa unica tabela), sem recorte por turma, liga ou periodo. Como destino de nav, ele compete em peso com os outros 4 setores, mas oferece uma comparacao em que a maioria fica no fim da lista.
- **Impacto na UX:** Ranking global tende a desmotivar quem nao esta no topo e a dar pouca renovacao competitiva; como item permanente da navegacao, entrega baixa recompensa para o jogador mediano. Engajamento competitivo costuma render mais em grupos pequenos e renovaveis.
- **Evidencia / boa pratica:** [Fato validado, com ressalva] O que faz a gamificacao do Duolingo funcionar e o desenho em camadas (habito -> progressao -> competicao), com ligas de ~30 alunos — cohorts pequenos, nao um ranking unico global. Ressalva honesta: os numeros de retencao associados sao dados agregados cross-app (plataforma de gamificacao), correlacionais e auto-reportados — a tese qualitativa (camadas; ligas ~30) e que tem suporte, nao cifras causais.
- **Referencia:** [`pesquisa/02-timing-motion-libs-casos.md`](pesquisa/02-timing-motion-libs-casos.md) (Alvo 5 — Duolingo, camadas/ligas). URL: <https://trophy.so/blog/duolingo-gamification-case-study>
- **Solucao proposta:** Adicionar recorte server-side ao controller de ranking: visao padrao "Sua liga/turma" (cohort de ~20-30) e periodo (semana), com aba opcional "Global". Implementavel com filtro no SQL (turma_id / faixa de nivel / janela temporal) e troca de view, sem novas deps. Tratar quaisquer numeros de retencao como direcionais, nao garantidos.
- **Prioridade:** Media
- **Dificuldade:** Media
- **Impacto esperado:** Engajamento/retencao competitiva (qualitativo); evidencia de gamificacao e majoritariamente correlacional — validar com os proprios jogadores.
