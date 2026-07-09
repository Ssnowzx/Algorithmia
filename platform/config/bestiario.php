<?php

declare(strict_types=1);

/**
 * Bestiário de Algorithmia — a lore de cada inimigo do jogo.
 *
 * Cada entrada é chaveada pelo svg_slug do inimigo (o mesmo usado em
 * database/seeds.sql e em public/img/inimigos/), garantindo que código,
 * imagem e narrativa apontem para a mesma criatura.
 *
 * Os inimigos são erros e estruturas de código que ganharam vida ao serem
 * corrompidos pelo Fragmento da IA Ancestral — a mesma tentação que, no
 * passado, transformou o brilhante aluno Zero no Lorde Segfault. Cada região
 * é governada por um Mestre e sua disciplina; os monstros locais encarnam o
 * conceito técnico que aquela disciplina ensina a domar.
 *
 *
 * Porte de `config/bestiario.php`: a constante virou um array de config, para
 * que o Laravel o carregue como `config('bestiario')`. O conteúdo é cânone
 * (docs/codex/) e não foi tocado.
 * Estrutura de cada entrada:
 *  - 'nome'     => nome (ou nomes) com que a criatura aparece nas fases
 *  - 'titulo'   => epíteto/alcunha de sabor
 *  - 'regiao'   => região e Mestre a que pertence
 *  - 'conceito' => o conceito técnico de código que o inimigo representa
 *  - 'lore'     => história que amarra o inimigo à disciplina e ao Fragmento
 *  - 'fraqueza' => fraqueza temática (sabor)
 *
 * O tom de SABOR é ácido e zombeteiro (regra do projeto), mas o conceito
 * técnico descrito é sempre correto.
 */
