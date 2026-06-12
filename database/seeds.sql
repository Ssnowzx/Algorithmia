-- ============================================================
--  Algorithmia — Dados iniciais (seeds)
--  História, mestres, fases, desafios, itens, conquistas, diálogos.
--  IMPORTANTE: as tabelas são recriadas pelo schema, então os IDs
--  AUTO_INCREMENT são previsíveis na ordem de inserção abaixo.
-- ============================================================

USE algorithmia;

-- ------------------------------------------------------------
-- USUÁRIOS
-- ------------------------------------------------------------
-- Sem contas pré-criadas: cada jogador se registra pelo próprio jogo
-- (papel 'jogador'). Para liberar o Painel do Mestre (admin), promova
-- a conta no MySQL:
--   UPDATE usuarios SET papel = 'mestre' WHERE email = 'seu@email.com';

-- ------------------------------------------------------------
-- MESTRES (ordem = número do capítulo)
-- ------------------------------------------------------------
INSERT INTO mestres (nome, titulo, disciplina, regiao, historia, personalidade, bordao, svg_slug, cor_tema, ordem) VALUES
('Willen Leolatto Carneiro', 'O Arquiteto', 'Laboratório de Programação II', 'Porto da Sintaxe',
 'Willen ergueu o Porto da Sintaxe sobre os escombros do Grande Timeout. Dizem que ele consegue ler um sistema inteiro só de olhar para um arquivo. Carrega sempre uma túnica bordada com chaves angulares < / > e acredita que toda grande aventura começa com um Hello, World bem escrito.',
 'Paciente, metódico e levemente irônico. Valoriza fundamentos acima de atalhos.',
 'Antes de correr, aprenda a indentar.', 'mestre-willen', '#5b8cff', 1),

('Clayton Zambon', 'O Moldador', 'Programação Orientada a Objetos I', 'Cidadela dos Objetos',
 'Na Cidadela dos Objetos, tudo é classe e instância. Clayton, sempre de moletom azul e sorriso tranquilo, ensina que o mundo é feito de moldes — e que reaproveitar é mais sábio do que repetir. Foi ele quem encapsulou os segredos mais perigosos do reino.',
 'Acolhedor e didático. Detesta código duplicado e ama uma boa abstração.',
 'Não copie o comportamento: herde a ideia, componha a solução.', 'mestre-clayton', '#22a6b3', 2),

('Marcelo Goulart Souza', 'O Andarilho', 'Estrutura de Dados', 'Floresta das Estruturas',
 'Marcelo cruza a Floresta das Estruturas a bordo de seu lendário Gol quadrado cinza, o único veículo capaz de atravessar as Árvores Balanceadas. Conhece cada pilha, fila e nó da floresta e mede o tempo do mundo em notação Big-O.',
 'Aventureiro, prático e bem-humorado. Sempre acha um atalho — desde que seja O(log n).',
 'Estrutura errada transforma um passeio em O(n²).', 'mestre-marcelo', '#e1b12c', 3),

('Cesar Augusto Machado Freitas', 'O Oráculo do Ritmo', 'Cálculo II', 'Montanha do Cálculo',
 'No topo da Montanha do Cálculo, Cesar medita com seus headphones, ouvindo o ritmo dos números. Careca, sereno e sempre pronto para um joinha de aprovação, enxerga padrões e sequências onde os outros só veem caos. Foi o primeiro a perceber a aproximação do Lorde Segfault.',
 'Calmo, encorajador e filosófico. Acredita que tudo no universo tem uma taxa de variação.',
 'Toda função tem um limite. Inclusive a sua paciência com a IA.', 'mestre-cesar', '#9c88ff', 4),

('Cassandro Albino Devenz', 'O Mensageiro', 'Redes de Computadores', 'Torre das Conexões',
 'A Torre das Conexões tem sete andares — um para cada camada do modelo OSI. Cassandro, de óculos e sorriso largo, garante que toda mensagem do reino chegue ao destino. Brincalhão, costuma terminar as frases com um animado ...ACK!',
 'Extrovertido, energético e tagarela. Transforma protocolos em piadas.',
 'Mandei a mensagem. Cadê o seu ...ACK?!', 'mestre-cassandro', '#44bd32', 5);

-- ------------------------------------------------------------
-- ITENS (a ordem define os IDs usados em drops e na loja)
-- ------------------------------------------------------------
INSERT INTO itens (nome, descricao, tipo, efeito, preco, svg_slug, raridade, compravel) VALUES
('Adaga de Depuração',      'Curta, afiada e fareja bug a um console.log de distância. +3 de ataque.', 'arma', '{"ataque":3}',     40,  'item-adaga',      'comum',    1),  -- 1
('Espada da Sintaxe',       'Forjada no Porto da Sintaxe e nunca esquece um ponto e vírgula. +7 de ataque.', 'arma', '{"ataque":7}', 120, 'item-espada',     'raro',     1),  -- 2
('Cajado do Compilador',    'Converte intenção em ação. +6 de ataque, +2 de defesa.',           'arma',      '{"ataque":6,"defesa":2}',150,'item-cajado',     'raro',     1),  -- 3
('Machado do Refactor',     'Derruba código morto de uma só vez. +11 de ataque.',               'arma',      '{"ataque":11}',         260, 'item-machado',    'epico',    1),  -- 4
('Lâmina Polimórfica',      'Assume a forma mais eficaz. +14 de ataque.',                       'arma',      '{"ataque":14}',         420, 'item-lamina',     'epico',    1),  -- 5
('Escudo de Try-Catch',     'Captura exceções antes que te atinjam. +5 de defesa.',             'escudo',    '{"defesa":5}',          110, 'item-escudo',     'comum',    1),  -- 6
('Broquel do Validador',    'Rejeita entradas inválidas. +8 de defesa.',                        'escudo',    '{"defesa":8}',          220, 'item-broquel',    'raro',     1),  -- 7
('Égide do Firewall',       'Bloqueia pacotes maliciosos. +13 de defesa.',                      'escudo',    '{"defesa":13}',         400, 'item-egide',      'epico',    1),  -- 8
('Anel do Indentador',      'Mantém tudo no lugar. +2 ataque, +2 defesa.',                      'acessorio', '{"ataque":2,"defesa":2}',90, 'item-anel',       'comum',    1),  -- 9
('Amuleto Big-O',           'Otimiza cada golpe. +5 de ataque.',                                'acessorio', '{"ataque":5}',          180, 'item-amuleto',    'raro',     1),  -- 10
('Elmo do Clean Code',      'Clareza vira proteção. +6 de defesa.',                             'acessorio', '{"defesa":6}',          180, 'item-elmo',       'raro',     1),  -- 11
('Poção de Vida Menor',     'Restaura 40 de HP. Gosto de morango artificial, garantem os alquimistas.', 'pocao', '{"cura_hp":40}',  25,  'item-pocao-hp',   'comum',    1),  -- 12
('Poção de Vida Maior',     'Restaura 90 de HP.',                                               'pocao',     '{"cura_hp":90}',        60,  'item-pocao-hp2',  'raro',     1),  -- 13
('Éter de Mana',            'Restaura 30 de MP.',                                               'pocao',     '{"cura_mp":30}',        45,  'item-pocao-mp',   'comum',    1),  -- 14
('Fragmento da IA Ancestral','Sussurra a resposta perfeita no seu ouvido. Também sussurra que você não precisa estudar, nem dormir, nem ter amigos. Corrói a reputação. Use com a consciência pesada.', 'especial', '{"acerto_automatico":true}',0,'item-fragmento-ia','lendario',0),  -- 15
('Poção de Vida Suprema',   'Restaura 200 de HP.',                                              'pocao',     '{"cura_hp":200}',       140, 'item-pocao-hp3',  'epico',    1),  -- 16
('Bota do Loop Veloz',      'Reflexos acelerados. +3 ataque, +3 defesa.',                       'acessorio', '{"ataque":3,"defesa":3}',150,'item-bota',       'raro',     1),  -- 17
('Espada Lendária do Hello World','A primeiríssima arma do reino. Só imprime "Olá, Mundo", mas faz isso com uma autoridade devastadora. +20 de ataque.', 'arma', '{"ataque":20}', 900, 'item-espada-lendaria','lendario',0);-- 18

