# 🎨 BRIEFING — Repintar os assets pixel-art (v1 → v2 ilustrado) · Algorithmia

> Documento para a IA geradora de imagens **com acesso ao projeto** (vê código, imagens e banco).
> **Missão:** substituir as imagens que ainda são **pixel art da v1** por **ilustrações no estilo v2**,
> salvando cada PNG por cima do arquivo atual. Inclui transformar os **itens da loja em cartas**
> (no mesmo formato dos personagens).

## 1) Estude o estilo-alvo (abra estas referências antes de gerar)

- `public/img/mestres/mestre-willen.png` — carta de personagem (moldura ouro + cena).
- `public/img/herois/card-draconato.png` — carta de herói.
- `public/img/atores/npc-anciao.png` — personagem em fundo transparente.
- `public/img/herois/hud-guerreiro.png` — card landscape de classe.

## 2) Fontes de verdade (cruze nomes/efeitos/raridades — não invente)

- **Classes jogáveis:** `config/config.php` → `const CLASSES` (nome, espécie, cor, lore).
- **Itens e raridades:** `database/seeds.sql` → `INSERT INTO itens`.
- **Inimigos:** `database/seeds.sql` → linhas das fases (coluna `svg_slug` + nome do monstro).

## 3) Bíblia de estilo (vale para TODA imagem)

Ilustração digital pintada à mão, alta resolução, **fantasia épica fundida com tecnologia arcana**
(magia + código). **Paleta oficial:** navy `#0b0c1d`, roxo `#7c5cff`/`#9d83ff`, ouro `#ffce47`,
runa ciano `#8ce6ff`. Iluminação dramática, brilho volumétrico, metais dourados, runas/glifos de
código (`</>`, `{}`, `;`, `0/1`) como magia rúnica. Acabamento de **card game premium**
(Hearthstone / Legends of Runeterra).

**Proibido:** pixel art, 8-bit, vetor chapado. **Sem texto/letras na imagem**
(o jogo escreve os nomes por cima em HTML).

## 4) Regras de saída (não-negociáveis)

- **Mesmo nome de arquivo, mesmo caminho** — sobrescreva o PNG atual em `public/img/...`.
- **Não toque** no que já é ilustração: `mestres/`, `atores/`, `herois/hud-*`, `herois/card-draconato`,
  `mapas/`, `fundos/`, `ui/molduras|logos|botoes|splash`.
- **Não toque** no arquivo histórico `docs/evolucao-visual/v1-pixel-art/` (registro da v1, preservado de propósito).
- ⚠️ **Ícones e troféus têm `.webp`** ao lado do PNG, e o helper `srcImagem()` serve o `.webp` primeiro.
  Depois de gerar o PNG novo, **regere o `.webp`** correspondente (ou apague o `.webp`, que o jogo cai no PNG).
  Inimigos, heróis e itens **não** têm `.webp` — basta o PNG.

---

## GRUPO A — Inimigos · `public/img/inimigos/`
**1024×1024 · fundo transparente · criatura full-body centralizada com leve sombra de contato**

| Arquivo | Criatura (conceito = erro/estrutura de código viva) |
|---|---|
| `inimigo-slime.png` | Slime verde-translúcido feito de sintaxe, pontuações boiando no gel |
| `inimigo-bug.png` | "Bug Primordial": inseto-glitch corrompido, carapaça rachada vazando código de erro vermelho |
| `inimigo-gargula.png` | Gárgula de pedra "do If", asas com runas de condição |
| `inimigo-espectro.png` | Espectro do "Spaghetti": fantasma dourado emaranhado em fios de código |
| `inimigo-sentinela.png` | Construto guardião com sigilos SQL / de camada de rede na armadura |
| `inimigo-kraken.png` | "Parse Error, o Kraken": polvo colossal de erros de sintaxe, tentáculos com `{ }` |
| `inimigo-golem.png` | Golem "de Classe", corpo de blocos modulares (objetos instanciados) |
| `inimigo-espiao.png` | "Espião dos Atributos": furtivo encapuzado, muitos olhos espiando dados privados |
| `inimigo-quimera.png` | Quimera da herança: animal híbrido instável de partes fundidas |
| `inimigo-fantasma.png` | "Contrato Fantasma": espectro etéreo com pergaminho-interface translúcido |
| `inimigo-godclass.png` | "Gárgula God-Class": gárgula gigante sobrecarregada, braços e bocas demais |
| `inimigo-pilha.png` | "Pilha Viva": torre de blocos empilhados (LIFO), instável |
| `inimigo-serpente.png` | Serpente cujo corpo é uma lista encadeada de nós ligados |
| `inimigo-ent.png` | Ent ancestral em estrutura de árvore binária, notação Big-O brilhando na casca |
| `inimigo-eco.png` | "Eco" da busca linear: silhuetas repetidas/ecoadas se sobrepondo |
| `inimigo-hidra.png` | Hidra recursiva: cabeças se multiplicando em padrão recursivo |
| `inimigo-espiral.png` | "Espiral Infinita": vórtice de energia em loop sem fim |
| `inimigo-colosso.png` | Titã de complexidade que cresce sem parar, escala imensa |
| `inimigo-roteador.png` | "Roteador Selvagem": besta mecânica de antenas e cabos, projéteis-pacote |
| `inimigo-pacote.png` | "Pacote Corrompido": criatura-caixote de dados rasgada vazando protocolo |
| `inimigo-ddos.png` | "DDoS, o Enxame": nuvem-enxame de milhares de drones/insetos idênticos |
| `inimigo-segfault.png` | **Chefe final** "Lorde Segfault": cavaleiro sombrio de armadura corrompida, energia de `/dev/null`, runas vermelhas de falha de memória |
| `inimigo-ia-ancestral.png` | "O Fragmento / IA Ancestral": entidade fria e sedutora, núcleo de olho de IA com circuitos (o vilão tentador) |

