# 🎨 A Evolução Visual

A cara de Algorithmia não nasceu pronta. O jogo passou por marcos visuais
distintos, e cada um foi preservado — arte nova nunca apaga o registro da antiga.

## As versões

### v1 — Pixel Art
O traço fundador. Sprites autorais em pixel art para mestres, inimigos, itens,
fundos, herói e ícones de UI — tudo desenhado à mão via os geradores em `tools/`.
Esta versão também guarda as fotos reais dos professores que inspiraram os
mestres e as fichas de galeria (*contact sheets*).

### v2 — Ilustrações *(atual)*
O salto para arte ilustrada de alta resolução: cards dos mestres, cenários,
cartas de herói, mapa e UI ornamentada. É a identidade que o jogo serve hoje,
espelhada em `public/img/` (somente PNG no histórico).

## O pipeline de recorte

As ilustrações da v2 chegam com fundo, mas o jogo precisa de sprites recortados
(alpha) para compô-los sobre os cenários. Esse recorte é automatizado com
**rembg** (remoção de fundo por rede neural) na ferramenta
[`tools/fundo/recortar_rembg.py`](../../tools/fundo/recortar_rembg.py), que roda em um
ambiente Python isolado — transformando uma ilustração de fundo cheio em um ator
recortado, pronto para o palco.

## Para folhear

- 📖 Histórico completo e regras de curadoria:
  [`docs/evolucao-visual/README.md`](../evolucao-visual/README.md)
- 🖼️ PDF-galeria com todas as versões lado a lado:
  [`docs/evolucao-visual/Evolucao-Visual.pdf`](../evolucao-visual/Evolucao-Visual.pdf)

> **Regra permanente:** só entra no histórico imagem relevante e de qualidade, um
> marco visual de verdade. Nada de rascunhos, testes ou *near-duplicates*. O
> arquivo guarda **PNG** (a fonte); os `.webp` de `public/img/` são cópias de
> performance e não entram. E nenhuma versão anterior é jamais apagada.