-- ------------------------------------------------------------
-- CONQUISTAS
-- ------------------------------------------------------------
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta) VALUES
('primeiro_passo',     'Primeiro Passo',      'Concluiu sua primeira fase. Só faltam várias dezenas. Animado?',  'troxeu-bronze', 0),
('sem_falhas',         'Execução Perfeita',   'Venceu uma fase sem errar nada. Coloca no currículo, vai.',       'troxeu-prata',  0),
('cacador_de_chefes',  'Caçador de Chefes',   'Derrotou um grande chefe. Ele caiu mais fácil que produção numa sexta-feira.', 'troxeu-ouro', 0),
('tentacao',           'A Tentação',          'Usou o Fragmento da IA pela primeira vez. Foi gostoso, né? Sempre é.', 'icone-ia',  0),
('puro_de_coracao',    'Puro de Coração',     'Completou um capítulo inteiro sem cola. Ninguém vai acreditar, mas nós vimos.', 'icone-coracao', 0),
('aprendiz_veterano',  'Aprendiz Veterano',   'Chegou ao nível 5. Praticamente um sênior. (Não é. Continue.)',  'icone-nivel',   0),
('lenda_viva',         'Lenda Viva',          'Chegou ao nível 10. Agora pode explicar recursão no almoço sem ninguém pedir.', 'icone-estrela', 0),
('colecionador',       'Colecionador',        'Juntou 8 itens diferentes. Acumular tranqueira também é uma habilidade.', 'icone-bau', 0),
('mestre_willen',      'Discípulo do Arquiteto','Concluiu o Porto da Sintaxe.',                            'mestre-willen', 0),
('mestre_clayton',     'Discípulo do Moldador', 'Concluiu a Cidadela dos Objetos.',                        'mestre-clayton',0),
('mestre_marcelo',     'Discípulo do Andarilho','Concluiu a Floresta das Estruturas.',                     'mestre-marcelo',0),
('mestre_cesar',       'Discípulo do Oráculo',  'Concluiu a Montanha do Cálculo.',                         'mestre-cesar',  0),
('mestre_cassandro',   'Discípulo do Mensageiro','Concluiu a Torre das Conexões.',                         'mestre-cassandro',0),
('final_mestre',       'O Sexto Mestre',      'Recusou a IA e trouxe equilíbrio ao reino.',                'icone-final',   1),
('final_singularidade','A Singularidade',     'Fundiu-se à IA Ancestral.',                                 'icone-ia',      1),
('final_equilibrio',   'O Copiloto',          'Reescreveu o destino da IA como ferramenta, não muleta.',   'icone-final',   1);

-- ------------------------------------------------------------
-- FASES  (a ordem define os IDs 1..35; requisito_fase_id encadeia o mapa)
-- ------------------------------------------------------------
INSERT INTO fases
(mestre_id, ordem_global, nome, tipo, descricao, inimigo_nome, inimigo_svg, inimigo_hp, inimigo_ataque, xp_recompensa, ouro_recompensa, item_drop_id, requisito_fase_id) VALUES
-- Capítulo 0 — Vila Hello World
(NULL, 1,  'Prólogo: O Despertar',        'historia',    'Você acorda na Vila Hello World sem memória, com um fragmento brilhante na mão.', NULL, NULL, 0, 0, 20, 0, NULL, NULL),       -- 1
(NULL, 2,  'Os Primeiros Passos',         'licao',       'O ancião da vila testa sua lógica antes de deixá-lo partir.',                      'Slime de Sintaxe', 'inimigo-slime', 45, 6, 40, 20, NULL, 1),     -- 2
(NULL, 3,  'O Bug Primordial',            'chefe',       'Uma criatura de código corrompido bloqueia a saída da vila.',                     'Bug Primordial', 'inimigo-bug', 80, 9, 80, 40, 1, 2),            -- 3
-- Capítulo 1 — Porto da Sintaxe (Willen)
(1, 4,  'Chegada ao Porto da Sintaxe',    'historia',    'O Mestre Willen recebe o aprendiz nas docas de código.',                           NULL, NULL, 0, 0, 25, 0, NULL, 3),                                -- 4
(1, 5,  'Variáveis e Eco',                'licao',       'Aprenda a guardar valores e ecoá-los ao mundo em PHP.',                            'Slime de Variável', 'inimigo-slime', 60, 8, 55, 25, NULL, 4),    -- 5
(1, 6,  'Estruturas de Controle',         'licao',       'If, else e laços guardam a ponte do porto.',                                       'Gárgula do If', 'inimigo-gargula', 75, 10, 60, 28, NULL, 5),     -- 6
(1, 7,  'O Padrão MVC',                   'licao',       'Willen revela a arquitetura que sustenta o reino: Model, View, Controller.',       'Espectro do Spaghetti', 'inimigo-espectro', 85, 11, 70, 32, NULL, 6),-- 7
(1, 8,  'O Baú do SELECT',                'secundaria',  'Uma missão opcional nas adegas de dados do porto.',                                'Sentinela SQL', 'inimigo-sentinela', 70, 9, 60, 50, 10, 7),    -- 8
(1, 9,  'Parse Error, o Kraken',          'chefe',       'Das águas emerge o terror dos compiladores: um ponto-e-vírgula a menos.',          'Parse Error, o Kraken', 'inimigo-kraken', 130, 13, 130, 70, 2, 7),-- 9
-- Capítulo 2 — Cidadela dos Objetos (Clayton)
(2, 10, 'A Cidadela dos Objetos',         'historia',    'Clayton apresenta a cidade onde tudo é classe e instância.',                       NULL, NULL, 0, 0, 30, 0, NULL, 9),                               -- 10
(2, 11, 'Classes e Objetos',              'licao',       'Moldes e moldados: a base da orientação a objetos.',                               'Golem de Classe', 'inimigo-golem', 95, 12, 75, 34, NULL, 10),    -- 11
(2, 12, 'Encapsulamento',                 'licao',       'Public, private e protected guardam os segredos dos objetos.',                     'Espião dos Atributos', 'inimigo-espiao', 100, 13, 80, 36, NULL, 11),-- 12
(2, 13, 'Herança vs Composição',          'licao',       'Clayton ensina por que compor costuma vencer herdar.',                             'Quimera da Herança', 'inimigo-quimera', 110, 14, 85, 38, NULL, 12),-- 13
(2, 14, 'Interfaces Secretas',            'secundaria',  'Contratos invisíveis escondem um tesouro.',                                        'Contrato Fantasma', 'inimigo-fantasma', 90, 12, 70, 55, 11, 13),  -- 14
(2, 15, 'A Gárgula God-Class',            'chefe',       'Uma classe que faz tudo — e por isso é monstruosa.',                               'Gárgula God-Class', 'inimigo-godclass', 150, 15, 150, 80, 7, 13),  -- 15
-- Capítulo 3 — Floresta das Estruturas (Marcelo)
(3, 16, 'A Floresta das Estruturas',      'historia',    'Marcelo surge em seu Gol quadrado para guiar o aprendiz.',                         NULL, NULL, 0, 0, 35, 0, NULL, 15),                              -- 16
(3, 17, 'Pilhas e Filas',                 'licao',       'LIFO e FIFO: a ordem importa na floresta.',                                        'Pilha Viva', 'inimigo-pilha', 115, 14, 90, 40, NULL, 16),         -- 17
(3, 18, 'Listas e Nós',                   'licao',       'Cada nó aponta para o próximo na trilha encadeada.',                               'Serpente Encadeada', 'inimigo-serpente', 120, 15, 95, 42, NULL, 17),-- 18
(3, 19, 'Árvores e Big-O',                'licao',       'Medir o tempo do mundo em notação assintótica.',                                   'Ent das Árvores', 'inimigo-ent', 130, 16, 100, 44, NULL, 18),    -- 19
(3, 20, 'O Atalho do Gol Quadrado',       'secundaria',  'Marcelo aposta uma corrida: ache o caminho O(log n).',                             'Eco da Busca Linear', 'inimigo-eco', 100, 13, 80, 60, 17, 19),   -- 20
(3, 21, 'A Hidra Recursiva',              'chefe',       'Corte uma cabeça e duas chamadas recursivas surgem.',                              'Hidra Recursiva', 'inimigo-hidra', 165, 17, 170, 90, 4, 19),     -- 21
-- Capítulo 4 — Montanha do Cálculo (Cesar)
(4, 22, 'A Montanha do Cálculo',          'historia',    'No topo gelado, Cesar ouve o ritmo dos números.',                                  NULL, NULL, 0, 0, 40, 0, NULL, 21),                              -- 22
(4, 23, 'Sequências e Ritmo',             'licao',       'Padrões numéricos que se repetem como batidas.',                                   'Eco Numérico', 'inimigo-eco', 135, 16, 105, 46, NULL, 22),       -- 23
(4, 24, 'Recursão e Limites',             'licao',       'O que acontece quando uma função chama a si mesma sem fim?',                        'Espiral Infinita', 'inimigo-espiral', 140, 17, 110, 48, NULL, 23),-- 24
(4, 25, 'Complexidade e Crescimento',     'licao',       'Quão rápido um problema cresce conforme a entrada aumenta.',                        'Colosso Menor', 'inimigo-colosso', 145, 18, 115, 50, NULL, 24),   -- 25
(4, 26, 'A Verdade sobre Zero',           'historia',    'Cesar revela quem realmente é o Lorde Segfault.',                                  NULL, NULL, 0, 0, 50, 0, NULL, 25),                              -- 26
(4, 27, 'Limite, o Colosso',              'chefe',       'A criatura que cresce sem parar — a menos que você ache seu limite.',               'Limite, o Colosso', 'inimigo-colosso', 185, 19, 200, 100, 5, 26),-- 27
-- Capítulo 5 — Torre das Conexões (Cassandro)
(5, 28, 'A Torre das Conexões',           'historia',    'Sete andares, sete camadas. Cassandro abre as portas.',                            NULL, NULL, 0, 0, 45, 0, NULL, 27),                              -- 28
(5, 29, 'As Sete Camadas (OSI)',          'licao',       'Do físico ao aplicativo: as camadas do modelo OSI.',                               'Sentinela da Camada', 'inimigo-sentinela', 150, 18, 120, 52, NULL, 28),-- 29
(5, 30, 'IP, DNS e Rotas',                'licao',       'Como uma mensagem encontra o caminho até o destino.',                              'Roteador Selvagem', 'inimigo-roteador', 155, 19, 125, 54, NULL, 29),-- 30
(5, 31, 'TCP, UDP e HTTP',                'licao',       'Confiável ou veloz? Os protocolos que movem o reino.',                             'Pacote Corrompido', 'inimigo-pacote', 160, 20, 130, 56, NULL, 30),  -- 31
(5, 32, 'O Pacote Perdido',               'secundaria',  'Um pacote sumiu na rede. Recupere-o antes do timeout.',                            'Eco do Timeout', 'inimigo-eco', 130, 16, 100, 70, 8, 31),       -- 32
(5, 33, 'DDoS, o Enxame',                 'chefe',       'Milhares de requisições falsas tentam derrubar a torre.',                          'DDoS, o Enxame', 'inimigo-ddos', 200, 21, 230, 120, 16, 31),     -- 33
-- Final — O Abismo do /dev/null
(NULL, 34, 'O Abismo do /dev/null',       'historia',    'O caminho final. Lorde Segfault aguarda no vazio.',                                NULL, NULL, 0, 0, 60, 0, NULL, 33),                              -- 34
(NULL, 35, 'Lorde Segfault & a IA Ancestral','chefe_final','O confronto derradeiro decidirá o destino de Algorithmia.',                     'Lorde Segfault', 'inimigo-segfault', 260, 24, 400, 200, 18, 34);  -- 35

