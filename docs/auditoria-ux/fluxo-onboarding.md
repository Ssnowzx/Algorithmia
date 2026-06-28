# Auditoria — Fluxo & Onboarding

Esta dimensão cobre os primeiros minutos e o "fio condutor" do jogador: cadastro/login → criar personagem (6 classes) → Prólogo (introduz a IA) → Mapa → Diálogo → Batalha → recompensas → Loja/Inventário/Perfil/Ranking → final (3 desfechos). Hoje o jogo **não credita nenhum progresso dotado** no início (Reputação nasce em `0` cru, rotulada "Aprendiz Neutro"), os **objetivos de cada fase não são explícitos**, o **senso de avanço fora da arena é fraco** e a **Reputação — o eixo central da narrativa (Disciplina vs IA)** — aparece só como um número minúsculo com tooltip, sem nunca ser explicada cedo. As recomendações abaixo se fundamentam em mecanismos com evidência **causal** (endowed progress, goal-gradient) e em feedback informacional de competência (SDT), evitando recompensas extrínsecas que corroem motivação.

---

### 1. Reputação (eixo central) é invisível e nunca explicada no início
- **Descrição:** A Reputação −100..+100 (Disciplina vs IA) governa diálogos, variantes e os 3 finais (`app/services/ReputacaoService.php`), mas para o jogador ela existe só como um ícone+número discreto no header (`app/views/layout/header.php:67-68` → `⚖️ 0`) e no perfil (`app/views/perfil/index.php:21-22`), com o significado escondido num `title` de hover. Não há barra/escala, os dois polos não são nomeados na tela, e o Prólogo introduz a IA sem ensinar que **as escolhas movem esse eixo**.
- **Impacto na UX:** O jogador não percebe que existe um sistema de alinhamento, não entende por que diálogos/finais mudam, e perde a principal alavanca de identidade ("eu sou Disciplina, não atalho"). O gancho narrativo mais forte do jogo fica mudo justamente quando deveria fisgar.
- **Evidência / boa prática:** A pesquisa marca isto como **"a maior oportunidade perdida atual"**: dar feedback visual à Reputação enquadrando-a como **competência/identidade informacional, nunca punição** [Boa prática / decisão de design ancorada em SDT]. Feedback de competência informacional (não controlador) tende a **não** corroer a motivação intrínseca e pode aumentá-la [Fato validado — CAUSAL, Deci/Koestner/Ryan 1999, ~128 experimentos].
- **Referência:** [pesquisa/03-gamificacao-retencao.md](pesquisa/03-gamificacao-retencao.md) — Eixo 5 (5.1, 5.2) e "Insumos diretos". URLs: <https://www.nngroup.com/articles/autonomy-relatedness-competence/> · <https://journals.sagepub.com/doi/10.3102/00346543071001001>
- **Solução proposta:** Renderizar (server-side, CSS puro, zero JS/deps) um **medidor de Reputação** com os dois polos rotulados (`⚖️ Disciplina` ↔ `🤖 Caminho da IA`), o `rotuloReputacao()` já existente (`app/core/helpers.php:440`) como legenda, e um marcador na posição atual. Introduzi-lo explicitamente no Prólogo ("toda escolha pende este eixo") e exibi-lo no header/perfil/mapa. Enquadrar sempre como identidade/competência ("Você está trilhando a Disciplina"), nunca como "você está perdendo pontos".
- **Prioridade:** Alta
- **Dificuldade:** Média
- **Impacto esperado:** Engajamento e clareza narrativa (qualitativo). O ganho de competência via feedback informacional tem suporte causal; a tradução para retenção neste jogo de turma é plausível mas não garantida.

---

### 2. Onboarding sem "progresso dotado" — tudo começa em 0% cru
- **Descrição:** Ao criar o personagem, nada é creditado: Reputação nasce em `0` (`database/schema.sql:75`, `config/config.php:129-130`), o que cai exatamente no rótulo neutro "Aprendiz Neutro" (`app/core/helpers.php:443`), comunicando "nada começou ainda". O Prólogo e a criação de herói não viram um primeiro marco visível (sem estrela, sem selo, sem "1º passo dado").
- **Impacto na UX:** A jornada é percebida como **não-iniciada** no momento mais frágil (antes da primeira vitória), reduzindo a probabilidade de o jogador seguir até a primeira batalha.
- **Evidência / boa prática:** **Endowed progress** [Fato validado — CAUSAL]: cartão "dotado" (10 selos, 2 já carimbados) teve **34% de conclusão vs 19%** de um cartão limpo de 8 selos, com as mesmas 8 visitas reais — porque a tarefa é percebida como **já iniciada** (Nunes & Drèze 2006, JCR). Decisão derivada na pesquisa: **"nunca iniciar trilhas/eixos em 0% cru… creditar onboarding / primeiras estrelas / 1º passo; o eixo Reputação deve partir de 'você já começou'."**
- **Referência:** [pesquisa/03-gamificacao-retencao.md](pesquisa/03-gamificacao-retencao.md) — Eixo 1 (1.2) e "Decisão p/ Algorithmia" #2. URLs: <https://academic.oup.com/jcr/article-pdf/32/4/504/17928623/32-4-504.pdf> · <https://www.coglode.com/nuggets/endowed-progress-effect>
- **Solução proposta:** No `PersonagemController` (pós-criação/Prólogo), creditar um **primeiro passo dotado**: iniciar a Reputação num pequeno positivo (ex.: `+8`, "você escolheu a Disciplina ao começar") em vez de `0`, conceder a primeira conquista "Primeiros Passos" e marcar o Prólogo como nó concluído (✓/estrela) no mapa. Tudo é dado de seed/insert simples (sem build). Manter as recompensas **informacionais** (não "ganhe X ouro"), evitando overjustification (pesquisa 1.5).
- **Prioridade:** Alta
- **Dificuldade:** Baixa
- **Impacto esperado:** Retenção do funil inicial (qualitativo). Efeito do mecanismo é causal em contexto de fidelidade/marketing; a transposição para o jogo é um sinal forte, não certeza.

