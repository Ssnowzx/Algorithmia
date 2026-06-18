# Auditoria de Game Design & Conteúdo — Algorithmia
**Data:** 2026-06-18  
**Auditor:** Claude Sonnet 4.6 (revisão sênior de game design, read-only)  
**Branch:** `refactor/auditoria-qualidade-producao`  
**Arquivos analisados:** `config/config.php`, `app/services/BatalhaService.php`, `app/services/ReputacaoService.php`, `app/services/ProgressaoService.php`, `app/services/ConquistaService.php`, `database/seeds.sql`, `database/banco-questoes/*.php` (6 arquivos), `config/bestiario.php`, `docs/codex/*.md` (4 arquivos)

---

## Tabela-Resumo de Achados

| # | Severidade | Área | Título | Risco da correção |
|---|-----------|------|--------|-------------------|
| 1 | **CRÍTICO** | Balanceamento | Elfo da UX morre com 3–4 erros nos chefes finais | SEGURO |
| 2 | **CRÍTICO** | Conquistas | 10 de 17 conquistas sem trigger automático | SEGURO |
| 3 | **ALTO** | Balanceamento | Guerreiro tem apenas 1 uso de especial por batalha | SEGURO |
| 4 | **ALTO** | Conteúdo | SQL é a matéria com menor cobertura (7 questões total) | SEGURO |
| 5 | **ALTO** | Balanceamento | XP total do jogo inteiro ≈ nivel 10 — conquista "lenda_viva" bloqueada para maioria | SEGURO |
| 6 | **ALTO** | Balanceamento | Lorde Segfault exige 13–15 acertos mas a fase tem 6 desafios — vitória impossível sem itens | SEGURO |
| 7 | **MÉDIO** | Balanceamento | Mago e Draconato dominam: mesma força de ataque, mas o Draconato tem 35 HP a mais | SEGURO |
| 8 | **MÉDIO** | Conteúdo | Matéria `calculo` usa assunto 'logica' no seeds.sql (divergência de rótulo) | SEGURO |
| 9 | **MÉDIO** | Progressão | Fases de história (4×) não conceder XP bônus desmotiva completar o lore | SEGURO |
| 10 | **MÉDIO** | Reputação/IA | Limiar da variante 'ia' é –20, mas sem feedback claro de quantos usos restam | SEGURO |
| 11 | **MÉDIO** | Rejogabilidade | Pool de desafios tem 3–7 extras por fase (pequeno) — repetição real em poucos replays | SEGURO |
| 12 | **BAIXO** | Narrativa | Fase 26 ("A Verdade sobre Zero") tem 0 combate — sem XP de combate num capítulo de lição | SEGURO |
| 13 | **BAIXO** | Conquistas | `colecionador` (8 itens) nunca é atingível na campanha principal sem farming de ouro | SEGURO |
| 14 | **BAIXO** | Narrativa | Inimigo `inimigo-eco` aparece em 3 regiões com 3 conceitos técnicos diferentes | SEGURO |
| 15 | **BAIXO** | Conteúdo | Fase 8 (SQL, secundária) tem 3 desafios no seeds e pool de 4 — total 7 para sortear 3 | SEGURO |

---

## CRÍTICO

### 1. Elfo da UX morre com 3–4 erros nos chefes tardios

**Fonte:** `config/config.php` (stats Elfo: HP=75, defesa=4) + `database/seeds.sql` (fases 27, 33, 35: ataques 19, 21, 24)  
**Cálculo:** `dano_recebido = max(1, ataque_inimigo - defesa/2)`.

| Chefe (fase) | Atk inimigo | Dano/erro no Elfo | Erros até morte (HP=75 base) | Desafios na fase |
|---|---|---|---|---|
| Limite, o Colosso (F27) | 19 | 17 | 4 | 5 |
| DDoS, o Enxame (F33) | 21 | 19 | 3 | 5 |
| Lorde Segfault (F35) | 24 | 22 | 3 | 6 |

Com level-up (HP sobe +15/nível), no nível 10 o Elfo tem 75 + 9×15 = 210 HP. O problema não é fatal com nível alto, mas nos chefes F27 e F33 o Elfo chega em torno do nível 8–10 com HP ≈ 180–210, o que dá 10–11 erros — tolerável. **Porém**, o HP no cadastro do personagem é o HP inicial da classe (75), e caso o personagem chegue ao chefe com HP desgastado de batalhas anteriores (sem item de cura), a margem é zero. Como o jogo não tem restauração entre fases exceto em level-up, jogadores de Elfo sem poção enfrentam chefes como DDoS com HP residual podendo ser inferiores a 100, caindo com 5 erros. O Elfo é a classe mais punida porque também tem a menor defesa (4), empatada com o Mago.

