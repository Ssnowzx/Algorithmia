<?php
/**
 * Banco de questões — Cesar, o Oráculo do Ritmo (Montanha do Cálculo).
 * Cálculo para funções de uma e de múltiplas variáveis: sequências, limites,
 * derivadas, integrais e derivadas parciais. Assunto próprio: 'calculo'.
 * Pool ampliado das fases 23, 24, 25 e 27 (chefe Limite, o Colosso).
 */

return [
    // ---- Fase 23: Sequências e Ritmo (limites de sequências) ----
    ['fase' => 23, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'O limite da sequência aₙ = 1/n quando n tende ao infinito é:', 'codigo' => null,
     'opcoes' => ['1', '0', 'infinito', 'n'], 'resposta' => 1,
     'explicacao' => 'Quanto maior o n, menor 1/n; o termo se aproxima de 0.', 'dif' => 2],

    ['fase' => 23, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'A sequência aₙ = (n + 1)/n, quando n tende ao infinito, tende a:', 'codigo' => null,
     'opcoes' => ['0', '1', 'infinito', '2'], 'resposta' => 1,
     'explicacao' => '(n+1)/n = 1 + 1/n; como 1/n → 0, a sequência tende a 1.', 'dif' => 3],

    ['fase' => 23, 'tipo' => 'vf', 'assunto' => 'calculo',
     'pergunta' => 'Numa progressão aritmética, a diferença entre termos consecutivos é constante.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'Essa diferença constante é a razão da PA.', 'dif' => 2],

    ['fase' => 23, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'Qual sequência CONVERGE (tem limite finito)?', 'codigo' => null,
     'opcoes' => ['1, 2, 3, 4, ...', '2, 4, 8, 16, ...', '1, 1/2, 1/3, 1/4, ...', '1, 2, 4, 8, ...'],
     'resposta' => 2,
     'explicacao' => '1/n tende a 0 (converge); as demais crescem sem limite.', 'dif' => 3],

    ['fase' => 23, 'tipo' => 'completar', 'assunto' => 'calculo',
     'pergunta' => 'Numa PA de primeiro termo 3 e razão 5, o termo geral é aₙ = 3 + (n - 1)·___', 'codigo' => null,
     'opcoes' => null, 'resposta' => ['5'],
     'explicacao' => 'O termo geral da PA é a₁ + (n−1)·r, com r = 5.', 'dif' => 2],

    ['fase' => 23, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'A soma infinita 1 + 1/2 + 1/4 + 1/8 + ... converge para:', 'codigo' => null,
     'opcoes' => ['1', '2', 'infinito', '1/2'], 'resposta' => 1,
     'explicacao' => 'Série geométrica de razão 1/2: a soma é 1/(1 − 1/2) = 2.', 'dif' => 4],

    // ---- Fase 24: Recursão e Limites ----
    ['fase' => 24, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'Quanto vale o limite, quando x tende a 2, de (x² − 4)/(x − 2)?', 'codigo' => null,
     'opcoes' => ['0', '2', '4', 'indefinido'], 'resposta' => 2,
     'explicacao' => 'x² − 4 = (x − 2)(x + 2); cancelando (x − 2) sobra x + 2, que em x=2 vale 4.', 'dif' => 4],

    ['fase' => 24, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'Uma função é CONTÍNUA num ponto quando:', 'codigo' => null,
     'opcoes' => ['O gráfico tem um salto ali', 'O limite no ponto existe e é igual ao valor da função', 'A função não está definida ali', 'A derivada é zero'],
     'resposta' => 1,
     'explicacao' => 'Continuidade: o limite existe, a função existe e os dois coincidem no ponto.', 'dif' => 4],

    ['fase' => 24, 'tipo' => 'vf', 'assunto' => 'calculo',
     'pergunta' => 'O limite de (sen x)/x quando x tende a 0 é igual a 1.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'É o limite fundamental trigonométrico, base de várias derivadas.', 'dif' => 4],

    ['fase' => 24, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'O limite de 1/x² quando x tende ao infinito é:', 'codigo' => null,
     'opcoes' => ['infinito', '1', '0', '−1'], 'resposta' => 2,
     'explicacao' => 'O denominador cresce sem limite, então a fração tende a 0.', 'dif' => 3],

    ['fase' => 24, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'Encontrar 0/0 ao calcular um limite significa que:', 'codigo' => null,
     'opcoes' => ['O limite é sempre 0', 'É uma indeterminação: é preciso manipular (fatorar/simplificar) a expressão', 'O limite não existe nunca', 'A função é contínua'],
     'resposta' => 1,
     'explicacao' => '0/0 é indeterminação; fatorar, simplificar ou usar L\'Hôpital costuma resolver.', 'dif' => 4],

    ['fase' => 24, 'tipo' => 'completar', 'assunto' => 'calculo',
     'pergunta' => 'Calcule o limite quando x tende a 3 de (x + 1) = ___', 'codigo' => null,
     'opcoes' => null, 'resposta' => ['4'],
     'explicacao' => 'A função é contínua: basta substituir x por 3 → 3 + 1 = 4.', 'dif' => 3],

    // ---- Fase 25: Complexidade e Crescimento (derivadas) ----
    ['fase' => 25, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'A derivada de uma função mede:', 'codigo' => null,
     'opcoes' => ['A área sob a curva', 'A taxa de variação instantânea da função', 'O valor máximo da função', 'O número de raízes'],
     'resposta' => 1,
     'explicacao' => 'A derivada é a inclinação da reta tangente: a rapidez com que a função muda naquele ponto.', 'dif' => 3],

    ['fase' => 25, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'A derivada de f(x) = x² é:', 'codigo' => null,
     'opcoes' => ['x', '2x', 'x²', '2'], 'resposta' => 1,
     'explicacao' => 'Pela regra do tombo, derivada de xⁿ é n·xⁿ⁻¹: aqui 2·x¹ = 2x.', 'dif' => 3],

    ['fase' => 25, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'A derivada de uma função constante f(x) = 7 é:', 'codigo' => null,
     'opcoes' => ['7', '1', '0', 'x'], 'resposta' => 2,
     'explicacao' => 'Uma constante não varia, então sua taxa de variação (derivada) é 0.', 'dif' => 3],

    ['fase' => 25, 'tipo' => 'vf', 'assunto' => 'calculo',
     'pergunta' => 'Se f\'(x) > 0 em todo um intervalo, então f é crescente nesse intervalo.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'Derivada positiva = inclinação para cima = função crescente.', 'dif' => 4],

    ['fase' => 25, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'A derivada de f(x) = 3x é:', 'codigo' => null,
     'opcoes' => ['3x', '3', 'x', '0'], 'resposta' => 1,
     'explicacao' => 'A derivada de uma reta a·x é a inclinação a; aqui, 3.', 'dif' => 3],

    ['fase' => 25, 'tipo' => 'completar', 'assunto' => 'calculo',
     'pergunta' => 'Pela regra do tombo, a derivada de x³ é ___·x²', 'codigo' => null,
     'opcoes' => null, 'resposta' => ['3'],
     'explicacao' => 'Derivada de xⁿ = n·xⁿ⁻¹; para n=3 dá 3x².', 'dif' => 4],

    // ---- Fase 27: Limite, o Colosso (chefe — integrais e múltiplas variáveis) ----
    ['fase' => 27, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'A integração é a operação inversa da:', 'codigo' => null,
     'opcoes' => ['Soma', 'Derivação', 'Raiz quadrada', 'Potenciação'], 'resposta' => 1,
     'explicacao' => 'Integral e derivada são operações inversas (Teorema Fundamental do Cálculo).', 'dif' => 4],

    ['fase' => 27, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'Quanto vale a integral indefinida ∫ 2x dx?', 'codigo' => null,
     'opcoes' => ['2 + C', 'x² + C', '2x² + C', 'x + C'], 'resposta' => 1,
     'explicacao' => 'A primitiva de 2x é x² (pois a derivada de x² é 2x), mais a constante C.', 'dif' => 4],

    ['fase' => 27, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'Na derivada PARCIAL em x de f(x, y), a variável y é tratada como:', 'codigo' => null,
     'opcoes' => ['Variável', 'Zero', 'Constante', 'Infinito'], 'resposta' => 2,
     'explicacao' => 'Deriva-se em relação a x mantendo y fixo (constante) — base do cálculo multivariável.', 'dif' => 4],

    ['fase' => 27, 'tipo' => 'vf', 'assunto' => 'calculo',
     'pergunta' => 'A integral definida pode ser interpretada como a área sob a curva da função.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'A integral definida soma "fatias infinitesimais", resultando na área entre a curva e o eixo.', 'dif' => 3],

    ['fase' => 27, 'tipo' => 'multipla', 'assunto' => 'calculo',
     'pergunta' => 'A derivada parcial ∂/∂x de f(x, y) = x²·y é:', 'codigo' => null,
     'opcoes' => ['x²', '2xy', '2x', 'y'], 'resposta' => 1,
     'explicacao' => 'Tratando y como constante: ∂/∂x (x²·y) = 2x·y = 2xy.', 'dif' => 4],

    ['fase' => 27, 'tipo' => 'completar', 'assunto' => 'calculo',
     'pergunta' => 'A constante C somada numa integral indefinida é a constante de ___.', 'codigo' => null,
     'opcoes' => null, 'resposta' => ['integração', 'integracao'],
     'explicacao' => 'Como a derivada de qualquer constante é 0, toda primitiva carrega o "+ C".', 'dif' => 4],
];