## GRUPO B — Heróis de batalha · `public/img/herois/`
**1024×1408 (retrato) · fundo transparente · corpo inteiro em pose de combate.** Use a `cor` de cada classe como acento.

| Arquivo | Personagem |
|---|---|
| `heroi-guerreiro.png` | Guerreiro do Frontend (humano), tanque de armadura pesada, escudo + espada · acento laranja `#ff7a59` |
| `heroi-mago.png` | Mago do Backend (humano), manto sombrio, conjura queries · acento roxo `#7c5cff` |
| `heroi-ranger.png` | Ranger Fullstack (humano), arco, equipamento leve, versátil · acento verde `#2ecc71` |
| `heroi-xeno.png` | Xeno do DevOps (humanoide insectoide xenoíde), estética colmeia/cluster · acento verde-água `#00e5a0` |
| `heroi-elfo.png` | Elfo da UX (elfo), orelhas afiadas, vestes etéreas, aura de interface · acento azul-claro `#a8d4ff` |
| `heroi-draconato.png` | Draconato do Kernel (dragonborn), escamas duras, tanque, energia do kernel · acento vermelho `#ff6b4a` (combine com o `card-draconato.png` existente) |

## GRUPO C — Cartas da loja (itens) · `public/img/itens/`
**1024×1536 (2:3) · CARTA com moldura ouro igual às de personagem.** Item flutua como artefato no centro,
com cena/aura ao fundo. **Aura conforme a raridade:** comum = prata/branco · raro = azul · épico = roxo · lendário = ouro/laranja. Sem texto na carta.

| Arquivo | Raridade | Item |
|---|---|---|
| `item-adaga.png` | comum | Adaga de Depuração, curta e afiada, fareja bugs |
| `item-espada.png` | raro | Espada da Sintaxe, runas, nunca esquece um `;` |
| `item-cajado.png` | raro | Cajado do Compilador, ponta cristalina |
| `item-machado.png` | épico | Machado do Refactor, pesado, derruba código morto |
| `item-lamina.png` | épico | Lâmina Polimórfica, metal mutável |
| `item-espada-lendaria.png` | lendário | Espada Lendária do Hello World, glifos "Olá, Mundo" dourados |
| `item-escudo.png` | comum | Escudo de Try-Catch |
| `item-broquel.png` | raro | Broquel do Validador (pequeno) |
| `item-egide.png` | épico | Égide do Firewall, barreira de energia |
| `item-anel.png` | comum | Anel do Indentador |
| `item-amuleto.png` | raro | Amuleto Big-O, gema |
| `item-elmo.png` | raro | Elmo do Clean Code |
| `item-bota.png` | raro | Bota do Loop Veloz, runas de velocidade |
| `item-pocao-hp.png` | comum | Poção de Vida Menor, frasco vermelho |
| `item-pocao-hp2.png` | raro | Poção de Vida Maior, frasco vermelho maior |
| `item-pocao-hp3.png` | épico | Poção de Vida Suprema, frasco radiante |
| `item-pocao-mp.png` | comum | Éter de Mana, frasco azul |
| `item-fragmento-ia.png` | lendário | Fragmento da IA Ancestral, esquirla corrompida e sedutora |
| `item-generico.png` | comum | Carta de item genérico/desconhecido (placeholder) |

## GRUPO D — Ícones de UI · `public/img/ui/icones/`
**512×512 · fundo transparente · ícone limpo (não é cena), dourado/gema** · ⚠️ regerar o `.webp`

| Arquivo | Ícone |
|---|---|
| `icone-ouro.png` | moeda de ouro com símbolo arcano |
| `icone-coracao.png` | coração de cristal vermelho (HP) |
| `icone-estrela.png` | estrela dourada (XP) |
| `icone-bau.png` | baú de madeira e ouro, brilho saindo |
| `icone-mapa.png` | pergaminho/mapa enrolado com bússola |
| `icone-nivel.png` | chevron ascendente brilhante (level up) |
| `icone-ia.png` | olho de IA / núcleo de circuito ciano-roxo (antagonista) |
| `icone-final.png` | coroa ou selo sobre caveira estilizada (capítulo final) |

## GRUPO E — Troféus / conquista · `public/img/ui/trofeus/`
**512×512 · fundo transparente · estilo heráldico com runas `</>`** · ⚠️ regerar o `.webp`

| Arquivo | Item |
|---|---|
| `troxeu-bronze.png` | troféu/medalha de bronze |
| `troxeu-prata.png` | troféu/medalha de prata |
| `troxeu-ouro.png` | troféu/medalha de ouro, mais imponente |
| `conquista-generica.png` | medalha de conquista genérica com estrela / selo `</>` |

---

## 5) Ao terminar

- Atualize o snapshot histórico copiando os PNGs novos para `docs/evolucao-visual/v2-ilustracoes/`
  (mesma estrutura), pois lá ainda estão as cópias pixel.
- Confira no jogo: inimigos na arena, loja, perfil e troféus.
