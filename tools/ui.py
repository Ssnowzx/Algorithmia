#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Pixel art dos 13 ícones de UI do Algorithmia (32x32)."""
import os, math
from pixelart import Canvas, _mix, _alpha, OUTLINE, WHITE, BROWN

RAIZ = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
G = 32
OURO = (255, 210, 70, 255); OURO_SH = (200, 150, 30, 255)
ROXO = (124, 92, 255, 255)


def logo(cv):
    cv.disc(16, 16, 13, _alpha(ROXO, 45))
    # escudo arcano
    r, rs = (140, 110, 240, 255), (90, 64, 190, 255)
    cv.rect(8, 5, 24, 18, r); cv.rect(8, 5, 9, 18, rs)
    cv.rect(10, 18, 22, 22, r); cv.rect(13, 22, 19, 27, r)
    cv.rect(8, 5, 24, 6, _mix(r, (255,255,255), 0.35))
    cv.ring(16, 15, 11, OURO)
    # "</>" dourado, claro e centralizado
    for (x, y) in [(13,12),(12,13),(11,14),(11,15),(12,16),(13,17)]: cv.set(x, y, OURO)  # <
    for (x, y) in [(18,12),(17,14),(16,15),(15,17)]: cv.set(x, y, OURO)                   # /
    for (x, y) in [(19,12),(20,13),(21,14),(21,15),(20,16),(19,17)]: cv.set(x, y, OURO)  # >
    cv.set(16, 8, WHITE); cv.set(22, 10, _alpha(WHITE, 160))  # brilhos


def icone_ouro(cv):
    cv.disc(16, 16, 11, _alpha(OURO, 40))
    cv.disc(16, 16, 9, OURO); cv.ring(16, 16, 9, OURO_SH); cv.disc(16, 16, 7, _mix(OURO,(255,255,255),0.2))
    cv.rect(15, 11, 16, 21, OURO_SH); cv.rect(13, 12, 18, 13, OURO_SH); cv.rect(13, 19, 18, 20, OURO_SH)  # $
    cv.set(12, 12, WHITE)


def icone_estrela(cv):
    cv.disc(16, 16, 11, _alpha(OURO, 45))
    pts = []
    for i in range(10):
        ang = math.radians(-90 + i*36); r = 10 if i % 2 == 0 else 4
        pts.append((16+r*math.cos(ang), 16+r*math.sin(ang)))
    # preenchimento simples por varredura
    for y in range(4, 28):
        xs = []
        for j in range(len(pts)):
            x1,y1 = pts[j]; x2,y2 = pts[(j+1)%len(pts)]
            if (y1<=y<y2) or (y2<=y<y1):
                xs.append(x1+(x2-x1)*(y-y1)/(y2-y1))
        xs.sort()
        for k in range(0, len(xs)-1, 2):
            cv.rect(round(xs[k]), y, round(xs[k+1]), y, OURO)
    cv.set(13, 13, WHITE)


def icone_coracao(cv):
    cv.disc(16, 17, 11, _alpha((255,93,108,255), 40))
    c, cs = (255, 93, 108, 255), (200, 50, 70, 255)
    cv.disc(12, 13, 4, c); cv.disc(20, 13, 4, c)
    for dy in range(0, 12):
        w = 8 - dy*0.7
        if w > 0: cv.rect(round(16-w), 13+dy, round(16+w), 13+dy, c)
    cv.disc(11, 12, 1.5, _mix(c,(255,255,255),0.5))  # brilho
    cv.rect(8, 16, 9, 17, cs)


def icone_nivel(cv):
    cv.disc(16, 16, 11, _alpha((74,163,255,255), 45))
    b, bs = (74, 163, 255, 255), (40, 110, 200, 255)
    cv.rect(9, 8, 23, 22, b); cv.rect(9, 8, 10, 22, bs)
    cv.rect(11, 22, 21, 24, bs); cv.rect(13, 24, 19, 26, bs)
    # seta para cima clara (chevron + haste)
    for (x, y) in [(16,10),(15,11),(17,11),(14,12),(18,12),(13,13),(19,13)]: cv.set(x, y, OURO)
    cv.rect(15, 13, 17, 20, OURO); cv.set(16, 13, _mix(OURO,(255,255,255),0.4))


