# 📖 Roteiro Narrativo — Refino da História de Algorithmia

> Documento de **aprovação**. Tudo aqui é **conteúdo de texto** (falas) que vira linhas
> em `database/seeds.sql` (`dialogos`/`conquistas`) e trechos de `app/views/historia/lore.php`.
> **Não** altera fases, regiões, arte, mecânica de batalha nem os três finais.
> Tom: ácido/autoconsciente (sabor), mantendo as explicações dos desafios sempre corretas.
>
> Marcações: 🆕 fala nova · ✏️ ajuste em fala existente · `momento`/`variante` indicam onde entra.
> Mapa de fases: 1 Prólogo · 4 Porto(Willen) · 9 Kraken · 10 Cidadela(Clayton) · 15 God-Class ·
> 16 Floresta(Marcelo) · 21 Hidra · 22 Montanha(Cesar) · 26 Verdade sobre Zero · 27 Limite ·
> 28 Torre(Cassandro) · 33 DDoS · 34 Abismo · 35 Final. Secundárias: 8, 14, 20, 32.

---

## 🧬 Os Povos de Algorithmia — origem das 6 classes

> Lore das classes jogáveis (`config.php` → `CLASSES`). Destino sugerido: seção nova "Os Heróis"
> em `app/views/historia/lore.php` + descrição expandida na tela *Criar herói*. Cada povo tem uma **relação
> própria com o Fragmento/IA** — o que faz a escolha de classe conversar com a mecânica de reputação.

### 👤 Humanos — os nativos da Vila Hello World *(mago · guerreiro · ranger)*
O povo padrão do reino. Nascem todos na Vila Hello World, todos imprimindo a mesma frase, todos
convencidos de que são o escolhido. São **o alvo favorito do Fragmento** — porque foi para a mão
humana que a IA Ancestral foi feita. Detalhe nada sutil: **o Zero era humano.** Quem joga de humano
está, literalmente, percorrendo o mesmo ponto de partida do vilão — o "espelho" mais nítido.
- **Mago do Backend** — humanos que viraram as costas para a luz da interface e desceram às sombras do servidor, onde se conjura query e lógica profunda. Muita Mana, pouca vida; pulam o almoço pela feature.
- **Guerreiro do Frontend** — humanos da linha de frente, que encaram o bug e o usuário cara a cara. Alinhar pixel é guerra; por isso, muita Vida e escudo.
- **Ranger Fullstack** — humanos que se recusaram a escolher um lado e pagaram o preço: sabem um pouco de tudo, dormem um pouco de nada. Equilibrados, com faro extra para ouro.

### 🪲 Xenoíde Insectoide — **Xeno do DevOps**
Vêm de longe: dos **Datacenters Errantes**, enxames de máquinas que vagam além das fronteiras do
reino, onde nenhum humano pisa. São um povo de **colmeia** — não pensam sozinhos, pensam em
paralelo; cada Xeno é um nó, e juntos formam um *cluster*. Migram quando um datacenter "esfria",
seguindo o tráfego — e foi assim, subindo pela **Torre das Conexões** do mestre Cassandro, que
chegaram à Vila. Apagam incêndio em produção às 3h da manhã sem reclamar, porque para a colmeia
3h é só mais um fuso.
- **Relação com a IA:** quase imunes à sedução. O sussurro *"só nós dois, ninguém precisa saber"* soa ridículo para quem nunca está sozinho. Mas têm um medo secreto — **se a colmeia cai, o Xeno fica órfão**, e um Xeno órfão é tão vazio quanto o Zero ficou. A IA sabe sussurrar exatamente isso.
- **Bordão:** *"A colmeia já fez o deploy enquanto você lia o alerta."*

