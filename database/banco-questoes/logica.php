<?php
/**
 * Banco de questões — Vila Hello World (fundamentos de Lógica e Algoritmos).
 * Pool ampliado das fases 2 e 3. Sabor leve; explicações corretas.
 *
 * Formato de cada linha (consumido por database/seed-banco-questoes.php):
 *   fase, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dif
 *   - multipla/erro : resposta = índice (int) da opção correta
 *   - vf            : resposta = bool
 *   - completar     : resposta = lista de strings aceitas
 *   - ordenar       : resposta = lista de índices na ordem correta
 */

return [
    // ---- Fase 2: Os Primeiros Passos (lição) ----
    ['fase' => 2, 'tipo' => 'multipla', 'assunto' => 'logica',
     'pergunta' => 'Quanto vale 10 - 2 * 3?', 'codigo' => null,
     'opcoes' => ['24', '4', '18', '6'], 'resposta' => 1,
     'explicacao' => 'A multiplicação vem antes: 2*3 = 6, depois 10 - 6 = 4.', 'dif' => 1],

    ['fase' => 2, 'tipo' => 'multipla', 'assunto' => 'logica',
     'pergunta' => 'O que é um algoritmo?', 'codigo' => null,
     'opcoes' => ['Um tipo de computador', 'Uma sequência finita de passos para resolver um problema', 'Uma linguagem de programação', 'Um erro no código'],
     'resposta' => 1,
     'explicacao' => 'Algoritmo é uma receita: passos finitos e bem definidos que levam a um resultado.', 'dif' => 1],

    ['fase' => 2, 'tipo' => 'vf', 'assunto' => 'logica',
     'pergunta' => 'Uma variável pode trocar de valor durante a execução do programa.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'Sim — é justamente por isso que ela se chama "variável".', 'dif' => 1],

    ['fase' => 2, 'tipo' => 'multipla', 'assunto' => 'logica',
     'pergunta' => 'Qual operador compara se dois valores são iguais?', 'codigo' => null,
     'opcoes' => ['=', '==', '=>', '+='], 'resposta' => 1,
     'explicacao' => 'Um = atribui valor; == compara. Confundir os dois é o bug mais clássico do reino.', 'dif' => 2],

    ['fase' => 2, 'tipo' => 'completar', 'assunto' => 'logica',
     'pergunta' => 'Complete o operador que verifica se a é MENOR que b:', 'codigo' => 'se (a ___ b) entao ...',
     'opcoes' => null, 'resposta' => ['<'],
     'explicacao' => 'O operador < testa se o valor da esquerda é menor que o da direita.', 'dif' => 1],

    ['fase' => 2, 'tipo' => 'vf', 'assunto' => 'logica',
     'pergunta' => 'Em muitas linguagens, o número 0 é tratado como "falso" numa condição.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'Zero costuma valer "falso"; qualquer outro número, "verdadeiro".', 'dif' => 2],

    ['fase' => 2, 'tipo' => 'ordenar', 'assunto' => 'logica',
     'pergunta' => 'Ordene os passos para trocar uma lâmpada queimada:', 'codigo' => null,
     'opcoes' => ['Colocar a lâmpada nova', 'Desligar o interruptor', 'Remover a lâmpada queimada'],
     'resposta' => [1, 2, 0],
     'explicacao' => 'Primeiro segurança (desligar), depois remover a velha e por fim instalar a nova.', 'dif' => 2],

    // ---- Fase 3: O Bug Primordial (chefe) ----
    ['fase' => 3, 'tipo' => 'multipla', 'assunto' => 'logica',
     'pergunta' => 'Quantas vezes executa um laço que vai de i = 0 enquanto i < 5?', 'codigo' => null,
     'opcoes' => ['4', '5', '6', 'infinito'], 'resposta' => 1,
     'explicacao' => 'i assume 0, 1, 2, 3, 4 — cinco repetições antes de i < 5 ficar falso.', 'dif' => 2],

    ['fase' => 3, 'tipo' => 'multipla', 'assunto' => 'logica',
     'pergunta' => 'O que é a "condição de parada" de um laço?', 'codigo' => null,
     'opcoes' => ['O comando que inicia o laço', 'O teste que, quando falha, encerra a repetição', 'A primeira linha do programa', 'Um tipo de variável'],
     'resposta' => 1,
     'explicacao' => 'É o teste que o laço avalia a cada volta; quando ele fica falso, o laço para.', 'dif' => 3],

    ['fase' => 3, 'tipo' => 'vf', 'assunto' => 'logica',
     'pergunta' => 'Um fluxograma é uma forma visual de representar um algoritmo.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'Sim: caixas e setas mostram o fluxo de decisões e ações.', 'dif' => 2],

    ['fase' => 3, 'tipo' => 'erro', 'assunto' => 'logica',
     'pergunta' => 'Por que este laço nunca termina?', 'codigo' => "i = 0\nenquanto i < 3:\n    mostrar i",
     'opcoes' => ['Falta mostrar o i', 'i nunca é incrementado, então i < 3 é sempre verdadeiro', 'O laço começa em 0', 'Não há erro'],
     'resposta' => 1,
     'explicacao' => 'Sem um i = i + 1 dentro do laço, a condição nunca fica falsa: laço infinito.', 'dif' => 3],

    ['fase' => 3, 'tipo' => 'completar', 'assunto' => 'logica',
     'pergunta' => 'Complete a estrutura que escolhe um caminho conforme uma condição:', 'codigo' => '___ (idade >= 18) entao mostrar "maior"',
     'opcoes' => null, 'resposta' => ['se', 'if'],
     'explicacao' => 'A estrutura condicional (se / if) executa um bloco somente quando a condição é verdadeira.', 'dif' => 2],

    ['fase' => 3, 'tipo' => 'ordenar', 'assunto' => 'logica',
     'pergunta' => 'Ordene as etapas de resolver um problema de programação:', 'codigo' => null,
     'opcoes' => ['Testar o resultado', 'Entender o problema', 'Escrever o código', 'Planejar a solução'],
     'resposta' => [1, 3, 2, 0],
     'explicacao' => 'Entender → planejar → codificar → testar. Pular o "entender" é como debugar no escuro.', 'dif' => 3],
];
