# Auditoria — Gamificação & Retenção

Esta dimensão avalia como as mecânicas de progressão, recompensa e comparação social do Algorithmia
servem (ou atrapalham) o aprendizado. Hoje o jogo tem XP/níveis que saturam por volta do nível 10
(`config/config.php:91`, curva `100*(nivel-1)^1.5`), ~17 conquistas (parte sem gatilho real), loja de
ouro, estrelas por fase (1–3) e um ranking GLOBAL único. A mecânica pedagógica central — o eixo de
**Reputação −100..+100 (Disciplina vs IA)** — existe no código mas é exibida apenas como um número cru,
sem feedback visual de competência. Não há streaks. As recomendações abaixo priorizam saliência e
enquadramento informacional (efeitos causais, baratos) sobre empilhamento de recompensas materiais
(risco de overjustification), respeitando que muitos números de gamificação são correlacionais.

---

### 1. Reputação (Disciplina vs IA) invisível como competência/identidade
- **Descricao:** O eixo Reputação é a mecânica pedagógica central (`app/services/ReputacaoService.php`, faixa definida em `config/config.php:129-130`, `REPUTACAO_MIN -100`/`REPUTACAO_MAX 100`) e já tem rótulos de identidade ("Aprendiz Neutro" → "Mestre do Código Limpo", `app/core/helpers.php:440`). Mas na interface ele aparece só como número cru + emoji ⚖️/🤖 (`app/views/layout/header.php:67-68,117,137`; `app/views/perfil/index.php:21-22,50`). Não há medidor visual do espectro −100..+100 nem narrativa de avanço; o jogador não percebe que está construindo uma competência.
- **Impacto na UX:** A escolha mais significativa do jogo (resistir ao atalho da IA) não tem retorno sensorial: o jogador não sente que "virou" um Mestre do Código Limpo, e a decisão pedagógica perde peso identitário.
- **Evidencia / boa pratica:** [Fato validado] Feedback de **competência informacional** (não controlador) sustenta a motivação intrínseca (SDT — autonomia/competência/pertencimento); recompensas verbais/informativas não corroem o interesse, ao contrário das tangíveis esperadas (Deci, Koestner & Ryan 1999). O relatório aponta este eixo como **"a maior oportunidade perdida atual"** (Eixo 5, decisão 1).
- **Referencia:** `pesquisa/03-gamificacao-retencao.md` (5.1, 5.2, decisão Eixo 5). URLs: <https://www.nngroup.com/articles/autonomy-relatedness-competence/> · <https://depts.washington.edu/techdocs/papers/deciExtrinsicRewardsAndIntrinsicMotivation99.pdf>
- **Solucao proposta:** Renderizar um **medidor visual** do eixo (barra/gauge HTML+CSS de −100 a +100, sem build) no header e no perfil, com o `rotuloReputacao()` como **título de identidade** e marcadores dos cinco patamares. Enquadrar SEMPRE como competência conquistada ("Você se tornou Discípulo Dedicado"), nunca como punição ("você está negativo"). Microcopy informacional no momento da variação ("+5 por vencer sem o Fragmento da IA").
- **Prioridade:** Alta
- **Dificuldade:** Media
- **Impacto esperado:** Engajamento e aprendizado: torna saliente a competência-alvo do jogo; baixo risco de overjustification por ser feedback informacional, não recompensa material.

---

