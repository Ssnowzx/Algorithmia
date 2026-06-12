#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Pixel art dos 19 itens do Algorithmia (ícones 32x32, brilho por raridade)."""
import os, math
from pixelart import Canvas, _mix, _alpha, OUTLINE, WHITE, BROWN

RAIZ = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
G = 32
RARIDADE = {  # cor do brilho de fundo
    "comum": (150, 160, 180, 255), "raro": (74, 163, 255, 255),
    "epico": (160, 90, 230, 255), "lendario": (255, 200, 60, 255),
}
PRATA = (210, 218, 232, 255); PRATA_SH = (150, 160, 178, 255)
OURO = (255, 210, 70, 255); OURO_SH = (200, 150, 30, 255)


def glow(cv, rar):
    c = RARIDADE[rar]
    cv.disc(16, 16, 12, _alpha(c, 28)); cv.disc(16, 16, 8, _alpha(c, 30))


def lamina_vert(cv, x, y0, y1, cor, cor_sh):
    cv.rect(x-1, y0+1, x+1, y1, cor); cv.rect(x-1, y0+1, x-1, y1, cor_sh)
    cv.rect(x, y0, x, y0, WHITE)            # ponta
    cv.set(x+1, y0+2, _mix(cor, (255,255,255), 0.5))


def punho(cv, x, gy, guarda, hilt=BROWN):
    cv.rect(x-3, gy, x+3, gy+1, guarda)    # guarda
    cv.rect(x-1, gy+2, x+1, gy+6, hilt)    # cabo
    cv.disc(x, gy+7, 1.5, guarda)          # pomo


def adaga(cv):
    glow(cv, "comum"); lamina_vert(cv, 16, 7, 17, PRATA, PRATA_SH)
    punho(cv, 16, 17, (120,90,60,255))


def espada(cv):
    glow(cv, "raro"); lamina_vert(cv, 16, 4, 19, PRATA, PRATA_SH)
    punho(cv, 16, 19, OURO)


def cajado(cv):
    glow(cv, "raro"); cv.rect(15, 10, 16, 28, BROWN); cv.rect(15, 10, 15, 28, _mix(BROWN,(0,0,0),0.3))
    cv.disc(15.5, 8, 3.2, _alpha((160,90,230,255),140)); cv.disc(15.5, 8, 2, (170,110,240,255)); cv.set(14,7,WHITE)


def machado(cv):
    glow(cv, "epico"); cv.rect(15, 6, 16, 28, BROWN)
    cv.rect(9, 7, 15, 14, PRATA); cv.rect(17, 7, 23, 14, PRATA)        # duas lâminas
    cv.rect(9, 7, 9, 14, PRATA_SH); cv.rect(23, 7, 23, 14, PRATA_SH)
    cv.set(11, 9, WHITE); cv.set(21, 9, WHITE)


def lamina(cv):
    glow(cv, "epico")
    cores = [(255,90,120,255),(255,200,70,255),(120,230,140,255),(90,170,255,255),(180,110,240,255)]
    for i, y in enumerate(range(5, 19)):
        c = cores[i % len(cores)]; cv.rect(15, y, 17, y, c)
    cv.set(16, 4, WHITE); punho(cv, 16, 19, OURO)


def escudo_base(cv, cor, cor_sh):
    cv.rect(10, 7, 22, 20, cor); cv.rect(10, 7, 11, 20, cor_sh)
    cv.rect(12, 20, 20, 23, cor); cv.rect(14, 23, 18, 26, cor)        # ponta inferior
    cv.rect(10, 7, 22, 8, _mix(cor,(255,255,255),0.25))


def escudo(cv):
    glow(cv, "comum"); escudo_base(cv, (70,120,200,255), (45,80,150,255))
    cv.rect(13, 11, 19, 12, WHITE); cv.rect(13, 15, 19, 16, WHITE)    # try/catch linhas


def broquel(cv):
    glow(cv, "raro"); cv.disc(16, 15, 8, (60,170,100,255)); cv.disc(16, 15, 8, None)
    cv.disc(16, 15, 8, (60,170,100,255)); cv.ring(16, 15, 7, (40,120,70,255))
    cv.disc(16, 15, 2.5, (180,255,200,255))
    for ang in range(0, 360, 60): cv.set(16+round(5*math.cos(math.radians(ang))), 15+round(5*math.sin(math.radians(ang))), WHITE)


def egide(cv):
    glow(cv, "epico"); escudo_base(cv, (200,60,60,255), (150,35,35,255))
    for x in (12, 16, 20):                                            # chamas no topo
        cv.rect(x, 4, x, 7, (255,140,40,255)); cv.set(x, 3, (255,210,80,255))


def anel(cv):
    glow(cv, "comum"); cv.ring(16, 18, 6, OURO); cv.ring(16, 18, 6, OURO)
    cv.disc(16, 11, 2.5, (90,170,255,255)); cv.set(15, 10, WHITE)


def amuleto(cv):
    glow(cv, "raro"); cv.ring(16, 8, 3, (180,180,190,255))           # corrente
    cv.disc(16, 18, 6, (90,170,255,255)); cv.disc(16, 18, 6, None); cv.disc(16, 18, 6, (90,170,255,255))
    cv.ring(16, 18, 2.5, (20,40,80,255))                             # letra O
    cv.set(13, 15, WHITE)


