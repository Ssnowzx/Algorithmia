# 🎨 BRIEFING v3 — Corrigir a arte dos ITENS (objeto transparente, sem moldura) · Algorithmia

> Documento para a IA geradora de imagens **com acesso ao projeto** (vê código, imagens e banco).
> **Missão:** regerar os **19 PNGs de item** de `public/img/itens/` no padrão **certo** —
> apenas o **artefato em fundo transparente**, sem carta/moldura. Quem desenha a moldura
> é a própria UI do jogo. Esta é uma **correção** do GRUPO C do briefing v2.

---

## 0) Por que estamos refazendo (leia, é o ponto inteiro)

Na rodada anterior os 19 itens foram gerados como **cartas autossuficientes**: cada um
veio com **moldura de ouro sobre fundo preto/escuro**, como se fosse uma carta de
Hearthstone pronta. Parecia ótimo isolado — e ficou **errado dentro do jogo**.

Motivo: a **loja** (`app/views/loja/index.php`, classe `.carta-loja` em
`public/css/style.css`) e o **inventário** (`app/views/inventario/index.php`, classe
`.item-card` / `.icone-item`) **já envolvem cada item na própria carta de UI** — painel
arredondado azul-escuro, selo de raridade no canto, nome, descrição, efeitos, preço e
botões Comprar/Vender. Ou seja: a moldura **já existe em HTML/CSS**.

Resultado da arte antiga dentro dessa UI:
- **Carta dentro de carta:** a moldura de ouro da imagem briga com a borda arredondada da `.carta-loja`.
- **Retângulo preto:** o fundo escuro da imagem aparece como um bloco que não combina com o `--bg-2` do slot de arte.
- Inconsistência com **inimigos, heróis e ícones**, que agora são **recortes transparentes** soltos.

> **A correção (regra global desta versão):** a arte de um item é **SÓ O OBJETO**
> (a espada, a poção, o anel, o elmo…) **flutuando em fundo 100% transparente**.
> **Sem moldura. Sem carta. Sem cenário. Sem chão. Sem sombra de contato.**
> Apenas o artefato, com uma **aura mágica sutil na cor da raridade**.
> A moldura quem dá é a loja/o inventário.

---

## 1) Estude o estilo-alvo (abra estas referências antes de gerar)

- `public/img/inimigos/inimigo-kraken.png` — criatura em **fundo transparente**, recortada (este é o alvo de recorte).
- `public/img/ui/icones/icone-bau.png` — objeto/ícone limpo, transparente, dourado/gema.
- `public/img/mestres/mestre-willen.png` — referência de **pintura/iluminação** (NÃO de moldura: ignore a moldura aqui).
- `app/views/loja/index.php` + `.carta-loja*` em `public/css/style.css` — veja a moldura que a UI **já** desenha (não repita na imagem).
- `app/views/inventario/index.php` + `.item-card`/`.icone-item` — o item aparece pequeno dentro de `.icone-item`; precisa ler bem recortado.

## 2) Fontes de verdade (cruze nomes/efeitos/raridades — não invente)

- **Itens, raridades e efeitos:** `database/seeds.sql` → bloco `INSERT INTO itens` (18 itens, IDs 1..18).
- O 19º arquivo, `item-generico.png`, é o **placeholder/fallback** (não está no seed; é a arte padrão quando um item não tem `svg_slug` próprio).

---

## 3) Bíblia de estilo (vale para TODA imagem — herdada da v2)

Ilustração digital pintada à mão, alta resolução, **fantasia épica fundida com tecnologia
arcana** (magia + código). **Paleta oficial:** navy `#0b0c1d`, roxo `#7c5cff`/`#9d83ff`,
ouro `#ffce47`, runa ciano `#8ce6ff`. Iluminação dramática, brilho volumétrico, metais
dourados, runas/glifos de código (`</>`, `{}`, `;`, `0/1`) como magia rúnica. Acabamento de
**card game premium** (Hearthstone / Legends of Runeterra) **no objeto** — não na moldura.

**Proibido sempre:** pixel art, 8-bit, vetor chapado, **texto/letras** na imagem
(o jogo escreve o nome do item por cima, em HTML). Exceção: glifos rúnicos decorativos
(`</>`, `;`, `{}`) gravados no próprio artefato contam como ornamento, não como texto.

### 3.1) Regra desta versão — **ITEM = OBJETO TRANSPARENTE, SEM MOLDURA**

- **Só o artefato**, centralizado, ocupando ~80–90% do quadro, em **pose de produto**
  (levemente em três-quartos, como item de catálogo épico flutuando).
- **Fundo 100% transparente.** NADA atrás: sem cena, sem paisagem, sem pedestal, sem
  pergaminho, sem retângulo, sem vinheta, sem moldura de ouro, sem borda.