### 2. Progresso pouco saliente perto do fim e iniciado em 0% cru
- **Descricao:** As fases já calculam estrelas (`app/services/ProgressaoService.php`, `calcularEstrelas`) e há curva de XP, mas a interface não destaca o quão perto o jogador está de concluir (faltam quantos desafios, qual a 3ª estrela) nem credita um "primeiro passo" — trilhas e o próprio eixo Reputação começam visualmente em zero/neutro.
- **Impacto na UX:** Sem saliência do "falta pouco", o jogador desacelera perto da meta; e uma barra que começa crua em 0% é percebida como esforço ainda não iniciado, reduzindo a chance de conclusão.
- **Evidencia / boa pratica:** [Fato validado — CAUSAL] **Goal-gradient**: cartão com 2 selos pré-preenchidos (mesmas compras reais) foi completado **~18% mais rápido** (12,7 vs 15,6 dias) — Kivetz, Urminsky & Zheng (2006). [Fato validado — CAUSAL] **Endowed progress**: cartão "dotado" (2 de 10 já carimbados) elevou conclusão para **34% vs 19%** — Nunes & Drèze (2006). Caveat do relatório (1.6 [Boa prática]): barras podem sair pela culatra se exigirem alto investimento inicial — não exibir "quanto falta" de forma esmagadora.
- **Referencia:** `pesquisa/03-gamificacao-retencao.md` (1.1, 1.2, 1.6, decisão Eixo 1). URLs: <https://www.chicagobooth.edu/review/going-goal> · <https://www.coglode.com/nuggets/endowed-progress-effect>
- **Solucao proposta:** No fim de fase/capítulo, mostrar metas próximas de forma concreta ("2 de 3 estrelas", "falta 1 desafio para o capítulo"). **Nunca iniciar em 0% cru**: creditar o onboarding / a primeira estrela / o 1º passo de conquistas no servidor (PHP), de modo que a barra já apareça parcialmente preenchida. Aumentar a saliência só perto do fim, sem enquadrar como "você está atrás".
- **Prioridade:** Alta
- **Dificuldade:** Media
- **Impacto esperado:** Retenção e conclusão de fases (efeito causal); risco de dark pattern se a saliência virar pressão de comparação.

---

### 3. Conquistas órfãs: medalhas de mestre sem gatilho
- **Descricao:** O catálogo (`database/conquistas.sql`) define cinco conquistas por mestre — `mestre_cesar`, `mestre_willen`, `mestre_clayton`, `mestre_marcelo`, `mestre_cassandro` — mas nenhuma é concedida em lugar algum do código: os grants existentes cobrem só `primeiro_passo`/`sem_falhas`/`cacador_de_chefes`/`tentacao`/`arquivista_do_vazio`/`aprendiz_veterano`/`lenda_viva`/`colecionador` (`app/services/ConquistaService.php`), os `final_*` em `app/controllers/HistoriaController.php:121-123` e `puro_de_coracao` em `app/controllers/BatalhaController.php:274`. Restam metas impossíveis de obter.
- **Impacto na UX:** O jogador derrota um mestre e a medalha correspondente nunca dispara: meta visível porém inalcançável, que mina a credibilidade de todo o sistema de conquistas.
- **Evidencia / boa pratica:** [Boa prática] Metas quebradas violam o princípio de progresso confiável (1.6). [Opinião] Bogost ("pointsification"): conquistas sem gatilho e XP saturado são exatamente os "incentivos vazios" que tornam a gamificação superficial (5.6). O relatório lista "consertar as conquistas órfãs" como decisão direta (Eixo 1, item 4; Eixo 5, item 3).
- **Referencia:** `pesquisa/03-gamificacao-retencao.md` (1.6, 5.6). URLs: <https://irrationallabs.com/blog/knowledge-cuts-both-ways-when-progress-bars-backfire/> · <https://bogost.com/writing/blog/gamification_is_bullshit/>
- **Solucao proposta:** Ligar cada `mestre_*` no `ProgressaoService::atualizarCapitulo()`/`avaliarAposFase()` ao derrotar o chefe daquele mestre (`fase['mestre_id']` → `mestre['codigo']`), reaproveitando o helper idempotente `conceder()`. Conquistas que não fizerem sentido devem ser **removidas** do catálogo, não deixadas mortas. Quick win.
- **Prioridade:** Alta
- **Dificuldade:** Baixa
- **Impacto esperado:** Usabilidade e confiança no sistema de progressão; impacto direto e barato (correção de bug de design).

---