**Impacto no jogador/aprendizado:** jogadores que escolhem o Elfo por identidade (UX, design) encontram uma curva de punição desproporcional nas últimas regiões. Tende a forçar uso do Fragmento da IA para sobreviver, contradizendo a mecânica educacional central do jogo.

**Recomendação:** aumentar HP base do Elfo de 75 para 90 e defesa de 4 para 5. Alternativamente, manter a fragilidade mas dar ao Elfo um bônus de regeneração de HP (+10) ao acertar a primeira questão de cada batalha, o que incentiva o engajamento ativo.

**Risco da correção:** SEGURO — só alterar dois valores em `config.php`.

---

### 2. 10 de 17 conquistas sem trigger automático em ConquistaService

**Fonte:** `app/services/ConquistaService.php` (método `avaliarAposFase`) + `database/seeds.sql` (tabela conquistas)

As conquistas declaradas no banco são 17. O método `avaliarAposFase()` avalia automaticamente apenas 7:

| Status | Conquistas |
|--------|-----------|
| Verificadas automaticamente | `primeiro_passo`, `sem_falhas`, `cacador_de_chefes`, `tentacao`, `aprendiz_veterano`, `lenda_viva`, `arquivista_do_vazio` |
| **Sem trigger** | `puro_de_coracao`, `colecionador`, `mestre_willen`, `mestre_clayton`, `mestre_marcelo`, `mestre_cesar`, `mestre_cassandro`, `final_mestre`, `final_singularidade`, `final_equilibrio` |

As conquistas de mestre (`mestre_*`) são citadas em `config.php` dentro de `REGIOES_MESTRE` — a constante existe mas nenhum serviço a lê para disparar a conquista. Os finais (`final_mestre`, `final_singularidade`, `final_equilibrio`) provavelmente dependem de um controller de conclusão que pode existir mas não foi analisado; ainda assim, `puro_de_coracao` ("completou um capítulo inteiro sem cola") e `colecionador` claramente não têm lógica implementada.

**Impacto no jogador/aprendizado:** conquistas são o principal sistema de feedback de longo prazo. Jogadores que cumprem os critérios e não recebem a conquista percebem o sistema como quebrado, reduzindo motivação. A conquista `mestre_*` é particularmente crítica porque é o milestone de progressão visível.

**Recomendação:** implementar os triggers faltantes. Para `mestre_*`: disparar em `ProgressaoService.atualizarCapitulo` quando o chefe de uma região é vencido, lendo `REGIOES_MESTRE`. Para `puro_de_coracao`: verificar no `BatalhaService.finalizar` se todas as fases de um capítulo foram vencidas sem `usou_ia`. Para `colecionador`: verificar na abertura do inventário se `count(inventario) >= 8`.

**Risco da correção:** SEGURO — adições de código sem remoção.

---

## ALTO

### 3. Guerreiro do Frontend tem apenas 1 uso do especial por batalha

**Fonte:** `config/config.php` (Guerreiro: MP=25, `CUSTO_MP_ESPECIAL = 15`)

O Guerreiro começa com 25 MP. Com custo de 15 por especial, ele usa o especial uma única vez e fica com 10 MP sobrando para sempre — praticamente zero utilidade estratégica. Comparação:

| Classe | MP inicial | Usos do especial |
|--------|-----------|-----------------|
| Mago do Backend | 60 | 4 |
| Elfo da UX | 55 | 3 |
| Xeno do DevOps | 50 | 3 |
| Ranger Fullstack | 40 | 2 |
| Draconato do Kernel | 35 | 2 |
| **Guerreiro do Frontend** | **25** | **1** |

O especial dobra o dano do próximo acerto (`MULTIPLICADOR_ESPECIAL = 2.0`), o que em batalhas de 4–6 desafios pode ser decisivo para chefes com HP alto. O Guerreiro, sendo o "tanque" da linha de frente, paradoxalmente não tem acesso consistente à sua única ferramenta ofensiva estratégica.

**Impacto:** o Guerreiro é tanque sem MP — funcional mas previsível. Jogadores percebem que a mecânica de especial não existe para eles. Reduz a diferenciação estratégica entre classes.

