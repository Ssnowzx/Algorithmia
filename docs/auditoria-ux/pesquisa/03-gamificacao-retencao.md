# Pesquisa — Passada 3: Gamificação & Retenção a serviço do aprendizado

> **Parte da auditoria de produto do Algorithmia.** Método: workflow próprio de _deep research_
> (5 pesquisadores em paralelo → **verificação adversarial 3-votos** com lentes [fonte-primária /
> **causalidade** / supergeneralização] → síntese citada). **39 afirmações → 21 confirmadas, 8
> refutadas/inconclusivas, 10 não-falsificáveis.**
>
> **Convenção de rótulos.** `[Fato validado]` = evidência experimental (causal); `[Consenso de mercado]`
> = convergência de fontes/líderes; `[Boa prática]`/`[Hipótese]`/`[Opinião]` = não-falsificável ou
> recomendação de design. Onde a evidência é **correlacional (não causal)**, está marcado explicitamente.
> Nenhuma afirmação sem fonte.
>
> **Mecânicas existentes que este relatório decide como evoluir:** XP/níveis (satura ~nível 10), ~17
> conquistas (várias sem gatilho), loja com ouro, eixo **Reputação −100..+100 (Disciplina vs IA)** hoje
> **sem feedback visual**, estrelas por fase (1–3), ranking/leaderboard. E se vale **introduzir streaks**.

---

## Eixo 1 — Progressão & senso de avanço

**1.1 Goal-gradient (gradiente de meta). [Fato validado — CAUSAL]** Kivetz, Urminsky & Zheng (2006),
_JMR_ 43(1):39–58: cartão de café de 12 selos com 2 pré-preenchidos (= mesmas 10 compras reais)
completado em **12,7 vs 15,6 dias (~18% mais rápido)**; intervalo entre compras encurta ~20% perto da
meta. Ressalva: a aceleração é causal; "quem acelerou reinscreve" é correlacional (auto-seleção).
Fontes: <https://www.chicagobooth.edu/review/going-goal> · <https://journals.sagepub.com/doi/abs/10.1509/jmkr.43.1.39>

**1.2 Endowed progress (progresso dotado). [Fato validado — CAUSAL]** Nunes & Drèze (2006), _JCR_
32(4):504–512: cartão "dotado" (10 selos, 2 já carimbados) → **34% de conclusão vs 19%** (cartão limpo
de 8), mesmas 8 visitas reais. Mecanismo: tarefa percebida como **já iniciada**.
Fontes: <https://academic.oup.com/jcr/article-pdf/32/4/504/17928623/32-4-504.pdf> · <https://www.coglode.com/nuggets/endowed-progress-effect>

**1.3 Streaks ↑ retenção — manchete correlacional, efeito causal real é pequeno. [Fato validado]** O
"7-day streak → 2,4× mais chance de voltar" do Duolingo é **correlacional**. O efeito **causal** (A/B):
desacoplar streak da meta diária deu **+3,3% retenção D14, +1% DAU, +10,5% usuários em streak**.
Fonte: <https://blog.duolingo.com/improving-the-streak/>

**1.4 Gamificação melhora aprendizagem: efeito pequeno-moderado e dependente de design. [Consenso de
mercado]** Sailer & Homner (2020), _Educ. Psych. Review_ 32: **g=0,49 cognitivo / 0,36 motivacional /
0,25 comportamental**; heterogeneidade enorme (I²~98,8%) → depende do **alinhamento mecânica↔pedagogia**.
Fontes: <https://link.springer.com/article/10.1007/s10648-019-09498-w> · <https://link.springer.com/article/10.1007/s11423-023-10337-7>

**1.5 Overjustification (sobre-justificação). [Fato validado — CAUSAL]** Recompensas extrínsecas
esperadas/contingentes corroem a motivação intrínseca (Lepper, Greene & Nisbett 1973; Deci, Koestner &
Ryan 1999). SDT (Ryan & Deci 2000): autonomia + competência + pertencimento sustentam a motivação.
Fontes: <https://www.structural-learning.com/post/overjustification-effect> · <https://selfdeterminationtheory.org/SDT/documents/2006_RyanRigbyPrzybylski_MandE.pdf>