### 4. Ranking global único → ligas de pares com delta pessoal
- **Descricao:** O ranking é uma lista global absoluta dos top 50 por nível/XP (`app/models/Personagem.php:11-23`, `ORDER BY p.nivel DESC, p.xp DESC LIMIT 50`; `app/controllers/RankingController.php`). Exibe posição crua e expõe quem está embaixo; não há segmentação nem foco em melhora pessoal.
- **Impacto na UX:** Num ranking global, a maioria fica num meio "ininfluenciável" e os de baixo desempenho ficam expostos — o que tende a deslocar a motivação para comparação social em vez de domínio do conteúdo.
- **Evidencia / boa pratica:** [Consenso de mercado] Ligas funcionam por **pools pequenos "ganháveis"** (~20–30 por liga, reset semanal, tiers) (4.1). [Boa prática] Leaderboards segmentados que mostram **delta/subida pessoal** (não posição crua) motivam os ~90% do meio/base (4.5, 4.6). **Atenção (refutado):** o relatório derruba como fatos "ranking global desmotiva" (R6) e "estar no fundo desmotiva" (R7 — experimento achou o oposto); portanto trate ligas como **boa prática de design**, não como correção de um efeito causal comprovado.
- **Referencia:** `pesquisa/03-gamificacao-retencao.md` (4.1, 4.5, 4.6; refutadas R6/R7). URLs: <https://duolingo.deconstructoroffun.com/mechanics/leagues> · <https://yukaichou.com/advanced-gamification/how-to-design-effective-leaderboards-boosting-motivation-and-engagement/>
- **Solucao proposta:** Em PHP, agrupar jogadores em **pools de ~20–30** (atribuição por janela de inscrição/atividade), reset semanal e tiers de promoção/rebaixamento. No `RankingController`, calcular e exibir **delta pessoal** ("subiu 3 posições", "+120 XP esta semana") em vez de posição absoluta; **nunca destacar o último colocado**; oferecer **opt-out** do ranking.
- **Prioridade:** Media
- **Dificuldade:** Media
- **Impacto esperado:** Engajamento social sem expor o jogador fraco; honesto: ganhos das "ligas" do Duolingo são correlacionais (R6/R7/4.4), tratar como hipótese de design.

---

### 5. Maestria horizontal após o nível 10 (não grind exponencial)
- **Descricao:** A curva de XP (`config/config.php:91`, `100*(nivel-1)^1.5`) e os ganhos por nível saturam por volta do nível 10 (a conquista `lenda_viva` marca esse teto em `app/services/ConquistaService.php`). Concluída a campanha, não há mais eixo de avanço.
- **Impacto na UX:** O jogador veterano fica sem objetivo após o nível 10; o XP vira "incentivo vazio" sem aprendizado novo associado.
- **Evidencia / boa pratica:** [Consenso de mercado] Gamificação tem efeito **pequeno-moderado** e maior na **cognição** (Sailer & Homner: g=0,49 cognitivo) — o ganho real é cognitivo, não o número subindo (1.4). **Atenção (refutado):** a tese "saturação no nível 10 → social/maestria" foi rebaixada (R2: má-atribuição, observacional, MMORPG comercial; o preditor real no topo era **social**). Portanto, maestria horizontal aqui é **decisão de design**, não fato causal.
- **Referencia:** `pesquisa/03-gamificacao-retencao.md` (1.4, decisão Eixo 1 item 3; refutada R2). URLs: <https://link.springer.com/article/10.1007/s10648-019-09498-w> · <https://www.thebehavioralscientist.com/articles/an-incomplete-loop-a-review-of-nir-eyals-hooked>
- **Solucao proposta:** Em vez de grind exponencial, oferecer **maestria horizontal**: rejogar fases para 3★, modos difíceis, e "selos de mestre" por assunto (recompensa **informacional** de domínio, não mais ouro/XP). Reusa o sistema de estrelas (`ProgressaoService::calcularEstrelas`) e conquistas já existentes.
- **Prioridade:** Media
- **Dificuldade:** Media
- **Impacto esperado:** Engajamento de longo prazo dos veteranos; manter como hipótese de design (R2 refutou a base causal), medindo competência demonstrada e não tempo de tela.

---

