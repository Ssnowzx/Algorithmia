#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Pixel art dos 8 cenários de fundo do Algorithmia (16:9, grade 64x36)."""
import os, math, random
from pixelart import Canvas, _mix, _alpha, OUTLINE, WHITE, BROWN

RAIZ = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
GW, GH = 64, 36


def vgrad(cv, y0, y1, ctop, cbot):
    """Gradiente vertical preenchendo linhas inteiras."""
    n = max(1, y1 - y0)
    for y in range(y0, y1 + 1):
        t = (y - y0) / n
        cv.rect(0, y, GW - 1, y, _mix(ctop, cbot, t))


def estrelas(cv, y0, y1, n, seed, cor=WHITE):
    r = random.Random(seed)
    for _ in range(n):
        x = r.randint(0, GW - 1); y = r.randint(y0, y1)
        cv.set(x, y, _alpha(cor, r.choice([120, 180, 255])))


def colina(cv, y, cor):
    cv.rect(0, y, GW - 1, GH - 1, cor)


# ---------------- cenários ----------------
def vila(cv):
    vgrad(cv, 0, 23, (255, 190, 140, 255), (150, 190, 255, 255))   # amanhecer
    # Sol centralizado
    cv.disc(32, 20, 6, (255, 230, 150, 255))
    cv.disc(32, 20, 8, _alpha((255, 230, 150, 255), 80))
    colina(cv, 23, (70, 150, 90, 255)); cv.rect(0, 23, GW-1, 24, (90, 175, 110, 255))
    
    # Caminho centralizado
    cv.rect(28, 24, 35, GH-1, (180, 150, 110, 255))
    cv.rect(29, 25, 34, GH-1, (160, 130, 90, 255))
    
    # Casinhas simetricamente próximas ao centro
    r = random.Random(1)
    for bx in (10, 20, 38, 48):                                      
        h = r.randint(5, 7); w = 7
        cv.rect(bx, 23-h, bx+w, 23, (180, 150, 110, 255))
        cv.rect(bx, 23-h, bx+1, 23, (140, 112, 80, 255))
        cv.rect(bx-1, 23-h-3, bx+w+1, 23-h, (150, 70, 60, 255))     # telhado
        cv.set(bx+w, 23-h-3, (150,70,60,255))
        cv.rect(bx+2, 23-h+2, bx+4, 23-h+4, (255, 220, 120, 255))   # janela
        for s in range(3):                                          # fumaça
            cv.set(bx+5, 23-h-4-s*2, _alpha(WHITE, 120-s*30))
    # Placas
    cv.rect(26, 21, 27, 24, BROWN); cv.rect(25, 19, 28, 21, (200,180,120,255))  # placa
    cv.set(26, 20, OUTLINE)


def porto(cv):
    vgrad(cv, 0, 18, (60, 70, 140, 255), (90, 140, 210, 255))      # céu crepúsculo
    estrelas(cv, 0, 10, 20, 2, (200, 210, 255, 255))
    vgrad(cv, 18, GH-1, (40, 90, 170, 255), (20, 50, 110, 255))    # mar de dados
    r = random.Random(3)
    for _ in range(40):                                            # cintilação
        cv.set(r.randint(0, GW-1), r.randint(19, GH-1), _alpha((150, 200, 255, 255), 120))
    for px in (18, 40):                                            # estruturas </> simétricas
        cv.set(px, 14, (130,200,255,255)); cv.set(px-1,15,(130,200,255,255)); cv.set(px,16,(130,200,255,255))
        cv.set(px+6,14,(130,200,255,255)); cv.set(px+7,15,(130,200,255,255)); cv.set(px+6,16,(130,200,255,255))
        cv.rect(px+3,13,px+3,17,(255,210,90,255))
    # doca central
    cv.rect(22, 20, 42, 22, (60, 45, 35, 255))                     
    for dx in range(24, 42, 4): cv.rect(dx, 22, dx, 27, (45, 34, 26, 255))
    # Barco ancorado no centro
    cv.rect(28, 17, 36, 19, (120, 80, 50, 255))
    cv.rect(26, 18, 38, 19, (100, 60, 40, 255))
    cv.rect(32, 12, 32, 17, BROWN) # mastro
    cv.rect(33, 13, 36, 16, (200, 200, 220, 255)) # vela