**1.6 [Boa prática] Barras de progresso podem sair pela culatra** quando exigem alto investimento
inicial; melhor: vitórias rápidas no começo, segmentar, não mostrar "quanto falta" de forma esmagadora.
(Evidência de _surveys_ — generalizar com cautela.)
Fonte: <https://irrationallabs.com/blog/knowledge-cuts-both-ways-when-progress-bars-backfire/>

### ✅ Decisão p/ Algorithmia
1. **Barras de progresso sempre visíveis e mais salientes perto do fim** ("2 de 3 estrelas", "falta 1
   desafio") — gradiente de meta (1.1).
2. **Nunca iniciar trilhas/eixos em 0% cru** (1.2): creditar onboarding/primeiras estrelas/1º passo das
   conquistas. O eixo Reputação deve partir de "você já começou".
3. **Saturação de XP no nível 10 → maestria/prestígio horizontal** (rejogar p/ 3★, modos difíceis,
   "selos de mestre" por assunto), **não** grind exponencial. _(Caveat: tratar como design, não fato —
   ver R2.)_
4. **Consertar as ~17 conquistas órfãs** (gatilho ou remover) — metas quebradas violam 1.6.

**Porquê:** ganhos de saliência/enquadramento (1.1/1.2/1.6) não são recompensa material → reduzem o risco
de overjustification (1.5). **Riscos:** saliência virar pressão ("você está atrás") = dark pattern;
empilhar XP/ouro agrava 1.5 sem ganho de aprendizado (1.4).

---

## Eixo 2 — Streaks & formação de hábito

**2.1 A motivação do streak muda no tempo. [Fato validado — ressalva causal]** Duolingo: no início domina
conquista ("2→3 dias = +50%; 200→201 = +0,5%"), depois aversão à perda; animações de marco +1,7% D7,
dobrar freezes +0,38% DAU. **Ressalva:** linguagem de _rollout_ observacional, sem controle randomizado.
Fonte: <https://blog.duolingo.com/how-duolingo-streak-builds-habit/>

**2.2 O streak fecha um loop comportamental reconhecido. [Consenso de mercado]** Fogg (B=MAP), Duhigg
(cue→routine→reward), Eyal (Hooked). **Ressalva:** consenso de **design**, não lei causal; o streak é
recompensa **previsível/acumulativa** (motor = aversão à perda), **não** variável.
Fontes: <https://www.thebehavioralscientist.com/articles/fogg-behavior-model> · <https://blog.duolingo.com/how-duolingo-streak-builds-habit/>

**2.3 Risco pedagógico: recompensas extrínsecas podem corroer o interesse. [Fato validado]** Solução:
amarrar o streak ao **aprendizado** ("dia de prática"), não a pontos/ouro; recompensas
**informativas/de competência** tendem a **não** corroer.
Fonte: <https://nerdsip.com/blog/gamification-gone-wrong-when-streaks-become-the-point>

**2.4 [Hipótese — CORRELACIONAL] Streaks longos → ansiedade/burnout (U-invertido).** Lin, Mo & Wei
(_Frontiers in Public Health_, 2026), survey transversal/SEM, 350 universitários: ansiedade→depleção
(β=0,52)→burnout (β=0,60); alto risco = **21,2%**. **NÃO** estabelece causa; letramento digital amortece.
Fonte: <https://pmc.ncbi.nlm.nih.gov/articles/PMC12913498/>

**2.5 [Boa prática] A quebra do streak é o momento de maior churn; perdoar > punir.** Freeze/reparo
combate o "what-the-hell effect"; notificações de culpa/vergonha = dark pattern.
Fonte: <https://yukaichou.com/gamification-study/master-the-art-of-streak-design-for-short-term-engagement-and-long-term-success/>

### ✅ Decisão p/ Algorithmia
**Introduzir streak LEVE, OPCIONAL, ANCORADO EM APRENDIZADO — com cautela alta:** conta **"dia de
prática"** (≥1 desafio real), não minutos; **perdão por padrão** (freeze/reparo automático, sem perda
total, sem culpa); casar com a narrativa Disciplina vs IA; **opt-out** disponível.
**Riscos:** dark pattern (2.5), overjustification (2.3), ansiedade/burnout em ~1/5 (2.4), atribuir ganhos
correlacionais ao streak (2.1).

