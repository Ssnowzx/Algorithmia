## Why

A Fase 1 deu som e juice de batalha. Os **cenários**, porém, ainda são chapados: as seções
de região do mapa têm um fundo estático, a arena de batalha usa sempre o mesmo
`fundo-batalha.png` e não há vida ambiental por bioma. Esta Fase 2 dá **profundidade** às
5 regiões — parallax leve, partículas ambientais coerentes com cada bioma e transição
visível ao percorrer o mapa — e faz a arena refletir a região da fase, sem tocar em
mecânica nem arquitetura.

## What Changes

- **Mapa com profundidade:** cada `.regiao` ganha camadas de parallax leve (fundo +
  névoa/brilho) e **partículas ambientais por bioma** (folhas na Floresta, brasas na
  Montanha do Cálculo, pacotes/luz de rede na Torre, respingo/maresia no Porto, runas/glow
  na Cidadela), tudo em CSS declarativo. **Transição visível** ao rolar para uma região
  (revelação suave via `IntersectionObserver`, respeitando `prefers-reduced-motion`).
- **Arena por bioma:** o fundo da arena passa a ser o da região da fase (via
  `fundoRegiao()` a partir do mestre da fase), em vez do `fundo-batalha.png` fixo —
  **mantendo** o chão em perspectiva e o brilho arcano já existentes. Camada de partículas
  ambientais do bioma sobreposta à arena (CSS, atrás dos combatentes).
- **Coerência:** uso exclusivo dos tokens de `:root` e da `--cor-regiao` já existente;
  nenhum hex novo hardcoded. Sprites/fundos continuam pixel art (`image-rendering:
  pixelated`); nenhuma arte externa nova é importada (fundos por bioma já existem em
  `public/img/fundos/`).
- **Acessibilidade:** todo movimento ambiental (parallax, partículas, revelação) é desligado
  sob `prefers-reduced-motion`, mantendo os fundos estáticos legíveis.

## Capabilities

### New Capabilities
- `cenarios-ambientais`: profundidade e vida ambiental por bioma — parallax leve, partículas
  temáticas e transição de região no mapa, e fundo por bioma na arena, com respeito a
  `prefers-reduced-motion` e uso exclusivo dos tokens existentes.

### Modified Capabilities
<!-- Nenhuma capability de spec existente muda de requisito. As regras de batalha,
     progressão e liberação de fases permanecem idênticas; esta change é puramente visual. -->

## Impact

- **Front-end + uma leitura extra no controller.** Arquivos afetados:
  - `public/css/mapa.css` (parallax, partículas ambientais por bioma, transição de região).
  - `public/css/batalha.css` (fundo por bioma via variável, camada de partículas ambientais
    atrás dos combatentes, preservando chão/brilho).
  - `app/views/mapa/index.php` (classe de bioma por região para as partículas; atributo para
    o `IntersectionObserver`).
  - `app/views/batalha/arena.php` (aplicar o fundo do bioma e a classe de bioma na arena).
  - `app/controllers/BatalhaController.php` (`iniciar`): carregar o mestre da fase e passar
    o fundo/bioma para a view — única mudança em PHP, sem alterar regra de negócio.
  - `public/js/app.js` ou um pequeno trecho na view do mapa: `IntersectionObserver` para a
    revelação das regiões (com guarda de `prefers-reduced-motion`).
- **Sem mudanças** em banco, rotas, mecânica de batalha, progressão, validação no servidor,
  CSRF/PDO/`e()`. Zero novas dependências.
- **Fora de escopo (fases seguintes):** microanimações de itens (loja/inventário), estados
  de vitória/derrota dos sprites de personagens, e trilha ambiente em loop.