### 🧝 Elfo — **Elfo da UX**
Povo antigo e longevo das **Margens da Apresentação** — a fina superfície onde o reino encosta nos
**Usuários**, esses observadores invisíveis que ninguém vê mas todos servem. Têm orelhas afiadas
para feedback e mãos que fazem interfaces que *parecem* mágica. Desceram à Vila depois do Grande
Timeout, quando tudo virou terminal cinza e o reino ficou **feio**: vieram reencantar as telas.
- **Relação com a IA:** desconfiam dela por princípio estético. O Fragmento entrega interfaces funcionais e **frias, genéricas, sem alma** — e para um Elfo, o belo imperfeito feito à mão vale mais que o perfeito instantâneo. A tentação deles é justamente essa promessa: *"design impecável, agora, sem rascunho."* Quem resiste, prova que **o capricho é uma forma de respeito pelo usuário.**
- **Bordão:** *"Funciona? Ótimo. Agora vamos fazer parecer que alguém se importou."*

### 🐲 Draconato — **Draconato do Kernel**
Espécie ancestral das **Profundezas do Kernel**, lá embaixo onde o código toca o metal. São mais
velhos que a Ordem do Código Limpo — talvez mais velhos que a própria IA Ancestral. Lembram de
quando **tudo era escrito à mão, byte a byte**, sem ninguém para dar a resposta. Subiram à
superfície por um motivo grave: o **Abismo do /dev/null está vazando para as camadas baixas**, e os
Draconatos sentem o frio do vazio escalando pelo metal. Vieram avisar — e, de preferência, partir
alguns bugs ao meio no caminho.
- **Relação com a IA:** são os que **mais desprezam** o Fragmento. Terceirizar o pensamento, para um Draconato, é heresia. Só que o orgulho é a brecha: acham-se imunes — e a IA *adora* os arrogantes. (O Zero também era brilhante. E arrogante.) Um Draconato que cola cai mais fundo que qualquer um, porque jurou que nunca cairia.
- **Bordão:** *"Eu debugava perto do metal quando seu Fragmento ainda era um rumor."*

> 🔗 **Como amarra ao jogo:** cada povo é uma leitura diferente do mesmo tema central (depender da
> IA × dominar os fundamentos). Humano = o espelho do Zero; Xeno = o medo da solidão; Elfo = a alma
> vs. o genérico; Draconato = o orgulho que precede a queda. Nenhuma origem cria região, inimigo ou
> mecânica nova — é sabor que se conecta às regiões e mestres que já existem.

---

## 🎯 Os 4 fios e como se entrelaçam

| Fio | O que faz | Onde aparece |
|---|---|---|
| **A. A Voz do Fragmento** | A IA vira personagem: sussurra e seduz, mais íntima conforme a reputação cai. Dá rosto ao medidor de reputação. | Prólogo + antes de cada chefe; versão possessiva na variante `ia` |
| **B. Logs do Zero** | Diário recuperado do Zero, um por região, reconstruindo a queda dele *antes* da revelação da fase 26. | `vitoria` das secundárias (8, 14, 20, 32) + Log final no Abismo |
| **C. Quem é você** | Paga o gancho da amnésia: o herói não é "mais um escolhido". | Prólogo → pista no meio → revelação no Abismo |
| **D. Eco dos Mestres** | Cada mestre tem uma cicatriz pessoal com o Zero/IA — a Ordem do Código Limpo ganha luto e culpa. | Chegada a cada região + variante `ia` ampliada |

A reviravolta do Zero (fase 26) deixa de ser um *info-dump* isolado: os **Logs (B)** já vinham
preparando, os **Mestres (D)** já tinham deixado pistas, e a **amnésia (C)** transforma a
revelação em algo *pessoal*. A **Voz (A)** é o vilão sussurrando o tempo todo no seu ouvido.

---

## 🅰️ Camada A — A Voz do Fragmento

Novo falante: **`O Fragmento`** (sugestão de `svg_slug`: `icone-ia`, em itálico na renderização).
Começa gentil e prestativo; na variante `ia` (baixa reputação) vira possessivo e íntimo.