def cidadela(cv):
    vgrad(cv, 0, GH-1, (140, 200, 230, 255), (90, 170, 200, 255))  # dia
    estrelas(cv, 0, 6, 6, 9, (255,255,255,255))
    r = random.Random(4)
    # Torres laterais
    for bx in (12, 20, 36, 44):                                 
        th = r.randint(12, 20); w = 8; tq = (40, 170, 180, 255); tqs = (26, 120, 130, 255)
        top = GH-1-th
        for i, by in enumerate(range(GH-2, top, -4)):
            cv.rect(bx, by-3, bx+w, by, tq if i % 2 == 0 else tqs)
            cv.rect(bx, by-3, bx+w, by-3, _mix(tq,(255,255,255),0.2))
            cv.rect(bx+2, by-2, bx+3, by-1, (200, 255, 255, 255))  # janelas
            cv.rect(bx+5, by-2, bx+6, by-1, (200, 255, 255, 255))
    # Torre principal ao centro
    bx = 26; w = 12; th = 26; top = GH-1-th; tq = (30, 140, 150, 255); tqs = (20, 100, 110, 255)
    for i, by in enumerate(range(GH-2, top, -4)):
        cv.rect(bx, by-3, bx+w, by, tq if i % 2 == 0 else tqs)
        cv.rect(bx, by-3, bx+w, by-3, _mix(tq,(255,255,255),0.2))
        cv.rect(bx+3, by-2, bx+4, by-1, (255, 255, 200, 255))
        cv.rect(bx+8, by-2, bx+9, by-1, (255, 255, 200, 255))
    # Cúpula da torre central
    cv.rect(bx, top-2, bx+w, top, (200, 220, 255, 255))
    cv.rect(bx+2, top-4, bx+w-2, top-2, (200, 220, 255, 255))
    cv.rect(bx+4, top-6, bx+w-4, top-4, (200, 220, 255, 255))
    cv.rect(bx+5, top-8, bx+w-5, top-6, (200, 220, 255, 255))


def floresta(cv):
    vgrad(cv, 0, 24, (120, 150, 80, 255), (200, 180, 110, 255))    # luz âmbar
    for ry in range(0, 24, 3):                                     # raios
        cv.rect(0, ry, GW-1, ry, _alpha((255, 235, 170, 255), 30))
    colina(cv, 24, (60, 110, 60, 255)); cv.rect(0, 24, GW-1, 25, (80, 140, 75, 255))
    # Caminho central na floresta
    cv.rect(28, 24, 35, GH-1, (130, 140, 90, 255))
    cv.rect(29, 25, 34, GH-1, (110, 120, 80, 255))
    
    for bx in (12, 22, 42, 52):                                     # árvores em perspectiva
        cv.rect(bx, 14, bx+1, 25, BROWN)
        g = (70, 160, 90, 255)
        cv.disc(bx+0.5, 12, 3, g)                                  # copa
        for (dx, dy) in [(-4, 9), (4, 9), (-6, 6), (-2, 6), (2, 6), (6, 6)]:
            cv.disc(bx+0.5+dx, dy, 2.5, g)
        cv.set(bx-3, 11, (180,255,180,255)); cv.set(bx+4, 11, (180,255,180,255))


def montanha(cv):
    vgrad(cv, 0, GH-1, (110, 80, 170, 255), (210, 150, 190, 255))  # entardecer violeta
    estrelas(cv, 0, 12, 14, 6, (255, 240, 255, 255))
    
    for (mx, mh) in [(16, 18), (48, 18), (32, 28)]:                # montanha central mais alta
        for dy in range(mh):
            w = round((mh - dy) * 0.7)
            cv.rect(mx - w, GH-1-dy, mx + w, GH-1-dy, (90, 100, 140, 255))
        for dy in range(6):                                        # neve
            w = round((mh - dy) * 0.7)
            cv.rect(mx - w, GH-1-mh+dy+1, mx + w, GH-1-mh+dy+1, (235, 240, 250, 255))
            
    for cy in (16, 20):                                            # nuvens
        cv.rect(10, cy, 26, cy, _alpha(WHITE, 90)); cv.rect(38, cy+1, 54, cy+1, _alpha(WHITE, 90))
    cv.set(30, 4, (220,200,255,255)); cv.set(31, 5, (220,200,255,255))   # ∞ no topo
    cv.set(34, 4, (220,200,255,255))                                     # símbolo


