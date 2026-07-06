# Glossario de Dominio

- **Jogador**: pessoa que consome o jogo e possui um personagem. No legado, e o usuario com papel `jogador`.
- **Usuario**: conta de autenticacao em `usuarios`. Pode ser jogador ou mestre.
- **Personagem**: avatar jogavel vinculado 1:1 ao usuario; guarda nivel, XP, HP, MP, ouro, reputacao e capitulo.
- **Mestre**: NPC principal de regiao e, em termos de acesso, papel administrativo `mestre` no legado.
- **Fase**: unidade de progressao da jornada; pode ser historia, licao, chefe, chefe final ou secundaria.
- **Desafio**: pergunta ou atividade de uma fase. Tem tipo, assunto, opcoes, resposta e explicacao.
- **Batalha**: fluxo de combate por turnos com sequencia de desafios, dano, combo e recompensas.
- **Turno**: interacao individual do combate; uma resposta do jogador ou uma resolucao automatizada via IA.
- **Combo**: acumulador de acertos consecutivos que aumenta o dano.
- **Especial**: ataque armado com MP que multiplica o proximo dano.
- **Poção**: item consumivel que cura HP/MP.
- **Fragmento da IA**: item consumivel de atalho/ajuda que responde o desafio atual em troca de penalidade de reputacao.
- **Reputacao**: medidor moral/narrativo do personagem; influencia variantes de dialogo e finais.
- **XP**: experiencia; sobe nivel e aumenta atributos maximos.
- **Ouro**: moeda do jogo usada para comprar itens.
- **Item**: objeto de inventario com tipo, raridade, preco e efeito.
- **Inventario**: conjunto de itens possuidos pelo personagem, com quantidade e estado de equipagem.
- **Conquista**: trofeu persistido quando o jogador satisfaz uma regra.
- **Progresso**: registro de conclusao de fase, estrelas e estatisticas de desempenho.
- **Resposta**: acao do jogador a um desafio; no banco aparece como historico em `respostas_log`.
- **Ranking**: ordenacao geral dos personagens por nivel e XP.
- **Painel mestre**: area administrativa do conteudo do jogo.
- **Conteudo**: perguntas, dialogos, fases, itens e conquistas do jogo.
- **Tenant**: instituicao ou contexto isolado na arquitetura futura. Nao existe no legado.
- **Instituicao**: entidade academica ou organizacional que tera isolamento proprio na nova arquitetura.
- **Turma**: agrupamento institucional futuro para dados de alunos.
- **Matricula**: vinculo entre usuario e instituicao/turma. Nao existe no legado.
- **Professor**: papel humano institucional futuro; no legado, a aproximacao mais proxima e o mestre.
- **Administrador institucional**: gestor de uma instituicao especifica na nova plataforma.
- **Administrador da plataforma**: gestor global do sistema, separado do contexto de tenant.

## Ambiguidades observadas
- **Mestre** e ambíguo entre NPC de regiao e papel administrativo. Isso aparece em `usuarios.papel = mestre` e em `mestres` como conteudo.
- **Reputacao** atua ao mesmo tempo como mecanismo narrativo e de progressao de fim.
- **Desafio** e tanto conteudo pedagogico quanto unidade mecanica do combate.
- **Conteudo** hoje mistura conteudo global e estado do jogador; a separacao ainda nao existe.