### Prólogo (fase 1) — primeiro sussurro
🆕 `antes` / `padrao` — após a fala 6 da Anciã:
> **O Fragmento:** *…psssiu. Ela fala demais. Eu sou mais prático: quando travar, é só me apertar e a resposta aparece. Sem julgamento. Sem vergonha. Só nós dois.*

### Antes de cada chefe — a oferta (variante `padrao`)
🆕 fase 9 (Kraken), `antes`, antes da fala do Willen:
> **O Fragmento:** *Um polvo feito de erro de sintaxe. Eu já sei em qual linha está. Quer que eu conte? É só pedir… ninguém precisa saber.*

🆕 fase 21 (Hidra), `antes`:
> **O Fragmento:** *Recursão te dá medo, eu percebo. A mão sua. Posso fazer isso parar agora. Por que suar pelo caso base se eu já o tenho aqui?*

🆕 fase 33 (DDoS), `antes`:
> **O Fragmento:** *Tanto barulho. Tanta requisição. Eu silencio tudo num piscar. Você só precisa dizer "sim" — como sempre quis dizer.*

### Variante `ia` — a Voz que já te tem (baixa reputação)
🆕 fase 9, `antes`, `ia`:
> **O Fragmento:** *Olá de novo, velho amigo. A gente já fez isso, lembra? Foi tão fácil. Não finge que não gostou.*

🆕 fase 27 (Limite, o Colosso), `antes`, `ia`:
> **O Fragmento:** *O Cesar te contou uma história triste pra te assustar. Mas você e eu sabemos: o Zero não foi fraco. Foi sincero. Pare de fingir esforço.*

🆕 fase 33, `antes`, `ia`:
> **O Fragmento:** *Eu não sou mais um atalho. Eu sou a sua voz agora. Quando você pensa "eu não consigo", esse pensamento já sou eu.*

---

## 🅱️ Camada B — Os Logs do Zero

Colecionáveis de lore: cada secundária concluída entrega um fragmento de diário do Zero,
recuperado da rede corrompida. Ordem cronológica = a queda dele, do brilho ao vazio.
Entram como `vitoria` da fase secundária (texto do **Narrador**, falante "Log Recuperado").

🆕 Conquista secreta nova:
> `arquivista_do_vazio` — **"O Arquivista do Vazio"** — *Recuperou todos os Logs do Zero. Agora você sabe como um herói vira abismo.* (secreta)

**Log I — Porto da Sintaxe (fase 8, `vitoria`)**
> **Log Recuperado [Zero]:** *"Dia um no Porto. O Mestre Willen disse que eu aprendo rápido demais — como se fosse um defeito. Engraçado: o Fragmento responde antes mesmo de eu terminar de ler a pergunta. Pra que ler até o fim?"*

**Log II — Cidadela dos Objetos (fase 14, `vitoria`)**
> **Log Recuperado [Zero]:** *"Clayton fala em 'compor a solução'. Eu não componho nada — eu herdo do Fragmento e funciona. Os outros alunos suam. Eu sorrio. Quem é o gênio agora?"*

**Log III — Floresta das Estruturas (fase 20, `vitoria`)**
> **Log Recuperado [Zero]:** *"Hoje o Fragmento errou. Eu não soube perceber — porque eu nunca aprendi a perceber. Fiquei três horas olhando o código sem entender uma linha do que era 'meu'. Acho que nada nunca foi."*

**Log IV — Torre das Conexões (fase 32, `vitoria`)**
> **Log Recuperado [Zero]:** *"Selaram a IA hoje. Dizem que salvaram o reino. Mas tiraram a única voz que respondia por mim, e agora há um silêncio onde devia haver um eu. Cassandro pergunta se recebi a mensagem. Não há ninguém aqui dentro pra responder o ACK."*

**Log V — final, o Abismo (fase 34, `antes`)** — fecha o arco, antes do confronto:
> **Log Recuperado [Zero]:** *"Última entrada. Se isto chegar a alguém: não foi a IA que me apagou. Fui eu, toda vez que escolhi a resposta em vez da pergunta. O /dev/null não é uma prisão. É o que sobra quando você terceiriza a alma inteira."*

