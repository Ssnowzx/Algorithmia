#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Sprites pixel de corpo inteiro para cenas de diálogo (atores/ — não sobrescreve mestres/)."""
import os
from pixelart import MESTRES, Canvas, desenhar

RAIZ = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))


def gerar():
    dest = os.path.join(RAIZ, "public", "img", "atores")
    os.makedirs(dest, exist_ok=True)
    for nome, params in MESTRES.items():
        cv = Canvas()
        desenhar(cv, params)
        cv.save(os.path.join(dest, f"{nome}.png"))
        print(f"  ok  atores/{nome}.png")


if __name__ == "__main__":
    print("Gerando sprites de diálogo (mestres)...")
    gerar()
    print(f"Concluido: {len(MESTRES)} atores.")