def torre(cv):
    vgrad(cv, 0, GH-1, (10, 40, 35, 255), (20, 70, 55, 255))       # noite esmeralda
    estrelas(cv, 0, 20, 30, 7, (150, 255, 200, 255))
    bx, w = 27, 10                                                 # torre central
    cv.rect(bx, 4, bx+w, GH-1, (30, 60, 50, 255)); cv.rect(bx, 4, bx+1, GH-1, (18, 40, 34, 255))
    cv.rect(bx+2, 2, bx+w-2, 5, (40, 75, 62, 255))                # topo
    for i, fy in enumerate(range(7, GH-2, 4)):                     # andares
        c = (120, 255, 180, 255) if i % 2 == 0 else (90, 220, 150, 255)
        cv.rect(bx+2, fy, bx+3, fy+1, c); cv.rect(bx+w-3, fy, bx+w-2, fy+1, c)
        cv.rect(bx, fy+2, bx+w, fy+2, (20, 45, 38, 255))
    r = random.Random(8)
    for _ in range(14):                                           # pacotes de luz
        cv.set(bx + r.randint(3, w-3), r.randint(4, GH-2), _alpha((160, 255, 200, 255), 200))
    # Brilho central
    cv.disc(32, 14, 12, _alpha((100, 255, 180, 255), 30))


def abismo(cv):
    vgrad(cv, 0, GH-1, (20, 10, 35, 255), (5, 2, 12, 255))         # vazio sombrio
    cx, cy = 32, 18
    cv.disc(cx, cy, 4, (2, 1, 6, 255))                             # centro /dev/null
    r = random.Random(11)
    for _ in range(120):                                           # espiral
        ang = r.uniform(0, 6.28); dist = r.uniform(4, 28)
        x = cx + dist * math.cos(ang); y = cy + dist * 0.8 * math.sin(ang)
        a = max(40, 220 - int(dist*6))
        cv.set(round(x), round(y), _alpha((150, 90, 230, 255), a))
    for k in range(10):                                            # braços da espiral
        ang = k/10*6.28
        for d in range(4, 24, 2):
            x = cx + d*math.cos(ang + d*0.15); y = cy + d*0.8*math.sin(ang + d*0.15)
            cv.set(round(x), round(y), _alpha((110, 70, 200, 255), 140))


def batalha(cv):
    vgrad(cv, 0, 20, (60, 45, 110, 255), (95, 80, 170, 255))       # plano etéreo
    vgrad(cv, 20, GH-1, (45, 35, 90, 255), (28, 22, 60, 255))      # chão
    
    # Sol central
    cv.disc(32, 20, 8, (255, 150, 100, 255))
    cv.disc(32, 20, 12, _alpha((255, 150, 100, 255), 60))
    
    for x in range(0, GW, 6):                                      # grade em perspectiva
        cv.set(x, 20, _alpha((150,130,230,255), 90))
        for i, y in enumerate(range(21, GH, 3)):
            xx = 32 + (x - 32) * (1 + i*0.5)
            if 0 <= xx < GW:
                cv.set(round(xx), y, _alpha((130, 110, 210, 255), 70))
    for y in range(22, GH, 3):                                     # linhas horizontais
        cv.rect(0, y, GW-1, y, _alpha((120, 100, 200, 255), 50))
    r = random.Random(5)
    for _ in range(30):                                           # partículas
        cv.set(r.randint(0, GW-1), r.randint(0, 19), _alpha((200, 180, 255, 255), 160))
    # Pilar central
    cv.rect(30, 10, 34, 20, _alpha((180, 150, 255, 255), 100))
    cv.rect(31, 12, 33, 20, _alpha((200, 180, 255, 255), 150))


CENARIOS = {
    "fundo-vila": vila, "fundo-porto": porto, "fundo-cidadela": cidadela,
    "fundo-floresta": floresta, "fundo-montanha": montanha, "fundo-torre": torre,
    "fundo-abismo": abismo, "fundo-batalha": batalha,
}


def gerar():
    for nome, fn in CENARIOS.items():
        cv = Canvas(GW, GH); fn(cv)
        cv.save(os.path.join(RAIZ, "public", "img", "fundos", f"{nome}.png"))
        print(f"  ok  fundos/{nome}.png")


if __name__ == "__main__":
    print("Gerando cenarios pixel art centralizados...")
    gerar()
    print(f"Concluido: {len(CENARIOS)} cenarios.")