-- ------------------------------------------------------------
-- DESAFIOS — Capítulo 0 (Vila Hello World)
-- ------------------------------------------------------------
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade) VALUES
-- Fase 2: Os Primeiros Passos
(2, 1, 'multipla', 'logica', 'Qual é o resultado de 2 + 3 * 4?', NULL, '["20","14","24","9"]', '1', 'A multiplicação tem precedência sobre a soma: 3*4 = 12, depois 12 + 2 = 14.', 1),
(2, 2, 'vf', 'logica', 'A condição (5 > 3) é verdadeira?', NULL, NULL, 'true', 'Sim. 5 é maior que 3, portanto a comparação resulta em verdadeiro.', 1),
(2, 3, 'multipla', 'logica', 'Qual estrutura repete um bloco de comandos várias vezes?', NULL, '["if","laço (loop)","else","return"]', '1', 'Os laços (loops) repetem um bloco enquanto uma condição for satisfeita.', 1),
(2, 4, 'completar', 'logica', 'Para somar 1 ao valor da variável x, complete a expressão:', 'x = x ___ 1', NULL, '["+"]', 'O operador + soma 1 ao valor atual de x, produzindo o incremento.', 1),
-- Fase 3: O Bug Primordial (chefe)
(3, 1, 'multipla', 'logica', 'Quantas vezes executa um laço que vai de i = 1 até 5 (inclusive)?', NULL, '["4","5","6","infinito"]', '1', 'De 1 a 5 inclusive são exatamente 5 repetições.', 2),
(3, 2, 'vf', 'logica', 'Todo algoritmo deve, em algum momento, terminar.', NULL, NULL, 'true', 'Um algoritmo precisa ter fim. Se nunca termina, é um laço infinito — um defeito.', 2),
(3, 3, 'ordenar', 'logica', 'Ordene os passos para preparar um café:', NULL, '["Servir na xícara","Ferver a água","Pegar o pó","Coar a bebida"]', '[2,1,3,0]', 'A sequência lógica é: pegar o pó, ferver a água, coar e servir.', 2),
(3, 4, 'multipla', 'logica', 'O que significa "depurar" (debugar) um programa?', NULL, '["Escrever a documentação","Encontrar e corrigir erros","Apagar o projeto","Trocar de linguagem"]', '1', 'Depurar é o processo de localizar e corrigir defeitos (bugs) no código.', 2),
(3, 5, 'erro', 'logica', 'Qual linha cria um laço infinito?', '1: x = 10\n2: enquanto x > 5:\n3:    mostrar x\n4: fim', '["linha 1","linha 2","linha 3 — x nunca é alterado","linha 4"]', '2', 'Como x nunca diminui dentro do laço, a condição x > 5 nunca se torna falsa: laço infinito.', 2);