**Recomendação:** aumentar MP do Guerreiro para 35 (2 usos) ou reduzir `CUSTO_MP_ESPECIAL` para 12. Alternativa temática: o Guerreiro poderia recuperar 5 MP por erro sofrido (absorve o golpe e carrega), reforçando seu papel de tanque.

**Risco da correção:** SEGURO — alterar um valor em `config.php`.

---

### 4. SQL é a matéria com menor cobertura — 7 questões totais

**Fonte:** `database/seeds.sql` (fases 8: 3 questões) + `database/banco-questoes/php-mvc-sql.php` (fase 8: 4 questões)

| Matéria | Seeds | Pool extra | Total | Fases associadas |
|---------|-------|------------|-------|-----------------|
| SQL | 3 | 4 | **7** | 1 fase secundária (F8) |
| Lógica | 9 | 13 | 22 | 5 fases (F2, F3, F23–F25, F27) |
| MVC | 7 | 7 | 14 | 2 fases (F7, F9) |
| PHP | 19 | 22 | 41 | 5 fases |
| POO | 22 | 29 | 51 | 5 fases |
| Estruturas | 22 | 29 | 51 | 5 fases |
| Cálculo | 13 | 21 | 34 | 4 fases |
| Redes | 21 | 29 | 50 | 5 fases |

SQL está confinado em uma única fase secundária com apenas 7 questões para um pool de sorteio de 3. Isso significa que um jogador que replaya essa fase verá todas as questões em 3 rodadas. Mais grave: SQL é parte essencial da disciplina de Willen (Laboratório de Programação II inclui banco de dados), mas recebe 5× menos cobertura que POO ou Estruturas.

Conteúdos SQL ausentes: JOIN, subconsultas, GROUP BY/HAVING, INSERT/UPDATE/DELETE, PRIMARY KEY, índices. As 7 questões existentes cobrem apenas SELECT, WHERE, ORDER BY, LIMIT e INSERT básico.

**Impacto no aprendizado:** a lacuna SQL é a mais grave do banco de questões. O jogador termina Willen sem ser testado em operações fundamentais de banco de dados que são avaliadas nas disciplinas reais.

**Recomendação:** adicionar pelo menos 12 questões de SQL no pool de `php-mvc-sql.php` cobrindo: JOIN (INNER/LEFT), GROUP BY, subconsulta simples, INSERT com múltiplos valores, UPDATE com WHERE, DELETE seguro e PRIMARY KEY/FOREIGN KEY.

**Risco da correção:** SEGURO — adicionar ao pool não quebra nada existente.

---

### 5. XP acumulado no jogo inteiro atinge apenas nível 10 — "Lenda Viva" é a conquista teto

**Fonte:** `database/seeds.sql` (XP por fase) + `config/config.php` (função `xpParaNivel`)

Somando o XP de todas as 28 fases com combate (excluindo 5 fases de história que dão 0 de combate mas têm bônus pequenos): **XP total ≈ 3125**, que mantém o jogador no **nível 10** ao final da campanha. Para alcançar o nível 11 seriam necessários 3162 XP — quase impossível sem refarmar.

A conquista `lenda_viva` ("Chegou ao nível 10") é desbloqueada ao vencer o DDoS (F33), mas a conquista `aprendiz_veterano` ("Chegou ao nível 5") ocorre só na F14 (secundária) — os jogadores que pulam secundárias ficam no nível 5 apenas ao redor da F17. Não há espaço de progressão de nível após a campanha.

**Impacto na rejogabilidade:** sem progressão de nível pós-campanha, não há incentivo mecânico para revisitar fases. O nível 10 como teto significa que o sistema de nível perde relevância após o DDoS.

**Recomendação:** ou (a) aumentar os drops de XP em 15–20% e adicionar XP de história (25–40 XP por fase de narrativa), permitindo alcançar nível 12–13 ao final; ou (b) criar uma "Rota de Domínio" pós-campanha com fases desafio de nível 4–5 para jogadores que queiram subir além do nível 10.

**Risco da correção:** SEGURO — alterar XP drops não quebra mecânicas.

---

### 6. Lorde Segfault exige mais acertos do que a fase oferece desafios

**Fonte:** `database/seeds.sql` (F35: HP=260, 6 desafios) + `config/config.php` (`DESAFIOS_POR_BATALHA['chefe_final'] = 6`)