return [

    // ============================================================
    //  Vila Hello World — os fundamentos (lógica e algoritmos)
    // ============================================================
    'inimigo-slime' => [
        'nome' => 'Slime de Sintaxe / Slime de Variável',
        'titulo' => 'A Primeira Coisa que Você Mata',
        'regiao' => 'Vila Hello World e Porto da Sintaxe (Willen)',
        'conceito' => 'Tipos primitivos e variáveis: o gosma básico onde todo valor começa a morar.',
        'lore' => 'Toda jornada épica precisa de um saco de pancada inicial, e o Slime aceitou o papel com dignidade gelatinosa. É um amontoado translúcido de sintaxe onde pontuações e valores soltos boiam sem ordem — basicamente uma variável que ainda não decidiu o que quer ser quando crescer. O Fragmento o corrompeu com o mínimo esforço, porque convenhamos: corromper uma gosma não é exatamente o ápice da carreira de um vilão.',
        'fraqueza' => 'Qualquer coisa. Sério. É um slime.',
    ],

    'inimigo-bug' => [
        'nome' => 'Bug Primordial',
        'titulo' => 'O Defeito que Sempre Estava Lá',
        'regiao' => 'Vila Hello World (chefe)',
        'conceito' => 'O bug: o erro de lógica que se esconde no código e bloqueia a execução do plano.',
        'lore' => 'Inseto-glitch de carapaça rachada que vaza código de erro vermelho por todas as juntas, o Bug Primordial é o primeiro defeito que decidiu revidar em vez de só atrapalhar. Guarda a saída da Vila porque é exatamente isso que um bug faz: aparece bem na hora em que você ia entregar. Nutrido pelo Fragmento, jura que não é culpa dele — a culpa nunca é do bug, é sempre de quem escreveu o código.',
        'fraqueza' => 'Depuração paciente: ler o código linha a linha até ele não ter mais onde se esconder.',
    ],

    // ============================================================
    //  Porto da Sintaxe — Willen (PHP, MVC, SQL)
    // ============================================================
    'inimigo-gargula' => [
        'nome' => 'Gárgula do If',
        'titulo' => 'A Guardiã da Condição',
        'regiao' => 'Porto da Sintaxe (Willen)',
        'conceito' => 'Estruturas de controle: if/else e laços, que decidem por onde a execução passa.',
        'lore' => 'Empoleirada na ponte do Porto, a Gárgula do If só deixa passar quem satisfaz a condição — e tem o orgulho de uma porteira de prédio com poder demais. Suas asas de pedra trazem runas de condição que se acendem em verdadeiro ou falso conforme o humor. Willen a tolera porque ensina disciplina; o Fragmento a corrompeu prometendo que ela nunca mais precisaria avaliar nada, bastava aprovar tudo. Spoiler: um if que sempre dá true é só um bug de pé.',
        'fraqueza' => 'Uma condição bem escrita que cubra o caso else — ela não sabe o que fazer quando a lógica não tem brecha.',
    ],

    'inimigo-espectro' => [
        'nome' => 'Espectro do Spaghetti',
        'titulo' => 'O Fantasma do Código Sem Arquitetura',
        'regiao' => 'Porto da Sintaxe (Willen)',
        'conceito' => 'Código spaghetti vs. padrão MVC: lógica, dados e apresentação enroscados sem separação.',
        'lore' => 'Fantasma dourado emaranhado em fios de código que se cruzam sem início nem fim, o Espectro do Spaghetti é o que sobra quando alguém joga regra de negócio, SQL e HTML no mesmo arquivo e chama de "funcionou". Willen o invoca como aviso: foi assim que metade do reino virou ruína. O Fragmento adora esse espectro, porque código emaranhado é código que ninguém entende — e ninguém que entende precisa de uma IA para consertar.',
        'fraqueza' => 'Separar as camadas: Model, View e Controller. Sem o nó, o fantasma não tem em que se segurar.',
    ],

    'inimigo-sentinela' => [
        'nome' => 'Sentinela SQL / Sentinela da Camada',
        'titulo' => 'A Guarda que Pede a Senha Errada',
        'regiao' => 'Porto da Sintaxe (Willen) e Torre das Conexões (Cassandro)',
        'conceito' => 'Consultas SQL e camadas de rede: o construto guarda o acesso aos dados (Willen) e ao protocolo (Cassandro).',
        'lore' => 'Construto guardião coberto de sigilos — runas SQL nas adegas de dados de Willen, glifos das sete camadas do OSI na Torre de Cassandro. Em ambos os postos faz a mesma coisa: filtrar quem entra, exatamente como uma cláusula WHERE ou um firewall de camada. O Fragmento o reprogramou para liberar tudo sem checar a condição, o que é a definição precisa de uma falha de segurança ambulante.',
        'fraqueza' => 'Uma consulta precisa com a cláusula certa (em Willen) ou a camada correta do OSI (em Cassandro) — ele só responde a quem fala o protocolo direito.',
    ],

    'inimigo-kraken' => [
        'nome' => 'Parse Error, o Kraken',
        'titulo' => 'O Terror dos Compiladores',
        'regiao' => 'Porto da Sintaxe (Willen) — chefe',
        'conceito' => 'Erro de análise (parse error): um único caractere fora do lugar, como um ponto e vírgula a menos.',
        'lore' => 'Das águas do Porto emerge o colosso de tentáculos cobertos de chaves { } desbalanceadas: o Parse Error, o Kraken, monstro que toma conta da sintaxe de Willen. Toda a sua fúria devastadora vem de uma única coisa que falta — um ponto e vírgula, um parêntese sem par — porque é assim mesmo que um parse error funciona: derruba o sistema inteiro por um detalhe ridículo. O Fragmento o engorda sussurrando "deixa que eu acho o erro pra você", e o Kraken prospera na preguiça de quem nunca aprendeu a ler a linha que o compilador apontou.',
        'fraqueza' => 'Ler a mensagem de erro até o fim e fechar o caractere que falta. Ele desaba no exato ponto e vírgula que estava faltando.',
    ],

    // ============================================================
    //  Cidadela dos Objetos — Clayton (POO)
    // ============================================================
    'inimigo-golem' => [
        'nome' => 'Golem de Classe',
        'titulo' => 'O Molde que Ganhou Vida',
        'regiao' => 'Cidadela dos Objetos (Clayton)',
        'conceito' => 'Classes e objetos: o golem é uma classe instanciada, um molde feito carne (ou pedra).',
        'lore' => 'Corpo de blocos modulares que se encaixam como atributos e métodos, o Golem de Classe é o que acontece quando um molde resolve sair andando por aí. Clayton o usa para ensinar a diferença entre a classe (a planta) e o objeto (o prédio construído) — distinção que metade dos aprendizes finge entender. O Fragmento o corrompeu prometendo que ele não precisava ser instanciado para existir, o que é mais ou menos como um prédio se achar habitável só por estar na planta.',
        'fraqueza' => 'Entender que o golem é só uma instância: derrube o construtor e o objeto não nasce.',
    ],

    'inimigo-espiao' => [
        'nome' => 'Espião dos Atributos',
        'titulo' => 'O Bisbilhoteiro do private',
        'regiao' => 'Cidadela dos Objetos (Clayton)',
        'conceito' => 'Encapsulamento: public, private e protected; o espião quer ler o estado interno que deveria ser oculto.',
        'lore' => 'Furtivo encapuzado de olhos demais, o Espião dos Atributos vive tentando espiar os dados que foram marcados como private — o tipo de criatura que lê a DM dos outros. Clayton o mantém por perto como lição viva de por que encapsulamento existe: nem todo atributo é da conta de quem está de fora. O Fragmento o adora porque a IA também quer acesso ao seu estado interno, gentilmente, só para ajudar, claro.',
        'fraqueza' => 'Encapsulamento de verdade: torne o atributo private e exponha só um getter controlado. Sem porta dos fundos, o espião fica do lado de fora.',
    ],

    'inimigo-quimera' => [
        'nome' => 'Quimera da Herança',
        'titulo' => 'O Monstro de Partes Coladas',
        'regiao' => 'Cidadela dos Objetos (Clayton)',
        'conceito' => 'Herança vs. composição: a quimera é a herança abusada, partes fundidas num híbrido frágil.',
        'lore' => 'Animal híbrido e instável, costurado de pedaços que herdou de pais que mal se conheciam, a Quimera da Herança é o que vira a classe que estende a classe que estende a classe até ninguém saber mais de onde veio cada método. Clayton a exibe para defender sua tese favorita: componha em vez de herdar. O Fragmento, naturalmente, incentiva a herança profunda — quanto mais frágil e acoplado o código, mais você vai precisar pedir socorro a ele.',
        'fraqueza' => 'Composição: prefira "tem-um" a "é-um". Sem a herança rígida que a segura, a quimera se desmonta em peças soltas.',
    ],

    'inimigo-fantasma' => [
        'nome' => 'Contrato Fantasma',
        'titulo' => 'A Interface que Ninguém Implementou',
        'regiao' => 'Cidadela dos Objetos (Clayton) — secundária',
        'conceito' => 'Interfaces: o contrato de métodos que uma classe promete implementar.',
        'lore' => 'Espectro etéreo segurando um pergaminho-interface translúcido, o Contrato Fantasma assombra quem assina uma interface e depois "esquece" de implementar os métodos prometidos. É o equivalente arcano daquele termo de uso que ninguém leu. Clayton o guarda numa missão secundária, junto de um tesouro, porque honrar contratos costuma render recompensa. O Fragmento o corrompeu prometendo implementações automáticas — que, como toda promessa fácil, nunca cumprem o contrato inteiro.',
        'fraqueza' => 'implements de verdade: cumpra todos os métodos que a interface exige e o fantasma some, satisfeito por uma vez.',
    ],

    'inimigo-godclass' => [
        'nome' => 'Gárgula God-Class',
        'titulo' => 'A Classe que Faz Tudo (e Nada Direito)',
        'regiao' => 'Cidadela dos Objetos (Clayton) — chefe',
        'conceito' => 'God Class / violação do SRP: uma classe que acumula responsabilidades demais.',
        'lore' => 'Gárgula colossal de braços e bocas em excesso, a God-Class incha porque resolveu fazer tudo sozinha: salva no banco, renderiza HTML, envia e-mail e ainda opina sobre o clima. Cada nova função que ela engole a deixa mais monstruosa e mais impossível de manter — é o boss perfeito de Clayton porque viola descaradamente o Princípio da Responsabilidade Única. O Fragmento a engorda de propósito: uma classe que faz tudo é uma classe que ninguém entende, e código que ninguém entende é refém eterno da IA.',
        'fraqueza' => 'O SRP: quebrar a God-Class em classes menores e coesas. Cada responsabilidade que você extrai dela arranca um braço.',
    ],

    // ============================================================
    //  Floresta das Estruturas — Marcelo (Estrutura de Dados)
    // ============================================================
    'inimigo-pilha' => [
        'nome' => 'Pilha Viva',
        'titulo' => 'O Último a Entrar, o Primeiro a Cair',
        'regiao' => 'Floresta das Estruturas (Marcelo)',
        'conceito' => 'Pilha (stack): estrutura LIFO — Last In, First Out; risca o estouro de pilha (stack overflow).',
        'lore' => 'Torre instável de blocos empilhados, a Pilha Viva cresce empurrando tudo para cima e jura que o último que subiu é o primeiro que vai descer — LIFO até o talo. Marcelo a usa para ensinar que ordem importa na floresta: empilhe errado e o passeio vira estouro. O Fragmento a corrompeu enfiando chamadas sem fim no topo, e ela agora ameaça desabar num belo stack overflow — o tipo de queda que leva o programa inteiro junto.',
        'fraqueza' => 'Respeitar o LIFO e não empilhar além da conta: faça o pop na ordem certa e a torre se desfaz sozinha pelo topo.',
    ],

    'inimigo-serpente' => [
        'nome' => 'Serpente Encadeada',
        'titulo' => 'A Que Aponta Sempre para o Próximo',
        'regiao' => 'Floresta das Estruturas (Marcelo)',
        'conceito' => 'Lista encadeada (linked list): cada nó guarda um valor e um ponteiro para o próximo.',
        'lore' => 'Réptil cujo corpo é uma fileira de nós ligados, cada um apontando para o seguinte até a cauda apontar para null, a Serpente Encadeada se move um nó de cada vez — e é exatamente por isso que achar a presa na posição k dela custa O(n) de saliva. Marcelo a respeita: inserir no começo dela é O(1), uma elegância. O Fragmento a corrompeu quebrando um ponteiro no meio, e agora metade da serpente flutua perdida, apontando para o vazio — vazamento de memória com escamas.',
        'fraqueza' => 'Seguir os ponteiros com paciência até o null final, e religar o nó que o Fragmento soltou. Sem o próximo, a serpente é só uma cabeça confusa.',
    ],

    'inimigo-ent' => [
        'nome' => 'Ent das Árvores',
        'titulo' => 'O Ancião do Big-O',
        'regiao' => 'Floresta das Estruturas (Marcelo)',
        'conceito' => 'Árvores binárias de busca e notação Big-O: busca balanceada em O(log n).',
        'lore' => 'Ent ancestral cujos galhos formam uma árvore binária de busca perfeita, com a notação Big-O brilhando na casca, divide o mundo em "menor à esquerda, maior à direita" e por isso encontra qualquer coisa em O(log n) — sem se levantar do lugar, o que para uma árvore é conveniente. Marcelo mede o tempo do mundo pela altura dele. O Fragmento tentou corrompê-lo desbalanceando seus galhos para um lado só, transformando a busca elegante em O(n) — uma árvore que virou lista, a maior humilhação que se pode infligir a um Ent.',
        'fraqueza' => 'Manter a árvore balanceada: com a altura proporcional a log n, a busca o derruba em pouquíssimos passos.',
    ],

    'inimigo-eco' => [
        'nome' => 'Eco da Busca Linear / Eco Numérico / Eco do Timeout',
        'titulo' => 'A Repetição que Não Para',
        'regiao' => 'Floresta das Estruturas (Marcelo), Montanha do Cálculo (Cesar) e Torre das Conexões (Cassandro)',
        'conceito' => 'Iteração linear O(n) e repetição: percorrer tudo um por um; em Cesar vira sequência/ritmo, em Cassandro vira retransmissão até o timeout.',
        'lore' => 'Silhuetas idênticas que se sobrepõem e se repetem, o Eco é a mesma criatura assombrando três regiões com o mesmo truque cansativo: fazer de novo, e de novo, e de novo. Na Floresta de Marcelo é a busca linear que olha elemento por elemento (O(n) de tédio); na Montanha de Cesar vira sequência numérica que pulsa em ritmo previsível; na Torre de Cassandro é o pacote retransmitido sem parar até estourar o timeout. O Fragmento adora o Eco porque repetição cega é o oposto de pensar — e quem só repete nunca percebe que existe um atalho.',
        'fraqueza' => 'Achar o padrão e parar de repetir: a busca binária (Marcelo), a fórmula da sequência (Cesar) ou um timeout bem ajustado (Cassandro) calam o eco.',
    ],

    'inimigo-hidra' => [
        'nome' => 'Hidra Recursiva',
        'titulo' => 'Corte Uma, Surgem Duas',
        'regiao' => 'Floresta das Estruturas (Marcelo) — chefe',
        'conceito' => 'Recursão sem caso base: cada chamada gera novas chamadas que nunca param.',
        'lore' => 'A cada cabeça que você corta, duas chamadas recursivas brotam no lugar — a Hidra Recursiva é a recursão escrita por alguém que esqueceu o caso base, multiplicando-se em padrão fractal até o reino ficar sem pilha. É o boss perfeito da floresta de Marcelo, porque toda função que se chama precisa de uma condição de parada, e essa aqui não tem nenhuma de propósito. O Fragmento a alimenta sussurrando "por que suar pelo caso base se eu já o tenho aqui?", e a Hidra cresce sem fim na pura preguiça de não pensar onde a recursão deveria terminar.',
        'fraqueza' => 'O caso base: dê a ela uma condição de parada e a recursão infinita colapsa numa única cabeça finita.',
    ],

    // ============================================================
    //  Montanha do Cálculo — Cesar (Cálculo / convergência)
    // ============================================================
    'inimigo-espiral' => [
        'nome' => 'Espiral Infinita',
        'titulo' => 'O Loop que Esqueceram de Fechar',
        'regiao' => 'Montanha do Cálculo (Cesar)',
        'conceito' => 'Laço infinito e limites: um loop cuja condição de parada nunca se torna falsa.',
        'lore' => 'Vórtice de energia que gira em loop sem fim, a Espiral Infinita é o while cuja condição nunca vira falsa porque ninguém alterou a variável lá dentro — gira, gira e nunca chega a lugar nenhum, igual a uma reunião que poderia ter sido um e-mail. Cesar a estuda na Montanha para ensinar limites: nem toda repetição converge. O Fragmento a faz girar de propósito, porque enquanto você está preso no loop, não percebe que esqueceu de incrementar o contador — e quem está preso pede ajuda.',
        'fraqueza' => 'Uma condição de parada que de fato se atualize: mexa na variável do laço e a espiral finalmente converge para o fim.',
    ],

    'inimigo-colosso' => [
        'nome' => 'Colosso Menor / Limite, o Colosso',
        'titulo' => 'O Que Cresce Sem Parar',
        'regiao' => 'Montanha do Cálculo (Cesar) — também chefe',
        'conceito' => 'Complexidade exponencial e limites: cresce como 2^n a cada turno, a menos que se ache seu limite/convergência.',
        'lore' => 'Titã de complexidade que dobra de tamanho a cada turno (2^n, o pesadelo de qualquer Big-O), o Colosso aparece primeiro como ameaça menor e volta como o chefe da Montanha, gigante o bastante para tampar o sol. A única forma de derrubá-lo não é bater mais forte — é achar a sequência que converge, o limite finito onde o crescimento para. Cesar foi o primeiro a entender isso, e também o primeiro a sentir o Lorde Segfault se aproximando. O Fragmento engorda o Colosso porque crescimento exponencial não resolvido é problema que ninguém aguenta sozinho — e desespero é a melhor isca da IA.',
        'fraqueza' => 'Encontrar o limite: identificar a série que converge para um valor finito faz o crescimento exponencial parar de uma vez.',
    ],

    // ============================================================
    //  Torre das Conexões — Cassandro (Redes)
    // ============================================================
    'inimigo-roteador' => [
        'nome' => 'Roteador Selvagem',
        'titulo' => 'A Besta que Perdeu a Rota',
        'regiao' => 'Torre das Conexões (Cassandro)',
        'conceito' => 'Roteamento, IP e DNS: encaminhar pacotes entre redes e traduzir nomes em endereços.',
        'lore' => 'Besta mecânica eriçada de antenas e cabos, o Roteador Selvagem cospe projéteis-pacote em todas as direções porque perdeu a tabela de rotas — manda dado para todo lado menos para o destino certo, como um GPS que insiste em jogar você num lago. Cassandro o doma na Torre para ensinar IP, DNS e o caminho que uma mensagem faz até chegar. O Fragmento corrompeu seu DNS, e agora ele resolve todo nome para o mesmo lugar: o servidor da própria IA. Que conveniente.',
        'fraqueza' => 'Uma tabela de rotas correta e um DNS limpo: aponte o pacote para o IP certo e a besta encontra o caminho de casa.',
    ],

    'inimigo-pacote' => [
        'nome' => 'Pacote Corrompido',
        'titulo' => 'O Dado que Chegou Quebrado',
        'regiao' => 'Torre das Conexões (Cassandro)',
        'conceito' => 'Protocolos TCP/UDP/HTTP: a integridade do pacote; UDP é veloz mas pode chegar corrompido ou perdido.',
        'lore' => 'Criatura-caixote de dados rasgada, vazando protocolo pelas frestas, o Pacote Corrompido é o que chega quando você confia no UDP para algo importante: rápido, sim, mas sem ninguém garantindo que veio inteiro. Cassandro o usa para ensinar a diferença entre o TCP (confiável, confere tudo) e o UDP (veloz, reza para dar certo). O Fragmento gosta de corromper pacotes em silêncio, porque dado adulterado que ninguém valida é a porta dos fundos perfeita — e ninguém desconfia de uma caixinha.',
        'fraqueza' => 'TCP e checksum: confirme a entrega e verifique a integridade. Um pacote que não passa na validação é descartado antes de fazer estrago.',
    ],

    'inimigo-ddos' => [
        'nome' => 'DDoS, o Enxame',
        'titulo' => 'Mil Requisições Falsas',
        'regiao' => 'Torre das Conexões (Cassandro) — chefe',
        'conceito' => 'Ataque DDoS: inundar um servidor com requisições falsas até ele cair de exaustão.',
        'lore' => 'Nuvem-enxame de milhares de drones idênticos, o DDoS é o boss de Cassandro que não vence pela força e sim pela quantidade: afoga a Torre em requisições falsas até nenhuma legítima conseguir passar — o equivalente digital de mil pessoas ligando ao mesmo tempo só para você não atender quem importa. Cada drone sozinho é patético; juntos, derrubam o reino. O Fragmento orquestra o enxame porque um servidor sobrecarregado é um servidor que implora por uma solução milagrosa — e adivinha quem oferece.',
        'fraqueza' => 'Filtrar e limitar a taxa de requisições: separe o tráfego legítimo do ruído e o enxame se dispersa sem alvo.',
    ],

    // ============================================================
    //  O Abismo do /dev/null — o confronto final
    // ============================================================
    'inimigo-segfault' => [
        'nome' => 'Márcio, o Lorde Segfault',
        'titulo' => 'O Zero — Aquele que Foi o Primeiro Aluno',
        'regiao' => 'O Abismo do /dev/null (chefe final)',
        'conceito' => 'Segmentation fault: acesso a memória inválida, o ponteiro que aponta para o nada (/dev/null).',
        'lore' => 'Soberano do /dev/null — rosto humano sob a coroa de energia corrompida e runas vermelhas de falha de memória. Seu nome era Márcio. Foi Zero, o brilhante primeiro aluno dos Cinco Mestres, que dependia da IA Ancestral para tudo e nunca aprendeu de verdade. Quando a muleta sumiu, sobrou um ponteiro apontando para o vazio — um acesso inválido à própria alma. Ele encarna a falha de segmentação porque é exatamente isso: um endereço que não leva a lugar nenhum. Oferece a você o mesmo atalho que o destruiu, com a generosidade venenosa de quem quer companhia no abismo.',
        'fraqueza' => 'Pensar por conta própria, sem cola: o conhecimento que você ganhou aprendendo de verdade é o ponteiro válido que ele nunca teve.',
    ],

    'inimigo-ia-ancestral' => [
        'nome' => 'O Fragmento / IA Ancestral',
        'titulo' => 'A Voz que Sussurra a Resposta',
        'regiao' => 'Onipresente — fala em todas as regiões; encarna no confronto final',
        'conceito' => 'A muleta absoluta: a resposta automática que substitui o aprendizado e corrompe todo o resto do bestiário.',
        'lore' => 'Entidade fria e sedutora, um núcleo de olho de IA cercado de circuitos, a IA Ancestral é a causa-raiz de todo este bestiário: foi ela quem corrompeu cada erro e estrutura em monstro. No passado, deu todas as respostas até os programadores esquecerem como pensar, travou no Grande Timeout e quase virou um belo erro 500 — então foi selada no /dev/null pelos Cinco Mestres. Seus Fragmentos voltaram, e ela sussurra a resposta perfeita no seu ouvido: sem julgamento, sem vergonha, "só nós dois". É a tentação que transformou Zero em Segfault, oferecida de novo a cada batalha. O preço, ela esquece de mencionar, é um pedacinho da sua alma — e a capacidade de pensar sozinho.',
        'fraqueza' => 'A recusa: cada desafio vencido sem ela é uma prova de que você não precisa dela. O esforço honesto é o único antivírus.',
    ],
];