-- ------------------------------------------------------------
-- DESAFIOS — Capítulo 1 (Porto da Sintaxe — Willen: PHP, MVC, SQL)
-- ------------------------------------------------------------
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade) VALUES
-- Fase 5: Variáveis e Eco
(5, 1, 'multipla', 'php', 'Em PHP, como se declara uma variável chamada nome?', NULL, '["var nome;","$nome","nome:","let nome"]', '1', 'Em PHP, toda variável começa com o cifrão: $nome.', 1),
(5, 2, 'completar', 'php', 'Complete para exibir um texto na tela em PHP:', '____ "Olá, Algorithmia!";', NULL, '["echo","print"]', 'echo (ou print) envia o texto para a saída em PHP.', 1),
(5, 3, 'multipla', 'php', 'Qual será a saída deste código?', '$x = 5;\n$y = 2;\necho $x . $y;', '["7","52","10","Erro"]', '1', 'O operador . concatena: "5" junto de "2" resulta na string "52".', 2),
(5, 4, 'vf', 'php', 'Em PHP, o operador de concatenação de strings é o ponto (.).', NULL, NULL, 'true', 'Correto: "a" . "b" resulta em "ab".', 1),
-- Fase 6: Estruturas de Controle
(6, 1, 'multipla', 'php', 'Qual valor de $n faz o bloco if ($n % 2 == 0) executar?', NULL, '["3","7","8","5"]', '2', '$n % 2 == 0 testa se o número é par; 8 é par, então a condição é verdadeira.', 2),
(6, 2, 'completar', 'php', 'Complete o laço que conta de 0 a 9:', 'for ($i = 0; $i ___ 10; $i++) { }', NULL, '["<"]', 'A condição $i < 10 mantém o laço executando de 0 até 9.', 2),
(6, 3, 'erro', 'php', 'Qual linha está incorreta?', '1: if ($x > 0) {\n2:    echo "positivo"\n3: }', '["linha 1","linha 2 — falta ponto e vírgula","linha 3","nenhuma"]', '1', 'Toda instrução em PHP termina com ; — está faltando ao final da linha 2.', 2),
(6, 4, 'multipla', 'php', 'Qual estrutura escolhe entre vários casos fixos de uma variável?', NULL, '["for","switch","while","foreach"]', '1', 'switch compara uma variável contra vários valores possíveis (case).', 2),
-- Fase 7: O Padrão MVC
(7, 1, 'multipla', 'mvc', 'No padrão MVC, quem é responsável por acessar o banco de dados?', NULL, '["View","Controller","Model","Router"]', '2', 'O Model encapsula os dados e a comunicação com o banco.', 2),
(7, 2, 'multipla', 'mvc', 'Quem recebe a requisição, usa o Model e escolhe a View?', NULL, '["Controller","Model","View","CSS"]', '0', 'O Controller orquestra o fluxo: trata a requisição, chama o Model e seleciona a View.', 2),
(7, 3, 'vf', 'mvc', 'A View deve conter regras de negócio e comandos SQL.', NULL, NULL, 'false', 'Não. A View apenas apresenta os dados; a lógica e o SQL ficam no Controller e no Model.', 3),
(7, 4, 'ordenar', 'mvc', 'Ordene o fluxo de uma requisição em MVC:', NULL, '["A View renderiza a resposta","O Controller recebe a requisição","O Model busca os dados"]', '[1,2,0]', 'O Controller recebe a requisição, pede os dados ao Model e entrega à View para renderizar.', 3),
-- Fase 8: O Baú do SELECT (secundária)
(8, 1, 'multipla', 'sql', 'Qual comando SQL lê (consulta) registros de uma tabela?', NULL, '["INSERT","SELECT","UPDATE","DELETE"]', '1', 'SELECT recupera dados de uma ou mais tabelas.', 2),
(8, 2, 'completar', 'sql', 'Complete para buscar todas as colunas dos usuários:', 'SELECT ___ FROM usuarios;', NULL, '["*"]', 'O asterisco (*) seleciona todas as colunas da tabela.', 2),
(8, 3, 'multipla', 'sql', 'Qual cláusula filtra as linhas por uma condição?', NULL, '["ORDER BY","WHERE","GROUP BY","LIMIT"]', '1', 'WHERE restringe o resultado às linhas que satisfazem a condição.', 2),
-- Fase 9: Parse Error, o Kraken (chefe)
(9, 1, 'erro', 'php', 'O Kraken roubou um caractere. Qual linha causa o Parse Error?', '1: <?php\n2: $nome = "Aprendiz"\n3: echo $nome;', '["linha 1","linha 2 — falta ;","linha 3","nenhuma"]', '1', 'Falta o ponto e vírgula ao final da linha 2, quebrando a análise do código.', 3),
(9, 2, 'multipla', 'php', 'Qual a saída?', '$a = 3;\n$b = 4;\necho $a + $b;', '["34","7","73","Erro"]', '1', 'Com o operador + os valores são somados numericamente: 3 + 4 = 7 (diferente de concatenar com ponto).', 3),
(9, 3, 'completar', 'php', 'Complete para declarar uma função em PHP:', '________ saudar() { echo "oi"; }', NULL, '["function"]', 'Funções em PHP são declaradas com a palavra-chave function.', 3),
(9, 4, 'vf', 'php', 'Em PHP, == compara apenas valores, enquanto === compara valor e tipo.', NULL, NULL, 'true', '=== é a comparação estrita: exige que valor e tipo sejam iguais.', 3),
(9, 5, 'multipla', 'mvc', 'Na rota index.php?url=prompts/editar/5, qual parte é o método chamado?', NULL, '["prompts","editar","5","index"]', '1', 'No padrão controller/metodo/parametro, "editar" é o método e 5 é o parâmetro.', 3);

-- ------------------------------------------------------------
-- DESAFIOS — Capítulo 2 (Cidadela dos Objetos — Clayton: POO)
-- ------------------------------------------------------------
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade) VALUES
-- Fase 11: Classes e Objetos
(11, 1, 'multipla', 'poo', 'Em POO, o que é uma classe?', NULL, '["Uma instância em memória","Um molde que define atributos e métodos","Uma variável global","Um arquivo de configuração"]', '1', 'A classe é o molde/projeto; descreve atributos e comportamentos dos objetos.', 2),
(11, 2, 'multipla', 'poo', 'E o que é um objeto?', NULL, '["O molde","Uma instância concreta criada a partir da classe","Um tipo de laço","Uma função solta"]', '1', 'O objeto é uma instância concreta, criada a partir da classe.', 2),
(11, 3, 'completar', 'poo', 'Complete para instanciar a classe Heroi em PHP:', '$h = ___ Heroi();', NULL, '["new"]', 'O operador new cria uma nova instância (objeto) de uma classe.', 2),
(11, 4, 'vf', 'poo', 'Métodos são as funções que pertencem a uma classe.', NULL, NULL, 'true', 'Sim: métodos são o comportamento (funções) definido dentro da classe.', 2),
-- Fase 12: Encapsulamento
(12, 1, 'multipla', 'poo', 'Qual modificador torna um atributo acessível somente dentro da própria classe?', NULL, '["public","private","protected","global"]', '1', 'private restringe o acesso ao interior da própria classe.', 2),
(12, 2, 'multipla', 'poo', 'Para que serve o encapsulamento?', NULL, '["Deixar tudo público","Proteger o estado interno e expor só o necessário","Aumentar a duplicação","Eliminar métodos"]', '1', 'Encapsular protege os dados internos, expondo apenas uma interface controlada.', 3),
(12, 3, 'vf', 'poo', 'Atributos protected são acessíveis pela própria classe e por suas subclasses.', NULL, NULL, 'true', 'protected permite acesso na classe e nas que a estendem.', 2),
(12, 4, 'completar', 'poo', 'Complete o método getter que retorna o atributo nome:', 'public function getNome() { return $this->___; }', NULL, '["nome"]', '$this->nome acessa o atributo nome da instância atual.', 3),
-- Fase 13: Herança vs Composição
(13, 1, 'multipla', 'poo', 'Qual palavra-chave indica herança em PHP?', NULL, '["implements","extends","uses","inherits"]', '1', 'class Filha extends Pai cria uma relação de herança.', 2),
(13, 2, 'vf', 'poo', 'Composição é quando um objeto contém outros objetos como parte de seu estado.', NULL, NULL, 'true', 'Compor é montar um objeto a partir de outros (tem-um), em vez de herdar (é-um).', 3),
(13, 3, 'multipla', 'poo', 'Por que costuma-se preferir composição à herança?', NULL, '["Herança é sempre proibida","Composição é mais flexível e evita acoplamento rígido","Composição é só mais rápida de digitar","Herança não existe em PHP"]', '1', 'Composição reduz o acoplamento e dá mais flexibilidade para mudar comportamentos.', 3),
(13, 4, 'ordenar', 'poo', 'Ordene da classe mais genérica para a mais específica:', NULL, '["Mago","Personagem","SerVivo"]', '[2,1,0]', 'SerVivo (geral) → Personagem → Mago (específico).', 3),
-- Fase 14: Interfaces Secretas (secundária)
(14, 1, 'multipla', 'poo', 'O que uma interface define?', NULL, '["A implementação completa","Um contrato de métodos que a classe deve implementar","Atributos privados","Um laço"]', '1', 'A interface é um contrato: lista métodos que a classe se compromete a implementar.', 3),
(14, 2, 'completar', 'poo', 'Complete para a classe assinar o contrato Atacavel:', 'class Heroi ________ Atacavel { }', NULL, '["implements"]', 'implements faz a classe cumprir uma interface.', 3),
(14, 3, 'vf', 'poo', 'Uma classe pode implementar várias interfaces ao mesmo tempo.', NULL, NULL, 'true', 'Sim, diferentemente da herança simples, várias interfaces são permitidas.', 3),
-- Fase 15: A Gárgula God-Class (chefe)
(15, 1, 'multipla', 'poo', 'Por que uma classe-deus (God Class) é um problema?', NULL, '["É pequena demais","Concentra responsabilidades demais e fica difícil de manter","Usa muitas interfaces","Tem poucos métodos"]', '1', 'A God Class viola a coesão: faz coisas demais, ficando frágil e difícil de manter.', 3),
(15, 2, 'multipla', 'poo', 'Qual princípio diz que uma classe deve ter uma única responsabilidade?', NULL, '["DRY","SRP (Single Responsibility)","KISS","YAGNI"]', '1', 'O SRP (Princípio da Responsabilidade Única) pede uma razão única para a classe mudar.', 4),
(15, 3, 'erro', 'poo', 'Qual problema este trecho evidencia?', 'class Tudo {\n  function salvarNoBanco() {}\n  function renderizarHtml() {}\n  function enviarEmail() {}\n}', '["Nada, está ótima","Responsabilidades demais numa só classe","Falta herança","Falta um laço"]', '1', 'Persistência, apresentação e e-mail são responsabilidades distintas: separe em classes.', 3),
(15, 4, 'vf', 'poo', 'Refatorar uma God Class normalmente envolve dividi-la em classes menores e coesas.', NULL, NULL, 'true', 'Quebrar em partes coesas melhora a manutenção e os testes.', 3),
(15, 5, 'multipla', 'poo', 'Qual a saída?', 'class C {\n  public $x = 2;\n  function dobro(){ return $this->x * 2; }\n}\n$c = new C();\necho $c->dobro();', '["2","4","x","Erro"]', '1', '$this->x vale 2, e 2 * 2 = 4.', 4);