---

## Eixo 3 — Recompensas variáveis & ética do reforço

**3.1 Razão variável é a mais resistente à extinção. [Fato validado — CAUSAL em laboratório]** Ferster &
Skinner (1957) — base de slot machines/loot boxes/gacha. **Ressalva ética:** é o mesmo mecanismo que
torna o design viciante (Bélgica/Holanda classificaram loot boxes como jogo de azar).
Fonte: <https://www.explorepsychology.com/variable-ratio-schedule/>

**3.2 Recompensas tangíveis ESPERADAS ↓ motivação intrínseca; verbais NÃO. [Fato validado — CAUSAL]**
Deci, Koestner & Ryan (1999, 128 exp.): tangíveis esperadas minam; **verbais/inesperadas não — podem
aumentar**. Fonte: <https://journals.sagepub.com/doi/10.3102/00346543071001001>

**3.3 Três tipos de recompensa variável (Eyal); a "do self" é a mais defensável. [Consenso de mercado]**
_tribe/hunt/self_; a "self" coincide com feedback de competência (Deci & Ryan). **Ressalva forte:**
_Hooked_ é framework de **hábito/retenção**, não de aprendizado; o benefício do feedback de competência é
**condicional** (só quando informacional, não controlador, + autonomia).
Fonte: <https://www.mindtools.com/aapqtdb/the-hook-model-of-behavioral-design/>

**3.4 [Fato validado — porém CORRELACIONAL] Loot boxes ↔ jogo problemático.** Montiel et al. (2022,
PMC8794181): associação robusta, mas "All studies… cross-sectional design… do not allow… causal effects".
Fonte: <https://pmc.ncbi.nlm.nih.gov/articles/PMC8794181/>

**3.5 [Consenso de mercado] Efeito positivo mas modesto/instável; ganho real é cognitivo.** Sailer &
Homner g=0,49/0,36/0,25; Li, Ma & Shi (2023) g=0,822 mas ~95% heterogeneidade (ceticismo com números altos).
Fonte: <https://pmc.ncbi.nlm.nih.gov/articles/PMC10591086/>

**3.6 [Boa prática] Variável NÃO é universalmente superior à previsível.** "Google venceu **diminuindo** a
variabilidade." Núcleo pedagógico (acertou/errou, XP por domínio, progresso da fase) deve ser
**previsível**; aleatoriedade só na **camada de sabor**.
Fonte: <https://www.thebehavioralscientist.com/articles/an-incomplete-loop-a-review-of-nir-eyals-hooked>

### ✅ Decisão p/ Algorithmia
1. **Núcleo previsível, sabor variável (3.6):** feedback/XP/estrelas/Reputação 100% previsíveis e
   contingentes à competência; aleatoriedade só em **cosméticos/eventos** sem poder.
2. **Recompensar de forma informacional, não controladora (3.2):** "você dominou recursão" > "faça X p/
   ganhar Y ouro".
3. **Se variável, usar a "do self" (3.3),** informacional + com autonomia.
4. **Proibição explícita: nada de loot boxes/gacha/monetização/pay-to-win** (3.1, 3.4).

---

## Eixo 4 — Social, leaderboards & ligas

**4.1 Ligas do Duolingo funcionam por segmentar em pools pequenos "ganháveis". [Consenso de mercado]**
~20–30 por liga, reset semanal, 10 tiers c/ promoção/rebaixamento. 3 mecanismos: competição **ganhável**,
aversão à perda (rebaixamento), reset semanal como passo de escalada. (Design replicável, não claim causal.)
Fontes: <https://duolingo.deconstructoroffun.com/mechanics/leagues> · <https://blog.duolingo.com/duolingo-leagues-leaderboards/>

