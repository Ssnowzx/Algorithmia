<?php
/**
 * Banco de questões — Marcelo, o Andarilho (Floresta das Estruturas).
 * Estrutura de Dados II: pilhas, filas, listas, árvores, Big-O e recursão.
 * Pool ampliado das fases 17, 18, 19, 20 (secundária) e 21 (chefe recursivo).
 */

return [
    // ---- Fase 17: Pilhas e Filas ----
    ['fase' => 17, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'Qual operação OLHA o elemento do topo da pilha sem removê-lo?', 'codigo' => null,
     'opcoes' => ['pop', 'push', 'peek (top)', 'enqueue'], 'resposta' => 2,
     'explicacao' => 'peek (ou top) consulta o topo sem desempilhar.', 'dif' => 2],

    ['fase' => 17, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'Numa fila, qual operação INSERE um elemento no fim?', 'codigo' => null,
     'opcoes' => ['dequeue', 'enqueue', 'pop', 'peek'], 'resposta' => 1,
     'explicacao' => 'enqueue adiciona ao fim da fila; dequeue remove da frente.', 'dif' => 2],

    ['fase' => 17, 'tipo' => 'vf', 'assunto' => 'estruturas',
     'pergunta' => 'A pilha de chamadas (call stack) de um programa funciona como uma pilha LIFO.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'A última função chamada é a primeira a retornar: puro LIFO.', 'dif' => 3],

    ['fase' => 17, 'tipo' => 'ordenar', 'assunto' => 'estruturas',
     'pergunta' => 'Enfileirei A, depois B, depois C. Em que ordem eles SAEM da fila?', 'codigo' => null,
     'opcoes' => ['A', 'B', 'C'], 'resposta' => [0, 1, 2],
     'explicacao' => 'Fila é FIFO: o primeiro a entrar (A) é o primeiro a sair.', 'dif' => 2],

    ['fase' => 17, 'tipo' => 'completar', 'assunto' => 'estruturas',
     'pergunta' => 'Complete a operação que adiciona um elemento ao topo da pilha:', 'codigo' => 'pilha.____(valor);',
     'opcoes' => null, 'resposta' => ['push'],
     'explicacao' => 'push empilha um novo elemento no topo.', 'dif' => 2],

    ['fase' => 17, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'O recurso "desfazer" (undo) de um editor é melhor modelado por:', 'codigo' => null,
     'opcoes' => ['Uma fila', 'Uma pilha', 'Uma árvore', 'Um grafo'], 'resposta' => 1,
     'explicacao' => 'Desfaz-se a última ação primeiro — comportamento LIFO de pilha.', 'dif' => 3],

    // ---- Fase 18: Listas e Nós ----
    ['fase' => 18, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'Numa lista DUPLAMENTE encadeada, cada nó aponta para:', 'codigo' => null,
     'opcoes' => ['Só o próximo', 'O anterior e o próximo', 'A cabeça e a cauda', 'Nenhum nó'],
     'resposta' => 1,
     'explicacao' => 'Dois ponteiros por nó (anterior e próximo) permitem percorrer nos dois sentidos.', 'dif' => 3],

    ['fase' => 18, 'tipo' => 'vf', 'assunto' => 'estruturas',
     'pergunta' => 'Numa lista encadeada, os elementos NÃO precisam ficar contíguos na memória.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'Cada nó guarda um ponteiro para o próximo; eles podem estar espalhados na memória.', 'dif' => 3],

    ['fase' => 18, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'Remover o PRIMEIRO nó de uma lista encadeada custa, no pior caso:', 'codigo' => null,
     'opcoes' => ['O(1)', 'O(n)', 'O(log n)', 'O(n²)'], 'resposta' => 0,
     'explicacao' => 'Basta mover a cabeça para o segundo nó: tempo constante.', 'dif' => 3],

    ['fase' => 18, 'tipo' => 'completar', 'assunto' => 'estruturas',
     'pergunta' => 'O ponteiro para o primeiro nó de uma lista costuma se chamar ___ (em inglês).', 'codigo' => null,
     'opcoes' => null, 'resposta' => ['head'],
     'explicacao' => 'head aponta para o início; o último nó (tail) aponta para nulo.', 'dif' => 2],

    ['fase' => 18, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'Qual a vantagem da lista encadeada sobre o vetor (array) fixo?', 'codigo' => null,
     'opcoes' => ['Acesso por índice em O(1)', 'Inserir/remover no meio sem deslocar os outros elementos', 'Ocupa menos memória por elemento', 'Permite busca binária direta'],
     'resposta' => 1,
     'explicacao' => 'Inserir/remover só reajusta ponteiros; no array seria preciso deslocar elementos.', 'dif' => 4],

    ['fase' => 18, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'Buscar um valor numa lista encadeada NÃO ordenada custa, no pior caso:', 'codigo' => null,
     'opcoes' => ['O(1)', 'O(log n)', 'O(n)', 'O(0)'], 'resposta' => 2,
     'explicacao' => 'Pode ser preciso percorrer nó a nó até o fim: linear, O(n).', 'dif' => 3],

    // ---- Fase 19: Árvores e Big-O ----
    ['fase' => 19, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'Numa árvore BINÁRIA, cada nó tem no máximo quantos filhos?', 'codigo' => null,
     'opcoes' => ['1', '2', '3', 'ilimitado'], 'resposta' => 1,
     'explicacao' => 'Binária = no máximo dois filhos por nó (esquerdo e direito).', 'dif' => 2],

    ['fase' => 19, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'Numa Árvore Binária de Busca (BST), valores MENORES que o nó vão para:', 'codigo' => null,
     'opcoes' => ['A subárvore direita', 'A subárvore esquerda', 'A raiz', 'Fora da árvore'],
     'resposta' => 1,
     'explicacao' => 'Por convenção, menores à esquerda e maiores à direita — é o que torna a busca rápida.', 'dif' => 3],

    ['fase' => 19, 'tipo' => 'vf', 'assunto' => 'estruturas',
     'pergunta' => 'Numa árvore de busca BALANCEADA, a altura cresce na ordem de log n.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'Por isso buscas, inserções e remoções saem em O(log n) quando a árvore está equilibrada.', 'dif' => 3],

    ['fase' => 19, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'Qual notação descreve o LIMITE SUPERIOR (pior caso) de um algoritmo?', 'codigo' => null,
     'opcoes' => ['Ômega (Ω)', 'Big-O (O)', 'Teta exato (Θ)', 'Nenhuma'], 'resposta' => 1,
     'explicacao' => 'Big-O dá o teto de crescimento — quão ruim pode ficar conforme n cresce.', 'dif' => 4],

    ['fase' => 19, 'tipo' => 'ordenar', 'assunto' => 'estruturas',
     'pergunta' => 'Ordene as complexidades do MELHOR (mais rápido) ao PIOR:', 'codigo' => null,
     'opcoes' => ['O(n log n)', 'O(1)', 'O(n)', 'O(log n)'], 'resposta' => [1, 3, 2, 0],
     'explicacao' => 'O(1) < O(log n) < O(n) < O(n log n) em ordem de crescimento.', 'dif' => 4],

    ['fase' => 19, 'tipo' => 'completar', 'assunto' => 'estruturas',
     'pergunta' => 'A busca binária descarta, a cada passo, ___ do espaço de busca.', 'codigo' => null,
     'opcoes' => null, 'resposta' => ['metade', 'a metade'],
     'explicacao' => 'Comparou com o meio e descartou metade — por isso o custo é O(log n).', 'dif' => 3],

    // ---- Fase 20: O Atalho do Gol Quadrado (secundária) ----
    ['fase' => 20, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'Por que ordenar um vetor antes de fazer MUITAS buscas costuma compensar?', 'codigo' => null,
     'opcoes' => ['Ordenar deixa a memória menor', 'Ordena-se uma vez e depois usa-se busca binária O(log n) muitas vezes', 'Busca em vetor ordenado é O(1) sempre', 'Não compensa nunca'],
     'resposta' => 1,
     'explicacao' => 'O custo único da ordenação se dilui em muitas buscas logarítmicas.', 'dif' => 3],

    ['fase' => 20, 'tipo' => 'vf', 'assunto' => 'estruturas',
     'pergunta' => 'Uma tabela hash bem dimensionada permite busca em tempo MÉDIO O(1).', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'A função de hash leva direto ao "balde"; em média, acesso constante.', 'dif' => 4],

    ['fase' => 20, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'Num vetor ORDENADO de 1.000.000 itens, a busca binária faz no máximo cerca de:', 'codigo' => null,
     'opcoes' => ['1.000.000 comparações', '500.000 comparações', '20 comparações', '1 comparação'],
     'resposta' => 2,
     'explicacao' => 'log2(1.000.000) ≈ 20: dobrar pela metade vinte vezes basta.', 'dif' => 4],

    ['fase' => 20, 'tipo' => 'completar', 'assunto' => 'estruturas',
     'pergunta' => 'A estrutura chave→valor com acesso médio O(1) é a tabela ___.', 'codigo' => null,
     'opcoes' => null, 'resposta' => ['hash'],
     'explicacao' => 'A tabela hash mapeia chaves a posições via função de hash.', 'dif' => 3],

    // ---- Fase 21: A Hidra Recursiva (chefe) ----
    ['fase' => 21, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'O que acontece na pilha de execução a cada chamada recursiva?', 'codigo' => null,
     'opcoes' => ['Nada', 'Empilha-se um novo registro de ativação (frame)', 'A pilha é esvaziada', 'O programa encerra'],
     'resposta' => 1,
     'explicacao' => 'Cada chamada empilha um frame; ao retornar, ele é desempilhado.', 'dif' => 3],

    ['fase' => 21, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'Um "stack overflow" numa recursão acontece tipicamente quando:', 'codigo' => null,
     'opcoes' => ['Há um caso base correto', 'Não há caso base, ou ele nunca é atingido', 'A função retorna cedo demais', 'Há poucos parâmetros'],
     'resposta' => 1,
     'explicacao' => 'Sem parada, a pilha cresce sem fim até estourar a memória reservada.', 'dif' => 3],

    ['fase' => 21, 'tipo' => 'vf', 'assunto' => 'estruturas',
     'pergunta' => 'Toda recursão pode, em princípio, ser reescrita como um laço (iteração).', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'Recursão e iteração têm o mesmo poder; às vezes a versão iterativa usa uma pilha explícita.', 'dif' => 4],

    ['fase' => 21, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'Com fib(n) = fib(n-1) + fib(n-2), fib(0)=0 e fib(1)=1, quanto vale fib(5)?', 'codigo' => null,
     'opcoes' => ['3', '5', '8', '13'], 'resposta' => 1,
     'explicacao' => 'Sequência: 0, 1, 1, 2, 3, 5 — fib(5) = 5.', 'dif' => 4],

    ['fase' => 21, 'tipo' => 'erro', 'assunto' => 'estruturas',
     'pergunta' => 'Por que esta recursão estoura a pilha?', 'codigo' => "function conta(\$n) {\n  return conta(\$n + 1);\n}",
     'opcoes' => ['Usa soma', 'Não tem caso base e n só cresce, nunca parando', 'Falta um parâmetro', 'Não há erro'],
     'resposta' => 1,
     'explicacao' => 'Sem condição de parada e com n sempre crescendo, ela se chama para sempre.', 'dif' => 3],

    ['fase' => 21, 'tipo' => 'ordenar', 'assunto' => 'estruturas',
     'pergunta' => 'fatorial(3) com base fatorial(0)=1. Ordene os RETORNOS, da base ao topo:', 'codigo' => null,
     'opcoes' => ['fatorial(2) retorna 2', 'fatorial(0) retorna 1', 'fatorial(1) retorna 1', 'fatorial(3) retorna 6'],
     'resposta' => [1, 2, 0, 3],
     'explicacao' => 'A base resolve primeiro: 1 → 1 → 2 → 6, subindo a pilha de volta.', 'dif' => 4],
];