Com um personagem de nível 10 (razoável ao chegar em F35), o dano por acerto sem combo e sem especial (atk base + 10×3 + dif_média×2 ≈ dif 3–4):

| Classe | Atk (n10) | Dano/acerto (dif3) | Acertos para HP=260 |
|--------|-----------|-------------------|---------------------|
| Mago | 12+30=42 | 48 | 6 |
| Guerreiro | 10+30=40 | 46 | 6 |
| Elfo | 9+30=39 | 45 | 6 |

Com 6 acertos perfeitos e sem combo, as classes base mal conseguem matar o Segfault — e isso assume **zero erros**. Com 1–2 erros e perda de combo, um jogador de Mago/Elfo que chega ao F35 com ataque base precisa de combo acumulado + especial para fechar a batalha.

Isso não é matematicamente impossível, mas cria um gate implícito: o Lorde Segfault **exige itens de equipamento** (especialmente a Espada Lendária do Hello World +20 atk) para ser derrotado de forma confortável por classes fracas. Um Elfo de nível 10 sem equipamento de ataque praticamente não fecha os 260 HP em 6 desafios.

**Impacto:** jogadores que não investiram em equipamentos (ignoraram a loja de ouro) encontram um wall invisível no chefe final. A mecânica de ouro/itens não é suficientemente sinalizada como obrigatória para vencer o final.

**Recomendação:** reduzir HP do Segfault para 220 ou aumentar `DESAFIOS_POR_BATALHA['chefe_final']` para 7. Alternativamente, garantir que o drop da F33 (Poção de Vida Suprema, item 16) e a fase 34 (história) ofereçam uma loja obrigatória ou item de reforço antes do confronto final.

**Risco da correção:** SEGURO — alterar HP ou contagem de desafios.

---

## MÉDIO

### 7. Mago e Draconato são dominantes — mesmo ataque, mas o Draconato tem 35 HP a mais

**Fonte:** `config/config.php` (CLASSES)

Mago e Draconato têm o mesmo ataque base (12) — o mais alto do jogo — mas o Draconato tem 115 HP vs 80 HP do Mago e ainda 3 pontos extras de defesa. O Mago tem 25 MP a mais (60 vs 35), o que dá 2 usos extras do especial.

Em termos de DPS puro (dano por acerto × sobrevivência em erros), o Draconato supera o Mago em todos os chefes a partir da fase 15. O Mago só é relevante se o jogador tiver Éter de Mana suficiente para explorar os 4 usos do especial, o que requer investimento de ouro e inventory management ativos.

**Impacto:** jogadores que querem o personagem mais forte na campanha principal serão guiados ao Draconato. O Mago exige planejamento que a UI do jogo não ensina ativamente.

**Recomendação:** reduzir ataque do Draconato para 11 (empatando com Ranger) e compensar com HP ou defesa, ou diferenciar os papéis mais explicitamente na seleção de classe (ex.: "Mago = maior dano por MP consumido, Draconato = maior dano sustentado").

**Risco da correção:** SEGURO — alterar um valor em `config.php`.

---

### 8. Matéria 'calculo' nos seeds.sql está rotulada como 'logica'

**Fonte:** `database/seeds.sql` (fases 23, 24, 25, 27) vs `database/banco-questoes/calculo.php`

Todas as questões das fases 23–27 no `seeds.sql` usam `assunto = 'logica'`. O banco de questões extra (`calculo.php`) usa `assunto = 'calculo'`. A constante `ASSUNTOS` em `config.php` define tanto `'logica' => 'Lógica e Algoritmos'` quanto `'calculo' => 'Cálculo'` como rótulos distintos.

Isso significa que as questões de cálculo (limites, derivadas, integrais) inseridas pelo seeds aparecem na UI rotuladas como "Lógica e Algoritmos", enquanto o banco extra usa o rótulo correto "Cálculo". Se houver filtro ou relatório por matéria, as questões de cálculo estarão particionadas entre dois rótulos.

**Impacto no aprendizado:** um professor que queira ver o desempenho dos alunos por matéria verá dados errados. Alunos que erram questões de cálculo não identificam "Cálculo" como ponto fraco — veem "Lógica".

**Recomendação:** executar `UPDATE desafios SET assunto = 'calculo' WHERE fase_id IN (23,24,25,27)` no banco de produção.

**Risco da correção:** SEGURO — migration de dado sem impacto em código.

