#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Pixel art dos 24 inimigos/criaturas do Algorithmia (grade 40x40)."""
import os, math
from pixelart import Canvas, _mix, _alpha, OUTLINE, WHITE, BROWN

RAIZ = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
G = 40


def base(cv, glow=None):
    cv.ellipse(20, 37, 11, 2, (0, 0, 0, 90))
    if glow:
        cv.disc(20, 20, 13, _alpha(glow, 30))


def olhos(cv, x1, x2, y, cor=WHITE, pup=OUTLINE, r=1):
    for cx in (x1, x2):
        cv.rect(cx-r, y-r, cx+r, y+r, cor)
        cv.set(cx, y, pup)


# ---------- criaturas ----------
def slime(cv):
    base(cv, (63, 207, 107, 255))
    g, gl = (63, 207, 107, 255), (158, 255, 192, 255)
    cv.disc(20, 26, 11, g)
    cv.rect(9, 26, 31, 33, g)
    cv.ellipse(20, 33, 11, 3, _mix(g, (0,0,0), 0.25))
    cv.disc(15, 20, 3, _alpha(gl, 160))
    olhos(cv, 15, 25, 26, WHITE, (20,58,34,255), 2)
    cv.rect(18, 31, 22, 31, (20,58,34,255))
    cv.rect(19, 18, 19, 22, (20,58,34,255)); cv.rect(19, 22, 21, 22, (20,58,34,255))  # ;
    cv.disc(21, 24, 1, (20,58,34,255))


def bug(cv):
    base(cv, (181, 63, 166, 255))
    b, bs = (150, 60, 150, 255), (110, 40, 110, 255)
    cv.disc(20, 24, 9, b); cv.disc(20, 28, 7, bs)
    cv.rect(14, 14, 16, 12, OUTLINE); cv.rect(24, 14, 26, 12, OUTLINE)   # antenas
    cv.disc(14, 11, 1.5, (255,80,80,255)); cv.disc(26, 11, 1.5, (255,80,80,255))
    olhos(cv, 16, 24, 22, (255,90,90,255), (120,0,0,255), 2)
    for sy in (24, 28, 32):                                             # pernas
        cv.rect(8, sy, 12, sy, OUTLINE); cv.rect(28, sy, 32, sy, OUTLINE)
    cv.rect(20, 18, 20, 30, bs)


def gargula(cv):
    base(cv, (150, 150, 165, 255))
    s, ss = (140, 142, 155, 255), (96, 98, 112, 255)
    cv.rect(10, 14, 14, 26, ss); cv.rect(26, 14, 30, 26, ss)            # asas
    cv.rect(7, 16, 11, 24, ss); cv.rect(29, 16, 33, 24, ss)
    cv.disc(20, 22, 8, s); cv.rect(14, 22, 26, 34, s)
    cv.rect(15, 14, 17, 17, s); cv.rect(23, 14, 25, 17, s)             # chifres
    olhos(cv, 16, 24, 22, (255,210,63,255), (120,80,0,255), 1)
    cv.rect(16, 27, 24, 28, ss); cv.rect(17, 28, 23, 29, OUTLINE)      # boca
    cv.rect(13, 34, 17, 36, ss); cv.rect(23, 34, 27, 36, ss)


def espectro(cv):
    base(cv, (255, 210, 90, 255))
    for i, y in enumerate(range(12, 30, 2)):                           # fios dourados
        off = 3*math.sin(i)
        cv.rect(round(12+off), y, round(28+off), y, _alpha((255,205,90,255),200))
    cv.disc(20, 22, 9, _alpha((250,220,120,255), 90))
    olhos(cv, 16, 24, 22, (190,130,255,255), (60,10,90,255), 1)
    for x in (13, 17, 21, 25):                                         # base esfarrapada
        cv.rect(x, 30, x+1, 33+(x%3), _alpha((255,205,90,255),180))