def elmo(cv):
    glow(cv, "raro"); cv.rect(10, 10, 22, 18, PRATA); cv.rect(10, 10, 12, 18, PRATA_SH)
    cv.rect(10, 8, 22, 10, PRATA); cv.rect(14, 13, 18, 17, (40,50,70,255))  # visor
    cv.rect(15, 5, 17, 8, (90,170,255,255)); cv.set(14, 9, WHITE)


def frasco(cv, liq, liq_sh, tampa=(120,90,60,255), cruz=False, dourado=False):
    if dourado: cv.disc(16, 18, 9, _alpha((255,210,70,255),50))
    cv.rect(14, 6, 18, 8, tampa)                                     # rolha
    cv.rect(13, 8, 19, 11, _alpha(WHITE, 120))                       # gargalo vidro
    cv.rect(11, 12, 21, 26, liq); cv.rect(11, 12, 12, 26, liq_sh)
    cv.rect(11, 11, 21, 11, _alpha(WHITE, 120))
    cv.disc(14, 16, 1.5, _alpha(WHITE, 160))                         # brilho
    if cruz:
        cv.rect(15, 17, 17, 22, WHITE); cv.rect(13, 19, 19, 20, WHITE)


def pocao_hp(cv): glow(cv, "comum"); frasco(cv, (230,70,90,255), (170,40,60,255))
def pocao_hp2(cv): glow(cv, "raro"); frasco(cv, (235,50,70,255), (175,30,50,255), cruz=True)
def pocao_hp3(cv): glow(cv, "epico"); frasco(cv, (255,60,80,255), (190,30,55,255), cruz=True, dourado=True)
def pocao_mp(cv): glow(cv, "comum"); frasco(cv, (74,163,255,255), (40,110,200,255))


def bota(cv):
    glow(cv, "raro"); c, cs = (120,80,50,255), (90,58,36,255)
    cv.rect(12, 8, 17, 22, c); cv.rect(12, 22, 24, 26, c)            # cano + pé
    cv.rect(12, 8, 13, 22, cs); cv.rect(12, 25, 24, 26, cs)
    cv.ring(16, 14, 2, (255,210,70,255))                            # runa de loop
    for x in (19, 22): cv.set(x, 21, _alpha(WHITE,120))             # rastro


def espada_lendaria(cv):
    glow(cv, "lendario"); lamina_vert(cv, 16, 3, 19, OURO, OURO_SH)
    cv.set(16, 2, WHITE); cv.rect(14, 8, 18, 8, _mix(OURO,(255,255,255),0.5))
    cv.rect(11, 19, 21, 20, OURO); cv.disc(11, 19, 1.5, (90,200,255,255)); cv.disc(21, 19, 1.5, (90,200,255,255))
    cv.rect(15, 21, 17, 26, (140,90,40,255)); cv.disc(16, 27, 2, OURO)
    for (sx, sy) in [(9,6),(24,10),(8,16)]: cv.set(sx, sy, WHITE)    # partículas


def fragmento_ia(cv):
    glow(cv, "lendario")
    # cristal losango roxo-azulado
    for dy in range(-7, 8):
        w = 6 - abs(dy)
        if w > 0: cv.rect(16-w, 16+dy, 16+w, 16+dy, _mix((110,90,220,255),(90,170,255,255),(dy+7)/14))
    cv.disc(16, 16, 2.5, (20,40,70,255)); cv.disc(16, 16, 1.4, (90,230,255,255))  # olho-circuito
    cv.set(16, 15, WHITE)
    for ang in range(0, 360, 90): cv.set(16+round(7*math.cos(math.radians(ang))), 16+round(7*math.sin(math.radians(ang))), (150,220,255,255))


def generico(cv):
    glow(cv, "comum"); m, ms = (140,95,55,255), (100,66,38,255)
    cv.rect(8, 16, 24, 26, m); cv.rect(8, 16, 9, 26, ms)            # base do baú
    cv.rect(8, 10, 24, 16, m); cv.rect(8, 14, 24, 15, ms)          # tampa curva
    cv.rect(8, 10, 24, 11, _mix(m,(255,255,255),0.2))
    cv.rect(14, 18, 18, 22, OURO); cv.set(16, 20, OUTLINE)         # fecho dourado
    cv.rect(8, 19, 24, 20, ms)


ITENS = {
    "item-adaga": adaga, "item-espada": espada, "item-cajado": cajado, "item-machado": machado,
    "item-lamina": lamina, "item-escudo": escudo, "item-broquel": broquel, "item-egide": egide,
    "item-anel": anel, "item-amuleto": amuleto, "item-elmo": elmo,
    "item-pocao-hp": pocao_hp, "item-pocao-hp2": pocao_hp2, "item-pocao-hp3": pocao_hp3,
    "item-pocao-mp": pocao_mp, "item-bota": bota, "item-espada-lendaria": espada_lendaria,
    "item-fragmento-ia": fragmento_ia, "item-generico": generico,
}


def gerar():
    for nome, fn in ITENS.items():
        cv = Canvas(G, G); fn(cv)
        cv.save(os.path.join(RAIZ, "public", "img", "itens", f"{nome}.png"))
        print(f"  ok  itens/{nome}.png")


if __name__ == "__main__":
    print("Gerando itens pixel art...")
    gerar()
    print(f"Concluido: {len(ITENS)} itens.")
