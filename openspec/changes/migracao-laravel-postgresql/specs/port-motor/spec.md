## ADDED Requirements

### Requirement: Equivalência do motor com o legado

O motor portado SHALL reproduzir, número a número, o comportamento do
`BatalhaService` do jogo em PHP puro. Os valores esperados SHALL ser derivados das
constantes de balanceamento, e não capturados de um snapshot: uma mudança de regra
tem de aparecer no diff como decisão, não como atualização de baseline.

Divergência entre as duas implementações é bug do port — nunca licença para
rebalancear o jogo.

#### Scenario: O dano escala com o combo e satura no teto

- **WHEN** um mago de nível 1 sem equipamento acerta cinco desafios de dificuldade 1 seguidos
- **THEN** os danos são 17, 21, 26, 30 e 30
- **AND** o combo é 1, 2, 3, 4 e 4 — saturando em `combo_max`

#### Scenario: A batalha nunca termina por acabarem as perguntas

- **WHEN** o jogador responde além do número de perguntas sorteadas, sem que nenhum HP zere
- **THEN** a batalha continua, em morte súbita, reciclando perguntas
- **AND** a fúria cresce a cada rodada até o teto de 4,0

#### Scenario: Usar o Fragmento da IA cobra reputação e derruba as estrelas

- **WHEN** o jogador usa o Fragmento da IA Ancestral durante a batalha
- **THEN** o desafio é acertado automaticamente e causa o mesmo dano
- **AND** a reputação cai 10 pontos
- **AND** a fase é concluída com 1 estrela, por mais limpa que tenha sido a luta

#### Scenario: O gabarito nunca chega ao cliente

- **WHEN** o estado da batalha é serializado para a tela
- **THEN** o desafio atual vai sem `resposta` e sem `explicacao`
- **AND** a sequência inteira de desafios permanece no servidor

### Requirement: Idempotência da recompensa

A concessão de recompensa SHALL ser idempotente por chave no banco, e não por flag
de sessão. Cada batalha SHALL nascer com um identificador gerado no servidor, nunca
recebido do cliente.

Esta é a **única divergência deliberada** em relação ao legado, onde a guarda vive
em `$_SESSION['batalha']['recompensado']` — durando o que dura a sessão, e inútil
contra requisições concorrentes.

#### Scenario: Repetir a concessão da mesma batalha não credita de novo

- **WHEN** a recompensa da mesma batalha é concedida duas vezes
- **THEN** a segunda chamada é recusada pela chave de idempotência
- **AND** XP, ouro e reputação são creditados uma única vez
- **AND** no legado equivalente, a reputação duplicaria

#### Scenario: Rejogar a mesma fase gera uma batalha nova, recompensada

- **WHEN** o jogador vence a mesma fase numa segunda batalha
- **THEN** a recompensa é concedida de novo, pois a idempotência é por batalha, não por fase
- **AND** o drop de item não se repete, pois ele já está no inventário

### Requirement: Progresso de fase preserva as estrelas e a última partida

`ProgressoFase` SHALL acumular o melhor resultado **apenas nas estrelas**. Acertos,
erros e uso da IA SHALL refletir a última partida.

Rejogar uma fase sem recorrer ao Fragmento apaga a mancha da IA. Isso não é
descuido: é o que permite reconquistar "Puro de Coração" depois de ter cedido à
tentação. Redenção é regra do jogo.

#### Scenario: Rejogar limpo mantém as estrelas e apaga a mancha

- **WHEN** o jogador conclui uma fase usando a IA (1 estrela) e depois a rejoga sem erro e sem IA (3 estrelas)
- **THEN** o progresso registra 3 estrelas
- **AND** `usou_ia` volta a ser falso

### Requirement: Costuras do motor limitadas ao indispensável

O motor SHALL depender de abstração apenas onde a implementação concreta impediria
um teste de medir a aritmética do combate: **onde a batalha é guardada** e **como as
perguntas são sorteadas**. Tudo o mais é regra, e regra é testada diretamente.

#### Scenario: O motor é exercitado sem HTTP e sem sessão

- **WHEN** o motor recebe um repositório em memória e um sorteio de sequência fixa
- **THEN** uma batalha inteira pode ser jogada e medida sem levantar uma requisição
- **AND** o mesmo repositório em memória serve ao comando de smoke, em produção