---

### 3. Objetivos de cada fase não são claros
- **Descrição:** Os nós do mapa mostram nome, ícone do inimigo e uma "tática" no tooltip (`app/views/mapa/index.php`), e o diálogo termina num botão genérico "⚔️ Iniciar Batalha" (`app/views/historia/dialogo.php`), mas em nenhum ponto se diz **o que conta como sucesso na fase** nem **o que rende 2★/3★**. O jogador entra na batalha sem saber a meta.
- **Impacto na UX:** Sem meta explícita, o jogador não consegue se orientar nem decidir se vale rejogar; a sensação de propósito ("por que estou aqui?") enfraquece e o sistema de estrelas vira ruído.
- **Evidência / boa prática:** O **gradiente de meta** só atua quando a meta é percebível e segmentada [Fato validado — CAUSAL]; a pesquisa recomenda explicitar "2 de 3 estrelas", "falta 1 desafio" e **segmentar** em vez de mostrar uma massa de "quanto falta" [Boa prática]. Metas quebradas/obscuras violam a boa prática 1.6.
- **Referência:** [pesquisa/03-gamificacao-retencao.md](pesquisa/03-gamificacao-retencao.md) — Eixo 1 (1.1, 1.6) e "Decisão" #1. URLs: <https://www.chicagobooth.edu/review/going-goal> · <https://irrationallabs.com/blog/knowledge-cuts-both-ways-when-progress-bars-backfire/>
- **Solução proposta:** Adicionar um **objetivo de fase** explícito (campo `objetivo` na tabela de fases, ou texto derivado): linha no diálogo pré-batalha e no tooltip do nó, ex.: "Vença acertando ≥ X de Y" e "★★★: termine sem usar o Fragmento da IA". É render server-side puro a partir de dados que o `BatalhaService` já manipula; sem JS novo.
- **Prioridade:** Alta
- **Dificuldade:** Média
- **Impacto esperado:** Usabilidade e engajamento (qualitativo); clareza de meta é pré-condição para o goal-gradient funcionar.

---

### 4. Progresso pouco saliente, sobretudo perto do fim
- **Descrição:** O mapa exibe um total global ("X / Y fases concluídas · ★ N estrelas", `app/views/mapa/index.php:16-20`), mas no nó da fase as estrelas só aparecem **se já houver ≥1** (`app/views/mapa/index.php:97-101`) — uma fase aberta mostra "vazio" em vez de "0 de 3". Não há barra por região nem destaque quando falta pouco para completar a fase/região.
- **Impacto na UX:** O jogador não vê o "quase lá" que mais motiva, e o avanço fora da arena (o eixo desta dimensão: senso de progresso) fica frio.
- **Evidência / boa prática:** **Goal-gradient** [Fato validado — CAUSAL]: cartão de 12 selos com 2 pré-preenchidos foi completado em **12,7 vs 15,6 dias (~18% mais rápido)** e o intervalo entre ações encurta **~20% perto da meta** (Kivetz, Urminsky & Zheng 2006, JMR). Decisão da pesquisa: **"barras de progresso sempre visíveis e mais salientes perto do fim ('2 de 3 estrelas', 'falta 1 desafio')."** Caveat: não transformar saliência em pressão/"você está atrás" (dark pattern).
- **Referência:** [pesquisa/03-gamificacao-retencao.md](pesquisa/03-gamificacao-retencao.md) — Eixo 1 (1.1, 1.6) e "Decisão" #1. URLs: <https://journals.sagepub.com/doi/abs/10.1509/jmkr.43.1.39> · <https://www.chicagobooth.edu/review/going-goal>
- **Solução proposta:** Sempre renderizar `★★☆ (2/3)` no nó (mesmo com 0), adicionar uma **barra de progresso por região** (estrelas obtidas / máximas) e realçar visualmente (cor/glow CSS) o nó/região quando falta **1** estrela ou **1** fase — tudo CSS server-rendered, sem deps. Evitar linguagem de cobrança; usar tom de "quase lá".
- **Prioridade:** Alta
- **Dificuldade:** Baixa
- **Impacto esperado:** Engajamento e senso de progresso (qualitativo); aceleração perto da meta tem base causal, magnitude no jogo a validar.