def sentinela(cv):
    base(cv, (74, 150, 230, 255))
    m, ms = (70, 120, 200, 255), (45, 80, 150, 255)
    cv.rect(12, 12, 28, 24, m); cv.rect(12, 12, 14, 24, ms)
    cv.rect(14, 15, 26, 20, (10,20,40,255))                            # visor
    cv.rect(15, 17, 25, 18, (90,220,255,255))
    cv.rect(16, 16, 24, 16, None)
    cv.rect(15, 25, 25, 34, ms); cv.rect(8, 25, 12, 30, m); cv.rect(28, 25, 32, 30, m)
    cv.disc(20, 8, 2, (90,220,255,255)); cv.rect(20, 8, 20, 12, ms)    # antena
    # "SQL"
    cv.rect(16, 17, 16, 18, WHITE); cv.rect(19, 17, 20, 18, WHITE); cv.rect(23, 17, 24, 18, WHITE)


def kraken(cv):
    base(cv, (150, 70, 200, 255))
    p, ps = (140, 70, 190, 255), (95, 45, 135, 255)
    cv.disc(20, 18, 9, p)                                              # manto
    cv.rect(14, 16, 26, 14, ps)
    for i, x in enumerate(range(8, 33, 4)):                            # tentáculos
        cv.rect(x, 24, x+2, 35-(i%3*2), ps)
        cv.set(x+1, 35-(i%3*2), p)
    olhos(cv, 16, 24, 18, (255,220,60,255), (120,60,0,255), 2)
    cv.rect(19, 4, 19, 8, (255,70,80,255)); cv.rect(19, 8, 21, 8, (255,70,80,255))  # ; vermelho
    cv.disc(21, 10, 1, (255,70,80,255))


def golem(cv):
    base(cv, (120, 150, 120, 255))
    r, rs = (118, 120, 100, 255), (86, 88, 72, 255)
    cv.rect(12, 14, 28, 32, r); cv.rect(12, 14, 14, 32, rs)
    cv.rect(15, 8, 25, 14, r); cv.rect(6, 16, 12, 28, r); cv.rect(28, 16, 34, 28, r)  # cabeça/braços
    olhos(cv, 18, 22, 11, (90,230,230,255), (0,80,80,255), 1)
    cv.rect(15, 20, 16, 26, (90,230,230,255)); cv.rect(16, 20, 17, 21, (90,230,230,255)); cv.rect(16, 25, 17, 26, (90,230,230,255))  # {
    cv.rect(24, 20, 25, 26, (90,230,230,255)); cv.rect(23, 20, 24, 21, (90,230,230,255)); cv.rect(23, 25, 24, 26, (90,230,230,255))  # }
    cv.rect(14, 32, 19, 36, rs); cv.rect(21, 32, 26, 36, rs)


def espiao(cv):
    base(cv, (90, 90, 110, 255))
    c, cs = (44, 44, 60, 255), (28, 28, 40, 255)
    cv.disc(20, 14, 7, c)                                              # capuz
    cv.rect(12, 14, 28, 36, c); cv.rect(12, 14, 15, 36, cs)
    cv.disc(20, 16, 5, (12,12,20,255))                                 # sombra do rosto
    olhos(cv, 18, 22, 16, (255,220,60,255), (255,220,60,255), 0)
    cv.rect(20, 16, 20, 16, None)
    cv.rect(14, 36, 26, 37, cs)


def quimera(cv):
    base(cv, (220, 90, 60, 255))
    o, os_ = (205, 90, 55, 255), (150, 60, 35, 255)
    cv.disc(20, 26, 9, o); cv.rect(11, 26, 29, 33, o)
    for hx in (13, 20, 27):                                            # três cabeças
        cv.disc(hx, 16, 4, o); cv.rect(hx-2, 12, hx-1, 14, os_)
        cv.set(hx-1, 16, (255,230,60,255)); cv.set(hx+1, 16, (255,230,60,255))
    cv.rect(15, 33, 19, 36, os_); cv.rect(21, 33, 25, 36, os_)