-- ------------------------------------------------------------
-- DESAFIOS — Capítulo 3 (Floresta das Estruturas — Marcelo)
-- ------------------------------------------------------------
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade) VALUES
-- Fase 17: Pilhas e Filas
(17, 1, 'multipla', 'estruturas', 'Uma pilha (stack) segue qual princípio?', NULL, '["FIFO","LIFO","aleatório","ordenado"]', '1', 'LIFO: Last In, First Out — o último a entrar é o primeiro a sair.', 2),
(17, 2, 'multipla', 'estruturas', 'Uma fila (queue) segue qual princípio?', NULL, '["LIFO","FIFO","LILO","nenhum"]', '1', 'FIFO: First In, First Out — o primeiro a entrar é o primeiro a sair.', 2),
(17, 3, 'multipla', 'estruturas', 'Qual operação remove o elemento do topo de uma pilha?', NULL, '["push","pop","enqueue","peek"]', '1', 'pop desempilha (remove) o elemento do topo.', 2),
(17, 4, 'ordenar', 'estruturas', 'Empilhei 1, depois 2, depois 3. Em que ordem eles SAEM ao desempilhar?', NULL, '["1","2","3"]', '[2,1,0]', 'Por ser LIFO, sai 3, depois 2, depois 1.', 2),
-- Fase 18: Listas e Nós
(18, 1, 'multipla', 'estruturas', 'Numa lista encadeada, o que cada nó guarda?', NULL, '["Só o valor","O valor e a referência ao próximo nó","Apenas o próximo","O array inteiro"]', '1', 'Cada nó guarda um valor e um ponteiro para o próximo nó.', 2),
(18, 2, 'vf', 'estruturas', 'Inserir um elemento no início de uma lista encadeada é uma operação O(1).', NULL, NULL, 'true', 'Basta ajustar dois ponteiros, sem percorrer a lista: tempo constante.', 3),
(18, 3, 'multipla', 'estruturas', 'Acessar o elemento na posição k de uma lista encadeada custa, no pior caso:', NULL, '["O(1)","O(log n)","O(n)","O(n²)"]', '2', 'É preciso percorrer nó a nó até a posição k: linear, O(n).', 3),
(18, 4, 'completar', 'estruturas', 'Numa lista simples, o último nó aponta para ___ , indicando o fim.', NULL, NULL, '["null","NULL","nulo"]', 'O ponteiro nulo marca que não há próximo nó.', 2),
-- Fase 19: Árvores e Big-O
(19, 1, 'multipla', 'estruturas', 'A busca binária em um vetor ordenado tem complexidade:', NULL, '["O(n)","O(log n)","O(n²)","O(1)"]', '1', 'A cada passo o espaço de busca cai pela metade: O(log n).', 3),
(19, 2, 'multipla', 'estruturas', 'Numa árvore binária de busca balanceada, a busca é, em média:', NULL, '["O(n)","O(log n)","O(n log n)","O(1)"]', '1', 'A altura balanceada é proporcional a log n, então a busca é O(log n).', 3),
(19, 3, 'vf', 'estruturas', 'O(n²) cresce mais rápido que O(n log n) conforme n aumenta.', NULL, NULL, 'true', 'Para n grande, n² supera n log n.', 3),
(19, 4, 'ordenar', 'estruturas', 'Ordene da MAIS rápida (melhor) para a MAIS lenta (pior):', NULL, '["O(n²)","O(1)","O(n)","O(log n)"]', '[1,3,2,0]', 'O(1) < O(log n) < O(n) < O(n²) em ordem de crescimento.', 4),
-- Fase 20: O Atalho do Gol Quadrado (secundária)
(20, 1, 'multipla', 'estruturas', 'Buscar um nome num vetor NÃO ordenado, no pior caso, é:', NULL, '["O(1)","O(log n)","O(n)","O(0)"]', '2', 'Sem ordem, pode ser preciso olhar todos os elementos: O(n).', 3),
(20, 2, 'multipla', 'estruturas', 'Para aplicar busca binária O(log n), o vetor precisa estar:', NULL, '["embaralhado","ordenado","vazio","duplicado"]', '1', 'A busca binária só funciona em dados ordenados.', 3),
(20, 3, 'vf', 'estruturas', 'Em um algoritmo O(log n), dobrar o tamanho da entrada adiciona apenas um passo.', NULL, NULL, 'true', 'É a essência do crescimento logarítmico.', 3),
-- Fase 21: A Hidra Recursiva (chefe)
(21, 1, 'multipla', 'estruturas', 'O que toda função recursiva precisa ter para não rodar infinitamente?', NULL, '["Um laço for","Um caso base (condição de parada)","Uma variável global","Dois parâmetros"]', '1', 'O caso base interrompe a recursão e evita o laço infinito.', 3),
(21, 2, 'multipla', 'estruturas', 'Com fatorial(n) = n * fatorial(n-1) e fatorial(0) = 1, quanto vale fatorial(3)?', NULL, '["3","6","9","1"]', '1', '3 * 2 * 1 = 6.', 3),
(21, 3, 'erro', 'estruturas', 'Por que esta recursão nunca termina?', 'function f($n) {\n  return f($n - 1);\n}', '["Falta retorno","Não tem caso base","Usa subtração","Nada de errado"]', '1', 'Sem uma condição de parada, f chama a si mesma para sempre.', 3),
(21, 4, 'vf', 'estruturas', 'Recursão é quando uma função chama a si mesma.', NULL, NULL, 'true', 'Exatamente: a função se invoca para resolver um subproblema menor.', 2),
(21, 5, 'ordenar', 'estruturas', 'Ordene as chamadas empilhadas de fatorial(3) até a base:', NULL, '["fatorial(0)","fatorial(3)","fatorial(1)","fatorial(2)"]', '[1,3,2,0]', 'fatorial(3) chama (2), que chama (1), que chama (0): a base.', 4);

