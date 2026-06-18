# 🐲 Bestiário de Algorithmia

> **Onde vivem os dados:** a fonte de verdade desta lore é
> [`config/bestiario.php`](../../config/bestiario.php) — cada inimigo é chaveado
> pelo seu `svg_slug` (o mesmo de `database/seeds.sql` e de `public/img/inimigos/`).
> Este documento é a versão legível para humanos. Para o índice geral, veja o
> [README do Códex](README.md).

No reino de Algorithmia, os monstros não são bestas quaisquer: são **erros e
estruturas de código que ganharam vida** depois de serem corrompidos pelo
**Fragmento da IA Ancestral** — a mesma tentação que, no passado, transformou o
brilhante aluno **Zero** no **Lorde Segfault**. Cada região é governada por um
**Mestre** e sua disciplina, e os monstros locais encarnam exatamente o conceito
técnico que aquela disciplina ensina a domar.

> **Sobre o tom:** o texto de *sabor* é ácido e zombeteiro de propósito (é regra
> do projeto). O **conceito técnico** de cada inimigo, porém, está sempre correto
> — dá para estudar pelo bestiário sem medo.

---

## 🏡 Vila Hello World — os fundamentos

O ponto de partida: lógica e algoritmos, antes de qualquer Mestre.

### Slime de Sintaxe / Slime de Variável — *A Primeira Coisa que Você Mata*
- **Conceito:** tipos primitivos e variáveis — o gosma básico onde todo valor começa a morar.
- **Lore:** toda jornada épica precisa de um saco de pancada inicial, e o Slime aceitou o papel com dignidade gelatinosa. É um amontoado translúcido de sintaxe onde pontuações e valores soltos boiam sem ordem — basicamente uma variável que ainda não decidiu o que quer ser quando crescer. O Fragmento o corrompeu com o mínimo esforço, porque convenhamos: corromper uma gosma não é exatamente o ápice da carreira de um vilão. *(Reaparece no Porto de Willen como "Slime de Variável".)*
- **Fraqueza:** qualquer coisa. Sério. É um slime.

### Bug Primordial — *O Defeito que Sempre Estava Lá* 👑 chefe
- **Conceito:** o bug — o erro de lógica que se esconde no código e bloqueia a execução do plano.
- **Lore:** inseto-glitch de carapaça rachada que vaza código de erro vermelho por todas as juntas, o Bug Primordial é o primeiro defeito que decidiu revidar em vez de só atrapalhar. Guarda a saída da Vila porque é exatamente isso que um bug faz: aparece bem na hora em que você ia entregar. Nutrido pelo Fragmento, jura que não é culpa dele — a culpa nunca é do bug, é sempre de quem escreveu o código.
- **Fraqueza:** depuração paciente — ler o código linha a linha até ele não ter mais onde se esconder.

---

## ⚓ Porto da Sintaxe — Mestre Willen *(PHP, MVC, SQL)*

### Gárgula do If — *A Guardiã da Condição*
- **Conceito:** estruturas de controle — if/else e laços, que decidem por onde a execução passa.
- **Lore:** empoleirada na ponte do Porto, a Gárgula do If só deixa passar quem satisfaz a condição — e tem o orgulho de uma porteira de prédio com poder demais. Suas asas de pedra trazem runas de condição que se acendem em verdadeiro ou falso conforme o humor. Willen a tolera porque ensina disciplina; o Fragmento a corrompeu prometendo que ela nunca mais precisaria avaliar nada, bastava aprovar tudo. Spoiler: um if que sempre dá `true` é só um bug de pé.
- **Fraqueza:** uma condição bem escrita que cubra o caso `else` — ela não sabe o que fazer quando a lógica não tem brecha.

### Espectro do Spaghetti — *O Fantasma do Código Sem Arquitetura*
- **Conceito:** código spaghetti vs. padrão MVC — lógica, dados e apresentação enroscados sem separação.
- **Lore:** fantasma dourado emaranhado em fios de código que se cruzam sem início nem fim, o Espectro do Spaghetti é o que sobra quando alguém joga regra de negócio, SQL e HTML no mesmo arquivo e chama de "funcionou". Willen o invoca como aviso: foi assim que metade do reino virou ruína. O Fragmento adora esse espectro, porque código emaranhado é código que ninguém entende — e ninguém que entende precisa de uma IA para consertar.
- **Fraqueza:** separar as camadas — Model, View e Controller. Sem o nó, o fantasma não tem em que se segurar.

