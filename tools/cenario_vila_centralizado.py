#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import os, random
from pixelart import Canvas, _mix, _alpha, OUTLINE, WHITE, BROWN
from cenarios import vgrad, colina, estrelas, GW, GH, RAIZ

def vila_centralizada(cv):
    # Fundo (amanhecer)
    vgrad(cv, 0, 23, (255, 190, 140, 255), (150, 190, 255, 255))
    
    # Sol exatamente no centro
    cv.disc(32, 20, 6, (255, 230, 150, 255))
    cv.disc(32, 20, 8, _alpha((255, 230, 150, 255), 80))
    
    # Colina base
    colina(cv, 23, (70, 150, 90, 255))
    cv.rect(0, 23, GW-1, 24, (90, 175, 110, 255))
    
    # Caminho centralizado
    cv.rect(28, 24, 35, GH-1, (180, 150, 110, 255))
    cv.rect(29, 25, 34, GH-1, (160, 130, 90, 255))
    
    r = random.Random(1)
    # Casinhas distribuídas simetricamente
    posicoes = [
        (10, 23, 6, 7),   # Esquerda longe
        (46, 23, 6, 7),   # Direita longe
        (20, 24, 7, 8),   # Esquerda perto
        (36, 24, 7, 8)    # Direita perto
    ]
    
    for (bx, by, h, w) in posicoes:
        # Base da casa
        cv.rect(bx, by-h, bx+w, by, (180, 150, 110, 255))
        cv.rect(bx, by-h, bx+1, by, (140, 112, 80, 255))
        
        # Telhado
        cv.rect(bx-1, by-h-3, bx+w+1, by-h, (150, 70, 60, 255))
        cv.set(bx+w, by-h-3, (150,70,60,255))
        
        # Janela centralizada na casinha
        jx = bx + (w//2) - 1
        cv.rect(jx, by-h+2, jx+2, by-h+4, (255, 220, 120, 255))
        
        # Fumaça
        for s in range(3):
            cv.set(bx+w-2, by-h-4-s*2, _alpha(WHITE, 120-s*30))
            
    # Placas simétricas
    cv.rect(26, 21, 27, 24, BROWN)
    cv.rect(25, 19, 28, 21, (200,180,120,255))
    
    cv.rect(36, 21, 37, 24, BROWN)
    cv.rect(35, 19, 38, 21, (200,180,120,255))

if __name__ == "__main__":
    cv = Canvas(GW, GH)
    vila_centralizada(cv)
    caminho = os.path.join(RAIZ, "public", "img", "fundos_novos", "fundo-vila-python.png")
    cv.save(caminho)
    print(f"Gerado: {caminho}")