---

### 5. Primeira vitória demora e a carga cognitiva inicial é alta
- **Descrição:** Antes da primeira vitória o jogador atravessa cadastro/login → tela de criação com **6 classes**, cada uma com stats e modal de lore (`app/views/auth/criar-personagem.php`) → Prólogo → Mapa → Diálogo → só então a 1ª batalha. Muita decisão e leitura antes de qualquer "ganhei".
- **Impacto na UX:** Excesso de escolha e texto no início aumenta o atrito e adia o reforço positivo; jogadores podem abandonar antes de provar o loop central (responder → vencer).
- **Evidência / boa prática:** A pesquisa recomenda **vitórias rápidas no começo** e **segmentar** a informação, em vez de exigir alto investimento inicial [Boa prática]; barras/decisões pesadas no início podem "sair pela culatra". Recompensa **verbal/informacional** logo na 1ª vitória não corrói motivação (pode aumentá-la) [Fato validado — CAUSAL].
- **Referência:** [pesquisa/03-gamificacao-retencao.md](pesquisa/03-gamificacao-retencao.md) — Eixo 1 (1.6) e Eixo 3 (3.2). URLs: <https://irrationallabs.com/blog/knowledge-cuts-both-ways-when-progress-bars-backfire/> · <https://journals.sagepub.com/doi/10.3102/00346543071001001>
- **Solução proposta:** Garantir que a **primeira fase seja uma vitória quase certa** (inimigo fraco/poucos desafios) e fechá-la com feedback verbal de competência ("Você dominou o primeiro conceito!"). Reduzir carga na criação: manter a classe recomendada pré-selecionada (já há `checked` no 1º card) e deixar lore/stats **colapsados por padrão** atrás do botão "Origem do povo" (já existente) — destacar visualmente um "Recomendada para começar". Sem novas deps; ajustes de view + dados de fase.
- **Prioridade:** Média
- **Dificuldade:** Média
- **Impacto esperado:** Retenção do funil inicial (qualitativo); "time-to-first-win" curto é boa prática consolidada, sem número causal direto neste contexto.

---

### 6. Falta de navegação persistente prejudica a continuidade da jornada
- **Descrição:** A jornada depende do Mapa como hub, mas os setores (Mapa, Inventário, Loja, Perfil, Ranking) não estão sempre visíveis numa navegação primária persistente, e a transição entre páginas é full-reload seco (PHP MPA). Isso enfraquece o "fio condutor" e o senso de onde se está/para onde ir.
- **Impacto na UX:** Navegação escondida/ausente reduz descoberta e atrasa o jogador; a falta de continuidade visual entre telas faz a jornada parecer fragmentada (recargas bruscas), reforçando o "senso de progresso fraco fora da arena".
- **Evidência / boa prática:** Navegação escondida **"is less discoverable"**, com **">20% drop in discoverability"**, **"21% increase"** em dificuldade e **"at least 39% slower"** no desktop / 15% no mobile vs. nav visível (NN/g, 179 participantes, re-validado 2024–2025) [Fato validado]. Material 3/Apple HIG: **3–5 destinos** numa barra inferior persistente (os 5 setores encaixam) com migração para rail no desktop, marcando o ativo com `aria-current="page"` [Fato validado]. View Transitions cross-document são **opt-in só-CSS, zero JS/deps**, degradando para navegação normal — ideal para PHP MPA [Fato validado].
- **Referência:** [pesquisa/01-navegacao-e-motion.md](pesquisa/01-navegacao-e-motion.md) — A1, A2, A4, A6 e B1/B2. URLs: <https://www.nngroup.com/articles/hamburger-menus/> · <https://developer.chrome.com/docs/web-platform/view-transitions/cross-document>
- **Solução proposta:** Adicionar uma **barra de navegação inferior persistente** (mobile) com os 5 setores, migrando para **rail à esquerda** no desktop por media query — HTML/CSS server-rendered, sem build —, marcando o item ativo com `aria-current="page"`. Habilitar continuidade tipo-SPA com `@view-transition { navigation: auto; }` como progressive enhancement puro-CSS (fallback = full reload). Nunca usar hambúrguer/aba "Mais" como navegação primária.
- **Prioridade:** Média
- **Dificuldade:** Média
- **Impacto esperado:** Usabilidade e continuidade da jornada (qualitativo); a perda por navegação escondida é quantificada em estudo controlado, mas o ganho exato no Algorithmia depende de implementação.