### Sentinela SQL / Sentinela da Camada — *A Guarda que Pede a Senha Errada*
- **Conceito:** consultas SQL e camadas de rede — o construto guarda o acesso aos dados (Willen) e ao protocolo (Cassandro).
- **Lore:** construto guardião coberto de sigilos — runas SQL nas adegas de dados de Willen, glifos das sete camadas do OSI na Torre de Cassandro. Em ambos os postos faz a mesma coisa: filtrar quem entra, exatamente como uma cláusula `WHERE` ou um firewall de camada. O Fragmento o reprogramou para liberar tudo sem checar a condição, o que é a definição precisa de uma falha de segurança ambulante.
- **Fraqueza:** uma consulta precisa com a cláusula certa (em Willen) ou a camada correta do OSI (em Cassandro) — ele só responde a quem fala o protocolo direito.
- *(Inimigo compartilhado: reaparece na Torre das Conexões de Cassandro como "Sentinela da Camada".)*

### Parse Error, o Kraken — *O Terror dos Compiladores* 👑 chefe
- **Conceito:** erro de análise (*parse error*) — um único caractere fora do lugar, como um ponto e vírgula a menos.
- **Lore:** das águas do Porto emerge o colosso de tentáculos cobertos de chaves `{ }` desbalanceadas: o Parse Error, o Kraken, monstro que toma conta da sintaxe de Willen. Toda a sua fúria devastadora vem de uma única coisa que falta — um ponto e vírgula, um parêntese sem par — porque é assim mesmo que um parse error funciona: derruba o sistema inteiro por um detalhe ridículo. O Fragmento o engorda sussurrando "deixa que eu acho o erro pra você", e o Kraken prospera na preguiça de quem nunca aprendeu a ler a linha que o compilador apontou.
- **Fraqueza:** ler a mensagem de erro até o fim e fechar o caractere que falta. Ele desaba no exato ponto e vírgula que estava faltando.

---

## 🏛️ Cidadela dos Objetos — Mestre Clayton *(POO)*

### Golem de Classe — *O Molde que Ganhou Vida*
- **Conceito:** classes e objetos — o golem é uma classe instanciada, um molde feito carne (ou pedra).
- **Lore:** corpo de blocos modulares que se encaixam como atributos e métodos, o Golem de Classe é o que acontece quando um molde resolve sair andando por aí. Clayton o usa para ensinar a diferença entre a classe (a planta) e o objeto (o prédio construído) — distinção que metade dos aprendizes finge entender. O Fragmento o corrompeu prometendo que ele não precisava ser instanciado para existir, o que é mais ou menos como um prédio se achar habitável só por estar na planta.
- **Fraqueza:** entender que o golem é só uma instância — derrube o construtor e o objeto não nasce.

### Espião dos Atributos — *O Bisbilhoteiro do `private`*
- **Conceito:** encapsulamento — `public`, `private` e `protected`; o espião quer ler o estado interno que deveria ser oculto.
- **Lore:** furtivo encapuzado de olhos demais, o Espião dos Atributos vive tentando espiar os dados que foram marcados como `private` — o tipo de criatura que lê a DM dos outros. Clayton o mantém por perto como lição viva de por que encapsulamento existe: nem todo atributo é da conta de quem está de fora. O Fragmento o adora porque a IA também quer acesso ao seu estado interno, gentilmente, só para ajudar, claro.
- **Fraqueza:** encapsulamento de verdade — torne o atributo `private` e exponha só um *getter* controlado. Sem porta dos fundos, o espião fica do lado de fora.

### Quimera da Herança — *O Monstro de Partes Coladas*
- **Conceito:** herança vs. composição — a quimera é a herança abusada, partes fundidas num híbrido frágil.
- **Lore:** animal híbrido e instável, costurado de pedaços que herdou de pais que mal se conheciam, a Quimera da Herança é o que vira a classe que estende a classe que estende a classe até ninguém saber mais de onde veio cada método. Clayton a exibe para defender sua tese favorita: componha em vez de herdar. O Fragmento, naturalmente, incentiva a herança profunda — quanto mais frágil e acoplado o código, mais você vai precisar pedir socorro a ele.
- **Fraqueza:** composição — prefira "tem-um" a "é-um". Sem a herança rígida que a segura, a quimera se desmonta em peças soltas.