def fantasma(cv):
    base(cv, (130, 200, 255, 255))
    f = _alpha((150, 210, 255, 255), 150)
    cv.disc(20, 20, 9, f); cv.rect(11, 20, 29, 30, f)
    for x in (12, 16, 20, 24, 28):
        cv.rect(x, 30, x+2, 33, f)
    olhos(cv, 16, 24, 19, (240,250,255,255), (60,110,170,255), 1)
    cv.rect(15, 25, 25, 25, _alpha((90,140,200,255),120))             # "interface" tênue


def godclass(cv):
    base(cv, (150, 70, 200, 255))
    p, ps = (130, 60, 180, 255), (90, 40, 130, 255)
    cv.disc(20, 20, 12, p); cv.rect(8, 20, 32, 34, p)
    for ang in (200, 230, 310, 340):                                  # braços demais
        ex = 20+round(13*math.cos(math.radians(ang))); ey = 22+round(13*math.sin(math.radians(ang)))
        cv.rect(min(20,ex), min(22,ey), max(20,ex), max(22,ey), ps)
    cv.rect(6, 18, 10, 30, ps); cv.rect(30, 18, 34, 30, ps)
    olhos(cv, 15, 25, 18, (255,80,80,255), (120,0,0,255), 1)
    cv.set(20, 18, (255,80,80,255))                                   # 3º olho
    cv.rect(15, 24, 25, 26, OUTLINE); cv.rect(16, 25, 24, 25, (255,80,80,255))


def pilha(cv):
    base(cv, (63, 207, 107, 255))
    cols = [(70,200,110,255),(60,180,100,255),(80,210,120,255)]
    for i, y in enumerate(range(30, 12, -6)):                         # blocos empilhados
        c = cols[i % 3]
        cv.rect(12, y, 28, y+5, c); cv.rect(12, y, 28, y, _mix(c,(255,255,255),0.2))
        cv.rect(12, y+5, 28, y+5, _mix(c,(0,0,0),0.3))
    olhos(cv, 17, 23, 16, WHITE, (20,58,34,255), 1)
    cv.rect(18, 19, 22, 19, (20,58,34,255))
    cv.rect(19, 6, 21, 10, (255,210,63,255)); cv.rect(17, 8, 23, 8, (255,210,63,255))  # seta push
    cv.set(20, 5, (255,210,63,255))