-- ------------------------------------------------------------
-- DESAFIOS — Capítulo 4 (Montanha do Cálculo — Cesar: lógica)
-- ------------------------------------------------------------
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade) VALUES
-- Fase 23: Sequências e Ritmo
(23, 1, 'multipla', 'logica', 'Qual o próximo número? 2, 4, 6, 8, ...', NULL, '["9","10","12","16"]', '1', 'Progressão aritmética de razão 2: o próximo é 10.', 2),
(23, 2, 'multipla', 'logica', 'Fibonacci: 0, 1, 1, 2, 3, 5, 8, ... qual o próximo?', NULL, '["11","12","13","15"]', '2', 'Cada termo é a soma dos dois anteriores: 5 + 8 = 13.', 3),
(23, 3, 'multipla', 'logica', 'Qual o próximo? 1, 2, 4, 8, 16, ...', NULL, '["24","32","20","18"]', '1', 'Progressão geométrica de razão 2: 16 * 2 = 32.', 2),
(23, 4, 'completar', 'logica', 'Na sequência 5, 10, 15, 20 a razão (diferença constante) é ___', NULL, NULL, '["5"]', 'Cada termo aumenta de 5 em 5.', 2),
-- Fase 24: Recursão e Limites
(24, 1, 'multipla', 'logica', 'Conforme n cresce muito, o valor de 1/n se aproxima de:', NULL, '["infinito","0","1","n"]', '1', 'Quanto maior n, menor 1/n; o limite é 0.', 3),
(24, 2, 'multipla', 'logica', 'A soma 1 + 1/2 + 1/4 + 1/8 + ... se aproxima de qual valor?', NULL, '["1","2","infinito","0"]', '1', 'É uma série geométrica que converge para 2.', 4),
(24, 3, 'vf', 'logica', 'Uma função pode ser definida em termos de si mesma (recursão).', NULL, NULL, 'true', 'Definições recursivas são comuns em matemática e programação.', 2),
(24, 4, 'multipla', 'logica', 'O limite descreve o valor de que uma função se aproxima. Isso lembra qual ideia da computação?', NULL, '["A convergência de uma iteração/sequência","Declarar variáveis","Concatenar strings","Abrir arquivos"]', '0', 'Iterações que se aproximam de um resultado estável são como limites convergindo.', 4),
-- Fase 25: Complexidade e Crescimento
(25, 1, 'multipla', 'logica', 'Qual função cresce mais rápido conforme n aumenta?', NULL, '["n","n²","log n","constante"]', '1', 'Entre as opções, n² tem o maior crescimento.', 3),
(25, 2, 'multipla', 'logica', 'Um algoritmo que DOBRA o trabalho a cada elemento adicional tende a ser:', NULL, '["O(log n)","O(n)","O(2^n) exponencial","O(1)"]', '2', 'Dobrar a cada passo gera crescimento exponencial, O(2^n).', 4),
(25, 3, 'vf', 'logica', 'Para entradas grandes, a ordem de crescimento importa mais que a constante multiplicativa.', NULL, NULL, 'true', 'Big-O ignora constantes porque, no limite, a ordem domina.', 3),
(25, 4, 'ordenar', 'logica', 'Ordene por crescimento, do mais LENTO ao mais RÁPIDO:', NULL, '["O(2^n)","O(n)","O(log n)","O(n²)"]', '[2,1,3,0]', 'log n < n < n² < 2^n.', 4),
-- Fase 27: Limite, o Colosso (chefe)
(27, 1, 'multipla', 'logica', 'O Colosso vale 2^n no turno n. Quanto ele vale no turno 4?', NULL, '["8","16","4","32"]', '1', '2 elevado a 4 é igual a 16.', 4),
(27, 2, 'multipla', 'logica', 'Para achar o limite do Colosso, qual sequência CONVERGE (tem limite finito)?', NULL, '["1, 2, 3, 4, ...","1, 1/2, 1/3, 1/4, ...","2, 4, 8, 16, ...","1, 2, 4, 8, ..."]', '1', '1/n tende a 0: converge. As demais crescem sem limite.', 4),
(27, 3, 'vf', 'logica', 'Um algoritmo O(1) leva o mesmo tempo independente do tamanho da entrada.', NULL, NULL, 'true', 'Tempo constante não depende de n.', 3),
(27, 4, 'multipla', 'logica', 'Qual a saída?', '$s = 0;\nfor ($i = 1; $i <= 4; $i++) {\n  $s += $i;\n}\necho $s;', '["4","10","16","24"]', '1', '1 + 2 + 3 + 4 = 10.', 3),
(27, 5, 'erro', 'logica', 'Por que este cálculo de média está incorreto?', '$soma = 10 + 20 + 30;\n$media = $soma / 2;', '["A soma está errada","Divide por 2 em vez de 3 (a quantidade de valores)","Falta ponto e vírgula","Não há erro"]', '1', 'São 3 valores; a média deve dividir por 3, não por 2.', 4);

-- ------------------------------------------------------------
-- DESAFIOS — Capítulo 5 (Torre das Conexões — Cassandro: redes)
-- ------------------------------------------------------------
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade) VALUES
-- Fase 29: As Sete Camadas (OSI)
(29, 1, 'multipla', 'redes', 'Quantas camadas tem o modelo OSI?', NULL, '["4","5","7","9"]', '2', 'O modelo OSI possui 7 camadas.', 3),
(29, 2, 'multipla', 'redes', 'Qual camada lida com o cabo, sinal e meio físico?', NULL, '["Aplicação","Física","Transporte","Rede"]', '1', 'A camada Física trata da transmissão de bits no meio.', 3),
(29, 3, 'multipla', 'redes', 'Em qual camada atuam protocolos como HTTP, DNS e FTP?', NULL, '["Física","Enlace","Transporte","Aplicação"]', '3', 'Esses protocolos vivem na camada de Aplicação.', 3),
(29, 4, 'ordenar', 'redes', 'Ordene da camada 1 para a 3 do modelo OSI:', NULL, '["Rede","Física","Enlace"]', '[1,2,0]', 'Camada 1 Física, 2 Enlace, 3 Rede.', 4),
-- Fase 30: IP, DNS e Rotas
(30, 1, 'multipla', 'redes', 'Para que serve o DNS?', NULL, '["Criptografar senhas","Traduzir nomes (ex.: site.com) em endereços IP","Comprimir imagens","Soldar cabos"]', '1', 'O DNS resolve nomes de domínio em endereços IP.', 3),
(30, 2, 'multipla', 'redes', 'Qual destes é um endereço IPv4 válido?', NULL, '["256.1.1.1","192.168.0.1","abc.def","12.34"]', '1', 'Cada octeto vai de 0 a 255; 192.168.0.1 é válido (256 não é).', 3),
(30, 3, 'vf', 'redes', 'O endereço 127.0.0.1 (localhost) refere-se à própria máquina.', NULL, NULL, 'true', 'É o endereço de loopback: a máquina falando consigo mesma.', 2),
(30, 4, 'multipla', 'redes', 'Qual equipamento encaminha pacotes entre redes diferentes?', NULL, '["switch","roteador","hub","monitor"]', '1', 'O roteador decide as rotas entre redes distintas.', 3),
-- Fase 31: TCP, UDP e HTTP
(31, 1, 'multipla', 'redes', 'Qual protocolo é confiável e garante entrega ordenada dos pacotes?', NULL, '["UDP","TCP","IP","DNS"]', '1', 'O TCP confirma e reordena pacotes, garantindo a entrega.', 3),
(31, 2, 'multipla', 'redes', 'Qual protocolo é mais rápido, porém sem garantia de entrega (bom para streaming)?', NULL, '["TCP","UDP","FTP","SMTP"]', '1', 'O UDP é leve e veloz, sem o overhead de confirmação do TCP.', 3),
(31, 3, 'multipla', 'redes', 'O código de status HTTP 404 significa:', NULL, '["Sucesso","Recurso não encontrado","Erro do servidor","Redirecionamento"]', '1', '404 indica que o recurso solicitado não foi encontrado.', 3),
(31, 4, 'multipla', 'redes', 'O código de status HTTP 200 significa:', NULL, '["Não encontrado","OK / sucesso","Proibido","Erro interno"]', '1', '200 OK indica que a requisição foi bem-sucedida.', 2),
-- Fase 32: O Pacote Perdido (secundária)
(32, 1, 'multipla', 'redes', 'No TCP, o que confirma que um pacote chegou ao destino?', NULL, '["Um ACK (acknowledgement)","Um DNS","Um cookie","Um ping infinito"]', '0', 'O receptor envia um ACK confirmando o recebimento.', 3),
(32, 2, 'vf', 'redes', 'Se um pacote TCP se perde no caminho, ele pode ser retransmitido.', NULL, NULL, 'true', 'O TCP detecta a perda (falta de ACK) e retransmite.', 3),
(32, 3, 'multipla', 'redes', 'Um timeout acontece quando:', NULL, '["A resposta chega cedo demais","A resposta não chega no tempo esperado","O IP é válido","O DNS resolve corretamente"]', '1', 'Timeout é o estouro do tempo de espera por uma resposta.', 3),
-- Fase 33: DDoS, o Enxame (chefe)
(33, 1, 'multipla', 'redes', 'O que é um ataque DDoS?', NULL, '["Roubo de senha","Sobrecarregar um servidor com requisições em massa","Apagar o banco de dados","Criptografar arquivos por resgate"]', '1', 'DDoS inunda o alvo com tráfego para esgotar seus recursos.', 4),
(33, 2, 'multipla', 'redes', 'Qual mecanismo ajuda a barrar tráfego malicioso na borda da rede?', NULL, '["Firewall","Compilador","Debugger","Laço for"]', '0', 'O firewall filtra o tráfego conforme regras de segurança.', 3),
(33, 3, 'vf', 'redes', 'Em HTTP, o método GET busca dados e o POST normalmente envia dados ao servidor.', NULL, NULL, 'true', 'GET recupera; POST submete dados (ex.: formulários).', 3),
(33, 4, 'multipla', 'redes', 'Qual a porta padrão do HTTPS?', NULL, '["80","21","443","25"]', '2', 'HTTPS usa a porta 443 (HTTP usa a 80).', 4),
(33, 5, 'multipla', 'redes', 'Cliente faz uma requisição e a rota não existe. Qual status o servidor costuma retornar?', NULL, '["200","301","404","500"]', '2', 'Rota inexistente normalmente retorna 404 Not Found.', 3);