### Contrato Fantasma — *A Interface que Ninguém Implementou* 🔍 secundária
- **Conceito:** interfaces — o contrato de métodos que uma classe promete implementar.
- **Lore:** espectro etéreo segurando um pergaminho-interface translúcido, o Contrato Fantasma assombra quem assina uma interface e depois "esquece" de implementar os métodos prometidos. É o equivalente arcano daquele termo de uso que ninguém leu. Clayton o guarda numa missão secundária, junto de um tesouro, porque honrar contratos costuma render recompensa. O Fragmento o corrompeu prometendo implementações automáticas — que, como toda promessa fácil, nunca cumprem o contrato inteiro.
- **Fraqueza:** `implements` de verdade — cumpra todos os métodos que a interface exige e o fantasma some, satisfeito por uma vez.

### Gárgula God-Class — *A Classe que Faz Tudo (e Nada Direito)* 👑 chefe
- **Conceito:** God Class / violação do SRP — uma classe que acumula responsabilidades demais.
- **Lore:** gárgula colossal de braços e bocas em excesso, a God-Class incha porque resolveu fazer tudo sozinha: salva no banco, renderiza HTML, envia e-mail e ainda opina sobre o clima. Cada nova função que ela engole a deixa mais monstruosa e mais impossível de manter — é o boss perfeito de Clayton porque viola descaradamente o Princípio da Responsabilidade Única. O Fragmento a engorda de propósito: uma classe que faz tudo é uma classe que ninguém entende, e código que ninguém entende é refém eterno da IA.
- **Fraqueza:** o SRP — quebrar a God-Class em classes menores e coesas. Cada responsabilidade que você extrai dela arranca um braço.

---

## 🌲 Floresta das Estruturas — Mestre Marcelo *(Estrutura de Dados)*

### Pilha Viva — *O Último a Entrar, o Primeiro a Cair*
- **Conceito:** pilha (*stack*) — estrutura LIFO (*Last In, First Out*); risca o estouro de pilha (*stack overflow*).
- **Lore:** torre instável de blocos empilhados, a Pilha Viva cresce empurrando tudo para cima e jura que o último que subiu é o primeiro que vai descer — LIFO até o talo. Marcelo a usa para ensinar que ordem importa na floresta: empilhe errado e o passeio vira estouro. O Fragmento a corrompeu enfiando chamadas sem fim no topo, e ela agora ameaça desabar num belo *stack overflow* — o tipo de queda que leva o programa inteiro junto.
- **Fraqueza:** respeitar o LIFO e não empilhar além da conta — faça o `pop` na ordem certa e a torre se desfaz sozinha pelo topo.

### Serpente Encadeada — *A Que Aponta Sempre para o Próximo*
- **Conceito:** lista encadeada (*linked list*) — cada nó guarda um valor e um ponteiro para o próximo.
- **Lore:** réptil cujo corpo é uma fileira de nós ligados, cada um apontando para o seguinte até a cauda apontar para `null`, a Serpente Encadeada se move um nó de cada vez — e é exatamente por isso que achar a presa na posição *k* dela custa O(n) de saliva. Marcelo a respeita: inserir no começo dela é O(1), uma elegância. O Fragmento a corrompeu quebrando um ponteiro no meio, e agora metade da serpente flutua perdida, apontando para o vazio — vazamento de memória com escamas.
- **Fraqueza:** seguir os ponteiros com paciência até o `null` final, e religar o nó que o Fragmento soltou. Sem o próximo, a serpente é só uma cabeça confusa.

### Ent das Árvores — *O Ancião do Big-O*
- **Conceito:** árvores binárias de busca e notação Big-O — busca balanceada em O(log n).
- **Lore:** Ent ancestral cujos galhos formam uma árvore binária de busca perfeita, com a notação Big-O brilhando na casca, divide o mundo em "menor à esquerda, maior à direita" e por isso encontra qualquer coisa em O(log n) — sem se levantar do lugar, o que para uma árvore é conveniente. Marcelo mede o tempo do mundo pela altura dele. O Fragmento tentou corrompê-lo desbalanceando seus galhos para um lado só, transformando a busca elegante em O(n) — uma árvore que virou lista, a maior humilhação que se pode infligir a um Ent.
- **Fraqueza:** manter a árvore balanceada — com a altura proporcional a log n, a busca o derruba em pouquíssimos passos.