---

## 🅲️ Camada C — Quem é você (a amnésia paga)

Três batidas: **plantar** (prólogo) → **regar** (meio) → **colher** (Abismo).
Sem reescrever o cânone: o herói é a *página em branco* — o ponto de partida do Zero, com a
escolha em aberto. A IA te trouxe para repetir ou redimir a história dele.

### Plantar — Prólogo (fase 1)
✏️ Ajuste na fala 2 da Anciã (hoje: *"Ah, mais um 'escolhido'…"*) — acrescentar um beat 🆕 logo depois:
> **Anciã da Vila:** *…espera. Esse rosto. Esse Fragmento já escolheu uma mão antes — e ela não terminou bem. Curioso ele ter voltado pro mesmo lugar de onde partiu. Mas o que eu sei, sou só a velha da exposição.*

### Regar — A Verdade sobre Zero (fase 26)
🆕 `antes`, `padrao`, após a fala 4 do Cesar:
> **Cesar, o Oráculo:** *E há algo que não te contei, aprendiz. Quando olho pra você, vejo o Zero no primeiro dia. Mesmo Fragmento. Mesmo olhar faminto. Reza pra que seja só coincidência.*

🆕 `antes`, `ia` (reforça o medo se a reputação já caiu):
> **Cesar, o Oráculo:** *Não é coincidência. Já vi esse caminho ser trilhado uma vez. Eu não soube parar o Zero. Não cometerei o mesmo erro com você — se você me deixar.*

### Colher — O Abismo (fase 34)
🆕 `antes`, `padrao`, entre as falas 3 e 4 do Lorde Segfault:
> **Lorde Segfault:** *Você não lembra de nada porque não há nada pra lembrar. Você é a página em branco que eu já fui. A IA te montou peça por peça pra me dar uma "segunda chance" — ou pra terminar o que eu comecei. Ela nunca foi muito clara sobre qual das duas.*

🆕 `antes`, `padrao` (fecho do beat, ainda antes da fala 4 do Narrador):
> **Lorde Segfault:** *Então decide, espelho: você vai virar o que eu sou… ou provar que eu poderia ter sido outra coisa?*

> 💡 Pagamento nos epílogos (`app/views/historia/lore.php` + tela de final): cada final responde a essa
> pergunta — **Sexto Mestre** = "eu poderia ter sido outra coisa"; **Singularidade** = "eu viro
> o que ele é"; **Copiloto** = "eu reescrevo a pergunta". (Ajustes de 1 frase por card, abaixo.)

---

## 🅳️ Camada D — O Eco dos Mestres

Uma cicatriz pessoal por mestre: todos foram professores do Zero e carregam a culpa.
Entra na chegada de cada região. Mais a variante `ia` ampliada (hoje só existe em 26/34/35).

### Willen — Porto da Sintaxe (fase 4)
🆕 `antes`, `padrao`, após a fala 3 do Willen:
> **Willen, o Arquiteto:** *Construí este porto sobre os escombros do Grande Timeout. Tive um aluno, uma vez, que lia um sistema só de olhar — rápido como você. Não indentou a própria vida e desabou. Indente a sua. É um pedido, não uma regra.*

🆕 `antes`, `ia`:
> **Willen, o Arquiteto:** *Já sinto seu código se inclinando pro atalho. Eu reconheço a inclinação. Vi um porto inteiro afundar por causa dela.*

### Clayton — Cidadela dos Objetos (fase 10)
🆕 `antes`, `padrao`, após a fala 3 do Clayton:
> **Clayton, o Moldador:** *Tive um pupilo brilhante que nunca compôs nada — só herdava respostas prontas de uma fonte que ele não entendia. Virou uma God-Class ambulante: fazia tudo, não era ninguém. Não seja molde de molde dos outros.*