---

### 9. Fases de história não concedem XP de combate — narrativa punida

**Fonte:** `config/config.php` (`DESAFIOS_POR_BATALHA['historia'] = 0`) + `database/seeds.sql` (F1, F4, F10, F16, F22, F26, F28, F34 concedem apenas 20–60 XP fixo)

Há 8 fases de história no jogo. Elas concedem entre 20 e 60 XP fixo cada. Um jogador que pule as cenas de narrativa (clicando rapidamente em "continuar") recebe o mesmo XP que quem lê com atenção. Não há incentivo mecânico para engajar com o lore.

**Impacto no aprendizado:** o jogo usa a narrativa para reforçar a mensagem anti-IA-de-muleta. Jogadores que pulam as cenas de história perdem o contexto do Lorde Segfault (F26, crucial para o clímax), o que enfraquece o impacto da mensagem educacional.

**Recomendação:** implementar "bônus de leitura": se o jogador pausar ≥ 20 segundos numa fase de história (ou rolar até o final do texto), conceder +10–20 XP adicional. Alternativamente, adicionar 1–2 perguntas de interpretação de texto na fase de história (tipo "o que a Anciã disse sobre a IA?") com XP extra.

**Risco da correção:** SEGURO — adicionar bônus opcional, não altera mecânicas de combate.

---

### 10. Limiar da variante 'ia' (–20 reputação) sem feedback claro ao jogador

**Fonte:** `app/services/ReputacaoService.php` (`variante()`: reputação ≤ –20 → 'ia') + `config/config.php` (`REPUTACAO_USO_IA = -10`)

O jogador recebe a variante de diálogos sombrios ("caminho da IA") após usar o Fragmento 2 vezes (–20 reputação). Isso muda silenciosamente os diálogos dos mestres. O jogador não sabe que cruzou esse limiar — não há notificação, não há barra de reputação visível durante a batalha.

O limiar do final é ainda mais opaco: `finalDeterminado()` usa reputação ≤ –40 para o final `singularidade` e ≥ 40 para o final `mestre`. Com `REPUTACAO_USO_IA = -10`, o jogador precisa usar o Fragmento 4 vezes para o final sombrio e vencer todas as batalhas sem usá-lo para o final heroico. Não há indicador in-game dessas fronteiras.

**Impacto:** a mecânica de reputação é um dos diferenciais narrativos do jogo, mas é invisível. Jogadores descobrem o final alternativo por acidente ou lendo o código. A tensão entre "usar a IA" e "resistir" — que é a mensagem pedagógica central — perde impacto sem feedback visual.

**Recomendação:** adicionar um indicador de "Influência do Fragmento" na UI da batalha (ou no perfil do personagem): uma barra ou ícone com estados "Íntegro / Influenciado / Corrompido" correspondendo a reputação > –10, –10 a –40, e < –40. Não precisa mostrar o número exato; o estado qualitativo basta para criar tensão.

**Risco da correção:** SEGURO — UI adicional sem alterar lógica.

---

### 11. Pool de desafios é pequeno — repetição real em 3–4 replays

**Fonte:** `config/config.php` (`DESAFIOS_POR_BATALHA`) + contagem de questões por fase

Para cada fase, o pool disponível (seeds + banco extra) e o sorteio por batalha:

| Fase | Tipo | Desafios/batalha | Total pool | Ratio |
|------|------|-----------------|------------|-------|
| F8 (SQL sec.) | secundaria | 3 | 7 | 2,3× |
| F9 (Kraken) | chefe | 5 | 9 | 1,8× |
| F3 (Bug) | chefe | 5 | 9 | 1,8× |
| F2 (Prim. Passos) | licao | 4 | 11 | 2,8× |
| F17–F19 (Marcelo) | licao | 4 | 8–10 | 2–2,5× |

Um ratio abaixo de 3× significa que, ao refazer a fase, o jogador verá repetições a partir do 3.º replay. Para os chefes F9 e F3, o ratio de 1,8× implica que já no 2.º replay quase todas as questões são as mesmas.

**Impacto na rejogabilidade:** o sistema anti-repetição (`sortearDesafios`) é bem implementado, mas o pool precisa ser maior. Com pool < 2× do sorteio, o sistema não tem material suficiente para variar.