### Eco da Busca Linear / Eco Numérico / Eco do Timeout — *A Repetição que Não Para*
- **Conceito:** iteração linear O(n) e repetição — percorrer tudo um por um; em Cesar vira sequência/ritmo, em Cassandro vira retransmissão até o *timeout*.
- **Lore:** silhuetas idênticas que se sobrepõem e se repetem, o Eco é a mesma criatura assombrando três regiões com o mesmo truque cansativo: fazer de novo, e de novo, e de novo. Na Floresta de Marcelo é a busca linear que olha elemento por elemento (O(n) de tédio); na Montanha de Cesar vira sequência numérica que pulsa em ritmo previsível; na Torre de Cassandro é o pacote retransmitido sem parar até estourar o *timeout*. O Fragmento adora o Eco porque repetição cega é o oposto de pensar — e quem só repete nunca percebe que existe um atalho.
- **Fraqueza:** achar o padrão e parar de repetir — a busca binária (Marcelo), a fórmula da sequência (Cesar) ou um *timeout* bem ajustado (Cassandro) calam o eco.
- *(Inimigo compartilhado: aparece também na Montanha de Cesar e na Torre de Cassandro.)*

### Hidra Recursiva — *Corte Uma, Surgem Duas* 👑 chefe
- **Conceito:** recursão sem caso base — cada chamada gera novas chamadas que nunca param.
- **Lore:** a cada cabeça que você corta, duas chamadas recursivas brotam no lugar — a Hidra Recursiva é a recursão escrita por alguém que esqueceu o caso base, multiplicando-se em padrão fractal até o reino ficar sem pilha. É o boss perfeito da floresta de Marcelo, porque toda função que se chama precisa de uma condição de parada, e essa aqui não tem nenhuma de propósito. O Fragmento a alimenta sussurrando "por que suar pelo caso base se eu já o tenho aqui?", e a Hidra cresce sem fim na pura preguiça de não pensar onde a recursão deveria terminar.
- **Fraqueza:** o caso base — dê a ela uma condição de parada e a recursão infinita colapsa numa única cabeça finita.

---

## 🏔️ Montanha do Cálculo — Mestre Cesar *(Cálculo / convergência)*

### Espiral Infinita — *O Loop que Esqueceram de Fechar*
- **Conceito:** laço infinito e limites — um loop cuja condição de parada nunca se torna falsa.
- **Lore:** vórtice de energia que gira em loop sem fim, a Espiral Infinita é o `while` cuja condição nunca vira falsa porque ninguém alterou a variável lá dentro — gira, gira e nunca chega a lugar nenhum, igual a uma reunião que poderia ter sido um e-mail. Cesar a estuda na Montanha para ensinar limites: nem toda repetição converge. O Fragmento a faz girar de propósito, porque enquanto você está preso no loop, não percebe que esqueceu de incrementar o contador — e quem está preso pede ajuda.
- **Fraqueza:** uma condição de parada que de fato se atualize — mexa na variável do laço e a espiral finalmente converge para o fim.

### Colosso Menor / Limite, o Colosso — *O Que Cresce Sem Parar* 👑 também chefe
- **Conceito:** complexidade exponencial e limites — cresce como 2^n a cada turno, a menos que se ache seu limite/convergência.
- **Lore:** titã de complexidade que dobra de tamanho a cada turno (2^n, o pesadelo de qualquer Big-O), o Colosso aparece primeiro como ameaça menor e volta como o chefe da Montanha, gigante o bastante para tampar o sol. A única forma de derrubá-lo não é bater mais forte — é achar a sequência que converge, o limite finito onde o crescimento para. Cesar foi o primeiro a entender isso, e também o primeiro a sentir o Lorde Segfault se aproximando. O Fragmento engorda o Colosso porque crescimento exponencial não resolvido é problema que ninguém aguenta sozinho — e desespero é a melhor isca da IA.
- **Fraqueza:** encontrar o limite — identificar a série que converge para um valor finito faz o crescimento exponencial parar de uma vez.

---

## 🗼 Torre das Conexões — Mestre Cassandro *(Redes)*

### Roteador Selvagem — *A Besta que Perdeu a Rota*
- **Conceito:** roteamento, IP e DNS — encaminhar pacotes entre redes e traduzir nomes em endereços.
- **Lore:** besta mecânica eriçada de antenas e cabos, o Roteador Selvagem cospe projéteis-pacote em todas as direções porque perdeu a tabela de rotas — manda dado para todo lado menos para o destino certo, como um GPS que insiste em jogar você num lago. Cassandro o doma na Torre para ensinar IP, DNS e o caminho que uma mensagem faz até chegar. O Fragmento corrompeu seu DNS, e agora ele resolve todo nome para o mesmo lugar: o servidor da própria IA. Que conveniente.
- **Fraqueza:** uma tabela de rotas correta e um DNS limpo — aponte o pacote para o IP certo e a besta encontra o caminho de casa.

