## Why

Algorithmia já tem direção de arte pixel art coesa e várias animações de batalha, mas
**não tem nenhum áudio** e o feedback visual de combate ainda é tímido. Som é a maior
lacuna de imersão: cada ação (acerto, dano, combo, coletar ouro, ceder ao Fragmento da IA)
hoje acontece em silêncio. Esta é a Fase 1 da evolução de "juice" — a de maior impacto e
menor risco — entregando uma camada de som procedural e reforçando o retorno visual das
ações de batalha, sem tocar em mecânica, segurança ou arquitetura.

## What Changes

- **Novo módulo de som procedural** (`public/js/som.js`, exposto como `window.SOM`),
  carregado no footer global. Web Audio API pura — osciladores + envelope + filtro,
  **sem bibliotecas e sem arquivos de áudio externos**. Desbloqueio do `AudioContext` no
  primeiro gesto do usuário (`click`/`keydown`/`touchstart`/`touchend`, via Promise de
  `resume()`).
- **Mapa evento → som** com timbre curto e distinto para: acerto, erro, dano recebido,
  dano causado, combo (pitch sobe a cada acerto encadeado), especial, poção/cura, coletar
  ouro, subir de nível, vitória, derrota, **Fragmento da IA** (timbre dissonante/corrompido),
  clique de UI, abrir/fechar modal e tic da máquina de escrever do diálogo.
- **Controle de volume + botão MUTE persistente** no header, estado salvo em `localStorage`
  (multi-página PHP) via um `GainNode` mestre.
- **Juice de batalha reforçado** (CSS + um sistema leve de partículas em `requestAnimationFrame`
  com object pooling): número de dano flutuante melhorado (arco + pop + cor por tipo,
  crítico maior), faíscas no impacto, explosão do "bug" derrotado, partículas ao coletar
  ouro, brilho no Especial e **screen-shake contido** (trauma-based, no contêiner da arena —
  nunca no `body`) na vitória/derrota e em grande dano.
- **Acessibilidade:** `prefers-reduced-motion` desliga shake, partículas e flutuações,
  mantendo feedback essencial por opacity/cor — respeitado tanto no CSS quanto no JS
  (gating do rAF via `matchMedia`).
- **Tokens:** uso exclusivo das variáveis de `:root` já existentes em `style.css`; nenhuma
  cor nova hardcoded (se faltar, derivar e propor como novo token).

## Capabilities

### New Capabilities
- `design-de-som`: camada de áudio procedural do jogo — síntese Web Audio sem dependências,
  desbloqueio do contexto, master gain, mute/volume persistentes e o mapa de eventos do jogo
  para timbres.
- `juice-batalha`: feedback visual de combate — partículas, números de dano flutuantes,
  brilho do especial e screen-shake contido, com respeito a `prefers-reduced-motion`.

### Modified Capabilities
<!-- Nenhuma capability de spec existente muda de requisito. As regras de batalha, reputação
     e Fragmento permanecem idênticas; esta change só adiciona feedback audiovisual sobre elas. -->

## Impact

- **Front-end apenas.** Arquivos afetados:
  - `public/js/som.js` (novo), `public/js/juice.js` (novo, sistema de partículas/shake),
    e edições em `public/js/batalha.js`, `public/js/dialogo.js`, `public/js/ui.js`,
    `public/js/app.js` para disparar sons/efeitos nos pontos certos.
  - `public/css/style.css` (controle de volume/mute no header, regras `prefers-reduced-motion`),
    `public/css/batalha.css` (números flutuantes melhorados, brilho do especial, canvas de
    partículas, shake da arena).
  - `app/views/layout/header.php` (botão de som/mute), `app/views/layout/footer.php`
    (incluir `som.js`/`juice.js`).
- **Sem mudanças** em PHP de domínio, banco, rotas, mecânica de batalha, reputação,
  Fragmento da IA, validação no servidor, CSRF/PDO/`e()`. Zero novas dependências.
- **Fora de escopo (fases seguintes):** parallax/cenários das 5 regiões, microanimações de
  itens na loja/inventário, estados de vitória/derrota dos sprites de personagens, e trilha
  ambiente em loop.
