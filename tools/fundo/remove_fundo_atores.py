#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Prepara PNGs ilustrados de diálogo: fundo transparente, personagem opaco."""
from __future__ import annotations

import glob
import os

from PIL import Image

from ator_pipeline import processar_palco

RAIZ = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
ATORES = os.path.join(RAIZ, "public", "img", "atores")
NPCS = ("npc-narrador.png", "npc-anciao.png")


def processar_arquivo(caminho: str, *, fundo_branco: bool = True) -> None:
    img = Image.open(caminho)
    img = processar_palco(img, fundo_branco=fundo_branco)
    img.save(caminho, optimize=True)
    print(f"  ok  {os.path.relpath(caminho, RAIZ)}")


def main() -> None:
    import sys

    todos = "--todos" in sys.argv or "--all" in sys.argv
    alvos: list[str] = []

    for nome in NPCS:
        p = os.path.join(ATORES, nome)
        if os.path.isfile(p):
            alvos.append(p)

    if todos:
        for p in sorted(glob.glob(os.path.join(ATORES, "fase-*.png"))):
            alvos.append(p)

    print(f"Processando {len(alvos)} atores...")
    for caminho in alvos:
        fundo_branco = os.path.basename(caminho).startswith("npc-")
        processar_arquivo(caminho, fundo_branco=fundo_branco)
    print("Concluido.")


if __name__ == "__main__":
    main()