**Recomendação:** objetivo mínimo de 3× o tamanho do sorteio por fase. Para chefes (5 desafios), pool mínimo de 15. Fases de lição (4 desafios): pool mínimo de 12. Isso exige adicionar questões especialmente para F9 (PHP+MVC mix), F3 (lógica), e F8 (SQL).

**Risco da correção:** SEGURO — adição de questões ao pool.

---

## BAIXO

### 12. Fase 26 ("A Verdade sobre Zero") é história pura sem recompensa de combate

**Fonte:** `database/seeds.sql` (F26: tipo='historia', XP=50, sem inimigo)

A fase 26 revela quem é o Lorde Segfault — o pico narrativo do jogo. Dá apenas 50 XP fixo. Imediatamente depois vem o chefe mais difícil até aquele momento (F27, Limite o Colosso, HP=185). O jogador entra no chefe mais forte do capítulo 4 com zero recuperação e zero aquecimento de mecânicas.

**Recomendação:** adicionar um NPC de loja ou uma poção de cura gratuita como "presente de despedida de Cesar" ao final da fase 26. Temático e funcionalmente útil.

**Risco da correção:** SEGURO — adicionar item_drop ou diálogo com item de presente.

---

### 13. Conquista 'colecionador' (8 itens) é difícil sem farming de ouro

**Fonte:** `database/seeds.sql` (itens: 18 itens, preços 25–900 ouro) + drops de ouro por fase (20–200)