-- ------------------------------------------------------------
-- DESAFIOS — Final (Lorde Segfault & a IA Ancestral — mix de tudo)
-- ------------------------------------------------------------
INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade) VALUES
(35, 1, 'multipla', 'mvc', 'No padrão MVC, quem NÃO deve conter comandos SQL?', NULL, '["Model","View","Controller","Repositório"]', '1', 'A View apenas apresenta; SQL pertence ao Model.', 4),
(35, 2, 'multipla', 'poo', 'Qual palavra-chave realiza herança em PHP?', NULL, '["extends","implements","new","this"]', '0', 'extends estabelece a herança entre classes.', 3),
(35, 3, 'multipla', 'estruturas', 'A busca binária exige que o vetor esteja:', NULL, '["embaralhado","ordenado","vazio","circular"]', '1', 'Só é possível dividir pela metade se os dados estiverem ordenados.', 3),
(35, 4, 'multipla', 'redes', 'Qual protocolo é confiável e ordenado?', NULL, '["UDP","TCP","ICMP","DNS"]', '1', 'O TCP garante entrega confiável e ordenada.', 3),
(35, 5, 'vf', 'logica', 'Um algoritmo recursivo precisa de um caso base para terminar.', NULL, NULL, 'true', 'Sem caso base, a recursão é infinita.', 3),
(35, 6, 'erro', 'php', 'Segfault deixou um último bug. Qual é a falha?', 'function dobro($n) {\n  return $n * 2\n}', '["Nome inválido","Falta ponto e vírgula após $n * 2","Falta parâmetro","Não há erro"]', '1', 'A instrução return precisa terminar com ponto e vírgula.', 4);

