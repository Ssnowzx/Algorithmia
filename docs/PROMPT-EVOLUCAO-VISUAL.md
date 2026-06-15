# PROMPT — Evolução visual, animação e som (Algorithmia)

> Prompt refinado e **adaptado ao projeto real** (Algorithmia — A Lenda dos Cinco Mestres).
> Cole isto como instrução para a IA que for trabalhar na camada visual/UX.
> Foi reescrito a partir de um template genérico de "jogo single-file + SVG": aqui o jogo
> é **PHP MVC multi-arquivo, pixel art PNG, com paleta e fontes já definidas**. As seções
> abaixo refletem isso — não invente estilo nem paleta novos, **evolua o que já existe**.

---

# OBJETIVO
Evoluir **Algorithmia — A Lenda dos Cinco Mestres** da versão atual para qualidade de
produção: identidade visual ainda mais coesa, animações fluidas a 60fps e design de som
imersivo (hoje inexistente). O jogo já tem direção de arte pixel art autoral e tema JRPG —
o trabalho é **aprofundar a coesão e dar "juice"** (feedback visual e sonoro em cada ação),
sem quebrar mecânicas, arquitetura ou as regras de desenvolvimento.

# CONTEXTO DO JOGO (já preenchido)
- **Gênero/mecânica:** RPG educativo (Duolingo + JRPG). Mapa-múndi de fases que se
  destravam em sequência; **batalha por turnos** onde responder desafios de programação
  causa/recebe dano.
- **Tema/ambientação:** "programar é magia". Mundo de fantasia tech, 5 regiões
  (Porto da Sintaxe, Cidadela dos Objetos, Floresta das Estruturas, Montanha do Cálculo,
  Torre das Conexões). Vilão: Lorde Segfault / IA Ancestral.
- **Sensação alvo:** épico-divertido com **humor ácido/autoconsciente**; nostalgia retro
  (pixel/chiptune) sem comprometer a clareza pedagógica das explicações.
- **Stack atual (FIXA, não trocar):** PHP 8 puro + MVC + MySQL/PDO. Front-end em
  **HTML/CSS/JS vanilla**, sem framework e **sem dependências de runtime**. Arte em
  **pixel art PNG** gerada por scripts Python/Pillow em `tools/`.
- **Estado atual (o que já existe e funciona):**
  - Tokens de cor e tipografia já definidos em `public/css/style.css` (`:root`).
  - Fontes: **Pixelify Sans** (títulos/HUD) + **Rubik** (corpo); mono p/ código.
  - 72 assets pixel art em `public/img/` (`herois/`, `inimigos/`, `mestres/`, `itens/`,
    `fundos/`, `ui/`), com `image-rendering: pixelated`.
  - Animações existentes: nós do mapa pulsando (`pulsa`), respiração idle dos sprites,
    investida + tremor na batalha, efeito máquina de escrever nos diálogos
    (`public/js/dialogo.js`), modal/toast temáticos (`public/js/ui.js`), fundo estelar.
  - JS por tela: `app.js` (global), `batalha.js`, `dialogo.js`, `ui.js`.
  - CSS por área: `style.css`, `mapa.css`, `batalha.css`.
- **O que NÃO pode quebrar (preservar 100%):**
  - Regras de batalha: combo (+25%/acerto, teto 4x), **Especial** (15 MP, dobra dano),
    **Poção**, **Fugir**, e a ordem de término vitória/derrota (inimigo 0 = vitória;
    herói 0 = derrota; acabaram desafios com inimigo vivo = derrota).
  - **Reputação** e o **Fragmento da IA** (−10 reputação, acerto automático, marca a fase).
  - Estrelas (1–3), liberação sequencial de fases, XP/níveis/ouro, os 3 finais.
  - Validação de resposta **sempre no servidor** (o gabarito nunca vai ao cliente).
  - Segurança existente: PDO/prepared, `e()`/escape, CSRF, `password_hash`, sessão.

# DIREÇÃO DE ARTE (coesão é requisito — e já existe uma base, RESPEITE-A)
- **Estilo único = pixel art autoral + tema JRPG escuro/vibrante já estabelecido.**
  Não criar estilo novo. Por que combina: pixel art reforça o subtexto "código/retro" de
  um mundo onde programar é magia, e o JRPG escuro dá peso épico às batalhas e à lore.