- **Sem sombra de contato / sem chão.** O objeto **flutua**. Nenhuma sombra projetada
  pintada no fundo (a sombra projetada é justamente o que estragou a versão anterior).
- **Aura mágica sutil na cor da raridade** — um *glow* que emana **do próprio objeto**
  (não um halo retangular, não um disco no chão), e se dissolve na transparência:
  - **comum** → aura prata/branca discreta
  - **raro** → aura azul (`#4aa3ff` / `#8cc6ff`)
  - **épico** → aura roxa (`#9d83ff`)
  - **lendário** → aura ouro/laranja (`#ffce47` / `#ff9a3c`), mais intensa e radiante
- **Enquadramento:** quadrado **1024×1024** (mesma proporção dos inimigos/ícones; o slot
  `.carta-loja__arte` usa `object-fit: contain`, então sobra transparente fica invisível).

---

## 4) Os 19 ITENS a REGERAR · `public/img/itens/`
**1024×1024 · fundo TRANSPARENTE · só o objeto flutuando · aura na cor da raridade · sem moldura/sem sombra de chão**

> Mesmo nome de arquivo, mesmo caminho, **sobrescrever** o PNG atual.

### Armas (`tipo: arma`)
| Arquivo | Raridade | Item (nome exato) + conceito |
|---|---|---|
| `item-adaga.png` | comum | **Adaga de Depuração** — adaga curta e afiada, fareja bug a um `console.log` de distância. Aura prata. (+3 atq) |
| `item-espada.png` | raro | **Espada da Sintaxe** — lâmina forjada no Porto da Sintaxe, runas gravadas, nunca esquece um `;`. Aura azul. (+7 atq) |
| `item-cajado.png` | raro | **Cajado do Compilador** — cajado de ponta cristalina que converte intenção em ação. Aura azul. (+6 atq, +2 def) |
| `item-machado.png` | épico | **Machado do Refactor** — machado pesado que derruba código morto de uma só vez. Aura roxa. (+11 atq) |
| `item-lamina.png` | épico | **Lâmina Polimórfica** — espada de metal mutável que assume a forma mais eficaz. Aura roxa. (+14 atq) |
| `item-espada-lendaria.png` | lendário | **Espada Lendária do Hello World** — a primeiríssima arma do reino; glifos dourados de "Olá, Mundo" gravados na lâmina, autoridade devastadora. Aura ouro/laranja radiante. (+20 atq) |

### Escudos (`tipo: escudo`)
| Arquivo | Raridade | Item (nome exato) + conceito |
|---|---|---|
| `item-escudo.png` | comum | **Escudo de Try-Catch** — escudo que captura exceções antes que te atinjam. Aura prata. (+5 def) |
| `item-broquel.png` | raro | **Broquel do Validador** — broquel pequeno que rejeita entradas inválidas. Aura azul. (+8 def) |
| `item-egide.png` | épico | **Égide do Firewall** — escudo-barreira de energia que bloqueia pacotes maliciosos. Aura roxa. (+13 def) |

### Acessórios (`tipo: acessorio`)
| Arquivo | Raridade | Item (nome exato) + conceito |
|---|---|---|
| `item-anel.png` | comum | **Anel do Indentador** — anel que mantém tudo no lugar (alinhamento perfeito). Aura prata. (+2 atq, +2 def) |
| `item-amuleto.png` | raro | **Amuleto Big-O** — amuleto com gema que otimiza cada golpe. Aura azul. (+5 atq) |
| `item-elmo.png` | raro | **Elmo do Clean Code** — elmo onde a clareza vira proteção. Aura azul. (+6 def) |
| `item-bota.png` | raro | **Bota do Loop Veloz** — bota com runas de velocidade, reflexos acelerados. Aura azul. (+3 atq, +3 def) |

### Poções (`tipo: pocao`)
| Arquivo | Raridade | Item (nome exato) + conceito |
|---|---|---|
| `item-pocao-hp.png` | comum | **Poção de Vida Menor** — frasco vermelho pequeno, restaura 40 HP. Aura prata. |
| `item-pocao-hp2.png` | raro | **Poção de Vida Maior** — frasco vermelho maior, restaura 90 HP. Aura azul. |
| `item-pocao-hp3.png` | épico | **Poção de Vida Suprema** — frasco vermelho radiante, restaura 200 HP. Aura roxa. |
| `item-pocao-mp.png` | comum | **Éter de Mana** — frasco azul que restaura 30 MP. Aura prata. |