**4.2 Gamificação tem efeito causal pequeno-moderado, mas os estudos NÃO isolam o leaderboard. [Fato
validado]** Sailer & Homner (pacote combinado, não leaderboard sozinho).
Fonte: <https://link.springer.com/article/10.1007/s10648-019-09498-w>

**4.3 Competição em equipe/cooperação apoia o pertencimento. [Consenso de mercado]** Dindar et al. (2021,
_BJET_); competição individual pode prejudicar a conexão social dos de baixo desempenho — times preservam
o incentivo sem expor o mais fraco. Fonte: <https://bera-journals.onlinelibrary.wiley.com/doi/10.1111/bjet.12977>

**4.4 [Hipótese] Os números de impacto das Ligas não são causalmente atribuíveis a elas** (dados
agregados/observacionais). Fonte: <https://trophy.so/blog/duolingo-gamification-case-study>

**4.5 [Boa prática] Leaderboards relativos/segmentados motivam os ~90% do meio/base** ("5/10 ok;
1005/1010 devastador"); mostrar **delta/subida**, não posição crua.
Fonte: <https://yukaichou.com/advanced-gamification/how-to-design-effective-leaderboards-boosting-motivation-and-engagement/>

**4.6 [Boa prática] Rankings mal calibrados podem deslocar a motivação intrínseca (SDT).** Mitigar:
competição ganhável/segmentada, foco em progresso pessoal, opt-out.
Fonte: <https://cluelabs.com/blog/the-psychological-impact-of-leaderboards-in-elearning/>

### ✅ Decisão p/ Algorithmia
1. **Trocar o ranking global único por ligas de pares** (pools ~20–30, reset semanal, tiers) (4.1, 4.5).
2. **Nunca expor "o último":** mostrar **delta/melhora pessoal**, não posição absoluta (4.5, 4.6).
3. **Adicionar camada cooperativa** (guildas/duplas/objetivos coletivos) p/ _relatedness_ (4.3).
4. **Leaderboard como reforço de competência, com opt-out** (4.6).

---

## Eixo 5 — SDT & riscos de over-gamification

**5.1 Motivação intrínseca duradoura = autonomia + competência + relatedness (SDT). [Fato validado — com
ponte aplicada inflada]** Núcleo da SDT tem suporte experimental (Sheldon & Filak 2008, 2×2×2). **Ressalva:**
"gamificação só sustenta aprendizado quando alimenta as 3" infla causalidade (efeitos pequenos/instáveis,
impacto **mínimo** em competência). Tratar como design normativo.
Fontes: <https://en.wikipedia.org/wiki/Self-determination_theory> · <https://www.nngroup.com/articles/autonomy-relatedness-competence/>

**5.2 Recompensas tangíveis esperadas corroem; feedback verbal não. [Fato validado — CAUSAL]** Deci
(1971), Lepper et al. (1973), Deci/Koestner/Ryan (1999, ~d=−0,34). Mitigação: recompensar de forma
informacional. Fonte: <https://depts.washington.edu/techdocs/papers/deciExtrinsicRewardsAndIntrinsicMotivation99.pdf>

**5.3 Efeito real porém pequeno-moderado; maior em cognição. [Fato validado]** Sailer & Homner; moderadores
fortes: **ficção de jogo** e **combinar competição + colaboração**.
Fonte: <https://link.springer.com/article/10.1007/s10648-019-09498-w>

**5.4 Correlação ≠ causalidade; maioria dos "sucessos" é correlacional/efeito-novidade. [Consenso de
mercado]** Hamari, Koivisto & Sarsa (2014): "effects greatly dependent on context… and users". Medir
**desempenho real**, não engajamento. Fonte: <https://pmc.ncbi.nlm.nih.gov/articles/PMC10591086/>

**5.5 PBL (points-badges-leaderboards) e ranking global são os mais associados a efeitos negativos. [Fato
validado — base NÃO-causal]** Almeida et al. (2023, mapeamento + focus group). **Ressalva:** frequência de
relato + percepção auto-reportada, não medição causal; literatura experimental é **mista**.
Fonte: <https://arxiv.org/abs/2305.08346>

**5.6 [Opinião] "Pointsification": pontos/medalhas/rankings são a parte MENOS importante.** Ian Bogost
(_Gamification is Bullshit_, 2011) — "exploitationware". Conquistas sem gatilho e XP saturado no nível 10
são exatamente esses "incentivos vazios". Fonte: <https://bogost.com/writing/blog/gamification_is_bullshit/>

### ✅ Decisão p/ Algorithmia
1. **Dar feedback visual ao eixo Reputação (Disciplina vs IA), hoje invisível** — enquadrar como
   **competência/identidade informacional**, nunca punição. **Maior oportunidade perdida atual.**
2. **Converter recompensas controladoras em informacionais (5.2).**
3. **Consertar conquistas órfãs + estender progressão além do nível 10 com maestria (5.6).**
4. **Investir em ficção/narrativa + competição-com-colaboração (5.3)** (moderadores com melhor evidência).
5. **Medir sucesso por competência demonstrada, não tempo de tela (5.4).**

---

## Refutadas / Inconclusivas (transparência — rebaixadas na verificação adversarial; NÃO citar como fato)

- **R1** — "Mastery learning (Khan) > XP": RCTs reais, mas o motor causal é a **supervisão humana
  empacotada**, não o mecanismo de maestria; Algorithmia é auto-dirigido sem facilitador. (2/3)
- **R2** — "Saturação de XP morre no nível 10 → social/maestria": má-atribuição (Park et al. 2017, não
  Bauckhage), **observacional**, em **MMORPG comercial**; o preditor real no topo é **social**, não maestria. (3/3)
- **R3** — "Loss aversion (λ≈2,25) é o motor do streak": λ é de Tversky & Kahneman **1992** (não 1979);
  meta-análise revisa p/ λ≈**1,31**, restrita a risco/loteria, não streaks. (3/3)
- **R4** — "Otimizações de streak: CURR +21% / churn −40%" (Mazal): números são **agregados de 4 anos** de
  um programa inteiro, não do streak isolado. (3/3)
- **R5** — "Streak freeze reduz desistência ~40% (Sharif & Shu)": número real é de **passos em
  laboratório** (2019/2021), não app/aprendizado; superlativo não testado. (3/3)
- **R6** — "N-Effect: leaderboard global desmotiva": N-Effect (Garcia & Tor 2009) é causal em **tiro
  único**; extrapolar p/ ranking persistente é indevido; moderado por Social Comparison Orientation. (2/3)
- **R7** — "Estar no fundo desmotiva (Na & Han 2023)": o experimento causal achou o **oposto** (baixo-rank
  **aumentou** esforço, t=3,55); útil: **nenhum** grupo exibiu motivação intrínseca (argumento p/ não
  centrar no ranking). (2/3)
- **R8** — "85% citam streaks, só 35% confiantes (Shortt 2023)": os números **não existem no paper**
  (revisão metodológica); vêm de um Substack sem atribuição. Tese qualitativa (engajamento ≠ proficiência) ok. (3/3)

---

### Nota metodológica
**Causal** (RCT/manipulação): goal-gradient (1.1), endowed progress (1.2), razão variável em lab (3.1),
overjustification (1.5/3.2/5.2), núcleo da SDT (5.1). **Correlacional/observacional:** manchetes de
retenção do Duolingo (1.3/2.1/R4), loot boxes↔jogo problemático (3.4), burnout (2.4), efeitos negativos de
PBL (5.5). Cases de uma única empresa em escala massiva (Duolingo) são **sinais**, não evidência
transferível a um jogo de turma.

### Insumos diretos para o protótipo/auditoria
- **Reputação visível** (eixo Disciplina↔IA) como medidor de competência/identidade — **prioridade alta**.
- **Progresso saliente perto do fim** (estrelas/desafios) + **nunca começar em 0% cru**.
- **Ligas de pares** (pools ~20–30) com **delta pessoal**, substituindo ranking global; opt-out.
- **Streak opcional ancorado em prática** com perdão; sem culpa.
- **Conquistas com gatilho**; progressão por maestria após o nível 10. Núcleo previsível; zero loot box.