### 6. Núcleo de recompensa previsível + sabor variável; zero loot box
- **Descricao:** A loja com ouro e os drops de fase introduzem recompensa material; hoje não há aleatoriedade pesada, mas também não há uma regra explícita protegendo o núcleo pedagógico contra futuras "caixas" ou recompensas controladoras ("faça X por Y ouro").
- **Impacto na UX:** Recompensas tangíveis esperadas tendem a corroer o interesse intrínseco no conteúdo; aleatoriedade em itens de poder cria padrão de reforço viciante e deslocaria o foco do aprender para o ganhar.
- **Evidencia / boa pratica:** [Boa prática] Variável **não** é universalmente superior à previsível — "Google venceu diminuindo a variabilidade" (3.6). [Fato validado — CAUSAL em lab] razão variável é a mais resistente à extinção, base de slot/loot box (3.1). [Fato validado — porém CORRELACIONAL] loot boxes ↔ jogo problemático (Montiel 2022, todos cross-sectional, sem causalidade) (3.4). [Fato validado — CAUSAL] recompensas tangíveis esperadas minam a motivação; verbais/inesperadas não (3.2).
- **Referencia:** `pesquisa/03-gamificacao-retencao.md` (3.1, 3.2, 3.4, 3.6, decisão Eixo 3). URLs: <https://www.explorepsychology.com/variable-ratio-schedule/> · <https://pmc.ncbi.nlm.nih.gov/articles/PMC8794181/>
- **Solucao proposta:** Fixar como regra de produto: **núcleo 100% previsível e contingente à competência** (acertou/errou, XP por domínio, estrelas, Reputação) e **aleatoriedade só na camada de sabor** (cosméticos/eventos sem poder). Converter microcopy de recompensa para informacional ("você dominou recursão" em vez de "faça X p/ ganhar ouro"). **Proibição explícita: nada de loot boxes/gacha/pay-to-win.**
- **Prioridade:** Media
- **Dificuldade:** Baixa
- **Impacto esperado:** Aprendizado e integridade ética (guardrail preventivo); evita overjustification que números altos de "engajamento" mascarariam.

---

### 7. Streak opcional ancorado em prática, com perdão (sem culpa)
- **Descricao:** O jogo não tem streaks. Há a tentação de adicioná-los para retenção, mas o relatório recomenda cautela alta e um desenho específico, casável com a narrativa Disciplina vs IA.
- **Impacto na UX:** Um streak mal desenhado (contar minutos, punir a quebra, notificar com culpa) vira dark pattern e fonte de ansiedade; bem desenhado, reforça o hábito de praticar.
- **Evidencia / boa pratica:** [Fato validado] O efeito causal do streak é **pequeno** (A/B do Duolingo ao desacoplar da meta diária: **+3,3% retenção D14, +1% DAU**) — a manchete "7-day → 2,4× voltar" é correlacional (1.3). [Fato validado] recompensas extrínsecas corroem; amarrar o streak ao **aprendizado** ("dia de prática"), não a pontos/ouro (2.3). [Boa prática] a quebra é o maior momento de churn; **perdoar > punir** (freeze/reparo); notificações de culpa = dark pattern (2.5). [Hipótese — CORRELACIONAL] streaks longos ↔ ansiedade/burnout, alto risco ~21,2% (não causal) (2.4). **Atenção (refutado):** não citar λ≈2,25 como motor (R3), nem "freeze reduz desistência ~40%" (R5).
- **Referencia:** `pesquisa/03-gamificacao-retencao.md` (1.3, 2.3, 2.4, 2.5, decisão Eixo 2; refutadas R3/R5). URLs: <https://blog.duolingo.com/improving-the-streak/> · <https://yukaichou.com/gamification-study/master-the-art-of-streak-design-for-short-term-engagement-and-long-term-success/>
- **Solucao proposta:** Se introduzido: streak **LEVE e OPCIONAL** que conta **"dia de prática"** (≥1 desafio real), com **perdão por padrão** (freeze/reparo automático, sem perda total nem culpa), casado com a narrativa Disciplina vs IA e com **opt-out** disponível. Implementável em PHP com uma coluna de data e contagem no servidor.
- **Prioridade:** Baixa
- **Dificuldade:** Media
- **Impacto esperado:** Retenção marginal (efeito causal pequeno, 1.3); risco real de over-gamification, ansiedade (~1/5) e overjustification — adotar só com perdão e opt-out, medindo prática real, não streak pelo streak.