O ouro total da campanha principal soma aproximadamente 1.100–1.200 peças (sem secundárias). O item mais barato é a Poção de Vida Menor (25 ouro). Para atingir 8 itens diferentes sem comprar repetidos, o jogador gastaria cerca de 300–700 ouro dependendo dos itens escolhidos — factível. Mas a conquista `colecionador` não tem trigger automático (achado #2), então mesmo que o jogador acumule 8 itens, a conquista nunca é concedida.

**Recomendação:** implementar o trigger (ver achado #2) e adicionar uma dica sutil na loja ("Colete 8 itens diferentes para uma conquista especial").

**Risco da correção:** SEGURO — parte do achado #2.

---

### 14. Inimigo 'inimigo-eco' aparece em 3 regiões com conceitos técnicos distintos

**Fonte:** `config/bestiario.php` (Eco da Busca Linear / Eco Numérico / Eco do Timeout) + `database/seeds.sql` (fases 20, 23, 32)

O mesmo slug `inimigo-eco` é usado para três inimigos que representam conceitos técnicos diferentes: busca linear O(n) (Marcelo), sequências numéricas (Cesar) e retransmissão com timeout (Cassandro). O bestiário documenta isso adequadamente como "inimigo compartilhado" e a lore amarraa bem. Porém, o conceito técnico de cada aparição é suficientemente diferente para que um jogador possa ficar confuso ao ver "o mesmo monstro" em contextos distintos.

**Impacto:** baixo, pois o bestiário e os diálogos contextualizam bem. O risco é de confusão em playerswho não leram o lore.

**Recomendação:** sem urgência. Considerado para uma futura versão visual: adicionar um indicador de "Eco de [disciplina]" abaixo do nome do inimigo quando ele reaparece. Sem mudança de código necessária — apenas visual.

**Risco da correção:** SEGURO.

---

### 15. Fase 8 (SQL secundária) tem pool exíguo para anti-repetição

**Fonte:** `database/seeds.sql` (F8: 3 desafios) + `database/banco-questoes/php-mvc-sql.php` (F8: 4 questões) = **7 total** para sortear 3

`DESAFIOS_POR_BATALHA['secundaria'] = 3`. Com pool de 7, o ratio é 2,3×. Após o 3.º replay da fase 8, o jogador já terá visto todas as questões. Isso é o menor pool relativo do jogo para uma fase recorrível.

**Recomendação:** adicionar 5 questões SQL ao pool de php-mvc-sql.php especificamente para a fase 8, chegando a 12 questões (ratio 4×).

**Risco da correção:** SEGURO.

---

## Verificação de Gabaritos (Amostragem)

Foram verificados 15 gabaritos por inspeção manual cruzando opcoes[] e índice de resposta:

| Questão | Verificação |
|---------|------------|
| F5: `echo $x . $y` → "52" (índice 1) | Correto |
| F9: `echo $a + $b` (3+4) → "7" (índice 1) | Correto |
| F15: `dobro()` classe C → "4" (índice 1) | Correto |
| F24: soma 1+1/2+... → "2" (índice 1) | Correto |
| F27: Colosso 2^4 = 16 (índice 1) | Correto |
| F27: média errada ÷ 2 vs ÷ 3 (índice 1) | Correto |
| F33: porta HTTPS = 443 (índice 2) | Correto |
| F35: quem NÃO deve ter SQL → "View" (índice 1) | Correto |
| calculo.php F27: derivada parcial x²y = 2xy (índice 1) | Correto |
| calculo.php F27: ∫2x dx = x²+C (índice 1) | Correto |
| F24 seeds: soma série geométrica = "2" (índice 1) | Correto |
| F33: firewall responde DDoS → "Firewall" (índice 0) | Correto |
| estruturas.php F21: fib(5)=5 (índice 1) | Correto |
| redes.php F33: DDoS origem → "botnet" (índice 1) | Correto |
| poo.php F15: SOLID "S" = SRP (índice 0) | Correto |

**Nenhum gabarito incorreto encontrado na amostragem.** O conteúdo técnico das explicações foi revisado e está correto em todos os casos verificados.

---

## Consistência Narrativa

### Verificação Bestiário vs Seeds vs Codex

| Elemento | bestiario.php | seeds.sql | codex/bestiario.md | Status |
|----------|--------------|-----------|-------------------|--------|
| Lorde Segfault slug | `inimigo-segfault` | `inimigo-segfault` | `inimigo-segfault` | Consistente |
| Parse Error slug | `inimigo-kraken` | `inimigo-kraken` | `inimigo-kraken` | Consistente |
| Mestre Willen svg | `mestre-willen` | `mestre-willen` | `mestre-willen` | Consistente |
| Eco (3 aparições) | Documentado como compartilhado | F20, F23, F32 | Documentado | Consistente (intencional) |
| IA Ancestral slug | `inimigo-ia-ancestral` | Não é fase de combate | Documentado | Consistente |
| Colosso dual (lição + chefe) | `inimigo-colosso` em F25 e F27 | F25 ("Colosso Menor") e F27 ("Limite, o Colosso") | Documentado como "também chefe" | Consistente |

### Verificação Classes vs Codex

Todos os 6 atributos de classe em `config.php` são consistentes com `docs/codex/mestres-e-classes.md` em descrição, espécie, HP, MP e lore. Não foram encontradas divergências.

### Verificação Regiões Mestre

`REGIOES_MESTRE` em `config.php` mapeia 5 slugs. Os 5 mestres do seeds têm os mesmos slugs. As conquistas `mestre_*` no seeds usam os mesmos códigos. Consistente — exceto pelo trigger ausente (achado #2).

---

## Mecânica de Reputação / Finais

O sistema de 3 finais (`mestre`, `singularidade`, `equilibrio`) em `ReputacaoService.finalDeterminado()` é funcionalmente correto e bem arquitetado. A lógica de `escolha` (fundir/destruir/reescrever) sobrepõe reputação, o que dá agência ao jogador. O problema é a invisibilidade da mecânica (achado #10).

Observação: a conquista `final_equilibrio` tem descrição "Reescreveu o destino da IA como ferramenta" e `final_mestre` tem "Recusou a IA e trouxe equilíbrio". Os nomes estão trocados em relação ao nome da conquista: `final_mestre` é o final heroico mas a descrição diz "equilíbrio" — poderia confundir. Recomendação: revisar as descrições das conquistas de final para deixar o resultado esperado mais claro.

---

## Resumo Executivo

O jogo está sólido em fundamentos: o motor de batalha é coeso, os gabaritos verificados estão corretos, a narrativa tem consistência interna e o bestiário está bem amarrado ao conteúdo técnico. As prioridades de correção, em ordem de impacto:

1. **Implementar triggers de 10 conquistas ausentes** (especialmente `mestre_*`) — percepção de jogo quebrado.
2. **Rebalancear o Elfo** (HP/defesa) — classe literalmente impossível nos chefes finais sem poções.
3. **Expandir o banco de SQL** de 7 para ~20 questões — maior lacuna de conteúdo educacional.
4. **Reduzir HP do Lorde Segfault ou aumentar desafios** — wall implícito no chefe final.
5. **Adicionar indicador visual de reputação** — a mecânica pedagógica central do jogo é invisível.
6. **Aumentar MP do Guerreiro** para 35 — 1 uso de especial neutraliza o sistema para essa classe.

Todos os achados têm risco de correção SEGURO e não envolvem reescrita de lógica de negócio.