### Especiais / placeholder
| Arquivo | Raridade | Item (nome exato) + conceito |
|---|---|---|
| `item-fragmento-ia.png` | lendário | **Fragmento da IA Ancestral** — esquirla de cristal corrompida e sedutora, núcleo-olho frio de IA com circuitos; sussurra a resposta perfeita. Aura ouro/laranja com um traço ciano-roxo doentio. (especial) |
| `item-generico.png` | comum | **Item genérico/desconhecido** (placeholder) — baú/saco de loot fechado ou caixote rúnico neutro, para itens sem arte própria. Aura prata. |

---

## 5) Outros assets — auditoria (NÃO obrigatório nesta rodada)

- **`public/img/atores/mestre-*.png` → LIXO, não regerar.** São relíquias de pixel art da
  v1 (~1,6–1,9 KB cada: `mestre-willen/clayton/marcelo/cesar/cassandro.png`). Os mestres
  **reais e ilustrados** vivem em `public/img/mestres/` (~3,4 MB cada). Esses arquivos em
  `atores/` estão **mortos** e **podem ser apagados** pela equipe — **não gere arte nova
  para eles**. (Anotação de limpeza, não tarefa de arte.)
- **`public/img/mapas/fase-*.png` → OPCIONAL / FUTURO.** A arte dos nós do mapa segue um
  **estilo de emblema antigo**. Funciona, mas não combina 100% com a linguagem nova.
  Fica registrado como **melhoria futura (v4)**, **não** é escopo desta rodada.

---

## 6) ⚠️ CRÍTICO — como entregar a transparência DE VERDADE (foi isto que estragou a v2)

Na rodada anterior os PNGs **não tinham transparência real**. A ferramenta entregou:
1. um **fundo xadrez branco/cinza PINTADO** (o padrão de "transparência" desenhado **como
   pixels**, sem canal alfa); e
2. uma **sombra de contato cinza pintada** embaixo do objeto.

Dentro do jogo, isso virou **um retângulo branco/cinza com sombra** — exatamente o que não
queremos. Para evitar repetir o erro, siga **uma** destas duas saídas, nesta ordem de preferência:

### Opção A (preferida) — PNG RGBA com canal alfa REAL
- Exporte **PNG-32 (RGBA)** com **canal alfa de verdade**: os pixels fora do objeto têm
  **alfa = 0** (transparentes), não uma cor pintada.
- **NÃO** desenhe o padrão xadrez de "transparência". Aquele xadrez é só a forma como
  editores **exibem** o alfa — ele **nunca** deve virar pixel no arquivo final.
- **NÃO** adicione sombra projetada, halo retangular, vinheta ou borda. Só o objeto + sua aura,
  e a aura **se dissolve no alfa** (vai ficando transparente nas bordas).

### Opção B (se a ferramenta NÃO exporta alfa real) — fundo CHAPADO magenta
- Entregue o objeto sobre um **fundo de cor única e sólida: magenta puro `#FF00FF`**.
- O fundo deve ser **100% chapado**: **sem gradiente, sem xadrez, sem textura, sem vinheta,
  sem sombra de contato, sem reflexo no chão.** Cor lisa, do canto ao canto.
- Use magenta porque **nenhum item** do jogo é magenta — isso garante recorte limpo por
  croma. (Não use branco, preto nem cinza: essas cores aparecem nos itens e na aura e seriam
  comidas no recorte.)
- A aura de raridade pode existir, **mas** evite que ela se confunda com o magenta; mantenha-a
  presa ao objeto.

### Regras inegociáveis (valem para A e B)
- ❌ **NUNCA** pinte um falso xadrez de transparência.
- ❌ **NUNCA** adicione sombra projetada / sombra de contato no fundo.
- ❌ **NUNCA** entregue moldura, carta, cenário, pedestal ou borda.
- ✅ Fundo é **transparente de verdade** (A) **ou** **magenta `#FF00FF` chapado** (B). Nada além disso.

> **Aviso para a IA:** a equipe vai passar todos os PNGs pelo **rembg** (modelo
> **`isnet-general-use`**) para finalizar/garantir o recorte. Por isso um fundo **limpo,
> chapado e uniforme** (ou um alfa real) é essencial: qualquer xadrez pintado, gradiente ou
> sombra no fundo **atrapalha o recorte** e volta como artefato no jogo.

---

## 7) Ao terminar

- Confira **no jogo**, não isolado: abra a **Loja** e o **Inventário** e veja se cada item
  aparece **flutuando dentro da carta da UI**, sem retângulo de fundo e sem moldura dupla.
- Verifique especialmente **épico** e **lendário** (a `.carta-loja` já aplica aura própria;
  a aura da arte deve **somar**, não brigar).
- Atualize o snapshot histórico copiando os PNGs novos para
  `docs/evolucao-visual/v2-ilustracoes/itens/` (mesma estrutura) — **só PNG curado**, sem `.webp`.
- Itens **não** têm `.webp`: basta o PNG.