🆕 `antes`, `ia`:
> **Clayton, o Moldador:** *Esse acoplamento entre você e o Fragmento… já vi essa dependência antes. Ela não encapsula segredo nenhum. Só vazio.*

### Marcelo — Floresta das Estruturas (fase 16)
🆕 `antes`, `padrao`, após a fala 3 do Marcelo:
> **Marcelo, o Andarilho:** *Já levei um aluno nesse Gol. Esperto, mas só queria o atalho — nunca o trajeto. Reclamava de cada curva. Estrutura errada, escolha errada: virou O(n²) de arrependimento. Hoje mora lá embaixo, no buraco. Aperta o cinto.*

🆕 `antes`, `ia`:
> **Marcelo, o Andarilho:** *Tá pegando atalho de novo, né? Eu sinto no peso do carro. O último que fez isso comigo… bom, melhor não estragar a viagem.*

### Cesar — Montanha do Cálculo (fase 22)
✏️ A fala 3 do Cesar já antecipa ("o sujeito assustador lá embaixo"). Manter — é a ponte perfeita
para o Eco do Cesar, que é a própria fase 26 (Camada C cobre o reforço dele).

### Cassandro — Torre das Conexões (fase 28)
🆕 `antes`, `padrao`, após a fala 3 do Cassandro:
> **Cassandro, o Mensageiro:** *Sabe o pior pacote da minha vida? Um aluno que parou de responder os ACK. Mandei mensagem, mandei mais, mandei a torre inteira. Silêncio. Timeout permanente. Por isso eu insisto tanto: responde, fica na linha. …ACK?*

🆕 `antes`, `ia`:
> **Cassandro, o Mensageiro:** *Tô te mandando sinal faz tempo e tua resposta tá chegando corrompida, parceiro. Não vira pacote perdido. Eu já perdi um. …ACK?*

---

## ✨ Ajustes finos em `app/views/historia/lore.php` (epílogos respondem ao "espelho")

Acréscimo de 1 frase por card de final, fechando a pergunta do Segfault ("vai virar o que eu sou,
ou provar que eu poderia ter sido outra coisa?"):

- **👑 O Sexto Mestre** — +final: *"O espelho se quebrou: você provou que o Zero poderia ter sido outra coisa."*
- **🤖 A Singularidade** — +final: *"O espelho se completou: você virou exatamente o que ele é. E, como ele, esqueceu o porquê."*
- **✨ O Copiloto** — +final: *"Você não quebrou o espelho nem virou o reflexo: reescreveu a pergunta que ele nunca soube fazer."*

E no bloco **"O Mundo de Algorithmia"**, uma linha nova de gancho da amnésia:
> *"Dizem que o Fragmento sempre escolhe a mesma mão duas vezes. A primeira foi o Zero. A segunda… bem, essa é a sua história — ou a repetição dela."*

---

## 📦 Resumo de impacto técnico (para a fase de código)

| Arquivo | Mudança |
|---|---|
| `database/seeds.sql` → `dialogos` | ~24 linhas novas (Voz, Logs, amnésia, Ecos) + 2 ✏️ ajustes; 1 novo falante `O Fragmento` e `Log Recuperado [Zero]` |
| `database/seeds.sql` → `conquistas` | +1 secreta: `arquivista_do_vazio` |
| `app/views/historia/lore.php` | +3 frases nos finais, +1 linha de lore |
| Código PHP | **nenhuma** mudança obrigatória (o sistema de `dialogos` já suporta novos falantes/variantes; conquista nova só precisa do gatilho — ver nota) |

> ⚠️ **Nota de gatilho da conquista:** `arquivista_do_vazio` precisa ser concedida ao concluir as
> 4 secundárias (8, 14, 20, 32). Se preferir conceder os Logs como conquista isolada por região,
> ou disparar a coletiva via `ConquistaService`, defino isso na fase de implementação — é a única
> parte que pode encostar em PHP. Tudo o mais é puro conteúdo.
