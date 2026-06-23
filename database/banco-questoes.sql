-- ============================================================================
-- Banco de questoes ampliado (anti-repeticao) — IMPORT IDEMPOTENTE
-- Gerado a partir de database/banco-questoes/*.php (NAO editar a mao).
-- Regerar: php tools/... (ver seed-banco-questoes.php) — fonte de verdade e o PHP.
--
-- Seguro em banco com jogadores: so INSERE perguntas que ainda nao existem
-- (chave logica: fase_id + texto da pergunta). Rodar de novo NAO duplica.
--
-- Uso:  mariadb -h HOST -u USUARIO -p --ssl=0 BANCO < database/banco-questoes.sql
-- ============================================================================

-- Garante o assunto 'calculo' no ENUM (idempotente).
ALTER TABLE desafios
  MODIFY assunto ENUM('php','mvc','sql','poo','estruturas','redes','logica','calculo') NOT NULL;

-- ---------- calculo (24 perguntas) ----------
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m), 'multipla', 'calculo', 'O limite da sequência aₙ = 1/n quando n tende ao infinito é:', NULL, '[\"1\",\"0\",\"infinito\",\"n\"]', '1', 'Quanto maior o n, menor 1/n; o termo se aproxima de 0.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='O limite da sequência aₙ = 1/n quando n tende ao infinito é:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m), 'multipla', 'calculo', 'A sequência aₙ = (n + 1)/n, quando n tende ao infinito, tende a:', NULL, '[\"0\",\"1\",\"infinito\",\"2\"]', '1', '(n+1)/n = 1 + 1/n; como 1/n → 0, a sequência tende a 1.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='A sequência aₙ = (n + 1)/n, quando n tende ao infinito, tende a:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m), 'vf', 'calculo', 'Numa progressão aritmética, a diferença entre termos consecutivos é constante.', NULL, NULL, 'true', 'Essa diferença constante é a razão da PA.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Numa progressão aritmética, a diferença entre termos consecutivos é constante.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m), 'multipla', 'calculo', 'Qual sequência CONVERGE (tem limite finito)?', NULL, '[\"1, 2, 3, 4, ...\",\"2, 4, 8, 16, ...\",\"1, 1\\/2, 1\\/3, 1\\/4, ...\",\"1, 2, 4, 8, ...\"]', '2', '1/n tende a 0 (converge); as demais crescem sem limite.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Qual sequência CONVERGE (tem limite finito)?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m), 'completar', 'calculo', 'Numa PA de primeiro termo 3 e razão 5, o termo geral é aₙ = 3 + (n - 1)·___', NULL, NULL, '[\"5\"]', 'O termo geral da PA é a₁ + (n−1)·r, com r = 5.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Numa PA de primeiro termo 3 e razão 5, o termo geral é aₙ = 3 + (n - 1)·___') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m), 'multipla', 'calculo', 'A soma infinita 1 + 1/2 + 1/4 + 1/8 + ... converge para:', NULL, '[\"1\",\"2\",\"infinito\",\"1\\/2\"]', '1', 'Série geométrica de razão 1/2: a soma é 1/(1 − 1/2) = 2.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='A soma infinita 1 + 1/2 + 1/4 + 1/8 + ... converge para:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m), 'multipla', 'calculo', 'Quanto vale o limite, quando x tende a 2, de (x² − 4)/(x − 2)?', NULL, '[\"0\",\"2\",\"4\",\"indefinido\"]', '2', 'x² − 4 = (x − 2)(x + 2); cancelando (x − 2) sobra x + 2, que em x=2 vale 4.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Quanto vale o limite, quando x tende a 2, de (x² − 4)/(x − 2)?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m), 'multipla', 'calculo', 'Uma função é CONTÍNUA num ponto quando:', NULL, '[\"O gráfico tem um salto ali\",\"O limite no ponto existe e é igual ao valor da função\",\"A função não está definida ali\",\"A derivada é zero\"]', '1', 'Continuidade: o limite existe, a função existe e os dois coincidem no ponto.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Uma função é CONTÍNUA num ponto quando:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m), 'vf', 'calculo', 'O limite de (sen x)/x quando x tende a 0 é igual a 1.', NULL, NULL, 'true', 'É o limite fundamental trigonométrico, base de várias derivadas.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='O limite de (sen x)/x quando x tende a 0 é igual a 1.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m), 'multipla', 'calculo', 'O limite de 1/x² quando x tende ao infinito é:', NULL, '[\"infinito\",\"1\",\"0\",\"−1\"]', '2', 'O denominador cresce sem limite, então a fração tende a 0.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='O limite de 1/x² quando x tende ao infinito é:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m), 'multipla', 'calculo', 'Encontrar 0/0 ao calcular um limite significa que:', NULL, '[\"O limite é sempre 0\",\"É uma indeterminação: é preciso manipular (fatorar\\/simplificar) a expressão\",\"O limite não existe nunca\",\"A função é contínua\"]', '1', '0/0 é indeterminação; fatorar, simplificar ou usar L\'Hôpital costuma resolver.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Encontrar 0/0 ao calcular um limite significa que:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m), 'completar', 'calculo', 'Calcule o limite quando x tende a 3 de (x + 1) = ___', NULL, NULL, '[\"4\"]', 'A função é contínua: basta substituir x por 3 → 3 + 1 = 4.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Calcule o limite quando x tende a 3 de (x + 1) = ___') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m), 'multipla', 'calculo', 'A derivada de uma função mede:', NULL, '[\"A área sob a curva\",\"A taxa de variação instantânea da função\",\"O valor máximo da função\",\"O número de raízes\"]', '1', 'A derivada é a inclinação da reta tangente: a rapidez com que a função muda naquele ponto.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='A derivada de uma função mede:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m), 'multipla', 'calculo', 'A derivada de f(x) = x² é:', NULL, '[\"x\",\"2x\",\"x²\",\"2\"]', '1', 'Pela regra do tombo, derivada de xⁿ é n·xⁿ⁻¹: aqui 2·x¹ = 2x.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='A derivada de f(x) = x² é:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m), 'multipla', 'calculo', 'A derivada de uma função constante f(x) = 7 é:', NULL, '[\"7\",\"1\",\"0\",\"x\"]', '2', 'Uma constante não varia, então sua taxa de variação (derivada) é 0.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='A derivada de uma função constante f(x) = 7 é:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m), 'vf', 'calculo', 'Se f\'(x) > 0 em todo um intervalo, então f é crescente nesse intervalo.', NULL, NULL, 'true', 'Derivada positiva = inclinação para cima = função crescente.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Se f\'(x) > 0 em todo um intervalo, então f é crescente nesse intervalo.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m), 'multipla', 'calculo', 'A derivada de f(x) = 3x é:', NULL, '[\"3x\",\"3\",\"x\",\"0\"]', '1', 'A derivada de uma reta a·x é a inclinação a; aqui, 3.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='A derivada de f(x) = 3x é:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m), 'completar', 'calculo', 'Pela regra do tombo, a derivada de x³ é ___·x²', NULL, NULL, '[\"3\"]', 'Derivada de xⁿ = n·xⁿ⁻¹; para n=3 dá 3x².', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Pela regra do tombo, a derivada de x³ é ___·x²') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m), 'multipla', 'calculo', 'A integração é a operação inversa da:', NULL, '[\"Soma\",\"Derivação\",\"Raiz quadrada\",\"Potenciação\"]', '1', 'Integral e derivada são operações inversas (Teorema Fundamental do Cálculo).', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='A integração é a operação inversa da:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m), 'multipla', 'calculo', 'Quanto vale a integral indefinida ∫ 2x dx?', NULL, '[\"2 + C\",\"x² + C\",\"2x² + C\",\"x + C\"]', '1', 'A primitiva de 2x é x² (pois a derivada de x² é 2x), mais a constante C.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='Quanto vale a integral indefinida ∫ 2x dx?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m), 'multipla', 'calculo', 'Na derivada PARCIAL em x de f(x, y), a variável y é tratada como:', NULL, '[\"Variável\",\"Zero\",\"Constante\",\"Infinito\"]', '2', 'Deriva-se em relação a x mantendo y fixo (constante) — base do cálculo multivariável.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='Na derivada PARCIAL em x de f(x, y), a variável y é tratada como:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m), 'vf', 'calculo', 'A integral definida pode ser interpretada como a área sob a curva da função.', NULL, NULL, 'true', 'A integral definida soma \"fatias infinitesimais\", resultando na área entre a curva e o eixo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='A integral definida pode ser interpretada como a área sob a curva da função.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m), 'multipla', 'calculo', 'A derivada parcial ∂/∂x de f(x, y) = x²·y é:', NULL, '[\"x²\",\"2xy\",\"2x\",\"y\"]', '1', 'Tratando y como constante: ∂/∂x (x²·y) = 2x·y = 2xy.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='A derivada parcial ∂/∂x de f(x, y) = x²·y é:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 27, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=27) AS _m), 'completar', 'calculo', 'A constante C somada numa integral indefinida é a constante de ___.', NULL, NULL, '[\"integração\",\"integracao\"]', 'Como a derivada de qualquer constante é 0, toda primitiva carrega o \"+ C\".', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=27 AND pergunta='A constante C somada numa integral indefinida é a constante de ___.') AS _chk);

-- ---------- estruturas (28 perguntas) ----------
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m), 'multipla', 'estruturas', 'Qual operação OLHA o elemento do topo da pilha sem removê-lo?', NULL, '[\"pop\",\"push\",\"peek (top)\",\"enqueue\"]', '2', 'peek (ou top) consulta o topo sem desempilhar.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Qual operação OLHA o elemento do topo da pilha sem removê-lo?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m), 'multipla', 'estruturas', 'Numa fila, qual operação INSERE um elemento no fim?', NULL, '[\"dequeue\",\"enqueue\",\"pop\",\"peek\"]', '1', 'enqueue adiciona ao fim da fila; dequeue remove da frente.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Numa fila, qual operação INSERE um elemento no fim?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m), 'vf', 'estruturas', 'A pilha de chamadas (call stack) de um programa funciona como uma pilha LIFO.', NULL, NULL, 'true', 'A última função chamada é a primeira a retornar: puro LIFO.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='A pilha de chamadas (call stack) de um programa funciona como uma pilha LIFO.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m), 'ordenar', 'estruturas', 'Enfileirei A, depois B, depois C. Em que ordem eles SAEM da fila?', NULL, '[\"A\",\"B\",\"C\"]', '[0,1,2]', 'Fila é FIFO: o primeiro a entrar (A) é o primeiro a sair.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Enfileirei A, depois B, depois C. Em que ordem eles SAEM da fila?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m), 'completar', 'estruturas', 'Complete a operação que adiciona um elemento ao topo da pilha:', 'pilha.____(valor);', NULL, '[\"push\"]', 'push empilha um novo elemento no topo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Complete a operação que adiciona um elemento ao topo da pilha:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m), 'multipla', 'estruturas', 'O recurso \"desfazer\" (undo) de um editor é melhor modelado por:', NULL, '[\"Uma fila\",\"Uma pilha\",\"Uma árvore\",\"Um grafo\"]', '1', 'Desfaz-se a última ação primeiro — comportamento LIFO de pilha.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='O recurso \"desfazer\" (undo) de um editor é melhor modelado por:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m), 'multipla', 'estruturas', 'Numa lista DUPLAMENTE encadeada, cada nó aponta para:', NULL, '[\"Só o próximo\",\"O anterior e o próximo\",\"A cabeça e a cauda\",\"Nenhum nó\"]', '1', 'Dois ponteiros por nó (anterior e próximo) permitem percorrer nos dois sentidos.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Numa lista DUPLAMENTE encadeada, cada nó aponta para:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m), 'vf', 'estruturas', 'Numa lista encadeada, os elementos NÃO precisam ficar contíguos na memória.', NULL, NULL, 'true', 'Cada nó guarda um ponteiro para o próximo; eles podem estar espalhados na memória.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Numa lista encadeada, os elementos NÃO precisam ficar contíguos na memória.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m), 'multipla', 'estruturas', 'Remover o PRIMEIRO nó de uma lista encadeada custa, no pior caso:', NULL, '[\"O(1)\",\"O(n)\",\"O(log n)\",\"O(n²)\"]', '0', 'Basta mover a cabeça para o segundo nó: tempo constante.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Remover o PRIMEIRO nó de uma lista encadeada custa, no pior caso:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m), 'completar', 'estruturas', 'O ponteiro para o primeiro nó de uma lista costuma se chamar ___ (em inglês).', NULL, NULL, '[\"head\"]', 'head aponta para o início; o último nó (tail) aponta para nulo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='O ponteiro para o primeiro nó de uma lista costuma se chamar ___ (em inglês).') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m), 'multipla', 'estruturas', 'Qual a vantagem da lista encadeada sobre o vetor (array) fixo?', NULL, '[\"Acesso por índice em O(1)\",\"Inserir\\/remover no meio sem deslocar os outros elementos\",\"Ocupa menos memória por elemento\",\"Permite busca binária direta\"]', '1', 'Inserir/remover só reajusta ponteiros; no array seria preciso deslocar elementos.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Qual a vantagem da lista encadeada sobre o vetor (array) fixo?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m), 'multipla', 'estruturas', 'Buscar um valor numa lista encadeada NÃO ordenada custa, no pior caso:', NULL, '[\"O(1)\",\"O(log n)\",\"O(n)\",\"O(0)\"]', '2', 'Pode ser preciso percorrer nó a nó até o fim: linear, O(n).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Buscar um valor numa lista encadeada NÃO ordenada custa, no pior caso:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m), 'multipla', 'estruturas', 'Numa árvore BINÁRIA, cada nó tem no máximo quantos filhos?', NULL, '[\"1\",\"2\",\"3\",\"ilimitado\"]', '1', 'Binária = no máximo dois filhos por nó (esquerdo e direito).', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Numa árvore BINÁRIA, cada nó tem no máximo quantos filhos?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m), 'multipla', 'estruturas', 'Numa Árvore Binária de Busca (BST), valores MENORES que o nó vão para:', NULL, '[\"A subárvore direita\",\"A subárvore esquerda\",\"A raiz\",\"Fora da árvore\"]', '1', 'Por convenção, menores à esquerda e maiores à direita — é o que torna a busca rápida.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Numa Árvore Binária de Busca (BST), valores MENORES que o nó vão para:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m), 'vf', 'estruturas', 'Numa árvore de busca BALANCEADA, a altura cresce na ordem de log n.', NULL, NULL, 'true', 'Por isso buscas, inserções e remoções saem em O(log n) quando a árvore está equilibrada.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Numa árvore de busca BALANCEADA, a altura cresce na ordem de log n.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m), 'multipla', 'estruturas', 'Qual notação descreve o LIMITE SUPERIOR (pior caso) de um algoritmo?', NULL, '[\"Ômega (Ω)\",\"Big-O (O)\",\"Teta exato (Θ)\",\"Nenhuma\"]', '1', 'Big-O dá o teto de crescimento — quão ruim pode ficar conforme n cresce.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Qual notação descreve o LIMITE SUPERIOR (pior caso) de um algoritmo?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m), 'ordenar', 'estruturas', 'Ordene as complexidades do MELHOR (mais rápido) ao PIOR:', NULL, '[\"O(n log n)\",\"O(1)\",\"O(n)\",\"O(log n)\"]', '[1,3,2,0]', 'O(1) < O(log n) < O(n) < O(n log n) em ordem de crescimento.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Ordene as complexidades do MELHOR (mais rápido) ao PIOR:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m), 'completar', 'estruturas', 'A busca binária descarta, a cada passo, ___ do espaço de busca.', NULL, NULL, '[\"metade\",\"a metade\"]', 'Comparou com o meio e descartou metade — por isso o custo é O(log n).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='A busca binária descarta, a cada passo, ___ do espaço de busca.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m), 'multipla', 'estruturas', 'Por que ordenar um vetor antes de fazer MUITAS buscas costuma compensar?', NULL, '[\"Ordenar deixa a memória menor\",\"Ordena-se uma vez e depois usa-se busca binária O(log n) muitas vezes\",\"Busca em vetor ordenado é O(1) sempre\",\"Não compensa nunca\"]', '1', 'O custo único da ordenação se dilui em muitas buscas logarítmicas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Por que ordenar um vetor antes de fazer MUITAS buscas costuma compensar?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m), 'vf', 'estruturas', 'Uma tabela hash bem dimensionada permite busca em tempo MÉDIO O(1).', NULL, NULL, 'true', 'A função de hash leva direto ao \"balde\"; em média, acesso constante.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Uma tabela hash bem dimensionada permite busca em tempo MÉDIO O(1).') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m), 'multipla', 'estruturas', 'Num vetor ORDENADO de 1.000.000 itens, a busca binária faz no máximo cerca de:', NULL, '[\"1.000.000 comparações\",\"500.000 comparações\",\"20 comparações\",\"1 comparação\"]', '2', 'log2(1.000.000) ≈ 20: dobrar pela metade vinte vezes basta.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Num vetor ORDENADO de 1.000.000 itens, a busca binária faz no máximo cerca de:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m), 'completar', 'estruturas', 'A estrutura chave→valor com acesso médio O(1) é a tabela ___.', NULL, NULL, '[\"hash\"]', 'A tabela hash mapeia chaves a posições via função de hash.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='A estrutura chave→valor com acesso médio O(1) é a tabela ___.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m), 'multipla', 'estruturas', 'O que acontece na pilha de execução a cada chamada recursiva?', NULL, '[\"Nada\",\"Empilha-se um novo registro de ativação (frame)\",\"A pilha é esvaziada\",\"O programa encerra\"]', '1', 'Cada chamada empilha um frame; ao retornar, ele é desempilhado.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='O que acontece na pilha de execução a cada chamada recursiva?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m), 'multipla', 'estruturas', 'Um \"stack overflow\" numa recursão acontece tipicamente quando:', NULL, '[\"Há um caso base correto\",\"Não há caso base, ou ele nunca é atingido\",\"A função retorna cedo demais\",\"Há poucos parâmetros\"]', '1', 'Sem parada, a pilha cresce sem fim até estourar a memória reservada.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Um \"stack overflow\" numa recursão acontece tipicamente quando:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m), 'vf', 'estruturas', 'Toda recursão pode, em princípio, ser reescrita como um laço (iteração).', NULL, NULL, 'true', 'Recursão e iteração têm o mesmo poder; às vezes a versão iterativa usa uma pilha explícita.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Toda recursão pode, em princípio, ser reescrita como um laço (iteração).') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m), 'multipla', 'estruturas', 'Com fib(n) = fib(n-1) + fib(n-2), fib(0)=0 e fib(1)=1, quanto vale fib(5)?', NULL, '[\"3\",\"5\",\"8\",\"13\"]', '1', 'Sequência: 0, 1, 1, 2, 3, 5 — fib(5) = 5.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Com fib(n) = fib(n-1) + fib(n-2), fib(0)=0 e fib(1)=1, quanto vale fib(5)?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m), 'erro', 'estruturas', 'Por que esta recursão estoura a pilha?', 'function conta($n) {\n  return conta($n + 1);\n}', '[\"Usa soma\",\"Não tem caso base e n só cresce, nunca parando\",\"Falta um parâmetro\",\"Não há erro\"]', '1', 'Sem condição de parada e com n sempre crescendo, ela se chama para sempre.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Por que esta recursão estoura a pilha?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m), 'ordenar', 'estruturas', 'fatorial(3) com base fatorial(0)=1. Ordene os RETORNOS, da base ao topo:', NULL, '[\"fatorial(2) retorna 2\",\"fatorial(0) retorna 1\",\"fatorial(1) retorna 1\",\"fatorial(3) retorna 6\"]', '[1,2,0,3]', 'A base resolve primeiro: 1 → 1 → 2 → 6, subindo a pilha de volta.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='fatorial(3) com base fatorial(0)=1. Ordene os RETORNOS, da base ao topo:') AS _chk);

-- ---------- final (8 perguntas) ----------
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m), 'multipla', 'mvc', 'Num bom design MVC, o que a View deve receber do Controller?', NULL, '[\"Conexões abertas com o banco\",\"Apenas os dados já prontos para exibir\",\"As queries SQL para executar\",\"As regras de negócio\"]', '1', 'A View só apresenta: recebe dados prontos, sem SQL nem lógica de negócio.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='Num bom design MVC, o que a View deve receber do Controller?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m), 'multipla', 'poo', 'O encapsulamento serve principalmente para:', NULL, '[\"Deixar todos os atributos públicos\",\"Proteger o estado interno, expondo só o necessário\",\"Eliminar métodos\",\"Acelerar o banco\"]', '1', 'Esconder o estado e expor uma interface controlada é a essência do encapsulamento.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='O encapsulamento serve principalmente para:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m), 'multipla', 'estruturas', 'Qual estrutura segue o princípio LIFO (último a entrar, primeiro a sair)?', NULL, '[\"Fila\",\"Pilha\",\"Lista ordenada\",\"Árvore balanceada\"]', '1', 'A pilha é LIFO; a fila é FIFO.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='Qual estrutura segue o princípio LIFO (último a entrar, primeiro a sair)?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m), 'multipla', 'redes', 'O código de status HTTP 404 significa:', NULL, '[\"Sucesso\",\"Recurso não encontrado\",\"Erro interno do servidor\",\"Acesso proibido\"]', '1', '404 Not Found: o recurso pedido não existe naquele endereço.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='O código de status HTTP 404 significa:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m), 'multipla', 'calculo', 'A derivada de f(x) = x² é:', NULL, '[\"x\",\"2x\",\"2\",\"x³\"]', '1', 'Regra do tombo: derivada de x² é 2x — a taxa de variação que cresce com x.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='A derivada de f(x) = x² é:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m), 'vf', 'logica', 'Todo algoritmo deve terminar após um número finito de passos.', NULL, NULL, 'true', 'Finitude é parte da definição de algoritmo; do contrário, é um laço infinito disfarçado.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='Todo algoritmo deve terminar após um número finito de passos.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m), 'erro', 'php', 'O último bug do Lorde Segfault. Qual é a falha?', 'if ($x == 5) {\n  echo \"cinco\"\n}', '[\"Falta o ; após echo \\\"cinco\\\"\",\"Deveria ser = em vez de ==\",\"Falta o $ em x\",\"Não há erro\"]', '0', 'A instrução echo precisa terminar com ponto e vírgula — o clássico que dá Parse Error.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='O último bug do Lorde Segfault. Qual é a falha?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 35, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=35) AS _m), 'multipla', 'sql', 'Qual cláusula AGRUPA linhas para usar com funções agregadas como COUNT()?', NULL, '[\"ORDER BY\",\"GROUP BY\",\"WHERE\",\"LIMIT\"]', '1', 'GROUP BY junta linhas por um critério para então contar, somar ou calcular médias por grupo.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=35 AND pergunta='Qual cláusula AGRUPA linhas para usar com funções agregadas como COUNT()?') AS _chk);

-- ---------- logica (13 perguntas) ----------
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m), 'multipla', 'logica', 'Quanto vale 10 - 2 * 3?', NULL, '[\"24\",\"4\",\"18\",\"6\"]', '1', 'A multiplicação vem antes: 2*3 = 6, depois 10 - 6 = 4.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='Quanto vale 10 - 2 * 3?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m), 'multipla', 'logica', 'O que é um algoritmo?', NULL, '[\"Um tipo de computador\",\"Uma sequência finita de passos para resolver um problema\",\"Uma linguagem de programação\",\"Um erro no código\"]', '1', 'Algoritmo é uma receita: passos finitos e bem definidos que levam a um resultado.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='O que é um algoritmo?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m), 'vf', 'logica', 'Uma variável pode trocar de valor durante a execução do programa.', NULL, NULL, 'true', 'Sim — é justamente por isso que ela se chama \"variável\".', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='Uma variável pode trocar de valor durante a execução do programa.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m), 'multipla', 'logica', 'Qual operador compara se dois valores são iguais?', NULL, '[\"=\",\"==\",\"=>\",\"+=\"]', '1', 'Um = atribui valor; == compara. Confundir os dois é o bug mais clássico do reino.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='Qual operador compara se dois valores são iguais?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m), 'completar', 'logica', 'Complete o operador que verifica se a é MENOR que b:', 'se (a ___ b) entao ...', NULL, '[\"<\"]', 'O operador < testa se o valor da esquerda é menor que o da direita.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='Complete o operador que verifica se a é MENOR que b:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m), 'vf', 'logica', 'Em muitas linguagens, o número 0 é tratado como \"falso\" numa condição.', NULL, NULL, 'true', 'Zero costuma valer \"falso\"; qualquer outro número, \"verdadeiro\".', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='Em muitas linguagens, o número 0 é tratado como \"falso\" numa condição.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 2, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=2) AS _m), 'ordenar', 'logica', 'Ordene os passos para trocar uma lâmpada queimada:', NULL, '[\"Colocar a lâmpada nova\",\"Desligar o interruptor\",\"Remover a lâmpada queimada\"]', '[1,2,0]', 'Primeiro segurança (desligar), depois remover a velha e por fim instalar a nova.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=2 AND pergunta='Ordene os passos para trocar uma lâmpada queimada:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m), 'multipla', 'logica', 'Quantas vezes executa um laço que vai de i = 0 enquanto i < 5?', NULL, '[\"4\",\"5\",\"6\",\"infinito\"]', '1', 'i assume 0, 1, 2, 3, 4 — cinco repetições antes de i < 5 ficar falso.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='Quantas vezes executa um laço que vai de i = 0 enquanto i < 5?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m), 'multipla', 'logica', 'O que é a \"condição de parada\" de um laço?', NULL, '[\"O comando que inicia o laço\",\"O teste que, quando falha, encerra a repetição\",\"A primeira linha do programa\",\"Um tipo de variável\"]', '1', 'É o teste que o laço avalia a cada volta; quando ele fica falso, o laço para.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='O que é a \"condição de parada\" de um laço?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m), 'vf', 'logica', 'Um fluxograma é uma forma visual de representar um algoritmo.', NULL, NULL, 'true', 'Sim: caixas e setas mostram o fluxo de decisões e ações.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='Um fluxograma é uma forma visual de representar um algoritmo.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m), 'erro', 'logica', 'Por que este laço nunca termina?', 'i = 0\nenquanto i < 3:\n    mostrar i', '[\"Falta mostrar o i\",\"i nunca é incrementado, então i < 3 é sempre verdadeiro\",\"O laço começa em 0\",\"Não há erro\"]', '1', 'Sem um i = i + 1 dentro do laço, a condição nunca fica falsa: laço infinito.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='Por que este laço nunca termina?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m), 'completar', 'logica', 'Complete a estrutura que escolhe um caminho conforme uma condição:', '___ (idade >= 18) entao mostrar \"maior\"', NULL, '[\"se\",\"if\"]', 'A estrutura condicional (se / if) executa um bloco somente quando a condição é verdadeira.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='Complete a estrutura que escolhe um caminho conforme uma condição:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 3, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=3) AS _m), 'ordenar', 'logica', 'Ordene as etapas de resolver um problema de programação:', NULL, '[\"Testar o resultado\",\"Entender o problema\",\"Escrever o código\",\"Planejar a solução\"]', '[1,3,2,0]', 'Entender → planejar → codificar → testar. Pular o \"entender\" é como debugar no escuro.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=3 AND pergunta='Ordene as etapas de resolver um problema de programação:') AS _chk);

-- ---------- php-mvc-sql (28 perguntas) ----------
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 5, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=5) AS _m), 'multipla', 'php', 'Qual função mostra tipo e valor de uma variável para depuração?', NULL, '[\"echo\",\"var_dump\",\"print\",\"len\"]', '1', 'var_dump() revela tipo e conteúdo — o canivete da depuração em PHP.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=5 AND pergunta='Qual função mostra tipo e valor de uma variável para depuração?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 5, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=5) AS _m), 'multipla', 'php', 'Qual a saída?', 'echo 7 % 3;', '[\"2\",\"1\",\"0\",\"21\"]', '1', 'O operador % devolve o RESTO da divisão: 7 dividido por 3 sobra 1.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=5 AND pergunta='Qual a saída?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 5, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=5) AS _m), 'multipla', 'php', 'Qual a saída?', 'echo \"5\" + 3;', '[\"53\",\"8\",\"Erro\",\"\\\"53\\\"\"]', '1', 'Com +, o PHP converte a string \"5\" em número: 5 + 3 = 8. (Para juntar texto, use o ponto.)', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=5 AND pergunta='Qual a saída?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 5, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=5) AS _m), 'vf', 'php', 'Em PHP, uma string pode ser escrita com aspas simples ou duplas.', NULL, NULL, 'true', 'Ambas valem; nas aspas duplas, variáveis dentro da string são interpoladas.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=5 AND pergunta='Em PHP, uma string pode ser escrita com aspas simples ou duplas.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 5, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=5) AS _m), 'multipla', 'php', 'Qual é um array associativo válido em PHP?', NULL, '[\"[1, 2, 3]\",\"[\'nome\' => \'Ana\', \'idade\' => 30]\",\"array<int>\",\"{nome: \\\"Ana\\\"}\"]', '1', 'O array associativo liga chaves a valores com =>: [\'nome\' => \'Ana\'].', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=5 AND pergunta='Qual é um array associativo válido em PHP?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 5, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=5) AS _m), 'completar', 'php', 'Complete para juntar o texto com a variável $nome:', 'echo \"Olá, \" ___ $nome;', NULL, '[\".\"]', 'O ponto (.) concatena strings em PHP.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=5 AND pergunta='Complete para juntar o texto com a variável $nome:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m), 'multipla', 'php', 'Qual laço executa o bloco ao menos uma vez antes de testar a condição?', NULL, '[\"for\",\"while\",\"do...while\",\"foreach\"]', '2', 'do...while testa a condição no FIM, então o corpo roda pelo menos uma vez.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='Qual laço executa o bloco ao menos uma vez antes de testar a condição?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m), 'completar', 'php', 'Complete o foreach que percorre cada item de $itens:', 'foreach ($itens ___ $item) { echo $item; }', NULL, '[\"as\"]', 'A sintaxe é foreach ($colecao as $item).', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='Complete o foreach que percorre cada item de $itens:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m), 'multipla', 'php', 'Qual a saída?', '$x = 4;\necho ($x % 2 == 0) ? \"par\" : \"impar\";', '[\"par\",\"impar\",\"4\",\"Erro\"]', '0', 'O operador ternário: como 4 é par, imprime \"par\".', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='Qual a saída?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m), 'multipla', 'php', 'Dentro de um switch, qual instrução encerra o case e evita o fall-through?', NULL, '[\"continue\",\"break\",\"stop\",\"exit\"]', '1', 'Sem break, a execução \"vaza\" para o próximo case. break encerra o bloco.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='Dentro de um switch, qual instrução encerra o case e evita o fall-through?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m), 'vf', 'php', 'O operador && só resulta verdadeiro quando AMBOS os lados são verdadeiros.', NULL, NULL, 'true', 'É o E lógico: basta um lado falso para o todo ser falso.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='O operador && só resulta verdadeiro quando AMBOS os lados são verdadeiros.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 6, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=6) AS _m), 'multipla', 'php', 'Quantas vezes \"oi\" é impresso?', 'for ($i = 0; $i < 3; $i++) { echo \"oi\"; }', '[\"2\",\"3\",\"4\",\"infinito\"]', '1', 'i vai de 0 a 2: três voltas, três \"oi\".', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=6 AND pergunta='Quantas vezes \"oi\" é impresso?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m), 'multipla', 'mvc', 'Em MVC, onde devem morar as regras de negócio e o acesso a dados?', NULL, '[\"Na View\",\"No Model\",\"No CSS\",\"No HTML\"]', '1', 'O Model concentra dados e regras; View só apresenta, Controller só orquestra.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Em MVC, onde devem morar as regras de negócio e o acesso a dados?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m), 'vf', 'mvc', 'Um Controller pode consultar vários Models antes de escolher a View.', NULL, NULL, 'true', 'O Controller é o maestro: reúne o que precisar dos Models e entrega à View.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Um Controller pode consultar vários Models antes de escolher a View.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m), 'multipla', 'mvc', 'Qual é a principal vantagem de separar em Model, View e Controller?', NULL, '[\"Deixa o site mais rápido sempre\",\"Separa responsabilidades, facilitando manutenção e testes\",\"Elimina a necessidade de banco\",\"Dispensa o HTML\"]', '1', 'Separação de responsabilidades: cada parte muda por um motivo só.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Qual é a principal vantagem de separar em Model, View e Controller?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m), 'multipla', 'mvc', 'Na rota index.php?url=perfil/editar/7, qual é o controller?', NULL, '[\"perfil\",\"editar\",\"7\",\"index\"]', '0', 'No padrão controller/metodo/parametro: \"perfil\" é o controller, \"editar\" o método e 7 o parâmetro.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Na rota index.php?url=perfil/editar/7, qual é o controller?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m), 'completar', 'mvc', 'Complete: a camada do MVC que renderiza o HTML para o usuário é a ___', NULL, NULL, '[\"View\",\"view\",\"Visão\"]', 'A View cuida só da apresentação — nada de SQL ou regra de negócio nela.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Complete: a camada do MVC que renderiza o HTML para o usuário é a ___') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m), 'erro', 'mvc', 'Qual prática QUEBRA a separação do MVC?', NULL, '[\"O Model acessar o banco\",\"O Controller chamar o Model\",\"A View conter SQL e regras de negócio\",\"A View apenas exibir os dados recebidos\"]', '2', 'SQL e lógica na View misturam as camadas — o caminho mais curto para o espaguete.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Qual prática QUEBRA a separação do MVC?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m), 'multipla', 'sql', 'Qual comando insere um novo registro numa tabela?', NULL, '[\"SELECT\",\"INSERT\",\"DROP\",\"WHERE\"]', '1', 'INSERT INTO ... VALUES ... adiciona uma nova linha.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Qual comando insere um novo registro numa tabela?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m), 'completar', 'sql', 'Complete para ordenar os usuários pelo nome:', 'SELECT * FROM usuarios ORDER ___ nome;', NULL, '[\"BY\",\"by\"]', 'ORDER BY coluna define a ordenação do resultado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Complete para ordenar os usuários pelo nome:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m), 'multipla', 'sql', 'Qual cláusula restringe a consulta a no máximo 10 linhas?', NULL, '[\"TOP\",\"LIMIT\",\"MAX\",\"FIRST\"]', '1', 'Em MySQL, LIMIT 10 corta o resultado nas 10 primeiras linhas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Qual cláusula restringe a consulta a no máximo 10 linhas?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m), 'multipla', 'sql', 'O que acontece com UPDATE usuarios SET ativo = 0; (sem WHERE)?', NULL, '[\"Atualiza só a primeira linha\",\"Não faz nada\",\"Atualiza TODAS as linhas da tabela\",\"Gera erro de sintaxe\"]', '2', 'Sem WHERE, o UPDATE atinge a tabela inteira. Respeite o WHERE — ou chore depois.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='O que acontece com UPDATE usuarios SET ativo = 0; (sem WHERE)?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m), 'erro', 'php', 'O Kraken roubou um símbolo. Onde está o Parse Error?', '$arr = [1, 2, 3;', '[\"Falta fechar o colchete ]\",\"Falta uma vírgula\",\"Falta o $\",\"Não há erro\"]', '0', 'O array abriu com [ mas nunca fechou com ]: o ; chegou cedo demais.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='O Kraken roubou um símbolo. Onde está o Parse Error?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m), 'multipla', 'php', 'Qual a saída?', '$a = [10, 20, 30];\necho count($a);', '[\"2\",\"3\",\"60\",\"0\"]', '1', 'count() devolve a QUANTIDADE de elementos: o array tem 3.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='Qual a saída?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m), 'multipla', 'php', 'Qual operador compara valor E tipo (comparação estrita)?', NULL, '[\"==\",\"===\",\"=\",\"<>\"]', '1', '=== exige que valor e tipo sejam iguais; \"5\" === 5 é falso.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='Qual operador compara valor E tipo (comparação estrita)?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m), 'vf', 'php', 'Em PHP, um parâmetro pode ter valor padrão: function taxa($v, $pct = 10).', NULL, NULL, 'true', 'Parâmetros com valor padrão tornam o argumento opcional na chamada.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='Em PHP, um parâmetro pode ter valor padrão: function taxa($v, $pct = 10).') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m), 'ordenar', 'mvc', 'Ordene o fluxo de uma requisição em MVC:', NULL, '[\"A View renderiza o HTML\",\"O Controller recebe a requisição\",\"O Model busca os dados\"]', '[1,2,0]', 'Controller recebe → Model busca os dados → View renderiza a resposta.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='Ordene o fluxo de uma requisição em MVC:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 9, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=9) AS _m), 'completar', 'php', 'Complete para a função devolver a soma:', 'function soma($a, $b) { ___ $a + $b; }', NULL, '[\"return\"]', 'return entrega o valor de volta a quem chamou a função.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=9 AND pergunta='Complete para a função devolver a soma:') AS _chk);

-- ---------- poo (28 perguntas) ----------
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m), 'multipla', 'poo', 'Qual método especial é chamado automaticamente ao criar um objeto?', NULL, '[\"O destrutor\",\"O construtor\",\"O getter\",\"O main\"]', '1', 'O construtor (__construct em PHP) inicializa o objeto no momento da criação.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Qual método especial é chamado automaticamente ao criar um objeto?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m), 'completar', 'poo', 'Complete o nome do método construtor em PHP:', 'public function ___________() { }', NULL, '[\"__construct\"]', 'Em PHP o construtor se chama __construct (dois underscores).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Complete o nome do método construtor em PHP:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m), 'vf', 'poo', 'Vários objetos diferentes podem ser criados a partir de uma mesma classe.', NULL, NULL, 'true', 'A classe é o molde; cada new gera uma instância independente com seu próprio estado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Vários objetos diferentes podem ser criados a partir de uma mesma classe.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m), 'multipla', 'poo', 'Numa classe, os atributos guardam o ___ e os métodos definem o ___.', NULL, '[\"comportamento \\/ estado\",\"estado \\/ comportamento\",\"nome \\/ tipo\",\"banco \\/ tela\"]', '1', 'Atributos = estado (dados); métodos = comportamento (ações).', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Numa classe, os atributos guardam o ___ e os métodos definem o ___.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m), 'multipla', 'poo', 'Qual a saída?', 'class Gato {\n  public $nome = \'Bigode\';\n}\n$g = new Gato();\necho $g->nome;', '[\"nome\",\"Bigode\",\"Gato\",\"Erro\"]', '1', 'O objeto $g acessa seu atributo nome com ->, imprimindo \"Bigode\".', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Qual a saída?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m), 'ordenar', 'poo', 'Ordene os passos para usar um objeto:', NULL, '[\"Chamar um método do objeto\",\"Declarar a classe\",\"Instanciar o objeto com new\"]', '[1,2,0]', 'Primeiro existe o molde (classe), depois cria-se a instância e então usam-se seus métodos.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Ordene os passos para usar um objeto:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m), 'multipla', 'poo', 'Qual modificador permite acesso ao atributo de QUALQUER lugar?', NULL, '[\"private\",\"protected\",\"public\",\"final\"]', '2', 'public deixa o membro acessível de dentro e de fora da classe.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Qual modificador permite acesso ao atributo de QUALQUER lugar?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m), 'vf', 'poo', 'Getters e setters dão acesso controlado a atributos privados.', NULL, NULL, 'true', 'Eles são a porta oficial: leem/alteram o estado interno com validação, sem expor o atributo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Getters e setters dão acesso controlado a atributos privados.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m), 'completar', 'poo', 'Torne o atributo $saldo inacessível de fora da classe:', 'class Conta { _______ $saldo; }', NULL, '[\"private\"]', 'private restringe o acesso ao interior da própria classe.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Torne o atributo $saldo inacessível de fora da classe:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m), 'multipla', 'poo', 'Por que manter atributos private com getters/setters?', NULL, '[\"Para digitar mais\",\"Para controlar e validar o acesso ao estado interno\",\"Para deixar tudo público\",\"Para remover métodos\"]', '1', 'Encapsular permite validar mudanças e proteger invariantes do objeto.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Por que manter atributos private com getters/setters?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m), 'erro', 'poo', 'O que fere o encapsulamento?', NULL, '[\"Usar getters e setters\",\"Deixar todos os atributos public e alterá-los direto de fora\",\"Marcar atributos como private\",\"Validar dados no setter\"]', '1', 'Expor tudo como public deixa o estado interno à mercê de qualquer um — o oposto de encapsular.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='O que fere o encapsulamento?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m), 'multipla', 'poo', 'O modificador protected permite acesso:', NULL, '[\"Só de fora da classe\",\"Na própria classe e nas suas subclasses\",\"De qualquer lugar\",\"Em nenhum lugar\"]', '1', 'protected libera o acesso à classe e às que a estendem, mas não ao mundo externo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='O modificador protected permite acesso:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m), 'multipla', 'poo', 'Herança expressa qual relação entre as classes?', NULL, '[\"tem-um (has-a)\",\"é-um (is-a)\",\"usa-um\",\"faz-um\"]', '1', 'Cachorro é-um Animal: herança modela especialização (is-a).', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Herança expressa qual relação entre as classes?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m), 'multipla', 'poo', 'Composição expressa qual relação?', NULL, '[\"é-um (is-a)\",\"tem-um (has-a)\",\"igual-a\",\"maior-que\"]', '1', 'Carro tem-um Motor: composição monta um objeto a partir de outros (has-a).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Composição expressa qual relação?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m), 'vf', 'poo', 'Em PHP, uma classe pode herdar de apenas uma classe pai (herança simples).', NULL, NULL, 'true', 'PHP não tem herança múltipla de classes; para múltiplos contratos, usam-se interfaces ou traits.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Em PHP, uma classe pode herdar de apenas uma classe pai (herança simples).') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m), 'completar', 'poo', 'Complete para chamar o construtor da classe pai:', 'parent::___________();', NULL, '[\"__construct\"]', 'parent::__construct() reaproveita a inicialização definida na superclasse.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Complete para chamar o construtor da classe pai:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m), 'multipla', 'poo', 'Sobrescrever (override) um método significa:', NULL, '[\"Apagar o método do pai\",\"Redefinir, na subclasse, um método herdado\",\"Criar um atributo novo\",\"Tornar o método privado\"]', '1', 'A subclasse fornece sua própria versão de um método já existente na superclasse.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Sobrescrever (override) um método significa:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m), 'ordenar', 'poo', 'Ordene da classe mais genérica para a mais específica:', NULL, '[\"Cachorro\",\"SerVivo\",\"Animal\"]', '[1,2,0]', 'SerVivo (geral) → Animal → Cachorro (específico).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Ordene da classe mais genérica para a mais específica:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m), 'multipla', 'poo', 'O que uma interface contém?', NULL, '[\"A implementação completa dos métodos\",\"Assinaturas de métodos sem implementação (um contrato)\",\"Apenas atributos privados\",\"Um laço de repetição\"]', '1', 'A interface lista o QUE deve existir; cada classe decide o COMO ao implementá-la.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='O que uma interface contém?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m), 'vf', 'poo', 'Uma mesma classe pode implementar várias interfaces ao mesmo tempo.', NULL, NULL, 'true', 'Diferente da herança de classe, assinar vários contratos (interfaces) é permitido.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Uma mesma classe pode implementar várias interfaces ao mesmo tempo.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m), 'multipla', 'poo', 'Polimorfismo permite:', NULL, '[\"Tratar objetos de tipos diferentes pela mesma interface\",\"Criar atributos privados\",\"Eliminar classes\",\"Acelerar o banco de dados\"]', '0', 'Vários tipos respondem à mesma chamada à sua maneira — código que fala com a interface, não com a classe concreta.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Polimorfismo permite:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m), 'completar', 'poo', 'Faça a classe Pato assinar o contrato Nadador:', 'class Pato __________ Nadador { }', NULL, '[\"implements\"]', 'implements obriga a classe a fornecer os métodos declarados na interface.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Faça a classe Pato assinar o contrato Nadador:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m), 'multipla', 'poo', 'No SOLID, o que o \"S\" (SRP) defende?', NULL, '[\"Single Responsibility: uma classe, uma razão para mudar\",\"Simple Rule\",\"Static Reference\",\"Sorted Records\"]', '0', 'Princípio da Responsabilidade Única: cada classe deve ter um único motivo para mudar.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='No SOLID, o que o \"S\" (SRP) defende?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m), 'multipla', 'poo', 'Uma classe com ALTA coesão:', NULL, '[\"Faz muitas coisas sem relação\",\"Concentra-se em uma responsabilidade bem definida\",\"Não tem métodos\",\"Depende de todas as outras classes\"]', '1', 'Coesão alta = a classe trata de um único assunto, o oposto da God Class.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Uma classe com ALTA coesão:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m), 'vf', 'poo', 'Baixo acoplamento entre classes facilita mudanças e testes.', NULL, NULL, 'true', 'Quanto menos uma classe depende das outras, mais fácil trocá-la ou testá-la isoladamente.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Baixo acoplamento entre classes facilita mudanças e testes.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m), 'erro', 'poo', 'Qual problema esta classe evidencia?', 'class Pedido {\n  function calcularFrete() {}\n  function gerarPdf() {}\n  function enviarEmail() {}\n  function salvarNoBanco() {}\n}', '[\"Está coesa e correta\",\"Responsabilidades demais (viola o SRP)\",\"Falta herança\",\"Falta um construtor\"]', '1', 'Frete, PDF, e-mail e persistência são quatro responsabilidades — separe em classes próprias.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Qual problema esta classe evidencia?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m), 'multipla', 'poo', 'A refatoração típica de uma God Class é:', NULL, '[\"Adicionar ainda mais métodos\",\"Dividi-la em classes menores e coesas\",\"Tornar tudo public\",\"Apagar todos os testes\"]', '1', 'Quebrar a classe-deus em partes coesas melhora manutenção, testes e leitura.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='A refatoração típica de uma God Class é:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m), 'completar', 'poo', 'Complete a sigla do princípio que evita repetir código: Don\'t Repeat Yourself = ___', NULL, NULL, '[\"DRY\",\"dry\"]', 'DRY: cada conhecimento deve ter uma única representação no sistema.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Complete a sigla do princípio que evita repetir código: Don\'t Repeat Yourself = ___') AS _chk);

-- ---------- redes (28 perguntas) ----------
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m), 'multipla', 'redes', 'Qual camada do modelo OSI cuida do endereçamento IP e do roteamento?', NULL, '[\"Física\",\"Enlace\",\"Rede\",\"Aplicação\"]', '2', 'A camada de Rede (3) endereça (IP) e escolhe rotas entre redes.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Qual camada do modelo OSI cuida do endereçamento IP e do roteamento?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m), 'multipla', 'redes', 'Qual camada cuida da entrega fim-a-fim e abriga TCP e UDP?', NULL, '[\"Rede\",\"Transporte\",\"Sessão\",\"Física\"]', '1', 'A camada de Transporte (4) controla a entrega entre os processos: TCP e UDP vivem aqui.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Qual camada cuida da entrega fim-a-fim e abriga TCP e UDP?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m), 'vf', 'redes', 'O modelo TCP/IP é mais enxuto que o OSI, com menos camadas.', NULL, NULL, 'true', 'O TCP/IP agrupa as 7 camadas do OSI em 4 (ou 5).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='O modelo TCP/IP é mais enxuto que o OSI, com menos camadas.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m), 'multipla', 'redes', 'O switch opera principalmente em qual camada do OSI?', NULL, '[\"Física\",\"Enlace\",\"Rede\",\"Transporte\"]', '1', 'O switch comuta quadros por endereço MAC: camada de Enlace (2).', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='O switch opera principalmente em qual camada do OSI?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m), 'completar', 'redes', 'A camada que entrega serviços direto ao usuário (HTTP, DNS, FTP) é a camada de ___.', NULL, NULL, '[\"Aplicação\",\"aplicacao\",\"aplicação\"]', 'A camada de Aplicação (7) é onde vivem os protocolos que o usuário usa.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='A camada que entrega serviços direto ao usuário (HTTP, DNS, FTP) é a camada de ___.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m), 'ordenar', 'redes', 'Ordene as camadas do OSI da 5 para a 7:', NULL, '[\"Aplicação\",\"Sessão\",\"Apresentação\"]', '[1,2,0]', 'Camada 5 Sessão, 6 Apresentação, 7 Aplicação.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Ordene as camadas do OSI da 5 para a 7:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m), 'multipla', 'redes', 'Quantos bits tem um endereço IPv4?', NULL, '[\"16\",\"32\",\"64\",\"128\"]', '1', 'IPv4 usa 32 bits, divididos em quatro octetos (ex.: 192.168.0.1).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Quantos bits tem um endereço IPv4?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m), 'multipla', 'redes', 'Quantos bits tem um endereço IPv6?', NULL, '[\"32\",\"64\",\"128\",\"256\"]', '2', 'IPv6 usa 128 bits — espaço gigantesco, criado porque o IPv4 acabou.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Quantos bits tem um endereço IPv6?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m), 'vf', 'redes', 'A máscara de sub-rede separa a parte de rede da parte de host de um endereço IP.', NULL, NULL, 'true', 'A máscara (ex.: 255.255.255.0) diz quais bits identificam a rede e quais o host.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='A máscara de sub-rede separa a parte de rede da parte de host de um endereço IP.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m), 'multipla', 'redes', 'Qual protocolo distribui endereços IP automaticamente aos dispositivos da rede?', NULL, '[\"DNS\",\"DHCP\",\"HTTP\",\"ARP\"]', '1', 'O DHCP atribui IP, máscara e gateway sem configuração manual.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Qual protocolo distribui endereços IP automaticamente aos dispositivos da rede?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m), 'multipla', 'redes', 'O \"gateway padrão\" de uma rede local é, normalmente:', NULL, '[\"O servidor DNS público\",\"O roteador que encaminha o tráfego para fora da rede local\",\"O switch principal\",\"O cabo de internet\"]', '1', 'Pacotes destinados a outras redes saem pelo gateway (o roteador da borda).', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='O \"gateway padrão\" de uma rede local é, normalmente:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m), 'completar', 'redes', 'O serviço que traduz www.exemplo.com em um endereço IP é o ___.', NULL, NULL, '[\"DNS\",\"dns\"]', 'O DNS é a \"agenda de contatos\" da internet: nome → IP.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='O serviço que traduz www.exemplo.com em um endereço IP é o ___.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m), 'multipla', 'redes', 'O \"aperto de mão\" de três vias (SYN, SYN-ACK, ACK) pertence a qual protocolo?', NULL, '[\"UDP\",\"TCP\",\"HTTP\",\"DNS\"]', '1', 'O TCP estabelece a conexão com o three-way handshake antes de enviar dados.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O \"aperto de mão\" de três vias (SYN, SYN-ACK, ACK) pertence a qual protocolo?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m), 'multipla', 'redes', 'Qual protocolo é preferido para chamadas de vídeo ao vivo e jogos online?', NULL, '[\"TCP\",\"UDP\",\"FTP\",\"SMTP\"]', '1', 'O UDP é veloz e sem overhead de confirmação — melhor perder um quadro do que travar.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Qual protocolo é preferido para chamadas de vídeo ao vivo e jogos online?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m), 'multipla', 'redes', 'O código de status HTTP 500 significa:', NULL, '[\"Sucesso\",\"Não encontrado\",\"Erro interno do servidor\",\"Redirecionamento\"]', '2', '5xx são erros do servidor; 500 é a falha interna genérica.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O código de status HTTP 500 significa:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m), 'multipla', 'redes', 'O código de status HTTP 403 significa:', NULL, '[\"OK\",\"Proibido (acesso negado)\",\"Não encontrado\",\"Criado\"]', '1', '403 Forbidden: o servidor entendeu o pedido, mas se recusa a atendê-lo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O código de status HTTP 403 significa:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m), 'vf', 'redes', 'HTTPS é o HTTP com uma camada de criptografia (TLS/SSL).', NULL, NULL, 'true', 'O TLS cifra a comunicação, protegendo os dados em trânsito.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='HTTPS é o HTTP com uma camada de criptografia (TLS/SSL).') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m), 'completar', 'redes', 'O método HTTP usado para ENVIAR os dados de um formulário ao servidor é o ___.', NULL, NULL, '[\"POST\",\"post\"]', 'POST envia dados no corpo da requisição; GET apenas busca recursos.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='O método HTTP usado para ENVIAR os dados de um formulário ao servidor é o ___.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m), 'multipla', 'redes', 'Para que serve o número de sequência do TCP?', NULL, '[\"Criptografar o pacote\",\"Remontar os pacotes na ordem correta no destino\",\"Escolher a rota\",\"Acelerar o DNS\"]', '1', 'Os números de sequência permitem reordenar pacotes que chegam fora de ordem.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Para que serve o número de sequência do TCP?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m), 'vf', 'redes', 'O UDP não retransmite automaticamente um pacote perdido.', NULL, NULL, 'true', 'O UDP é \"dispare e esqueça\": sem confirmação nem retransmissão.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='O UDP não retransmite automaticamente um pacote perdido.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m), 'multipla', 'redes', 'Qual comando testa conectividade enviando pacotes ICMP echo?', NULL, '[\"ping\",\"grep\",\"echo\",\"cat\"]', '0', 'ping mede se o destino responde e em quanto tempo (latência).', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Qual comando testa conectividade enviando pacotes ICMP echo?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m), 'multipla', 'redes', 'Latência alta numa rede significa:', NULL, '[\"Mais banda disponível\",\"Maior demora para um pacote ir e voltar\",\"Menos perda de pacotes\",\"IP inválido\"]', '1', 'Latência é o atraso de ida e volta (RTT); alta = resposta lenta.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Latência alta numa rede significa:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m), 'multipla', 'redes', 'Num ataque DDoS, o tráfego malicioso costuma vir de:', NULL, '[\"Uma única máquina\",\"Muitas máquinas distribuídas (uma botnet)\",\"O próprio servidor\",\"O cabo de rede\"]', '1', 'O \"D\" extra é de Distributed: milhares de fontes inundam o alvo ao mesmo tempo.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Num ataque DDoS, o tráfego malicioso costuma vir de:') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m), 'multipla', 'redes', 'Qual é a porta padrão do HTTP (sem o S)?', NULL, '[\"21\",\"80\",\"443\",\"8080\"]', '1', 'HTTP usa a porta 80; HTTPS usa a 443.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Qual é a porta padrão do HTTP (sem o S)?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m), 'multipla', 'redes', 'Qual é a porta padrão do SSH?', NULL, '[\"21\",\"22\",\"23\",\"25\"]', '1', 'SSH usa a porta 22 (21 é FTP, 23 é Telnet, 25 é SMTP).', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Qual é a porta padrão do SSH?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m), 'vf', 'redes', 'Um firewall pode bloquear tráfego com base em portas, IPs e protocolos.', NULL, NULL, 'true', 'O firewall aplica regras de filtragem para permitir ou barrar o tráfego.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Um firewall pode bloquear tráfego com base em portas, IPs e protocolos.') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m), 'erro', 'redes', 'Qual destas NÃO é uma defesa contra DDoS?', NULL, '[\"Rate limiting (limitar requisições por IP)\",\"Filtros e firewall na borda\",\"Publicar a senha de admin do servidor\",\"Usar uma CDN para absorver o tráfego\"]', '2', 'Publicar credenciais é entregar o reino ao inimigo — nada a ver com mitigar DDoS.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Qual destas NÃO é uma defesa contra DDoS?') AS _chk);
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33, (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m), 'multipla', 'redes', 'A criptografia ponta-a-ponta garante que:', NULL, '[\"A rede fica mais rápida\",\"Só o remetente e o destinatário conseguem ler o conteúdo\",\"Os pacotes nunca se perdem\",\"O IP fica oculto para sempre\"]', '1', 'Apenas as pontas têm as chaves; intermediários veem só dados cifrados.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='A criptografia ponta-a-ponta garante que:') AS _chk);

-- ============================================================================
-- Total no arquivo: 157 perguntas.
--   calculo        24
--   estruturas     28
--   final          8
--   logica         13
--   php-mvc-sql    28
--   poo            28
--   redes          28
-- Confira no banco:  SELECT fase_id, COUNT(*) FROM desafios GROUP BY fase_id;
-- ============================================================================