def icone_bau(cv):
    cv.disc(16, 16, 11, _alpha(OURO, 30)); m, ms = (140,95,55,255), (100,66,38,255)
    cv.rect(8, 16, 24, 25, m); cv.rect(8, 16, 9, 25, ms)
    cv.rect(8, 10, 24, 16, m); cv.rect(8, 10, 24, 11, _mix(m,(255,255,255),0.2))
    cv.rect(8, 18, 24, 19, ms); cv.rect(14, 17, 18, 21, OURO); cv.set(16, 19, OUTLINE)


def icone_ia(cv):
    cv.disc(16, 16, 11, _alpha((90,220,255,255), 50))
    cv.disc(16, 16, 8, (40, 30, 80, 255)); cv.disc(16, 16, 6, (90, 220, 255, 255))
    cv.disc(16, 16, 3, (20, 30, 60, 255)); cv.disc(16, 16, 1.4, (200,240,255,255))
    for ang in range(0, 360, 45):
        cv.set(16+round(10*math.cos(math.radians(ang))), 16+round(10*math.sin(math.radians(ang))), (160,120,255,255))


def icone_final(cv):
    cv.disc(16, 16, 12, _alpha(ROXO, 50))
    for r in (11, 8, 5):
        cv.ring(16, 16, r, _mix(ROXO, (255,255,255), 0.1))
    cv.disc(16, 16, 3.5, OURO); cv.disc(16, 16, 2, (255,245,200,255))
    for (sx, sy) in [(8,8),(24,9),(9,23),(23,23)]: cv.set(sx, sy, WHITE)


def conquista_generica(cv):
    cv.rect(12, 20, 14, 30, (90,160,255,255)); cv.rect(18, 20, 20, 30, (90,160,255,255))  # fitas
    cv.set(13, 30, (60,120,210,255)); cv.set(19, 30, (60,120,210,255))
    cv.disc(16, 14, 9, _alpha(OURO, 40)); cv.disc(16, 14, 7, OURO); cv.ring(16, 14, 7, OURO_SH)
    cv.disc(16, 14, 4.5, _mix(OURO,(255,255,255),0.25))
    cv.set(16, 11, (255,255,255,255)); cv.set(15, 14, OURO_SH); cv.set(17, 14, OURO_SH)  # estrela simples
    cv.rect(15, 12, 17, 16, OURO_SH)


def placeholder(cv):
    cv.disc(16, 16, 12, _alpha(ROXO, 30))
    cv.ring(16, 16, 10, _mix(ROXO,(255,255,255),0.2))
    # "?"
    cv.rect(13, 10, 18, 11, (200,180,255,255)); cv.rect(18, 11, 19, 14, (200,180,255,255))
    cv.rect(15, 14, 16, 18, (200,180,255,255)); cv.rect(15, 20, 16, 21, (200,180,255,255))


def trofeu(cv, cor, cor_sh):
    cv.disc(16, 14, 11, _alpha(cor, 35))
    cv.rect(11, 7, 21, 14, cor); cv.rect(11, 7, 12, 14, cor_sh)        # taça
    cv.rect(11, 7, 21, 8, _mix(cor,(255,255,255),0.3))
    cv.rect(7, 8, 9, 12, cor); cv.rect(23, 8, 25, 12, cor)            # alças
    cv.rect(14, 14, 18, 18, cor_sh)                                   # haste
    cv.rect(11, 18, 21, 20, cor); cv.rect(13, 20, 19, 23, cor_sh)     # base
    cv.disc(14, 10, 1.5, _mix(cor,(255,255,255),0.5))


def troxeu_ouro(cv): trofeu(cv, OURO, OURO_SH)
def troxeu_prata(cv): trofeu(cv, (210,216,228,255), (150,158,176,255))
def troxeu_bronze(cv): trofeu(cv, (205,130,70,255), (150,90,45,255))


UI = {
    "logo": logo, "icone-ouro": icone_ouro, "icone-estrela": icone_estrela,
    "icone-coracao": icone_coracao, "icone-nivel": icone_nivel, "icone-bau": icone_bau,
    "icone-ia": icone_ia, "icone-final": icone_final, "conquista-generica": conquista_generica,
    "placeholder": placeholder, "troxeu-ouro": troxeu_ouro, "troxeu-prata": troxeu_prata,
    "troxeu-bronze": troxeu_bronze,
}


def gerar():
    for nome, fn in UI.items():
        cv = Canvas(G, G); fn(cv)
        cv.save(os.path.join(RAIZ, "public", "img", "ui", f"{nome}.png"))
        print(f"  ok  ui/{nome}.png")


if __name__ == "__main__":
    print("Gerando UI pixel art...")
    gerar()
    print(f"Concluido: {len(UI)} icones.")