- **Paleta fixa = os tokens que JÁ existem em `:root` (`public/css/style.css`).** Use
  SOMENTE estes; nenhum hex solto fora deles. São eles:
  `--bg #0b0c1d`, `--bg-2 #14162c`, `--painel #1a1d38`, `--painel-2 #23274a`,
  `--borda #353c6b`, `--borda-luz #4a54a0`, `--texto #eef0fb`, `--texto-fraco #9aa0c9`,
  `--primaria #7c5cff`, `--primaria-2 #9d83ff`, `--hp #ff5d6c`, `--mp #4aa3ff`,
  `--xp #ffd23f`, `--ouro #ffce47`, `--sucesso #2ecc71`, `--erro #ff6b6b`,
  `--aviso #ffb142`. Se faltar uma cor, **derive** das existentes e proponha como NOVO
  token (não hardcode).
- **Linguagem visual consistente:** todo sprite novo/redesenhado mantém a mesma resolução
  base, mesma densidade de pixel, mesma fonte de luz e contorno dos assets atuais — herói,
  inimigo e item devem parecer "da mesma mão". Sprites novos devem ser gerados pelos
  scripts em `tools/` (Pillow), não importados de fontes externas.
- **Tipografia:** manter Pixelify Sans (título/HUD) + Rubik (corpo) + mono (código), com os
  fallbacks já presentes. Não adicionar fontes novas sem justificar.

# EVOLUÇÃO POR CATEGORIA
1. **CENÁRIOS (`fundos/`, `mapa.css`, `batalha.css`):** dar profundidade às 5 regiões —
   parallax leve em camadas, elementos ambientais animados coerentes por bioma (brasas no
   Cálculo, pacotes/luz de rede na Torre, folhas na Floresta), e transição visível ao trocar
   de região no mapa. Manter o chão em perspectiva e o brilho arcano já existentes na arena.
2. **ITENS (`itens/`, inventário, loja):** garantir que armas/escudos/poções/relíquias
   sigam o mesmo traço pixel; dar uma microanimação por item (poção borbulhando, relíquia
   brilhando, moeda/ouro girando) nas listas de inventário e loja, e um "pop" ao
   equipar/usar/comprar.
3. **PERSONAGENS (`herois/`, `inimigos/`, `mestres/`):** estados de animação por entidade —
   **idle** (respiração, já existe), **ação/ataque** (investida, já existe), **dano**
   (flash + tremor), **vitória** e **derrota**. Os 5 mestres e os 3 chefes merecem um idle
   mais expressivo que os inimigos comuns.

# ANIMAÇÃO (especificação — adaptada a PIXEL ART, não SVG)
- **Estados visuais distintos por entidade interativa:** idle, movimento, ação, dano,
  vitória/derrota. Trocar de estado = trocar classe CSS (o JS de batalha já controla isso).
- **Técnicas (priorize 60fps, anime só `transform` e `opacity` — nunca `top/left/width`
  que causam reflow):**
  - **Transforms CSS** (`translate/scale/rotate`) + `opacity` para movimento, pop, flash,
    tremor e flutuação idle. É o que o projeto já faz — mantenha esse padrão.
  - **Sprite-sheet com `animation-timing-function: steps(N)`** para walk/attack cycles
    quando quiser quadros desenhados (não interpolados). Anime sprites a **4–8fps** mesmo
    com a tela a 60fps: o "choppy" é o charme retro correto, e é barato.
  - **`image-rendering: pixelated`** em todo sprite escalado (já presente) para não borrar.
  - **`requestAnimationFrame`** apenas para partículas/efeitos dinâmicos com muitos elementos
    (coleta de ouro, faíscas de acerto, explosão de bug derrotado). Use CSS para tudo que for
    estado declarativo; use rAF só quando precisar de física/spawn por frame. Justifique a
    escolha em cada caso.
- **Timing:** transições de estado de UI **150–300ms, ease-out**; loops ambientais suaves
  (idle flutuando ~2s, moeda girando ~1s, pulso do nó atual ~1.6s — já existe).
- **Feedback de ação (juice — TODA ação responde na hora):** partículas ao coletar
  ouro/item; flash + shake ao tomar dano (herói e inimigo); "pop"/recuo no acerto; combo
  crescendo visualmente; número de dano flutuante; brilho no Especial; tremor de tela
  contido na vitória/derrota. **Nada de ação sem retorno visual.**
- **Acessibilidade:** respeitar `prefers-reduced-motion` — desligar/reduzir parallax,
  flutuações, partículas e shake; manter só transições essenciais e instantâneas.