def serpente(cv):
    base(cv, (63, 190, 107, 255))
    g = (70, 190, 110, 255)
    pts = [(10,30),(16,26),(22,30),(28,24),(30,18),(24,14)]
    for (x, y) in pts:
        cv.disc(x, y, 3, g); cv.disc(x, y, 1.4, _mix(g,(255,255,255),0.2))
    for i in range(len(pts)-1):
        cv.set((pts[i][0]+pts[i+1][0])//2, (pts[i][1]+pts[i+1][1])//2, (255,230,120,255))
    olhos(cv, 23, 25, 13, (255,230,60,255), (120,0,0,255), 0)
    cv.set(24, 16, (255,255,255,255))                                 # presa


def ent(cv):
    base(cv, (120, 200, 120, 255))
    cv.rect(17, 24, 23, 36, BROWN); cv.rect(17, 24, 18, 36, _mix(BROWN,(0,0,0),0.3))
    g = (70, 170, 90, 255)
    cv.disc(20, 16, 3, g)                                             # raiz da árvore (copa binária)
    for (x, y) in [(13,10),(27,10),(10,6),(16,6),(24,6),(30,6)]:
        cv.disc(x, y, 2.5, g)
    cv.set(13,13,(180,255,180,255)); cv.set(27,13,(180,255,180,255))
    olhos(cv, 18, 22, 28, (250,240,120,255), (60,40,0,255), 1)
    cv.rect(19, 31, 21, 31, (60,40,0,255))


def eco(cv):
    base(cv, (74, 163, 255, 255))
    for r, a in [(11,70),(8,110),(5,160)]:
        cv.ring(20, 20, r, _alpha((90,170,255,255), a))
    cv.disc(20, 20, 3, _alpha((200,230,255,255), 200))
    olhos(cv, 18, 22, 20, (240,250,255,255), (40,90,160,255), 0)


def hidra(cv):
    base(cv, (63, 190, 107, 255))
    g, gs = (70, 185, 110, 255), (45, 130, 75, 255)
    cv.disc(20, 30, 7, g); cv.rect(13, 30, 27, 35, g)
    for bx, by in [(11,16),(20,12),(29,16)]:                          # 3 pescoços/cabeças
        cv.rect(bx-1, by, bx+1, 30, gs)
        cv.disc(bx, by, 3, g)
        cv.set(bx-1, by, (255,70,70,255)); cv.set(bx+1, by, (255,70,70,255))
        cv.set(bx, by+2, (120,0,0,255))


def espiral(cv):
    base(cv, (150, 90, 230, 255))
    for i in range(0, 720, 18):
        ang = math.radians(i); r = i/720*13
        cv.set(round(20+r*math.cos(ang)), round(20+r*math.sin(ang)), _alpha((170,120,250,255),200))
    cv.disc(20, 20, 2, (240,230,255,255)); cv.set(20, 20, (90,30,140,255))  # olho central


def colosso(cv):
    base(cv, (120, 150, 180, 255))
    r, rs = (110, 130, 160, 255), (78, 94, 120, 255)
    cv.rect(10, 18, 30, 36, r); cv.rect(10, 18, 13, 36, rs)
    cv.rect(14, 8, 26, 18, r)                                         # cabeça/pico
    cv.rect(14, 8, 26, 11, (235,240,250,255))                        # neve
    cv.rect(4, 20, 10, 32, r); cv.rect(30, 20, 36, 32, r)            # braços
    olhos(cv, 18, 22, 14, (255,210,63,255), (120,80,0,255), 1)
    cv.rect(15, 22, 25, 32, rs)


def roteador(cv):
    base(cv, (80, 90, 110, 255))
    b, bs = (50, 54, 70, 255), (32, 34, 48, 255)
    cv.rect(10, 20, 30, 32, b); cv.rect(10, 20, 30, 21, _mix(b,(255,255,255),0.2))
    cv.rect(13, 12, 14, 20, bs); cv.rect(20, 10, 21, 20, bs); cv.rect(27, 12, 28, 20, bs)  # antenas
    for x in (13,20,27): cv.disc(x+0.5, 10, 1.2, (120,255,140,255))
    olhos(cv, 16, 24, 26, (255,80,80,255), (120,0,0,255), 1)
    cv.rect(12, 23, 13, 24, (120,255,140,255)); cv.rect(26, 23, 27, 24, (255,210,60,255))  # LEDs
    for r in (6, 9):
        cv.ring(20, 16, r, _alpha((120,255,160,255), 90))            # ondas de sinal


def pacote(cv):
    base(cv, (160, 120, 80, 255))
    b, bs = (150, 110, 70, 255), (110, 80, 50, 255)
    cv.rect(11, 14, 29, 32, b); cv.rect(11, 14, 13, 32, bs)
    cv.rect(11, 22, 29, 23, bs); cv.rect(19, 14, 21, 32, bs)         # fita do pacote
    cv.rect(24, 16, 27, 18, (255,70,70,255)); cv.rect(13, 27, 16, 28, (90,220,255,255))  # glitches
    olhos(cv, 16, 24, 20, (255,230,120,255), (90,40,0,255), 1)
    cv.rect(17, 26, 23, 26, OUTLINE); cv.set(19, 25, OUTLINE); cv.set(21, 27, OUTLINE)


def ddos(cv):
    base(cv, (255, 70, 80, 255))
    cv.disc(20, 20, 5, (255,120,80,255)); cv.disc(20, 20, 2.5, (255,230,150,255))  # núcleo
    for ang in range(0, 360, 40):                                    # enxame de pacotes
        x = 20+round(12*math.cos(math.radians(ang))); y = 20+round(12*math.sin(math.radians(ang)))
        cv.rect(x-1, y-1, x+1, y+1, (230,50,60,255)); cv.set(x, y, (255,180,180,255))


def segfault(cv):
    base(cv, (130, 70, 200, 255))
    c, cs = (40, 36, 56, 255), (26, 22, 38, 255)
    cv.disc(20, 14, 7, c); cv.rect(11, 14, 29, 37, c); cv.rect(11, 14, 14, 37, cs)
    cv.disc(20, 16, 5, (5, 5, 10, 255))                              # rosto vazio /dev/null
    olhos(cv, 18, 22, 16, (170,110,255,255), (170,110,255,255), 0)
    for x in (13, 18, 24, 28):                                       # manto rasgado/glitch
        cv.rect(x, 33, x+1, 37, cs)
    cv.rect(24, 22, 26, 23, (120,80,200,255))                       # glitch


def ia_ancestral(cv):
    base(cv, (124, 92, 255, 255))
    cv.disc(20, 20, 11, _alpha((124,92,255,255), 60))
    cv.disc(20, 20, 8, (40, 30, 80, 255))
    cv.disc(20, 20, 6, (90, 220, 255, 255))                          # olho que tudo sabe
    cv.disc(20, 20, 3, (20, 30, 60, 255)); cv.disc(20, 20, 1.4, (200,240,255,255))
    for ang in range(0, 360, 30):                                    # circuitos
        x = 20+round(10*math.cos(math.radians(ang))); y = 20+round(10*math.sin(math.radians(ang)))
        cv.set(x, y, (180,140,255,255))
        cv.set(20+round(13*math.cos(math.radians(ang))), 20+round(13*math.sin(math.radians(ang))), _alpha((124,92,255,255),150))


def anciao(cv):
    base(cv, (156, 136, 255, 255))
    r, rs = (110, 80, 170, 255), (78, 54, 124, 255)
    cv.rect(12, 18, 28, 37, r); cv.rect(12, 18, 14, 37, rs)
    pele, pele_sh = (226,175,130,255), (185,136,96,255)
    cv.rect(15, 8, 25, 18, pele); cv.rect(23, 8, 25, 18, pele_sh)
    cv.rect(13, 6, 27, 10, (220,220,230,255))                        # cabelo grisalho
    cv.rect(14, 16, 26, 24, (220,220,230,255))                       # barba
    cv.rect(17, 13, 18, 13, OUTLINE); cv.rect(22, 13, 23, 13, OUTLINE)
    cv.rect(29, 10, 30, 36, BROWN)                                   # cajado
    cv.disc(29.5, 8, 2.5, _alpha((180,120,255,255),160)); cv.disc(29.5, 8, 1.4, (200,150,255,255))


CRIATURAS = {
    "inimigo-slime": slime, "inimigo-bug": bug, "inimigo-gargula": gargula,
    "inimigo-espectro": espectro, "inimigo-sentinela": sentinela, "inimigo-kraken": kraken,
    "inimigo-golem": golem, "inimigo-espiao": espiao, "inimigo-quimera": quimera,
    "inimigo-fantasma": fantasma, "inimigo-godclass": godclass, "inimigo-pilha": pilha,
    "inimigo-serpente": serpente, "inimigo-ent": ent, "inimigo-eco": eco,
    "inimigo-hidra": hidra, "inimigo-espiral": espiral, "inimigo-colosso": colosso,
    "inimigo-roteador": roteador, "inimigo-pacote": pacote, "inimigo-ddos": ddos,
    "inimigo-segfault": segfault, "inimigo-ia-ancestral": ia_ancestral, "npc-anciao": anciao,
}


def gerar():
    for nome, fn in CRIATURAS.items():
        cv = Canvas(G, G); fn(cv)
        cv.save(os.path.join(RAIZ, "public", "img", "inimigos", f"{nome}.png"))
        print(f"  ok  inimigos/{nome}.png")


if __name__ == "__main__":
    print("Gerando bestiario pixel art...")
    gerar()
    print(f"Concluido: {len(CRIATURAS)} criaturas.")
