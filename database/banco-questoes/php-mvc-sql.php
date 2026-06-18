<?php
/**
 * Banco de questões — Willen, o Arquiteto (Porto da Sintaxe).
 * Laboratório de Programação II: PHP, padrão MVC e SQL. Sabor PHP mantido.
 * Pool ampliado das fases 5, 6 (PHP), 7 (MVC), 8 (SQL) e 9 (chefe, mix).
 */

return [
    // ---- Fase 5: Variáveis e Eco (PHP) ----
    ['fase' => 5, 'tipo' => 'multipla', 'assunto' => 'php',
     'pergunta' => 'Qual função mostra tipo e valor de uma variável para depuração?', 'codigo' => null,
     'opcoes' => ['echo', 'var_dump', 'print', 'len'], 'resposta' => 1,
     'explicacao' => 'var_dump() revela tipo e conteúdo — o canivete da depuração em PHP.', 'dif' => 2],

    ['fase' => 5, 'tipo' => 'multipla', 'assunto' => 'php',
     'pergunta' => 'Qual a saída?', 'codigo' => 'echo 7 % 3;',
     'opcoes' => ['2', '1', '0', '21'], 'resposta' => 1,
     'explicacao' => 'O operador % devolve o RESTO da divisão: 7 dividido por 3 sobra 1.', 'dif' => 2],

    ['fase' => 5, 'tipo' => 'multipla', 'assunto' => 'php',
     'pergunta' => 'Qual a saída?', 'codigo' => 'echo "5" + 3;',
     'opcoes' => ['53', '8', 'Erro', '"53"'], 'resposta' => 1,
     'explicacao' => 'Com +, o PHP converte a string "5" em número: 5 + 3 = 8. (Para juntar texto, use o ponto.)', 'dif' => 3],

    ['fase' => 5, 'tipo' => 'vf', 'assunto' => 'php',
     'pergunta' => 'Em PHP, uma string pode ser escrita com aspas simples ou duplas.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'Ambas valem; nas aspas duplas, variáveis dentro da string são interpoladas.', 'dif' => 1],

    ['fase' => 5, 'tipo' => 'multipla', 'assunto' => 'php',
     'pergunta' => 'Qual é um array associativo válido em PHP?', 'codigo' => null,
     'opcoes' => ['[1, 2, 3]', "['nome' => 'Ana', 'idade' => 30]", 'array<int>', '{nome: "Ana"}'],
     'resposta' => 1,
     'explicacao' => "O array associativo liga chaves a valores com =>: ['nome' => 'Ana'].", 'dif' => 2],

    ['fase' => 5, 'tipo' => 'completar', 'assunto' => 'php',
     'pergunta' => 'Complete para juntar o texto com a variável $nome:', 'codigo' => 'echo "Olá, " ___ $nome;',
     'opcoes' => null, 'resposta' => ['.'],
     'explicacao' => 'O ponto (.) concatena strings em PHP.', 'dif' => 1],

    // ---- Fase 6: Estruturas de Controle (PHP) ----
    ['fase' => 6, 'tipo' => 'multipla', 'assunto' => 'php',
     'pergunta' => 'Qual laço executa o bloco ao menos uma vez antes de testar a condição?', 'codigo' => null,
     'opcoes' => ['for', 'while', 'do...while', 'foreach'], 'resposta' => 2,
     'explicacao' => 'do...while testa a condição no FIM, então o corpo roda pelo menos uma vez.', 'dif' => 3],

    ['fase' => 6, 'tipo' => 'completar', 'assunto' => 'php',
     'pergunta' => 'Complete o foreach que percorre cada item de $itens:', 'codigo' => 'foreach ($itens ___ $item) { echo $item; }',
     'opcoes' => null, 'resposta' => ['as'],
     'explicacao' => 'A sintaxe é foreach ($colecao as $item).', 'dif' => 2],

    ['fase' => 6, 'tipo' => 'multipla', 'assunto' => 'php',
     'pergunta' => 'Qual a saída?', 'codigo' => '$x = 4;' . "\n" . 'echo ($x % 2 == 0) ? "par" : "impar";',
     'opcoes' => ['par', 'impar', '4', 'Erro'], 'resposta' => 0,
     'explicacao' => 'O operador ternário: como 4 é par, imprime "par".', 'dif' => 3],

    ['fase' => 6, 'tipo' => 'multipla', 'assunto' => 'php',
     'pergunta' => 'Dentro de um switch, qual instrução encerra o case e evita o fall-through?', 'codigo' => null,
     'opcoes' => ['continue', 'break', 'stop', 'exit'], 'resposta' => 1,
     'explicacao' => 'Sem break, a execução "vaza" para o próximo case. break encerra o bloco.', 'dif' => 3],

    ['fase' => 6, 'tipo' => 'vf', 'assunto' => 'php',
     'pergunta' => 'O operador && só resulta verdadeiro quando AMBOS os lados são verdadeiros.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'É o E lógico: basta um lado falso para o todo ser falso.', 'dif' => 2],

    ['fase' => 6, 'tipo' => 'multipla', 'assunto' => 'php',
     'pergunta' => 'Quantas vezes "oi" é impresso?', 'codigo' => 'for ($i = 0; $i < 3; $i++) { echo "oi"; }',
     'opcoes' => ['2', '3', '4', 'infinito'], 'resposta' => 1,
     'explicacao' => 'i vai de 0 a 2: três voltas, três "oi".', 'dif' => 2],

    // ---- Fase 7: O Padrão MVC ----
    ['fase' => 7, 'tipo' => 'multipla', 'assunto' => 'mvc',
     'pergunta' => 'Em MVC, onde devem morar as regras de negócio e o acesso a dados?', 'codigo' => null,
     'opcoes' => ['Na View', 'No Model', 'No CSS', 'No HTML'], 'resposta' => 1,
     'explicacao' => 'O Model concentra dados e regras; View só apresenta, Controller só orquestra.', 'dif' => 2],

    ['fase' => 7, 'tipo' => 'vf', 'assunto' => 'mvc',
     'pergunta' => 'Um Controller pode consultar vários Models antes de escolher a View.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'O Controller é o maestro: reúne o que precisar dos Models e entrega à View.', 'dif' => 3],

    ['fase' => 7, 'tipo' => 'multipla', 'assunto' => 'mvc',
     'pergunta' => 'Qual é a principal vantagem de separar em Model, View e Controller?', 'codigo' => null,
     'opcoes' => ['Deixa o site mais rápido sempre', 'Separa responsabilidades, facilitando manutenção e testes', 'Elimina a necessidade de banco', 'Dispensa o HTML'],
     'resposta' => 1,
     'explicacao' => 'Separação de responsabilidades: cada parte muda por um motivo só.', 'dif' => 3],

    ['fase' => 7, 'tipo' => 'multipla', 'assunto' => 'mvc',
     'pergunta' => 'Na rota index.php?url=perfil/editar/7, qual é o controller?', 'codigo' => null,
     'opcoes' => ['perfil', 'editar', '7', 'index'], 'resposta' => 0,
     'explicacao' => 'No padrão controller/metodo/parametro: "perfil" é o controller, "editar" o método e 7 o parâmetro.', 'dif' => 3],

    ['fase' => 7, 'tipo' => 'completar', 'assunto' => 'mvc',
     'pergunta' => 'Complete: a camada do MVC que renderiza o HTML para o usuário é a ___', 'codigo' => null,
     'opcoes' => null, 'resposta' => ['View', 'view', 'Visão'],
     'explicacao' => 'A View cuida só da apresentação — nada de SQL ou regra de negócio nela.', 'dif' => 2],

    ['fase' => 7, 'tipo' => 'erro', 'assunto' => 'mvc',
     'pergunta' => 'Qual prática QUEBRA a separação do MVC?', 'codigo' => null,
     'opcoes' => ['O Model acessar o banco', 'O Controller chamar o Model', 'A View conter SQL e regras de negócio', 'A View apenas exibir os dados recebidos'],
     'resposta' => 2,
     'explicacao' => 'SQL e lógica na View misturam as camadas — o caminho mais curto para o espaguete.', 'dif' => 3],

    // ---- Fase 8: O Baú do SELECT (SQL, secundária) ----
    ['fase' => 8, 'tipo' => 'multipla', 'assunto' => 'sql',
     'pergunta' => 'Qual comando insere um novo registro numa tabela?', 'codigo' => null,
     'opcoes' => ['SELECT', 'INSERT', 'DROP', 'WHERE'], 'resposta' => 1,
     'explicacao' => 'INSERT INTO ... VALUES ... adiciona uma nova linha.', 'dif' => 2],

    ['fase' => 8, 'tipo' => 'completar', 'assunto' => 'sql',
     'pergunta' => 'Complete para ordenar os usuários pelo nome:', 'codigo' => 'SELECT * FROM usuarios ORDER ___ nome;',
     'opcoes' => null, 'resposta' => ['BY', 'by'],
     'explicacao' => 'ORDER BY coluna define a ordenação do resultado.', 'dif' => 2],

    ['fase' => 8, 'tipo' => 'multipla', 'assunto' => 'sql',
     'pergunta' => 'Qual cláusula restringe a consulta a no máximo 10 linhas?', 'codigo' => null,
     'opcoes' => ['TOP', 'LIMIT', 'MAX', 'FIRST'], 'resposta' => 1,
     'explicacao' => 'Em MySQL, LIMIT 10 corta o resultado nas 10 primeiras linhas.', 'dif' => 2],

    ['fase' => 8, 'tipo' => 'multipla', 'assunto' => 'sql',
     'pergunta' => 'O que acontece com UPDATE usuarios SET ativo = 0; (sem WHERE)?', 'codigo' => null,
     'opcoes' => ['Atualiza só a primeira linha', 'Não faz nada', 'Atualiza TODAS as linhas da tabela', 'Gera erro de sintaxe'],
     'resposta' => 2,
     'explicacao' => 'Sem WHERE, o UPDATE atinge a tabela inteira. Respeite o WHERE — ou chore depois.', 'dif' => 3],

    // ---- Fase 9: Parse Error, o Kraken (chefe — PHP + MVC + SQL) ----
    ['fase' => 9, 'tipo' => 'erro', 'assunto' => 'php',
     'pergunta' => 'O Kraken roubou um símbolo. Onde está o Parse Error?', 'codigo' => '$arr = [1, 2, 3;',
     'opcoes' => ['Falta fechar o colchete ]', 'Falta uma vírgula', 'Falta o $', 'Não há erro'],
     'resposta' => 0,
     'explicacao' => 'O array abriu com [ mas nunca fechou com ]: o ; chegou cedo demais.', 'dif' => 3],

    ['fase' => 9, 'tipo' => 'multipla', 'assunto' => 'php',
     'pergunta' => 'Qual a saída?', 'codigo' => '$a = [10, 20, 30];' . "\n" . 'echo count($a);',
     'opcoes' => ['2', '3', '60', '0'], 'resposta' => 1,
     'explicacao' => 'count() devolve a QUANTIDADE de elementos: o array tem 3.', 'dif' => 3],

    ['fase' => 9, 'tipo' => 'multipla', 'assunto' => 'php',
     'pergunta' => 'Qual operador compara valor E tipo (comparação estrita)?', 'codigo' => null,
     'opcoes' => ['==', '===', '=', '<>'], 'resposta' => 1,
     'explicacao' => '=== exige que valor e tipo sejam iguais; "5" === 5 é falso.', 'dif' => 3],

    ['fase' => 9, 'tipo' => 'vf', 'assunto' => 'php',
     'pergunta' => 'Em PHP, um parâmetro pode ter valor padrão: function taxa($v, $pct = 10).', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'Parâmetros com valor padrão tornam o argumento opcional na chamada.', 'dif' => 3],

    ['fase' => 9, 'tipo' => 'ordenar', 'assunto' => 'mvc',
     'pergunta' => 'Ordene o fluxo de uma requisição em MVC:', 'codigo' => null,
     'opcoes' => ['A View renderiza o HTML', 'O Controller recebe a requisição', 'O Model busca os dados'],
     'resposta' => [1, 2, 0],
     'explicacao' => 'Controller recebe → Model busca os dados → View renderiza a resposta.', 'dif' => 4],

    ['fase' => 9, 'tipo' => 'completar', 'assunto' => 'php',
     'pergunta' => 'Complete para a função devolver a soma:', 'codigo' => 'function soma($a, $b) { ___ $a + $b; }',
     'opcoes' => null, 'resposta' => ['return'],
     'explicacao' => 'return entrega o valor de volta a quem chamou a função.', 'dif' => 3],
];