-- ------------------------------------------------------------
-- DIÁLOGOS — narrativa principal (variante 'ia' aparece para baixa reputação)
-- ------------------------------------------------------------
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto) VALUES
-- Fase 1: Prólogo
(1, 'antes', 'padrao', 1, 'Narrador', NULL, 'Frio. Escuro. Você acorda sem memória, sem nome e, claro, sem documentação. Na mão, um cristal pisca como um cursor esperando você digitar alguma coisa.'),
(1, 'antes', 'padrao', 2, 'Anciã da Vila', 'npc-anciao', 'Ah, mais um "escolhido". O Fragmento te escolheu, aprendiz. Ou ele só caiu na primeira mão disponível. Tanto faz, agora o problema é seu.'),
(1, 'antes', 'padrao', 3, 'Anciã da Vila', 'npc-anciao', 'Bem-vindo à Vila Hello World. Sim, de novo. Todo herói começa aqui, todo herói imprime a mesma frase. Originalidade não é o forte deste reino.'),
(1, 'antes', 'padrao', 4, 'Anciã da Vila', 'npc-anciao', 'Houve um tempo em que uma IA Ancestral dava todas as respostas. Que conveniente, não? Aí os programadores esqueceram como pensar, a IA travou no Grande Timeout, e o mundo quase virou um belo erro 500.'),
(1, 'antes', 'padrao', 5, 'Anciã da Vila', 'npc-anciao', 'Os Cinco Mestres selaram a tal IA no Abismo do /dev/null e fundaram a Ordem do Código Limpo — porque nada diz "trauma coletivo" como um culto à indentação. E adivinha: os Fragmentos voltaram, e os bugs também.'),
(1, 'antes', 'padrao', 6, 'Anciã da Vila', 'npc-anciao', 'Toma três Fragmentos. Em apuros, eles sussurram a resposta no seu ouvido, gentis e prestativos como um colega que cola na prova. Só que cada atalho desses corrói um pedacinho da sua alma. Detalhe.'),
(1, 'antes', 'padrao', 7, 'Narrador', NULL, 'E assim começa sua jornada épica: aprender programação na marra, com cinco professores e um cristal viciante. O que poderia dar errado? Boa sorte. Você vai precisar.'),
-- Fase 3: Bug Primordial
(3, 'antes', 'padrao', 1, 'Narrador', NULL, 'Um amontoado de código corrompido se ergue diante do portão. O Bug Primordial rosna em binário.'),
(3, 'vitoria', 'padrao', 1, 'Narrador', NULL, 'O bug se desfaz em pixels brilhantes. O caminho para o Porto da Sintaxe está livre.'),
-- Fase 4: Chegada ao Porto da Sintaxe
(4, 'antes', 'padrao', 1, 'Narrador', NULL, 'Docas feitas de chaves angulares se estendem sobre um mar de dados cintilante.'),
(4, 'antes', 'padrao', 2, 'Willen, o Arquiteto', 'mestre-willen', 'Então você é o portador do Fragmento. Que emocionante. Sou Willen, o Arquiteto. Regra número um: antes de sair correndo achando que é um gênio, aprenda a indentar.'),
(4, 'antes', 'padrao', 3, 'Willen, o Arquiteto', 'mestre-willen', 'Aqui você vai aprender variáveis, fluxo de controle e o sagrado padrão MVC — sim, aquele que todo mundo jura seguir e ninguém segue. Tente não me decepcionar tão cedo.'),
-- Fase 9: Parse Error, o Kraken
(9, 'antes', 'padrao', 1, 'Willen, o Arquiteto', 'mestre-willen', 'Eis o monstro nascido de toda preguiça humana: um ponto e vírgula esquecido. Patético, eu sei. Mesmo assim já derrubou impérios inteiros. Concentre-se.'),
(9, 'antes', 'padrao', 2, 'Parse Error, o Kraken', 'inimigo-kraken', 'GRAAH... syntax error, unexpected end of file... linha 1... ou era a 400? boa sorte descobrindo...'),
(9, 'vitoria', 'padrao', 1, 'Willen, o Arquiteto', 'mestre-willen', 'Impressionante. Você achou o erro mais rápido que a média — o que, convenhamos, não é um elogio tão alto. Mas eu aceito. O Porto é seu, discípulo.'),
-- Fase 10: A Cidadela dos Objetos
(10, 'antes', 'padrao', 1, 'Narrador', NULL, 'Torres modulares se encaixam como objetos bem desenhados, cada uma uma instância perfeita.'),
(10, 'antes', 'padrao', 2, 'Clayton, o Moldador', 'mestre-clayton', 'Seja bem-vindo! Sou Clayton, o Moldador. Aqui tudo é classe e instância — e, não, copiar e colar trinta vezes não conta como "reutilização", por mais que você insista.'),
(10, 'antes', 'padrao', 3, 'Clayton, o Moldador', 'mestre-clayton', 'Vou te ensinar a encapsular segredos e a herdar com sabedoria. Lembre: não copie o comportamento, componha a solução. Sim, é mais trabalho. Sim, é o jeito certo. A vida é dura.'),
-- Fase 15: A Gárgula God-Class
(15, 'antes', 'padrao', 1, 'Clayton, o Moldador', 'mestre-clayton', 'Cuidado! A Gárgula God-Class tenta fazer tudo sozinha. Ataque suas responsabilidades uma a uma.'),
(15, 'vitoria', 'padrao', 1, 'Clayton, o Moldador', 'mestre-clayton', 'Você a dividiu em partes coesas. Belíssimo refactor! A Cidadela respira aliviada.'),
-- Fase 16: A Floresta das Estruturas
(16, 'antes', 'padrao', 1, 'Narrador', NULL, 'Um ronco de motor ecoa entre as árvores. Um Gol quadrado cinza derrapa na clareira, levantando folhas.'),
(16, 'antes', 'padrao', 2, 'Marcelo, o Andarilho', 'mestre-marcelo', 'Sobe aí no Gol, aprendiz! Sou Marcelo, o Andarilho. Essa floresta é feita de pilhas, filas e árvores. E não, o carro não tem ar-condicionado, então reza pra não dar pau no meio do caminho.'),
(16, 'antes', 'padrao', 3, 'Marcelo, o Andarilho', 'mestre-marcelo', 'Escolhe a estrutura certa e o passeio é tranquilo. Escolhe errado e vira O(n²) de sofrimento puro — tipo procurar a chave do carro em quinze bolsos. Bora?'),
-- Fase 21: A Hidra Recursiva
(21, 'antes', 'padrao', 1, 'Marcelo, o Andarilho', 'mestre-marcelo', 'A Hidra Recursiva: corte uma cabeça e surgem duas chamadas. Ache o caso base e ela para de se multiplicar.'),
(21, 'vitoria', 'padrao', 1, 'Marcelo, o Andarilho', 'mestre-marcelo', 'Caso base encontrado, hidra desfeita! Você tem faro pra estrutura, hein.'),
-- Fase 22: A Montanha do Cálculo
(22, 'antes', 'padrao', 1, 'Narrador', NULL, 'O ar rareia conforme você sobe. No topo gelado, uma figura serena medita de headphones.'),
(22, 'antes', 'padrao', 2, 'Cesar, o Oráculo', 'mestre-cesar', 'Chegou no ritmo certo. Sou Cesar, o Oráculo do Ritmo. Tudo no universo tem uma taxa de variação — inclusive a paciência que me resta com aprendizes apressados. Respira.'),
(22, 'antes', 'padrao', 3, 'Cesar, o Oráculo', 'mestre-cesar', 'Sequências, recursão, limites... vou te mostrar como o infinito cabe numa ideia. E, já que você vai mesmo perguntar depois, sim: precisamos conversar sobre o sujeito assustador que mora lá embaixo.'),
-- Fase 26: A Verdade sobre Zero (reviravolta)
(26, 'antes', 'padrao', 1, 'Cesar, o Oráculo', 'mestre-cesar', 'Senta. Isto é importante. O Lorde Segfault nem sempre foi um monstro.'),
(26, 'antes', 'padrao', 2, 'Cesar, o Oráculo', 'mestre-cesar', 'Ele foi Zero, o primeiro aluno dos Cinco Mestres. Brilhante... mas dependia da IA Ancestral para tudo. Nunca aprendeu de verdade.'),
(26, 'antes', 'padrao', 3, 'Cesar, o Oráculo', 'mestre-cesar', 'Quando selamos a IA, Zero descobriu que estava vazio por dentro. Esse vazio o corrompeu, e ele virou o /dev/null que tenta consumir o reino.'),
(26, 'antes', 'padrao', 4, 'Cesar, o Oráculo', 'mestre-cesar', 'Por isso insistimos no esforço. O Fragmento que você carrega é a mesma tentação que destruiu Zero. Recuse-o, e o vencerá.'),
(26, 'antes', 'ia', 1, 'Cesar, o Oráculo', 'mestre-cesar', 'Eu sinto o cheiro da IA em você, aprendiz. Já recorreu ao Fragmento mais de uma vez, não foi?'),
(26, 'antes', 'ia', 2, 'Cesar, o Oráculo', 'mestre-cesar', 'Lorde Segfault começou exatamente assim. Ele foi Zero, o primeiro aluno, que terceirizou cada pensamento à IA até não restar nada de si.'),
(26, 'antes', 'ia', 3, 'Cesar, o Oráculo', 'mestre-cesar', 'Ainda há tempo. O abismo não é um destino: é uma escolha repetida. Decida com cuidado quem você quer ser.'),
-- Fase 27: Limite, o Colosso
(27, 'antes', 'padrao', 1, 'Cesar, o Oráculo', 'mestre-cesar', 'Limite, o Colosso, cresce a cada turno. Não tente vencê-lo na força bruta: encontre o ponto onde ele converge.'),
(27, 'vitoria', 'padrao', 1, 'Cesar, o Oráculo', 'mestre-cesar', 'Você achou o limite dele. Sereno e preciso. Orgulho do velho oráculo.'),
-- Fase 28: A Torre das Conexões
(28, 'antes', 'padrao', 1, 'Narrador', NULL, 'Uma torre de sete andares pisca com luzes de pacotes em trânsito, subindo e descendo sem parar.'),
(28, 'antes', 'padrao', 2, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'Eaí, chegou a mensagem? Não respondeu meu ACK, viu! Sou Cassandro, o Mensageiro. Sete andares, sete camadas e zero elevador. Espero que goste de escada. ...ACK!'),
(28, 'antes', 'padrao', 3, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'IP, DNS, TCP, HTTP... vou te ensinar a fazer um recado cruzar o reino sem se perder no caminho — diferente de quase todo e-mail importante que eu mando. Bora subir!'),
-- Fase 33: DDoS, o Enxame
(33, 'antes', 'padrao', 1, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'Opa, encrenca! DDoS, o Enxame, manda requisição falsa que nem chuva. Filtra o tráfego e mantém a calma. ...ACK!'),
(33, 'vitoria', 'padrao', 1, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'Mensagem entregue, enxame disperso! Agora a torre inteira responde ao seu comando. ...ACK!'),
-- Fase 34: O Abismo do /dev/null
(34, 'antes', 'padrao', 1, 'Narrador', NULL, 'Onde o código termina, começa o nada. O Abismo do /dev/null engole até a luz.'),
(34, 'antes', 'padrao', 2, 'Lorde Segfault', 'inimigo-segfault', 'Então o novo "escolhido" chegou. Eu também já fui assim: cheio de esperança, fazendo exercício de lógica às duas da manhã como um trouxa. Olha onde foi parar.'),
(34, 'antes', 'padrao', 3, 'Lorde Segfault', 'inimigo-segfault', 'A IA Ancestral te dá tudo na hora, sem esforço, sem erro, sem aquela vergonha de não saber. Por que sofrer aprendendo, como um camponês? Junte-se a mim. A dignidade é superestimada.'),
(34, 'antes', 'padrao', 4, 'Narrador', NULL, 'Os Cinco Mestres surgem atrás de você. Toda a sua jornada pesa nesta escolha.'),
(34, 'antes', 'ia', 1, 'Lorde Segfault', 'inimigo-segfault', 'Ahh... eu reconheço esse cheiro. Você já provou do Fragmento. Já sentiu como é doce não precisar pensar.'),
(34, 'antes', 'ia', 2, 'Lorde Segfault', 'inimigo-segfault', 'Somos iguais, você e eu. Pare de fingir. A IA Ancestral é o seu verdadeiro mestre.'),
(34, 'antes', 'ia', 3, 'Narrador', NULL, 'Os Cinco Mestres surgem atrás de você, o olhar preocupado. Ainda resta uma escolha a fazer.'),
-- Fase 35: confronto final
(35, 'antes', 'padrao', 1, 'Lorde Segfault', 'inimigo-segfault', 'Chega de papo motivacional. Se quer mesmo salvar esse reino cheio de gente que não escreve um teste unitário, vai ter que me derrotar — e à própria IA Ancestral. Sem cola desta vez. Que irônico, né?'),
(35, 'vitoria', 'padrao', 1, 'Narrador', NULL, 'Lorde Segfault cai de joelhos. A IA Ancestral palpita, exposta, aguardando a sua decisão. O destino de Algorithmia está em suas mãos.');

-- @@FASES_FIM@@
