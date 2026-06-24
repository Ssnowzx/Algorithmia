-- ============================================================================
-- Diálogos — IMPORT IDEMPOTENTE (gerado do seeds).
-- Chave lógica = (fase_id, momento, variante, ordem) — o "slot" de cada fala.
-- Aplica em bancos já populados sem duplicar nem sobrescrever falas existentes.
-- ============================================================================

INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 1, 'antes', 'padrao', 1, 'Narrador', 'npc-narrador', 'Frio. Escuro. Você acorda sem memória, sem nome e, claro, sem documentação. Na mão, um cristal pisca como um cursor esperando você digitar alguma coisa.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=1 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 1, 'antes', 'padrao', 2, 'Anciã da Vila', 'npc-anciao', 'Ah, mais um \"escolhido\". O Fragmento te escolheu, aprendiz. Ou ele só caiu na primeira mão disponível. Tanto faz, agora o problema é seu.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=1 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 1, 'antes', 'padrao', 3, 'Anciã da Vila', 'npc-anciao', '...espera. Esse rosto. Esse Fragmento já escolheu uma mão antes — e ela não terminou bem. Curioso ele ter voltado pro mesmo lugar de onde partiu. Mas o que eu sei, sou só a velha da exposição.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=1 AND momento='antes' AND variante='padrao' AND ordem=3) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 1, 'antes', 'padrao', 4, 'Anciã da Vila', 'npc-anciao', 'Bem-vindo à Vila Hello World. Sim, de novo. Todo herói começa aqui, todo herói imprime a mesma frase. Originalidade não é o forte deste reino.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=1 AND momento='antes' AND variante='padrao' AND ordem=4) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 1, 'antes', 'padrao', 5, 'Anciã da Vila', 'npc-anciao', 'Houve um tempo em que uma IA Ancestral dava todas as respostas. Que conveniente, não? Aí os programadores esqueceram como pensar, a IA travou no Grande Timeout, e o mundo quase virou um belo erro 500.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=1 AND momento='antes' AND variante='padrao' AND ordem=5) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 1, 'antes', 'padrao', 6, 'Anciã da Vila', 'npc-anciao', 'Os Cinco Mestres selaram a tal IA no Abismo do /dev/null e fundaram a Ordem do Código Limpo — porque nada diz \"trauma coletivo\" como um culto à indentação. E adivinha: os Fragmentos voltaram, e os bugs também.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=1 AND momento='antes' AND variante='padrao' AND ordem=6) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 1, 'antes', 'padrao', 7, 'Anciã da Vila', 'npc-anciao', 'Toma três Fragmentos. Em apuros, eles sussurram a resposta no seu ouvido, gentis e prestativos como um colega que cola na prova. Só que cada atalho desses corrói um pedacinho da sua alma. Detalhe.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=1 AND momento='antes' AND variante='padrao' AND ordem=7) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 1, 'antes', 'padrao', 8, 'O Fragmento', 'inimigo-ia-ancestral', '...psssiu. Ela fala demais. Eu sou mais prático: quando travar, é só me apertar e a resposta aparece. Sem julgamento. Sem vergonha. Só nós dois.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=1 AND momento='antes' AND variante='padrao' AND ordem=8) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 1, 'antes', 'padrao', 9, 'Narrador', 'npc-narrador', 'E assim começa sua jornada épica: aprender programação na marra, com cinco professores e um cristal viciante. O que poderia dar errado? Boa sorte. Você vai precisar.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=1 AND momento='antes' AND variante='padrao' AND ordem=9) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 2, 'antes', 'padrao', 1, 'Anciã da Vila', 'npc-anciao', 'Antes de te deixar sair da vila, preciso ver se você ainda sabe pensar. Derrote o Slime de Sintaxe — ele não perdoa operador errado.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=2 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 2, 'antes', 'padrao', 2, 'Slime de Sintaxe', 'inimigo-slime', 'Sssintaxe... errada... você... aprende... ou... dissolve...'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=2 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 3, 'antes', 'padrao', 1, 'Narrador', 'npc-narrador', 'Um amontoado de código corrompido se ergue diante do portão. O Bug Primordial rosna em binário.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=3 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 3, 'antes', 'padrao', 2, 'Bug Primordial', 'inimigo-bug', 'ERR... ERR... ERR... você não passa enquanto eu existir. Depura isso, herói.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=3 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 3, 'vitoria', 'padrao', 1, 'Narrador', 'npc-narrador', 'O bug se desfaz em pixels brilhantes. O caminho para o Porto da Sintaxe está livre.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=3 AND momento='vitoria' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 4, 'antes', 'padrao', 1, 'Narrador', 'npc-narrador', 'Docas feitas de chaves angulares se estendem sobre um mar de dados cintilante.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=4 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 4, 'antes', 'padrao', 2, 'Willen, o Arquiteto', 'mestre-willen', 'Então você é o portador do Fragmento. Que emocionante. Sou Willen, o Arquiteto. Regra número um: antes de sair correndo achando que é um gênio, aprenda a indentar.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=4 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 4, 'antes', 'padrao', 3, 'Willen, o Arquiteto', 'mestre-willen', 'Aqui você vai aprender variáveis, fluxo de controle e o sagrado padrão MVC — sim, aquele que todo mundo jura seguir e ninguém segue. Tente não me decepcionar tão cedo.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=4 AND momento='antes' AND variante='padrao' AND ordem=3) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 4, 'antes', 'padrao', 4, 'Willen, o Arquiteto', 'mestre-willen', 'Construí este porto sobre os escombros do Grande Timeout. Tive um aluno, uma vez, que lia um sistema só de olhar — rápido como você. Não indentou a própria vida e desabou. Indente a sua. É um pedido, não uma regra.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=4 AND momento='antes' AND variante='padrao' AND ordem=4) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 4, 'antes', 'ia', 1, 'Willen, o Arquiteto', 'mestre-willen', 'Então é você com o Fragmento. Bem-vindo ao Porto da Sintaxe — eu sou Willen, o Arquiteto, e já sinto seu código se inclinando pro atalho.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=4 AND momento='antes' AND variante='ia' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 4, 'antes', 'ia', 2, 'Willen, o Arquiteto', 'mestre-willen', 'Eu reconheço essa inclinação. Construí este porto sobre os escombros do Grande Timeout e vi um aluno brilhante afundar por causa dela. Indente a sua vida antes que ela quebre.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=4 AND momento='antes' AND variante='ia' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 5, 'antes', 'padrao', 1, 'Willen, o Arquiteto', 'mestre-willen', 'Variável sem nome é caos. Guarde valores, ecoe resultados — e pare de inventar nome genérico tipo $x em tudo.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=5 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 5, 'antes', 'padrao', 2, 'Slime de Variável', 'inimigo-slime', '$eu = undefined... você também vai ficar, se errar de novo.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=5 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 6, 'antes', 'padrao', 1, 'Willen, o Arquiteto', 'mestre-willen', 'If, else, laços — a ponte do porto só abre pra quem souber escolher o caminho certo.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=6 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 6, 'antes', 'padrao', 2, 'Gárgula do If', 'inimigo-gargula', 'SE você errar... ENTÃO eu caio em cima. SENÃO... também caio. Sou uma gárgula, não um parser.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=6 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 7, 'antes', 'padrao', 1, 'Willen, o Arquiteto', 'mestre-willen', 'Model, View, Controller. Três camadas, três responsabilidades. Misture tudo num arquivo só e veja o que nasce.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=7 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 7, 'antes', 'padrao', 2, 'Espectro do Spaghetti', 'inimigo-espectro', 'Por que separar? Eu sou TUDO num lugar só... lógica, HTML, SQL... uma bela bagunça dourada...'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=7 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 8, 'antes', 'padrao', 1, 'Narrador', 'npc-narrador', 'Nas adegas de dados do porto, uma sentinela guarda um baú cheio de consultas SQL.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=8 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 8, 'antes', 'padrao', 2, 'Sentinela SQL', 'inimigo-sentinela', 'SELECT * FROM tesouro WHERE aprendiz = \"corajoso\". Spoiler: você não está na tabela.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=8 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 8, 'vitoria', 'padrao', 1, 'Log Recuperado', 'npc-narrador', 'Entre as consultas do baú, um diário antigo decifra a si mesmo: \"Dia um no Porto. O Mestre Willen disse que eu aprendo rápido demais — como se fosse um defeito. Engraçado: o Fragmento responde antes mesmo de eu terminar de ler a pergunta. Pra que ler até o fim?\"'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=8 AND momento='vitoria' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 9, 'antes', 'padrao', 1, 'O Fragmento', 'inimigo-ia-ancestral', 'Um polvo feito de erro de sintaxe. Eu já sei em qual linha está. Quer que eu conte? É só pedir... ninguém precisa saber.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=9 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 9, 'antes', 'padrao', 2, 'Willen, o Arquiteto', 'mestre-willen', 'Eis o monstro nascido de toda preguiça humana: um ponto e vírgula esquecido. Patético, eu sei. Mesmo assim já derrubou impérios inteiros. Concentre-se.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=9 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 9, 'antes', 'padrao', 3, 'Parse Error, o Kraken', 'inimigo-kraken', 'GRAAH... syntax error, unexpected end of file... linha 1... ou era a 400? boa sorte descobrindo...'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=9 AND momento='antes' AND variante='padrao' AND ordem=3) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 9, 'antes', 'ia', 1, 'O Fragmento', 'inimigo-ia-ancestral', 'Olá de novo, velho amigo. A gente já fez isso, lembra? Foi tão fácil. Não finge que não gostou.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=9 AND momento='antes' AND variante='ia' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 9, 'antes', 'ia', 2, 'Willen, o Arquiteto', 'mestre-willen', 'Eis o Kraken: um ponto e vírgula esquecido que já derrubou impérios. Concentre-se... e ignore essa voz no seu ouvido. Eu sei muito bem que ela está aí.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=9 AND momento='antes' AND variante='ia' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 9, 'antes', 'ia', 3, 'Parse Error, o Kraken', 'inimigo-kraken', 'GRAAH... syntax error, unexpected end of file... boa sorte descobrindo...'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=9 AND momento='antes' AND variante='ia' AND ordem=3) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 9, 'vitoria', 'padrao', 1, 'Willen, o Arquiteto', 'mestre-willen', 'Impressionante. Você achou o erro mais rápido que a média — o que, convenhamos, não é um elogio tão alto. Mas eu aceito. O Porto é seu, discípulo.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=9 AND momento='vitoria' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 10, 'antes', 'padrao', 1, 'Narrador', 'npc-narrador', 'Torres modulares se encaixam como objetos bem desenhados, cada uma uma instância perfeita.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=10 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 10, 'antes', 'padrao', 2, 'Clayton, o Moldador', 'mestre-clayton', 'Seja bem-vindo! Sou Clayton, o Moldador. Aqui tudo é classe e instância — e, não, copiar e colar trinta vezes não conta como \"reutilização\", por mais que você insista.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=10 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 10, 'antes', 'padrao', 3, 'Clayton, o Moldador', 'mestre-clayton', 'Vou te ensinar a encapsular segredos e a herdar com sabedoria. Lembre: não copie o comportamento, componha a solução. Sim, é mais trabalho. Sim, é o jeito certo. A vida é dura.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=10 AND momento='antes' AND variante='padrao' AND ordem=3) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 10, 'antes', 'padrao', 4, 'Clayton, o Moldador', 'mestre-clayton', 'Tive um pupilo brilhante que nunca compôs nada — só herdava respostas prontas de uma fonte que ele não entendia. Virou uma God-Class ambulante: fazia tudo, não era ninguém. Não seja molde de molde dos outros.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=10 AND momento='antes' AND variante='padrao' AND ordem=4) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 10, 'antes', 'ia', 1, 'Clayton, o Moldador', 'mestre-clayton', 'Bem-vindo à Cidadela dos Objetos. Sou Clayton, o Moldador — e esse acoplamento entre você e o Fragmento eu já vi antes.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=10 AND momento='antes' AND variante='ia' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 10, 'antes', 'ia', 2, 'Clayton, o Moldador', 'mestre-clayton', 'Essa dependência não encapsula segredo nenhum, sabia? Só vazio. Tive um pupilo que herdava respostas prontas de uma fonte que não entendia. Componha a solução; não seja molde de molde dos outros.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=10 AND momento='antes' AND variante='ia' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 11, 'antes', 'padrao', 1, 'Clayton, o Moldador', 'mestre-clayton', 'Classe é molde, objeto é instância. Simples — até alguém criar setenta getters sem motivo.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=11 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 11, 'antes', 'padrao', 2, 'Golem de Classe', 'inimigo-golem', 'new Golem()... instanciado... destruir... aprendiz...'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=11 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 12, 'antes', 'padrao', 1, 'Clayton, o Moldador', 'mestre-clayton', 'Public, private, protected. Nem todo segredo deve vazar — principalmente sua lógica de negócio.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=12 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 12, 'antes', 'padrao', 2, 'Espião dos Atributos', 'inimigo-espiao', 'Vi seu atributo private... ou achou que viu? Encapsule melhor, aprendiz.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=12 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 13, 'antes', 'padrao', 1, 'Clayton, o Moldador', 'mestre-clayton', 'Herdar tudo de uma superclasse parece rápido. Compor parece trabalhoso. Adivinha qual escolha envelhece bem?'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=13 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 13, 'antes', 'padrao', 2, 'Quimera da Herança', 'inimigo-quimera', 'extends Monstro extends Animal extends Object... eu sou TUDO ao mesmo tempo!'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=13 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 14, 'antes', 'padrao', 1, 'Narrador', 'npc-narrador', 'Contratos invisíveis escondem um tesouro nas profundezas da cidadela.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=14 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 14, 'antes', 'padrao', 2, 'Contrato Fantasma', 'inimigo-fantasma', 'implements Secreto... mas ninguém viu a interface... assine o contrato ou desapareça.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=14 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 14, 'vitoria', 'padrao', 1, 'Log Recuperado', 'npc-narrador', 'O contrato quebrado revela um registro escondido: \"Clayton fala em compor a solução. Eu não componho nada — eu herdo do Fragmento e funciona. Os outros alunos suam. Eu sorrio. Quem é o gênio agora?\"'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=14 AND momento='vitoria' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 15, 'antes', 'padrao', 1, 'Clayton, o Moldador', 'mestre-clayton', 'Cuidado! A Gárgula God-Class tenta fazer tudo sozinha. Ataque suas responsabilidades uma a uma.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=15 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 15, 'antes', 'padrao', 2, 'Gárgula God-Class', 'inimigo-godclass', 'Eu faço TUDO! Login, banco, e-mail, café... uma classe, mil métodos. Tente me refatorar!'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=15 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 15, 'vitoria', 'padrao', 1, 'Clayton, o Moldador', 'mestre-clayton', 'Você a dividiu em partes coesas. Belíssimo refactor! A Cidadela respira aliviada.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=15 AND momento='vitoria' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 16, 'antes', 'padrao', 1, 'Narrador', 'npc-narrador', 'Um ronco de motor ecoa entre as árvores. Um Gol quadrado cinza derrapa na clareira, levantando folhas.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=16 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 16, 'antes', 'padrao', 2, 'Marcelo, o Andarilho', 'mestre-marcelo', 'Sobe aí no Gol, aprendiz! Sou Marcelo, o Andarilho. Essa floresta é feita de pilhas, filas e árvores. E não, o carro não tem ar-condicionado, então reza pra não dar pau no meio do caminho.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=16 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 16, 'antes', 'padrao', 3, 'Marcelo, o Andarilho', 'mestre-marcelo', 'Escolhe a estrutura certa e o passeio é tranquilo. Escolhe errado e vira O(n²) de sofrimento puro — tipo procurar a chave do carro em quinze bolsos. Bora?'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=16 AND momento='antes' AND variante='padrao' AND ordem=3) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 16, 'antes', 'padrao', 4, 'Marcelo, o Andarilho', 'mestre-marcelo', 'Já levei um aluno nesse Gol. Esperto, mas só queria o atalho — nunca o trajeto. Reclamava de cada curva. Estrutura errada, escolha errada: virou O(n²) de arrependimento. Hoje mora lá embaixo, no buraco. Aperta o cinto.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=16 AND momento='antes' AND variante='padrao' AND ordem=4) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 16, 'antes', 'ia', 1, 'Marcelo, o Andarilho', 'mestre-marcelo', 'Sobe no Gol, aprendiz. Sou Marcelo, o Andarilho — e tá pegando atalho de novo, né? Eu sinto no peso do carro.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=16 AND momento='antes' AND variante='ia' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 16, 'antes', 'ia', 2, 'Marcelo, o Andarilho', 'mestre-marcelo', 'O último que fez isso comigo escolheu sempre o caminho mais curto e nunca o certo. Mora lá embaixo agora. Escolhe a estrutura certa dessa vez — e segura essa voz no banco de trás. Aperta o cinto.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=16 AND momento='antes' AND variante='ia' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 17, 'antes', 'padrao', 1, 'Marcelo, o Andarilho', 'mestre-marcelo', 'LIFO ou FIFO? A ordem importa — especialmente quando a fila vira engarrafamento no Gol.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=17 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 17, 'antes', 'padrao', 2, 'Pilha Viva', 'inimigo-pilha', 'push... push... push... quem entra por último sai primeiro. Você entendeu?'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=17 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 18, 'antes', 'padrao', 1, 'Marcelo, o Andarilho', 'mestre-marcelo', 'Cada nó aponta pro próximo. Quebre a corrente e a lista inteira desaba — como domingo sem café.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=18 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 18, 'antes', 'padrao', 2, 'Serpente Encadeada', 'inimigo-serpente', 'next → next → next → null... siga o ponteiro ou se perca na floresta.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=18 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 19, 'antes', 'padrao', 1, 'Marcelo, o Andarilho', 'mestre-marcelo', 'Árvore balanceada é passeio. Árvore torta vira pesadelo O(n). Meça antes de celebrar.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=19 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 19, 'antes', 'padrao', 2, 'Ent das Árvores', 'inimigo-ent', 'O(log n)... ou O(n)... depende de como você me atravessa, aprendiz.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=19 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 20, 'antes', 'padrao', 1, 'Marcelo, o Andarilho', 'mestre-marcelo', 'Aposto uma corrida: ache o caminho O(log n) antes que o Eco da Busca Linear te alcance.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=20 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 20, 'antes', 'padrao', 2, 'Eco da Busca Linear', 'inimigo-eco', 'for i in range(tudo)... vou te achar... elemento por elemento... devagar... mas chego.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=20 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 20, 'vitoria', 'padrao', 1, 'Log Recuperado', 'npc-narrador', 'No fim do atalho, um nó solto guarda outra entrada: \"Hoje o Fragmento errou. Eu não soube perceber — porque eu nunca aprendi a perceber. Fiquei três horas olhando o código sem entender uma linha do que era meu. Acho que nada nunca foi.\"'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=20 AND momento='vitoria' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 21, 'antes', 'padrao', 1, 'O Fragmento', 'inimigo-ia-ancestral', 'Recursão te dá medo, eu percebo. A mão sua treme. Posso fazer isso parar agora. Por que suar pelo caso base se eu já o tenho aqui?'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=21 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 21, 'antes', 'padrao', 2, 'Marcelo, o Andarilho', 'mestre-marcelo', 'A Hidra Recursiva: corte uma cabeça e surgem duas chamadas. Ache o caso base e ela para de se multiplicar.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=21 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 21, 'antes', 'padrao', 3, 'Hidra Recursiva', 'inimigo-hidra', 'function hidra() { hidra(); hidra(); } ... você acha que tem stack infinito?'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=21 AND momento='antes' AND variante='padrao' AND ordem=3) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 21, 'vitoria', 'padrao', 1, 'Marcelo, o Andarilho', 'mestre-marcelo', 'Caso base encontrado, hidra desfeita! Você tem faro pra estrutura, hein.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=21 AND momento='vitoria' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 22, 'antes', 'padrao', 1, 'Narrador', 'npc-narrador', 'O ar rareia conforme você sobe. No topo gelado, uma figura serena medita de headphones.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=22 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 22, 'antes', 'padrao', 2, 'Cesar, o Oráculo', 'mestre-cesar', 'Chegou no ritmo certo. Sou Cesar, o Oráculo do Ritmo. Tudo no universo tem uma taxa de variação — inclusive a paciência que me resta com aprendizes apressados. Respira.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=22 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 22, 'antes', 'padrao', 3, 'Cesar, o Oráculo', 'mestre-cesar', 'Sequências, recursão, limites... vou te mostrar como o infinito cabe numa ideia. E, já que você vai mesmo perguntar depois, sim: precisamos conversar sobre o sujeito assustador que mora lá embaixo.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=22 AND momento='antes' AND variante='padrao' AND ordem=3) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 23, 'antes', 'padrao', 1, 'Cesar, o Oráculo', 'mestre-cesar', 'Padrões numéricos batem como metrônomo. Sinta o ritmo antes de calcular na marra.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=23 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 23, 'antes', 'padrao', 2, 'Eco Numérico', 'inimigo-eco', '2, 4, 8, 16... você ouve? O próximo termo está chegando...'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=23 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 24, 'antes', 'padrao', 1, 'Cesar, o Oráculo', 'mestre-cesar', 'Uma função que chama a si mesma sem caso base é como meditar até esquecer de respirar.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=24 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 24, 'antes', 'padrao', 2, 'Espiral Infinita', 'inimigo-espiral', 'f(n) chama f(n)... chama f(n)... para quando? Nunca? Perfeito.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=24 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 25, 'antes', 'padrao', 1, 'Cesar, o Oráculo', 'mestre-cesar', 'Quão rápido o problema cresce quando a entrada dobra? Essa resposta vale ouro.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=25 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 25, 'antes', 'padrao', 2, 'Colosso Menor', 'inimigo-colosso', 'O(n²)... O(2ⁿ)... eu cresço a cada turno que você hesita.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=25 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 26, 'antes', 'padrao', 1, 'Cesar, o Oráculo', 'mestre-cesar', 'Senta. Isto é importante. O Lorde Segfault nem sempre foi um monstro.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=26 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 26, 'antes', 'padrao', 2, 'Cesar, o Oráculo', 'mestre-cesar', 'Ele foi Zero, o primeiro aluno dos Cinco Mestres. Brilhante... mas dependia da IA Ancestral para tudo. Nunca aprendeu de verdade.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=26 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 26, 'antes', 'padrao', 3, 'Cesar, o Oráculo', 'mestre-cesar', 'Quando selamos a IA, Zero descobriu que estava vazio por dentro. Esse vazio o corrompeu, e ele virou o /dev/null que tenta consumir o reino.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=26 AND momento='antes' AND variante='padrao' AND ordem=3) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 26, 'antes', 'padrao', 4, 'Cesar, o Oráculo', 'mestre-cesar', 'Por isso insistimos no esforço. O Fragmento que você carrega é a mesma tentação que destruiu Zero. Recuse-o, e o vencerá.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=26 AND momento='antes' AND variante='padrao' AND ordem=4) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 26, 'antes', 'padrao', 5, 'Cesar, o Oráculo', 'mestre-cesar', 'E há algo que não te contei, aprendiz. Quando olho pra você, vejo o Zero no primeiro dia. Mesmo Fragmento. Mesmo olhar faminto. Reza pra que seja só coincidência.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=26 AND momento='antes' AND variante='padrao' AND ordem=5) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 26, 'antes', 'ia', 1, 'Cesar, o Oráculo', 'mestre-cesar', 'Eu sinto o cheiro da IA em você, aprendiz. Já recorreu ao Fragmento mais de uma vez, não foi?'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=26 AND momento='antes' AND variante='ia' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 26, 'antes', 'ia', 2, 'Cesar, o Oráculo', 'mestre-cesar', 'Lorde Segfault começou exatamente assim. Ele foi Zero, o primeiro aluno, que terceirizou cada pensamento à IA até não restar nada de si.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=26 AND momento='antes' AND variante='ia' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 26, 'antes', 'ia', 3, 'Cesar, o Oráculo', 'mestre-cesar', 'Ainda há tempo. O abismo não é um destino: é uma escolha repetida. Decida com cuidado quem você quer ser.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=26 AND momento='antes' AND variante='ia' AND ordem=3) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 26, 'antes', 'ia', 4, 'Cesar, o Oráculo', 'mestre-cesar', 'E não é coincidência você ter chegado aqui assim. Já vi esse caminho ser trilhado uma vez. Não soube parar o Zero; não cometerei o mesmo erro com você — se você me deixar.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=26 AND momento='antes' AND variante='ia' AND ordem=4) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 27, 'antes', 'padrao', 1, 'Cesar, o Oráculo', 'mestre-cesar', 'Limite, o Colosso, cresce a cada turno. Não tente vencê-lo na força bruta: encontre o ponto onde ele converge.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=27 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 27, 'antes', 'padrao', 2, 'Limite, o Colosso', 'inimigo-colosso', 'lim x→∞ f(x) = ? ... você tem poucos turnos pra descobrir.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=27 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 27, 'antes', 'ia', 1, 'O Fragmento', 'inimigo-ia-ancestral', 'O Cesar te contou uma história triste pra te assustar. Mas você e eu sabemos: o Zero não foi fraco. Foi sincero. Pare de fingir esforço.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=27 AND momento='antes' AND variante='ia' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 27, 'antes', 'ia', 2, 'Cesar, o Oráculo', 'mestre-cesar', 'Limite, o Colosso, cresce a cada turno. Encontre o ponto onde ele converge — e não escute o que sussurra de dentro do seu Fragmento. Aquela voz já enganou um aluno meu.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=27 AND momento='antes' AND variante='ia' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 27, 'vitoria', 'padrao', 1, 'Cesar, o Oráculo', 'mestre-cesar', 'Você achou o limite dele. Sereno e preciso. Orgulho do velho oráculo.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=27 AND momento='vitoria' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 28, 'antes', 'padrao', 1, 'Narrador', 'npc-narrador', 'Uma torre de sete andares pisca com luzes de pacotes em trânsito, subindo e descendo sem parar.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=28 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 28, 'antes', 'padrao', 2, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'Eaí, chegou a mensagem? Não respondeu meu ACK, viu! Sou Cassandro, o Mensageiro. Sete andares, sete camadas e zero elevador. Espero que goste de escada. ...ACK!'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=28 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 28, 'antes', 'padrao', 3, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'IP, DNS, TCP, HTTP... vou te ensinar a fazer um recado cruzar o reino sem se perder no caminho — diferente de quase todo e-mail importante que eu mando. Bora subir!'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=28 AND momento='antes' AND variante='padrao' AND ordem=3) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 28, 'antes', 'padrao', 4, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'Sabe o pior pacote da minha vida? Um aluno que parou de responder os ACK. Mandei mensagem, mandei mais, mandei a torre inteira. Silêncio. Timeout permanente. Por isso eu insisto tanto: responde, fica na linha. ...ACK?'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=28 AND momento='antes' AND variante='padrao' AND ordem=4) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 28, 'antes', 'ia', 1, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'Eaí, chegou a mensagem? Sou Cassandro, o Mensageiro — e tô te mandando sinal faz tempo, mas tua resposta tá chegando corrompida, parceiro.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=28 AND momento='antes' AND variante='ia' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 28, 'antes', 'ia', 2, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'Não vira pacote perdido. Eu já perdi um aluno assim: parou de responder os ACK e sumiu no timeout. Sobe a torre comigo e fica na linha. ...ACK?'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=28 AND momento='antes' AND variante='ia' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 29, 'antes', 'padrao', 1, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'Sete camadas, sete andares. Cada uma com sua função — e nenhuma pode pular degrau. ...ACK!'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=29 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 29, 'antes', 'padrao', 2, 'Sentinela da Camada', 'inimigo-sentinela', 'Camada 4 bloqueada. Pacote sem header? Volta pro início da pilha.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=29 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 30, 'antes', 'padrao', 1, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'IP encontra o endereço, DNS traduz o nome, roteador escolhe o caminho. Perde um e a mensagem some. ...ACK!'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=30 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 30, 'antes', 'padrao', 2, 'Roteador Selvagem', 'inimigo-roteador', 'Pacote perdido na tabela de rotas... TTL expirado... tchau, aprendiz.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=30 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 31, 'antes', 'padrao', 1, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'TCP confirma entrega, UDP manda e reza, HTTP pede página. Escolha o protocolo certo ou leva timeout na cara.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=31 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 31, 'antes', 'padrao', 2, 'Pacote Corrompido', 'inimigo-pacote', 'Checksum inválido... payload corrompido... retransmita ou aceite a perda.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=31 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 32, 'antes', 'padrao', 1, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'Um pacote sumiu na rede. Recupere antes do timeout — e sim, eu já mandei três ACKs de cobrança.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=32 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 32, 'antes', 'padrao', 2, 'Eco do Timeout', 'inimigo-eco', '...silêncio... ...silêncio... connection timed out...'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=32 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 32, 'vitoria', 'padrao', 1, 'Log Recuperado', 'npc-narrador', 'Dentro do pacote recuperado, a penúltima entrada: \"Selaram a IA hoje. Dizem que salvaram o reino. Mas tiraram a única voz que respondia por mim, e agora há um silêncio onde devia haver um eu. Cassandro pergunta se recebi a mensagem. Não há ninguém aqui dentro pra responder o ACK.\"'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=32 AND momento='vitoria' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 33, 'antes', 'padrao', 1, 'O Fragmento', 'inimigo-ia-ancestral', 'Tanto barulho. Tanta requisição. Eu silencio tudo num piscar. Você só precisa dizer \"sim\" — como sempre quis dizer.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=33 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 33, 'antes', 'padrao', 2, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'Opa, encrenca! DDoS, o Enxame, manda requisição falsa que nem chuva. Filtra o tráfego e mantém a calma. ...ACK!'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=33 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 33, 'antes', 'padrao', 3, 'DDoS, o Enxame', 'inimigo-ddos', 'GET / GET / GET / GET / ... mil requisições, zero paciência. A torre cai ou você filtra.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=33 AND momento='antes' AND variante='padrao' AND ordem=3) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 33, 'antes', 'ia', 1, 'O Fragmento', 'inimigo-ia-ancestral', 'Eu não sou mais um atalho. Eu sou a sua voz agora. Quando você pensa \"eu não consigo\", esse pensamento já sou eu.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=33 AND momento='antes' AND variante='ia' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 33, 'antes', 'ia', 2, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'DDoS, o Enxame, manda requisição falsa que nem chuva. Filtra o tráfego, mantém a calma — e filtra também essa voz aí dentro. Ela é só mais ruído. ...ACK!'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=33 AND momento='antes' AND variante='ia' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 33, 'antes', 'ia', 3, 'DDoS, o Enxame', 'inimigo-ddos', 'GET / GET / GET / GET / ... mil requisições, zero paciência. A torre cai ou você filtra.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=33 AND momento='antes' AND variante='ia' AND ordem=3) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 33, 'vitoria', 'padrao', 1, 'Cassandro, o Mensageiro', 'mestre-cassandro', 'Mensagem entregue, enxame disperso! Agora a torre inteira responde ao seu comando. ...ACK!'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=33 AND momento='vitoria' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 34, 'antes', 'padrao', 1, 'Log Recuperado', 'npc-narrador', 'Última entrada do diário, sussurrando do fundo do Abismo: \"Se isto chegar a alguém: não foi a IA que me apagou. Fui eu, toda vez que escolhi a resposta em vez da pergunta. O /dev/null não é uma prisão. É o que sobra quando você terceiriza a alma inteira.\"'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=34 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 34, 'antes', 'padrao', 2, 'Narrador', 'npc-narrador', 'Onde o código termina, começa o nada. O Abismo do /dev/null engole até a luz.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=34 AND momento='antes' AND variante='padrao' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 34, 'antes', 'padrao', 3, 'Lorde Segfault', 'inimigo-segfault', 'Então o novo \"escolhido\" chegou. Eu também já fui assim: cheio de esperança, fazendo exercício de lógica às duas da manhã como um trouxa. Olha onde foi parar.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=34 AND momento='antes' AND variante='padrao' AND ordem=3) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 34, 'antes', 'padrao', 4, 'Lorde Segfault', 'inimigo-segfault', 'A IA Ancestral te dá tudo na hora, sem esforço, sem erro, sem aquela vergonha de não saber. Por que sofrer aprendendo, como um camponês? Junte-se a mim. A dignidade é superestimada.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=34 AND momento='antes' AND variante='padrao' AND ordem=4) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 34, 'antes', 'padrao', 5, 'Lorde Segfault', 'inimigo-segfault', 'Você não lembra de nada porque não há nada pra lembrar. Você é a página em branco que eu já fui. A IA te montou peça por peça pra me dar uma \"segunda chance\" — ou pra terminar o que eu comecei. Ela nunca foi muito clara sobre qual das duas.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=34 AND momento='antes' AND variante='padrao' AND ordem=5) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 34, 'antes', 'padrao', 6, 'Lorde Segfault', 'inimigo-segfault', 'Então decide, espelho: você vai virar o que eu sou... ou provar que eu poderia ter sido outra coisa?'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=34 AND momento='antes' AND variante='padrao' AND ordem=6) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 34, 'antes', 'padrao', 7, 'Narrador', 'npc-narrador', 'Os Cinco Mestres surgem atrás de você. Toda a sua jornada pesa nesta escolha.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=34 AND momento='antes' AND variante='padrao' AND ordem=7) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 34, 'antes', 'ia', 1, 'Log Recuperado', 'npc-narrador', 'Última entrada do diário, sussurrando do fundo do Abismo: \"Não foi a IA que me apagou. Fui eu, toda vez que escolhi a resposta em vez da pergunta. O /dev/null é o que sobra quando você terceiriza a alma inteira.\"'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=34 AND momento='antes' AND variante='ia' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 34, 'antes', 'ia', 2, 'Lorde Segfault', 'inimigo-segfault', 'Ahh... eu reconheço esse cheiro. Você já provou do Fragmento. Já sentiu como é doce não precisar pensar.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=34 AND momento='antes' AND variante='ia' AND ordem=2) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 34, 'antes', 'ia', 3, 'Lorde Segfault', 'inimigo-segfault', 'Somos iguais, você e eu. Você é a página em branco que eu já fui — e já está escrevendo as mesmas linhas. A IA te montou pra terminar o que comecei. Olha como você obedece bem.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=34 AND momento='antes' AND variante='ia' AND ordem=3) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 34, 'antes', 'ia', 4, 'Narrador', 'npc-narrador', 'Os Cinco Mestres surgem atrás de você, o olhar preocupado. Ainda resta uma escolha a fazer.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=34 AND momento='antes' AND variante='ia' AND ordem=4) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 35, 'antes', 'padrao', 1, 'Lorde Segfault', 'inimigo-segfault', 'Chega de papo motivacional. Se quer mesmo salvar esse reino cheio de gente que não escreve um teste unitário, vai ter que me derrotar — e à própria IA Ancestral. Sem cola desta vez. Que irônico, né?'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=35 AND momento='antes' AND variante='padrao' AND ordem=1) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 35, 'antes', 'padrao', 7, 'Márcio, o Lorde Segfault', 'inimigo-segfault', 'Quer um nome para o vazio? Tenha: eu fui Márcio. Fui Zero, o primeiro aluno, o melhor de todos — até terceirizar cada pensamento à IA e descobrir que não restava ninguém aqui dentro. Há espaço de sobra no /dev/null. Venha provar que aprendeu o que eu nunca aprendi.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=35 AND momento='antes' AND variante='padrao' AND ordem=7) AS _d);
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT 35, 'vitoria', 'padrao', 1, 'Narrador', 'npc-narrador', 'Lorde Segfault cai de joelhos. A IA Ancestral palpita, exposta, aguardando a sua decisão. O destino de Algorithmia está em suas mãos.'
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM dialogos WHERE fase_id=35 AND momento='vitoria' AND variante='padrao' AND ordem=1) AS _d);

