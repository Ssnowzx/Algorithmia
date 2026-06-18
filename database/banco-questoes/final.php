<?php
/**
 * Banco de questões — O Abismo do /dev/null (Lorde Segfault & a IA Ancestral).
 * Confronto final: mistura das cinco disciplinas. Pool ampliado da fase 35.
 * Dificuldade alta (3-4): é a prova de que o aprendiz dominou o reino inteiro.
 */

return [
    ['fase' => 35, 'tipo' => 'multipla', 'assunto' => 'mvc',
     'pergunta' => 'Num bom design MVC, o que a View deve receber do Controller?', 'codigo' => null,
     'opcoes' => ['Conexões abertas com o banco', 'Apenas os dados já prontos para exibir', 'As queries SQL para executar', 'As regras de negócio'],
     'resposta' => 1,
     'explicacao' => 'A View só apresenta: recebe dados prontos, sem SQL nem lógica de negócio.', 'dif' => 4],

    ['fase' => 35, 'tipo' => 'multipla', 'assunto' => 'poo',
     'pergunta' => 'O encapsulamento serve principalmente para:', 'codigo' => null,
     'opcoes' => ['Deixar todos os atributos públicos', 'Proteger o estado interno, expondo só o necessário', 'Eliminar métodos', 'Acelerar o banco'],
     'resposta' => 1,
     'explicacao' => 'Esconder o estado e expor uma interface controlada é a essência do encapsulamento.', 'dif' => 3],

    ['fase' => 35, 'tipo' => 'multipla', 'assunto' => 'estruturas',
     'pergunta' => 'Qual estrutura segue o princípio LIFO (último a entrar, primeiro a sair)?', 'codigo' => null,
     'opcoes' => ['Fila', 'Pilha', 'Lista ordenada', 'Árvore balanceada'], 'resposta' => 1,
     'explicacao' => 'A pilha é LIFO; a fila é FIFO.', 'dif' => 3],

    ['fase' => 35, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'O código de status HTTP 404 significa:', 'codigo' => null,
     'opcoes' => ['Sucesso', 'Recurso não encontrado', 'Erro interno do servidor', 'Acesso proibido'],
     'resposta' => 1,
     'explicacao' => '404 Not Found: o recurso pedido não existe naquele endereço.', 'dif' => 3],

    ['fase' => 35, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'A derivada de f(x) = x² é:', 'codigo' => null,
     'opcoes' => ['x', '2x', '2', 'x³'], 'resposta' => 1,
     'explicacao' => 'Regra do tombo: derivada de x² é 2x — a taxa de variação que cresce com x.', 'dif' => 4],

    ['fase' => 35, 'tipo' => 'vf', 'assunto' => 'logica',
     'pergunta' => 'Todo algoritmo deve terminar após um número finito de passos.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'Finitude é parte da definição de algoritmo; do contrário, é um laço infinito disfarçado.', 'dif' => 3],

    ['fase' => 35, 'tipo' => 'erro', 'assunto' => 'php',
     'pergunta' => 'O último bug do Lorde Segfault. Qual é a falha?', 'codigo' => "if (\$x == 5) {\n  echo \"cinco\"\n}",
     'opcoes' => ['Falta o ; após echo "cinco"', 'Deveria ser = em vez de ==', 'Falta o $ em x', 'Não há erro'],
     'resposta' => 0,
     'explicacao' => 'A instrução echo precisa terminar com ponto e vírgula — o clássico que dá Parse Error.', 'dif' => 4],

    ['fase' => 35, 'tipo' => 'multipla', 'assunto' => 'sql',
     'pergunta' => 'Qual cláusula AGRUPA linhas para usar com funções agregadas como COUNT()?', 'codigo' => null,
     'opcoes' => ['ORDER BY', 'GROUP BY', 'WHERE', 'LIMIT'], 'resposta' => 1,
     'explicacao' => 'GROUP BY junta linhas por um critério para então contar, somar ou calcular médias por grupo.', 'dif' => 4],
];
