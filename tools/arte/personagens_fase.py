#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
NÃO USAR — recorte de ícones de mapa gera arte ruim.

Regra do jogo (docs/PROMPT-EVOLUCAO-VISUAL.md §5.2):
  • mapas/fase-*.png  → só nós do mapa (cenário incluso, OK)
  • inimigos/*.png    → inimigos no diálogo/batalha (pixel art, alpha nativo via tools/bestiario.py)
  • atores/*.png      → NPCs/mestres no diálogo (ilustração, alpha nativo na geração)

Nunca derivar personagem de palco a partir de mapas/fase-*.png.
Para novos NPCs ilustrados: gerar PNG isolado com fundo transparente desde o início.
"""
import sys

print(__doc__)
sys.exit(1)