# DESIGN DE SOM (imersão — HOJE NÃO EXISTE, é a maior novidade)
- **Web Audio API, som procedural via osciladores — combina com o tema retro/chiptune e
  respeita "sem dependências".** Use square/pulse para beeps de UI e acertos, sawtooth para
  dano/erro, mais envelopes (gain) e um filtro simples. **Sem arquivos de áudio externos
  pesados** sem autorização explícita.
- **Desbloqueio:** navegadores bloqueiam áudio até o 1º gesto — destravar o `AudioContext`
  no primeiro clique/tecla (ex.: no botão de criar personagem / iniciar fase).
- **Mapa evento → som (timbre curto e distinto para cada um):**
  acerto, erro, **dano recebido**, combo subindo, **Especial**, usar poção, coletar
  ouro/item, subir de nível, **vitória**, **derrota**, usar o **Fragmento da IA** (som
  "corrompido"/dissonante, reforçando que é o atalho que corrói a alma), clique de UI,
  abrir/fechar modal, máquina de escrever do diálogo (tic discreto por caractere).
- **Controle de volume + botão MUTE persistente** (estado salvo entre páginas — como é
  multi-página PHP, persistir via `localStorage` ou cookie/sessão; escolher e justificar).
- **Opcional (com meu OK):** trilha ambiente em loop, baixa, coerente por região, separada
  dos efeitos, com volume independente.

# RESTRIÇÕES TÉCNICAS
- **Entrega:** edições nos arquivos existentes (`public/css/*`, `public/js/*`,
  `app/views/*`), respeitando a separação MVC. Som novo deve ir num módulo próprio
  (ex.: `public/js/som.js`) exposto como `window.SOM`, no padrão de `window.UI`/`window.BATALHA`.
- **Dependências:** **nenhuma nova** (sem framework, sem libs de runtime, sem Composer).
- **Suporte:** desktop **e** mobile com controles touch; navegadores atuais.
- **Performance:** manter 60fps com vários elementos animados (batalha com partículas +
  sprites + HUD ao mesmo tempo).
- **Padrões de código do projeto:** `declare(strict_types=1)` no PHP, PHPDoc em funções
  públicas, nomes de domínio em **português**, escape `e()` em toda saída de view.

# PROCESSO (OBRIGATÓRIO — o projeto é spec-driven com OpenSpec)
1. **Antes de codar:** abra uma **proposta OpenSpec** (`/opsx:propose` ou skill
   `openspec-propose`) e apresente um **plano curto** + a direção (confirmação dos tokens
   existentes a usar, lista de estados de animação por entidade, mapa evento→som). **Espere
   meu OK.** Não fazer grandes mudanças direto no código sem proposta.
2. **Depois do OK:** implemente (`/opsx:apply`) e entregue o código **completo, sem trechos
   truncados**, pronto para rodar com `php -S localhost:8001`.
3. **Explique** as decisões principais (por que CSS vs rAF em cada efeito, por que cada
   timbre) e **como testar** cada novidade. Ao concluir, **arquive** (`/opsx:archive`).

# DEFINIÇÃO DE "PRONTO"
- Todo elemento segue a MESMA direção de arte e usa SOMENTE os tokens de `:root`.
- Toda ação tem feedback **visual E sonoro**.
- 60fps mantidos; **mute persistente** e **`prefers-reduced-motion`** funcionando.
- Mecânicas, segurança e arquitetura MVC preservadas — **zero regressão**.
- Mudança passou pelo fluxo OpenSpec (proposta → spec → implementação → arquivo).

---

## Referências de técnica (para fundamentar a implementação)
- Sprite sheet + `steps()` e por que animar a 4–8fps mesmo com tela a 60fps:
  [kirupa](https://www.kirupa.com/html5/sprite_sheet_animations_using_only_css.htm) ·
  [Pixnote — FPS de pixel art](https://pixnote.net/en/learn/animation/) ·
  [Otimização de sprite sheet](https://sosquishy.io/articles/sprite-sheet-optimization)
- Som de jogo procedural sem arquivos (osciladores + envelope + filtro):
  [Synth zero-dependência (Web Audio)](https://dev.to/hexshift/how-to-build-a-zero-dependency-audio-synth-in-the-browser-using-web-audio-api-1bp5) ·
  [Som em jogo de navegador](https://dinogame.gg/blog/how-to-add-sound-to-browser-game/)