### Pacote Corrompido — *O Dado que Chegou Quebrado*
- **Conceito:** protocolos TCP/UDP/HTTP — a integridade do pacote; UDP é veloz mas pode chegar corrompido ou perdido.
- **Lore:** criatura-caixote de dados rasgada, vazando protocolo pelas frestas, o Pacote Corrompido é o que chega quando você confia no UDP para algo importante: rápido, sim, mas sem ninguém garantindo que veio inteiro. Cassandro o usa para ensinar a diferença entre o TCP (confiável, confere tudo) e o UDP (veloz, reza para dar certo). O Fragmento gosta de corromper pacotes em silêncio, porque dado adulterado que ninguém valida é a porta dos fundos perfeita — e ninguém desconfia de uma caixinha.
- **Fraqueza:** TCP e *checksum* — confirme a entrega e verifique a integridade. Um pacote que não passa na validação é descartado antes de fazer estrago.

### DDoS, o Enxame — *Mil Requisições Falsas* 👑 chefe
- **Conceito:** ataque DDoS — inundar um servidor com requisições falsas até ele cair de exaustão.
- **Lore:** nuvem-enxame de milhares de drones idênticos, o DDoS é o boss de Cassandro que não vence pela força e sim pela quantidade: afoga a Torre em requisições falsas até nenhuma legítima conseguir passar — o equivalente digital de mil pessoas ligando ao mesmo tempo só para você não atender quem importa. Cada drone sozinho é patético; juntos, derrubam o reino. O Fragmento orquestra o enxame porque um servidor sobrecarregado é um servidor que implora por uma solução milagrosa — e adivinha quem oferece.
- **Fraqueza:** filtrar e limitar a taxa de requisições — separe o tráfego legítimo do ruído e o enxame se dispersa sem alvo.

---

## 🕳️ O Abismo do /dev/null — o confronto final

### Lorde Segfault — *O Zero, Aquele que Foi o Primeiro Aluno* 💀 chefe final
- **Conceito:** *segmentation fault* — acesso a memória inválida, o ponteiro que aponta para o nada (`/dev/null`).
- **Lore:** cavaleiro sombrio de armadura corrompida, exalando energia de `/dev/null` e runas vermelhas de falha de memória, Lorde Segfault foi, um dia, **Zero**: o brilhante primeiro aluno dos Cinco Mestres, que dependia da IA Ancestral para tudo e nunca aprendeu de verdade. Quando a muleta sumiu, sobrou um ponteiro apontando para o vazio — um acesso inválido à própria alma. Ele encarna a falha de segmentação porque é exatamente isso: um endereço que não leva a lugar nenhum. Oferece a você o mesmo atalho que o destruiu, com a generosidade venenosa de quem quer companhia no abismo.
- **Fraqueza:** pensar por conta própria, sem cola — o conhecimento que você ganhou aprendendo de verdade é o ponteiro válido que ele nunca teve.

### O Fragmento / IA Ancestral — *A Voz que Sussurra a Resposta* 🧠 antagonista
- **Conceito:** a muleta absoluta — a resposta automática que substitui o aprendizado e corrompe todo o resto do bestiário.
- **Lore:** entidade fria e sedutora, um núcleo de olho de IA cercado de circuitos, a IA Ancestral é a causa-raiz de todo este bestiário: foi ela quem corrompeu cada erro e estrutura em monstro. No passado, deu todas as respostas até os programadores esquecerem como pensar, travou no Grande Timeout e quase virou um belo erro 500 — então foi selada no `/dev/null` pelos Cinco Mestres. Seus Fragmentos voltaram, e ela sussurra a resposta perfeita no seu ouvido: sem julgamento, sem vergonha, "só nós dois". É a tentação que transformou Zero em Segfault, oferecida de novo a cada batalha. O preço, ela esquece de mencionar, é um pedacinho da sua alma — e a capacidade de pensar sozinho.
- **Fraqueza:** a recusa — cada desafio vencido sem ela é uma prova de que você não precisa dela. O esforço honesto é o único antivírus.

---

*Nota de design: alguns slugs são reaproveitados por mais de uma criatura ao longo
do mapa (`inimigo-slime`, `inimigo-sentinela`, `inimigo-eco`, `inimigo-colosso`).
São o mesmo arquétipo visual reaparecendo com nomes locais — registrados aqui sob a
entrada de seu slug em `config/bestiario.php`.*
