# Auditoria de Produto (UX) — Algorithmia

Sumário executivo da auditoria de **experiência de produto** do Algorithmia (RPG educacional
PHP MVC server-rendered, mobile + web, sem build e com dependências mínimas). A auditoria olha o jogo
pelos olhos do jogador — como ele navega, percebe progresso, sente o movimento da interface, entra no
jogo, usa-o por teclado/leitor de tela e quão rápido as telas carregam — e propõe melhorias **viáveis no
stack atual** (PHP + CSS/JS nativo, sem ferramentas de build).

> **Importante — escopo.** Esta auditoria **não duplica** a auditoria técnica existente em
> [`docs/auditoria/`](../auditoria/) (segurança, transações, arquitetura, débitos de código). Aqui o foco
> é **UX de produto**: navegação/IA, design system/UI, gamificação/retenção, motion/microinterações,
> fluxo/onboarding, acessibilidade e performance de entrega.
>
> **Importante — governança.** Nada descrito aqui é aplicado direto no jogo vivo. A implementação
> só ocorre **após aprovação do protótipo** (`../../prototipo/`). Este diretório é o plano; o protótipo é
> onde as mudanças são validadas antes de tocar a produção.

---

## Método

- **Deep research multi-fonte:** cinco passadas iterativas de pesquisa de nível consultoria
  (~500 agentes, ~12,7M tokens de subagente, ~3.700 buscas/fetches), cada passada indo a fundo num
  conjunto de domínios e alimentando lacunas para a próxima. Relatórios completos em
  [`pesquisa/`](pesquisa/README.md).
- **Verificação adversarial 3-votos:** cada afirmação factual passou por checagem cruzada por votação
  (3 votos) e foi **rotulada** por força de evidência — `Fato validado`, `Fato validado — CAUSAL`,
  `Consenso de mercado`, `Boa prática`, `Hipótese`, `Opinião`. Afirmações que não sobreviveram aparecem
  explicitamente como **refutadas** nos relatórios (transparência: ex. R2, R3, R5, R6, R7, R2.1, R5.1, R5.2).
- **Leitura honesta:** distinguimos onde há **evidência causal** (ex.: endowed progress, goal-gradient,
  Doherty/<400ms) de onde há apenas **correlação ou boa prática de design** (ex.: ligas, streaks, números
  de gamificação cross-app). Recomendações marcadas como direcionais devem ser **medidas no próprio jogo**.
- **Ancoragem no código:** cada achado aponta arquivo/linha reais do repositório como evidência.

---

## Régua de prioridade

A prioridade combina **impacto na experiência do jogador** com **alcance** (quantos jogadores/telas toca),
ponderada pela **força da evidência**. A dificuldade é registrada à parte (não rebaixa a prioridade — orienta
a ordem de execução).

- **Alta** — corrige uma barreira real, um bug de design ou destrava a maior alavanca de uma dimensão; afeta
  a maioria dos jogadores ou a tarefa mais frequente; evidência forte (fato validado/causal) **ou** pré-requisito
  barato que habilita as demais melhorias.
- **Média** — melhoria clara de usabilidade/polimento/manutenibilidade, mas de alcance menor, efeito
  qualitativo/indireto, ou apoiada em consenso de mercado e não em causalidade comprovada.
- **Baixa** — refinamento, guarda-corpo ou enhancement opcional; ganho marginal, ou mecânica de risco
  (ex.: streak) que só compensa com desenho cuidadoso e medição.

---

## Índice das dimensões

| # | Dimensão | Arquivo | Achados |
|---|----------|---------|---------|
| 1 | Navegação & Arquitetura da Informação | [navegacao-ia.md](navegacao-ia.md) | 5 |
| 2 | Design System & UI | [design-system-ui.md](design-system-ui.md) | 7 |
| 3 | Gamificação & Retenção | [gamificacao-retencao.md](gamificacao-retencao.md) | 7 |
| 4 | Motion & Microinterações | [motion-microinteracoes.md](motion-microinteracoes.md) | 7 |
| 5 | Fluxo & Onboarding | [fluxo-onboarding.md](fluxo-onboarding.md) | 6 |
| 6 | Acessibilidade | [acessibilidade.md](acessibilidade.md) | 7 |
| 7 | Performance & Entrega (imagens) | [performance-entrega.md](performance-entrega.md) | 7 |

**Documentos de apoio:**
- [roadmap-priorizado.md](roadmap-priorizado.md) — todos os 46 achados agrupados por prioridade, com ordem de execução sugerida.
- [referencias.md](referencias.md) — bibliografia (fontes primárias) agrupada por tema.
- [pesquisa/](pesquisa/README.md) — os 5 relatórios de deep research que embasam os achados.

---

## Os 5–7 achados de maior impacto

Selecionados por impacto × alcance × força da evidência (transversais às dimensões):

1. **Reputação (Disciplina vs IA) invisível como competência/identidade** — *Gamificação & Retenção / Fluxo & Onboarding.*
   O eixo pedagógico central existe no código mas aparece só como número cru; a pesquisa o chama de **"a maior
   oportunidade perdida atual"**. Render um medidor visual e enquadrá-lo como competência (feedback informacional,
   suporte causal via SDT) destrava o maior gancho de aprendizado e identidade do jogo.

2. **Mobile sem padrão de navegação — o topo só reflui e encolhe** — *Navegação & IA.*
   Não há barra inferior, drawer nem rail; os 5 destinos viram links minúsculos no topo, fora da zona do polegar.
   Navegação pouco visível tem queda de descoberta >20% e é ≥39% mais lenta (NN/g). Atinge a tarefa mais
   repetida do jogo no público mobile-first.

3. **Artes oversize servidas em slots minúsculos (right-size de imagens)** — *Performance & Entrega.*
   PNG/WebP de 1024–1536px pintados em 50–270px (desperdício de 5–30×). É a **maior alavanca de LCP e peso de
   página**, sentida no primeiro contato em mobile/3G — onde está a maioria.

4. **Onboarding sem "progresso dotado" + objetivos de fase obscuros + progresso pouco saliente** — *Fluxo & Onboarding / Gamificação.*
   Tudo começa em 0% cru e o jogador entra na batalha sem saber a meta nem o que rende 2★/3★. Endowed progress
   (34% vs 19%) e goal-gradient (~18% mais rápido) têm **evidência causal**; é retenção do funil inicial barata.

5. **Conquistas órfãs: medalhas de mestre sem gatilho** — *Gamificação & Retenção.*
   Cinco conquistas `mestre_*` definidas no catálogo e nunca concedidas — meta visível porém inalcançável, bug de
   design que mina a credibilidade de todo o sistema de progressão. Correção barata e direta.

6. **Acessibilidade de operação: foco visível + `aria-current` ausentes** — *Acessibilidade / Navegação & IA.*
   Apenas um componente tem `:focus-visible` e nenhum link de nav expõe a página atual. Quem joga por teclado/leitor
   de tela fica "cego" no menu e no mapa. Correção CSS+PHP de baixo risco que remove uma barreira de operabilidade.

7. **Dois namespaces de token com literais duplicados (consolidar fundação de cor)** — *Design System & UI.*
   `style.css` (`--bg`) e `tokens.css` (`--cor-bg`) repetem o mesmo valor literal — risco-mestre de "token drift"
   que sabota qualquer evolução de cor/tema. Aliasar é **zero-diff visual** e é o pré-requisito barato das demais
   melhorias visuais.
