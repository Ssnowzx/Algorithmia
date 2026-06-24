-- ============================================================================
-- Banco completo de desafios do host - IMPORT IDEMPOTENTE
-- Gerado a partir do banco de producao (fonte de verdade).
-- Nao editar a mao. Rodar de novo nao duplica: identidade logica = fase_id + pergunta.
-- ============================================================================

-- Tipos no host: multipla=269, vf=163, completar=148, ordenar=131, erro=130, arrastar=115

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m),
       'multipla', 'logica', 'Qual é o resultado de 2 + 3 * 4?', NULL, '[\"20\",\"14\",\"24\",\"9\"]', '1', 'A multiplicação tem precedência sobre a soma: 3*4 = 12, depois 12 + 2 = 14.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='Qual é o resultado de 2 + 3 * 4?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m),
       'vf', 'logica', 'A condição (5 > 3) é verdadeira?', NULL, NULL, 'true', 'Sim. 5 é maior que 3, portanto a comparação resulta em verdadeiro.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='A condição (5 > 3) é verdadeira?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m),
       'multipla', 'logica', 'Qual estrutura repete um bloco de comandos várias vezes?', NULL, '[\"if\",\"laço (loop)\",\"else\",\"return\"]', '1', 'Os laços (loops) repetem um bloco enquanto uma condição for satisfeita.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='Qual estrutura repete um bloco de comandos várias vezes?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m),
       'completar', 'logica', 'Para somar 1 ao valor da variável x, complete a expressão:', 'x = x ___ 1', NULL, '[\"+\"]', 'O operador + soma 1 ao valor atual de x, produzindo o incremento.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='Para somar 1 ao valor da variável x, complete a expressão:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m),
       'multipla', 'logica', 'Quanto vale 10 - 2 * 3?', NULL, '[\"24\",\"4\",\"18\",\"6\"]', '1', 'A multiplicação vem antes: 2*3 = 6, depois 10 - 6 = 4.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='Quanto vale 10 - 2 * 3?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m),
       'multipla', 'logica', 'O que é um algoritmo?', NULL, '[\"Um tipo de computador\",\"Uma sequência finita de passos para resolver um problema\",\"Uma linguagem de programação\",\"Um erro no código\"]', '1', 'Algoritmo é uma receita: passos finitos e bem definidos que levam a um resultado.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='O que é um algoritmo?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m),
       'vf', 'logica', 'Uma variável pode trocar de valor durante a execução do programa.', NULL, NULL, 'true', 'Sim — é justamente por isso que ela se chama \"variável\".', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='Uma variável pode trocar de valor durante a execução do programa.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m),
       'multipla', 'logica', 'Qual operador compara se dois valores são iguais?', NULL, '[\"=\",\"==\",\"=>\",\"+=\"]', '1', 'Um = atribui valor; == compara. Confundir os dois é o bug mais clássico do reino.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='Qual operador compara se dois valores são iguais?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m),
       'completar', 'logica', 'Complete o operador que verifica se a é MENOR que b:', 'se (a ___ b) entao ...', NULL, '[\"<\"]', 'O operador < testa se o valor da esquerda é menor que o da direita.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='Complete o operador que verifica se a é MENOR que b:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m),
       'vf', 'logica', 'Em muitas linguagens, o número 0 é tratado como \"falso\" numa condição.', NULL, NULL, 'true', 'Zero costuma valer \"falso\"; qualquer outro número, \"verdadeiro\".', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='Em muitas linguagens, o número 0 é tratado como \"falso\" numa condição.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m),
       'ordenar', 'logica', 'Ordene os passos para trocar uma lâmpada queimada:', NULL, '[\"Colocar a lâmpada nova\",\"Desligar o interruptor\",\"Remover a lâmpada queimada\"]', '[1,2,0]', 'Primeiro segurança (desligar), depois remover a velha e por fim instalar a nova.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='Ordene os passos para trocar uma lâmpada queimada:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m),
       'multipla', 'logica', 'Quantas vezes executa um laço que vai de i = 1 até 5 (inclusive)?', NULL, '[\"4\",\"5\",\"6\",\"infinito\"]', '1', 'De 1 a 5 inclusive são exatamente 5 repetições.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='Quantas vezes executa um laço que vai de i = 1 até 5 (inclusive)?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m),
       'vf', 'logica', 'Todo algoritmo deve, em algum momento, terminar.', NULL, NULL, 'true', 'Um algoritmo precisa ter fim. Se nunca termina, é um laço infinito — um defeito.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='Todo algoritmo deve, em algum momento, terminar.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m),
       'ordenar', 'logica', 'Ordene os passos para preparar um café:', NULL, '[\"Servir na xícara\",\"Ferver a água\",\"Pegar o pó\",\"Coar a bebida\"]', '[2,1,3,0]', 'A sequência lógica é: pegar o pó, ferver a água, coar e servir.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='Ordene os passos para preparar um café:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m),
       'multipla', 'logica', 'O que significa \"depurar\" (debugar) um programa?', NULL, '[\"Escrever a documentação\",\"Encontrar e corrigir erros\",\"Apagar o projeto\",\"Trocar de linguagem\"]', '1', 'Depurar é o processo de localizar e corrigir defeitos (bugs) no código.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='O que significa \"depurar\" (debugar) um programa?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m),
       'erro', 'logica', 'Qual linha cria um laço infinito?', '1: x = 10\n2: enquanto x > 5:\n3:    mostrar x\n4: fim', '[\"linha 1\",\"linha 2\",\"linha 3 — x nunca é alterado\",\"linha 4\"]', '2', 'Como x nunca diminui dentro do laço, a condição x > 5 nunca se torna falsa: laço infinito.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='Qual linha cria um laço infinito?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m),
       'multipla', 'logica', 'Quantas vezes executa um laço que vai de i = 0 enquanto i < 5?', NULL, '[\"4\",\"5\",\"6\",\"infinito\"]', '1', 'i assume 0, 1, 2, 3, 4 — cinco repetições antes de i < 5 ficar falso.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='Quantas vezes executa um laço que vai de i = 0 enquanto i < 5?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m),
       'multipla', 'logica', 'O que é a \"condição de parada\" de um laço?', NULL, '[\"O comando que inicia o laço\",\"O teste que, quando falha, encerra a repetição\",\"A primeira linha do programa\",\"Um tipo de variável\"]', '1', 'É o teste que o laço avalia a cada volta; quando ele fica falso, o laço para.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='O que é a \"condição de parada\" de um laço?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m),
       'vf', 'logica', 'Um fluxograma é uma forma visual de representar um algoritmo.', NULL, NULL, 'true', 'Sim: caixas e setas mostram o fluxo de decisões e ações.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='Um fluxograma é uma forma visual de representar um algoritmo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m),
       'erro', 'logica', 'Por que este laço nunca termina?', 'i = 0\nenquanto i < 3:\n    mostrar i', '[\"Falta mostrar o i\",\"i nunca é incrementado, então i < 3 é sempre verdadeiro\",\"O laço começa em 0\",\"Não há erro\"]', '1', 'Sem um i = i + 1 dentro do laço, a condição nunca fica falsa: laço infinito.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='Por que este laço nunca termina?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m),
       'completar', 'logica', 'Complete a estrutura que escolhe um caminho conforme uma condição:', '___ (idade >= 18) entao mostrar \"maior\"', NULL, '[\"se\",\"if\"]', 'A estrutura condicional (se / if) executa um bloco somente quando a condição é verdadeira.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='Complete a estrutura que escolhe um caminho conforme uma condição:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m),
       'ordenar', 'logica', 'Ordene as etapas de resolver um problema de programação:', NULL, '[\"Testar o resultado\",\"Entender o problema\",\"Escrever o código\",\"Planejar a solução\"]', '[1,3,2,0]', 'Entender → planejar → codificar → testar. Pular o \"entender\" é como debugar no escuro.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='Ordene as etapas de resolver um problema de programação:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 5,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=5) AS _m),
       'multipla', 'php', 'Em PHP, como se declara uma variável chamada nome?', NULL, '[\"var nome;\",\"$nome\",\"nome:\",\"let nome\"]', '1', 'Em PHP, toda variável começa com o cifrão: $nome.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=5 AND pergunta='Em PHP, como se declara uma variável chamada nome?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 5,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=5) AS _m),
       'completar', 'php', 'Complete para exibir um texto na tela em PHP:', '____ \"Olá, Algorithmia!\";', NULL, '[\"echo\",\"print\"]', 'echo (ou print) envia o texto para a saída em PHP.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=5 AND pergunta='Complete para exibir um texto na tela em PHP:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 5,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=5) AS _m),
       'multipla', 'php', 'Qual será a saída deste código?', '$x = 5;\n$y = 2;\necho $x . $y;', '[\"7\",\"52\",\"10\",\"Erro\"]', '1', 'O operador . concatena: \"5\" junto de \"2\" resulta na string \"52\".', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=5 AND pergunta='Qual será a saída deste código?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 5,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=5) AS _m),
       'vf', 'php', 'Em PHP, o operador de concatenação de strings é o ponto (.).', NULL, NULL, 'true', 'Correto: \"a\" . \"b\" resulta em \"ab\".', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=5 AND pergunta='Em PHP, o operador de concatenação de strings é o ponto (.).') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 5,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=5) AS _m),
       'multipla', 'php', 'Qual função mostra tipo e valor de uma variável para depuração?', NULL, '[\"echo\",\"var_dump\",\"print\",\"len\"]', '1', 'var_dump() revela tipo e conteúdo — o canivete da depuração em PHP.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=5 AND pergunta='Qual função mostra tipo e valor de uma variável para depuração?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 5,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=5) AS _m),
       'multipla', 'php', 'Qual a saída?', 'echo 7 % 3;', '[\"2\",\"1\",\"0\",\"21\"]', '1', 'O operador % devolve o RESTO da divisão: 7 dividido por 3 sobra 1.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=5 AND pergunta='Qual a saída?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 5,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=5) AS _m),
       'vf', 'php', 'Em PHP, uma string pode ser escrita com aspas simples ou duplas.', NULL, NULL, 'true', 'Ambas valem; nas aspas duplas, variáveis dentro da string são interpoladas.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=5 AND pergunta='Em PHP, uma string pode ser escrita com aspas simples ou duplas.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 5,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=5) AS _m),
       'multipla', 'php', 'Qual é um array associativo válido em PHP?', NULL, '[\"[1, 2, 3]\",\"[\'nome\' => \'Ana\', \'idade\' => 30]\",\"array<int>\",\"{nome: \\\"Ana\\\"}\"]', '1', 'O array associativo liga chaves a valores com =>: [\'nome\' => \'Ana\'].', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=5 AND pergunta='Qual é um array associativo válido em PHP?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 5,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=5) AS _m),
       'completar', 'php', 'Complete para juntar o texto com a variável $nome:', 'echo \"Olá, \" ___ $nome;', NULL, '[\".\"]', 'O ponto (.) concatena strings em PHP.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=5 AND pergunta='Complete para juntar o texto com a variável $nome:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m),
       'multipla', 'php', 'Qual valor de $n faz o bloco if ($n % 2 == 0) executar?', NULL, '[\"3\",\"7\",\"8\",\"5\"]', '2', '$n % 2 == 0 testa se o número é par; 8 é par, então a condição é verdadeira.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='Qual valor de $n faz o bloco if ($n % 2 == 0) executar?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m),
       'completar', 'php', 'Complete o laço que conta de 0 a 9:', 'for ($i = 0; $i ___ 10; $i++) { }', NULL, '[\"<\"]', 'A condição $i < 10 mantém o laço executando de 0 até 9.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='Complete o laço que conta de 0 a 9:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m),
       'erro', 'php', 'Qual linha está incorreta?', '1: if ($x > 0) {\n2:    echo \"positivo\"\n3: }', '[\"linha 1\",\"linha 2 — falta ponto e vírgula\",\"linha 3\",\"nenhuma\"]', '1', 'Toda instrução em PHP termina com ; — está faltando ao final da linha 2.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='Qual linha está incorreta?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m),
       'multipla', 'php', 'Qual estrutura escolhe entre vários casos fixos de uma variável?', NULL, '[\"for\",\"switch\",\"while\",\"foreach\"]', '1', 'switch compara uma variável contra vários valores possíveis (case).', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='Qual estrutura escolhe entre vários casos fixos de uma variável?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m),
       'multipla', 'php', 'Qual laço executa o bloco ao menos uma vez antes de testar a condição?', NULL, '[\"for\",\"while\",\"do...while\",\"foreach\"]', '2', 'do...while testa a condição no FIM, então o corpo roda pelo menos uma vez.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='Qual laço executa o bloco ao menos uma vez antes de testar a condição?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m),
       'completar', 'php', 'Complete o foreach que percorre cada item de $itens:', 'foreach ($itens ___ $item) { echo $item; }', NULL, '[\"as\"]', 'A sintaxe é foreach ($colecao as $item).', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='Complete o foreach que percorre cada item de $itens:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m),
       'multipla', 'php', 'Qual a saída?', '$x = 4;\necho ($x % 2 == 0) ? \"par\" : \"impar\";', '[\"par\",\"impar\",\"4\",\"Erro\"]', '0', 'O operador ternário: como 4 é par, imprime \"par\".', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='Qual a saída?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m),
       'multipla', 'php', 'Dentro de um switch, qual instrução encerra o case e evita o fall-through?', NULL, '[\"continue\",\"break\",\"stop\",\"exit\"]', '1', 'Sem break, a execução \"vaza\" para o próximo case. break encerra o bloco.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='Dentro de um switch, qual instrução encerra o case e evita o fall-through?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m),
       'vf', 'php', 'O operador && só resulta verdadeiro quando AMBOS os lados são verdadeiros.', NULL, NULL, 'true', 'É o E lógico: basta um lado falso para o todo ser falso.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='O operador && só resulta verdadeiro quando AMBOS os lados são verdadeiros.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m),
       'multipla', 'php', 'Quantas vezes \"oi\" é impresso?', 'for ($i = 0; $i < 3; $i++) { echo \"oi\"; }', '[\"2\",\"3\",\"4\",\"infinito\"]', '1', 'i vai de 0 a 2: três voltas, três \"oi\".', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='Quantas vezes \"oi\" é impresso?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'multipla', 'mvc', 'No padrão MVC, quem é responsável por acessar o banco de dados?', NULL, '[\"View\",\"Controller\",\"Model\",\"Router\"]', '2', 'O Model encapsula os dados e a comunicação com o banco.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='No padrão MVC, quem é responsável por acessar o banco de dados?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'multipla', 'mvc', 'Quem recebe a requisição, usa o Model e escolhe a View?', NULL, '[\"Controller\",\"Model\",\"View\",\"CSS\"]', '0', 'O Controller orquestra o fluxo: trata a requisição, chama o Model e seleciona a View.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Quem recebe a requisição, usa o Model e escolhe a View?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'vf', 'mvc', 'A View deve conter regras de negócio e comandos SQL.', NULL, NULL, 'false', 'Não. A View apenas apresenta os dados; a lógica e o SQL ficam no Controller e no Model.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='A View deve conter regras de negócio e comandos SQL.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'ordenar', 'mvc', 'Ordene o fluxo de uma requisição em MVC:', NULL, '[\"A View renderiza a resposta\",\"O Controller recebe a requisição\",\"O Model busca os dados\"]', '[1,2,0]', 'O Controller recebe a requisição, pede os dados ao Model e entrega à View para renderizar.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Ordene o fluxo de uma requisição em MVC:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'multipla', 'mvc', 'Willen abre o Portão das Camadas. Qual guardião do MVC representa a tela vista pelo jogador?', NULL, '[\"Model\", \"View\", \"Controller\", \"ORM\"]', '1', 'A View é a camada de apresentação visual, como uma página exibida ao usuário.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Willen abre o Portão das Camadas. Qual guardião do MVC representa a tela vista pelo jogador?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'multipla', 'mvc', 'O Espectro do Spaghetti tenta colocar regra de negócio dentro da tela. Em qual camada essa regra deve ficar?', NULL, '[\"View\", \"Controller\", \"Model\", \"CSS\"]', '2', 'O Model concentra dados e regras de negócio, evitando que a View vire um caldeirão de responsabilidades.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='O Espectro do Spaghetti tenta colocar regra de negócio dentro da tela. Em qual camada essa regra deve ficar?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'multipla', 'mvc', 'Quando o aprendiz clica em um botão e a missão precisa decidir o próximo caminho, qual camada coordena o fluxo?', NULL, '[\"Controller\", \"Banco de dados\", \"Arquivo de imagem\", \"Bootstrap\"]', '0', 'O Controller recebe a ação e decide como conduzir o fluxo da requisição.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Quando o aprendiz clica em um botão e a missão precisa decidir o próximo caminho, qual camada coordena o fluxo?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'multipla', 'mvc', 'No mapa de Willen, o navegador chama uma rota do reino. O que o roteamento ajuda a definir?', NULL, '[\"Qual URL leva a qual ação ou controlador\", \"Qual cor terá o botão\", \"Qual senha do banco será usada\", \"Qual personagem terá mais ouro\"]', '0', 'Roteamento liga um endereço ou caminho da aplicação à ação responsável por atendê-lo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='No mapa de Willen, o navegador chama uma rota do reino. O que o roteamento ajuda a definir?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'multipla', 'mvc', 'O ferreiro fala em ORM como uma ponte mágica. O que essa ponte faz?', NULL, '[\"Mapeia objetos do código para estruturas relacionais\", \"Desenha telas responsivas\", \"Substitui o navegador\", \"Apaga a necessidade de classes\"]', '0', 'ORM é mapeamento objeto-relacional: aproxima objetos da aplicação e dados relacionais.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='O ferreiro fala em ORM como uma ponte mágica. O que essa ponte faz?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'vf', 'mvc', 'No grimório de Willen, MVC separa Model, View e Controller para reduzir mistura de responsabilidades.', NULL, NULL, 'true', 'Essa separação é o objetivo central do padrão MVC.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='No grimório de Willen, MVC separa Model, View e Controller para reduzir mistura de responsabilidades.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'vf', 'mvc', 'A View deve concentrar consultas, regras de negócio e decisões de rota para simplificar o reino.', NULL, NULL, 'false', 'Isso cria acoplamento e aproxima o sistema do problema conhecido como spaghetti code.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='A View deve concentrar consultas, regras de negócio e decisões de rota para simplificar o reino.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'vf', 'mvc', 'O Controller pode receber ações do usuário e decidir se chama o Model ou retorna uma View.', NULL, NULL, 'true', 'O Controller coordena o fluxo entre interface e dados/regras.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='O Controller pode receber ações do usuário e decidir se chama o Model ou retorna uma View.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'vf', 'mvc', 'ORM significa mapeamento objeto-relacional.', NULL, NULL, 'true', 'ORM vem de Object-Relational Mapping, ou mapeamento objeto-relacional.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='ORM significa mapeamento objeto-relacional.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'vf', 'mvc', 'Roteamento e MVC são a mesma coisa: ambos significam somente criar tabelas no banco.', NULL, NULL, 'false', 'Roteamento liga caminhos a ações; MVC organiza responsabilidades em camadas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Roteamento e MVC são a mesma coisa: ambos significam somente criar tabelas no banco.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'completar', 'mvc', 'Complete a tríade sagrada de Willen: Model, View e _____.', NULL, NULL, '[\"Controller\", \"controller\"]', 'MVC significa Model, View e Controller.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Complete a tríade sagrada de Willen: Model, View e _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'completar', 'mvc', 'Na arquitetura MVC, a camada que apresenta a tela ao jogador é a _____.', NULL, NULL, '[\"View\", \"view\"]', 'A View é responsável pela apresentação.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Na arquitetura MVC, a camada que apresenta a tela ao jogador é a _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'completar', 'mvc', 'No MVC, a camada que representa dados e regras de negócio é o _____.', NULL, NULL, '[\"Model\", \"model\"]', 'O Model reúne dados, regras e operações relacionadas ao domínio.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='No MVC, a camada que representa dados e regras de negócio é o _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'completar', 'mvc', 'A ponte entre objetos e tabelas relacionais é conhecida pela sigla _____.', NULL, NULL, '[\"ORM\", \"orm\"]', 'ORM é o mapeamento objeto-relacional.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='A ponte entre objetos e tabelas relacionais é conhecida pela sigla _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'completar', 'mvc', 'O mapa que liga uma URL a uma ação do sistema é chamado de _____.', NULL, NULL, '[\"roteamento\", \"rota\", \"routing\"]', 'O roteamento define como caminhos da aplicação chegam aos controladores/ações.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='O mapa que liga uma URL a uma ação do sistema é chamado de _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'ordenar', 'mvc', 'Ordene a travessia correta de uma requisição no templo MVC.', NULL, '[\"Jogador aciona a tela\", \"Controller recebe a ação\", \"Model processa dados e regras\", \"View mostra a resposta\"]', '[0, 1, 2, 3]', 'O fluxo começa na interação do usuário, passa pelo Controller, usa o Model e retorna para a View.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Ordene a travessia correta de uma requisição no templo MVC.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'ordenar', 'mvc', 'Ordene a sigla gravada no escudo arquitetural de Willen.', NULL, '[\"Controller\", \"Model\", \"View\"]', '[1, 2, 0]', 'A sigla MVC é Model, View, Controller.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Ordene a sigla gravada no escudo arquitetural de Willen.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'ordenar', 'mvc', 'Ordene a limpeza de uma tela que fazia tudo sozinha.', NULL, '[\"Separar a apresentação na View\", \"Mover decisões de fluxo ao Controller\", \"Mover regras e dados ao Model\", \"Manter comunicação clara entre as camadas\"]', '[0, 1, 2, 3]', 'A refatoração distribui responsabilidades e preserva o fluxo entre camadas.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Ordene a limpeza de uma tela que fazia tudo sozinha.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'ordenar', 'mvc', 'Ordene o diagnóstico dos sinais deixados pelo Espectro: visual quebrado, clique sem ação, dado incorreto.', NULL, '[\"Investigar a View\", \"Investigar o Controller\", \"Investigar o Model\"]', '[0, 1, 2]', 'Problemas visuais apontam para View; ações para Controller; dados/regras para Model.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Ordene o diagnóstico dos sinais deixados pelo Espectro: visual quebrado, clique sem ação, dado incorreto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'ordenar', 'mvc', 'Ordene do conceito mais visível ao mais interno no castelo MVC.', NULL, '[\"View\", \"Controller\", \"Model\", \"Persistência/ORM\"]', '[0, 1, 2, 3]', 'A interação passa da interface ao controle, ao domínio e, quando necessário, à persistência.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Ordene do conceito mais visível ao mais interno no castelo MVC.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'erro', 'mvc', 'O Espectro do Spaghetti escreveu um decreto. Qual linha viola a separação MVC?', '1: View mostra o formulário.\n2: Controller recebe o clique.\n3: Model aplica regra de negócio.\n4: View executa regra de negócio e grava no banco.', '[\"linha 1\", \"linha 2\", \"linha 3\", \"linha 4\"]', '3', 'A View não deve concentrar regra de negócio nem persistência.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='O Espectro do Spaghetti escreveu um decreto. Qual linha viola a separação MVC?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'erro', 'mvc', 'Willen pediu para associar cada camada. Qual associação está corrompida?', '1: View -> apresentação\n2: Controller -> coordenação do fluxo\n3: Model -> dados e regras\n4: ORM -> folha de estilo visual', '[\"linha 1\", \"linha 2\", \"linha 3\", \"linha 4\"]', '3', 'ORM se relaciona ao mapeamento objeto-relacional, não a estilo visual.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Willen pediu para associar cada camada. Qual associação está corrompida?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'erro', 'mvc', 'Qual ordem alimenta o Espectro em vez de derrotá-lo?', NULL, '[\"View chama Controller, Controller chama Model\", \"Controller decide a próxima tela\", \"Model representa dados e regras\", \"View recebe tudo e decide regra, rota e banco\"]', '3', 'A última opção concentra responsabilidades na View.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Qual ordem alimenta o Espectro em vez de derrotá-lo?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'erro', 'mvc', 'O escriba confundiu roteamento. Qual frase está errada?', '1: Rota pode ligar URL a uma ação.\n2: Roteamento ajuda o sistema a decidir quem atende a requisição.\n3: Roteamento é apenas a criação de colunas no banco.\n4: Controller pode ser acionado por uma rota.', '[\"linha 1\", \"linha 2\", \"linha 3\", \"linha 4\"]', '2', 'Roteamento não é criação de colunas; ele organiza caminhos da aplicação.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='O escriba confundiu roteamento. Qual frase está errada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'erro', 'mvc', 'Qual peça foi colocada no altar errado?', '1: Tela de cadastro -> View\n2: Decisão de fluxo -> Controller\n3: Regra de negócio -> Model\n4: Cor do botão -> Model', '[\"linha 1\", \"linha 2\", \"linha 3\", \"linha 4\"]', '3', 'A cor do botão é aspecto de apresentação, portanto pertence à View/estilo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Qual peça foi colocada no altar errado?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'arrastar', 'mvc', 'Arraste cada artefato ao guardião correto do MVC.', NULL, '{\"itens\": [\"Tela do usuário\", \"Regra de negócio\", \"Decisão de fluxo\", \"Mapeamento objeto-relacional\"], \"alvos\": [\"View\", \"Model\", \"Controller\", \"ORM\"]}', '[0, 1, 2, 3]', 'Cada item pertence a uma responsabilidade específica.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Arraste cada artefato ao guardião correto do MVC.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'arrastar', 'mvc', 'Arraste cada sintoma para a camada mais provável.', NULL, '{\"itens\": [\"Botão não dispara ação\", \"Dado calculado errado\", \"Layout quebrado\", \"Objeto não mapeia tabela\"], \"alvos\": [\"Controller\", \"Model\", \"View\", \"ORM\"]}', '[0, 1, 2, 3]', 'O sintoma indica onde começar a investigação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Arraste cada sintoma para a camada mais provável.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'arrastar', 'mvc', 'Arraste cada missão do Porto da Sintaxe para sua camada.', NULL, '{\"itens\": [\"Mostrar formulário\", \"Receber comando\", \"Aplicar regra\", \"Mapear objeto para tabela\"], \"alvos\": [\"View\", \"Controller\", \"Model\", \"ORM\"]}', '[0, 1, 2, 3]', 'A arquitetura fica limpa quando cada missão tem dono.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Arraste cada missão do Porto da Sintaxe para sua camada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'arrastar', 'mvc', 'Arraste cada conceito ao significado correto.', NULL, '{\"itens\": [\"Model\", \"View\", \"Controller\", \"Roteamento\"], \"alvos\": [\"Dados e regras\", \"Apresentação\", \"Coordenação\", \"URL para ação\"]}', '[0, 1, 2, 3]', 'Esses conceitos compõem a organização da fase O Padrão MVC.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Arraste cada conceito ao significado correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'arrastar', 'mvc', 'Arraste cada etapa ao caminho correto da requisição.', NULL, '{\"itens\": [\"Navegador do jogador\", \"Ação do Controller\", \"Consulta ao Model\", \"Resposta visual\"], \"alvos\": [\"Início da interação\", \"Coordenação\", \"Processamento de dados\", \"View final\"]}', '[0, 1, 2, 3]', 'O fluxo evita mistura de responsabilidades.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Arraste cada etapa ao caminho correto da requisição.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'multipla', 'mvc', 'Em MVC, onde devem morar as regras de negócio e o acesso a dados?', NULL, '[\"Na View\",\"No Model\",\"No CSS\",\"No HTML\"]', '1', 'O Model concentra dados e regras; View só apresenta, Controller só orquestra.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Em MVC, onde devem morar as regras de negócio e o acesso a dados?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'vf', 'mvc', 'Um Controller pode consultar vários Models antes de escolher a View.', NULL, NULL, 'true', 'O Controller é o maestro: reúne o que precisar dos Models e entrega à View.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Um Controller pode consultar vários Models antes de escolher a View.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'multipla', 'mvc', 'Qual é a principal vantagem de separar em Model, View e Controller?', NULL, '[\"Deixa o site mais rápido sempre\",\"Separa responsabilidades, facilitando manutenção e testes\",\"Elimina a necessidade de banco\",\"Dispensa o HTML\"]', '1', 'Separação de responsabilidades: cada parte muda por um motivo só.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Qual é a principal vantagem de separar em Model, View e Controller?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'multipla', 'mvc', 'Na rota index.php?url=perfil/editar/7, qual é o controller?', NULL, '[\"perfil\",\"editar\",\"7\",\"index\"]', '0', 'No padrão controller/metodo/parametro: \"perfil\" é o controller, \"editar\" o método e 7 o parâmetro.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Na rota index.php?url=perfil/editar/7, qual é o controller?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'completar', 'mvc', 'Complete: a camada do MVC que renderiza o HTML para o usuário é a ___', NULL, NULL, '[\"View\",\"view\",\"Visão\"]', 'A View cuida só da apresentação — nada de SQL ou regra de negócio nela.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Complete: a camada do MVC que renderiza o HTML para o usuário é a ___') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'erro', 'mvc', 'Qual prática QUEBRA a separação do MVC?', NULL, '[\"O Model acessar o banco\",\"O Controller chamar o Model\",\"A View conter SQL e regras de negócio\",\"A View apenas exibir os dados recebidos\"]', '2', 'SQL e lógica na View misturam as camadas — o caminho mais curto para o espaguete.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Qual prática QUEBRA a separação do MVC?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'multipla', 'sql', 'Qual comando SQL lê (consulta) registros de uma tabela?', NULL, '[\"INSERT\",\"SELECT\",\"UPDATE\",\"DELETE\"]', '1', 'SELECT recupera dados de uma ou mais tabelas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Qual comando SQL lê (consulta) registros de uma tabela?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'completar', 'sql', 'Complete para buscar todas as colunas dos usuários:', 'SELECT ___ FROM usuarios;', NULL, '[\"*\"]', 'O asterisco (*) seleciona todas as colunas da tabela.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Complete para buscar todas as colunas dos usuários:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'multipla', 'sql', 'Qual cláusula filtra as linhas por uma condição?', NULL, '[\"ORDER BY\",\"WHERE\",\"GROUP BY\",\"LIMIT\"]', '1', 'WHERE restringe o resultado às linhas que satisfazem a condição.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Qual cláusula filtra as linhas por uma condição?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'multipla', 'sql', 'A Sentinela SQL guarda o Baú. Qual comando escolhe colunas para consulta?', NULL, '[\"SELECT\", \"WHERE\", \"ORDER BY\", \"DELETE\"]', '0', 'SELECT indica quais colunas ou expressões serão retornadas.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='A Sentinela SQL guarda o Baú. Qual comando escolhe colunas para consulta?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'multipla', 'sql', 'O baú pergunta de qual sala virão os registros. Qual palavra indica a tabela origem?', NULL, '[\"FROM\", \"JOIN\", \"COUNT\", \"UPDATE\"]', '0', 'FROM informa a tabela ou fonte da consulta.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='O baú pergunta de qual sala virão os registros. Qual palavra indica a tabela origem?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'multipla', 'sql', 'Para filtrar apenas poções com preco > 10, qual cláusula o aprendiz deve usar?', NULL, '[\"WHERE\", \"ORDER BY\", \"INSERT\", \"COUNT\"]', '0', 'WHERE aplica condições de filtragem.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Para filtrar apenas poções com preco > 10, qual cláusula o aprendiz deve usar?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'multipla', 'sql', 'A Sentinela quer unir heróis e guildas relacionadas. Qual comando cria a ponte entre tabelas?', NULL, '[\"JOIN\", \"DELETE\", \"SET\", \"VALUES\"]', '0', 'JOIN relaciona registros de tabelas diferentes.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='A Sentinela quer unir heróis e guildas relacionadas. Qual comando cria a ponte entre tabelas?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'multipla', 'sql', 'Qual função conta quantos pergaminhos existem em uma consulta?', NULL, '[\"COUNT\", \"ORDER BY\", \"FROM\", \"VALUES\"]', '0', 'COUNT retorna a contagem de registros ou valores.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Qual função conta quantos pergaminhos existem em uma consulta?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'vf', 'sql', 'SELECT e FROM podem aparecer juntos para buscar dados de uma tabela.', NULL, NULL, 'true', 'SELECT escolhe o que retorna; FROM indica a origem.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='SELECT e FROM podem aparecer juntos para buscar dados de uma tabela.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'vf', 'sql', 'WHERE serve para ordenar o resultado em ordem crescente ou decrescente.', NULL, NULL, 'false', 'WHERE filtra. A ordenação é feita por ORDER BY.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='WHERE serve para ordenar o resultado em ordem crescente ou decrescente.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'vf', 'sql', 'INSERT adiciona novos registros ao baú de dados.', NULL, NULL, 'true', 'INSERT é usado para inserir registros.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='INSERT adiciona novos registros ao baú de dados.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'vf', 'sql', 'UPDATE altera registros existentes e DELETE remove registros.', NULL, NULL, 'true', 'UPDATE modifica; DELETE remove.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='UPDATE altera registros existentes e DELETE remove registros.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'vf', 'sql', 'JOIN é usado apenas para apagar linhas duplicadas.', NULL, NULL, 'false', 'JOIN relaciona tabelas; não é comando de remoção.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='JOIN é usado apenas para apagar linhas duplicadas.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'completar', 'sql', 'Complete o feitiço para escolher todas as colunas do grimório: _____ * FROM herois;', NULL, NULL, '[\"SELECT\", \"select\"]', 'SELECT escolhe os dados retornados pela consulta.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Complete o feitiço para escolher todas as colunas do grimório: _____ * FROM herois;') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'completar', 'sql', 'Complete a origem do baú: SELECT nome _____ herois;', NULL, NULL, '[\"FROM\", \"from\"]', 'FROM indica de qual tabela os dados serão buscados.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Complete a origem do baú: SELECT nome _____ herois;') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'completar', 'sql', 'Complete o filtro: SELECT nome FROM herois _____ nivel >= 10;', NULL, NULL, '[\"WHERE\", \"where\"]', 'WHERE restringe os registros conforme uma condição.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Complete o filtro: SELECT nome FROM herois _____ nivel >= 10;') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'completar', 'sql', 'Complete a ordenação: SELECT nome FROM herois _____ nome ASC;', NULL, NULL, '[\"ORDER BY\", \"order by\", \"ORDERBY\", \"orderby\"]', 'ORDER BY define a ordenação do resultado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Complete a ordenação: SELECT nome FROM herois _____ nome ASC;') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'completar', 'sql', 'Complete a contagem: SELECT _____(*) FROM herois;', NULL, NULL, '[\"COUNT\", \"count\"]', 'COUNT(*) conta os registros retornados.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Complete a contagem: SELECT _____(*) FROM herois;') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'ordenar', 'sql', 'Ordene a consulta para listar heróis da guilda 3 em ordem de nome.', NULL, '[\"SELECT nome\", \"FROM herois\", \"WHERE guilda_id = 3\", \"ORDER BY nome ASC\"]', '[0, 1, 2, 3]', 'A ordem básica é SELECT, FROM, WHERE e ORDER BY.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Ordene a consulta para listar heróis da guilda 3 em ordem de nome.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'ordenar', 'sql', 'Ordene o ritual de inserção de uma nova poção.', NULL, '[\"INSERT INTO pocoes (nome, preco)\", \"VALUES (\'Cura\', 10)\"]', '[0, 1]', 'INSERT INTO define tabela e colunas; VALUES define os dados.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Ordene o ritual de inserção de uma nova poção.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'ordenar', 'sql', 'Ordene o encantamento para alterar o preço de uma poção específica.', NULL, '[\"UPDATE pocoes\", \"SET preco = 12\", \"WHERE id = 5\"]', '[0, 1, 2]', 'UPDATE escolhe a tabela, SET define alterações e WHERE limita o alvo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Ordene o encantamento para alterar o preço de uma poção específica.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'ordenar', 'sql', 'Ordene a remoção segura de um registro amaldiçoado.', NULL, '[\"DELETE FROM pocoes\", \"WHERE id = 5\"]', '[0, 1]', 'DELETE FROM remove da tabela; WHERE evita remoção ampla.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Ordene a remoção segura de um registro amaldiçoado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'ordenar', 'sql', 'Ordene a consulta que une heróis e guildas.', NULL, '[\"SELECT herois.nome, guildas.nome\", \"FROM herois\", \"JOIN guildas ON guildas.id = herois.guilda_id\", \"WHERE guildas.nome = \'Azul\'\"]', '[0, 1, 2, 3]', 'JOIN entra depois da origem principal e antes do filtro final.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Ordene a consulta que une heróis e guildas.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'erro', 'sql', 'A Sentinela rabiscou uma consulta. Qual linha está faltando para indicar a origem dos dados?', '1: SELECT nome\n2: WHERE nivel > 5\n3: ORDER BY nome', '[\"linha 1\", \"faltou FROM entre as linhas 1 e 2\", \"linha 3\", \"não há erro\"]', '1', 'Sem FROM, a consulta não informa de qual tabela os dados vêm.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='A Sentinela rabiscou uma consulta. Qual linha está faltando para indicar a origem dos dados?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'erro', 'sql', 'Qual comando ameaça apagar todas as poções por falta de filtro?', 'DELETE FROM pocoes;', '[\"Seguro: remove só uma poção\", \"Perigoso: não há WHERE\", \"Errado porque DELETE não existe\", \"Seguro porque tem ORDER BY\"]', '1', 'Sem WHERE, o DELETE pode remover todos os registros da tabela.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Qual comando ameaça apagar todas as poções por falta de filtro?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'erro', 'sql', 'O aprendiz queria ordenar, mas escolheu a cláusula errada. Onde está o erro?', 'SELECT nome FROM herois WHERE nome ASC;', '[\"SELECT\", \"FROM\", \"WHERE nome ASC\", \"não há erro\"]', '2', 'Ordenação deve usar ORDER BY, não WHERE.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='O aprendiz queria ordenar, mas escolheu a cláusula errada. Onde está o erro?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'erro', 'sql', 'Qual linha confunde contagem com ordenação?', '1: SELECT COUNT(*)\n2: FROM herois\n3: ORDER BY COUNT(*) como filtro obrigatório', '[\"linha 1\", \"linha 2\", \"linha 3\", \"nenhuma\"]', '2', 'COUNT conta; ORDER BY ordena e não é filtro obrigatório.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Qual linha confunde contagem com ordenação?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'erro', 'sql', 'Qual opção NÃO pertence ao grupo de comandos de modificação de dados?', NULL, '[\"INSERT\", \"UPDATE\", \"DELETE\", \"ORDER BY\"]', '3', 'ORDER BY apenas ordena resultados; não insere, altera ou remove registros.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Qual opção NÃO pertence ao grupo de comandos de modificação de dados?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'arrastar', 'sql', 'Arraste cada palavra do Baú para seu poder SQL.', NULL, '{\"itens\": [\"SELECT\", \"FROM\", \"WHERE\", \"ORDER BY\"], \"alvos\": [\"Escolher colunas\", \"Indicar tabela\", \"Filtrar registros\", \"Ordenar resultado\"]}', '[0, 1, 2, 3]', 'Essas cláusulas formam a base de uma consulta SQL.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Arraste cada palavra do Baú para seu poder SQL.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'arrastar', 'sql', 'Arraste cada comando de manipulação ao efeito correto.', NULL, '{\"itens\": [\"INSERT\", \"UPDATE\", \"DELETE\", \"SELECT\"], \"alvos\": [\"Inserir registro\", \"Alterar registro\", \"Remover registro\", \"Consultar dados\"]}', '[0, 1, 2, 3]', 'Cada comando tem uma função específica no banco.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Arraste cada comando de manipulação ao efeito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'arrastar', 'sql', 'Arraste cada fragmento ao lugar correto da consulta.', NULL, '{\"itens\": [\"SELECT nome\", \"FROM herois\", \"WHERE nivel > 3\", \"ORDER BY nome\"], \"alvos\": [\"Projeção\", \"Origem\", \"Filtro\", \"Ordenação\"]}', '[0, 1, 2, 3]', 'A consulta fica legível quando cada parte cumpre seu papel.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Arraste cada fragmento ao lugar correto da consulta.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'arrastar', 'sql', 'Arraste cada situação ao recurso SQL mais adequado.', NULL, '{\"itens\": [\"Contar registros\", \"Unir tabelas\", \"Filtrar por cidade\", \"Ordenar por data\"], \"alvos\": [\"COUNT\", \"JOIN\", \"WHERE\", \"ORDER BY\"]}', '[0, 1, 2, 3]', 'COUNT, JOIN, WHERE e ORDER BY resolvem necessidades diferentes.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Arraste cada situação ao recurso SQL mais adequado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'arrastar', 'sql', 'Arraste cada perigo à proteção correspondente.', NULL, '{\"itens\": [\"DELETE sem filtro\", \"UPDATE geral sem alvo\", \"Consulta confusa sem origem\", \"Resultado fora de ordem\"], \"alvos\": [\"Usar WHERE\", \"Usar WHERE com chave\", \"Usar FROM\", \"Usar ORDER BY\"]}', '[0, 1, 2, 3]', 'Filtros, origem e ordenação tornam o feitiço SQL mais controlado.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Arraste cada perigo à proteção correspondente.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'multipla', 'sql', 'Qual comando insere um novo registro numa tabela?', NULL, '[\"SELECT\",\"INSERT\",\"DROP\",\"WHERE\"]', '1', 'INSERT INTO ... VALUES ... adiciona uma nova linha.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Qual comando insere um novo registro numa tabela?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'completar', 'sql', 'Complete para ordenar os usuários pelo nome:', 'SELECT * FROM usuarios ORDER ___ nome;', NULL, '[\"BY\",\"by\"]', 'ORDER BY coluna define a ordenação do resultado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Complete para ordenar os usuários pelo nome:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'multipla', 'sql', 'Qual cláusula restringe a consulta a no máximo 10 linhas?', NULL, '[\"TOP\",\"LIMIT\",\"MAX\",\"FIRST\"]', '1', 'Em MySQL, LIMIT 10 corta o resultado nas 10 primeiras linhas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Qual cláusula restringe a consulta a no máximo 10 linhas?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'multipla', 'sql', 'O que acontece com UPDATE usuarios SET ativo = 0; (sem WHERE)?', NULL, '[\"Atualiza só a primeira linha\",\"Não faz nada\",\"Atualiza TODAS as linhas da tabela\",\"Gera erro de sintaxe\"]', '2', 'Sem WHERE, o UPDATE atinge a tabela inteira. Respeite o WHERE — ou chore depois.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='O que acontece com UPDATE usuarios SET ativo = 0; (sem WHERE)?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m),
       'erro', 'php', 'O Kraken roubou um caractere. Qual linha causa o Parse Error?', '1: <?php\n2: $nome = \"Aprendiz\"\n3: echo $nome;', '[\"linha 1\",\"linha 2 — falta ;\",\"linha 3\",\"nenhuma\"]', '1', 'Falta o ponto e vírgula ao final da linha 2, quebrando a análise do código.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='O Kraken roubou um caractere. Qual linha causa o Parse Error?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m),
       'multipla', 'php', 'Qual a saída?', '$a = 3;\n$b = 4;\necho $a + $b;', '[\"34\",\"7\",\"73\",\"Erro\"]', '1', 'Com o operador + os valores são somados numericamente: 3 + 4 = 7 (diferente de concatenar com ponto).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='Qual a saída?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m),
       'completar', 'php', 'Complete para declarar uma função em PHP:', '________ saudar() { echo \"oi\"; }', NULL, '[\"function\"]', 'Funções em PHP são declaradas com a palavra-chave function.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='Complete para declarar uma função em PHP:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m),
       'vf', 'php', 'Em PHP, == compara apenas valores, enquanto === compara valor e tipo.', NULL, NULL, 'true', '=== é a comparação estrita: exige que valor e tipo sejam iguais.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='Em PHP, == compara apenas valores, enquanto === compara valor e tipo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m),
       'multipla', 'mvc', 'Na rota index.php?url=prompts/editar/5, qual parte é o método chamado?', NULL, '[\"prompts\",\"editar\",\"5\",\"index\"]', '1', 'No padrão controller/metodo/parametro, \"editar\" é o método e 5 é o parâmetro.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='Na rota index.php?url=prompts/editar/5, qual parte é o método chamado?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m),
       'erro', 'php', 'O Kraken roubou um símbolo. Onde está o Parse Error?', '$arr = [1, 2, 3;', '[\"Falta fechar o colchete ]\",\"Falta uma vírgula\",\"Falta o $\",\"Não há erro\"]', '0', 'O array abriu com [ mas nunca fechou com ]: o ; chegou cedo demais.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='O Kraken roubou um símbolo. Onde está o Parse Error?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m),
       'multipla', 'php', 'Qual operador compara valor E tipo (comparação estrita)?', NULL, '[\"==\",\"===\",\"=\",\"<>\"]', '1', '=== exige que valor e tipo sejam iguais; \"5\" === 5 é falso.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='Qual operador compara valor E tipo (comparação estrita)?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m),
       'vf', 'php', 'Em PHP, um parâmetro pode ter valor padrão: function taxa($v, $pct = 10).', NULL, NULL, 'true', 'Parâmetros com valor padrão tornam o argumento opcional na chamada.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='Em PHP, um parâmetro pode ter valor padrão: function taxa($v, $pct = 10).') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m),
       'ordenar', 'mvc', 'Ordene o fluxo de uma requisição em MVC:', NULL, '[\"A View renderiza o HTML\",\"O Controller recebe a requisição\",\"O Model busca os dados\"]', '[1,2,0]', 'Controller recebe → Model busca os dados → View renderiza a resposta.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='Ordene o fluxo de uma requisição em MVC:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m),
       'completar', 'php', 'Complete para a função devolver a soma:', 'function soma($a, $b) { ___ $a + $b; }', NULL, '[\"return\"]', 'return entrega o valor de volta a quem chamou a função.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='Complete para a função devolver a soma:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'multipla', 'poo', 'Em POO, o que é uma classe?', NULL, '[\"Uma instância em memória\",\"Um molde que define atributos e métodos\",\"Uma variável global\",\"Um arquivo de configuração\"]', '1', 'A classe é o molde/projeto; descreve atributos e comportamentos dos objetos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Em POO, o que é uma classe?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'multipla', 'poo', 'E o que é um objeto?', NULL, '[\"O molde\",\"Uma instância concreta criada a partir da classe\",\"Um tipo de laço\",\"Uma função solta\"]', '1', 'O objeto é uma instância concreta, criada a partir da classe.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='E o que é um objeto?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'completar', 'poo', 'Complete para instanciar a classe Heroi em PHP:', '$h = ___ Heroi();', NULL, '[\"new\"]', 'O operador new cria uma nova instância (objeto) de uma classe.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Complete para instanciar a classe Heroi em PHP:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'vf', 'poo', 'Métodos são as funções que pertencem a uma classe.', NULL, NULL, 'true', 'Sim: métodos são o comportamento (funções) definido dentro da classe.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Métodos são as funções que pertencem a uma classe.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'multipla', 'poo', 'Clayton ergue um molde chamado Heroi. Em POO, esse molde é uma:', NULL, '[\"classe\", \"instância\", \"pacote\", \"exceção\"]', '0', 'Classe é o molde que define atributos e métodos.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Clayton ergue um molde chamado Heroi. Em POO, esse molde é uma:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'multipla', 'poo', 'Quando o aprendiz usa new Heroi(), o que nasce na Cidadela?', NULL, '[\"um objeto\", \"um pacote\", \"um comentário\", \"uma exceção obrigatória\"]', '0', 'new cria uma instância/objeto a partir de uma classe.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Quando o aprendiz usa new Heroi(), o que nasce na Cidadela?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'multipla', 'poo', 'No herói da Cidadela, nome, nivel e vida são exemplos de:', NULL, '[\"atributos\", \"métodos\", \"construtores\", \"pacotes\"]', '0', 'Atributos guardam estado do objeto.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='No herói da Cidadela, nome, nivel e vida são exemplos de:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'multipla', 'poo', 'Em public void atacar(), atacar é um:', NULL, '[\"método\", \"atributo\", \"objeto\", \"operador SQL\"]', '0', 'Métodos definem comportamentos da classe.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Em public void atacar(), atacar é um:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'multipla', 'poo', 'Qual palavra indica que o atributo pertence à própria classe, e não a cada objeto individual?', NULL, '[\"static\", \"new\", \"this\", \"return\"]', '0', 'static marca membros compartilhados pela classe.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Qual palavra indica que o atributo pertence à própria classe, e não a cada objeto individual?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'vf', 'poo', 'Uma classe pode definir atributos e métodos.', NULL, NULL, 'true', 'A classe reúne estado e comportamento.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Uma classe pode definir atributos e métodos.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'vf', 'poo', 'Todo objeto criado com new é uma instância de alguma classe.', NULL, NULL, 'true', 'new instancia objetos a partir de classes.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Todo objeto criado com new é uma instância de alguma classe.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'vf', 'poo', 'this pode ser usado para referenciar o próprio objeto dentro da classe.', NULL, NULL, 'true', 'this aponta para a instância atual.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='this pode ser usado para referenciar o próprio objeto dentro da classe.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'vf', 'poo', 'Um construtor serve para ordenar resultados de uma tabela SQL.', NULL, NULL, 'false', 'Construtor inicializa objetos.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Um construtor serve para ordenar resultados de uma tabela SQL.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'vf', 'poo', 'Um atributo static é exclusivo de cada objeto e nunca pertence à classe.', NULL, NULL, 'false', 'static pertence à classe, sendo compartilhado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Um atributo static é exclusivo de cada objeto e nunca pertence à classe.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'completar', 'poo', 'Complete a abertura do molde de Clayton:', 'public _____ Heroi { }', NULL, '[\"class\", \"classe\"]', 'Em Java, class declara uma classe.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Complete a abertura do molde de Clayton:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'completar', 'poo', 'Complete a criação do objeto:', 'Heroi h = _____ Heroi();', NULL, '[\"new\"]', 'new cria uma nova instância.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Complete a criação do objeto:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'completar', 'poo', 'Complete a referência ao próprio objeto:', 'this.nome = nome;\nA palavra usada é _____.', NULL, '[\"this\"]', 'this referencia o objeto atual.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Complete a referência ao próprio objeto:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'completar', 'poo', 'Complete o construtor da classe Heroi:', 'public _____(String nome) { this.nome = nome; }', NULL, '[\"Heroi\"]', 'O construtor tem o mesmo nome da classe.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Complete o construtor da classe Heroi:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'completar', 'poo', 'Complete a palavra que torna o contador compartilhado pela classe:', 'public _____ int totalHerois;', NULL, '[\"static\"]', 'static cria um membro pertencente à classe.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Complete a palavra que torna o contador compartilhado pela classe:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'ordenar', 'poo', 'Ordene a forja de um objeto na Cidadela.', NULL, '[\"Declarar a classe\", \"Definir atributos e métodos\", \"Criar objeto com new\", \"Usar o objeto na missão\"]', '[0, 1, 2, 3]', 'Primeiro vem o molde, depois seus membros, então a instância e o uso.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Ordene a forja de um objeto na Cidadela.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'ordenar', 'poo', 'Ordene do molde ao personagem vivo.', NULL, '[\"Classe Heroi\", \"Construtor Heroi()\", \"new Heroi()\", \"Objeto h\"]', '[0, 1, 2, 3]', 'A classe define, o construtor inicializa, new instancia e a variável referencia o objeto.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Ordene do molde ao personagem vivo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'ordenar', 'poo', 'Ordene a execução de um método atacar().', NULL, '[\"Objeto recebe chamada\", \"Método é localizado na classe\", \"Código do método executa\", \"Resultado da ação aparece\"]', '[0, 1, 2, 3]', 'A chamada parte da instância e executa o comportamento definido na classe.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Ordene a execução de um método atacar().') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'ordenar', 'poo', 'Ordene os conceitos do mais geral ao mais concreto.', NULL, '[\"Classe\", \"Construtor\", \"Objeto\", \"Valor de atributo\"]', '[0, 1, 2, 3]', 'A classe é geral; o objeto é concreto e possui valores.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Ordene os conceitos do mais geral ao mais concreto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'ordenar', 'poo', 'Ordene a inicialização de nome usando this.', NULL, '[\"Receber parâmetro nome\", \"Entrar no construtor\", \"Executar this.nome = nome\", \"Objeto fica com estado inicial\"]', '[0, 1, 2, 3]', 'O construtor recebe dados e inicializa o estado do objeto.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Ordene a inicialização de nome usando this.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'erro', 'poo', 'O Golem de Classe rachou o código. Qual linha impede declarar corretamente a classe?', '1: public class Heroi {\n2: private String nome;\n3: public void atacar() { }\n4: public objeto vida;', '[\"linha 1\", \"linha 2\", \"linha 3\", \"linha 4\"]', '3', 'objeto não é tipo Java adequado para declarar vida; seria necessário um tipo válido, como int.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='O Golem de Classe rachou o código. Qual linha impede declarar corretamente a classe?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'erro', 'poo', 'Qual linha confunde atributo com método?', '1: private int nivel;\n2: public void atacar() { }\n3: private String nome;\n4: vida();', '[\"linha 1\", \"linha 2\", \"linha 3\", \"linha 4\"]', '3', 'vida(); é chamada de método, não declaração de atributo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Qual linha confunde atributo com método?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'erro', 'poo', 'Qual criação de objeto está incorreta?', NULL, '[\"Heroi h = new Heroi();\", \"Heroi h = Heroi new();\", \"new Heroi();\", \"Heroi mago = new Heroi();\"]', '1', 'A palavra new vem antes do construtor: new Heroi().', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Qual criação de objeto está incorreta?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'erro', 'poo', 'Qual frase sobre static está errada?', NULL, '[\"static pertence à classe\", \"static pode ser compartilhado entre instâncias\", \"static significa que cada objeto tem uma cópia obrigatoriamente isolada\", \"static pode marcar método ou atributo\"]', '2', 'Membros static pertencem à classe, não a uma cópia isolada por objeto.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Qual frase sobre static está errada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'erro', 'poo', 'O construtor foi escrito com nome diferente. Qual linha revela o erro?', '1: public class Heroi {\n2: public Guerreiro(String nome) {\n3: this.nome = nome;\n4: }', '[\"linha 1\", \"linha 2\", \"linha 3\", \"linha 4\"]', '1', 'Em Java, o construtor deve ter o mesmo nome da classe.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='O construtor foi escrito com nome diferente. Qual linha revela o erro?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada peça da forja ao conceito correto.', NULL, '{\"itens\": [\"class Heroi\", \"new Heroi()\", \"nome\", \"atacar()\"], \"alvos\": [\"Classe\", \"Objeto/instância\", \"Atributo\", \"Método\"]}', '[0, 1, 2, 3]', 'Classe, objeto, atributo e método são os fundamentos da fase.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada peça da forja ao conceito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada palavra Java ao seu papel.', NULL, '{\"itens\": [\"new\", \"this\", \"static\", \"constructor\"], \"alvos\": [\"Cria objeto\", \"Objeto atual\", \"Membro da classe\", \"Inicializa objeto\"]}', '[0, 1, 2, 3]', 'Essas palavras aparecem no vocabulário central de classes e objetos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada palavra Java ao seu papel.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada exemplo ao tipo correto.', NULL, '{\"itens\": [\"int vida\", \"void curar()\", \"Heroi()\", \"Heroi h\"], \"alvos\": [\"Atributo\", \"Método\", \"Construtor\", \"Referência de objeto\"]}', '[0, 1, 2, 3]', 'Cada trecho mostra uma parte da construção orientada a objetos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada exemplo ao tipo correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada descrição ao conceito.', NULL, '{\"itens\": [\"Molde\", \"Coisa criada do molde\", \"Estado guardado\", \"Comportamento executável\"], \"alvos\": [\"Classe\", \"Objeto\", \"Atributo\", \"Método\"]}', '[0, 1, 2, 3]', 'A metáfora do molde ajuda a entender a Cidadela dos Objetos.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada descrição ao conceito.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada runa ao momento da criação.', NULL, '{\"itens\": [\"Definir classe\", \"Chamar construtor\", \"Usar new\", \"Acessar método\"], \"alvos\": [\"Antes da instância\", \"Durante inicialização\", \"Criação do objeto\", \"Comportamento do objeto\"]}', '[0, 1, 2, 3]', 'A sequência mostra como um objeto nasce e age.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada runa ao momento da criação.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'multipla', 'poo', 'Na Cidadela dos Objetos, Clayton mostra o molde arcano chamado Guerreiro. Em POO, esse molde é uma:', NULL, '[\"classe\",\"objeto\",\"pacote\",\"exceção\"]', '0', 'Classe é o molde que define atributos e métodos dos objetos.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Na Cidadela dos Objetos, Clayton mostra o molde arcano chamado Guerreiro. Em POO, esse molde é uma:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'multipla', 'poo', 'O aprendiz conjura new Guerreiro(). O que surge diante do Golem de Classe?', NULL, '[\"uma instância/objeto\",\"uma interface sem métodos\",\"um pacote vazio\",\"um comentário\"]', '0', 'O operador new cria um objeto, isto é, uma instância da classe.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='O aprendiz conjura new Guerreiro(). O que surge diante do Golem de Classe?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'multipla', 'poo', 'No grimório public class Pocao { int cura; }, o campo cura representa:', NULL, '[\"um atributo\",\"um método\",\"um construtor\",\"um pacote\"]', '0', 'Atributos armazenam o estado de um objeto.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='No grimório public class Pocao { int cura; }, o campo cura representa:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'multipla', 'poo', 'No ritual public void atacar(), atacar é:', NULL, '[\"um método\",\"um atributo\",\"uma constante de pacote\",\"um objeto já instanciado\"]', '0', 'Métodos representam comportamentos ou operações de uma classe.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='No ritual public void atacar(), atacar é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'multipla', 'poo', 'Qual palavra indica que uma runa pertence à classe inteira, e não a cada objeto individual?', NULL, '[\"static\",\"new\",\"this\",\"extends\"]', '0', 'Membros static pertencem à classe e são compartilhados pelas instâncias.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Qual palavra indica que uma runa pertence à classe inteira, e não a cada objeto individual?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'vf', 'poo', 'Uma classe pode reunir atributos e métodos no mesmo molde.', NULL, NULL, 'true', 'A classe descreve estado e comportamento.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Uma classe pode reunir atributos e métodos no mesmo molde.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'vf', 'poo', 'Um objeto é uma instância criada a partir de uma classe.', NULL, NULL, 'true', 'Objetos são instâncias concretas dos moldes.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Um objeto é uma instância criada a partir de uma classe.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'vf', 'poo', 'A palavra this permite referenciar o próprio objeto dentro de seus métodos ou construtores.', NULL, NULL, 'true', 'this aponta para a instância atual.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='A palavra this permite referenciar o próprio objeto dentro de seus métodos ou construtores.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'vf', 'poo', 'Um construtor sempre deve declarar tipo de retorno, como void.', NULL, NULL, 'false', 'Construtores não possuem tipo de retorno.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Um construtor sempre deve declarar tipo de retorno, como void.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'vf', 'poo', 'Um atributo static é exclusivo de cada objeto criado com new.', NULL, NULL, 'false', 'static pertence à classe, não a uma instância específica.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Um atributo static é exclusivo de cada objeto criado com new.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'completar', 'poo', 'Complete a runa de criação: para instanciar um objeto em Java, usa-se a palavra-chave ___.', NULL, NULL, '[\"new\"]', 'new cria uma nova instância.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Complete a runa de criação: para instanciar um objeto em Java, usa-se a palavra-chave ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'completar', 'poo', 'Complete o espelho interno: a palavra ___ referencia o próprio objeto atual.', NULL, NULL, '[\"this\"]', 'this permite diferenciar atributos da instância e variáveis locais.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Complete o espelho interno: a palavra ___ referencia o próprio objeto atual.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'completar', 'poo', 'Complete o selo compartilhado: um membro da classe, e não da instância, usa a palavra ___.', NULL, NULL, '[\"static\"]', 'static define membros pertencentes à classe.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Complete o selo compartilhado: um membro da classe, e não da instância, usa a palavra ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'completar', 'poo', 'Complete: o método especial que inicializa um objeto recém-criado chama-se ___.', NULL, NULL, '[\"construtor\",\"Construtor\"]', 'O construtor prepara o estado inicial do objeto.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Complete: o método especial que inicializa um objeto recém-criado chama-se ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'completar', 'poo', 'Complete: quando uma referência ainda não aponta para objeto algum, ela pode estar com o valor ___.', NULL, NULL, '[\"null\",\"NULL\"]', 'null indica ausência de referência para um objeto.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Complete: quando uma referência ainda não aponta para objeto algum, ela pode estar com o valor ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'ordenar', 'poo', 'Ordene o ritual correto para Clayton criar e usar um objeto Heroi.', NULL, '[\"Chamar um método do objeto\",\"Definir a classe Heroi\",\"Instanciar com new Heroi()\",\"Declarar atributos e métodos\"]', '[1,3,2,0]', 'Primeiro define-se o molde, depois seus membros, instancia-se e então o objeto pode ser usado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Ordene o ritual correto para Clayton criar e usar um objeto Heroi.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'ordenar', 'poo', 'Ordene a jornada de um objeto recém-nascido.', NULL, '[\"Construtor inicializa o estado\",\"new solicita a criação\",\"A referência passa a apontar para o objeto\",\"Métodos podem ser chamados\"]', '[1,0,2,3]', 'new cria o objeto, o construtor o inicializa e a referência permite o uso.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Ordene a jornada de um objeto recém-nascido.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'ordenar', 'poo', 'Ordene os elementos de uma classe simples.', NULL, '[\"Métodos de comportamento\",\"Declaração da classe\",\"Atributos de estado\",\"Construtor\"]', '[1,2,3,0]', 'Uma classe costuma ser lida como declaração, estado, inicialização e comportamento.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Ordene os elementos de uma classe simples.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'ordenar', 'poo', 'Ordene o raciocínio para decidir entre atributo e método.', NULL, '[\"Se guarda estado, é atributo\",\"Identificar o papel da informação\",\"Se executa ação, é método\"]', '[1,0,2]', 'Primeiro entende-se o papel; estado vira atributo e ação vira método.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Ordene o raciocínio para decidir entre atributo e método.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'ordenar', 'poo', 'Ordene o uso de static no Salão das Instâncias.', NULL, '[\"Acessar pelo nome da classe quando fizer sentido\",\"Identificar se o valor é compartilhado\",\"Marcar o membro como static\",\"Evitar usar static para estado individual\"]', '[1,2,0,3]', 'static é adequado para dados ou comportamentos da classe, não para estado individual.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Ordene o uso de static no Salão das Instâncias.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'erro', 'poo', 'O Golem de Classe encontrou este construtor falso. Qual é o problema?', 'public class Heroi {\n  public void Heroi(String nome) {\n    this.nome = nome;\n  }\n}', '[\"Tem void, então é método comum, não construtor\",\"Falta usar SQL\",\"Construtor não pode receber parâmetro\",\"this só existe em interface\"]', '0', 'Construtor não declara tipo de retorno.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='O Golem de Classe encontrou este construtor falso. Qual é o problema?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'erro', 'poo', 'Clayton viu este código. Qual erro impede a criação correta do objeto?', 'Heroi h;\nh.atacar();', '[\"A referência foi declarada, mas não recebeu objeto com new\",\"Métodos nunca podem ser chamados\",\"Faltou criar um pacote SQL\",\"static sempre é obrigatório\"]', '0', 'A variável h não aponta para um objeto; chamar método causaria problema.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Clayton viu este código. Qual erro impede a criação correta do objeto?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'erro', 'poo', 'Qual falha conceitual existe na runa: uma classe é um objeto pronto?', NULL, '[\"Confunde molde com instância\",\"Confunde getter com setter\",\"Confunde public com private\",\"Não há falha\"]', '0', 'Classe é o molde; objeto é a instância criada a partir dela.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Qual falha conceitual existe na runa: uma classe é um objeto pronto?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'erro', 'poo', 'No código abaixo, qual é a falha de intenção?', 'public class Bau {\n  int moedas;\n  public Bau(int moedas) {\n    moedas = moedas;\n  }\n}', '[\"O parâmetro sombreia o atributo; faltou this.moedas\",\"Construtor não pode ter parâmetro\",\"int não pode ser atributo\",\"A classe precisa ser interface\"]', '0', 'this.moedas diferencia o atributo do parâmetro moedas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='No código abaixo, qual é a falha de intenção?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'erro', 'poo', 'A torre declarou static int vida para guardar a vida de cada herói. Qual é o problema?', NULL, '[\"Todos os heróis compartilhariam a mesma vida\",\"static deixa a variável invisível\",\"new deixaria de funcionar\",\"static só existe em SQL\"]', '0', 'Estado individual não deve ser static, pois static é compartilhado pela classe.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='A torre declarou static int vida para guardar a vida de cada herói. Qual é o problema?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada artefato da Cidadela ao seu papel.', NULL, '{\"itens\":[\"Classe\",\"Objeto\",\"Atributo\",\"Método\"],\"alvos\":[\"Molde do ser\",\"Instância criada\",\"Estado guardado\",\"Comportamento executado\"]}', '[0,1,2,3]', 'Esses são os elementos centrais de classes e objetos.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada artefato da Cidadela ao seu papel.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada palavra-chave ao efeito correto.', NULL, '{\"itens\":[\"new\",\"this\",\"static\",\"null\"],\"alvos\":[\"Cria instância\",\"Refere-se ao objeto atual\",\"Pertence à classe\",\"Ausência de referência\"]}', '[0,1,2,3]', 'As palavras-chave controlam criação, referência e escopo de membros.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada palavra-chave ao efeito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada fragmento ao tipo de membro.', NULL, '{\"itens\":[\"String nome\",\"void atacar()\",\"Heroi(String nome)\",\"static int total\"],\"alvos\":[\"Atributo\",\"Método\",\"Construtor\",\"Membro de classe\"]}', '[0,1,2,3]', 'Cada fragmento cumpre papel específico dentro da classe.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada fragmento ao tipo de membro.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada situação à decisão correta.', NULL, '{\"itens\":[\"Vida do herói\",\"Total global de heróis\",\"Inicialização do nome\",\"Ação pular\"],\"alvos\":[\"Atributo de instância\",\"static\",\"Construtor\",\"Método\"]}', '[0,1,2,3]', 'Estado individual, estado compartilhado, inicialização e comportamento têm papéis distintos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada situação à decisão correta.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada criatura ao conceito que ela testa.', NULL, '{\"itens\":[\"Golem de Classe\",\"Espelho this\",\"Selo static\",\"Forja new\"],\"alvos\":[\"Molde\",\"Objeto atual\",\"Membro compartilhado\",\"Instanciação\"]}', '[0,1,2,3]', 'A narrativa reforça os conceitos básicos de POO.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada criatura ao conceito que ela testa.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'multipla', 'poo', 'Qual método especial é chamado automaticamente ao criar um objeto?', NULL, '[\"O destrutor\",\"O construtor\",\"O getter\",\"O main\"]', '1', 'O construtor (__construct em PHP) inicializa o objeto no momento da criação.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Qual método especial é chamado automaticamente ao criar um objeto?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'completar', 'poo', 'Complete o nome do método construtor em PHP:', 'public function ___________() { }', NULL, '[\"__construct\"]', 'Em PHP o construtor se chama __construct (dois underscores).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Complete o nome do método construtor em PHP:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'vf', 'poo', 'Vários objetos diferentes podem ser criados a partir de uma mesma classe.', NULL, NULL, 'true', 'A classe é o molde; cada new gera uma instância independente com seu próprio estado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Vários objetos diferentes podem ser criados a partir de uma mesma classe.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'multipla', 'poo', 'Numa classe, os atributos guardam o ___ e os métodos definem o ___.', NULL, '[\"comportamento \\/ estado\",\"estado \\/ comportamento\",\"nome \\/ tipo\",\"banco \\/ tela\"]', '1', 'Atributos = estado (dados); métodos = comportamento (ações).', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Numa classe, os atributos guardam o ___ e os métodos definem o ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'multipla', 'poo', 'Qual a saída?', 'class Gato {\n  public $nome = \'Bigode\';\n}\n$g = new Gato();\necho $g->nome;', '[\"nome\",\"Bigode\",\"Gato\",\"Erro\"]', '1', 'O objeto $g acessa seu atributo nome com ->, imprimindo \"Bigode\".', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Qual a saída?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'ordenar', 'poo', 'Ordene os passos para usar um objeto:', NULL, '[\"Chamar um método do objeto\",\"Declarar a classe\",\"Instanciar o objeto com new\"]', '[1,2,0]', 'Primeiro existe o molde (classe), depois cria-se a instância e então usam-se seus métodos.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Ordene os passos para usar um objeto:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'multipla', 'poo', 'Qual modificador torna um atributo acessível somente dentro da própria classe?', NULL, '[\"public\",\"private\",\"protected\",\"global\"]', '1', 'private restringe o acesso ao interior da própria classe.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Qual modificador torna um atributo acessível somente dentro da própria classe?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'multipla', 'poo', 'Para que serve o encapsulamento?', NULL, '[\"Deixar tudo público\",\"Proteger o estado interno e expor só o necessário\",\"Aumentar a duplicação\",\"Eliminar métodos\"]', '1', 'Encapsular protege os dados internos, expondo apenas uma interface controlada.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Para que serve o encapsulamento?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'vf', 'poo', 'Atributos protected são acessíveis pela própria classe e por suas subclasses.', NULL, NULL, 'true', 'protected permite acesso na classe e nas que a estendem.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Atributos protected são acessíveis pela própria classe e por suas subclasses.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'completar', 'poo', 'Complete o método getter que retorna o atributo nome:', 'public function getNome() { return $this->___; }', NULL, '[\"nome\"]', '$this->nome acessa o atributo nome da instância atual.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Complete o método getter que retorna o atributo nome:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'multipla', 'poo', 'O Espião dos Atributos tenta tocar diretamente no coração do objeto. Qual modificador fecha melhor esse portão?', NULL, '[\"private\", \"public\", \"print\", \"static\"]', '0', 'private impede acesso direto externo ao atributo.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='O Espião dos Atributos tenta tocar diretamente no coração do objeto. Qual modificador fecha melhor esse portão?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'multipla', 'poo', 'Qual método normalmente lê o valor de nome sem expor o atributo?', NULL, '[\"getNome()\", \"setNome()\", \"new Nome()\", \"deleteNome()\"]', '0', 'Getters retornam valores de atributos.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Qual método normalmente lê o valor de nome sem expor o atributo?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'multipla', 'poo', 'Qual método normalmente altera o valor de nivel de forma controlada?', NULL, '[\"setNivel(int nivel)\", \"getNivel()\", \"staticNivel()\", \"classNivel()\"]', '0', 'Setters alteram valores de atributos.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Qual método normalmente altera o valor de nivel de forma controlada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'multipla', 'poo', 'protected permite acesso principalmente dentro da própria classe, do pacote e de subclasses. Ele é um:', NULL, '[\"modificador de acesso\", \"tipo de banco\", \"laço de repetição\", \"comando SQL\"]', '0', 'public, private e protected são modificadores de acesso.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='protected permite acesso principalmente dentro da própria classe, do pacote e de subclasses. Ele é um:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'multipla', 'poo', 'Por que o encapsulamento protege o estado interno?', NULL, '[\"Porque força acesso controlado por métodos\", \"Porque remove todos os atributos\", \"Porque proíbe objetos\", \"Porque transforma método em tabela\"]', '0', 'Acesso controlado evita mudanças indevidas no estado do objeto.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Por que o encapsulamento protege o estado interno?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'vf', 'poo', 'Encapsulamento ajuda a proteger o estado interno do objeto.', NULL, NULL, 'true', 'A ideia é controlar o acesso aos dados internos.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Encapsulamento ajuda a proteger o estado interno do objeto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'vf', 'poo', 'Atributos public sempre são a melhor forma de proteger dados sensíveis do objeto.', NULL, NULL, 'false', 'public expõe diretamente; private costuma ser usado para proteger.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Atributos public sempre são a melhor forma de proteger dados sensíveis do objeto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'vf', 'poo', 'Getters e setters podem controlar leitura e alteração de atributos.', NULL, NULL, 'true', 'Eles são a forma comum de acesso controlado.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Getters e setters podem controlar leitura e alteração de atributos.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'vf', 'poo', 'Pacotes ajudam a organizar classes em grupos lógicos.', NULL, NULL, 'true', 'Packages organizam o código e também influenciam acesso em alguns casos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Pacotes ajudam a organizar classes em grupos lógicos.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'vf', 'poo', 'private permite acesso direto ao atributo por qualquer classe de qualquer pacote.', NULL, NULL, 'false', 'private restringe o acesso direto à própria classe.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='private permite acesso direto ao atributo por qualquer classe de qualquer pacote.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'completar', 'poo', 'Complete o lacre do atributo secreto:', '_____ String nome;', NULL, '[\"private\"]', 'private protege o atributo contra acesso direto externo.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Complete o lacre do atributo secreto:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'completar', 'poo', 'Complete o método de leitura:', 'public String _____() { return nome; }', NULL, '[\"getNome\"]', 'getNome é o getter esperado para nome.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Complete o método de leitura:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'completar', 'poo', 'Complete o método de alteração:', 'public void _____(String nome) { this.nome = nome; }', NULL, '[\"setNome\"]', 'setNome é o setter esperado para nome.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Complete o método de alteração:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'completar', 'poo', 'Complete a declaração de pacote:', '_____ br.com.algorithmia.modelo;', NULL, '[\"package\"]', 'package declara o pacote da classe.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Complete a declaração de pacote:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'completar', 'poo', 'Complete o modificador intermediário usado por subclasses:', '_____ int energia;', NULL, '[\"protected\"]', 'protected permite acesso em subclasses e no mesmo pacote.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Complete o modificador intermediário usado por subclasses:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'ordenar', 'poo', 'Ordene o ritual seguro para alterar o nome do herói.', NULL, '[\"Atributo nome fica private\", \"Criar setNome(String nome)\", \"Chamar objeto.setNome(Ayla)\", \"Objeto altera estado de forma controlada\"]', '[0, 1, 2, 3]', 'O acesso ao estado ocorre por método público controlado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Ordene o ritual seguro para alterar o nome do herói.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'ordenar', 'poo', 'Ordene a leitura encapsulada de energia.', NULL, '[\"Atributo energia fica privado\", \"Criar getEnergia()\", \"Chamar objeto.getEnergia()\", \"Receber valor sem acesso direto ao campo\"]', '[0, 1, 2, 3]', 'O getter permite leitura controlada.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Ordene a leitura encapsulada de energia.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'ordenar', 'poo', 'Ordene do acesso mais restrito ao mais aberto.', NULL, '[\"private\", \"protected\", \"public\"]', '[0, 1, 2]', 'private é mais restrito; public é o mais aberto.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Ordene do acesso mais restrito ao mais aberto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'ordenar', 'poo', 'Ordene a organização de uma classe no reino Java.', NULL, '[\"Declarar package\", \"Declarar class\", \"Declarar atributos privados\", \"Declarar getters e setters\"]', '[0, 1, 2, 3]', 'Pacote vem antes da classe; depois vêm membros e métodos.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Ordene a organização de uma classe no reino Java.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'ordenar', 'poo', 'Ordene a correção de um atributo exposto.', NULL, '[\"Trocar public por private\", \"Criar getter\", \"Criar setter com validação se necessário\", \"Atualizar chamadas externas para usar métodos\"]', '[0, 1, 2, 3]', 'A refatoração protege o estado e preserva acesso controlado.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Ordene a correção de um atributo exposto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'erro', 'poo', 'O Espião encontrou uma fresta. Qual linha expõe demais o estado interno?', '1: private String nome;\n2: public int vida;\n3: public String getNome() { return nome; }\n4: public void setNome(String nome) { this.nome = nome; }', '[\"linha 1\", \"linha 2\", \"linha 3\", \"linha 4\"]', '1', 'public int vida expõe o atributo diretamente.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='O Espião encontrou uma fresta. Qual linha expõe demais o estado interno?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'erro', 'poo', 'Qual getter devolve o atributo errado?', '1: private String nome;\n2: private int nivel;\n3: public String getNome() { return nome; }\n4: public int getNivel() { return vida; }', '[\"linha 1\", \"linha 2\", \"linha 3\", \"linha 4\"]', '3', 'getNivel deveria retornar nivel, não vida.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Qual getter devolve o atributo errado?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'erro', 'poo', 'Qual setter não altera o estado do objeto?', '1: private String nome;\n2: public void setNome(String nome) {\n3: nome = nome;\n4: }', '[\"linha 1\", \"linha 2\", \"linha 3\", \"linha 4\"]', '2', 'nome = nome atribui o parâmetro a ele mesmo; o correto seria this.nome = nome.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Qual setter não altera o estado do objeto?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'erro', 'poo', 'Qual afirmação enfraquece o castelo do encapsulamento?', NULL, '[\"Usar private nos atributos\", \"Criar getters e setters quando necessário\", \"Expor todos os atributos como public por padrão\", \"Organizar classes em pacotes\"]', '2', 'Expor tudo como public reduz controle sobre o estado interno.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Qual afirmação enfraquece o castelo do encapsulamento?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'erro', 'poo', 'Qual declaração de pacote está no lugar errado em um arquivo Java?', '1: public class Heroi { }\n2: package br.com.algorithmia.modelo;', '[\"linha 1\", \"linha 2\", \"as duas linhas estão na ordem certa\", \"não existe package em Java\"]', '1', 'A declaração package deve aparecer antes da declaração da classe.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Qual declaração de pacote está no lugar errado em um arquivo Java?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada modificador ao nível de abertura.', NULL, '{\"itens\": [\"private\", \"protected\", \"public\"], \"alvos\": [\"Mais restrito\", \"Intermediário/subclasses e pacote\", \"Mais aberto\"]}', '[0, 1, 2]', 'Os modificadores controlam visibilidade.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada modificador ao nível de abertura.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada método ao uso correto.', NULL, '{\"itens\": [\"getNome()\", \"setNome(String nome)\", \"getNivel()\", \"setNivel(int nivel)\"], \"alvos\": [\"Ler nome\", \"Alterar nome\", \"Ler nível\", \"Alterar nível\"]}', '[0, 1, 2, 3]', 'Getters leem; setters alteram.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada método ao uso correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada ameaça do Espião para a defesa.', NULL, '{\"itens\": [\"Atributo public\", \"Alteração sem validação\", \"Classe perdida no projeto\", \"Acesso direto ao campo\"], \"alvos\": [\"Usar private\", \"Usar setter controlado\", \"Usar package organizado\", \"Usar getter/setter\"]}', '[0, 1, 2, 3]', 'Encapsulamento combina proteção de estado e organização.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada ameaça do Espião para a defesa.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada termo ao conceito correto.', NULL, '{\"itens\": [\"Estado interno\", \"Getter\", \"Setter\", \"Package\"], \"alvos\": [\"Dados do objeto\", \"Método de leitura\", \"Método de alteração\", \"Organização de classes\"]}', '[0, 1, 2, 3]', 'São termos centrais da fase Encapsulamento.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada termo ao conceito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada linha ao papel no castelo.', NULL, '{\"itens\": [\"private int vida;\", \"public int getVida()\", \"public void setVida(int vida)\", \"package br.com.jogo;\"], \"alvos\": [\"Campo protegido\", \"Leitura controlada\", \"Alteração controlada\", \"Organização\"]}', '[0, 1, 2, 3]', 'Cada linha contribui para o encapsulamento ou organização.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada linha ao papel no castelo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'multipla', 'poo', 'O Espião dos Atributos tenta ler o segredo vida diretamente. Qual modificador melhor protege o estado interno?', NULL, '[\"private\",\"public\",\"static\",\"new\"]', '0', 'private restringe o acesso direto ao interior da própria classe.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='O Espião dos Atributos tenta ler o segredo vida diretamente. Qual modificador melhor protege o estado interno?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'multipla', 'poo', 'Qual método costuma revelar um atributo privado sem abrir o cofre inteiro?', NULL, '[\"getter\",\"construtor vazio\",\"pacote\",\"lambda\"]', '0', 'Getters consultam atributos mantendo controle da classe.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Qual método costuma revelar um atributo privado sem abrir o cofre inteiro?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'multipla', 'poo', 'Qual método costuma alterar um atributo privado com validação?', NULL, '[\"setter\",\"extends\",\"interface\",\"main\"]', '0', 'Setters permitem controlar mudanças no estado interno.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Qual método costuma alterar um atributo privado com validação?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'multipla', 'poo', 'Na Cidadela, public significa que o membro pode ser acessado:', NULL, '[\"de qualquer lugar visível\",\"apenas pela própria classe\",\"apenas por subclasses\",\"somente por SQL\"]', '0', 'public deixa o membro acessível por outras classes.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Na Cidadela, public significa que o membro pode ser acessado:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'multipla', 'poo', 'Qual recurso organiza classes em grupos e ajuda a controlar nomes e visibilidade?', NULL, '[\"pacote\",\"construtor\",\"objeto null\",\"operador ternário\"]', '0', 'Pacotes organizam classes e influenciam a visibilidade padrão/protected.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Qual recurso organiza classes em grupos e ajuda a controlar nomes e visibilidade?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'vf', 'poo', 'Encapsulamento protege o estado interno e expõe uma interface controlada de uso.', NULL, NULL, 'true', 'Esse é o objetivo central do encapsulamento.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Encapsulamento protege o estado interno e expõe uma interface controlada de uso.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'vf', 'poo', 'Atributos public são sempre a melhor forma de garantir validações.', NULL, NULL, 'false', 'Atributos public permitem alteração direta e enfraquecem validações.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Atributos public são sempre a melhor forma de garantir validações.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'vf', 'poo', 'Getters e setters podem impor regras antes de ler ou alterar dados.', NULL, NULL, 'true', 'Eles controlam o acesso ao estado interno.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Getters e setters podem impor regras antes de ler ou alterar dados.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'vf', 'poo', 'protected permite acesso por subclasses e também por classes do mesmo pacote.', NULL, NULL, 'true', 'Em Java, protected alcança subclasses e pacote.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='protected permite acesso por subclasses e também por classes do mesmo pacote.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'vf', 'poo', 'O modificador private permite acesso direto por qualquer classe do reino.', NULL, NULL, 'false', 'private restringe acesso à própria classe.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='O modificador private permite acesso direto por qualquer classe do reino.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'completar', 'poo', 'Complete o lacre do cofre: para esconder um atributo, use ___.', NULL, NULL, '[\"private\"]', 'private é o modificador mais restritivo.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Complete o lacre do cofre: para esconder um atributo, use ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'completar', 'poo', 'Complete: o método de leitura de nome costuma começar com ___.', NULL, NULL, '[\"get\",\"getNome\",\"getter\",\"Getter\"]', 'Getters geralmente usam o prefixo get.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Complete: o método de leitura de nome costuma começar com ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'completar', 'poo', 'Complete: o método de alteração de nome costuma começar com ___.', NULL, NULL, '[\"set\",\"setNome\",\"setter\",\"Setter\"]', 'Setters geralmente usam o prefixo set.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Complete: o método de alteração de nome costuma começar com ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'completar', 'poo', 'Complete: classes organizadas no mesmo agrupamento pertencem a um ___.', NULL, NULL, '[\"pacote\",\"package\",\"Pacote\"]', 'Pacotes agrupam classes relacionadas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Complete: classes organizadas no mesmo agrupamento pertencem a um ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'completar', 'poo', 'Complete: o acesso intermediário entre public e private, útil para subclasses, é ___.', NULL, NULL, '[\"protected\"]', 'protected permite acesso por herança e pacote.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Complete: o acesso intermediário entre public e private, útil para subclasses, é ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'ordenar', 'poo', 'Ordene o ritual para proteger a energia de um Personagem.', NULL, '[\"Criar getter se a leitura for necessária\",\"Declarar o atributo como private\",\"Criar setter com validação\",\"Impedir alteração direta externa\"]', '[1,0,2,3]', 'O encapsulamento começa escondendo o atributo e expondo acesso controlado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Ordene o ritual para proteger a energia de um Personagem.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'ordenar', 'poo', 'Ordene a criação de um setter seguro para vida.', NULL, '[\"Atribuir ao atributo somente se válido\",\"Receber o novo valor\",\"Testar se o valor não é negativo\"]', '[1,2,0]', 'Um setter deve receber, validar e só então alterar o estado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Ordene a criação de um setter seguro para vida.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'ordenar', 'poo', 'Ordene a organização de classes em pacotes.', NULL, '[\"Declarar package no topo do arquivo\",\"Escolher um nome coerente de pacote\",\"Colocar a classe na pasta correspondente\",\"Importar quando usar de outro pacote\"]', '[1,2,0,3]', 'Nome, pasta, declaração e importação precisam ser coerentes.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Ordene a organização de classes em pacotes.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'ordenar', 'poo', 'Ordene o acesso correto ao atributo privado mana.', NULL, '[\"Código externo chama getMana()\",\"Classe mantém mana como private\",\"getMana retorna o valor permitido\"]', '[1,0,2]', 'O acesso externo deve passar pelo método público.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Ordene o acesso correto ao atributo privado mana.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'ordenar', 'poo', 'Ordene a análise de visibilidade feita pelo Espião.', NULL, '[\"Precisa ser visto por todos? public\",\"Só a própria classe usa? private\",\"Subclasses precisam acessar? protected\",\"Definir o menor acesso suficiente\"]', '[3,1,2,0]', 'Boa prática é conceder o menor acesso necessário.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Ordene a análise de visibilidade feita pelo Espião.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'erro', 'poo', 'O Espião dos Atributos sorri ao ver este código. Qual é a falha?', 'public class Personagem {\n  public int vida;\n}', '[\"O estado ficou exposto para alteração direta\",\"public impede compilação\",\"int não pode ser atributo\",\"Faltou usar interface\"]', '0', 'Atributo public quebra o encapsulamento do estado interno.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='O Espião dos Atributos sorri ao ver este código. Qual é a falha?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'erro', 'poo', 'Qual problema existe neste setter?', 'public void setVida(int vida) {\n  this.vida = vida;\n}', '[\"Não valida valores como vida negativa\",\"Setter nunca pode existir\",\"this é proibido em setter\",\"private não aceita int\"]', '0', 'Sem validação, estados inválidos podem entrar no objeto.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Qual problema existe neste setter?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'erro', 'poo', 'Qual falha de encapsulamento ocorre ao retornar uma lista interna modificável?', 'public List<Item> getItens() {\n  return itens;\n}', '[\"Código externo pode alterar a lista sem passar por regras da classe\",\"List não existe em Java\",\"Getter deve sempre ser private\",\"O método deveria ser construtor\"]', '0', 'Expor coleção interna mutável permite burlar validações.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Qual falha de encapsulamento ocorre ao retornar uma lista interna modificável?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'erro', 'poo', 'O aprendiz colocou todas as classes no pacote padrão sem organização. Qual risco aparece?', NULL, '[\"Perda de organização e conflitos de nomes\",\"Objetos deixam de existir\",\"private vira public\",\"new para de funcionar\"]', '0', 'Pacotes organizam o projeto e evitam conflitos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='O aprendiz colocou todas as classes no pacote padrão sem organização. Qual risco aparece?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'erro', 'poo', 'Qual erro conceitual existe em deixar validação apenas na tela e não no objeto?', NULL, '[\"Outras partes do sistema podem alterar o estado sem regra\",\"A classe fica mais encapsulada\",\"O código compila mais rápido sempre\",\"protected vira public\"]', '0', 'As regras do estado devem estar próximas do objeto que o controla.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Qual erro conceitual existe em deixar validação apenas na tela e não no objeto?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada modificador ao alcance mais adequado.', NULL, '{\"itens\":[\"private\",\"public\",\"protected\",\"sem modificador\"],\"alvos\":[\"Somente a própria classe\",\"Acesso geral\",\"Subclasses e pacote\",\"Acesso de pacote\"]}', '[0,1,2,3]', 'Cada modificador define um nível de visibilidade.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada modificador ao alcance mais adequado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada membro ao papel no encapsulamento.', NULL, '{\"itens\":[\"atributo private\",\"getter\",\"setter\",\"pacote\"],\"alvos\":[\"Estado protegido\",\"Leitura controlada\",\"Alteração controlada\",\"Organização de classes\"]}', '[0,1,2,3]', 'Encapsulamento combina estado oculto, acesso controlado e organização.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada membro ao papel no encapsulamento.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada ameaça do Espião à defesa correta.', NULL, '{\"itens\":[\"Alteração direta\",\"Valor inválido\",\"Classe perdida no reino\",\"Exposição de lista interna\"],\"alvos\":[\"private\",\"validação no setter\",\"package organizado\",\"cópia ou lista imutável\"]}', '[0,1,2,3]', 'Cada risco pede uma barreira específica.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada ameaça do Espião à defesa correta.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada método ao uso correto.', NULL, '{\"itens\":[\"getVida()\",\"setVida(int)\",\"getNome()\",\"setNome(String)\"],\"alvos\":[\"Consultar vida\",\"Alterar vida\",\"Consultar nome\",\"Alterar nome\"]}', '[0,1,2,3]', 'Prefixos get e set indicam leitura e alteração.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada método ao uso correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada decisão à boa prática.', NULL, '{\"itens\":[\"Dado interno sensível\",\"Operação pública necessária\",\"Acesso só por herança\",\"Classes relacionadas\"],\"alvos\":[\"private\",\"método public\",\"protected\",\"mesmo pacote\"]}', '[0,1,2,3]', 'A visibilidade deve refletir a necessidade real de acesso.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada decisão à boa prática.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'multipla', 'poo', 'Qual modificador permite acesso ao atributo de QUALQUER lugar?', NULL, '[\"private\",\"protected\",\"public\",\"final\"]', '2', 'public deixa o membro acessível de dentro e de fora da classe.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Qual modificador permite acesso ao atributo de QUALQUER lugar?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'vf', 'poo', 'Getters e setters dão acesso controlado a atributos privados.', NULL, NULL, 'true', 'Eles são a porta oficial: leem/alteram o estado interno com validação, sem expor o atributo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Getters e setters dão acesso controlado a atributos privados.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'completar', 'poo', 'Torne o atributo $saldo inacessível de fora da classe:', 'class Conta { _______ $saldo; }', NULL, '[\"private\"]', 'private restringe o acesso ao interior da própria classe.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Torne o atributo $saldo inacessível de fora da classe:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'multipla', 'poo', 'Por que manter atributos private com getters/setters?', NULL, '[\"Para digitar mais\",\"Para controlar e validar o acesso ao estado interno\",\"Para deixar tudo público\",\"Para remover métodos\"]', '1', 'Encapsular permite validar mudanças e proteger invariantes do objeto.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Por que manter atributos private com getters/setters?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'erro', 'poo', 'O que fere o encapsulamento?', NULL, '[\"Usar getters e setters\",\"Deixar todos os atributos public e alterá-los direto de fora\",\"Marcar atributos como private\",\"Validar dados no setter\"]', '1', 'Expor tudo como public deixa o estado interno à mercê de qualquer um — o oposto de encapsular.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='O que fere o encapsulamento?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'multipla', 'poo', 'O modificador protected permite acesso:', NULL, '[\"Só de fora da classe\",\"Na própria classe e nas suas subclasses\",\"De qualquer lugar\",\"Em nenhum lugar\"]', '1', 'protected libera o acesso à classe e às que a estendem, mas não ao mundo externo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='O modificador protected permite acesso:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'multipla', 'poo', 'Qual palavra-chave indica herança em PHP?', NULL, '[\"implements\",\"extends\",\"uses\",\"inherits\"]', '1', 'class Filha extends Pai cria uma relação de herança.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Qual palavra-chave indica herança em PHP?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'vf', 'poo', 'Composição é quando um objeto contém outros objetos como parte de seu estado.', NULL, NULL, 'true', 'Compor é montar um objeto a partir de outros (tem-um), em vez de herdar (é-um).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Composição é quando um objeto contém outros objetos como parte de seu estado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'multipla', 'poo', 'Por que costuma-se preferir composição à herança?', NULL, '[\"Herança é sempre proibida\",\"Composição é mais flexível e evita acoplamento rígido\",\"Composição é só mais rápida de digitar\",\"Herança não existe em PHP\"]', '1', 'Composição reduz o acoplamento e dá mais flexibilidade para mudar comportamentos.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Por que costuma-se preferir composição à herança?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'ordenar', 'poo', 'Ordene da classe mais genérica para a mais específica:', NULL, '[\"Mago\",\"Personagem\",\"SerVivo\"]', '[2,1,0]', 'SerVivo (geral) → Personagem → Mago (específico).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Ordene da classe mais genérica para a mais específica:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'multipla', 'poo', 'A Quimera da Herança pergunta: qual palavra faz uma classe filha herdar de uma classe mãe?', NULL, '[\"extends\",\"implements\",\"private\",\"new\"]', '0', 'extends declara herança entre classes.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='A Quimera da Herança pergunta: qual palavra faz uma classe filha herdar de uma classe mãe?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'multipla', 'poo', 'Qual palavra permite chamar o construtor ou método da classe mãe?', NULL, '[\"super\",\"this\",\"static\",\"package\"]', '0', 'super referencia a superclasse.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Qual palavra permite chamar o construtor ou método da classe mãe?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'multipla', 'poo', 'Quando diferentes subclasses respondem ao mesmo método de formas diferentes, ocorre:', NULL, '[\"polimorfismo\",\"concatenação\",\"pacote padrão\",\"overflow\"]', '0', 'Polimorfismo permite múltiplas formas para uma mesma operação.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Quando diferentes subclasses respondem ao mesmo método de formas diferentes, ocorre:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'multipla', 'poo', 'Qual anotação indica que um método pretende sobrescrever outro da superclasse?', NULL, '[\"@Override\",\"@Entity\",\"@Table\",\"@GetterSQL\"]', '0', '@Override sinaliza sobrescrita de método.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Qual anotação indica que um método pretende sobrescrever outro da superclasse?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'multipla', 'poo', 'Quando um Cavaleiro possui uma Espada como parte de sua estrutura, isso é exemplo de:', NULL, '[\"composição\",\"herança obrigatória\",\"método abstrato\",\"exceção verificada\"]', '0', 'Composição modela relação tem-um, usando objetos como partes.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Quando um Cavaleiro possui uma Espada como parte de sua estrutura, isso é exemplo de:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'vf', 'poo', 'Herança é adequada quando existe relação é-um entre classes.', NULL, NULL, 'true', 'Uma subclasse deve ser um tipo da superclasse.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Herança é adequada quando existe relação é-um entre classes.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'vf', 'poo', 'Composição representa reúso por meio de objetos contidos em outros objetos.', NULL, NULL, 'true', 'Composição modela relação tem-um.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Composição representa reúso por meio de objetos contidos em outros objetos.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'vf', 'poo', '@Override cria automaticamente uma nova classe mãe.', NULL, NULL, 'false', '@Override apenas verifica/sinaliza sobrescrita de método.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='@Override cria automaticamente uma nova classe mãe.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'vf', 'poo', 'super pode ser usado para acessar comportamento da superclasse.', NULL, NULL, 'true', 'super permite chamar construtores e métodos herdados.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='super pode ser usado para acessar comportamento da superclasse.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'vf', 'poo', 'Herança deve ser usada sempre que uma classe precisar de qualquer método de outra.', NULL, NULL, 'false', 'Reúso cego por herança gera acoplamento; composição pode ser melhor.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Herança deve ser usada sempre que uma classe precisar de qualquer método de outra.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'completar', 'poo', 'Complete o pacto da Quimera: class Dragao ___ Monstro.', NULL, NULL, '[\"extends\"]', 'extends declara a superclasse.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Complete o pacto da Quimera: class Dragao ___ Monstro.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'completar', 'poo', 'Complete: para chamar o construtor da classe mãe, usa-se ___.', NULL, NULL, '[\"super\",\"super()\"]', 'super() chama o construtor da superclasse.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Complete: para chamar o construtor da classe mãe, usa-se ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'completar', 'poo', 'Complete a anotação de sobrescrita: ___.', NULL, NULL, '[\"@Override\",\"Override\"]', '@Override indica intenção de sobrescrever.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Complete a anotação de sobrescrita: ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'completar', 'poo', 'Complete: quando uma classe possui outra como parte, preferimos chamar isso de ___.', NULL, NULL, '[\"composição\",\"composicao\",\"Composição\",\"Composicao\"]', 'Composição é relação tem-um.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Complete: quando uma classe possui outra como parte, preferimos chamar isso de ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'completar', 'poo', 'Complete: a capacidade de tratar subclasses pela referência da superclasse chama-se ___.', NULL, NULL, '[\"polimorfismo\",\"Polimorfismo\"]', 'Polimorfismo permite usar objetos diferentes por um tipo comum.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Complete: a capacidade de tratar subclasses pela referência da superclasse chama-se ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'ordenar', 'poo', 'Ordene o ritual para criar uma subclasse correta.', NULL, '[\"Sobrescrever métodos necessários\",\"Definir a superclasse\",\"Declarar a subclasse com extends\",\"Chamar super quando precisar inicializar a parte herdada\"]', '[1,2,3,0]', 'A herança parte da superclasse, declara extends, inicializa com super e especializa comportamento.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Ordene o ritual para criar uma subclasse correta.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'ordenar', 'poo', 'Ordene a decisão entre herança e composição.', NULL, '[\"Perguntar se é-um\",\"Se sim, considerar herança\",\"Perguntar se tem-um\",\"Se sim, considerar composição\"]', '[0,1,2,3]', 'Herança modela é-um; composição modela tem-um.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Ordene a decisão entre herança e composição.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'ordenar', 'poo', 'Ordene o polimorfismo no campo de batalha.', NULL, '[\"Criar subclasses específicas\",\"Declarar referência do tipo base\",\"Sobrescrever o método atacar\",\"Chamar atacar sem conhecer a classe concreta\"]', '[0,2,1,3]', 'Subclasses especializam o método e podem ser usadas pelo tipo base.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Ordene o polimorfismo no campo de batalha.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'ordenar', 'poo', 'Ordene a construção de um objeto Guerreiro com Arma por composição.', NULL, '[\"Criar classe Arma\",\"Criar atributo Arma em Guerreiro\",\"Instanciar Arma\",\"Passar ou atribuir Arma ao Guerreiro\"]', '[0,1,2,3]', 'Composição usa objetos como partes de outros objetos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Ordene a construção de um objeto Guerreiro com Arma por composição.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'ordenar', 'poo', 'Ordene a correção de uma herança abusiva.', NULL, '[\"Identificar métodos herdados sem sentido\",\"Extrair objeto auxiliar\",\"Substituir extends por atributo composto quando couber\",\"Delegar a operação ao objeto auxiliar\"]', '[0,1,2,3]', 'Quando não há relação é-um, composição reduz acoplamento.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Ordene a correção de uma herança abusiva.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'erro', 'poo', 'A Quimera mostra este código. Qual erro de modelagem aparece?', 'class Motor {}\nclass Carro extends Motor {}', '[\"Carro não é um Motor; deveria possuir um Motor\",\"extends nunca compila\",\"Motor deve ser interface sempre\",\"Faltou usar SQL\"]', '0', 'Carro tem um motor; composição é mais adequada que herança.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='A Quimera mostra este código. Qual erro de modelagem aparece?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'erro', 'poo', 'Qual problema existe neste método?', 'class Mago extends Personagem {\n  @Override\n  public void ataca() {}\n}', '[\"Pode não sobrescrever atacar se o nome correto for atacar\",\"@Override cria atributo privado\",\"Método com void nunca compila\",\"extends impede métodos\"]', '0', '@Override ajuda a detectar assinaturas incorretas de sobrescrita.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Qual problema existe neste método?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'erro', 'poo', 'Qual falha conceitual existe em usar herança apenas para reaproveitar código sem relação é-um?', NULL, '[\"Aumenta acoplamento e pode criar hierarquia falsa\",\"Garante coesão perfeita\",\"Elimina a necessidade de testes\",\"Transforma objeto em pacote\"]', '0', 'Herança deve representar especialização real, não só cópia de código.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Qual falha conceitual existe em usar herança apenas para reaproveitar código sem relação é-um?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'erro', 'poo', 'O aprendiz chama this() querendo acessar a classe mãe. Qual é a correção?', NULL, '[\"Usar super para a superclasse\",\"Usar static para tudo\",\"Usar package no método\",\"Usar private no construtor\"]', '0', 'this refere-se ao objeto atual; super refere-se à superclasse.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='O aprendiz chama this() querendo acessar a classe mãe. Qual é a correção?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'erro', 'poo', 'Na composição, o Guerreiro tem uma Arma, mas o método atacar ignora a Arma. Qual problema de projeto?', NULL, '[\"A composição existe, mas o comportamento não delega à parte composta\",\"Composição exige extends\",\"Arma precisa ser SQL\",\"Todo atributo composto deve ser public\"]', '0', 'Composição deve ser usada de modo coerente, delegando responsabilidades às partes.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Na composição, o Guerreiro tem uma Arma, mas o método atacar ignora a Arma. Qual problema de projeto?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'arrastar', 'poo', 'Arraste cada símbolo da Quimera ao significado.', NULL, '{\"itens\":[\"extends\",\"super\",\"@Override\",\"composição\"],\"alvos\":[\"Herda de uma classe\",\"Acessa a superclasse\",\"Sobrescreve método\",\"Relação tem-um\"]}', '[0,1,2,3]', 'Esses elementos modelam herança, sobrescrita e composição.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Arraste cada símbolo da Quimera ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'arrastar', 'poo', 'Arraste cada relação ao melhor modelo.', NULL, '{\"itens\":[\"Dragao é Monstro\",\"Carro tem Motor\",\"Pedido tem Itens\",\"Cachorro é Animal\"],\"alvos\":[\"herança\",\"composição\",\"composição\",\"herança\"]}', '[0,1,2,3]', 'é-um favorece herança; tem-um favorece composição.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Arraste cada relação ao melhor modelo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'arrastar', 'poo', 'Arraste cada conceito ao efeito no reúso.', NULL, '{\"itens\":[\"Herança\",\"Composição\",\"Polimorfismo\",\"Sobrescrita\"],\"alvos\":[\"Reúso por especialização\",\"Reúso por partes\",\"Uso por tipo comum\",\"Novo comportamento no filho\"]}', '[0,1,2,3]', 'Cada mecanismo reutiliza ou especializa comportamento de forma diferente.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Arraste cada conceito ao efeito no reúso.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'arrastar', 'poo', 'Arraste cada criatura ao alerta de projeto.', NULL, '{\"itens\":[\"Quimera da Herança\",\"Armadura Composta\",\"Eco Polimórfico\",\"Selo Override\"],\"alvos\":[\"Evitar hierarquia falsa\",\"Preferir partes quando há tem-um\",\"Mesmo chamado, formas diferentes\",\"Verificar sobrescrita\"]}', '[0,1,2,3]', 'A fase ensina a escolher herança ou composição com cuidado.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Arraste cada criatura ao alerta de projeto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'arrastar', 'poo', 'Arraste cada trecho ao diagnóstico.', NULL, '{\"itens\":[\"class A extends B\",\"super()\",\"new Motor() em Carro\",\"List<Personagem> com Mago e Guerreiro\"],\"alvos\":[\"Herança\",\"Construtor da superclasse\",\"Composição\",\"Polimorfismo\"]}', '[0,1,2,3]', 'Os trechos representam recursos clássicos de POO em Java.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Arraste cada trecho ao diagnóstico.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'multipla', 'poo', 'Herança expressa qual relação entre as classes?', NULL, '[\"tem-um (has-a)\",\"é-um (is-a)\",\"usa-um\",\"faz-um\"]', '1', 'Cachorro é-um Animal: herança modela especialização (is-a).', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Herança expressa qual relação entre as classes?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'multipla', 'poo', 'Composição expressa qual relação?', NULL, '[\"é-um (is-a)\",\"tem-um (has-a)\",\"igual-a\",\"maior-que\"]', '1', 'Carro tem-um Motor: composição monta um objeto a partir de outros (has-a).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Composição expressa qual relação?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'vf', 'poo', 'Em PHP, uma classe pode herdar de apenas uma classe pai (herança simples).', NULL, NULL, 'true', 'PHP não tem herança múltipla de classes; para múltiplos contratos, usam-se interfaces ou traits.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Em PHP, uma classe pode herdar de apenas uma classe pai (herança simples).') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'completar', 'poo', 'Complete para chamar o construtor da classe pai:', 'parent::___________();', NULL, '[\"__construct\"]', 'parent::__construct() reaproveita a inicialização definida na superclasse.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Complete para chamar o construtor da classe pai:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'multipla', 'poo', 'Sobrescrever (override) um método significa:', NULL, '[\"Apagar o método do pai\",\"Redefinir, na subclasse, um método herdado\",\"Criar um atributo novo\",\"Tornar o método privado\"]', '1', 'A subclasse fornece sua própria versão de um método já existente na superclasse.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Sobrescrever (override) um método significa:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'multipla', 'poo', 'O que uma interface define?', NULL, '[\"A implementação completa\",\"Um contrato de métodos que a classe deve implementar\",\"Atributos privados\",\"Um laço\"]', '1', 'A interface é um contrato: lista métodos que a classe se compromete a implementar.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='O que uma interface define?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'completar', 'poo', 'Complete para a classe assinar o contrato Atacavel:', 'class Heroi ________ Atacavel { }', NULL, '[\"implements\"]', 'implements faz a classe cumprir uma interface.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Complete para a classe assinar o contrato Atacavel:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'vf', 'poo', 'Uma classe pode implementar várias interfaces ao mesmo tempo.', NULL, NULL, 'true', 'Sim, diferentemente da herança simples, várias interfaces são permitidas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Uma classe pode implementar várias interfaces ao mesmo tempo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'multipla', 'poo', 'O Contrato Fantasma oferece uma promessa sem corpo. Em Java, esse contrato costuma ser declarado com:', NULL, '[\"interface\",\"extends final\",\"new private\",\"package static\"]', '0', 'interface declara um contrato de métodos.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='O Contrato Fantasma oferece uma promessa sem corpo. Em Java, esse contrato costuma ser declarado com:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'multipla', 'poo', 'Qual palavra faz uma classe cumprir uma interface?', NULL, '[\"implements\",\"extends\",\"super\",\"null\"]', '0', 'implements indica que a classe implementa o contrato da interface.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Qual palavra faz uma classe cumprir uma interface?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'multipla', 'poo', 'Uma interface serve principalmente para definir:', NULL, '[\"um contrato de comportamento\",\"um banco de dados\",\"um valor numérico fixo\",\"uma pasta obrigatória\"]', '0', 'Interfaces definem operações que classes devem fornecer.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Uma interface serve principalmente para definir:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'multipla', 'poo', 'Em Java, uma classe pode implementar:', NULL, '[\"múltiplas interfaces\",\"apenas interfaces com construtor\",\"somente uma interface e nenhuma classe\",\"interfaces apenas se forem public static\"]', '0', 'Java permite implementar várias interfaces.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Em Java, uma classe pode implementar:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'multipla', 'poo', 'Um método sem implementação, exigido pelo contrato, é chamado de:', NULL, '[\"abstrato\",\"estático final obrigatório\",\"SQL\",\"construtor\"]', '0', 'Métodos abstratos declaram assinatura sem corpo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Um método sem implementação, exigido pelo contrato, é chamado de:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'vf', 'poo', 'Interfaces ajudam a programar para contratos, não apenas para classes concretas.', NULL, NULL, 'true', 'O código pode depender de um tipo abstrato/contratual.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Interfaces ajudam a programar para contratos, não apenas para classes concretas.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'vf', 'poo', 'implements é usado quando uma classe assume cumprir uma interface.', NULL, NULL, 'true', 'implements liga classe concreta ao contrato.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='implements é usado quando uma classe assume cumprir uma interface.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'vf', 'poo', 'Uma interface tradicional deve ter construtores públicos para instanciar objetos.', NULL, NULL, 'false', 'Interfaces não são instanciadas diretamente por construtor.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Uma interface tradicional deve ter construtores públicos para instanciar objetos.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'vf', 'poo', 'Uma classe que implementa uma interface deve fornecer os métodos exigidos, salvo se também for abstrata.', NULL, NULL, 'true', 'Classes concretas precisam implementar os métodos abstratos.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Uma classe que implementa uma interface deve fornecer os métodos exigidos, salvo se também for abstrata.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'vf', 'poo', 'Interfaces impedem qualquer forma de polimorfismo.', NULL, NULL, 'false', 'Interfaces favorecem polimorfismo por contrato.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Interfaces impedem qualquer forma de polimorfismo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'completar', 'poo', 'Complete a inscrição do contrato: public ___ Curavel { void curar(); }', NULL, NULL, '[\"interface\"]', 'interface declara um contrato.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Complete a inscrição do contrato: public ___ Curavel { void curar(); }') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'completar', 'poo', 'Complete: class Pocao ___ Curavel.', NULL, NULL, '[\"implements\"]', 'implements faz a classe cumprir a interface.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Complete: class Pocao ___ Curavel.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'completar', 'poo', 'Complete: um método sem corpo em contrato é um método ___.', NULL, NULL, '[\"abstrato\",\"abstract\",\"Abstrato\"]', 'Métodos abstratos exigem implementação posterior.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Complete: um método sem corpo em contrato é um método ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'completar', 'poo', 'Complete: interfaces permitem trabalhar com múltiplos ___ sem herança múltipla de classes.', NULL, NULL, '[\"contratos\",\"Contratos\"]', 'Uma classe pode cumprir vários contratos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Complete: interfaces permitem trabalhar com múltiplos ___ sem herança múltipla de classes.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'completar', 'poo', 'Complete: ao implementar uma interface, a classe concreta deve fornecer os ___ exigidos.', NULL, NULL, '[\"métodos\",\"metodos\",\"métodos abstratos\",\"metodos abstratos\"]', 'O contrato exige implementação dos métodos declarados.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Complete: ao implementar uma interface, a classe concreta deve fornecer os ___ exigidos.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'ordenar', 'poo', 'Ordene o ritual para cumprir uma Interface Secreta.', NULL, '[\"Declarar a interface com os métodos\",\"Criar classe concreta\",\"Usar implements\",\"Implementar os métodos exigidos\"]', '[0,1,2,3]', 'Primeiro existe o contrato; depois a classe o assume e implementa.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Ordene o ritual para cumprir uma Interface Secreta.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'ordenar', 'poo', 'Ordene o uso polimórfico por interface.', NULL, '[\"Declarar variável do tipo da interface\",\"Criar objeto de classe implementadora\",\"Atribuir objeto à variável do contrato\",\"Chamar o método declarado no contrato\"]', '[0,1,2,3]', 'A referência pela interface permite usar qualquer implementação compatível.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Ordene o uso polimórfico por interface.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'ordenar', 'poo', 'Ordene a criação de múltiplos contratos para um Guardião.', NULL, '[\"Definir interface Movel\",\"Definir interface Atacante\",\"Criar classe Guardiao\",\"Implementar Movel e Atacante\"]', '[0,1,2,3]', 'Uma classe pode cumprir mais de uma interface.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Ordene a criação de múltiplos contratos para um Guardião.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'ordenar', 'poo', 'Ordene a correção de uma classe que esqueceu um método da interface.', NULL, '[\"Ler os métodos exigidos\",\"Localizar a classe implementadora\",\"Adicionar o método ausente\",\"Testar a chamada pelo tipo da interface\"]', '[1,0,2,3]', 'Classe concreta precisa fornecer o contrato completo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Ordene a correção de uma classe que esqueceu um método da interface.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'ordenar', 'poo', 'Ordene a separação entre contrato e execução.', NULL, '[\"Interface declara o que deve existir\",\"Classe decide como fazer\",\"Código cliente usa o contrato\",\"Implementação pode ser trocada\"]', '[0,1,2,3]', 'Interfaces reduzem dependência da classe concreta.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Ordene a separação entre contrato e execução.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'erro', 'poo', 'O Contrato Fantasma rejeitou este código. Qual é a falha?', 'public interface Curavel {\n  public Curavel() {}\n}', '[\"Interface não deve declarar construtor de instância\",\"Faltou usar extends\",\"Construtor precisa ser static\",\"interface não aceita public\"]', '0', 'Interfaces não são instanciadas diretamente por construtores.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='O Contrato Fantasma rejeitou este código. Qual é a falha?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'erro', 'poo', 'Qual problema aparece aqui?', 'interface Atacante { void atacar(); }\nclass Guerreiro implements Atacante { }', '[\"Guerreiro não implementou o método atacar\",\"implements deveria ser private\",\"Interface precisa de new\",\"void não pode aparecer em interface\"]', '0', 'Classe concreta deve implementar os métodos da interface.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Qual problema aparece aqui?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'erro', 'poo', 'O aprendiz tentou herdar duas classes para ganhar dois poderes. Qual alternativa Java é mais adequada?', NULL, '[\"Usar uma classe base e múltiplas interfaces quando forem contratos\",\"Usar extends A, B em qualquer classe\",\"Trocar objetos por SQL\",\"Declarar tudo public static\"]', '0', 'Java não usa herança múltipla de classes, mas aceita múltiplas interfaces.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='O aprendiz tentou herdar duas classes para ganhar dois poderes. Qual alternativa Java é mais adequada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'erro', 'poo', 'Qual falha conceitual existe em depender sempre da classe concreta DragaoDeFogo em vez da interface Atacante?', NULL, '[\"O código fica menos flexível para trocar implementações\",\"A interface deixa de compilar\",\"Objetos não podem atacar\",\"Atacante vira atributo private\"]', '0', 'Depender de contrato facilita substituição e polimorfismo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Qual falha conceitual existe em depender sempre da classe concreta DragaoDeFogo em vez da interface Atacante?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'erro', 'poo', 'O método do contrato foi implementado com nome diferente. Qual é a consequência?', 'interface Abrivel { void abrir(); }\nclass Bau implements Abrivel {\n  public void abre() {}\n}', '[\"A classe não cumpre o contrato abrir\",\"abre substitui abrir automaticamente\",\"Interface permite qualquer nome\",\"Bau vira pacote\"]', '0', 'A assinatura precisa corresponder ao método declarado na interface.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='O método do contrato foi implementado com nome diferente. Qual é a consequência?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'arrastar', 'poo', 'Arraste cada elemento das Interfaces Secretas ao significado.', NULL, '{\"itens\":[\"interface\",\"implements\",\"método abstrato\",\"contrato\"],\"alvos\":[\"Define comportamentos esperados\",\"Classe assume cumprir\",\"Assinatura sem corpo\",\"Promessa de uso\"]}', '[0,1,2,3]', 'Interfaces funcionam como contratos de comportamento.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Arraste cada elemento das Interfaces Secretas ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'arrastar', 'poo', 'Arraste cada trecho ao conceito.', NULL, '{\"itens\":[\"interface Curavel\",\"class Pocao implements Curavel\",\"void curar();\",\"Curavel c = new Pocao()\"],\"alvos\":[\"Contrato\",\"Implementação\",\"Método exigido\",\"Polimorfismo\"]}', '[0,1,2,3]', 'A interface permite usar objetos por um contrato comum.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Arraste cada trecho ao conceito.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'arrastar', 'poo', 'Arraste cada contrato ao guardião compatível.', NULL, '{\"itens\":[\"Voador\",\"Nadador\",\"Atacante\",\"Curavel\"],\"alvos\":[\"DragaoAlado\",\"Sereia\",\"Guerreiro\",\"Pocao\"]}', '[0,1,2,3]', 'Classes diferentes podem implementar contratos diferentes.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Arraste cada contrato ao guardião compatível.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'arrastar', 'poo', 'Arraste cada vantagem da interface ao efeito.', NULL, '{\"itens\":[\"Baixo acoplamento\",\"Troca de implementação\",\"Múltiplos contratos\",\"Polimorfismo\"],\"alvos\":[\"Depender menos da classe concreta\",\"Substituir classe sem mudar cliente\",\"Cumprir vários papéis\",\"Usar pelo tipo do contrato\"]}', '[0,1,2,3]', 'Interfaces ajudam a organizar e flexibilizar o código.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Arraste cada vantagem da interface ao efeito.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'arrastar', 'poo', 'Arraste cada erro do Contrato Fantasma à correção.', NULL, '{\"itens\":[\"Método ausente\",\"Nome diferente\",\"Dependência concreta\",\"Tentativa de construtor\"],\"alvos\":[\"Implementar assinatura\",\"Usar mesmo nome e parâmetros\",\"Usar tipo da interface\",\"Remover construtor da interface\"]}', '[0,1,2,3]', 'As correções preservam o contrato e o polimorfismo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Arraste cada erro do Contrato Fantasma à correção.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'multipla', 'poo', 'O que uma interface contém?', NULL, '[\"A implementação completa dos métodos\",\"Assinaturas de métodos sem implementação (um contrato)\",\"Apenas atributos privados\",\"Um laço de repetição\"]', '1', 'A interface lista o QUE deve existir; cada classe decide o COMO ao implementá-la.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='O que uma interface contém?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'vf', 'poo', 'Uma mesma classe pode implementar várias interfaces ao mesmo tempo.', NULL, NULL, 'true', 'Diferente da herança de classe, assinar vários contratos (interfaces) é permitido.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Uma mesma classe pode implementar várias interfaces ao mesmo tempo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'multipla', 'poo', 'Polimorfismo permite:', NULL, '[\"Tratar objetos de tipos diferentes pela mesma interface\",\"Criar atributos privados\",\"Eliminar classes\",\"Acelerar o banco de dados\"]', '0', 'Vários tipos respondem à mesma chamada à sua maneira — código que fala com a interface, não com a classe concreta.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Polimorfismo permite:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'completar', 'poo', 'Faça a classe Pato assinar o contrato Nadador:', 'class Pato __________ Nadador { }', NULL, '[\"implements\"]', 'implements obriga a classe a fornecer os métodos declarados na interface.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Faça a classe Pato assinar o contrato Nadador:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'Por que uma classe-deus (God Class) é um problema?', NULL, '[\"É pequena demais\",\"Concentra responsabilidades demais e fica difícil de manter\",\"Usa muitas interfaces\",\"Tem poucos métodos\"]', '1', 'A God Class viola a coesão: faz coisas demais, ficando frágil e difícil de manter.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Por que uma classe-deus (God Class) é um problema?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'Qual princípio diz que uma classe deve ter uma única responsabilidade?', NULL, '[\"DRY\",\"SRP (Single Responsibility)\",\"KISS\",\"YAGNI\"]', '1', 'O SRP (Princípio da Responsabilidade Única) pede uma razão única para a classe mudar.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual princípio diz que uma classe deve ter uma única responsabilidade?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'erro', 'poo', 'Qual problema este trecho evidencia?', 'class Tudo {\n  function salvarNoBanco() {}\n  function renderizarHtml() {}\n  function enviarEmail() {}\n}', '[\"Nada, está ótima\",\"Responsabilidades demais numa só classe\",\"Falta herança\",\"Falta um laço\"]', '1', 'Persistência, apresentação e e-mail são responsabilidades distintas: separe em classes.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual problema este trecho evidencia?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'vf', 'poo', 'Refatorar uma God Class normalmente envolve dividi-la em classes menores e coesas.', NULL, NULL, 'true', 'Quebrar em partes coesas melhora a manutenção e os testes.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Refatorar uma God Class normalmente envolve dividi-la em classes menores e coesas.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'Qual a saída?', 'class C {\n  public $x = 2;\n  function dobro(){ return $this->x * 2; }\n}\n$c = new C();\necho $c->dobro();', '[\"2\",\"4\",\"x\",\"Erro\"]', '1', '$this->x vale 2, e 2 * 2 = 4.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual a saída?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'A Gárgula God-Class faz inventário, batalha, tela, banco e log no mesmo corpo. Qual princípio ela viola diretamente?', NULL, '[\"SRP\", \"FIFO\", \"OSI\", \"HTML\"]', '0', 'SRP é o Princípio da Responsabilidade Única.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='A Gárgula God-Class faz inventário, batalha, tela, banco e log no mesmo corpo. Qual princípio ela viola diretamente?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'Uma classe coesa no reino de Clayton é aquela que:', NULL, '[\"tem responsabilidades fortemente relacionadas\", \"faz tudo que o sistema precisa\", \"mistura tela e banco por praticidade\", \"evita métodos pequenos\"]', '0', 'Coesão significa manter responsabilidades relacionadas dentro da mesma classe.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Uma classe coesa no reino de Clayton é aquela que:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'Qual declaração usa generics para guardar apenas nomes de relíquias?', NULL, '[\"List<String> reliquias\", \"List reliquias\", \"String<List> reliquias\", \"new static reliquias\"]', '0', 'List<String> indica uma coleção parametrizada com String.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual declaração usa generics para guardar apenas nomes de relíquias?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'Qual expressão lambda representa uma ação simples sobre x?', NULL, '[\"x -> x + 1\", \"x => x + 1\", \"lambda x: x + 1\", \"x + 1 THEN\"]', '0', 'Em Java, lambdas usam a seta ->.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual expressão lambda representa uma ação simples sobre x?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'Quando uma operação pode falhar e precisa ser tratada, qual recurso entra na batalha?', NULL, '[\"Exceções\", \"Pacotes CSS\", \"ORDER BY\", \"Roteamento visual\"]', '0', 'Exceções representam situações de erro que podem ser lançadas e tratadas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Quando uma operação pode falhar e precisa ser tratada, qual recurso entra na batalha?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'vf', 'poo', 'Uma God Class concentra responsabilidades demais e tende a ser difícil de manter.', NULL, NULL, 'true', 'Esse é o problema central da classe-deus.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Uma God Class concentra responsabilidades demais e tende a ser difícil de manter.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'vf', 'poo', 'SRP sugere que uma classe tenha uma única responsabilidade principal.', NULL, NULL, 'true', 'SRP reduz motivos diferentes para alterar a mesma classe.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='SRP sugere que uma classe tenha uma única responsabilidade principal.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'vf', 'poo', 'Generics ajudam a indicar o tipo esperado dentro de coleções, como List<String>.', NULL, NULL, 'true', 'Generics aumentam segurança de tipo em coleções.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Generics ajudam a indicar o tipo esperado dentro de coleções, como List<String>.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'vf', 'poo', 'Lambda em Java é escrita obrigatoriamente com palavras de consulta de banco de dados.', NULL, NULL, 'false', 'Lambda em Java usa sintaxe como x -> x + 1.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Lambda em Java é escrita obrigatoriamente com palavras de consulta de banco de dados.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'vf', 'poo', 'Exceções podem ser tratadas com try/catch.', NULL, NULL, 'true', 'try/catch é usado para capturar e tratar exceções.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Exceções podem ser tratadas com try/catch.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'completar', 'poo', 'Complete a sigla do princípio que fere a Gárgula God-Class: _____.', NULL, NULL, '[\"SRP\", \"srp\", \"Single Responsibility Principle\"]', 'SRP é o Princípio da Responsabilidade Única.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Complete a sigla do princípio que fere a Gárgula God-Class: _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'completar', 'poo', 'Complete a coleção genérica de nomes:', 'List<_____> nomes;', NULL, '[\"String\"]', 'List<String> restringe a coleção a Strings.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Complete a coleção genérica de nomes:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'completar', 'poo', 'Complete a seta da lambda Java:', 'x _____ x + 1', NULL, '[\"->\"]', 'A seta -> é usada em lambdas Java.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Complete a seta da lambda Java:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'completar', 'poo', 'Complete o bloco que tenta executar código arriscado:', '_____ {\n  abrirBau();\n} catch (Exception e) { }', NULL, '[\"try\"]', 'try envolve o trecho que pode lançar exceção.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Complete o bloco que tenta executar código arriscado:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'completar', 'poo', 'Complete a captura da exceção:', 'try { abrirBau(); } _____ (Exception e) { }', NULL, '[\"catch\"]', 'catch captura a exceção lançada no bloco try.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Complete a captura da exceção:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'ordenar', 'poo', 'Ordene a estratégia para derrotar uma God Class.', NULL, '[\"Identificar responsabilidades misturadas\", \"Separar classes coesas\", \"Mover métodos para os lugares corretos\", \"Testar cada parte isolada\"]', '[0, 1, 2, 3]', 'Refatorar exige identificar, separar, mover e validar.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Ordene a estratégia para derrotar uma God Class.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'ordenar', 'poo', 'Ordene o tratamento de uma exceção.', NULL, '[\"Executar código dentro de try\", \"Exceção é lançada\", \"catch captura a exceção\", \"Sistema responde de forma controlada\"]', '[0, 1, 2, 3]', 'try/catch organiza o tratamento de falhas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Ordene o tratamento de uma exceção.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'ordenar', 'poo', 'Ordene o uso de uma coleção genérica.', NULL, '[\"Declarar List<String>\", \"Adicionar nomes\", \"Percorrer a coleção\", \"Usar cada nome com segurança de tipo\"]', '[0, 1, 2, 3]', 'Generics definem o tipo antes do uso da coleção.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Ordene o uso de uma coleção genérica.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'ordenar', 'poo', 'Ordene a evolução de uma classe monstruosa para classes coesas.', NULL, '[\"Classe faz tudo\", \"Separar persistência\", \"Separar regra de negócio\", \"Separar apresentação\"]', '[0, 1, 2, 3]', 'Separar responsabilidades reduz o tamanho e aumenta coesão.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Ordene a evolução de uma classe monstruosa para classes coesas.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'ordenar', 'poo', 'Ordene o uso de lambda para processar uma lista.', NULL, '[\"Criar coleção\", \"Definir expressão lambda\", \"Aplicar lambda aos itens\", \"Obter resultado processado\"]', '[0, 1, 2, 3]', 'A lambda define comportamento aplicado aos elementos.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Ordene o uso de lambda para processar uma lista.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'erro', 'poo', 'A Gárgula esconde responsabilidades demais. Qual linha denuncia a God Class?', '1: class GuildaService {\n2: void calcularRanking() {}\n3: void renderizarTelaHtml() {}\n4: void salvarNoBanco() {}\n5: void enviarEmailMarketing() {}', '[\"linha 1\", \"linhas 2 a 5 juntas indicam responsabilidades demais\", \"apenas linha 2\", \"não há problema\"]', '1', 'Misturar ranking, tela, banco e e-mail em uma classe indica baixa coesão e excesso de responsabilidades.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='A Gárgula esconde responsabilidades demais. Qual linha denuncia a God Class?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'erro', 'poo', 'Qual declaração perde a vantagem de generics?', NULL, '[\"List<String> nomes = new ArrayList<>();\", \"List<Integer> niveis = new ArrayList<>();\", \"List itens = new ArrayList();\", \"Map<String, Integer> placar = new HashMap<>();\"]', '2', 'List sem tipo parametrizado usa tipo cru e reduz segurança de tipo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual declaração perde a vantagem de generics?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'erro', 'poo', 'Qual lambda Java está escrita de forma incorreta?', NULL, '[\"x -> x + 1\", \"nome -> nome.length()\", \"(a, b) -> a + b\", \"x => x + 1\"]', '3', 'Em Java, a seta da lambda é ->, não =>.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual lambda Java está escrita de forma incorreta?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'erro', 'poo', 'Qual opção trata exceção de modo mais inadequado?', NULL, '[\"try/catch com mensagem útil\", \"Relançar exceção quando necessário\", \"Ignorar catch vazio sempre\", \"Tratar falha sem derrubar tudo\"]', '2', 'catch vazio esconde problemas e dificulta manutenção.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual opção trata exceção de modo mais inadequado?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'erro', 'poo', 'Qual frase sobre coesão está errada?', NULL, '[\"Classe coesa tem responsabilidades relacionadas\", \"God Class tende a ter baixa coesão\", \"Alta coesão ajuda manutenção\", \"Quanto mais responsabilidades diferentes, maior a coesão\"]', '3', 'Muitas responsabilidades diferentes reduzem a coesão.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual frase sobre coesão está errada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada conceito ao golpe correto contra a Gárgula.', NULL, '{\"itens\": [\"SRP\", \"Coesão\", \"God Class\", \"Refatoração\"], \"alvos\": [\"Responsabilidade única\", \"Responsabilidades relacionadas\", \"Classe que faz demais\", \"Dividir e melhorar o código\"]}', '[0, 1, 2, 3]', 'Esses conceitos formam o núcleo da fase chefe.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada conceito ao golpe correto contra a Gárgula.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada recurso Java ao significado.', NULL, '{\"itens\": [\"Generics\", \"Lambda\", \"Coleção\", \"Exceção\"], \"alvos\": [\"Tipo parametrizado\", \"Função/expressão compacta\", \"Grupo de elementos\", \"Falha tratável\"]}', '[0, 1, 2, 3]', 'Generics, lambdas, coleções e exceções aparecem no conjunto de tópicos da fase.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada recurso Java ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada cheiro de código à cura.', NULL, '{\"itens\": [\"Classe faz tudo\", \"List sem tipo\", \"catch vazio\", \"Método sem relação com a classe\"], \"alvos\": [\"Aplicar SRP\", \"Usar generics\", \"Tratar exceção\", \"Aumentar coesão\"]}', '[0, 1, 2, 3]', 'Cada cura reduz um problema comum da Gárgula.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada cheiro de código à cura.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada fragmento ao tópico da fase.', NULL, '{\"itens\": [\"List<String>\", \"x -> x + 1\", \"try/catch\", \"classe com 20 funções distintas\"], \"alvos\": [\"Generics\", \"Lambda\", \"Exceções\", \"God Class\"]}', '[0, 1, 2, 3]', 'Os fragmentos representam diretamente os tópicos listados para a chefe.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada fragmento ao tópico da fase.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada missão ao artefato mais adequado.', NULL, '{\"itens\": [\"Guardar vários nomes\", \"Processar cada item com expressão curta\", \"Responder a falha de arquivo\", \"Dividir classe gigante\"], \"alvos\": [\"Coleção\", \"Lambda\", \"Exceção\", \"SRP/refatoração\"]}', '[0, 1, 2, 3]', 'A escolha correta enfraquece a Gárgula God-Class.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada missão ao artefato mais adequado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'A Gárgula God-Class acumula cadastro, impressão, banco e batalha. Qual princípio ela viola?', NULL, '[\"SRP: responsabilidade única\",\"FIFO\",\"OSI\",\"SELECT\"]', '0', 'SRP recomenda que uma classe tenha uma razão principal para mudar.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='A Gárgula God-Class acumula cadastro, impressão, banco e batalha. Qual princípio ela viola?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'Qual sinal revela baixa coesão em uma classe?', NULL, '[\"Métodos tratam responsabilidades desconexas\",\"Todos os métodos tratam o mesmo conceito\",\"A classe tem nome claro\",\"O estado é protegido\"]', '0', 'Baixa coesão aparece quando a classe mistura tarefas sem unidade.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual sinal revela baixa coesão em uma classe?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'Em List<String>, o uso de String entre sinais de menor/maior representa:', NULL, '[\"generics\",\"lambda\",\"exceção\",\"pacote\"]', '0', 'Generics parametrizam tipos em classes e métodos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Em List<String>, o uso de String entre sinais de menor/maior representa:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'Qual expressão tem forma típica de lambda em Java?', NULL, '[\"x -> x * 2\",\"class -> extends\",\"try -> catch\",\"public -> private\"]', '0', 'Lambda expressa comportamento de forma compacta.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual expressão tem forma típica de lambda em Java?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'Qual bloco captura uma exceção lançada no código?', NULL, '[\"try/catch\",\"extends/super\",\"get/set\",\"new/static\"]', '0', 'try/catch envolve código sujeito a falha e trata a exceção.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual bloco captura uma exceção lançada no código?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'vf', 'poo', 'Uma God Class costuma ter responsabilidades demais e baixa coesão.', NULL, NULL, 'true', 'Ela concentra tarefas que deveriam estar separadas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Uma God Class costuma ter responsabilidades demais e baixa coesão.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'vf', 'poo', 'Generics ajudam a criar estruturas reutilizáveis com tipos mais seguros.', NULL, NULL, 'true', 'Generics evitam casts desnecessários e aumentam segurança de tipo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Generics ajudam a criar estruturas reutilizáveis com tipos mais seguros.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'vf', 'poo', 'Lambda pode representar comportamento passado como valor, como um critério ou ação.', NULL, NULL, 'true', 'Lambdas simplificam funções anônimas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Lambda pode representar comportamento passado como valor, como um critério ou ação.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'vf', 'poo', 'List, Set e Map são exemplos de coleções com exatamente o mesmo comportamento.', NULL, NULL, 'false', 'List permite sequência, Set evita duplicados e Map associa chave a valor.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='List, Set e Map são exemplos de coleções com exatamente o mesmo comportamento.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'vf', 'poo', 'Capturar uma exceção e ignorá-la silenciosamente é sempre a melhor prática.', NULL, NULL, 'false', 'Ignorar exceções esconde falhas e dificulta manutenção.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Capturar uma exceção e ignorá-la silenciosamente é sempre a melhor prática.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'completar', 'poo', 'Complete: o princípio da responsabilidade única é conhecido pela sigla ___.', NULL, NULL, '[\"SRP\",\"srp\"]', 'SRP vem de Single Responsibility Principle.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Complete: o princípio da responsabilidade única é conhecido pela sigla ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'completar', 'poo', 'Complete: List<String> usa o recurso chamado ___.', NULL, NULL, '[\"generics\",\"Generics\",\"genéricos\",\"genericos\"]', 'Generics parametrizam o tipo aceito.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Complete: List<String> usa o recurso chamado ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'completar', 'poo', 'Complete: uma expressão como x -> x + 1 é uma ___.', NULL, NULL, '[\"lambda\",\"Lambda\"]', 'Lambda representa comportamento de forma curta.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Complete: uma expressão como x -> x + 1 é uma ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'completar', 'poo', 'Complete: uma coleção que não deve manter duplicados é um ___.', NULL, NULL, '[\"Set\",\"set\",\"conjunto\",\"Conjunto\"]', 'Set representa conjunto sem duplicados.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Complete: uma coleção que não deve manter duplicados é um ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'completar', 'poo', 'Complete: para tratar falhas em Java, usamos try e ___.', NULL, NULL, '[\"catch\"]', 'catch captura e trata exceções.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Complete: para tratar falhas em Java, usamos try e ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'ordenar', 'poo', 'Ordene o ritual para derrotar uma God Class.', NULL, '[\"Identificar responsabilidades misturadas\",\"Separar classes menores\",\"Mover métodos para a classe correta\",\"Testar se cada classe ficou coesa\"]', '[0,1,2,3]', 'Refatoração reduz responsabilidades e aumenta coesão.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Ordene o ritual para derrotar uma God Class.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'ordenar', 'poo', 'Ordene o uso seguro de uma coleção genérica.', NULL, '[\"Escolher a coleção adequada\",\"Declarar o tipo genérico\",\"Adicionar elementos do tipo correto\",\"Iterar sem casts desnecessários\"]', '[0,1,2,3]', 'Generics tornam a coleção mais segura e clara.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Ordene o uso seguro de uma coleção genérica.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'ordenar', 'poo', 'Ordene o uso de lambda para filtrar criaturas.', NULL, '[\"Definir a coleção de criaturas\",\"Criar critério com lambda\",\"Aplicar o filtro\",\"Usar o resultado filtrado\"]', '[0,1,2,3]', 'Lambda pode representar o critério passado para uma operação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Ordene o uso de lambda para filtrar criaturas.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'ordenar', 'poo', 'Ordene o tratamento responsável de exceção.', NULL, '[\"Executar código arriscado no try\",\"Capturar exceção específica\",\"Registrar ou recuperar a falha\",\"Evitar engolir o erro sem ação\"]', '[0,1,2,3]', 'Exceções devem ser tratadas de forma explícita e útil.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Ordene o tratamento responsável de exceção.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'ordenar', 'poo', 'Ordene a escolha entre List, Set e Map.', NULL, '[\"Precisa manter ordem e repetição? List\",\"Precisa evitar duplicados? Set\",\"Precisa associar chave a valor? Map\",\"Escolher pela necessidade real\"]', '[3,0,1,2]', 'A estrutura deve responder ao objetivo dos dados.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Ordene a escolha entre List, Set e Map.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'erro', 'poo', 'A Gárgula mostra esta classe. Qual cheiro de código aparece?', 'class Sistema {\n  void salvarUsuario() {}\n  void enviarEmail() {}\n  void imprimirRelatorio() {}\n  void calcularImposto() {}\n}', '[\"God Class: responsabilidades demais\",\"Classe altamente coesa\",\"Interface correta\",\"Coleção genérica perfeita\"]', '0', 'A classe mistura responsabilidades diferentes.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='A Gárgula mostra esta classe. Qual cheiro de código aparece?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'erro', 'poo', 'Qual problema há neste uso de coleção?', 'List lista = new ArrayList();\nlista.add(\"runa\");\nInteger n = (Integer) lista.get(0);', '[\"Uso cru sem generics permite erro de tipo em tempo de execução\",\"List nunca aceita String\",\"ArrayList é uma exceção\",\"Casting sempre corrige qualquer tipo\"]', '0', 'Sem generics, o erro só aparece no cast em execução.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual problema há neste uso de coleção?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'erro', 'poo', 'Qual falha existe neste tratamento?', 'try {\n  abrirPortal();\n} catch (Exception e) {\n}', '[\"A exceção foi engolida sem tratamento\",\"catch nunca pode existir\",\"try exige extends\",\"Exception é uma coleção\"]', '0', 'Capturar e ignorar torna a falha invisível.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual falha existe neste tratamento?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'erro', 'poo', 'A lambda abaixo tenta filtrar nomes, mas retorna texto em vez de booleano. Qual é o problema?', 'nomes.stream().filter(n -> n.toUpperCase());', '[\"filter espera um predicado booleano\",\"lambda não existe em Java\",\"stream só aceita números\",\"toUpperCase apaga a lista\"]', '0', 'filter precisa de uma condição true/false.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='A lambda abaixo tenta filtrar nomes, mas retorna texto em vez de booleano. Qual é o problema?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'erro', 'poo', 'Qual sinal indica baixa coesão?', NULL, '[\"Uma classe Relatorio também controla login, estoque e pagamento\",\"Uma classe Pedido calcula total do pedido\",\"Uma classe Email envia e-mails\",\"Uma classe Usuario guarda dados do usuário\"]', '0', 'Misturar domínios diferentes reduz coesão.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual sinal indica baixa coesão?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada conceito da Gárgula ao significado.', NULL, '{\"itens\":[\"SRP\",\"God Class\",\"Coesão\",\"Generics\"],\"alvos\":[\"Responsabilidade única\",\"Classe com tarefas demais\",\"Unidade de propósito\",\"Tipo parametrizado\"]}', '[0,1,2,3]', 'Esses conceitos ajudam a organizar código orientado a objetos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada conceito da Gárgula ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada coleção ao uso típico.', NULL, '{\"itens\":[\"List\",\"Set\",\"Map\",\"Stream\"],\"alvos\":[\"Sequência de elementos\",\"Conjunto sem duplicados\",\"Chave associada a valor\",\"Fluxo de operações\"]}', '[0,1,2,3]', 'Coleções possuem papéis diferentes.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada coleção ao uso típico.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada palavra de exceção ao papel.', NULL, '{\"itens\":[\"try\",\"catch\",\"throw\",\"Exception\"],\"alvos\":[\"Bloco arriscado\",\"Tratamento da falha\",\"Lança falha\",\"Tipo de falha\"]}', '[0,1,2,3]', 'Essas palavras aparecem no tratamento de erros.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada palavra de exceção ao papel.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada fragmento ao recurso.', NULL, '{\"itens\":[\"List<String>\",\"x -> x > 0\",\"new ArrayList<>()\",\"catch (IOException e)\"],\"alvos\":[\"Generics\",\"Lambda\",\"Coleção\",\"Captura de exceção\"]}', '[0,1,2,3]', 'A fase combina organização, coleções, lambdas e erros.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada fragmento ao recurso.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada ameaça à refatoração indicada.', NULL, '{\"itens\":[\"Classe faz tudo\",\"Lista sem tipo\",\"Erro ignorado\",\"Código repetido em filtros\"],\"alvos\":[\"Separar responsabilidades\",\"Usar generics\",\"Tratar ou registrar exceção\",\"Usar lambda/reuso de critério\"]}', '[0,1,2,3]', 'Cada problema pede uma correção de projeto ou linguagem.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada ameaça à refatoração indicada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'No SOLID, o que o \"S\" (SRP) defende?', NULL, '[\"Single Responsibility: uma classe, uma razão para mudar\",\"Simple Rule\",\"Static Reference\",\"Sorted Records\"]', '0', 'Princípio da Responsabilidade Única: cada classe deve ter um único motivo para mudar.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='No SOLID, o que o \"S\" (SRP) defende?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'Uma classe com ALTA coesão:', NULL, '[\"Faz muitas coisas sem relação\",\"Concentra-se em uma responsabilidade bem definida\",\"Não tem métodos\",\"Depende de todas as outras classes\"]', '1', 'Coesão alta = a classe trata de um único assunto, o oposto da God Class.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Uma classe com ALTA coesão:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'vf', 'poo', 'Baixo acoplamento entre classes facilita mudanças e testes.', NULL, NULL, 'true', 'Quanto menos uma classe depende das outras, mais fácil trocá-la ou testá-la isoladamente.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Baixo acoplamento entre classes facilita mudanças e testes.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'erro', 'poo', 'Qual problema esta classe evidencia?', 'class Pedido {\n  function calcularFrete() {}\n  function gerarPdf() {}\n  function enviarEmail() {}\n  function salvarNoBanco() {}\n}', '[\"Está coesa e correta\",\"Responsabilidades demais (viola o SRP)\",\"Falta herança\",\"Falta um construtor\"]', '1', 'Frete, PDF, e-mail e persistência são quatro responsabilidades — separe em classes próprias.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual problema esta classe evidencia?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'multipla', 'poo', 'A refatoração típica de uma God Class é:', NULL, '[\"Adicionar ainda mais métodos\",\"Dividi-la em classes menores e coesas\",\"Tornar tudo public\",\"Apagar todos os testes\"]', '1', 'Quebrar a classe-deus em partes coesas melhora manutenção, testes e leitura.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='A refatoração típica de uma God Class é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'completar', 'poo', 'Complete a sigla do princípio que evita repetir código: Don\'t Repeat Yourself = ___', NULL, NULL, '[\"DRY\",\"dry\"]', 'DRY: cada conhecimento deve ter uma única representação no sistema.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Complete a sigla do princípio que evita repetir código: Don\'t Repeat Yourself = ___') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'multipla', 'estruturas', 'Uma pilha (stack) segue qual princípio?', NULL, '[\"FIFO\",\"LIFO\",\"aleatório\",\"ordenado\"]', '1', 'LIFO: Last In, First Out — o último a entrar é o primeiro a sair.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Uma pilha (stack) segue qual princípio?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'multipla', 'estruturas', 'Uma fila (queue) segue qual princípio?', NULL, '[\"LIFO\",\"FIFO\",\"LILO\",\"nenhum\"]', '1', 'FIFO: First In, First Out — o primeiro a entrar é o primeiro a sair.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Uma fila (queue) segue qual princípio?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'multipla', 'estruturas', 'Qual operação remove o elemento do topo de uma pilha?', NULL, '[\"push\",\"pop\",\"enqueue\",\"peek\"]', '1', 'pop desempilha (remove) o elemento do topo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Qual operação remove o elemento do topo de uma pilha?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'ordenar', 'estruturas', 'Empilhei 1, depois 2, depois 3. Em que ordem eles SAEM ao desempilhar?', NULL, '[\"1\",\"2\",\"3\"]', '[2,1,0]', 'Por ser LIFO, sai 3, depois 2, depois 1.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Empilhei 1, depois 2, depois 3. Em que ordem eles SAEM ao desempilhar?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'multipla', 'estruturas', 'Na clareira de Marcelo, a Pilha Viva só entrega o último cristal guardado. Qual princípio governa essa criatura?', NULL, '[\"FIFO\",\"LIFO\",\"Random\",\"Ordenação alfabética\"]', '1', 'Pilha segue LIFO: o último a entrar é o primeiro a sair.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Na clareira de Marcelo, a Pilha Viva só entrega o último cristal guardado. Qual princípio governa essa criatura?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'multipla', 'estruturas', 'O aprendiz empilha as runas 10, 20 e 30. Qual runa sai primeiro ao remover do topo?', NULL, '[\"10\",\"20\",\"30\",\"Nenhuma, pilha não remove\"]', '2', 'Na pilha, remove-se o item do topo; 30 foi o último inserido.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='O aprendiz empilha as runas 10, 20 e 30. Qual runa sai primeiro ao remover do topo?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'multipla', 'estruturas', 'A Fila dos Goblins atende quem chegou primeiro. Qual princípio define essa ordem?', NULL, '[\"LIFO\",\"FIFO\",\"Heap\",\"Hash\"]', '1', 'Fila segue FIFO: o primeiro a entrar é o primeiro a sair.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='A Fila dos Goblins atende quem chegou primeiro. Qual princípio define essa ordem?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'multipla', 'estruturas', 'Na pilha estática de Marcelo, qual variável costuma guardar o índice do último elemento inserido?', NULL, '[\"base\",\"topo\",\"proximo\",\"hash\"]', '1', 'A variável topo indica onde está o último elemento da pilha.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Na pilha estática de Marcelo, qual variável costuma guardar o índice do último elemento inserido?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'multipla', 'estruturas', 'Ao tentar inserir mais um item em uma pilha estática cheia, que armadilha acontece?', NULL, '[\"Underflow\",\"Overflow\",\"Busca binária\",\"Colisão hash\"]', '1', 'Overflow ocorre quando se tenta inserir além da capacidade da estrutura.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Ao tentar inserir mais um item em uma pilha estática cheia, que armadilha acontece?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'vf', 'estruturas', 'A Pilha Viva permite remover primeiro o elemento mais antigo inserido.', NULL, NULL, 'false', 'Isso descreve fila. Pilha remove primeiro o elemento mais recente.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='A Pilha Viva permite remover primeiro o elemento mais antigo inserido.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'vf', 'estruturas', 'Na fila, o primeiro elemento inserido deve ser o primeiro removido.', NULL, NULL, 'true', 'Esse é o princípio FIFO.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Na fila, o primeiro elemento inserido deve ser o primeiro removido.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'vf', 'estruturas', 'Uma pilha vazia removida sem teste pode causar underflow.', NULL, NULL, 'true', 'Underflow é a tentativa de remover de uma estrutura vazia.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Uma pilha vazia removida sem teste pode causar underflow.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'vf', 'estruturas', 'Uma fila circular pode reaproveitar espaços livres do vetor após remoções.', NULL, NULL, 'true', 'A fila circular evita desperdício ao circular os índices no vetor.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Uma fila circular pode reaproveitar espaços livres do vetor após remoções.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'vf', 'estruturas', 'Em uma pilha encadeada, o limite principal deixa de ser um tamanho fixo e passa a ser a memória disponível.', NULL, NULL, 'true', 'Estruturas encadeadas são dinâmicas, limitadas pela memória do processo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Em uma pilha encadeada, o limite principal deixa de ser um tamanho fixo e passa a ser a memória disponível.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'completar', 'estruturas', 'Complete a inscrição da Pilha Viva: LIFO significa Last In, First ___.', NULL, NULL, '[\"Out\",\"out\",\"OUT\"]', 'LIFO significa Last In, First Out.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Complete a inscrição da Pilha Viva: LIFO significa Last In, First ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'completar', 'estruturas', 'Complete a inscrição da fila dos viajantes: FIFO significa First In, First ___.', NULL, NULL, '[\"Out\",\"out\",\"OUT\"]', 'FIFO significa First In, First Out.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Complete a inscrição da fila dos viajantes: FIFO significa First In, First ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'completar', 'estruturas', 'Na pilha estática, quando não há elementos, o índice topo costuma iniciar em ___.', NULL, NULL, '[\"-1\"]', 'O valor -1 indica que nenhum índice válido está ocupado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Na pilha estática, quando não há elementos, o índice topo costuma iniciar em ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'completar', 'estruturas', 'A tentativa de remover de uma pilha vazia recebe o nome de ___.', NULL, NULL, '[\"underflow\",\"Underflow\",\"UNDERFLOW\"]', 'Underflow é remoção inválida em estrutura vazia.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='A tentativa de remover de uma pilha vazia recebe o nome de ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'completar', 'estruturas', 'A tentativa de inserir em uma pilha cheia recebe o nome de ___.', NULL, NULL, '[\"overflow\",\"Overflow\",\"OVERFLOW\"]', 'Overflow é inserção inválida em estrutura cheia.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='A tentativa de inserir em uma pilha cheia recebe o nome de ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'ordenar', 'estruturas', 'Ordene a saída das runas ao desempilhar uma pilha onde entraram A, depois B, depois C.', NULL, '[\"A\",\"B\",\"C\"]', '[2,1,0]', 'Na pilha, sai C, depois B, depois A.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Ordene a saída das runas ao desempilhar uma pilha onde entraram A, depois B, depois C.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'ordenar', 'estruturas', 'Ordene o atendimento da fila onde entraram Ana, Beto e Cora.', NULL, '[\"Cora\",\"Ana\",\"Beto\"]', '[1,2,0]', 'Na fila, a primeira pessoa a entrar é atendida primeiro.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Ordene o atendimento da fila onde entraram Ana, Beto e Cora.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'ordenar', 'estruturas', 'Ordene os passos de inserção em uma pilha estática.', NULL, '[\"Guardar o valor no vetor\",\"Testar se está cheia\",\"Avançar o topo\"]', '[1,2,0]', 'Primeiro testa overflow, depois move o topo e guarda o valor.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Ordene os passos de inserção em uma pilha estática.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'ordenar', 'estruturas', 'Ordene os passos de remoção em uma pilha estática.', NULL, '[\"Retornar o valor guardado\",\"Testar se está vazia\",\"Ler o dado no topo\",\"Recuar o topo\"]', '[1,2,3,0]', 'Primeiro testa underflow, lê o topo, recua o índice e retorna o valor.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Ordene os passos de remoção em uma pilha estática.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'ordenar', 'estruturas', 'Ordene o ciclo básico de uma fila circular.', NULL, '[\"Remover da base\",\"Inserir no topo\",\"Reaproveitar espaço livre\",\"Avançar índice circular\"]', '[1,0,3,2]', 'A fila insere e remove atualizando índices que podem circular e reaproveitar posições.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Ordene o ciclo básico de uma fila circular.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'erro', 'estruturas', 'A Pilha Viva recebeu este ritual. Qual é a falha?', 'remover() sem verificar isVazia()', '[\"Falta verificar underflow\",\"Falta ordenar a pilha\",\"Deveria usar DNS\",\"Não há falha\"]', '0', 'Antes de remover, é preciso verificar se a pilha está vazia.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='A Pilha Viva recebeu este ritual. Qual é a falha?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'erro', 'estruturas', 'A fila do acampamento remove sempre o último que chegou. Qual erro conceitual ocorreu?', NULL, '[\"A fila foi tratada como pilha\",\"A fila virou vetor ordenado\",\"O hash colidiu\",\"Está tudo correto\"]', '0', 'Remover o último primeiro é comportamento de pilha, não de fila.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='A fila do acampamento remove sempre o último que chegou. Qual erro conceitual ocorreu?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'erro', 'estruturas', 'O aprendiz tentou inserir na pilha mesmo com isCheia verdadeiro. Que perigo Marcelo aponta?', NULL, '[\"Overflow\",\"Underflow\",\"Percurso em ordem\",\"Busca binária\"]', '0', 'Inserir em estrutura cheia gera overflow.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='O aprendiz tentou inserir na pilha mesmo com isCheia verdadeiro. Que perigo Marcelo aponta?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'erro', 'estruturas', 'Na pilha encadeada, o novo nó foi criado, mas não aponta para o antigo topo. O que se perde?', NULL, '[\"A ligação com os elementos anteriores\",\"A porta do servidor\",\"O valor de hash\",\"A ordenação alfabética obrigatória\"]', '0', 'Sem apontar para o topo anterior, a cadeia é quebrada.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Na pilha encadeada, o novo nó foi criado, mas não aponta para o antigo topo. O que se perde?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'erro', 'estruturas', 'Na fila encadeada, removeu-se a base, mas a referência base não foi atualizada para o próximo nó. Qual é o problema?', NULL, '[\"A fila continua apontando para o nó removido\",\"A pilha fica balanceada\",\"O vetor fica ordenado\",\"A busca vira O(log n)\"]', '0', 'Após remover, a base precisa apontar para o próximo elemento.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Na fila encadeada, removeu-se a base, mas a referência base não foi atualizada para o próximo nó. Qual é o problema?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'arrastar', 'estruturas', 'Arraste cada criatura para sua regra de remoção.', NULL, '{\"itens\":[\"Pilha Viva\",\"Fila dos Goblins\",\"Pilha estática\",\"Fila circular\"],\"alvos\":[\"Remove o último inserido\",\"Remove o primeiro inserido\",\"Tem capacidade fixa\",\"Reaproveita posições do vetor\"]}', '[0,1,2,3]', 'Cada estrutura tem uma regra de acesso e uma estratégia de armazenamento.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Arraste cada criatura para sua regra de remoção.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'arrastar', 'estruturas', 'Arraste cada operação ao efeito correto.', NULL, '{\"itens\":[\"push ou adicionar\",\"pop ou remover\",\"peek ou consultar\",\"limpar\"],\"alvos\":[\"Insere elemento\",\"Remove elemento\",\"Olha sem remover\",\"Esvazia a estrutura\"]}', '[0,1,2,3]', 'Essas são operações comuns em pilhas e filas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Arraste cada operação ao efeito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'arrastar', 'estruturas', 'Arraste cada armadilha ao significado correto.', NULL, '{\"itens\":[\"Overflow\",\"Underflow\",\"LIFO\",\"FIFO\"],\"alvos\":[\"Inserir quando está cheia\",\"Remover quando está vazia\",\"Último entra, primeiro sai\",\"Primeiro entra, primeiro sai\"]}', '[0,1,2,3]', 'Overflow e underflow são erros de estado; LIFO e FIFO são políticas de acesso.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Arraste cada armadilha ao significado correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'arrastar', 'estruturas', 'Arraste cada referência da fila encadeada ao papel correto.', NULL, '{\"itens\":[\"base\",\"topo\",\"proximo\",\"null\"],\"alvos\":[\"Primeiro elemento a sair\",\"Último elemento inserido\",\"Ligação ao nó seguinte\",\"Fim da cadeia\"]}', '[0,1,2,3]', 'A fila encadeada depende dessas referências para manter a ordem FIFO.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Arraste cada referência da fila encadeada ao papel correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'arrastar', 'estruturas', 'Arraste cada implementação à característica dominante.', NULL, '{\"itens\":[\"Pilha estática\",\"Pilha encadeada\",\"Fila estática\",\"Fila encadeada\"],\"alvos\":[\"Usa vetor fixo e topo\",\"Usa nós ligados ao topo\",\"Pode usar vetor circular\",\"Usa nós com base e topo\"]}', '[0,1,2,3]', 'As estruturas podem ser sequenciais ou encadeadas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Arraste cada implementação à característica dominante.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'multipla', 'estruturas', 'Qual operação OLHA o elemento do topo da pilha sem removê-lo?', NULL, '[\"pop\",\"push\",\"peek (top)\",\"enqueue\"]', '2', 'peek (ou top) consulta o topo sem desempilhar.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Qual operação OLHA o elemento do topo da pilha sem removê-lo?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'multipla', 'estruturas', 'Numa fila, qual operação INSERE um elemento no fim?', NULL, '[\"dequeue\",\"enqueue\",\"pop\",\"peek\"]', '1', 'enqueue adiciona ao fim da fila; dequeue remove da frente.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Numa fila, qual operação INSERE um elemento no fim?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'vf', 'estruturas', 'A pilha de chamadas (call stack) de um programa funciona como uma pilha LIFO.', NULL, NULL, 'true', 'A última função chamada é a primeira a retornar: puro LIFO.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='A pilha de chamadas (call stack) de um programa funciona como uma pilha LIFO.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'ordenar', 'estruturas', 'Enfileirei A, depois B, depois C. Em que ordem eles SAEM da fila?', NULL, '[\"A\",\"B\",\"C\"]', '[0,1,2]', 'Fila é FIFO: o primeiro a entrar (A) é o primeiro a sair.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Enfileirei A, depois B, depois C. Em que ordem eles SAEM da fila?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'completar', 'estruturas', 'Complete a operação que adiciona um elemento ao topo da pilha:', 'pilha.____(valor);', NULL, '[\"push\"]', 'push empilha um novo elemento no topo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Complete a operação que adiciona um elemento ao topo da pilha:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'multipla', 'estruturas', 'O recurso \"desfazer\" (undo) de um editor é melhor modelado por:', NULL, '[\"Uma fila\",\"Uma pilha\",\"Uma árvore\",\"Um grafo\"]', '1', 'Desfaz-se a última ação primeiro — comportamento LIFO de pilha.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='O recurso \"desfazer\" (undo) de um editor é melhor modelado por:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'multipla', 'estruturas', 'Numa lista encadeada, o que cada nó guarda?', NULL, '[\"Só o valor\",\"O valor e a referência ao próximo nó\",\"Apenas o próximo\",\"O array inteiro\"]', '1', 'Cada nó guarda um valor e um ponteiro para o próximo nó.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Numa lista encadeada, o que cada nó guarda?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'vf', 'estruturas', 'Inserir um elemento no início de uma lista encadeada é uma operação O(1).', NULL, NULL, 'true', 'Basta ajustar dois ponteiros, sem percorrer a lista: tempo constante.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Inserir um elemento no início de uma lista encadeada é uma operação O(1).') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'multipla', 'estruturas', 'Acessar o elemento na posição k de uma lista encadeada custa, no pior caso:', NULL, '[\"O(1)\",\"O(log n)\",\"O(n)\",\"O(n²)\"]', '2', 'É preciso percorrer nó a nó até a posição k: linear, O(n).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Acessar o elemento na posição k de uma lista encadeada custa, no pior caso:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'completar', 'estruturas', 'Numa lista simples, o último nó aponta para ___ , indicando o fim.', NULL, NULL, '[\"null\",\"NULL\",\"nulo\"]', 'O ponteiro nulo marca que não há próximo nó.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Numa lista simples, o último nó aponta para ___ , indicando o fim.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'multipla', 'estruturas', 'Na trilha da Serpente Encadeada, o que um nó guarda em uma lista encadeada?', NULL, '[\"Somente o tamanho da lista\",\"Um dado e referências para outros nós\",\"A tabela inteira do banco\",\"A senha do usuário\"]', '1', 'O nó guarda o valor e uma ou mais referências de ligação.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Na trilha da Serpente Encadeada, o que um nó guarda em uma lista encadeada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'multipla', 'estruturas', 'Marcelo mostra uma lista estática. Qual estrutura costuma armazenar seus dados lado a lado?', NULL, '[\"Vetor\",\"DNS\",\"Firewall\",\"Thread\"]', '0', 'Listas estáticas usam armazenamento sequencial, geralmente vetor.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Marcelo mostra uma lista estática. Qual estrutura costuma armazenar seus dados lado a lado?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'multipla', 'estruturas', 'Qual é a principal desvantagem da lista encadeada para acesso por posição?', NULL, '[\"Exige percorrer nó a nó\",\"Não pode remover\",\"Sempre duplica dados\",\"Não guarda valores\"]', '0', 'Sem acesso direto por índice, é preciso caminhar pelos nós.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Qual é a principal desvantagem da lista encadeada para acesso por posição?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'multipla', 'estruturas', 'Em uma lista duplamente encadeada, quais referências aparecem em cada nó?', NULL, '[\"anterior e proximo\",\"porta e ip\",\"model e view\",\"base e dns\"]', '0', 'A lista dupla liga cada nó ao anterior e ao próximo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Em uma lista duplamente encadeada, quais referências aparecem em cada nó?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'multipla', 'estruturas', 'Ao limpar uma lista encadeada, o ritual mais simples é:', NULL, '[\"Definir base e topo como null\",\"Ordenar todos os nós\",\"Criar mais nós\",\"Duplicar o vetor\"]', '0', 'Ao remover referências principais, os nós ficam inacessíveis e podem ser coletados.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Ao limpar uma lista encadeada, o ritual mais simples é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'vf', 'estruturas', 'Uma lista encadeada pode crescer dinamicamente conforme há memória disponível.', NULL, NULL, 'true', 'Nós são alocados gradualmente, sem tamanho fixo inicial.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Uma lista encadeada pode crescer dinamicamente conforme há memória disponível.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'vf', 'estruturas', 'Em uma lista sequencial, os dados ficam lado a lado na memória.', NULL, NULL, 'true', 'Essa é a característica das estruturas sequenciais.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Em uma lista sequencial, os dados ficam lado a lado na memória.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'vf', 'estruturas', 'A lista encadeada permite acesso direto instantâneo a qualquer índice como um vetor.', NULL, NULL, 'false', 'Ela precisa percorrer nó a nó até a posição desejada.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='A lista encadeada permite acesso direto instantâneo a qualquer índice como um vetor.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'vf', 'estruturas', 'Em uma lista duplamente encadeada, remover um nó do meio exige ajustar ligações do anterior e do próximo.', NULL, NULL, 'true', 'A remoção reconecta os vizinhos ao redor do nó removido.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Em uma lista duplamente encadeada, remover um nó do meio exige ajustar ligações do anterior e do próximo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'vf', 'estruturas', 'O último nó de uma lista simples normalmente aponta para um valor nulo.', NULL, NULL, 'true', 'A referência nula marca o fim da cadeia.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='O último nó de uma lista simples normalmente aponta para um valor nulo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'completar', 'estruturas', 'Complete: o elemento de ligação de uma lista encadeada é chamado de ___.', NULL, NULL, '[\"no\",\"nó\",\"No\",\"Nó\"]', 'O nó guarda o dado e as referências da lista.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Complete: o elemento de ligação de uma lista encadeada é chamado de ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'completar', 'estruturas', 'Complete: em uma lista duplamente encadeada, um nó guarda o dado, o próximo e o ___.', NULL, NULL, '[\"anterior\",\"Anterior\"]', 'A referência anterior permite caminhar de volta.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Complete: em uma lista duplamente encadeada, um nó guarda o dado, o próximo e o ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'completar', 'estruturas', 'Complete: em Java, uma referência sem objeto costuma receber o valor ___.', NULL, NULL, '[\"null\",\"NULL\",\"nulo\"]', 'null indica ausência de objeto referenciado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Complete: em Java, uma referência sem objeto costuma receber o valor ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'completar', 'estruturas', 'Complete: na lista encadeada, acessar a posição k exige percorrer os nós, então o custo no pior caso é O(___).', NULL, NULL, '[\"n\",\"N\"]', 'Acesso por índice em lista encadeada é linear.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Complete: na lista encadeada, acessar a posição k exige percorrer os nós, então o custo no pior caso é O(___).') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'completar', 'estruturas', 'Complete: estruturas com tamanho fixo definido na criação são chamadas de ___.', NULL, NULL, '[\"estáticas\",\"estaticas\",\"estática\",\"estatica\"]', 'Estruturas estáticas têm capacidade fixa.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Complete: estruturas com tamanho fixo definido na criação são chamadas de ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'ordenar', 'estruturas', 'Ordene a inserção de um nó entre dois guardiões da lista dupla.', NULL, '[\"Atualizar anterior.proximo\",\"Criar o novo nó\",\"Ajustar no.anterior e no.proximo\",\"Atualizar proximo.anterior\"]', '[1,2,0,3]', 'Primeiro cria o nó, ajusta suas ligações e depois reconecta os vizinhos.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Ordene a inserção de um nó entre dois guardiões da lista dupla.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'ordenar', 'estruturas', 'Ordene a remoção segura de um nó no meio da lista.', NULL, '[\"Reduzir tamanho\",\"Guardar referência ao anterior e próximo\",\"Ligar anterior ao próximo\",\"Ligar próximo ao anterior\"]', '[1,2,3,0]', 'A remoção precisa reconectar os vizinhos e atualizar o tamanho.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Ordene a remoção segura de um nó no meio da lista.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'ordenar', 'estruturas', 'Ordene os nós visitados ao percorrer A -> B -> C -> null.', NULL, '[\"C\",\"A\",\"B\"]', '[1,2,0]', 'O percurso segue do primeiro nó até o fim.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Ordene os nós visitados ao percorrer A -> B -> C -> null.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'ordenar', 'estruturas', 'Ordene do acesso mais direto ao menos direto.', NULL, '[\"Lista encadeada por índice\",\"Vetor por índice\",\"Percorrer lista com iterador\"]', '[1,2,0]', 'Vetor acessa direto; iterador percorre uma vez; índice em lista pode repercorrer a cadeia.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Ordene do acesso mais direto ao menos direto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'ordenar', 'estruturas', 'Ordene os passos para limpar uma lista encadeada.', NULL, '[\"Zerar tamanho\",\"Definir base como null\",\"Definir topo como null\",\"Garbage collector poderá recolher nós sem referência\"]', '[1,2,0,3]', 'Ao remover as referências principais, os nós deixam de ser alcançáveis.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Ordene os passos para limpar uma lista encadeada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'erro', 'estruturas', 'A Serpente Encadeada encontrou este nó sem ligação ao próximo. Qual problema ele causa se estiver no meio da lista?', 'no.proximo = null', '[\"Corta a cadeia e perde os nós seguintes\",\"Transforma a lista em fila FIFO\",\"Ordena automaticamente\",\"Melhora busca binária\"]', '0', 'Um null no meio encerra o percurso antes da hora.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='A Serpente Encadeada encontrou este nó sem ligação ao próximo. Qual problema ele causa se estiver no meio da lista?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'erro', 'estruturas', 'O aprendiz usa for com get(i) em lista encadeada grande. Qual é a falha de performance?', NULL, '[\"Cada get pode percorrer a lista de novo\",\"O código vira criptografia\",\"A lista passa a usar TCP\",\"O vetor fica menor\"]', '0', 'Repetir acesso por índice em lista encadeada pode gerar muitos percursos.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='O aprendiz usa for com get(i) em lista encadeada grande. Qual é a falha de performance?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'erro', 'estruturas', 'Na inserção de uma lista dupla, o aprendiz atualizou no.proximo, mas esqueceu proximo.anterior. Qual falha fica?', NULL, '[\"A ligação de volta fica quebrada\",\"A pilha vira heap\",\"O hash fica duplicado\",\"O índice vira constante\"]', '0', 'A lista dupla precisa manter coerentes as duas direções.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Na inserção de uma lista dupla, o aprendiz atualizou no.proximo, mas esqueceu proximo.anterior. Qual falha fica?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'erro', 'estruturas', 'A lista estática recebeu mais elementos que sua capacidade. Qual erro Marcelo espera?', NULL, '[\"Overflow ou falha de capacidade\",\"Underflow de rede\",\"Colisão de DNS\",\"Percurso em ordem\"]', '0', 'Uma estrutura estática tem limite fixo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='A lista estática recebeu mais elementos que sua capacidade. Qual erro Marcelo espera?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'erro', 'estruturas', 'O mapa da Serpente diz que lista encadeada sempre ocupa memória contígua. O que está errado?', NULL, '[\"Nós ficam dispersos e ligados por referências\",\"Listas não guardam dados\",\"Todo nó precisa ser raiz\",\"Fila e lista são idênticas\"]', '0', 'Nas estruturas encadeadas, os nós podem estar dispersos na memória.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='O mapa da Serpente diz que lista encadeada sempre ocupa memória contígua. O que está errado?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'arrastar', 'estruturas', 'Arraste cada peça da lista ao papel correto.', NULL, '{\"itens\":[\"dado\",\"proximo\",\"anterior\",\"tamanho\"],\"alvos\":[\"Valor armazenado\",\"Referência ao nó seguinte\",\"Referência ao nó anterior\",\"Quantidade de elementos\"]}', '[0,1,2,3]', 'Esses campos organizam a lista e seu percurso.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Arraste cada peça da lista ao papel correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'arrastar', 'estruturas', 'Arraste cada tipo de lista à característica.', NULL, '{\"itens\":[\"Lista estática\",\"Lista encadeada\",\"Lista duplamente encadeada\",\"Lista circular\"],\"alvos\":[\"Usa vetor fixo\",\"Usa nós e referências\",\"Permite voltar ao nó anterior\",\"Último nó aponta para o início\"]}', '[0,1,2,3]', 'Cada variação organiza seus nós de forma diferente.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Arraste cada tipo de lista à característica.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'arrastar', 'estruturas', 'Arraste cada operação ao custo típico em lista encadeada.', NULL, '{\"itens\":[\"Acessar posição k\",\"Inserir no início\",\"Remover nó já localizado\",\"Percorrer todos os nós\"],\"alvos\":[\"O(n)\",\"O(1)\",\"O(1)\",\"O(n)\"]}', '[0,1,2,3]', 'O custo depende de já ter ou não a referência ao nó.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Arraste cada operação ao custo típico em lista encadeada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'arrastar', 'estruturas', 'Arraste cada sentinela da Serpente ao valor que costuma indicar vazio ou fim.', NULL, '{\"itens\":[\"base vazia\",\"topo vazio\",\"proximo do último\",\"anterior do primeiro\"],\"alvos\":[\"null\",\"null\",\"null\",\"null\"]}', '[0,1,2,3]', 'O valor null indica ausência de nó em vários pontos da lista.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Arraste cada sentinela da Serpente ao valor que costuma indicar vazio ou fim.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'arrastar', 'estruturas', 'Arraste cada frase ao tipo correto.', NULL, '{\"itens\":[\"Dados lado a lado\",\"Nós dispersos\",\"Acesso direto por índice\",\"Percurso por referências\"],\"alvos\":[\"Sequencial\",\"Encadeada\",\"Sequencial\",\"Encadeada\"]}', '[0,1,2,3]', 'Sequencial favorece índice; encadeada favorece inserções e remoções por ligação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Arraste cada frase ao tipo correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'multipla', 'estruturas', 'Numa lista DUPLAMENTE encadeada, cada nó aponta para:', NULL, '[\"Só o próximo\",\"O anterior e o próximo\",\"A cabeça e a cauda\",\"Nenhum nó\"]', '1', 'Dois ponteiros por nó (anterior e próximo) permitem percorrer nos dois sentidos.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Numa lista DUPLAMENTE encadeada, cada nó aponta para:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'vf', 'estruturas', 'Numa lista encadeada, os elementos NÃO precisam ficar contíguos na memória.', NULL, NULL, 'true', 'Cada nó guarda um ponteiro para o próximo; eles podem estar espalhados na memória.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Numa lista encadeada, os elementos NÃO precisam ficar contíguos na memória.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'multipla', 'estruturas', 'Remover o PRIMEIRO nó de uma lista encadeada custa, no pior caso:', NULL, '[\"O(1)\",\"O(n)\",\"O(log n)\",\"O(n²)\"]', '0', 'Basta mover a cabeça para o segundo nó: tempo constante.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Remover o PRIMEIRO nó de uma lista encadeada custa, no pior caso:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'completar', 'estruturas', 'O ponteiro para o primeiro nó de uma lista costuma se chamar ___ (em inglês).', NULL, NULL, '[\"head\"]', 'head aponta para o início; o último nó (tail) aponta para nulo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='O ponteiro para o primeiro nó de uma lista costuma se chamar ___ (em inglês).') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'multipla', 'estruturas', 'Qual a vantagem da lista encadeada sobre o vetor (array) fixo?', NULL, '[\"Acesso por índice em O(1)\",\"Inserir\\/remover no meio sem deslocar os outros elementos\",\"Ocupa menos memória por elemento\",\"Permite busca binária direta\"]', '1', 'Inserir/remover só reajusta ponteiros; no array seria preciso deslocar elementos.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Qual a vantagem da lista encadeada sobre o vetor (array) fixo?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'multipla', 'estruturas', 'Buscar um valor numa lista encadeada NÃO ordenada custa, no pior caso:', NULL, '[\"O(1)\",\"O(log n)\",\"O(n)\",\"O(0)\"]', '2', 'Pode ser preciso percorrer nó a nó até o fim: linear, O(n).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Buscar um valor numa lista encadeada NÃO ordenada custa, no pior caso:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'multipla', 'estruturas', 'A busca binária em um vetor ordenado tem complexidade:', NULL, '[\"O(n)\",\"O(log n)\",\"O(n²)\",\"O(1)\"]', '1', 'A cada passo o espaço de busca cai pela metade: O(log n).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='A busca binária em um vetor ordenado tem complexidade:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'multipla', 'estruturas', 'Numa árvore binária de busca balanceada, a busca é, em média:', NULL, '[\"O(n)\",\"O(log n)\",\"O(n log n)\",\"O(1)\"]', '1', 'A altura balanceada é proporcional a log n, então a busca é O(log n).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Numa árvore binária de busca balanceada, a busca é, em média:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'vf', 'estruturas', 'O(n²) cresce mais rápido que O(n log n) conforme n aumenta.', NULL, NULL, 'true', 'Para n grande, n² supera n log n.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='O(n²) cresce mais rápido que O(n log n) conforme n aumenta.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'ordenar', 'estruturas', 'Ordene da MAIS rápida (melhor) para a MAIS lenta (pior):', NULL, '[\"O(n²)\",\"O(1)\",\"O(n)\",\"O(log n)\"]', '[1,3,2,0]', 'O(1) < O(log n) < O(n) < O(n²) em ordem de crescimento.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Ordene da MAIS rápida (melhor) para a MAIS lenta (pior):') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'multipla', 'estruturas', 'O Ent das Árvores ergue uma raiz com dois caminhos. Em uma árvore binária, quantos filhos um nó pode ter no máximo?', NULL, '[\"1\",\"2\",\"3\",\"Sem limite\"]', '1', 'Árvores binárias limitam cada nó a no máximo dois filhos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='O Ent das Árvores ergue uma raiz com dois caminhos. Em uma árvore binária, quantos filhos um nó pode ter no máximo?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'multipla', 'estruturas', 'Na BST de Marcelo, para onde vão valores menores ou iguais ao nó atual?', NULL, '[\"Esquerda\",\"Direita\",\"Sempre raiz\",\"São descartados\"]', '0', 'Na árvore binária de busca apresentada, menores ou iguais seguem à esquerda.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Na BST de Marcelo, para onde vão valores menores ou iguais ao nó atual?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'multipla', 'estruturas', 'Uma árvore de busca bem distribuída procura valores de forma parecida com qual algoritmo?', NULL, '[\"Bubble sort\",\"Busca binária\",\"Fila FIFO\",\"Hash sem colisão\"]', '1', 'A árvore divide o espaço de busca de modo semelhante à busca binária.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Uma árvore de busca bem distribuída procura valores de forma parecida com qual algoritmo?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'multipla', 'estruturas', 'Qual notação descreve o crescimento de custo de um algoritmo conforme a entrada aumenta?', NULL, '[\"HTML\",\"Big-O\",\"OSI\",\"CRUD\"]', '1', 'Big-O descreve a ordem de crescimento do algoritmo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Qual notação descreve o crescimento de custo de um algoritmo conforme a entrada aumenta?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'multipla', 'estruturas', 'O Ent pergunta: em uma BST, qual percurso normalmente visita os valores em ordem crescente?', NULL, '[\"Pré-ordem\",\"Em ordem\",\"Pós-ordem\",\"Aleatório\"]', '1', 'O percurso em ordem visita esquerda, nó e direita, produzindo ordenação na BST.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='O Ent pergunta: em uma BST, qual percurso normalmente visita os valores em ordem crescente?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'vf', 'estruturas', 'Uma árvore binária de busca mantém uma regra de posicionamento entre valores menores e maiores.', NULL, NULL, 'true', 'Essa regra permite reduzir o espaço de busca.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Uma árvore binária de busca mantém uma regra de posicionamento entre valores menores e maiores.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'vf', 'estruturas', 'Se uma BST fica perfeitamente distribuída, sua busca tende a tempo logarítmico.', NULL, NULL, 'true', 'A altura balanceada fica proporcional a log n.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Se uma BST fica perfeitamente distribuída, sua busca tende a tempo logarítmico.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'vf', 'estruturas', 'Big-O dá mais importância ao crescimento para entradas grandes do que a constantes pequenas.', NULL, NULL, 'true', 'A notação assintótica foca o comportamento de crescimento.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Big-O dá mais importância ao crescimento para entradas grandes do que a constantes pequenas.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'vf', 'estruturas', 'Bubble sort é apresentado como um algoritmo que percorre o vetor várias vezes comparando vizinhos.', NULL, NULL, 'true', 'O bubble sort compara elementos vizinhos e troca quando estão fora de ordem.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Bubble sort é apresentado como um algoritmo que percorre o vetor várias vezes comparando vizinhos.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'vf', 'estruturas', 'Uma árvore binária permite número ilimitado de filhos em cada nó.', NULL, NULL, 'false', 'A limitação de uma árvore binária é ter no máximo dois filhos por nó.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Uma árvore binária permite número ilimitado de filhos em cada nó.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'completar', 'estruturas', 'Complete: uma árvore binária de busca também é conhecida pela sigla ___.', NULL, NULL, '[\"BST\",\"bst\"]', 'BST vem de binary search tree.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Complete: uma árvore binária de busca também é conhecida pela sigla ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'completar', 'estruturas', 'Complete: em uma árvore, o primeiro nó é chamado de ___.', NULL, NULL, '[\"raiz\",\"Raiz\"]', 'Raiz é o nó principal de onde a árvore se desenvolve.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Complete: em uma árvore, o primeiro nó é chamado de ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'completar', 'estruturas', 'Complete: na BST, valores maiores que o nó atual seguem para a ___.', NULL, NULL, '[\"direita\",\"Direita\"]', 'Maiores são posicionados à direita.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Complete: na BST, valores maiores que o nó atual seguem para a ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'completar', 'estruturas', 'Complete: a busca em uma árvore bem distribuída tende a O(___ n).', NULL, NULL, '[\"log\",\"Log\",\"LOG\"]', 'Uma árvore balanceada permite busca logarítmica.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Complete: a busca em uma árvore bem distribuída tende a O(___ n).') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'completar', 'estruturas', 'Complete: o algoritmo da bolha é conhecido como _____ sort.', NULL, NULL, '[\"bubble\",\"Bubble\",\"BUBBLE\"]', 'Bubble sort percorre comparando elementos vizinhos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Complete: o algoritmo da bolha é conhecido como _____ sort.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'ordenar', 'estruturas', 'Ordene da menor para a maior taxa de crescimento.', NULL, '[\"O(n)\",\"O(1)\",\"O(n²)\",\"O(log n)\"]', '[1,3,0,2]', 'Constante cresce menos, depois logarítmico, linear e quadrático.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Ordene da menor para a maior taxa de crescimento.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'ordenar', 'estruturas', 'Ordene o percurso em ordem de uma BST.', NULL, '[\"Visitar nó atual\",\"Percorrer direita\",\"Percorrer esquerda\"]', '[2,0,1]', 'Em ordem significa esquerda, nó, direita.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Ordene o percurso em ordem de uma BST.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'ordenar', 'estruturas', 'Ordene a inserção dos valores 10, 7 e 15 em uma BST inicialmente vazia.', NULL, '[\"Inserir 15 à direita de 10\",\"Inserir 10 como raiz\",\"Inserir 7 à esquerda de 10\"]', '[1,2,0]', '10 vira raiz; 7 é menor e vai à esquerda; 15 é maior e vai à direita.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Ordene a inserção dos valores 10, 7 e 15 em uma BST inicialmente vazia.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'ordenar', 'estruturas', 'Ordene as etapas principais do bubble sort em uma passada.', NULL, '[\"Trocar se estiverem fora de ordem\",\"Comparar elementos vizinhos\",\"Avançar para o próximo par\"]', '[1,0,2]', 'Bubble sort compara vizinhos, troca se necessário e continua varrendo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Ordene as etapas principais do bubble sort em uma passada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'ordenar', 'estruturas', 'Ordene os conceitos do mais estrutural ao mais analítico.', NULL, '[\"Calcular Big-O\",\"Criar nós e filhos\",\"Aplicar regra da BST\",\"Percorrer a árvore\"]', '[1,2,3,0]', 'Primeiro existe a estrutura, depois regras, percursos e análise de custo.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Ordene os conceitos do mais estrutural ao mais analítico.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'erro', 'estruturas', 'O Ent encontrou uma BST com 8 à direita de 10. Qual regra foi quebrada?', NULL, '[\"Valor menor deveria ir à esquerda\",\"Todo valor menor deve ser apagado\",\"Raiz não pode ser número\",\"Busca binária exige fila\"]', '0', 'Se 8 é menor que 10, deve ficar no lado esquerdo da raiz 10.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='O Ent encontrou uma BST com 8 à direita de 10. Qual regra foi quebrada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'erro', 'estruturas', 'O pergaminho diz que O(n²) cresce menos que O(n). Qual é a correção?', NULL, '[\"O(n²) cresce mais rápido que O(n)\",\"O(n) é sempre mais lento que O(n²)\",\"Big-O não compara crescimento\",\"Ambos são constantes\"]', '0', 'Para entradas grandes, crescimento quadrático supera linear.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='O pergaminho diz que O(n²) cresce menos que O(n). Qual é a correção?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'erro', 'estruturas', 'O aprendiz chama de árvore binária uma estrutura em que cada nó tem cinco filhos obrigatórios. O que está errado?', NULL, '[\"Binária permite no máximo dois filhos\",\"Árvore não tem nós\",\"Todo filho deve ser null\",\"BST não possui raiz\"]', '0', 'A árvore binária limita os filhos a esquerdo e direito.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='O aprendiz chama de árvore binária uma estrutura em que cada nó tem cinco filhos obrigatórios. O que está errado?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'erro', 'estruturas', 'O código do Ent procura numa BST ignorando esquerda e direita e varrendo todos os nós. Qual vantagem foi perdida?', NULL, '[\"A redução do espaço de busca\",\"O uso de HTML\",\"A conexão TCP\",\"A fila FIFO\"]', '0', 'A BST existe para orientar a busca pela relação entre valores.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='O código do Ent procura numa BST ignorando esquerda e direita e varrendo todos os nós. Qual vantagem foi perdida?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'erro', 'estruturas', 'A bolha do pântano faz uma única comparação e declara o vetor ordenado. Qual erro existe?', NULL, '[\"Bubble sort precisa de passadas sucessivas\",\"Bubble sort não compara vizinhos\",\"Ordenação exige DNS\",\"Árvores não têm valores\"]', '0', 'Bubble sort costuma percorrer o vetor repetidamente até ordenar.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='A bolha do pântano faz uma única comparação e declara o vetor ordenado. Qual erro existe?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'arrastar', 'estruturas', 'Arraste cada termo da árvore ao significado.', NULL, '{\"itens\":[\"raiz\",\"folha\",\"filho esquerdo\",\"filho direito\"],\"alvos\":[\"Nó principal\",\"Nó sem filhos\",\"Caminho dos menores na BST\",\"Caminho dos maiores na BST\"]}', '[0,1,2,3]', 'A árvore organiza dados por relações hierárquicas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Arraste cada termo da árvore ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'arrastar', 'estruturas', 'Arraste cada custo à descrição.', NULL, '{\"itens\":[\"O(1)\",\"O(log n)\",\"O(n)\",\"O(n²)\"],\"alvos\":[\"Constante\",\"Logarítmico\",\"Linear\",\"Quadrático\"]}', '[0,1,2,3]', 'Essas classes expressam crescimento de custo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Arraste cada custo à descrição.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'arrastar', 'estruturas', 'Arraste cada algoritmo à ideia dominante.', NULL, '{\"itens\":[\"Busca binária\",\"Bubble sort\",\"BST\",\"AVL\"],\"alvos\":[\"Dividir intervalo ordenado\",\"Comparar vizinhos\",\"Árvore com regra de busca\",\"Árvore balanceada\"]}', '[0,1,2,3]', 'Cada técnica organiza ou procura dados de forma distinta.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Arraste cada algoritmo à ideia dominante.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'arrastar', 'estruturas', 'Arraste cada percurso ao início típico.', NULL, '{\"itens\":[\"Pré-ordem\",\"Em ordem\",\"Pós-ordem\",\"Busca na BST\"],\"alvos\":[\"Visita o nó antes dos filhos\",\"Visita esquerda antes do nó\",\"Visita filhos antes do nó\",\"Compara e escolhe lado\"]}', '[0,1,2,3]', 'Percursos definem a ordem de visita dos nós.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Arraste cada percurso ao início típico.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'arrastar', 'estruturas', 'Arraste cada criatura do Ent ao risco correto.', NULL, '{\"itens\":[\"Árvore desbalanceada\",\"Bubble sort em vetor grande\",\"BST balanceada\",\"Big-O ignorado\"],\"alvos\":[\"Pode degradar a busca\",\"Pode ser lento por muitas comparações\",\"Busca tende a log n\",\"Escolha ruim de estrutura\"]}', '[0,1,2,3]', 'Análise e balanceamento evitam escolhas ineficientes.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Arraste cada criatura do Ent ao risco correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'multipla', 'estruturas', 'Numa árvore BINÁRIA, cada nó tem no máximo quantos filhos?', NULL, '[\"1\",\"2\",\"3\",\"ilimitado\"]', '1', 'Binária = no máximo dois filhos por nó (esquerdo e direito).', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Numa árvore BINÁRIA, cada nó tem no máximo quantos filhos?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'multipla', 'estruturas', 'Numa Árvore Binária de Busca (BST), valores MENORES que o nó vão para:', NULL, '[\"A subárvore direita\",\"A subárvore esquerda\",\"A raiz\",\"Fora da árvore\"]', '1', 'Por convenção, menores à esquerda e maiores à direita — é o que torna a busca rápida.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Numa Árvore Binária de Busca (BST), valores MENORES que o nó vão para:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'vf', 'estruturas', 'Numa árvore de busca BALANCEADA, a altura cresce na ordem de log n.', NULL, NULL, 'true', 'Por isso buscas, inserções e remoções saem em O(log n) quando a árvore está equilibrada.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Numa árvore de busca BALANCEADA, a altura cresce na ordem de log n.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'multipla', 'estruturas', 'Qual notação descreve o LIMITE SUPERIOR (pior caso) de um algoritmo?', NULL, '[\"Ômega (Ω)\",\"Big-O (O)\",\"Teta exato (Θ)\",\"Nenhuma\"]', '1', 'Big-O dá o teto de crescimento — quão ruim pode ficar conforme n cresce.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Qual notação descreve o LIMITE SUPERIOR (pior caso) de um algoritmo?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'ordenar', 'estruturas', 'Ordene as complexidades do MELHOR (mais rápido) ao PIOR:', NULL, '[\"O(n log n)\",\"O(1)\",\"O(n)\",\"O(log n)\"]', '[1,3,2,0]', 'O(1) < O(log n) < O(n) < O(n log n) em ordem de crescimento.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Ordene as complexidades do MELHOR (mais rápido) ao PIOR:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'completar', 'estruturas', 'A busca binária descarta, a cada passo, ___ do espaço de busca.', NULL, NULL, '[\"metade\",\"a metade\"]', 'Comparou com o meio e descartou metade — por isso o custo é O(log n).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='A busca binária descarta, a cada passo, ___ do espaço de busca.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'multipla', 'estruturas', 'Buscar um nome num vetor NÃO ordenado, no pior caso, é:', NULL, '[\"O(1)\",\"O(log n)\",\"O(n)\",\"O(0)\"]', '2', 'Sem ordem, pode ser preciso olhar todos os elementos: O(n).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Buscar um nome num vetor NÃO ordenado, no pior caso, é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'multipla', 'estruturas', 'Para aplicar busca binária O(log n), o vetor precisa estar:', NULL, '[\"embaralhado\",\"ordenado\",\"vazio\",\"duplicado\"]', '1', 'A busca binária só funciona em dados ordenados.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Para aplicar busca binária O(log n), o vetor precisa estar:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'vf', 'estruturas', 'Em um algoritmo O(log n), dobrar o tamanho da entrada adiciona apenas um passo.', NULL, NULL, 'true', 'É a essência do crescimento logarítmico.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Em um algoritmo O(log n), dobrar o tamanho da entrada adiciona apenas um passo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'multipla', 'estruturas', 'Marcelo liga o Gol Quadrado diante de um vetor desordenado. No pior caso, a busca linear precisa:', NULL, '[\"Olhar apenas o meio\",\"Olhar todos os elementos\",\"Usar rotação AVL\",\"Criar um hash obrigatório\"]', '1', 'Sem ordem, a busca pode precisar examinar todos os elementos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Marcelo liga o Gol Quadrado diante de um vetor desordenado. No pior caso, a busca linear precisa:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'multipla', 'estruturas', 'Para que o Atalho O(log n) da busca binária funcione, o vetor precisa estar:', NULL, '[\"Criptografado\",\"Ordenado\",\"Vazio\",\"Circular\"]', '1', 'Busca binária exige dados ordenados e acesso direto ao meio.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Para que o Atalho O(log n) da busca binária funcione, o vetor precisa estar:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'multipla', 'estruturas', 'A busca binária começa olhando qual posição do trecho pesquisado?', NULL, '[\"O primeiro elemento sempre\",\"O último elemento sempre\",\"O elemento central\",\"Um elemento aleatório\"]', '2', 'Ela compara com o elemento central para descartar metade do espaço.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='A busca binária começa olhando qual posição do trecho pesquisado?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'multipla', 'estruturas', 'Em uma estrutura encadeada sem acesso direto ao meio, a busca binária fica comprometida porque:', NULL, '[\"É caro chegar ao elemento central\",\"Ela exige firewall\",\"Ela não aceita números\",\"Ela só roda em SQL\"]', '0', 'Busca binária depende de acessar rapidamente o elemento central.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Em uma estrutura encadeada sem acesso direto ao meio, a busca binária fica comprometida porque:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'multipla', 'estruturas', 'Se o alvo é menor que o elemento central em vetor ordenado, qual lado Marcelo descarta?', NULL, '[\"Direito\",\"Esquerdo\",\"Nenhum\",\"Ambos\"]', '0', 'Se o alvo é menor, ele só pode estar à esquerda; o lado direito é descartado.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Se o alvo é menor que o elemento central em vetor ordenado, qual lado Marcelo descarta?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'vf', 'estruturas', 'A busca linear funciona mesmo quando os dados não estão ordenados.', NULL, NULL, 'true', 'Ela simplesmente percorre os elementos em sequência.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='A busca linear funciona mesmo quando os dados não estão ordenados.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'vf', 'estruturas', 'A busca binária descarta parte do espaço de busca a cada comparação.', NULL, NULL, 'true', 'Ela elimina metade do intervalo quando os dados estão ordenados.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='A busca binária descarta parte do espaço de busca a cada comparação.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'vf', 'estruturas', 'Dobrar a entrada em um algoritmo logarítmico aumenta o trabalho muito menos do que em busca linear.', NULL, NULL, 'true', 'Esse é o ganho do crescimento logarítmico.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Dobrar a entrada em um algoritmo logarítmico aumenta o trabalho muito menos do que em busca linear.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'vf', 'estruturas', 'A busca binária é indicada para listas encadeadas porque o acesso ao meio é instantâneo.', NULL, NULL, 'false', 'Em listas encadeadas, chegar ao meio exige percorrer nós.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='A busca binária é indicada para listas encadeadas porque o acesso ao meio é instantâneo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'vf', 'estruturas', 'Em vetor ordenado, a inserção pode exigir deslocar elementos para manter a ordem.', NULL, NULL, 'true', 'Manter ordenação pode custar deslocamentos.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Em vetor ordenado, a inserção pode exigir deslocar elementos para manter a ordem.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'completar', 'estruturas', 'Complete: a busca que testa elemento por elemento é chamada de busca ___.', NULL, NULL, '[\"linear\",\"Linear\"]', 'A busca linear percorre sequencialmente.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Complete: a busca que testa elemento por elemento é chamada de busca ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'completar', 'estruturas', 'Complete: a busca que divide o intervalo ao meio é chamada de busca ___.', NULL, NULL, '[\"binária\",\"binaria\",\"Binária\",\"Binaria\"]', 'A busca binária reduz o intervalo a cada comparação.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Complete: a busca que divide o intervalo ao meio é chamada de busca ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'completar', 'estruturas', 'Complete: a busca binária em vetor ordenado tem crescimento O(___ n).', NULL, NULL, '[\"log\",\"Log\",\"LOG\"]', 'Ela tem comportamento logarítmico.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Complete: a busca binária em vetor ordenado tem crescimento O(___ n).') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'completar', 'estruturas', 'Complete: para busca binária, os dados precisam estar em ordem ___.', NULL, NULL, '[\"ordenada\",\"Ordenada\",\"ordenado\",\"Ordenado\"]', 'Sem ordenação, não é possível descartar metade com segurança.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Complete: para busca binária, os dados precisam estar em ordem ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'completar', 'estruturas', 'Complete: em busca linear, no pior caso, o custo é O(___).', NULL, NULL, '[\"n\",\"N\"]', 'No pior caso, todos os elementos são examinados.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Complete: em busca linear, no pior caso, o custo é O(___).') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'ordenar', 'estruturas', 'Ordene a busca binária pelo valor 7 no vetor 1, 3, 5, 7, 9, 11, 13.', NULL, '[\"Comparar com 7 e encontrar\",\"Olhar o meio do vetor\",\"Escolher metade correta\"]', '[1,0,2]', 'O meio já é 7 nesse exemplo; a busca termina ali.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Ordene a busca binária pelo valor 7 no vetor 1, 3, 5, 7, 9, 11, 13.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'ordenar', 'estruturas', 'Ordene o raciocínio da busca binária quando o alvo é maior que o meio.', NULL, '[\"Descartar metade esquerda\",\"Comparar com o elemento central\",\"Continuar na metade direita\"]', '[1,0,2]', 'Se o alvo é maior que o meio, ele só pode estar à direita.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Ordene o raciocínio da busca binária quando o alvo é maior que o meio.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'ordenar', 'estruturas', 'Ordene da busca menos eficiente para a mais eficiente em dados ordenados com acesso direto.', NULL, '[\"Busca binária\",\"Busca linear\"]', '[1,0]', 'Linear é O(n); binária é O(log n).', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Ordene da busca menos eficiente para a mais eficiente em dados ordenados com acesso direto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'ordenar', 'estruturas', 'Ordene os passos para inserir 6 no vetor ordenado 2, 4, 8, 10.', NULL, '[\"Inserir 6 na lacuna\",\"Encontrar a posição correta\",\"Deslocar 8 e 10 à direita\"]', '[1,2,0]', 'Para manter ordem, localiza a posição, abre espaço e insere.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Ordene os passos para inserir 6 no vetor ordenado 2, 4, 8, 10.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'ordenar', 'estruturas', 'Ordene o crescimento do menor para o maior.', NULL, '[\"O(n)\",\"O(log n)\",\"O(n²)\"]', '[1,0,2]', 'Logarítmico cresce menos que linear, que cresce menos que quadrático.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Ordene o crescimento do menor para o maior.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'erro', 'estruturas', 'O Eco da Busca Linear tentou usar busca binária neste vetor. Qual falha impede o atalho?', '[8, 3, 10, 1, 7]', '[\"O vetor não está ordenado\",\"O vetor tem números\",\"O vetor é pequeno\",\"Busca binária nunca usa vetor\"]', '0', 'Busca binária exige ordem para decidir qual metade descartar.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='O Eco da Busca Linear tentou usar busca binária neste vetor. Qual falha impede o atalho?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'erro', 'estruturas', 'O aprendiz compara o alvo com o meio, mas descarta o lado errado. Se alvo menor que meio, qual lado deve ser descartado?', NULL, '[\"Direito\",\"Esquerdo\",\"Nenhum\",\"O centro apenas\"]', '0', 'Se o alvo é menor, os maiores à direita não servem.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='O aprendiz compara o alvo com o meio, mas descarta o lado errado. Se alvo menor que meio, qual lado deve ser descartado?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'erro', 'estruturas', 'Marcelo diz que O(log n) e O(n) crescem igual. Qual correção vence a corrida?', NULL, '[\"O(log n) cresce mais lentamente que O(n)\",\"O(n) é sempre constante\",\"O(log n) é pior que O(n²)\",\"Não existe crescimento logarítmico\"]', '0', 'Crescimento logarítmico aumenta devagar em relação ao linear.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Marcelo diz que O(log n) e O(n) crescem igual. Qual correção vence a corrida?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'erro', 'estruturas', 'O Gol quer acessar o meio de uma lista encadeada como se fosse vetor. Qual problema aparece?', NULL, '[\"Não há acesso direto ao índice central\",\"A lista vira árvore AVL\",\"A busca deixa de comparar\",\"O heap desaparece\"]', '0', 'Na lista encadeada, é preciso seguir referências até chegar ao meio.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='O Gol quer acessar o meio de uma lista encadeada como se fosse vetor. Qual problema aparece?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'erro', 'estruturas', 'Em uma lista ordenada, o aprendiz insere 5 depois de 9 e mantém a lista como ordenada. Qual erro ocorreu?', NULL, '[\"A ordem foi quebrada\",\"A lista ficou mais rápida\",\"A busca binária foi garantida\",\"O vetor virou pilha\"]', '0', 'Inserções precisam preservar a ordem para buscas eficientes.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Em uma lista ordenada, o aprendiz insere 5 depois de 9 e mantém a lista como ordenada. Qual erro ocorreu?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'arrastar', 'estruturas', 'Arraste cada busca à condição principal.', NULL, '{\"itens\":[\"Busca linear\",\"Busca binária\",\"Vetor ordenado\",\"Lista encadeada\"],\"alvos\":[\"Percorre item por item\",\"Divide intervalo ao meio\",\"Permite descartar metades\",\"Não oferece acesso direto ao meio\"]}', '[0,1,2,3]', 'O tipo de estrutura define a estratégia eficiente.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Arraste cada busca à condição principal.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'arrastar', 'estruturas', 'Arraste cada custo à busca típica.', NULL, '{\"itens\":[\"O(n)\",\"O(log n)\",\"O(1)\",\"O(n²)\"],\"alvos\":[\"Busca linear no pior caso\",\"Busca binária\",\"Acesso direto a índice de vetor\",\"Alguns algoritmos com laços aninhados\"]}', '[0,1,2,3]', 'Cada padrão de acesso tem um crescimento típico.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Arraste cada custo à busca típica.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'arrastar', 'estruturas', 'Arraste cada situação à escolha adequada.', NULL, '{\"itens\":[\"Dados desordenados e poucos\",\"Vetor ordenado grande\",\"Lista encadeada sem índice direto\",\"Precisa manter ordem após inserir\"],\"alvos\":[\"Busca linear aceitável\",\"Busca binária indicada\",\"Evitar busca binária direta\",\"Inserir na posição correta\"]}', '[0,1,2,3]', 'O atalho depende de ordem e acesso eficiente.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Arraste cada situação à escolha adequada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'arrastar', 'estruturas', 'Arraste cada comparação da busca binária à ação.', NULL, '{\"itens\":[\"alvo menor que meio\",\"alvo maior que meio\",\"alvo igual ao meio\",\"intervalo vazio\"],\"alvos\":[\"Pesquisar esquerda\",\"Pesquisar direita\",\"Encontrou\",\"Não encontrado\"]}', '[0,1,2,3]', 'A comparação central decide o próximo intervalo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Arraste cada comparação da busca binária à ação.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'arrastar', 'estruturas', 'Arraste cada placa do caminho ao significado.', NULL, '{\"itens\":[\"meio\",\"inicio\",\"fim\",\"intervalo\"],\"alvos\":[\"Elemento comparado\",\"Limite inferior\",\"Limite superior\",\"Trecho ainda pesquisado\"]}', '[0,1,2,3]', 'Busca binária controla os limites do intervalo pesquisado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Arraste cada placa do caminho ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'multipla', 'estruturas', 'Por que ordenar um vetor antes de fazer MUITAS buscas costuma compensar?', NULL, '[\"Ordenar deixa a memória menor\",\"Ordena-se uma vez e depois usa-se busca binária O(log n) muitas vezes\",\"Busca em vetor ordenado é O(1) sempre\",\"Não compensa nunca\"]', '1', 'O custo único da ordenação se dilui em muitas buscas logarítmicas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Por que ordenar um vetor antes de fazer MUITAS buscas costuma compensar?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'vf', 'estruturas', 'Uma tabela hash bem dimensionada permite busca em tempo MÉDIO O(1).', NULL, NULL, 'true', 'A função de hash leva direto ao \"balde\"; em média, acesso constante.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Uma tabela hash bem dimensionada permite busca em tempo MÉDIO O(1).') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'multipla', 'estruturas', 'Num vetor ORDENADO de 1.000.000 itens, a busca binária faz no máximo cerca de:', NULL, '[\"1.000.000 comparações\",\"500.000 comparações\",\"20 comparações\",\"1 comparação\"]', '2', 'log2(1.000.000) ≈ 20: dobrar pela metade vinte vezes basta.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Num vetor ORDENADO de 1.000.000 itens, a busca binária faz no máximo cerca de:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'completar', 'estruturas', 'A estrutura chave→valor com acesso médio O(1) é a tabela ___.', NULL, NULL, '[\"hash\"]', 'A tabela hash mapeia chaves a posições via função de hash.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='A estrutura chave→valor com acesso médio O(1) é a tabela ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'multipla', 'estruturas', 'O que toda função recursiva precisa ter para não rodar infinitamente?', NULL, '[\"Um laço for\",\"Um caso base (condição de parada)\",\"Uma variável global\",\"Dois parâmetros\"]', '1', 'O caso base interrompe a recursão e evita o laço infinito.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='O que toda função recursiva precisa ter para não rodar infinitamente?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'multipla', 'estruturas', 'Com fatorial(n) = n * fatorial(n-1) e fatorial(0) = 1, quanto vale fatorial(3)?', NULL, '[\"3\",\"6\",\"9\",\"1\"]', '1', '3 * 2 * 1 = 6.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Com fatorial(n) = n * fatorial(n-1) e fatorial(0) = 1, quanto vale fatorial(3)?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'erro', 'estruturas', 'Por que esta recursão nunca termina?', 'function f($n) {\n  return f($n - 1);\n}', '[\"Falta retorno\",\"Não tem caso base\",\"Usa subtração\",\"Nada de errado\"]', '1', 'Sem uma condição de parada, f chama a si mesma para sempre.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Por que esta recursão nunca termina?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'vf', 'estruturas', 'Recursão é quando uma função chama a si mesma.', NULL, NULL, 'true', 'Exatamente: a função se invoca para resolver um subproblema menor.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Recursão é quando uma função chama a si mesma.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'ordenar', 'estruturas', 'Ordene as chamadas empilhadas de fatorial(3) até a base:', NULL, '[\"fatorial(0)\",\"fatorial(3)\",\"fatorial(1)\",\"fatorial(2)\"]', '[1,3,2,0]', 'fatorial(3) chama (2), que chama (1), que chama (0): a base.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Ordene as chamadas empilhadas de fatorial(3) até a base:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'multipla', 'estruturas', 'A Hidra chama a si mesma para dividir a batalha em partes menores. O que uma recursão precisa para não ser infinita?', NULL, '[\"Caso base\",\"Conector RJ-45\",\"Comando SELECT\",\"View\"]', '0', 'O caso base encerra as chamadas recursivas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='A Hidra chama a si mesma para dividir a batalha em partes menores. O que uma recursão precisa para não ser infinita?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'multipla', 'estruturas', 'No quick sort do pântano, a estratégia central é:', NULL, '[\"Dividir a lista e ordenar partes recursivamente\",\"Atender em FIFO\",\"Sempre comparar apenas vizinhos uma vez\",\"Eliminar todos os nós\"]', '0', 'O quick sort subdivide a lista e ordena recursivamente.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='No quick sort do pântano, a estratégia central é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'multipla', 'estruturas', 'Quando várias chamadas de função se acumulam, qual área de memória registra o retorno de cada chamada?', NULL, '[\"Pilha de execução\",\"Tabela DNS\",\"Vetor de hash\",\"Fila de impressão\"]', '0', 'A pilha de execução armazena chamadas, variáveis locais e pontos de retorno.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Quando várias chamadas de função se acumulam, qual área de memória registra o retorno de cada chamada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'multipla', 'estruturas', 'Em uma tabela hash, o que é colisão?', NULL, '[\"Duas chaves caírem no mesmo índice\",\"Um nó ter dois filhos\",\"Uma fila ficar vazia\",\"Um vetor estar ordenado\"]', '0', 'Colisão acontece quando códigos hash levam ao mesmo índice.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Em uma tabela hash, o que é colisão?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'multipla', 'estruturas', 'O bubble sort da Hidra troca elementos quando:', NULL, '[\"Vizinhos estão fora de ordem\",\"A pilha está vazia\",\"O hash é null\",\"A raiz é preta\"]', '0', 'Bubble sort compara vizinhos e troca quando necessário.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='O bubble sort da Hidra troca elementos quando:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'vf', 'estruturas', 'Uma função recursiva sem condição de parada pode continuar chamando a si mesma indefinidamente.', NULL, NULL, 'true', 'Sem caso base, a recursão não termina.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Uma função recursiva sem condição de parada pode continuar chamando a si mesma indefinidamente.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'vf', 'estruturas', 'A pilha de execução registra o ponto ao qual o programa deve retornar após uma chamada de função.', NULL, NULL, 'true', 'A call stack guarda informações de retorno e variáveis locais.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='A pilha de execução registra o ponto ao qual o programa deve retornar após uma chamada de função.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'vf', 'estruturas', 'Colisões hash são impossíveis porque todo código hash é único.', NULL, NULL, 'false', 'Como o vetor é finito, colisões podem ocorrer.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Colisões hash são impossíveis porque todo código hash é único.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'vf', 'estruturas', 'Bubble sort compara elementos vizinhos durante suas passadas.', NULL, NULL, 'true', 'Esse é o comportamento básico do algoritmo da bolha.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Bubble sort compara elementos vizinhos durante suas passadas.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'vf', 'estruturas', 'Rehash pode ampliar a estrutura quando há muitas colisões ou ocupação.', NULL, NULL, 'true', 'Rehash recalcula posições em uma estrutura maior.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Rehash pode ampliar a estrutura quando há muitas colisões ou ocupação.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'completar', 'estruturas', 'Complete: a condição que encerra uma recursão é chamada de caso ___.', NULL, NULL, '[\"base\",\"Base\"]', 'Caso base impede recursão infinita.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Complete: a condição que encerra uma recursão é chamada de caso ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'completar', 'estruturas', 'Complete: a área que empilha chamadas de função é a pilha de ___.', NULL, NULL, '[\"execução\",\"execucao\",\"chamada\",\"chamadas\"]', 'A pilha de execução também é chamada de call stack.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Complete: a área que empilha chamadas de função é a pilha de ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'completar', 'estruturas', 'Complete: quando duas chaves caem no mesmo índice da tabela hash, ocorre uma ___.', NULL, NULL, '[\"colisão\",\"colisao\",\"Colisão\",\"Colisao\"]', 'Colisão é conflito de índice em hashing.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Complete: quando duas chaves caem no mesmo índice da tabela hash, ocorre uma ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'completar', 'estruturas', 'Complete: no hashing, recalcular posições após ampliar a tabela é chamado de ___.', NULL, NULL, '[\"rehash\",\"Rehash\",\"REHASH\"]', 'Rehash reorganiza os elementos conforme uma nova capacidade.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Complete: no hashing, recalcular posições após ampliar a tabela é chamado de ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'completar', 'estruturas', 'Complete: o algoritmo da bolha é o _____ sort.', NULL, NULL, '[\"bubble\",\"Bubble\",\"BUBBLE\"]', 'Bubble sort é o nome do algoritmo da bolha.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Complete: o algoritmo da bolha é o _____ sort.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'ordenar', 'estruturas', 'Ordene as chamadas de uma recursão que reduz n até 0.', NULL, '[\"chamada com n = 0\",\"chamada com n = 3\",\"chamada com n = 1\",\"chamada com n = 2\"]', '[1,3,2,0]', 'A chamada vai reduzindo até alcançar o caso base.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Ordene as chamadas de uma recursão que reduz n até 0.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'ordenar', 'estruturas', 'Ordene a volta da pilha de execução após chegar ao caso base n = 0.', NULL, '[\"retorna n = 3\",\"retorna n = 0\",\"retorna n = 2\",\"retorna n = 1\"]', '[1,3,2,0]', 'Depois do caso base, as chamadas retornam na ordem inversa.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Ordene a volta da pilha de execução após chegar ao caso base n = 0.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'ordenar', 'estruturas', 'Ordene o tratamento simples de colisão com encadeamento.', NULL, '[\"Percorrer a lista do índice\",\"Calcular o índice pela função hash\",\"Inserir ou comparar na lista\"]', '[1,0,2]', 'Hash escolhe o índice; a lista do balde resolve a colisão.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Ordene o tratamento simples de colisão com encadeamento.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'ordenar', 'estruturas', 'Ordene uma passada do bubble sort.', NULL, '[\"Trocar se necessário\",\"Comparar vizinhos\",\"Avançar para o próximo par\"]', '[1,0,2]', 'Bubble sort repete comparações de vizinhos ao longo do vetor.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Ordene uma passada do bubble sort.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'ordenar', 'estruturas', 'Ordene a estratégia do quick sort em alto nível.', NULL, '[\"Combinar resultado ordenado\",\"Escolher pivô e particionar\",\"Ordenar recursivamente as partes\"]', '[1,2,0]', 'Quick sort divide por pivô, ordena partes e obtém a sequência ordenada.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Ordene a estratégia do quick sort em alto nível.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'erro', 'estruturas', 'A Hidra escreveu esta função. Qual maldição aparece?', 'processar(n) chama processar(n - 1) sem testar parada', '[\"Falta caso base\",\"Falta cabo UTP\",\"Falta ORDER BY\",\"Está perfeitamente segura\"]', '0', 'Sem condição de parada, a recursão pode nunca terminar.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='A Hidra escreveu esta função. Qual maldição aparece?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'erro', 'estruturas', 'A tabela hash joga todos os livros no índice 0 e nunca compara os elementos da lista. Qual é o erro?', NULL, '[\"Ignora colisões dentro do mesmo balde\",\"Usa busca binária corretamente\",\"Está usando fila FIFO\",\"Está balanceando árvore\"]', '0', 'Com colisões, é preciso verificar os elementos do balde.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='A tabela hash joga todos os livros no índice 0 e nunca compara os elementos da lista. Qual é o erro?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'erro', 'estruturas', 'No bubble sort, o aprendiz compara elementos distantes e nunca compara vizinhos. Qual regra foi quebrada?', NULL, '[\"Bubble sort compara pares vizinhos\",\"Bubble sort só usa hash\",\"Bubble sort exige recursão\",\"Bubble sort não ordena vetores\"]', '0', 'A bolha sobe por comparações entre vizinhos.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='No bubble sort, o aprendiz compara elementos distantes e nunca compara vizinhos. Qual regra foi quebrada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'erro', 'estruturas', 'A Hidra faz rehash, mas mantém os elementos nos índices antigos sem recalcular. Qual problema surge?', NULL, '[\"As posições podem ficar incorretas na nova tabela\",\"A árvore fica rubro-negra\",\"A pilha vira fila\",\"A lista passa a ser estática\"]', '0', 'Ao mudar a capacidade, os índices precisam ser recalculados.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='A Hidra faz rehash, mas mantém os elementos nos índices antigos sem recalcular. Qual problema surge?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'erro', 'estruturas', 'O aprendiz empilha chamadas recursivas, mas esquece que retornam em ordem inversa. Que estrutura explica isso?', NULL, '[\"Pilha de execução LIFO\",\"Fila circular FIFO\",\"Tabela HTML\",\"Rede WAN\"]', '0', 'Chamadas retornam em ordem LIFO pela pilha de execução.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='O aprendiz empilha chamadas recursivas, mas esquece que retornam em ordem inversa. Que estrutura explica isso?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'arrastar', 'estruturas', 'Arraste cada cabeça da Hidra ao conceito correto.', NULL, '{\"itens\":[\"recursão\",\"caso base\",\"pilha de execução\",\"retorno\"],\"alvos\":[\"Função chama a si mesma\",\"Condição de parada\",\"Armazena chamadas\",\"Desempilha a chamada concluída\"]}', '[0,1,2,3]', 'Recursão depende de chamadas empilhadas e de uma parada clara.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Arraste cada cabeça da Hidra ao conceito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'arrastar', 'estruturas', 'Arraste cada termo de hash ao significado.', NULL, '{\"itens\":[\"função hash\",\"colisão\",\"balde\",\"rehash\"],\"alvos\":[\"Calcula índice\",\"Conflito de índice\",\"Lista ou espaço de armazenamento\",\"Recalcula após ampliar\"]}', '[0,1,2,3]', 'Tabelas hash dependem de cálculo de índice e tratamento de colisões.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Arraste cada termo de hash ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'arrastar', 'estruturas', 'Arraste cada algoritmo ao comportamento.', NULL, '{\"itens\":[\"bubble sort\",\"quick sort\",\"insertion sort\",\"busca binária\"],\"alvos\":[\"Compara vizinhos\",\"Divide e ordena recursivamente\",\"Insere no ponto correto\",\"Descarta metade do intervalo\"]}', '[0,1,2,3]', 'Cada algoritmo usa uma estratégia própria.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Arraste cada algoritmo ao comportamento.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'arrastar', 'estruturas', 'Arraste cada risco ao antídoto.', NULL, '{\"itens\":[\"recursão infinita\",\"muitas colisões\",\"vetor quase ordenado\",\"tabela muito cheia\"],\"alvos\":[\"caso base\",\"melhor função hash ou encadeamento\",\"insertion sort pode ser útil\",\"rehash\"]}', '[0,1,2,3]', 'Cada problema pede uma técnica de controle adequada.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Arraste cada risco ao antídoto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'arrastar', 'estruturas', 'Arraste cada elemento da chamada recursiva ao papel.', NULL, '{\"itens\":[\"parâmetro menor\",\"condição de parada\",\"chamada pendente\",\"resultado retornado\"],\"alvos\":[\"Aproxima do caso base\",\"Interrompe a expansão\",\"Fica na pilha\",\"Volta para quem chamou\"]}', '[0,1,2,3]', 'Uma recursão saudável avança para a parada e retorna resultados.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Arraste cada elemento da chamada recursiva ao papel.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'multipla', 'estruturas', 'O que acontece na pilha de execução a cada chamada recursiva?', NULL, '[\"Nada\",\"Empilha-se um novo registro de ativação (frame)\",\"A pilha é esvaziada\",\"O programa encerra\"]', '1', 'Cada chamada empilha um frame; ao retornar, ele é desempilhado.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='O que acontece na pilha de execução a cada chamada recursiva?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'multipla', 'estruturas', 'Um \"stack overflow\" numa recursão acontece tipicamente quando:', NULL, '[\"Há um caso base correto\",\"Não há caso base, ou ele nunca é atingido\",\"A função retorna cedo demais\",\"Há poucos parâmetros\"]', '1', 'Sem parada, a pilha cresce sem fim até estourar a memória reservada.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Um \"stack overflow\" numa recursão acontece tipicamente quando:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'vf', 'estruturas', 'Toda recursão pode, em princípio, ser reescrita como um laço (iteração).', NULL, NULL, 'true', 'Recursão e iteração têm o mesmo poder; às vezes a versão iterativa usa uma pilha explícita.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Toda recursão pode, em princípio, ser reescrita como um laço (iteração).') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'multipla', 'estruturas', 'Com fib(n) = fib(n-1) + fib(n-2), fib(0)=0 e fib(1)=1, quanto vale fib(5)?', NULL, '[\"3\",\"5\",\"8\",\"13\"]', '1', 'Sequência: 0, 1, 1, 2, 3, 5 — fib(5) = 5.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Com fib(n) = fib(n-1) + fib(n-2), fib(0)=0 e fib(1)=1, quanto vale fib(5)?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'erro', 'estruturas', 'Por que esta recursão estoura a pilha?', 'function conta($n) {\n  return conta($n + 1);\n}', '[\"Usa soma\",\"Não tem caso base e n só cresce, nunca parando\",\"Falta um parâmetro\",\"Não há erro\"]', '1', 'Sem condição de parada e com n sempre crescendo, ela se chama para sempre.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Por que esta recursão estoura a pilha?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'ordenar', 'estruturas', 'fatorial(3) com base fatorial(0)=1. Ordene os RETORNOS, da base ao topo:', NULL, '[\"fatorial(2) retorna 2\",\"fatorial(0) retorna 1\",\"fatorial(1) retorna 1\",\"fatorial(3) retorna 6\"]', '[1,2,0,3]', 'A base resolve primeiro: 1 → 1 → 2 → 6, subindo a pilha de volta.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='fatorial(3) com base fatorial(0)=1. Ordene os RETORNOS, da base ao topo:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'multipla', 'logica', 'Qual o próximo número? 2, 4, 6, 8, ...', NULL, '[\"9\",\"10\",\"12\",\"16\"]', '1', 'Progressão aritmética de razão 2: o próximo é 10.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Qual o próximo número? 2, 4, 6, 8, ...') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'multipla', 'logica', 'Fibonacci: 0, 1, 1, 2, 3, 5, 8, ... qual o próximo?', NULL, '[\"11\",\"12\",\"13\",\"15\"]', '2', 'Cada termo é a soma dos dois anteriores: 5 + 8 = 13.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Fibonacci: 0, 1, 1, 2, 3, 5, 8, ... qual o próximo?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'multipla', 'logica', 'Qual o próximo? 1, 2, 4, 8, 16, ...', NULL, '[\"24\",\"32\",\"20\",\"18\"]', '1', 'Progressão geométrica de razão 2: 16 * 2 = 32.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Qual o próximo? 1, 2, 4, 8, 16, ...') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'completar', 'logica', 'Na sequência 5, 10, 15, 20 a razão (diferença constante) é ___', NULL, NULL, '[\"5\"]', 'Cada termo aumenta de 5 em 5.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Na sequência 5, 10, 15, 20 a razão (diferença constante) é ___') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'multipla', 'logica', 'No altar gelado de Cesar, o aprendiz observa f(x) quando x se aproxima de 3. O limite descreve:', NULL, '[\"o valor que a função tende a assumir perto de 3\",\"a quantidade de fases do jogo\",\"sempre o valor exato de f(3)\",\"apenas números inteiros\"]', '0', 'Limite descreve o comportamento da função quando x se aproxima de um ponto.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='No altar gelado de Cesar, o aprendiz observa f(x) quando x se aproxima de 3. O limite descreve:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'multipla', 'logica', 'O Eco Numérico só abre a passagem se os limites laterais em um ponto forem:', NULL, '[\"iguais\",\"sempre positivos\",\"sempre infinitos\",\"diferentes\"]', '0', 'Para existir limite bilateral, os limites pela esquerda e pela direita devem coincidir.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='O Eco Numérico só abre a passagem se os limites laterais em um ponto forem:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'multipla', 'logica', 'Para uma função ser contínua em x = a, qual condição precisa acontecer?', NULL, '[\"o limite existe, f(a) existe e ambos são iguais\",\"f(a) precisa ser infinito\",\"a função deve ser quadrática\",\"o limite pela esquerda deve ser negativo\"]', '0', 'Continuidade exige valor definido, limite existente e igualdade entre limite e valor da função.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Para uma função ser contínua em x = a, qual condição precisa acontecer?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'multipla', 'logica', 'Quando x caminha para muito longe na montanha, Cesar pergunta pelo limite no infinito de 1/x. A tendência é:', NULL, '[\"0\",\"1\",\"infinito\",\"não muda\"]', '0', 'Conforme x cresce sem limite, 1/x se aproxima de 0.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Quando x caminha para muito longe na montanha, Cesar pergunta pelo limite no infinito de 1/x. A tendência é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'multipla', 'logica', 'A runa f(x) = (x^2 - 4)/(x - 2) falha em x = 2, mas o portal quer o limite quando x se aproxima de 2. Qual é o valor?', NULL, '[\"4\",\"0\",\"2\",\"não existe por decreto\"]', '0', 'Fatorando x^2 - 4 = (x - 2)(x + 2), sobra x + 2; perto de 2, o limite é 4.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='A runa f(x) = (x^2 - 4)/(x - 2) falha em x = 2, mas o portal quer o limite quando x se aproxima de 2. Qual é o valor?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'vf', 'logica', 'Um limite pode existir mesmo que a função não esteja definida exatamente no ponto observado.', NULL, NULL, 'true', 'O limite depende da aproximação, não necessariamente do valor no ponto.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Um limite pode existir mesmo que a função não esteja definida exatamente no ponto observado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'vf', 'logica', 'Se o limite pela esquerda e o limite pela direita são diferentes, o limite bilateral existe.', NULL, NULL, 'false', 'Limites laterais diferentes impedem a existência do limite bilateral.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Se o limite pela esquerda e o limite pela direita são diferentes, o limite bilateral existe.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'vf', 'logica', 'A continuidade em um ponto exige que não haja ruptura entre o limite e o valor da função naquele ponto.', NULL, NULL, 'true', 'A função é contínua quando o comportamento ao redor combina com o valor no ponto.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='A continuidade em um ponto exige que não haja ruptura entre o limite e o valor da função naquele ponto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'vf', 'logica', 'Limites no infinito ajudam a entender o comportamento da função para valores muito grandes de x.', NULL, NULL, 'true', 'Eles analisam a tendência da função quando x cresce ou decresce indefinidamente.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Limites no infinito ajudam a entender o comportamento da função para valores muito grandes de x.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'vf', 'logica', 'Se f(2) não existe, então nenhum limite quando x se aproxima de 2 pode existir.', NULL, NULL, 'false', 'Uma descontinuidade removível pode ter limite mesmo sem valor definido no ponto.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Se f(2) não existe, então nenhum limite quando x se aproxima de 2 pode existir.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'completar', 'logica', 'Complete a palavra mágica: o comportamento de f(x) quando x se aproxima de a é chamado de ___.', NULL, NULL, '[\"limite\",\"Limite\"]', 'Limite é o conceito central da fase Sequências e Ritmo.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Complete a palavra mágica: o comportamento de f(x) quando x se aproxima de a é chamado de ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'completar', 'logica', 'Complete: quando os limites pela esquerda e pela direita são iguais, o limite bilateral ___ .', NULL, NULL, '[\"existe\",\"Existe\"]', 'A igualdade dos limites laterais garante a existência do limite bilateral.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Complete: quando os limites pela esquerda e pela direita são iguais, o limite bilateral ___ .') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'completar', 'logica', 'Complete: uma função sem salto, furo ou quebra no ponto é chamada de ___.', NULL, NULL, '[\"contínua\",\"continua\",\"Contínua\",\"Continua\"]', 'A continuidade expressa a ausência de ruptura naquele ponto.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Complete: uma função sem salto, furo ou quebra no ponto é chamada de ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'completar', 'logica', 'Complete: em lim x -> +infinito, investigamos o comportamento da função quando x cresce sem ___.', NULL, NULL, '[\"limite\",\"Limite\"]', 'O limite no infinito observa a tendência para x cada vez maior.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Complete: em lim x -> +infinito, investigamos o comportamento da função quando x cresce sem ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'completar', 'logica', 'Complete o valor que abre a porta: lim x -> 2 de (x^2 - 4)/(x - 2) = ___.', NULL, NULL, '[\"4\"]', 'Após simplificar, a expressão se comporta como x + 2, que tende a 4.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Complete o valor que abre a porta: lim x -> 2 de (x^2 - 4)/(x - 2) = ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'ordenar', 'logica', 'Ordene o ritual de Cesar para verificar continuidade em x = a.', NULL, '[\"Calcular o limite quando x se aproxima de a\",\"Verificar se f(a) existe\",\"Comparar o limite com f(a)\",\"Declarar continuidade se forem iguais\"]', '[1,0,2,3]', 'A continuidade exige valor definido, limite existente e igualdade entre eles.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Ordene o ritual de Cesar para verificar continuidade em x = a.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'ordenar', 'logica', 'Ordene a travessia dos limites laterais.', NULL, '[\"Analisar x vindo pela esquerda\",\"Analisar x vindo pela direita\",\"Comparar os dois resultados\",\"Concluir se o limite bilateral existe\"]', '[0,1,2,3]', 'Os limites laterais são comparados para decidir a existência do limite.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Ordene a travessia dos limites laterais.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'ordenar', 'logica', 'Ordene a simplificação da runa (x^2 - 4)/(x - 2) para x -> 2.', NULL, '[\"Fatorar x^2 - 4\",\"Cancelar o fator comum x - 2\",\"Obter x + 2 para x diferente de 2\",\"Substituir a tendência x -> 2\"]', '[0,1,2,3]', 'A simplificação remove o obstáculo e revela a tendência igual a 4.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Ordene a simplificação da runa (x^2 - 4)/(x - 2) para x -> 2.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'ordenar', 'logica', 'Ordene o pensamento para um limite no infinito.', NULL, '[\"Observar a função para x muito grande\",\"Identificar os termos dominantes\",\"Comparar o crescimento dos termos\",\"Determinar a tendência final\"]', '[0,1,2,3]', 'Limites no infinito dependem do comportamento dominante da função.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Ordene o pensamento para um limite no infinito.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'ordenar', 'logica', 'Ordene a leitura de um gráfico na Ponte dos Ritmos.', NULL, '[\"Localizar o ponto de aproximação no eixo x\",\"Seguir a curva pela esquerda\",\"Seguir a curva pela direita\",\"Verificar se as alturas se encontram\"]', '[0,1,2,3]', 'No gráfico, o limite depende da altura para a qual a curva se aproxima pelos lados.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Ordene a leitura de um gráfico na Ponte dos Ritmos.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'erro', 'logica', 'O aprendiz escreveu: se f(a) não existe, o limite em a nunca existe. Qual é o erro da runa?', NULL, '[\"Confunde limite com valor da função no ponto\",\"Confunde derivada com integral\",\"Esquece que x precisa ser inteiro\",\"Nada está errado\"]', '0', 'O limite pode existir mesmo quando f(a) não está definido.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='O aprendiz escreveu: se f(a) não existe, o limite em a nunca existe. Qual é o erro da runa?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'erro', 'logica', 'Cesar nota uma falha: lim pela esquerda = 2 e lim pela direita = 5, mas o aprendiz declarou limite = 7. Qual é o problema?', NULL, '[\"Limites laterais diferentes impedem o limite bilateral\",\"Deveria somar 2 com 5\",\"Todo limite lateral vira infinito\",\"O valor correto é sempre zero\"]', '0', 'Quando os laterais são diferentes, o limite bilateral não existe.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Cesar nota uma falha: lim pela esquerda = 2 e lim pela direita = 5, mas o aprendiz declarou limite = 7. Qual é o problema?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'erro', 'logica', 'Qual erro conceitual aparece na frase: continuidade significa apenas f(a) existir?', NULL, '[\"Falta exigir que o limite exista e seja igual a f(a)\",\"Continuidade não usa função\",\"f(a) nunca pode existir\",\"Toda função é contínua por padrão\"]', '0', 'f(a) existir é necessário, mas não suficiente para continuidade.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Qual erro conceitual aparece na frase: continuidade significa apenas f(a) existir?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'erro', 'logica', 'A runa diz: 1/x tende a 1 quando x vai para +infinito. Onde está a falha?', NULL, '[\"1/x tende a 0 para x muito grande\",\"1/x cresce para infinito\",\"1/x vira sempre -1\",\"Não há como analisar no infinito\"]', '0', 'Quanto maior x, menor fica 1/x, aproximando-se de 0.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='A runa diz: 1/x tende a 1 quando x vai para +infinito. Onde está a falha?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'erro', 'logica', 'No mapa, o aprendiz substituiu x = 2 diretamente em (x^2 - 4)/(x - 2) e desistiu. O que ele deveria tentar antes?', NULL, '[\"Simplificar a expressão para estudar a tendência\",\"Trocar limite por soma finita\",\"Ignorar o denominador\",\"Declarar sempre infinito\"]', '0', 'Em indeterminações removíveis, fatorar e simplificar pode revelar o limite.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='No mapa, o aprendiz substituiu x = 2 diretamente em (x^2 - 4)/(x - 2) e desistiu. O que ele deveria tentar antes?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'arrastar', 'logica', 'Arraste cada símbolo da Montanha do Cálculo ao seu significado.', NULL, '{\"itens\":[\"Limite\",\"Continuidade\",\"Limite lateral\",\"Limite no infinito\"],\"alvos\":[\"Tendência ao aproximar\",\"Sem ruptura no ponto\",\"Aproximação por um lado\",\"Comportamento para x muito grande\"]}', '[0,1,2,3]', 'Cada conceito descreve uma forma específica de analisar funções.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Arraste cada símbolo da Montanha do Cálculo ao seu significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'arrastar', 'logica', 'Arraste cada situação ao veredito correto.', NULL, '{\"itens\":[\"laterais iguais\",\"laterais diferentes\",\"limite igual a f(a)\",\"função sem f(a)\"],\"alvos\":[\"limite bilateral pode existir\",\"limite bilateral não existe\",\"continuidade no ponto\",\"pode haver limite sem valor definido\"]}', '[0,1,2,3]', 'A fase exige distinguir limite, laterais e continuidade.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Arraste cada situação ao veredito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'arrastar', 'logica', 'Arraste cada guardião à pergunta que ele faz.', NULL, '{\"itens\":[\"Eco da Esquerda\",\"Eco da Direita\",\"Guardião do Infinito\",\"Sentinela da Continuidade\"],\"alvos\":[\"x se aproxima por valores menores\",\"x se aproxima por valores maiores\",\"x cresce sem limite\",\"limite e valor coincidem\"]}', '[0,1,2,3]', 'A narrativa reforça os lados e o comportamento da função.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Arraste cada guardião à pergunta que ele faz.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'arrastar', 'logica', 'Arraste cada expressão ao limite correto.', NULL, '{\"itens\":[\"1/x com x -> +infinito\",\"x + 2 com x -> 2\",\"3x com x -> 1\",\"x^2 com x -> 3\"],\"alvos\":[\"0\",\"4\",\"3\",\"9\"]}', '[0,1,2,3]', 'São avaliações diretas de tendências simples.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Arraste cada expressão ao limite correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'arrastar', 'logica', 'Arraste cada obstáculo ao cuidado necessário.', NULL, '{\"itens\":[\"furo no ponto\",\"salto no gráfico\",\"x indo ao infinito\",\"denominador zerando\"],\"alvos\":[\"verificar se o limite ainda existe\",\"comparar limites laterais\",\"observar termos dominantes\",\"tentar simplificar antes de concluir\"]}', '[0,1,2,3]', 'Cada obstáculo pede uma leitura matemática adequada.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Arraste cada obstáculo ao cuidado necessário.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'multipla', 'calculo', 'O limite da sequência aₙ = 1/n quando n tende ao infinito é:', NULL, '[\"1\",\"0\",\"infinito\",\"n\"]', '1', 'Quanto maior o n, menor 1/n; o termo se aproxima de 0.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='O limite da sequência aₙ = 1/n quando n tende ao infinito é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'multipla', 'calculo', 'A sequência aₙ = (n + 1)/n, quando n tende ao infinito, tende a:', NULL, '[\"0\",\"1\",\"infinito\",\"2\"]', '1', '(n+1)/n = 1 + 1/n; como 1/n → 0, a sequência tende a 1.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='A sequência aₙ = (n + 1)/n, quando n tende ao infinito, tende a:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'vf', 'calculo', 'Numa progressão aritmética, a diferença entre termos consecutivos é constante.', NULL, NULL, 'true', 'Essa diferença constante é a razão da PA.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Numa progressão aritmética, a diferença entre termos consecutivos é constante.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'multipla', 'calculo', 'Qual sequência CONVERGE (tem limite finito)?', NULL, '[\"1, 2, 3, 4, ...\",\"2, 4, 8, 16, ...\",\"1, 1\\/2, 1\\/3, 1\\/4, ...\",\"1, 2, 4, 8, ...\"]', '2', '1/n tende a 0 (converge); as demais crescem sem limite.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Qual sequência CONVERGE (tem limite finito)?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'completar', 'calculo', 'Numa PA de primeiro termo 3 e razão 5, o termo geral é aₙ = 3 + (n - 1)·___', NULL, NULL, '[\"5\"]', 'O termo geral da PA é a₁ + (n−1)·r, com r = 5.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Numa PA de primeiro termo 3 e razão 5, o termo geral é aₙ = 3 + (n - 1)·___') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'multipla', 'calculo', 'A soma infinita 1 + 1/2 + 1/4 + 1/8 + ... converge para:', NULL, '[\"1\",\"2\",\"infinito\",\"1\\/2\"]', '1', 'Série geométrica de razão 1/2: a soma é 1/(1 − 1/2) = 2.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='A soma infinita 1 + 1/2 + 1/4 + 1/8 + ... converge para:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'multipla', 'logica', 'Conforme n cresce muito, o valor de 1/n se aproxima de:', NULL, '[\"infinito\",\"0\",\"1\",\"n\"]', '1', 'Quanto maior n, menor 1/n; o limite é 0.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Conforme n cresce muito, o valor de 1/n se aproxima de:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'multipla', 'logica', 'A soma 1 + 1/2 + 1/4 + 1/8 + ... se aproxima de qual valor?', NULL, '[\"1\",\"2\",\"infinito\",\"0\"]', '1', 'É uma série geométrica que converge para 2.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='A soma 1 + 1/2 + 1/4 + 1/8 + ... se aproxima de qual valor?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'vf', 'logica', 'Uma função pode ser definida em termos de si mesma (recursão).', NULL, NULL, 'true', 'Definições recursivas são comuns em matemática e programação.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Uma função pode ser definida em termos de si mesma (recursão).') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'multipla', 'logica', 'O limite descreve o valor de que uma função se aproxima. Isso lembra qual ideia da computação?', NULL, '[\"A convergência de uma iteração/sequência\",\"Declarar variáveis\",\"Concatenar strings\",\"Abrir arquivos\"]', '0', 'Iterações que se aproximam de um resultado estável são como limites convergindo.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='O limite descreve o valor de que uma função se aproxima. Isso lembra qual ideia da computação?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'multipla', 'logica', 'Na Espiral Infinita, Cesar pergunta: a derivada em um ponto representa principalmente:', NULL, '[\"taxa de variação instantânea e inclinação da tangente\",\"área total acumulada\",\"quantidade de elementos de um conjunto\",\"uma busca binária\"]', '0', 'A derivada mede variação instantânea e inclinação da reta tangente.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Na Espiral Infinita, Cesar pergunta: a derivada em um ponto representa principalmente:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'multipla', 'logica', 'Pela regra da potência, a derivada de x^4 é:', NULL, '[\"4x^3\",\"x^5/5\",\"4x\",\"x^4 + C\"]', '0', 'Pela regra da potência, d/dx de x^n é n*x^(n-1).', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Pela regra da potência, a derivada de x^4 é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'multipla', 'logica', 'Quando duas funções aparecem multiplicadas, qual regra costuma ser usada?', NULL, '[\"regra do produto\",\"regra do baú\",\"regra do TCP\",\"regra do intervalo vazio\"]', '0', 'A regra do produto deriva multiplicações de funções.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Quando duas funções aparecem multiplicadas, qual regra costuma ser usada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'multipla', 'logica', 'A cadeia encantada surge quando há uma função dentro de outra. Qual regra resolve esse caso?', NULL, '[\"regra da cadeia\",\"regra do JOIN\",\"regra da pilha\",\"regra do valor absoluto apenas\"]', '0', 'A regra da cadeia trata funções compostas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='A cadeia encantada surge quando há uma função dentro de outra. Qual regra resolve esse caso?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'multipla', 'logica', 'Se s(t) mede posição ao longo do tempo, a derivada de s em relação a t indica:', NULL, '[\"velocidade instantânea\",\"área entre curvas\",\"o denominador da fração\",\"o número de linhas da matriz\"]', '0', 'A derivada de posição em relação ao tempo é interpretada como velocidade.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Se s(t) mede posição ao longo do tempo, a derivada de s em relação a t indica:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'vf', 'logica', 'A derivada pode ser interpretada como a inclinação da reta tangente ao gráfico em um ponto.', NULL, NULL, 'true', 'Essa é uma das interpretações geométricas fundamentais da derivada.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='A derivada pode ser interpretada como a inclinação da reta tangente ao gráfico em um ponto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'vf', 'logica', 'A derivada de uma constante é a própria constante.', NULL, NULL, 'false', 'A derivada de uma constante é zero.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='A derivada de uma constante é a própria constante.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'vf', 'logica', 'Na regra da cadeia, primeiro considera-se a função externa e depois a derivada da interna.', NULL, NULL, 'true', 'Para funções compostas, derivamos a externa e multiplicamos pela derivada da interna.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Na regra da cadeia, primeiro considera-se a função externa e depois a derivada da interna.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'vf', 'logica', 'A regra do produto diz que a derivada de f*g é sempre f vezes g.', NULL, NULL, 'false', 'A regra correta é f\'g + fg\', não apenas fg.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='A regra do produto diz que a derivada de f*g é sempre f vezes g.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'vf', 'logica', 'Taxas de variação relacionam mudanças entre grandezas dependentes.', NULL, NULL, 'true', 'A derivada formaliza a variação de uma grandeza em relação a outra.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Taxas de variação relacionam mudanças entre grandezas dependentes.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'completar', 'logica', 'Complete: a derivada nasce como limite da taxa de variação quando o intervalo tende a ___.', NULL, NULL, '[\"zero\",\"0\"]', 'A derivada é obtida quando o incremento tende a zero.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Complete: a derivada nasce como limite da taxa de variação quando o intervalo tende a ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'completar', 'logica', 'Complete: a derivada de uma constante é ___.', NULL, NULL, '[\"0\",\"zero\"]', 'Constantes não variam, por isso têm derivada zero.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Complete: a derivada de uma constante é ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'completar', 'logica', 'Complete: pela regra da potência, d/dx de x^5 é ___.', NULL, NULL, '[\"5x^4\",\"5*x^4\"]', 'Multiplica-se pelo expoente e reduz-se o expoente em uma unidade.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Complete: pela regra da potência, d/dx de x^5 é ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'completar', 'logica', 'Complete: a regra usada para derivar funções compostas é a regra da ___.', NULL, NULL, '[\"cadeia\",\"Cadeia\"]', 'A regra da cadeia trata uma função dentro de outra.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Complete: a regra usada para derivar funções compostas é a regra da ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'completar', 'logica', 'Complete: geometricamente, a derivada indica a inclinação da reta ___.', NULL, NULL, '[\"tangente\",\"Tangente\"]', 'A reta tangente toca a curva no ponto analisado.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Complete: geometricamente, a derivada indica a inclinação da reta ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'ordenar', 'logica', 'Ordene o ritual da derivada como limite.', NULL, '[\"Calcular a variação média\",\"Diminuir o intervalo de análise\",\"Fazer o intervalo tender a zero\",\"Obter a taxa instantânea\"]', '[0,1,2,3]', 'A derivada surge do limite da taxa média quando o intervalo se aproxima de zero.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Ordene o ritual da derivada como limite.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'ordenar', 'logica', 'Ordene a regra da potência para x^n.', NULL, '[\"Identificar o expoente n\",\"Multiplicar a função por n\",\"Reduzir o expoente em uma unidade\",\"Escrever n*x^(n-1)\"]', '[0,1,2,3]', 'A regra da potência segue a forma n*x^(n-1).', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Ordene a regra da potência para x^n.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'ordenar', 'logica', 'Ordene a aplicação da regra da cadeia em f(g(x)).', NULL, '[\"Identificar a função externa\",\"Identificar a função interna\",\"Derivar a externa mantendo a interna\",\"Multiplicar pela derivada da interna\"]', '[0,1,2,3]', 'A função composta exige derivada externa vezes derivada interna.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Ordene a aplicação da regra da cadeia em f(g(x)).') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'ordenar', 'logica', 'Ordene a leitura geométrica da derivada.', NULL, '[\"Escolher o ponto da curva\",\"Traçar a reta tangente imaginária\",\"Medir a inclinação dessa reta\",\"Interpretar como taxa instantânea\"]', '[0,1,2,3]', 'A inclinação da tangente expressa a taxa de variação no ponto.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Ordene a leitura geométrica da derivada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'ordenar', 'logica', 'Ordene a análise de uma taxa de variação em uma missão.', NULL, '[\"Definir a grandeza dependente\",\"Definir a variável independente\",\"Montar a função que relaciona ambas\",\"Derivar para obter a taxa instantânea\"]', '[0,1,2,3]', 'Taxas de variação pedem relação entre grandezas e derivação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Ordene a análise de uma taxa de variação em uma missão.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'erro', 'logica', 'A Espiral mostra a runa d/dx(x^3) = 3x. Qual é o erro?', NULL, '[\"O correto é 3x^2\",\"O correto é x^4/4\",\"A derivada não existe\",\"O correto é 0\"]', '0', 'Pela regra da potência, reduzimos o expoente de 3 para 2.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='A Espiral mostra a runa d/dx(x^3) = 3x. Qual é o erro?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'erro', 'logica', 'O aprendiz escreveu d/dx(7) = 7. Qual falha Cesar aponta?', NULL, '[\"Constante deriva para zero\",\"Constante não pode existir\",\"Toda derivada precisa de integral\",\"7 vira infinito\"]', '0', 'Como 7 não varia com x, sua derivada é 0.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='O aprendiz escreveu d/dx(7) = 7. Qual falha Cesar aponta?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'erro', 'logica', 'A runa diz: derivada de f(g(x)) é apenas f\'(g(x)). O que falta?', NULL, '[\"Multiplicar pela derivada da função interna g\'(x)\",\"Somar a integral definida\",\"Trocar por limite lateral\",\"Adicionar constante C sempre\"]', '0', 'A regra da cadeia exige o fator da derivada interna.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='A runa diz: derivada de f(g(x)) é apenas f\'(g(x)). O que falta?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'erro', 'logica', 'Na ponte das tangentes, alguém afirma que derivada mede sempre área acumulada. Qual é o problema?', NULL, '[\"Isso descreve melhor a integral; derivada mede taxa de variação\",\"Derivada e área são sempre a mesma coisa\",\"Área acumulada não existe\",\"A afirmação só vale para redes\"]', '0', 'Derivada está ligada à variação instantânea; integral está ligada à acumulação.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Na ponte das tangentes, alguém afirma que derivada mede sempre área acumulada. Qual é o problema?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'erro', 'logica', 'O grimório usa regra do produto assim: d(fg)=f\'g\'. Qual parte foi esquecida?', NULL, '[\"Os termos f\'g + fg\'\",\"A constante de integração\",\"O denominador do limite infinito\",\"O operador de união\"]', '0', 'A regra do produto combina duas parcelas: derivada da primeira vezes segunda mais primeira vezes derivada da segunda.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='O grimório usa regra do produto assim: d(fg)=f\'g\'. Qual parte foi esquecida?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'arrastar', 'logica', 'Arraste cada artefato da Espiral ao conceito correto.', NULL, '{\"itens\":[\"Derivada\",\"Reta tangente\",\"Regra da potência\",\"Regra da cadeia\"],\"alvos\":[\"Taxa instantânea\",\"Inclinação no ponto\",\"Deriva x^n\",\"Deriva função composta\"]}', '[0,1,2,3]', 'Os principais instrumentos da fase são taxa, tangente e regras de derivação.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Arraste cada artefato da Espiral ao conceito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'arrastar', 'logica', 'Arraste cada função à sua derivada.', NULL, '{\"itens\":[\"x^2\",\"x^3\",\"5\",\"2x\"],\"alvos\":[\"2x\",\"3x^2\",\"0\",\"2\"]}', '[0,1,2,3]', 'São derivadas básicas pela regra da potência e pela regra da constante.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Arraste cada função à sua derivada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'arrastar', 'logica', 'Arraste cada missão à regra mais adequada.', NULL, '{\"itens\":[\"x^5\",\"f(x)g(x)\",\"f(g(x))\",\"constante 9\"],\"alvos\":[\"potência\",\"produto\",\"cadeia\",\"constante\"]}', '[0,1,2,3]', 'Cada forma algébrica aciona uma regra de derivação.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Arraste cada missão à regra mais adequada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'arrastar', 'logica', 'Arraste cada interpretação ao nome correto.', NULL, '{\"itens\":[\"velocidade instantânea\",\"inclinação da tangente\",\"variação média em intervalo\",\"intervalo tendendo a zero\"],\"alvos\":[\"derivada aplicada\",\"leitura geométrica\",\"taxa média\",\"passo do limite\"]}', '[0,1,2,3]', 'A derivada conecta taxas médias, limite e interpretação geométrica.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Arraste cada interpretação ao nome correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'arrastar', 'logica', 'Arraste cada erro ao conserto.', NULL, '{\"itens\":[\"d(x^4)=x^3\",\"d(8)=8\",\"esquecer g\'(x)\",\"usar área para derivada\"],\"alvos\":[\"4x^3\",\"0\",\"aplicar cadeia completa\",\"usar taxa de variação\"]}', '[0,1,2,3]', 'Os consertos preservam as regras básicas de derivação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Arraste cada erro ao conserto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'multipla', 'calculo', 'Quanto vale o limite, quando x tende a 2, de (x² − 4)/(x − 2)?', NULL, '[\"0\",\"2\",\"4\",\"indefinido\"]', '2', 'x² − 4 = (x − 2)(x + 2); cancelando (x − 2) sobra x + 2, que em x=2 vale 4.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Quanto vale o limite, quando x tende a 2, de (x² − 4)/(x − 2)?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'multipla', 'calculo', 'Uma função é CONTÍNUA num ponto quando:', NULL, '[\"O gráfico tem um salto ali\",\"O limite no ponto existe e é igual ao valor da função\",\"A função não está definida ali\",\"A derivada é zero\"]', '1', 'Continuidade: o limite existe, a função existe e os dois coincidem no ponto.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Uma função é CONTÍNUA num ponto quando:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'vf', 'calculo', 'O limite de (sen x)/x quando x tende a 0 é igual a 1.', NULL, NULL, 'true', 'É o limite fundamental trigonométrico, base de várias derivadas.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='O limite de (sen x)/x quando x tende a 0 é igual a 1.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'multipla', 'calculo', 'O limite de 1/x² quando x tende ao infinito é:', NULL, '[\"infinito\",\"1\",\"0\",\"−1\"]', '2', 'O denominador cresce sem limite, então a fração tende a 0.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='O limite de 1/x² quando x tende ao infinito é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'multipla', 'calculo', 'Encontrar 0/0 ao calcular um limite significa que:', NULL, '[\"O limite é sempre 0\",\"É uma indeterminação: é preciso manipular (fatorar\\/simplificar) a expressão\",\"O limite não existe nunca\",\"A função é contínua\"]', '1', '0/0 é indeterminação; fatorar, simplificar ou usar L\'Hôpital costuma resolver.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Encontrar 0/0 ao calcular um limite significa que:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'completar', 'calculo', 'Calcule o limite quando x tende a 3 de (x + 1) = ___', NULL, NULL, '[\"4\"]', 'A função é contínua: basta substituir x por 3 → 3 + 1 = 4.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Calcule o limite quando x tende a 3 de (x + 1) = ___') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'multipla', 'logica', 'Qual função cresce mais rápido conforme n aumenta?', NULL, '[\"n\",\"n²\",\"log n\",\"constante\"]', '1', 'Entre as opções, n² tem o maior crescimento.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Qual função cresce mais rápido conforme n aumenta?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'multipla', 'logica', 'Um algoritmo que DOBRA o trabalho a cada elemento adicional tende a ser:', NULL, '[\"O(log n)\",\"O(n)\",\"O(2^n) exponencial\",\"O(1)\"]', '2', 'Dobrar a cada passo gera crescimento exponencial, O(2^n).', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Um algoritmo que DOBRA o trabalho a cada elemento adicional tende a ser:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'vf', 'logica', 'Para entradas grandes, a ordem de crescimento importa mais que a constante multiplicativa.', NULL, NULL, 'true', 'Big-O ignora constantes porque, no limite, a ordem domina.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Para entradas grandes, a ordem de crescimento importa mais que a constante multiplicativa.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'ordenar', 'logica', 'Ordene por crescimento, do mais LENTO ao mais RÁPIDO:', NULL, '[\"O(2^n)\",\"O(n)\",\"O(log n)\",\"O(n²)\"]', '[2,1,3,0]', 'log n < n < n² < 2^n.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Ordene por crescimento, do mais LENTO ao mais RÁPIDO:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'multipla', 'logica', 'Diante do Colosso Menor, Cesar pergunta: uma primitiva de f é uma função cuja derivada é:', NULL, '[\"f\",\"sempre zero\",\"sempre infinito\",\"o domínio vazio\"]', '0', 'Uma primitiva F satisfaz F\' = f.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Diante do Colosso Menor, Cesar pergunta: uma primitiva de f é uma função cuja derivada é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'multipla', 'logica', 'A integral indefinida representa:', NULL, '[\"uma família de primitivas com constante C\",\"um único número final sempre positivo\",\"apenas um limite lateral\",\"um comando SQL\"]', '0', 'A integral indefinida gera primitivas e inclui a constante de integração.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='A integral indefinida representa:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'multipla', 'logica', 'A integral definida de f em um intervalo costuma produzir:', NULL, '[\"um número associado à acumulação no intervalo\",\"uma classe Java\",\"um limite lateral obrigatório\",\"uma interface\"]', '0', 'A integral definida acumula valores em um intervalo e retorna um número.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='A integral definida de f em um intervalo costuma produzir:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'multipla', 'logica', 'Pela regra da potência para integrais, a integral de x^2 dx é:', NULL, '[\"x^3/3 + C\",\"2x + C\",\"x^2 + C\",\"3x^2 + C\"]', '0', 'Aumenta-se o expoente em uma unidade e divide-se pelo novo expoente.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Pela regra da potência para integrais, a integral de x^2 dx é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'multipla', 'logica', 'O Teorema Fundamental do Cálculo conecta diretamente:', NULL, '[\"derivadas e integrais\",\"redes e roteadores\",\"classes e objetos\",\"pilhas e filas\"]', '0', 'O teorema mostra a relação inversa entre derivar e integrar em condições adequadas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='O Teorema Fundamental do Cálculo conecta diretamente:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'vf', 'logica', 'A integral indefinida deve incluir uma constante de integração.', NULL, NULL, 'true', 'A constante C representa a família de primitivas.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='A integral indefinida deve incluir uma constante de integração.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'vf', 'logica', 'A integral definida sempre retorna uma função com +C.', NULL, NULL, 'false', 'A integral definida retorna um número; +C aparece na integral indefinida.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='A integral definida sempre retorna uma função com +C.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'vf', 'logica', 'A integral pode ser interpretada como área acumulada sob uma curva em certos contextos.', NULL, NULL, 'true', 'Uma aplicação importante da integral é o cálculo de áreas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='A integral pode ser interpretada como área acumulada sob uma curva em certos contextos.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'vf', 'logica', 'Se F é uma primitiva de f, então F\' = f.', NULL, NULL, 'true', 'Essa é a definição de primitiva.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Se F é uma primitiva de f, então F\' = f.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'vf', 'logica', 'A integral de x dx é x + C.', NULL, NULL, 'false', 'Pela regra da potência, a integral de x dx é x^2/2 + C.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='A integral de x dx é x + C.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'completar', 'logica', 'Complete: uma função F cuja derivada é f recebe o nome de ___.', NULL, NULL, '[\"primitiva\",\"Primitiva\",\"antiderivada\",\"Antiderivada\"]', 'Primitiva ou antiderivada é a função anterior ao processo de derivação.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Complete: uma função F cuja derivada é f recebe o nome de ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'completar', 'logica', 'Complete: na integral indefinida, acrescentamos a constante ___.', NULL, NULL, '[\"C\",\"c\"]', 'A constante C representa todas as primitivas possíveis.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Complete: na integral indefinida, acrescentamos a constante ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'completar', 'logica', 'Complete: a integral de 2x dx é ___ + C.', NULL, NULL, '[\"x^2\",\"x²\"]', 'A derivada de x^2 é 2x.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Complete: a integral de 2x dx é ___ + C.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'completar', 'logica', 'Complete: a integral definida entre a e b pode ser calculada por F(b) - F(___).', NULL, NULL, '[\"a\"]', 'Pelo Teorema Fundamental do Cálculo, avalia-se a primitiva nos extremos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Complete: a integral definida entre a e b pode ser calculada por F(b) - F(___).') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'completar', 'logica', 'Complete: a área sob a curva em um intervalo pode ser modelada por uma integral ___.', NULL, NULL, '[\"definida\",\"Definida\"]', 'A integral definida acumula a contribuição da função em um intervalo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Complete: a área sob a curva em um intervalo pode ser modelada por uma integral ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'ordenar', 'logica', 'Ordene o ritual para calcular uma integral indefinida simples.', NULL, '[\"Identificar a função a integrar\",\"Aplicar a regra de integração adequada\",\"Somar a constante C\",\"Conferir derivando o resultado\"]', '[0,1,2,3]', 'A conferência por derivada confirma a primitiva encontrada.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Ordene o ritual para calcular uma integral indefinida simples.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'ordenar', 'logica', 'Ordene o uso do Teorema Fundamental do Cálculo.', NULL, '[\"Encontrar uma primitiva F\",\"Avaliar F no limite superior b\",\"Avaliar F no limite inferior a\",\"Calcular F(b) - F(a)\"]', '[0,1,2,3]', 'A integral definida é obtida pela diferença da primitiva nos extremos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Ordene o uso do Teorema Fundamental do Cálculo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'ordenar', 'logica', 'Ordene a regra da potência para integrar x^n.', NULL, '[\"Aumentar o expoente em 1\",\"Dividir pelo novo expoente\",\"Adicionar C se for indefinida\",\"Escrever o resultado final\"]', '[0,1,2,3]', 'Para n diferente de -1, integra-se x^n como x^(n+1)/(n+1) + C.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Ordene a regra da potência para integrar x^n.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'ordenar', 'logica', 'Ordene a leitura de área sob uma curva positiva.', NULL, '[\"Escolher o intervalo\",\"Identificar a função superior ao eixo\",\"Montar a integral definida\",\"Interpretar o valor como área acumulada\"]', '[0,1,2,3]', 'A área sob curva positiva pode ser calculada por integral definida.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Ordene a leitura de área sob uma curva positiva.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'ordenar', 'logica', 'Ordene a diferença entre integral definida e indefinida.', NULL, '[\"Perguntar se há intervalo fechado\",\"Se houver limites, calcular número\",\"Se não houver limites, buscar família de primitivas\",\"Usar +C apenas na indefinida\"]', '[0,1,2,3]', 'Limites de integração definem um valor numérico; sem limites, temos família de primitivas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Ordene a diferença entre integral definida e indefinida.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'erro', 'logica', 'O aprendiz escreveu integral indefinida de 2x dx = x^2. Qual detalhe foi esquecido?', NULL, '[\"A constante +C\",\"O limite pela esquerda\",\"A derivada parcial\",\"O roteador padrão\"]', '0', 'Na integral indefinida, deve-se adicionar a constante de integração.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='O aprendiz escreveu integral indefinida de 2x dx = x^2. Qual detalhe foi esquecido?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'erro', 'logica', 'A runa diz: integral definida de a até b sempre termina com +C. Qual é a falha?', NULL, '[\"+C pertence à integral indefinida, não ao valor definido\",\"Toda integral definida é uma classe\",\"A integral definida não existe\",\"Faltou usar switch\"]', '0', 'A integral definida calcula um número ao avaliar a primitiva nos extremos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='A runa diz: integral definida de a até b sempre termina com +C. Qual é a falha?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'erro', 'logica', 'O Colosso viu: integral de x^3 dx = 3x^2 + C. Qual regra foi confundida?', NULL, '[\"Foi usada derivação em vez de integração\",\"Foi usado limite lateral\",\"Foi usado JOIN\",\"Nada está errado\"]', '0', '3x^2 é derivada de x^3; a integral correta é x^4/4 + C.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='O Colosso viu: integral de x^3 dx = 3x^2 + C. Qual regra foi confundida?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'erro', 'logica', 'No Teorema Fundamental, o aprendiz calculou F(a) - F(b). Qual ajuste abre o portão?', NULL, '[\"Usar F(b) - F(a)\",\"Somar C duas vezes\",\"Trocar integral por derivada parcial\",\"Cancelar o intervalo\"]', '0', 'Para integral de a até b, avalia-se a primitiva no superior menos no inferior.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='No Teorema Fundamental, o aprendiz calculou F(a) - F(b). Qual ajuste abre o portão?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'erro', 'logica', 'Alguém afirmou que integral nunca tem relação com área. Qual é o erro?', NULL, '[\"Integrais definidas podem modelar áreas acumuladas\",\"Área só existe em redes\",\"Integral é sempre texto\",\"Área é uma exceção de Java\"]', '0', 'Uma das aplicações centrais da integral definida é o cálculo de áreas.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Alguém afirmou que integral nunca tem relação com área. Qual é o erro?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'arrastar', 'logica', 'Arraste cada relíquia do Colosso Menor ao conceito correto.', NULL, '{\"itens\":[\"Primitiva\",\"Integral indefinida\",\"Integral definida\",\"Teorema Fundamental\"],\"alvos\":[\"Função cuja derivada retorna f\",\"Família com +C\",\"Acumulação em intervalo\",\"Conecta derivada e integral\"]}', '[0,1,2,3]', 'A fase trata de primitivas, acumulação e relação com derivadas.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Arraste cada relíquia do Colosso Menor ao conceito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'arrastar', 'logica', 'Arraste cada expressão à integral indefinida correta.', NULL, '{\"itens\":[\"2x\",\"x\",\"x^2\",\"0\"],\"alvos\":[\"x^2 + C\",\"x^2/2 + C\",\"x^3/3 + C\",\"C\"]}', '[0,1,2,3]', 'As correspondências seguem a regra da potência e a constante de integração.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Arraste cada expressão à integral indefinida correta.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'arrastar', 'logica', 'Arraste cada pergunta ao tipo de integral.', NULL, '{\"itens\":[\"Qual família de funções deriva para f?\",\"Quanto acumula de a até b?\",\"Qual área sob a curva no intervalo?\",\"Qual primitiva geral?\"],\"alvos\":[\"indefinida\",\"definida\",\"definida\",\"indefinida\"]}', '[0,1,2,3]', 'Integrais definidas têm intervalo; indefinidas geram primitivas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Arraste cada pergunta ao tipo de integral.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'arrastar', 'logica', 'Arraste cada erro ao conserto.', NULL, '{\"itens\":[\"esquecer +C\",\"usar F(a)-F(b)\",\"integrar x^2 como 2x\",\"confundir área com derivada\"],\"alvos\":[\"adicionar constante\",\"usar F(b)-F(a)\",\"usar x^3/3\",\"usar integral definida\"]}', '[0,1,2,3]', 'Cada conserto recupera a lógica correta da integração.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Arraste cada erro ao conserto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'arrastar', 'logica', 'Arraste cada símbolo de batalha ao papel matemático.', NULL, '{\"itens\":[\"a\",\"b\",\"F\",\"C\"],\"alvos\":[\"limite inferior\",\"limite superior\",\"primitiva\",\"constante de integração\"]}', '[0,1,2,3]', 'Na notação de integrais, extremos, primitiva e constante têm papéis próprios.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Arraste cada símbolo de batalha ao papel matemático.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'multipla', 'calculo', 'A derivada de uma função mede:', NULL, '[\"A área sob a curva\",\"A taxa de variação instantânea da função\",\"O valor máximo da função\",\"O número de raízes\"]', '1', 'A derivada é a inclinação da reta tangente: a rapidez com que a função muda naquele ponto.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='A derivada de uma função mede:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'multipla', 'calculo', 'A derivada de f(x) = x² é:', NULL, '[\"x\",\"2x\",\"x²\",\"2\"]', '1', 'Pela regra do tombo, derivada de xⁿ é n·xⁿ⁻¹: aqui 2·x¹ = 2x.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='A derivada de f(x) = x² é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'multipla', 'calculo', 'A derivada de uma função constante f(x) = 7 é:', NULL, '[\"7\",\"1\",\"0\",\"x\"]', '2', 'Uma constante não varia, então sua taxa de variação (derivada) é 0.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='A derivada de uma função constante f(x) = 7 é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'vf', 'calculo', 'Se f\'(x) > 0 em todo um intervalo, então f é crescente nesse intervalo.', NULL, NULL, 'true', 'Derivada positiva = inclinação para cima = função crescente.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Se f\'(x) > 0 em todo um intervalo, então f é crescente nesse intervalo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'multipla', 'calculo', 'A derivada de f(x) = 3x é:', NULL, '[\"3x\",\"3\",\"x\",\"0\"]', '1', 'A derivada de uma reta a·x é a inclinação a; aqui, 3.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='A derivada de f(x) = 3x é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'completar', 'calculo', 'Pela regra do tombo, a derivada de x³ é ___·x²', NULL, NULL, '[\"3\"]', 'Derivada de xⁿ = n·xⁿ⁻¹; para n=3 dá 3x².', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Pela regra do tombo, a derivada de x³ é ___·x²') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m),
       'multipla', 'logica', 'O Colosso vale 2^n no turno n. Quanto ele vale no turno 4?', NULL, '[\"8\",\"16\",\"4\",\"32\"]', '1', '2 elevado a 4 é igual a 16.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='O Colosso vale 2^n no turno n. Quanto ele vale no turno 4?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m),
       'multipla', 'logica', 'Para achar o limite do Colosso, qual sequência CONVERGE (tem limite finito)?', NULL, '[\"1, 2, 3, 4, ...\",\"1, 1/2, 1/3, 1/4, ...\",\"2, 4, 8, 16, ...\",\"1, 2, 4, 8, ...\"]', '1', '1/n tende a 0: converge. As demais crescem sem limite.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='Para achar o limite do Colosso, qual sequência CONVERGE (tem limite finito)?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m),
       'vf', 'logica', 'Um algoritmo O(1) leva o mesmo tempo independente do tamanho da entrada.', NULL, NULL, 'true', 'Tempo constante não depende de n.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='Um algoritmo O(1) leva o mesmo tempo independente do tamanho da entrada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m),
       'multipla', 'logica', 'Qual a saída?', '$s = 0;\nfor ($i = 1; $i <= 4; $i++) {\n  $s += $i;\n}\necho $s;', '[\"4\",\"10\",\"16\",\"24\"]', '1', '1 + 2 + 3 + 4 = 10.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='Qual a saída?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m),
       'erro', 'logica', 'Por que este cálculo de média está incorreto?', '$soma = 10 + 20 + 30;\n$media = $soma / 2;', '[\"A soma está errada\",\"Divide por 2 em vez de 3 (a quantidade de valores)\",\"Falta ponto e vírgula\",\"Não há erro\"]', '1', 'São 3 valores; a média deve dividir por 3, não por 2.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='Por que este cálculo de média está incorreto?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m),
       'multipla', 'calculo', 'A integração é a operação inversa da:', NULL, '[\"Soma\",\"Derivação\",\"Raiz quadrada\",\"Potenciação\"]', '1', 'Integral e derivada são operações inversas (Teorema Fundamental do Cálculo).', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='A integração é a operação inversa da:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m),
       'multipla', 'calculo', 'Quanto vale a integral indefinida ∫ 2x dx?', NULL, '[\"2 + C\",\"x² + C\",\"2x² + C\",\"x + C\"]', '1', 'A primitiva de 2x é x² (pois a derivada de x² é 2x), mais a constante C.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='Quanto vale a integral indefinida ∫ 2x dx?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m),
       'multipla', 'calculo', 'Na derivada PARCIAL em x de f(x, y), a variável y é tratada como:', NULL, '[\"Variável\",\"Zero\",\"Constante\",\"Infinito\"]', '2', 'Deriva-se em relação a x mantendo y fixo (constante) — base do cálculo multivariável.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='Na derivada PARCIAL em x de f(x, y), a variável y é tratada como:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m),
       'vf', 'calculo', 'A integral definida pode ser interpretada como a área sob a curva da função.', NULL, NULL, 'true', 'A integral definida soma \"fatias infinitesimais\", resultando na área entre a curva e o eixo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='A integral definida pode ser interpretada como a área sob a curva da função.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m),
       'multipla', 'calculo', 'A derivada parcial ∂/∂x de f(x, y) = x²·y é:', NULL, '[\"x²\",\"2xy\",\"2x\",\"y\"]', '1', 'Tratando y como constante: ∂/∂x (x²·y) = 2x·y = 2xy.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='A derivada parcial ∂/∂x de f(x, y) = x²·y é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m),
       'completar', 'calculo', 'A constante C somada numa integral indefinida é a constante de ___.', NULL, NULL, '[\"integração\",\"integracao\"]', 'Como a derivada de qualquer constante é 0, toda primitiva carrega o \"+ C\".', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='A constante C somada numa integral indefinida é a constante de ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'multipla', 'redes', 'Quantas camadas tem o modelo OSI?', NULL, '[\"4\",\"5\",\"7\",\"9\"]', '2', 'O modelo OSI possui 7 camadas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Quantas camadas tem o modelo OSI?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'multipla', 'redes', 'Qual camada lida com o cabo, sinal e meio físico?', NULL, '[\"Aplicação\",\"Física\",\"Transporte\",\"Rede\"]', '1', 'A camada Física trata da transmissão de bits no meio.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Qual camada lida com o cabo, sinal e meio físico?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'multipla', 'redes', 'Em qual camada atuam protocolos como HTTP, DNS e FTP?', NULL, '[\"Física\",\"Enlace\",\"Transporte\",\"Aplicação\"]', '3', 'Esses protocolos vivem na camada de Aplicação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Em qual camada atuam protocolos como HTTP, DNS e FTP?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'ordenar', 'redes', 'Ordene da camada 1 para a 3 do modelo OSI:', NULL, '[\"Rede\",\"Física\",\"Enlace\"]', '[1,2,0]', 'Camada 1 Física, 2 Enlace, 3 Rede.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Ordene da camada 1 para a 3 do modelo OSI:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'multipla', 'redes', 'Cassandro abre o primeiro andar da Torre das Conexões. Quantas camadas possui o modelo OSI?', NULL, '[\"4 camadas\",\"5 camadas\",\"7 camadas\",\"9 camadas\"]', '2', 'O modelo OSI organiza a comunicação em sete camadas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Cassandro abre o primeiro andar da Torre das Conexões. Quantas camadas possui o modelo OSI?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'multipla', 'redes', 'A Sentinela da Camada aponta para cabos, conectores e sinais de radiofrequência. Qual camada ela guarda?', NULL, '[\"Aplicação\",\"Física\",\"Transporte\",\"Sessão\"]', '1', 'A camada Física trata dos componentes eletrônicos, cabos, conectores, tensões, bits e sinais.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='A Sentinela da Camada aponta para cabos, conectores e sinais de radiofrequência. Qual camada ela guarda?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'multipla', 'redes', 'Um pergaminho do reino precisa escolher rotas para pacotes entre redes. Qual camada do OSI assume essa missão?', NULL, '[\"Enlace de dados\",\"Rede\",\"Apresentação\",\"Aplicação\"]', '1', 'A camada de Rede é responsável pelo roteamento ponto a ponto, com uso de protocolos como o IP.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Um pergaminho do reino precisa escolher rotas para pacotes entre redes. Qual camada do OSI assume essa missão?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'multipla', 'redes', 'No espelho TCP/IP de Cassandro, as camadas Física e Enlace do OSI aparecem fundidas em qual camada?', NULL, '[\"Aplicação\",\"Transporte\",\"Host/Rede\",\"Inter-redes\"]', '2', 'No modelo TCP/IP, Física e Enlace são agregadas na camada Host/Rede.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='No espelho TCP/IP de Cassandro, as camadas Física e Enlace do OSI aparecem fundidas em qual camada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'multipla', 'redes', 'A caravana cruza do castelo local, passa pela cidade e alcança continentes. Qual sequência representa melhor LAN, MAN e WAN?', NULL, '[\"Prédio, cidade, grande área geográfica\",\"Continente, sala, cidade\",\"Servidor, protocolo, cabo\",\"Aplicação, transporte, rede\"]', '0', 'LAN cobre área local, MAN cobre área metropolitana e WAN interliga áreas geográficas amplas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='A caravana cruza do castelo local, passa pela cidade e alcança continentes. Qual sequência representa melhor LAN, MAN e WAN?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'vf', 'redes', 'No grimório de Cassandro, a camada de Aplicação representa serviços usados pelo usuário, como HTTP e SMTP.', NULL, NULL, 'true', 'A camada de Aplicação corresponde aos protocolos e serviços diretamente usados pelas aplicações.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='No grimório de Cassandro, a camada de Aplicação representa serviços usados pelo usuário, como HTTP e SMTP.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'vf', 'redes', 'No modelo TCP/IP, as camadas de Sessão e Apresentação aparecem como camadas próprias e independentes.', NULL, NULL, 'false', 'No TCP/IP, Sessão e Apresentação do OSI não têm correspondência direta.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='No modelo TCP/IP, as camadas de Sessão e Apresentação aparecem como camadas próprias e independentes.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'vf', 'redes', 'Uma LAN normalmente fica restrita a uma construção, edifício, residência, empresa ou campus.', NULL, NULL, 'true', 'LAN é uma rede local, normalmente privada e limitada a poucos quilômetros.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Uma LAN normalmente fica restrita a uma construção, edifício, residência, empresa ou campus.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'vf', 'redes', 'A WAN é sempre menor que uma LAN e serve apenas para conectar dois computadores na mesma sala.', NULL, NULL, 'false', 'WAN cobre grandes áreas e pode interligar redes metropolitanas, países ou continentes.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='A WAN é sempre menor que uma LAN e serve apenas para conectar dois computadores na mesma sala.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'vf', 'redes', 'Encapsulamento significa que dados de uma camada superior podem ser tratados como payload pela camada inferior.', NULL, NULL, 'true', 'No encapsulamento, cada camada acrescenta seus controles e trata o conteúdo superior como dados.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Encapsulamento significa que dados de uma camada superior podem ser tratados como payload pela camada inferior.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'completar', 'redes', 'Complete a senha da torre: o modelo OSI possui _____ camadas.', NULL, NULL, '[\"7\",\"sete\"]', 'O modelo OSI é composto por sete camadas.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Complete a senha da torre: o modelo OSI possui _____ camadas.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'completar', 'redes', 'Na travessia OSI, a camada que transforma dados em bits e sinais é a camada _____.', NULL, NULL, '[\"Física\",\"fisica\",\"física\"]', 'A camada Física envia bits pelo meio físico ou por radiofrequência.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Na travessia OSI, a camada que transforma dados em bits e sinais é a camada _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'completar', 'redes', 'No modelo TCP/IP, TCP e UDP ficam na camada de _____.', NULL, NULL, '[\"Transporte\",\"transporte\"]', 'TCP e UDP são protocolos da camada de Transporte.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='No modelo TCP/IP, TCP e UDP ficam na camada de _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'completar', 'redes', 'A sigla LAN significa rede de área _____.', NULL, NULL, '[\"local\",\"Local\"]', 'LAN vem de Local Area Network, rede de área local.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='A sigla LAN significa rede de área _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'completar', 'redes', 'No modelo TCP/IP, o protocolo usado na camada Inter-redes é o _____.', NULL, NULL, '[\"IP\",\"ip\"]', 'A camada Inter-redes do TCP/IP usa o protocolo IP.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='No modelo TCP/IP, o protocolo usado na camada Inter-redes é o _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'ordenar', 'redes', 'Ordene os andares do OSI do mais próximo do cabo até o mais próximo do usuário.', NULL, '[\"Aplicação\",\"Rede\",\"Física\",\"Transporte\",\"Enlace de dados\"]', '[2,4,1,3,0]', 'A sequência parcial correta é Física, Enlace, Rede, Transporte e Aplicação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Ordene os andares do OSI do mais próximo do cabo até o mais próximo do usuário.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'ordenar', 'redes', 'Ordene o encapsulamento de uma mensagem HTTP descendo a torre.', NULL, '[\"IP envolve o segmento\",\"HTTP gera a mensagem\",\"TCP prepara o segmento\",\"O meio físico transmite bits\"]', '[1,2,0,3]', 'A aplicação gera dados, o transporte segmenta, a rede endereça e o meio transmite.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Ordene o encapsulamento de uma mensagem HTTP descendo a torre.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'ordenar', 'redes', 'Ordene as redes por alcance, da menor para a maior.', NULL, '[\"WAN\",\"LAN\",\"MAN\"]', '[1,2,0]', 'LAN é local, MAN é metropolitana e WAN cobre grandes áreas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Ordene as redes por alcance, da menor para a maior.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'ordenar', 'redes', 'Ordene as camadas TCP/IP do topo para a base.', NULL, '[\"Host/Rede\",\"Aplicação\",\"Inter-redes\",\"Transporte\"]', '[1,3,2,0]', 'No TCP/IP: Aplicação, Transporte, Inter-redes e Host/Rede.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Ordene as camadas TCP/IP do topo para a base.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'ordenar', 'redes', 'Ordene a chegada de uma mensagem no host destino, da camada mais baixa até a aplicação.', NULL, '[\"Aplicação entrega ao usuário\",\"Física recebe sinais\",\"Rede interpreta o IP\",\"Enlace trata quadros\"]', '[1,3,2,0]', 'Na recepção, a mensagem sobe da Física até a Aplicação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Ordene a chegada de uma mensagem no host destino, da camada mais baixa até a aplicação.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'erro', 'redes', 'A Sentinela misturou uma camada. Qual associação está errada?', 'Aplicação = HTTP e SMTP\nTransporte = TCP e UDP\nRede = IP\nFísica = páginas web e e-mails', '[\"Aplicação = HTTP e SMTP\",\"Transporte = TCP e UDP\",\"Rede = IP\",\"Física = páginas web e e-mails\"]', '3', 'Páginas web e e-mails pertencem aos serviços de aplicação; a Física lida com sinais, cabos e bits.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='A Sentinela misturou uma camada. Qual associação está errada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'erro', 'redes', 'O mapa da torre diz que uma MAN cobre apenas um único cabo entre dois computadores. Qual é o problema?', NULL, '[\"MAN cobre área de uma cidade, não um simples cabo\",\"MAN é nome de protocolo HTTP\",\"MAN é uma camada do OSI\",\"Não há problema\"]', '0', 'MAN é rede metropolitana, como infraestrutura de uma cidade.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='O mapa da torre diz que uma MAN cobre apenas um único cabo entre dois computadores. Qual é o problema?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'erro', 'redes', 'O aprendiz escreveu: “No TCP/IP, Sessão e Apresentação são obrigatórias como no OSI”. Onde está a falha?', NULL, '[\"TCP/IP não possui equivalentes diretos para essas camadas\",\"OSI não tem camadas\",\"Sessão é cabo físico\",\"Apresentação é roteador\"]', '0', 'No TCP/IP, essas funções são absorvidas por outras camadas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='O aprendiz escreveu: “No TCP/IP, Sessão e Apresentação são obrigatórias como no OSI”. Onde está a falha?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'erro', 'redes', 'Qual frase do pergaminho está corrompida?', NULL, '[\"LAN costuma ser local\",\"WAN pode abranger continentes\",\"A camada de Rede realiza roteamento\",\"A camada Física escolhe rotas IP entre redes\"]', '3', 'Roteamento é responsabilidade da camada de Rede; Física trata o meio de transmissão.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Qual frase do pergaminho está corrompida?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'erro', 'redes', 'O Monstro do Encapsulamento afirma que payload é sempre o cabeçalho extra colocado pela camada. Qual correção vence o monstro?', NULL, '[\"Payload é o conteúdo transportado; overhead é o dado adicional de controle\",\"Payload é a tomada do cabo\",\"Payload é sinônimo de firewall\",\"Overhead é sempre a mensagem útil\"]', '0', 'O conteúdo útil é payload; cabeçalhos e controles adicionados são overhead.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='O Monstro do Encapsulamento afirma que payload é sempre o cabeçalho extra colocado pela camada. Qual correção vence o monstro?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'arrastar', 'redes', 'Arraste cada camada OSI ao poder correto da torre.', NULL, '{\"itens\":[\"Física\",\"Enlace de dados\",\"Rede\",\"Transporte\"],\"alvos\":[\"Sinais, cabos e bits\",\"Quadros entre pontos físicos\",\"Roteamento e IP\",\"Comunicação fim a fim\"]}', '[0,1,2,3]', 'Cada camada possui uma responsabilidade própria no fluxo da rede.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Arraste cada camada OSI ao poder correto da torre.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'arrastar', 'redes', 'Arraste cada sigla ao território correto do mapa de Cassandro.', NULL, '{\"itens\":[\"LAN\",\"MAN\",\"WAN\",\"WLAN\"],\"alvos\":[\"Área local\",\"Área metropolitana\",\"Área geográfica ampla\",\"Rede local sem fio\"]}', '[0,1,2,3]', 'As siglas classificam redes pelo alcance e forma de acesso.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Arraste cada sigla ao território correto do mapa de Cassandro.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'arrastar', 'redes', 'Arraste cada camada TCP/IP ao equivalente mais próximo.', NULL, '{\"itens\":[\"Aplicação\",\"Transporte\",\"Inter-redes\",\"Host/Rede\"],\"alvos\":[\"Aplicação do OSI\",\"Transporte do OSI\",\"Rede do OSI\",\"Física + Enlace do OSI\"]}', '[0,1,2,3]', 'O TCP/IP simplifica e agrupa algumas funções do OSI.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Arraste cada camada TCP/IP ao equivalente mais próximo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'arrastar', 'redes', 'Arraste cada item ao tipo correto: payload ou overhead.', NULL, '{\"itens\":[\"Cabeçalho IP\",\"Mensagem HTTP\",\"Cabeçalho TCP\",\"Dados do usuário\"],\"alvos\":[\"Overhead\",\"Payload\",\"Overhead\",\"Payload\"]}', '[0,1,2,3]', 'Cabeçalhos são controle adicional; dados da aplicação são conteúdo transportado.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Arraste cada item ao tipo correto: payload ou overhead.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'arrastar', 'redes', 'Arraste cada criatura da torre para a camada onde costuma agir.', NULL, '{\"itens\":[\"Roteador\",\"Switch\",\"Navegador web\",\"Cabo UTP\"],\"alvos\":[\"Rede\",\"Enlace de dados\",\"Aplicação\",\"Física\"]}', '[0,1,2,3]', 'Roteadores lidam com rotas, switches com enlace, navegadores com aplicação e cabos com a camada física.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Arraste cada criatura da torre para a camada onde costuma agir.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'multipla', 'redes', 'Qual camada do modelo OSI cuida do endereçamento IP e do roteamento?', NULL, '[\"Física\",\"Enlace\",\"Rede\",\"Aplicação\"]', '2', 'A camada de Rede (3) endereça (IP) e escolhe rotas entre redes.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Qual camada do modelo OSI cuida do endereçamento IP e do roteamento?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'multipla', 'redes', 'Qual camada cuida da entrega fim-a-fim e abriga TCP e UDP?', NULL, '[\"Rede\",\"Transporte\",\"Sessão\",\"Física\"]', '1', 'A camada de Transporte (4) controla a entrega entre os processos: TCP e UDP vivem aqui.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Qual camada cuida da entrega fim-a-fim e abriga TCP e UDP?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'vf', 'redes', 'O modelo TCP/IP é mais enxuto que o OSI, com menos camadas.', NULL, NULL, 'true', 'O TCP/IP agrupa as 7 camadas do OSI em 4 (ou 5).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='O modelo TCP/IP é mais enxuto que o OSI, com menos camadas.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'multipla', 'redes', 'O switch opera principalmente em qual camada do OSI?', NULL, '[\"Física\",\"Enlace\",\"Rede\",\"Transporte\"]', '1', 'O switch comuta quadros por endereço MAC: camada de Enlace (2).', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='O switch opera principalmente em qual camada do OSI?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'completar', 'redes', 'A camada que entrega serviços direto ao usuário (HTTP, DNS, FTP) é a camada de ___.', NULL, NULL, '[\"Aplicação\",\"aplicacao\",\"aplicação\"]', 'A camada de Aplicação (7) é onde vivem os protocolos que o usuário usa.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='A camada que entrega serviços direto ao usuário (HTTP, DNS, FTP) é a camada de ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'ordenar', 'redes', 'Ordene as camadas do OSI da 5 para a 7:', NULL, '[\"Aplicação\",\"Sessão\",\"Apresentação\"]', '[1,2,0]', 'Camada 5 Sessão, 6 Apresentação, 7 Aplicação.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Ordene as camadas do OSI da 5 para a 7:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'multipla', 'redes', 'Para que serve o DNS?', NULL, '[\"Criptografar senhas\",\"Traduzir nomes (ex.: site.com) em endereços IP\",\"Comprimir imagens\",\"Soldar cabos\"]', '1', 'O DNS resolve nomes de domínio em endereços IP.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Para que serve o DNS?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'multipla', 'redes', 'Qual destes é um endereço IPv4 válido?', NULL, '[\"256.1.1.1\",\"192.168.0.1\",\"abc.def\",\"12.34\"]', '1', 'Cada octeto vai de 0 a 255; 192.168.0.1 é válido (256 não é).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Qual destes é um endereço IPv4 válido?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'vf', 'redes', 'O endereço 127.0.0.1 (localhost) refere-se à própria máquina.', NULL, NULL, 'true', 'É o endereço de loopback: a máquina falando consigo mesma.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='O endereço 127.0.0.1 (localhost) refere-se à própria máquina.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'multipla', 'redes', 'Qual equipamento encaminha pacotes entre redes diferentes?', NULL, '[\"switch\",\"roteador\",\"hub\",\"monitor\"]', '1', 'O roteador decide as rotas entre redes distintas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Qual equipamento encaminha pacotes entre redes diferentes?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'multipla', 'redes', 'O Roteador Selvagem entrega a Cassandro um datagrama. Em quais duas grandes partes ele se divide?', NULL, '[\"Cabeçalho e dados\",\"Tela e botão\",\"Usuário e senha\",\"Classe e objeto\"]', '0', 'O datagrama IP possui cabeçalho e dados.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='O Roteador Selvagem entrega a Cassandro um datagrama. Em quais duas grandes partes ele se divide?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'multipla', 'redes', 'Qual campo do datagrama impede que um pacote fique vagando eternamente pelo reino?', NULL, '[\"TTL / tempo de vida\",\"Porta HTTP\",\"Nome do usuário\",\"Cor do cabo\"]', '0', 'O TTL limita a quantidade máxima de saltos do datagrama.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Qual campo do datagrama impede que um pacote fique vagando eternamente pelo reino?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'multipla', 'redes', 'Um aprendiz digita www.reino.com em vez de decorar números. Qual serviço traduz esse nome para IP?', NULL, '[\"DHCP\",\"DNS\",\"FTP\",\"TCP\"]', '1', 'O DNS resolve nomes como URLs em endereços IP.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Um aprendiz digita www.reino.com em vez de decorar números. Qual serviço traduz esse nome para IP?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'multipla', 'redes', 'O endereço 127.0.0.1 aparece no espelho do aprendiz. Que tipo de endereço é esse?', NULL, '[\"Broadcast\",\"Loopback\",\"Endereço de rede\",\"Máscara de sub-rede\"]', '1', 'Endereços 127.x.x.x são loopback e são processados localmente.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='O endereço 127.0.0.1 aparece no espelho do aprendiz. Que tipo de endereço é esse?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'multipla', 'redes', 'Dois hosts estão em redes diferentes. Para sair da LAN, a mensagem deve procurar primeiro qual guardião?', NULL, '[\"Gateway/roteador\",\"Monitor\",\"Editor de texto\",\"Impressora\"]', '0', 'Para alcançar outra rede, o host envia pacotes ao gateway/roteador.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Dois hosts estão em redes diferentes. Para sair da LAN, a mensagem deve procurar primeiro qual guardião?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'vf', 'redes', 'No IPv4, cada octeto deve ficar no intervalo de 0 a 255.', NULL, NULL, 'true', 'IPv4 é representado por quatro octetos de 8 bits.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='No IPv4, cada octeto deve ficar no intervalo de 0 a 255.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'vf', 'redes', 'A máscara de sub-rede ajuda a separar a parte de rede da parte de host no endereço IP.', NULL, NULL, 'true', 'A máscara funciona como filtro para identificar rede e host.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='A máscara de sub-rede ajuda a separar a parte de rede da parte de host no endereço IP.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'vf', 'redes', 'O endereço de broadcast de uma rede /24 termina com todos os bits de host em 0.', NULL, NULL, 'false', 'Com todos os bits de host em 0 temos o endereço de rede; broadcast usa bits de host em 1.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='O endereço de broadcast de uma rede /24 termina com todos os bits de host em 0.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'vf', 'redes', 'Datagramas entre dois hosts podem seguir rotas diferentes pela rede.', NULL, NULL, 'true', 'O roteamento de pacotes permite caminhos diferentes para datagramas e respostas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Datagramas entre dois hosts podem seguir rotas diferentes pela rede.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'vf', 'redes', 'Na topologia em estrela, não existe elemento central; cada nó liga-se diretamente a dois vizinhos.', NULL, NULL, 'false', 'Essa descrição corresponde ao anel; na estrela há um elemento central, como o switch.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Na topologia em estrela, não existe elemento central; cada nó liga-se diretamente a dois vizinhos.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'completar', 'redes', 'Complete o encantamento: o protocolo de rede mais usado para endereçar datagramas é o _____.', NULL, NULL, '[\"IP\",\"ip\"]', 'O protocolo IP atua na camada de rede/inter-redes.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Complete o encantamento: o protocolo de rede mais usado para endereçar datagramas é o _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'completar', 'redes', 'O serviço que traduz URL para endereço IP chama-se _____.', NULL, NULL, '[\"DNS\",\"dns\"]', 'DNS significa sistema/serviço de nomes de domínio.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='O serviço que traduz URL para endereço IP chama-se _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'completar', 'redes', 'Endereços iniciados por 127 são endereços de _____.', NULL, NULL, '[\"loopback\",\"Loopback\"]', 'Loopback permite testar a própria máquina sem sair pela interface de rede.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Endereços iniciados por 127 são endereços de _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'completar', 'redes', 'Na máscara 255.255.255.0, a notação CIDR equivalente é /_____.', NULL, NULL, '[\"24\"]', '255.255.255.0 possui 24 bits de rede.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Na máscara 255.255.255.0, a notação CIDR equivalente é /_____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'completar', 'redes', 'O endereço com todos os bits de host em 1 é o endereço de _____.', NULL, NULL, '[\"broadcast\",\"Broadcast\"]', 'Broadcast envia para todos os hosts da rede.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='O endereço com todos os bits de host em 1 é o endereço de _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'ordenar', 'redes', 'Ordene a resolução de uma URL até o início da rota.', NULL, '[\"Cliente recebe o IP\",\"Usuário informa a URL\",\"Consulta segue ao DNS\",\"Pacote pode ser enviado ao destino\"]', '[1,2,0,3]', 'Primeiro há a URL, depois consulta DNS, retorno do IP e comunicação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Ordene a resolução de uma URL até o início da rota.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'ordenar', 'redes', 'Ordene o cálculo da rede de um host usando máscara.', NULL, '[\"Aplicar AND bit a bit\",\"Obter o endereço de rede\",\"Ler endereço IP e máscara\",\"Comparar com outros hosts se necessário\"]', '[2,0,1,3]', 'A máscara é aplicada ao IP para descobrir a rede.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Ordene o cálculo da rede de um host usando máscara.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'ordenar', 'redes', 'Ordene a viagem para fora da rede local.', NULL, '[\"Host percebe que o destino é externo\",\"Host envia ao gateway\",\"Roteador encaminha para outra rede\",\"Destino recebe o datagrama\"]', '[0,1,2,3]', 'O gateway/roteador faz a ponte entre redes distintas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Ordene a viagem para fora da rede local.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'ordenar', 'redes', 'Ordene a abrangência de topologias/estruturas citadas, do nó local até a internet ampla.', NULL, '[\"WAN\",\"Host na LAN\",\"MAN\",\"Roteador de borda\"]', '[1,3,2,0]', 'O host sai pela borda, alcança redes metropolitanas e depois grandes redes WAN.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Ordene a abrangência de topologias/estruturas citadas, do nó local até a internet ampla.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'ordenar', 'redes', 'Ordene os endereços reservados de uma rede /24 típica.', NULL, '[\"Broadcast .255\",\"Primeiro host .1\",\"Rede .0\",\"Último host .254\"]', '[2,1,3,0]', 'Em /24, .0 é rede, .1 inicia hosts, .254 é último host e .255 é broadcast.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Ordene os endereços reservados de uma rede /24 típica.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'erro', 'redes', 'O goblin do IP escreveu um endereço impossível. Qual opção denuncia o erro?', NULL, '[\"192.168.1.10\",\"10.0.0.1\",\"256.10.0.1\",\"172.16.0.5\"]', '2', 'Em IPv4, nenhum octeto pode ultrapassar 255.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='O goblin do IP escreveu um endereço impossível. Qual opção denuncia o erro?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'erro', 'redes', 'Qual afirmação do Roteador Selvagem está errada?', NULL, '[\"TTL evita permanência infinita do datagrama\",\"DNS traduz nomes em IPs\",\"Máscara separa rede e host\",\"Loopback sempre atravessa a internet antes de voltar\"]', '3', 'Loopback é tratado localmente, sem sair pela interface de rede.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Qual afirmação do Roteador Selvagem está errada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'erro', 'redes', 'Um aprendiz marcou 192.168.1.0/24 como host comum. Qual é a falha?', NULL, '[\"É endereço de rede, não host utilizável\",\"É sempre porta HTTP\",\"É endereço MAC\",\"É nome DNS\"]', '0', 'Em uma rede /24, .0 representa a rede.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Um aprendiz marcou 192.168.1.0/24 como host comum. Qual é a falha?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'erro', 'redes', 'O diagrama mostra uma estrela sem elemento central. O que precisa ser corrigido?', NULL, '[\"Topologia em estrela exige um agregador central, como switch\",\"Estrela nunca usa switch\",\"Anel e estrela são a mesma coisa\",\"Topologia não tem nós\"]', '0', 'Na topologia em estrela, os hosts se conectam a um elemento central.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='O diagrama mostra uma estrela sem elemento central. O que precisa ser corrigido?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'erro', 'redes', 'O escriba diz: “Broadcast usa todos os bits de host em 0”. Qual correção salva o mapa?', NULL, '[\"Todos os bits de host em 0 indicam rede; broadcast usa todos em 1\",\"Broadcast é igual a loopback\",\"Broadcast usa sempre 127.0.0.1\",\"Broadcast é uma porta TCP\"]', '0', 'Endereço de rede e broadcast são reservados com padrões opostos nos bits de host.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='O escriba diz: “Broadcast usa todos os bits de host em 0”. Qual correção salva o mapa?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'arrastar', 'redes', 'Arraste cada campo ou conceito IPv4 ao seu papel.', NULL, '{\"itens\":[\"TTL\",\"Endereço de origem\",\"Endereço de destino\",\"Checksum do cabeçalho\"],\"alvos\":[\"Limita saltos\",\"Identifica remetente\",\"Identifica alvo\",\"Verifica erro no cabeçalho\"]}', '[0,1,2,3]', 'Esses campos ajudam o datagrama a circular corretamente.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Arraste cada campo ou conceito IPv4 ao seu papel.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'arrastar', 'redes', 'Arraste cada item ao significado correto.', NULL, '{\"itens\":[\"DNS\",\"Gateway\",\"Loopback\",\"Broadcast\"],\"alvos\":[\"Nome para IP\",\"Saída para outra rede\",\"Teste local\",\"Mensagem para todos da rede\"]}', '[0,1,2,3]', 'São conceitos centrais de IP, nomes e rotas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Arraste cada item ao significado correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'arrastar', 'redes', 'Arraste cada exemplo à categoria correta.', NULL, '{\"itens\":[\"192.168.1.0/24\",\"192.168.1.255\",\"192.168.1.50\",\"127.0.0.1\"],\"alvos\":[\"Endereço de rede\",\"Broadcast\",\"Host\",\"Loopback\"]}', '[0,1,2,3]', 'Cada endereço cumpre um papel específico.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Arraste cada exemplo à categoria correta.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'arrastar', 'redes', 'Arraste cada topologia ao traço dominante.', NULL, '{\"itens\":[\"Estrela\",\"Anel\",\"Barramento\",\"Rede roteada\"],\"alvos\":[\"Elemento central\",\"Nós ligados a dois vizinhos\",\"Meio compartilhado\",\"Caminho entre redes\"]}', '[0,1,2,3]', 'Topologias e roteamento organizam caminhos de comunicação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Arraste cada topologia ao traço dominante.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'arrastar', 'redes', 'Arraste cada notação de máscara ao equivalente aproximado.', NULL, '{\"itens\":[\"/8\",\"/16\",\"/24\",\"255.255.254.0\"],\"alvos\":[\"255.0.0.0\",\"255.255.0.0\",\"255.255.255.0\",\"/23\"]}', '[0,1,2,3]', 'A notação CIDR representa a quantidade de bits 1 na máscara.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Arraste cada notação de máscara ao equivalente aproximado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'multipla', 'redes', 'Quantos bits tem um endereço IPv4?', NULL, '[\"16\",\"32\",\"64\",\"128\"]', '1', 'IPv4 usa 32 bits, divididos em quatro octetos (ex.: 192.168.0.1).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Quantos bits tem um endereço IPv4?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'multipla', 'redes', 'Quantos bits tem um endereço IPv6?', NULL, '[\"32\",\"64\",\"128\",\"256\"]', '2', 'IPv6 usa 128 bits — espaço gigantesco, criado porque o IPv4 acabou.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Quantos bits tem um endereço IPv6?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'vf', 'redes', 'A máscara de sub-rede separa a parte de rede da parte de host de um endereço IP.', NULL, NULL, 'true', 'A máscara (ex.: 255.255.255.0) diz quais bits identificam a rede e quais o host.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='A máscara de sub-rede separa a parte de rede da parte de host de um endereço IP.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'multipla', 'redes', 'Qual protocolo distribui endereços IP automaticamente aos dispositivos da rede?', NULL, '[\"DNS\",\"DHCP\",\"HTTP\",\"ARP\"]', '1', 'O DHCP atribui IP, máscara e gateway sem configuração manual.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Qual protocolo distribui endereços IP automaticamente aos dispositivos da rede?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'multipla', 'redes', 'O \"gateway padrão\" de uma rede local é, normalmente:', NULL, '[\"O servidor DNS público\",\"O roteador que encaminha o tráfego para fora da rede local\",\"O switch principal\",\"O cabo de internet\"]', '1', 'Pacotes destinados a outras redes saem pelo gateway (o roteador da borda).', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='O \"gateway padrão\" de uma rede local é, normalmente:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'completar', 'redes', 'O serviço que traduz www.exemplo.com em um endereço IP é o ___.', NULL, NULL, '[\"DNS\",\"dns\"]', 'O DNS é a \"agenda de contatos\" da internet: nome → IP.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='O serviço que traduz www.exemplo.com em um endereço IP é o ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'multipla', 'redes', 'Qual protocolo é confiável e garante entrega ordenada dos pacotes?', NULL, '[\"UDP\",\"TCP\",\"IP\",\"DNS\"]', '1', 'O TCP confirma e reordena pacotes, garantindo a entrega.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Qual protocolo é confiável e garante entrega ordenada dos pacotes?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'multipla', 'redes', 'Qual protocolo é mais rápido, porém sem garantia de entrega (bom para streaming)?', NULL, '[\"TCP\",\"UDP\",\"FTP\",\"SMTP\"]', '1', 'O UDP é leve e veloz, sem o overhead de confirmação do TCP.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Qual protocolo é mais rápido, porém sem garantia de entrega (bom para streaming)?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'multipla', 'redes', 'O código de status HTTP 404 significa:', NULL, '[\"Sucesso\",\"Recurso não encontrado\",\"Erro do servidor\",\"Redirecionamento\"]', '1', '404 indica que o recurso solicitado não foi encontrado.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O código de status HTTP 404 significa:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'multipla', 'redes', 'O código de status HTTP 200 significa:', NULL, '[\"Não encontrado\",\"OK / sucesso\",\"Proibido\",\"Erro interno\"]', '1', '200 OK indica que a requisição foi bem-sucedida.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O código de status HTTP 200 significa:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'multipla', 'redes', 'O Pacote Corrompido desafia: qual protocolo de transporte é orientado à conexão e busca entrega confiável?', NULL, '[\"UDP\",\"TCP\",\"IP\",\"DNS\"]', '1', 'TCP é orientado à conexão e possui mecanismos de confiabilidade.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O Pacote Corrompido desafia: qual protocolo de transporte é orientado à conexão e busca entrega confiável?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'multipla', 'redes', 'Qual protocolo é mais simples, sem conexão e não confirma se os dados chegaram corretamente?', NULL, '[\"TCP\",\"UDP\",\"HTTP\",\"SSH\"]', '1', 'UDP é sem conexão e não oferece a mesma confiabilidade do TCP.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Qual protocolo é mais simples, sem conexão e não confirma se os dados chegaram corretamente?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'multipla', 'redes', 'No livro de portas bem conhecidas, qual porta é associada ao HTTP?', NULL, '[\"25\",\"53\",\"80\",\"110\"]', '2', 'HTTP utiliza tradicionalmente a porta 80.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='No livro de portas bem conhecidas, qual porta é associada ao HTTP?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'multipla', 'redes', 'Na fase TCP, UDP e HTTP, qual status HTTP representa sucesso na requisição?', NULL, '[\"200\",\"404\",\"500\",\"301\"]', '0', 'Status 200 indica sucesso/OK.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Na fase TCP, UDP e HTTP, qual status HTTP representa sucesso na requisição?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'multipla', 'redes', 'O ritual de abertura de conexão TCP é conhecido como:', NULL, '[\"two-way echo\",\"three-way handshake\",\"DNS enumeration\",\"loopback chant\"]', '1', 'O TCP inicia a conexão com o three-way handshake.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O ritual de abertura de conexão TCP é conhecido como:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'vf', 'redes', 'Portas ajudam a identificar serviços diferentes rodando no mesmo host.', NULL, NULL, 'true', 'A camada de transporte usa portas junto do endereço do host para identificar serviços.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Portas ajudam a identificar serviços diferentes rodando no mesmo host.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'vf', 'redes', 'UDP é indicado quando a aplicação aceita priorizar tempo real em vez de confirmação rígida de entrega.', NULL, NULL, 'true', 'Videoconferência é um exemplo em que tempo real pode pesar mais que confiabilidade total.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='UDP é indicado quando a aplicação aceita priorizar tempo real em vez de confirmação rígida de entrega.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'vf', 'redes', 'TCP e UDP são protocolos da camada de Aplicação.', NULL, NULL, 'false', 'TCP e UDP pertencem à camada de Transporte.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='TCP e UDP são protocolos da camada de Aplicação.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'vf', 'redes', 'O código HTTP 404 indica que o recurso solicitado não foi encontrado.', NULL, NULL, 'true', '404 é o status de não encontrado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O código HTTP 404 indica que o recurso solicitado não foi encontrado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'vf', 'redes', 'HTTPS é apenas HTTP sem qualquer preocupação adicional de segurança.', NULL, NULL, 'false', 'HTTPS é HTTP seguro, associado ao uso de criptografia.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='HTTPS é apenas HTTP sem qualquer preocupação adicional de segurança.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'completar', 'redes', 'Complete: o protocolo confiável e orientado à conexão é o _____.', NULL, NULL, '[\"TCP\",\"tcp\"]', 'TCP fornece conexão e mecanismos de confiabilidade.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Complete: o protocolo confiável e orientado à conexão é o _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'completar', 'redes', 'Complete: o protocolo de transporte sem conexão é o _____.', NULL, NULL, '[\"UDP\",\"udp\"]', 'UDP é o User Datagram Protocol e não mantém conexão.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Complete: o protocolo de transporte sem conexão é o _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'completar', 'redes', 'A porta bem conhecida do HTTP é _____.', NULL, NULL, '[\"80\"]', 'HTTP tradicionalmente usa a porta 80.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='A porta bem conhecida do HTTP é _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'completar', 'redes', 'O status HTTP “não encontrado” é _____.', NULL, NULL, '[\"404\"]', '404 significa recurso não encontrado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O status HTTP “não encontrado” é _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'completar', 'redes', 'O handshake de abertura do TCP usa o bit de controle _____.', NULL, NULL, '[\"SYN\",\"syn\"]', 'O cliente inicia a conexão enviando SYN.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O handshake de abertura do TCP usa o bit de controle _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'ordenar', 'redes', 'Ordene o three-way handshake TCP.', NULL, '[\"Cliente envia SYN\",\"Servidor responde SYN + ACK\",\"Cliente responde ACK\"]', '[0,1,2]', 'O three-way handshake segue SYN, SYN+ACK e ACK.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Ordene o three-way handshake TCP.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'ordenar', 'redes', 'Ordene a escolha do protocolo para uma videoconferência em tempo real.', NULL, '[\"Aceitar possível perda pequena\",\"Priorizar baixa latência\",\"Escolher UDP\",\"Transmitir sem confirmação rígida\"]', '[1,0,2,3]', 'Quando tempo real é prioridade, UDP pode ser escolhido pela simplicidade.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Ordene a escolha do protocolo para uma videoconferência em tempo real.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'ordenar', 'redes', 'Ordene as camadas envolvidas em uma navegação web do topo para baixo.', NULL, '[\"IP encaminha datagramas\",\"HTTP define a mensagem\",\"TCP transporta com conexão\",\"Meio físico envia sinais\"]', '[1,2,0,3]', 'HTTP está na aplicação, TCP no transporte, IP na rede e o meio na física.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Ordene as camadas envolvidas em uma navegação web do topo para baixo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'ordenar', 'redes', 'Ordene os status HTTP por significado nesta missão.', NULL, '[\"404 = não encontrado\",\"200 = sucesso\",\"500 = erro do servidor\"]', '[1,0,2]', '200 indica sucesso; 404 não encontrado; 500 erro interno do servidor.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Ordene os status HTTP por significado nesta missão.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'ordenar', 'redes', 'Ordene o uso de porta em um serviço web simples.', NULL, '[\"Servidor escuta em uma porta\",\"Cliente conecta ao IP e porta\",\"Aplicação troca mensagens HTTP\",\"Conexão é encerrada\"]', '[0,1,2,3]', 'O serviço usa porta para ser encontrado, depois troca mensagens e encerra.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Ordene o uso de porta em um serviço web simples.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'erro', 'redes', 'Qual linha do pergaminho está errada?', 'TCP = confiável e orientado à conexão\nUDP = sem conexão\nHTTP = protocolo de aplicação\nIP = protocolo de transporte confiável', '[\"TCP = confiável e orientado à conexão\",\"UDP = sem conexão\",\"HTTP = protocolo de aplicação\",\"IP = protocolo de transporte confiável\"]', '3', 'IP é protocolo de rede/inter-redes, não transporte confiável.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Qual linha do pergaminho está errada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'erro', 'redes', 'O aprendiz escolheu UDP para garantir retransmissão automática e ordenação completa. Qual é o erro?', NULL, '[\"Essa garantia é típica do TCP, não do UDP\",\"UDP não usa portas\",\"UDP é uma camada física\",\"Não existe protocolo TCP\"]', '0', 'UDP não implementa os mecanismos de confiabilidade do TCP.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O aprendiz escolheu UDP para garantir retransmissão automática e ordenação completa. Qual é o erro?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'erro', 'redes', 'Qual associação de porta está corrompida para a fase?', NULL, '[\"HTTP = 80\",\"HTTPS = 443\",\"DNS = 53\",\"SMTP = 404\"]', '3', '404 é status HTTP, não porta SMTP.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Qual associação de porta está corrompida para a fase?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'erro', 'redes', 'O monstro diz: “Status 500 significa recurso não encontrado”. Qual correção derrota o monstro?', NULL, '[\"500 indica erro do servidor; 404 indica não encontrado\",\"500 é porta DNS\",\"404 indica sucesso\",\"200 indica erro do servidor\"]', '0', 'Em HTTP, 500 é erro interno do servidor e 404 é não encontrado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O monstro diz: “Status 500 significa recurso não encontrado”. Qual correção derrota o monstro?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'erro', 'redes', 'O handshake TCP foi registrado assim: ACK, SYN+ACK, SYN. Qual é o problema?', NULL, '[\"A ordem correta começa com SYN\",\"TCP não tem handshake\",\"SYN só aparece no DNS\",\"ACK é endereço IP\"]', '0', 'A conexão TCP inicia com SYN do cliente.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O handshake TCP foi registrado assim: ACK, SYN+ACK, SYN. Qual é o problema?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'arrastar', 'redes', 'Arraste cada protocolo ao papel correto.', NULL, '{\"itens\":[\"TCP\",\"UDP\",\"HTTP\",\"HTTPS\"],\"alvos\":[\"Transporte confiável\",\"Transporte sem conexão\",\"Web em hipertexto\",\"Web com segurança\"]}', '[0,1,2,3]', 'Cada protocolo atua em um papel específico na comunicação.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Arraste cada protocolo ao papel correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'arrastar', 'redes', 'Arraste cada porta ao serviço esperado no mapa da fase.', NULL, '{\"itens\":[\"80\",\"443\",\"53\",\"25\"],\"alvos\":[\"HTTP\",\"HTTPS\",\"DNS\",\"SMTP\"]}', '[0,1,2,3]', 'Essas portas são referências usuais para serviços de rede.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Arraste cada porta ao serviço esperado no mapa da fase.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'arrastar', 'redes', 'Arraste cada status HTTP ao significado.', NULL, '{\"itens\":[\"200\",\"404\",\"500\",\"301\"],\"alvos\":[\"Sucesso\",\"Não encontrado\",\"Erro do servidor\",\"Redirecionamento\"]}', '[0,1,2,3]', 'Os códigos HTTP indicam o resultado da requisição.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Arraste cada status HTTP ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'arrastar', 'redes', 'Arraste cada campo TCP ao seu uso.', NULL, '{\"itens\":[\"SEQ\",\"ACK\",\"Janela de recepção\",\"FIN\"],\"alvos\":[\"Sequência\",\"Reconhecimento\",\"Controle de fluxo\",\"Encerramento\"]}', '[0,1,2,3]', 'O TCP usa campos e flags para controlar a comunicação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Arraste cada campo TCP ao seu uso.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'arrastar', 'redes', 'Arraste cada situação ao protocolo mais adequado.', NULL, '{\"itens\":[\"Página web comum\",\"Videoconferência em tempo real\",\"Acesso web seguro\",\"Serviço que exige entrega ordenada\"],\"alvos\":[\"HTTP\",\"UDP\",\"HTTPS\",\"TCP\"]}', '[0,1,2,3]', 'A escolha depende de aplicação, segurança, latência e confiabilidade.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Arraste cada situação ao protocolo mais adequado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'multipla', 'redes', 'O \"aperto de mão\" de três vias (SYN, SYN-ACK, ACK) pertence a qual protocolo?', NULL, '[\"UDP\",\"TCP\",\"HTTP\",\"DNS\"]', '1', 'O TCP estabelece a conexão com o three-way handshake antes de enviar dados.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O \"aperto de mão\" de três vias (SYN, SYN-ACK, ACK) pertence a qual protocolo?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'multipla', 'redes', 'Qual protocolo é preferido para chamadas de vídeo ao vivo e jogos online?', NULL, '[\"TCP\",\"UDP\",\"FTP\",\"SMTP\"]', '1', 'O UDP é veloz e sem overhead de confirmação — melhor perder um quadro do que travar.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Qual protocolo é preferido para chamadas de vídeo ao vivo e jogos online?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'multipla', 'redes', 'O código de status HTTP 500 significa:', NULL, '[\"Sucesso\",\"Não encontrado\",\"Erro interno do servidor\",\"Redirecionamento\"]', '2', '5xx são erros do servidor; 500 é a falha interna genérica.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O código de status HTTP 500 significa:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'multipla', 'redes', 'O código de status HTTP 403 significa:', NULL, '[\"OK\",\"Proibido (acesso negado)\",\"Não encontrado\",\"Criado\"]', '1', '403 Forbidden: o servidor entendeu o pedido, mas se recusa a atendê-lo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O código de status HTTP 403 significa:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'vf', 'redes', 'HTTPS é o HTTP com uma camada de criptografia (TLS/SSL).', NULL, NULL, 'true', 'O TLS cifra a comunicação, protegendo os dados em trânsito.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='HTTPS é o HTTP com uma camada de criptografia (TLS/SSL).') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'completar', 'redes', 'O método HTTP usado para ENVIAR os dados de um formulário ao servidor é o ___.', NULL, NULL, '[\"POST\",\"post\"]', 'POST envia dados no corpo da requisição; GET apenas busca recursos.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O método HTTP usado para ENVIAR os dados de um formulário ao servidor é o ___.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'multipla', 'redes', 'No TCP, o que confirma que um pacote chegou ao destino?', NULL, '[\"Um ACK (acknowledgement)\",\"Um DNS\",\"Um cookie\",\"Um ping infinito\"]', '0', 'O receptor envia um ACK confirmando o recebimento.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='No TCP, o que confirma que um pacote chegou ao destino?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'vf', 'redes', 'Se um pacote TCP se perde no caminho, ele pode ser retransmitido.', NULL, NULL, 'true', 'O TCP detecta a perda (falta de ACK) e retransmite.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Se um pacote TCP se perde no caminho, ele pode ser retransmitido.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'multipla', 'redes', 'Um timeout acontece quando:', NULL, '[\"A resposta chega cedo demais\",\"A resposta não chega no tempo esperado\",\"O IP é válido\",\"O DNS resolve corretamente\"]', '1', 'Timeout é o estouro do tempo de espera por uma resposta.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Um timeout acontece quando:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'multipla', 'redes', 'O Eco do Timeout escondeu uma confirmação. No TCP, qual campo/sinal indica reconhecimento de recebimento?', NULL, '[\"ACK\",\"DNS\",\"TTL\",\"HTTP\"]', '0', 'ACK indica reconhecimento de dados recebidos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='O Eco do Timeout escondeu uma confirmação. No TCP, qual campo/sinal indica reconhecimento de recebimento?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'multipla', 'redes', 'Se o emissor TCP não recebe confirmação no tempo esperado, o que tende a acontecer?', NULL, '[\"Retransmissão\",\"Troca para HDMI\",\"Apagar a máscara\",\"Gerar endereço MAC\"]', '0', 'Sem ACK, o TCP pode retransmitir a mensagem.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Se o emissor TCP não recebe confirmação no tempo esperado, o que tende a acontecer?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'multipla', 'redes', 'Qual campo TCP informa quantos bytes o receptor está disposto a aceitar?', NULL, '[\"Janela de recepção\",\"Endereço de origem\",\"Tipo de serviço\",\"URL\"]', '0', 'A janela de recepção participa do controle de fluxo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Qual campo TCP informa quantos bytes o receptor está disposto a aceitar?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'multipla', 'redes', 'Pacotes chegaram fora de ordem depois de rotas diferentes. Qual recurso ajuda o TCP a reorganizar a mensagem?', NULL, '[\"Número de sequência\",\"Conector RJ-45\",\"Nome DNS\",\"Topologia em estrela\"]', '0', 'Números de sequência permitem ordenar os fragmentos recebidos.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Pacotes chegaram fora de ordem depois de rotas diferentes. Qual recurso ajuda o TCP a reorganizar a mensagem?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'multipla', 'redes', 'O timeout da masmorra representa:', NULL, '[\"Tempo de espera estourado sem resposta esperada\",\"Cabo com conector azul\",\"URL traduzida com sucesso\",\"Uma camada do OSI\"]', '0', 'Timeout ocorre quando a resposta não chega dentro do tempo esperado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='O timeout da masmorra representa:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'vf', 'redes', 'No TCP, a falta de ACK pode levar à retransmissão da mensagem.', NULL, NULL, 'true', 'A retransmissão é usada para manter a comunicação confiável.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='No TCP, a falta de ACK pode levar à retransmissão da mensagem.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'vf', 'redes', 'A janela de recepção é parte do controle de fluxo do TCP.', NULL, NULL, 'true', 'Ela indica quanto o destinatário está disposto a aceitar.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='A janela de recepção é parte do controle de fluxo do TCP.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'vf', 'redes', 'Números de sequência ajudam a reordenar dados que chegaram fora de ordem.', NULL, NULL, 'true', 'SEQ permite remontar a mensagem corretamente.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Números de sequência ajudam a reordenar dados que chegaram fora de ordem.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'vf', 'redes', 'UDP resolve automaticamente pacotes perdidos com ACK e retransmissão obrigatória.', NULL, NULL, 'false', 'UDP não implementa esses mecanismos como o TCP.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='UDP resolve automaticamente pacotes perdidos com ACK e retransmissão obrigatória.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'vf', 'redes', 'ACK inválido ou ausente pode indicar que a comunicação não transcorreu como esperado.', NULL, NULL, 'true', 'O ACK é parte do controle de reconhecimento da comunicação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='ACK inválido ou ausente pode indicar que a comunicação não transcorreu como esperado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'completar', 'redes', 'Complete: no TCP, o reconhecimento de recebimento é indicado por _____.', NULL, NULL, '[\"ACK\",\"ack\"]', 'ACK vem de acknowledgement, reconhecimento.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Complete: no TCP, o reconhecimento de recebimento é indicado por _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'completar', 'redes', 'Complete: quando a confirmação não chega, o TCP pode fazer uma _____.', NULL, NULL, '[\"retransmissão\",\"retransmissao\",\"Retransmissão\"]', 'Retransmitir é enviar novamente o dado não confirmado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Complete: quando a confirmação não chega, o TCP pode fazer uma _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'completar', 'redes', 'O campo usado para ordenar fragmentos TCP chama-se número de _____.', NULL, NULL, '[\"sequência\",\"sequencia\",\"Sequência\"]', 'O número de sequência permite reconstruir a ordem correta.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='O campo usado para ordenar fragmentos TCP chama-se número de _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'completar', 'redes', 'O controle de fluxo usa a janela de _____.', NULL, NULL, '[\"recepção\",\"recepcao\",\"Recepção\"]', 'A janela de recepção informa a capacidade aceita pelo receptor.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='O controle de fluxo usa a janela de _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'completar', 'redes', 'O estouro do tempo de espera é chamado de _____.', NULL, NULL, '[\"timeout\",\"Timeout\"]', 'Timeout é o limite de espera sem resposta esperada.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='O estouro do tempo de espera é chamado de _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'ordenar', 'redes', 'Ordene o resgate de um pacote perdido no TCP.', NULL, '[\"Emissor envia segmento\",\"Receptor não confirma\",\"Tempo de espera estoura\",\"Emissor retransmite\"]', '[0,1,2,3]', 'Sem confirmação, o timeout leva à retransmissão.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Ordene o resgate de um pacote perdido no TCP.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'ordenar', 'redes', 'Ordene a reconstrução de dados fora de ordem.', NULL, '[\"Fragmentos chegam ao receptor\",\"Números de sequência são lidos\",\"Dados são reordenados\",\"Mensagem é entregue à aplicação\"]', '[0,1,2,3]', 'SEQ permite colocar os fragmentos na ordem correta.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Ordene a reconstrução de dados fora de ordem.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'ordenar', 'redes', 'Ordene o fluxo normal de confirmação TCP.', NULL, '[\"Cliente envia dados\",\"Servidor recebe e decodifica\",\"Servidor envia ACK\",\"Cliente prossegue a transmissão\"]', '[0,1,2,3]', 'O ACK permite ao emissor continuar com confiança.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Ordene o fluxo normal de confirmação TCP.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'ordenar', 'redes', 'Ordene o controle de fluxo pela janela de recepção.', NULL, '[\"Receptor informa janela\",\"Emissor limita o volume enviado\",\"Receptor processa dados\",\"Nova janela pode ser anunciada\"]', '[0,1,2,3]', 'A janela guia quanto o emissor pode transmitir.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Ordene o controle de fluxo pela janela de recepção.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'ordenar', 'redes', 'Ordene a investigação do Eco do Timeout.', NULL, '[\"Verificar se houve ACK\",\"Checar se o tempo expirou\",\"Retransmitir se necessário\",\"Confirmar recebimento correto\"]', '[0,1,2,3]', 'A análise começa pelo ACK e pelo tempo de espera.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Ordene a investigação do Eco do Timeout.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'erro', 'redes', 'Qual frase do pergaminho do TCP está errada?', NULL, '[\"ACK reconhece recebimento\",\"SEQ ajuda na ordem\",\"Janela de recepção controla fluxo\",\"Timeout significa que a resposta chegou cedo demais\"]', '3', 'Timeout indica estouro do tempo de espera, não resposta antecipada.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Qual frase do pergaminho do TCP está errada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'erro', 'redes', 'O Eco registrou: “Se um pacote TCP some, nunca há tentativa de novo envio”. Onde está a falha?', NULL, '[\"TCP pode retransmitir dados não confirmados\",\"TCP não usa portas\",\"IP corrige automaticamente todo erro\",\"HTTP substitui ACK\"]', '0', 'A retransmissão é parte do mecanismo de confiabilidade do TCP.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='O Eco registrou: “Se um pacote TCP some, nunca há tentativa de novo envio”. Onde está a falha?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'erro', 'redes', 'O diário diz: “A janela de recepção define a cor da interface gráfica”. Qual correção está certa?', NULL, '[\"Ela indica quantos bytes o receptor aceita\",\"Ela resolve nomes DNS\",\"Ela calcula endereço de rede\",\"Ela liga cabos UTP\"]', '0', 'A janela de recepção participa do controle de fluxo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='O diário diz: “A janela de recepção define a cor da interface gráfica”. Qual correção está certa?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'erro', 'redes', 'Qual decisão é inadequada para recuperar perda com confiabilidade garantida?', NULL, '[\"Usar TCP\",\"Verificar ACK\",\"Aguardar timeout antes de retransmitir\",\"Depender de UDP para ACK obrigatório\"]', '3', 'UDP não oferece ACK obrigatório e retransmissão como TCP.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Qual decisão é inadequada para recuperar perda com confiabilidade garantida?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'erro', 'redes', 'O pacote 3 chegou antes do pacote 2. O aprendiz entregou imediatamente à aplicação sem olhar SEQ. Qual é o problema?', NULL, '[\"Deveria usar números de sequência para reordenar\",\"Deveria consultar DNS\",\"Deveria trocar para cabo cross-over\",\"Deveria apagar o gateway\"]', '0', 'SEQ ajuda a reconstruir a ordem original.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='O pacote 3 chegou antes do pacote 2. O aprendiz entregou imediatamente à aplicação sem olhar SEQ. Qual é o problema?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'arrastar', 'redes', 'Arraste cada sinal TCP à função no resgate do pacote.', NULL, '{\"itens\":[\"ACK\",\"SEQ\",\"Janela de recepção\",\"Timeout\"],\"alvos\":[\"Reconhecer recebimento\",\"Ordenar fragmentos\",\"Controlar fluxo\",\"Detectar espera excessiva\"]}', '[0,1,2,3]', 'Esses recursos ajudam o TCP a manter comunicação confiável.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Arraste cada sinal TCP à função no resgate do pacote.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'arrastar', 'redes', 'Arraste cada evento ao efeito esperado.', NULL, '{\"itens\":[\"ACK não chega\",\"Pacote chega fora de ordem\",\"Janela pequena\",\"ACK válido\"],\"alvos\":[\"Retransmitir após timeout\",\"Reordenar por SEQ\",\"Enviar menos dados\",\"Continuar transmissão\"]}', '[0,1,2,3]', 'O TCP adapta o envio conforme confirmações e capacidade do receptor.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Arraste cada evento ao efeito esperado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'arrastar', 'redes', 'Arraste cada termo ao monstro que ele derrota.', NULL, '{\"itens\":[\"ACK\",\"Retransmissão\",\"Controle de fluxo\",\"Número de sequência\"],\"alvos\":[\"Dúvida de recebimento\",\"Perda de pacote\",\"Excesso no receptor\",\"Ordem embaralhada\"]}', '[0,1,2,3]', 'Cada mecanismo reduz um tipo de problema de transporte.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Arraste cada termo ao monstro que ele derrota.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'arrastar', 'redes', 'Arraste cada protocolo ao comportamento diante da perda.', NULL, '{\"itens\":[\"TCP\",\"UDP\",\"HTTP sobre TCP\",\"Aplicação em tempo real sobre UDP\"],\"alvos\":[\"Pode retransmitir\",\"Não garante retransmissão\",\"Herda confiabilidade do TCP\",\"Pode tolerar perdas pequenas\"]}', '[0,1,2,3]', 'A confiabilidade depende do protocolo usado por baixo.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Arraste cada protocolo ao comportamento diante da perda.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'arrastar', 'redes', 'Arraste cada pista do Eco do Timeout à interpretação.', NULL, '{\"itens\":[\"Sem ACK\",\"SEQ fora de ordem\",\"Janela zerada\",\"FIN recebido\"],\"alvos\":[\"Possível perda\",\"Reordenar dados\",\"Pausar envio\",\"Encerrar conexão\"]}', '[0,1,2,3]', 'Essas pistas orientam o diagnóstico da sessão TCP.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Arraste cada pista do Eco do Timeout à interpretação.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'multipla', 'redes', 'Para que serve o número de sequência do TCP?', NULL, '[\"Criptografar o pacote\",\"Remontar os pacotes na ordem correta no destino\",\"Escolher a rota\",\"Acelerar o DNS\"]', '1', 'Os números de sequência permitem reordenar pacotes que chegam fora de ordem.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Para que serve o número de sequência do TCP?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'vf', 'redes', 'O UDP não retransmite automaticamente um pacote perdido.', NULL, NULL, 'true', 'O UDP é \"dispare e esqueça\": sem confirmação nem retransmissão.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='O UDP não retransmite automaticamente um pacote perdido.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'multipla', 'redes', 'Qual comando testa conectividade enviando pacotes ICMP echo?', NULL, '[\"ping\",\"grep\",\"echo\",\"cat\"]', '0', 'ping mede se o destino responde e em quanto tempo (latência).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Qual comando testa conectividade enviando pacotes ICMP echo?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'multipla', 'redes', 'Latência alta numa rede significa:', NULL, '[\"Mais banda disponível\",\"Maior demora para um pacote ir e voltar\",\"Menos perda de pacotes\",\"IP inválido\"]', '1', 'Latência é o atraso de ida e volta (RTT); alta = resposta lenta.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Latência alta numa rede significa:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'multipla', 'redes', 'O que é um ataque DDoS?', NULL, '[\"Roubo de senha\",\"Sobrecarregar um servidor com requisições em massa\",\"Apagar o banco de dados\",\"Criptografar arquivos por resgate\"]', '1', 'DDoS inunda o alvo com tráfego para esgotar seus recursos.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='O que é um ataque DDoS?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'multipla', 'redes', 'Qual mecanismo ajuda a barrar tráfego malicioso na borda da rede?', NULL, '[\"Firewall\",\"Compilador\",\"Debugger\",\"Laço for\"]', '0', 'O firewall filtra o tráfego conforme regras de segurança.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Qual mecanismo ajuda a barrar tráfego malicioso na borda da rede?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'vf', 'redes', 'Em HTTP, o método GET busca dados e o POST normalmente envia dados ao servidor.', NULL, NULL, 'true', 'GET recupera; POST submete dados (ex.: formulários).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Em HTTP, o método GET busca dados e o POST normalmente envia dados ao servidor.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'multipla', 'redes', 'Qual a porta padrão do HTTPS?', NULL, '[\"80\",\"21\",\"443\",\"25\"]', '2', 'HTTPS usa a porta 443 (HTTP usa a 80).', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Qual a porta padrão do HTTPS?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'multipla', 'redes', 'Cliente faz uma requisição e a rota não existe. Qual status o servidor costuma retornar?', NULL, '[\"200\",\"301\",\"404\",\"500\"]', '2', 'Rota inexistente normalmente retorna 404 Not Found.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Cliente faz uma requisição e a rota não existe. Qual status o servidor costuma retornar?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'multipla', 'redes', 'DDoS, o Enxame, convoca milhares de máquinas zumbis. Qual é o objetivo principal desse ataque?', NULL, '[\"Sobrecarregar um serviço com requisições em massa\",\"Organizar cabos por cor\",\"Criar uma máscara /24\",\"Traduzir URL em IP\"]', '0', 'DDoS tenta tornar o serviço indisponível por excesso de tráfego.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='DDoS, o Enxame, convoca milhares de máquinas zumbis. Qual é o objetivo principal desse ataque?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'multipla', 'redes', 'Qual guardião filtra pacotes e bloqueia portas/protocolos não autorizados?', NULL, '[\"Firewall\",\"Compilador\",\"Switch de vídeo\",\"Editor de texto\"]', '0', 'Firewall protege a rede filtrando tráfego por regras.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Qual guardião filtra pacotes e bloqueia portas/protocolos não autorizados?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'multipla', 'redes', 'Na criptografia simétrica, quantas chaves principais são usadas para cifrar e decifrar a mensagem?', NULL, '[\"Uma mesma chave\",\"Duas chaves públicas\",\"Nenhuma chave\",\"Uma chave para cada pacote IP\"]', '0', 'Na criptografia simétrica, emissor e receptor usam a mesma chave.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Na criptografia simétrica, quantas chaves principais são usadas para cifrar e decifrar a mensagem?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'multipla', 'redes', 'Um invasor se coloca entre dois hosts e intercepta o tráfego. Qual ataque é esse?', NULL, '[\"Man-in-the-middle\",\"Loopback\",\"CIDR\",\"Full-Duplex\"]', '0', 'No man-in-the-middle, o atacante se infiltra no caminho da comunicação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Um invasor se coloca entre dois hosts e intercepta o tráfego. Qual ataque é esse?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'multipla', 'redes', 'Um servidor DNS legítimo é alterado para levar usuários a uma página falsa. Qual ataque aparece?', NULL, '[\"Sequestro de DNS / pharming\",\"Three-way handshake\",\"Broadcast local\",\"Encapsulamento OSI\"]', '0', 'No sequestro de DNS, nomes legítimos apontam para destinos maliciosos.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Um servidor DNS legítimo é alterado para levar usuários a uma página falsa. Qual ataque aparece?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'vf', 'redes', 'Uma política de segurança deve considerar ameaças ao hardware, software, informação, sistema e medidas de segurança.', NULL, NULL, 'true', 'O PDF enumera esses tipos de ameaça como parte da avaliação de segurança.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Uma política de segurança deve considerar ameaças ao hardware, software, informação, sistema e medidas de segurança.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'vf', 'redes', 'Engenharia social explora o usuário como vulnerabilidade, tentando obter confiança ou dados.', NULL, NULL, 'true', 'Esse ataque usa fraude e manipulação do usuário.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Engenharia social explora o usuário como vulnerabilidade, tentando obter confiança ou dados.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'vf', 'redes', 'Um firewall normalmente deve começar permitindo tudo e só depois bloquear o que parecer perigoso.', NULL, NULL, 'false', 'A prática descrita é bloquear o tráfego geral e criar permissões específicas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Um firewall normalmente deve começar permitindo tudo e só depois bloquear o que parecer perigoso.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'vf', 'redes', 'Criptografia torna a mensagem incompreensível para quem captura o tráfego sem a chave.', NULL, NULL, 'true', 'O objetivo é proteger o conteúdo mesmo se a comunicação for interceptada.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Criptografia torna a mensagem incompreensível para quem captura o tráfego sem a chave.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'vf', 'redes', 'Em um ataque DDoS, o servidor sempre consegue distinguir com facilidade requisições legítimas das maliciosas.', NULL, NULL, 'false', 'No DDoS, as requisições vêm de muitos pontos, dificultando a distinção.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Em um ataque DDoS, o servidor sempre consegue distinguir com facilidade requisições legítimas das maliciosas.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'completar', 'redes', 'Complete: o ataque distribuído de negação de serviço é conhecido pela sigla _____.', NULL, NULL, '[\"DDoS\",\"ddos\"]', 'DDoS significa Distributed Denial of Service.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Complete: o ataque distribuído de negação de serviço é conhecido pela sigla _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'completar', 'redes', 'O dispositivo ou software que filtra pacotes e portas chama-se _____.', NULL, NULL, '[\"firewall\",\"Firewall\"]', 'Firewall aplica regras para permitir ou bloquear tráfego.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='O dispositivo ou software que filtra pacotes e portas chama-se _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'completar', 'redes', 'A escrita secreta usada para proteger mensagens é a _____.', NULL, NULL, '[\"criptografia\",\"Criptografia\"]', 'Criptografia transforma texto simples em mensagem cifrada.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='A escrita secreta usada para proteger mensagens é a _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'completar', 'redes', 'Na criptografia assimétrica, a chave que pode ser divulgada é a chave _____.', NULL, NULL, '[\"pública\",\"publica\",\"Pública\"]', 'A chave pública pode ser compartilhada; a privada deve permanecer protegida.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Na criptografia assimétrica, a chave que pode ser divulgada é a chave _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'completar', 'redes', 'A manipulação do usuário para obter acesso indevido chama-se engenharia _____.', NULL, NULL, '[\"social\",\"Social\"]', 'Engenharia social ataca o fator humano da segurança.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='A manipulação do usuário para obter acesso indevido chama-se engenharia _____.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'ordenar', 'redes', 'Ordene a criação de uma regra segura de firewall.', NULL, '[\"Bloquear tráfego geral\",\"Definir porta e protocolo permitidos\",\"Liberar apenas serviços necessários\",\"Negar o que não casar com regras\"]', '[0,1,2,3]', 'A lógica defensiva começa negando por padrão e liberando exceções controladas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Ordene a criação de uma regra segura de firewall.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'ordenar', 'redes', 'Ordene a narrativa de um DDoS na torre.', NULL, '[\"Atacante infecta máquinas\",\"Máquinas zumbis disparam requisições\",\"Servidor fica sobrecarregado\",\"Serviço pode ficar indisponível\"]', '[0,1,2,3]', 'O DDoS distribui o ataque por múltiplas fontes.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Ordene a narrativa de um DDoS na torre.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'ordenar', 'redes', 'Ordene o uso básico de criptografia assimétrica.', NULL, '[\"Receptor cria par de chaves\",\"Emissor obtém chave pública\",\"Emissor cifra a mensagem\",\"Receptor decifra com chave privada\"]', '[0,1,2,3]', 'Na criptografia assimétrica, a pública cifra para o destinatário e a privada decifra.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Ordene o uso básico de criptografia assimétrica.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'ordenar', 'redes', 'Ordene o ataque man-in-the-middle.', NULL, '[\"Atacante se posiciona no caminho\",\"Hosts acreditam usar rota legítima\",\"Tráfego passa pelo atacante\",\"Dados podem ser capturados\"]', '[0,1,2,3]', 'O atacante intercepta a comunicação entre as partes.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Ordene o ataque man-in-the-middle.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'ordenar', 'redes', 'Ordene o sequestro de DNS.', NULL, '[\"Atacante altera entrada DNS\",\"Usuário tenta acessar site legítimo\",\"DNS aponta para página falsa\",\"Usuário pode entregar dados sigilosos\"]', '[0,1,2,3]', 'O DNS adulterado redireciona o usuário a um destino malicioso.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Ordene o sequestro de DNS.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'erro', 'redes', 'Qual defesa está mal configurada?', NULL, '[\"Manter sistema atualizado\",\"Treinar usuários contra engenharia social\",\"Criar firewall com bloqueio padrão e permissões específicas\",\"Liberar todas as portas para facilitar acesso externo\"]', '3', 'Liberar todas as portas aumenta a superfície de ataque.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Qual defesa está mal configurada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'erro', 'redes', 'O mago diz: “Na criptografia simétrica, a chave privada fica secreta e a pública é distribuída”. Qual é a falha?', NULL, '[\"Isso descreve criptografia assimétrica, não simétrica\",\"Criptografia não usa chaves\",\"Chave pública é sempre senha de Wi-Fi\",\"Simétrica exige três chaves\"]', '0', 'Simétrica usa a mesma chave para cifrar e decifrar.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='O mago diz: “Na criptografia simétrica, a chave privada fica secreta e a pública é distribuída”. Qual é a falha?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'erro', 'redes', 'Qual afirmação sobre DDoS está errada?', NULL, '[\"Usa muitas fontes para gerar tráfego\",\"Pode envolver computadores zumbis\",\"Busca indisponibilizar serviço\",\"É apenas uma consulta DNS comum e inofensiva\"]', '3', 'DDoS é ataque de negação de serviço distribuída.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Qual afirmação sobre DDoS está errada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'erro', 'redes', 'O diário de segurança diz que engenharia social é sempre exploração de falha elétrica no cabo. Qual correção é adequada?', NULL, '[\"Engenharia social explora o comportamento do usuário\",\"Engenharia social é máscara /23\",\"Engenharia social é porta 80\",\"Engenharia social é camada Física\"]', '0', 'Engenharia social usa manipulação e fraude contra pessoas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='O diário de segurança diz que engenharia social é sempre exploração de falha elétrica no cabo. Qual correção é adequada?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'erro', 'redes', 'O atacante capturou tráfego em HTTP sem proteção. Qual escolha reduziria a leitura do conteúdo por terceiros?', NULL, '[\"Usar HTTPS/criptografia\",\"Remover DNS\",\"Trocar o IP por 127.0.0.1 em produção\",\"Desligar a camada de Transporte\"]', '0', 'Criptografia protege o conteúdo contra leitura por interceptadores.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='O atacante capturou tráfego em HTTP sem proteção. Qual escolha reduziria a leitura do conteúdo por terceiros?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'arrastar', 'redes', 'Arraste cada ameaça ao tipo correto de proteção.', NULL, '{\"itens\":[\"Vírus em terminal\",\"Porta exposta\",\"Mensagem interceptada\",\"Usuário enganado por e-mail falso\"],\"alvos\":[\"Antivírus/atualizações\",\"Firewall\",\"Criptografia\",\"Treinamento contra engenharia social\"]}', '[0,1,2,3]', 'Segurança combina ferramentas técnicas e políticas de usuário.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Arraste cada ameaça ao tipo correto de proteção.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'arrastar', 'redes', 'Arraste cada ataque ao efeito típico.', NULL, '{\"itens\":[\"DDoS\",\"Man-in-the-middle\",\"Sequestro de DNS\",\"Engenharia social\"],\"alvos\":[\"Indisponibilidade por excesso de tráfego\",\"Interceptação da comunicação\",\"Redirecionamento para página falsa\",\"Manipulação do usuário\"]}', '[0,1,2,3]', 'Cada ataque explora uma fraqueza diferente.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Arraste cada ataque ao efeito típico.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'arrastar', 'redes', 'Arraste cada conceito de criptografia ao significado.', NULL, '{\"itens\":[\"Texto simples\",\"Mensagem cifrada\",\"Chave pública\",\"Chave privada\"],\"alvos\":[\"Conteúdo original\",\"Conteúdo criptografado\",\"Pode ser compartilhada\",\"Deve ficar protegida\"]}', '[0,1,2,3]', 'A segurança depende de proteger chaves e transformar a mensagem.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Arraste cada conceito de criptografia ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'arrastar', 'redes', 'Arraste cada zona da fortaleza ao papel.', NULL, '{\"itens\":[\"Internet\",\"DMZ\",\"Intranet\",\"Firewall\"],\"alvos\":[\"Rede externa\",\"Zona intermediária\",\"Rede interna protegida\",\"Filtro de tráfego\"]}', '[0,1,2,3]', 'A DMZ isola serviços expostos da rede interna.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Arraste cada zona da fortaleza ao papel.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'arrastar', 'redes', 'Arraste cada etapa ao plano de segurança.', NULL, '{\"itens\":[\"Avaliar ameaças\",\"Identificar bens críticos\",\"Escolher controles\",\"Treinar usuários\"],\"alvos\":[\"Entender riscos\",\"Saber o que proteger\",\"Aplicar firewall/cripto/antivírus\",\"Reduzir engenharia social\"]}', '[0,1,2,3]', 'Um plano de segurança começa pelo risco e combina controles técnicos e humanos.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Arraste cada etapa ao plano de segurança.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'multipla', 'redes', 'Num ataque DDoS, o tráfego malicioso costuma vir de:', NULL, '[\"Uma única máquina\",\"Muitas máquinas distribuídas (uma botnet)\",\"O próprio servidor\",\"O cabo de rede\"]', '1', 'O \"D\" extra é de Distributed: milhares de fontes inundam o alvo ao mesmo tempo.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Num ataque DDoS, o tráfego malicioso costuma vir de:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'multipla', 'redes', 'Qual é a porta padrão do HTTP (sem o S)?', NULL, '[\"21\",\"80\",\"443\",\"8080\"]', '1', 'HTTP usa a porta 80; HTTPS usa a 443.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Qual é a porta padrão do HTTP (sem o S)?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'multipla', 'redes', 'Qual é a porta padrão do SSH?', NULL, '[\"21\",\"22\",\"23\",\"25\"]', '1', 'SSH usa a porta 22 (21 é FTP, 23 é Telnet, 25 é SMTP).', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Qual é a porta padrão do SSH?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'vf', 'redes', 'Um firewall pode bloquear tráfego com base em portas, IPs e protocolos.', NULL, NULL, 'true', 'O firewall aplica regras de filtragem para permitir ou barrar o tráfego.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Um firewall pode bloquear tráfego com base em portas, IPs e protocolos.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'erro', 'redes', 'Qual destas NÃO é uma defesa contra DDoS?', NULL, '[\"Rate limiting (limitar requisições por IP)\",\"Filtros e firewall na borda\",\"Publicar a senha de admin do servidor\",\"Usar uma CDN para absorver o tráfego\"]', '2', 'Publicar credenciais é entregar o reino ao inimigo — nada a ver com mitigar DDoS.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Qual destas NÃO é uma defesa contra DDoS?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'multipla', 'redes', 'A criptografia ponta-a-ponta garante que:', NULL, '[\"A rede fica mais rápida\",\"Só o remetente e o destinatário conseguem ler o conteúdo\",\"Os pacotes nunca se perdem\",\"O IP fica oculto para sempre\"]', '1', 'Apenas as pontas têm as chaves; intermediários veem só dados cifrados.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='A criptografia ponta-a-ponta garante que:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m),
       'multipla', 'mvc', 'No padrão MVC, quem NÃO deve conter comandos SQL?', NULL, '[\"Model\",\"View\",\"Controller\",\"Repositório\"]', '1', 'A View apenas apresenta; SQL pertence ao Model.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='No padrão MVC, quem NÃO deve conter comandos SQL?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m),
       'multipla', 'poo', 'Qual palavra-chave realiza herança em PHP?', NULL, '[\"extends\",\"implements\",\"new\",\"this\"]', '0', 'extends estabelece a herança entre classes.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='Qual palavra-chave realiza herança em PHP?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m),
       'multipla', 'estruturas', 'A busca binária exige que o vetor esteja:', NULL, '[\"embaralhado\",\"ordenado\",\"vazio\",\"circular\"]', '1', 'Só é possível dividir pela metade se os dados estiverem ordenados.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='A busca binária exige que o vetor esteja:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m),
       'multipla', 'redes', 'Qual protocolo é confiável e ordenado?', NULL, '[\"UDP\",\"TCP\",\"ICMP\",\"DNS\"]', '1', 'O TCP garante entrega confiável e ordenada.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='Qual protocolo é confiável e ordenado?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m),
       'vf', 'logica', 'Um algoritmo recursivo precisa de um caso base para terminar.', NULL, NULL, 'true', 'Sem caso base, a recursão é infinita.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='Um algoritmo recursivo precisa de um caso base para terminar.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m),
       'erro', 'php', 'Segfault deixou um último bug. Qual é a falha?', 'function dobro($n) {\n  return $n * 2\n}', '[\"Nome inválido\",\"Falta ponto e vírgula após $n * 2\",\"Falta parâmetro\",\"Não há erro\"]', '1', 'A instrução return precisa terminar com ponto e vírgula.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='Segfault deixou um último bug. Qual é a falha?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m),
       'multipla', 'mvc', 'Num bom design MVC, o que a View deve receber do Controller?', NULL, '[\"Conexões abertas com o banco\",\"Apenas os dados já prontos para exibir\",\"As queries SQL para executar\",\"As regras de negócio\"]', '1', 'A View só apresenta: recebe dados prontos, sem SQL nem lógica de negócio.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='Num bom design MVC, o que a View deve receber do Controller?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m),
       'multipla', 'poo', 'O encapsulamento serve principalmente para:', NULL, '[\"Deixar todos os atributos públicos\",\"Proteger o estado interno, expondo só o necessário\",\"Eliminar métodos\",\"Acelerar o banco\"]', '1', 'Esconder o estado e expor uma interface controlada é a essência do encapsulamento.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='O encapsulamento serve principalmente para:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m),
       'multipla', 'estruturas', 'Qual estrutura segue o princípio LIFO (último a entrar, primeiro a sair)?', NULL, '[\"Fila\",\"Pilha\",\"Lista ordenada\",\"Árvore balanceada\"]', '1', 'A pilha é LIFO; a fila é FIFO.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='Qual estrutura segue o princípio LIFO (último a entrar, primeiro a sair)?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m),
       'multipla', 'redes', 'O código de status HTTP 404 significa:', NULL, '[\"Sucesso\",\"Recurso não encontrado\",\"Erro interno do servidor\",\"Acesso proibido\"]', '1', '404 Not Found: o recurso pedido não existe naquele endereço.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='O código de status HTTP 404 significa:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m),
       'multipla', 'calculo', 'A derivada de f(x) = x² é:', NULL, '[\"x\",\"2x\",\"2\",\"x³\"]', '1', 'Regra do tombo: derivada de x² é 2x — a taxa de variação que cresce com x.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='A derivada de f(x) = x² é:') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m),
       'vf', 'logica', 'Todo algoritmo deve terminar após um número finito de passos.', NULL, NULL, 'true', 'Finitude é parte da definição de algoritmo; do contrário, é um laço infinito disfarçado.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='Todo algoritmo deve terminar após um número finito de passos.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m),
       'erro', 'php', 'O último bug do Lorde Segfault. Qual é a falha?', 'if ($x == 5) {\n  echo \"cinco\"\n}', '[\"Falta o ; após echo \\\"cinco\\\"\",\"Deveria ser = em vez de ==\",\"Falta o $ em x\",\"Não há erro\"]', '0', 'A instrução echo precisa terminar com ponto e vírgula — o clássico que dá Parse Error.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='O último bug do Lorde Segfault. Qual é a falha?') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m),
       'multipla', 'sql', 'Qual cláusula AGRUPA linhas para usar com funções agregadas como COUNT()?', NULL, '[\"ORDER BY\",\"GROUP BY\",\"WHERE\",\"LIMIT\"]', '1', 'GROUP BY junta linhas por um critério para então contar, somar ou calcular médias por grupo.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='Qual cláusula AGRUPA linhas para usar com funções agregadas como COUNT()?') AS _chk);
