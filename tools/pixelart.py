#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Gerador de PIXEL ART de CORPO INTEIRO (PNG) dos personagens do Algorithmia.
Sem API, sem custo — composição por partes numa grade lógica 32x44, ampliada
com vizinho-mais-próximo para sprites nítidos. Fundo transparente + sombra no chão.

Saída: public/img/<categoria>/<nome>.png
"""

import os
from PIL import Image

W, H = 32, 44      # grade lógica (corpo inteiro: mais alto que largo)
SCALE = 8          # 32x44 -> 256x352
RAIZ = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))


class Canvas:
    def __init__(self, w=W, h=H):
        self.w, self.h = w, h
        self.img = Image.new("RGBA", (w, h), (0, 0, 0, 0))
        self.px = self.img.load()

    def set(self, x, y, c):
        if c is not None and 0 <= x < self.w and 0 <= y < self.h:
            if len(c) == 4 and c[3] < 255:  # alpha blend
                bx, by, bz, ba = self.px[x, y]
                a = c[3] / 255
                self.px[x, y] = (round(c[0]*a+bx*(1-a)), round(c[1]*a+by*(1-a)),
                                 round(c[2]*a+bz*(1-a)), max(ba, c[3]))
            else:
                self.px[x, y] = c

    def rect(self, x0, y0, x1, y1, c):
        for y in range(int(y0), int(y1)+1):
            for x in range(int(x0), int(x1)+1):
                self.set(x, y, c)

    def disc(self, cx, cy, r, c):
        for y in range(self.h):
            for x in range(self.w):
                if (x-cx)**2 + (y-cy)**2 <= r*r:
                    self.set(x, y, c)

    def ellipse(self, cx, cy, rx, ry, c):
        for y in range(self.h):
            for x in range(self.w):
                if ((x-cx)/rx)**2 + ((y-cy)/ry)**2 <= 1:
                    self.set(x, y, c)

    def ring(self, cx, cy, r, c):
        for y in range(self.h):
            for x in range(self.w):
                d = (x-cx)**2 + (y-cy)**2
                if (r-0.8)**2 <= d <= (r+0.2)**2:
                    self.set(x, y, c)

    def save(self, caminho):
        os.makedirs(os.path.dirname(caminho), exist_ok=True)
        self.img.resize((self.w*SCALE, self.h*SCALE), Image.NEAREST).save(caminho)


OUTLINE = (28, 24, 40, 255)
WHITE = (245, 247, 251, 255)
BROWN = (120, 84, 50, 255)
SKIN = {
    "claro": ((240, 200, 158, 255), (205, 158, 118, 255)),
    "medio": ((226, 175, 130, 255), (185, 136, 96, 255)),
    "moreno": ((196, 142, 100, 255), (158, 108, 76, 255)),
}


def _mix(c, alvo, t):
    return tuple(round(c[i] + (alvo[i]-c[i])*t) for i in range(3)) + (255,)


def _alpha(c, a):
    return (c[0], c[1], c[2], a)


def desenhar(cv: Canvas, p: dict):
    pele, pele_sh = SKIN[p.get("pele", "medio")]
    aura = p["aura"]
    roupa, roupa_sh = p["roupa"], p["roupa_sh"]
    out = OUTLINE

    # --- sombra no chão ---
    cv.ellipse(16, 42, 8, 1.6, (0, 0, 0, 90))
    # --- glow de aura sutil atrás (mantém fundo quase transparente) ---
    cv.disc(16, 11, 9, _alpha(aura, 38))
    cv.disc(16, 24, 7, _alpha(aura, 26))

    item = p.get("item")

    # ============ ITENS atrás do corpo (cajado, arco) ============
    if item == "cajado":
        oc = p.get("item_col", aura)
        cv.rect(23, 9, 24, 33, BROWN)                     # haste
        cv.rect(23, 9, 23, 33, _mix(BROWN, (0, 0, 0), 0.3))
        cv.disc(23.5, 7, 2.6, _alpha(oc, 120))            # glow orbe
        cv.disc(23.5, 7, 1.7, oc)
        cv.set(23, 6, WHITE)
    elif item == "arco":
        ac = (150, 110, 60, 255)
        for y in range(10, 33):                            # arco curvo
            import math
            dx = round(3.2 * math.sin((y-10)/22 * math.pi))
            cv.set(8 - dx, y, ac)
        cv.rect(8, 10, 8, 32, None)
        # corda
        for y in range(10, 33):
            cv.set(8, y, _alpha((230, 230, 210, 255), 160))

    # ============ CABEÇA ============
    hx0, hx1, hy0, hy1 = 12, 19, 4, 12
    # cabelo atrás (longo) / capuz
    if p.get("cabelo") == "longo":
        h = p["cabelo_col"]; hsh = _mix(h, (0, 0, 0), 0.35)
        cv.rect(11, 6, 20, 16, hsh)
    if p.get("acc") == "capuz":
        ac = p["acc_col"]; acsh = _mix(ac, (0, 0, 0), 0.3)
        cv.rect(10, 4, 21, 17, acsh)
        cv.rect(11, 3, 20, 7, ac)
    cv.rect(hx0, hy0, hx1, hy1, pele)
    cv.rect(18, hy0, 19, hy1, pele_sh)                    # sombra direita
    cv.rect(12, hy0, 12, hy1, _mix(pele_sh, (126, 166, 255), 0.4))  # luz azul esq.
    cv.rect(14, 12, 17, 13, pele_sh)                      # pescoço

    # cabelo frente
    cab = p.get("cabelo")
    if cab in ("longo", "curto"):
        h = p["cabelo_col"]
        cv.rect(12, 3, 19, 5, h); cv.rect(11, 4, 12, 9, h); cv.rect(19, 4, 20, 9, h)
        cv.rect(13, 3, 18, 3, h); cv.set(14, 4, _mix(h, (255,255,255), 0.2))
    elif cab == "careca":
        cv.rect(13, 3, 18, 4, _mix(pele, (255,255,255), 0.18))

    # chapéu pontudo (mago)
    if p.get("acc") == "chapeu":
        ac = p["acc_col"]; acsh = _mix(ac, (0,0,0), 0.3)
        cv.rect(10, 4, 21, 5, ac); cv.rect(13, 0, 18, 3, ac); cv.rect(15, -2, 16, 0, ac)
        cv.rect(10, 4, 13, 5, acsh); cv.set(16, 0, _mix(ac,(255,255,255),0.4))
        cv.rect(10, 5, 21, 5, _mix(ac,(255,255,255),0.2))
    # elmo (guerreiro)
    if p.get("acc") == "elmo":
        ac = p["acc_col"]; acsh = _mix(ac, (0,0,0), 0.3)
        cv.rect(11, 2, 20, 6, ac); cv.rect(11, 2, 13, 6, acsh)
        cv.rect(15, 0, 16, 6, _mix(ac,(255,255,255),0.4))  # crista
        cv.rect(11, 6, 20, 6, acsh)
    # headphones
    if p.get("acc") == "headphones":
        hp = p["acc_col"]
        cv.rect(11, 2, 20, 3, out)
        cv.rect(9, 6, 11, 10, out); cv.rect(10, 7, 10, 9, hp)
        cv.rect(20, 6, 22, 10, out); cv.rect(21, 7, 21, 9, hp)

    # olhos
    oy = 8
    cv.set(13, oy, WHITE); cv.set(14, oy, out)
    cv.set(17, oy, out); cv.set(18, oy, WHITE)
    if p.get("olhos") == "fechado":
        cv.set(13, oy, out); cv.set(14, oy, out); cv.set(17, oy, out); cv.set(18, oy, out)

    # óculos
    g = p.get("oculos")
    if g == "redondo":
        gc = p.get("oculos_col", out)
        for cxg in (13.5, 17.5):
            for ang in range(0, 360, 45):
                import math
                cv.set(round(cxg+1.6*math.cos(math.radians(ang))),
                       round(8+1.6*math.sin(math.radians(ang))), gc)
        cv.set(16, 8, gc)

    # barba
    if p.get("barba"):
        bc = p.get("barba_col", p.get("cabelo_col", (60,50,70,255)))
        cv.rect(12, 9, 19, 12, bc); cv.rect(13, 12, 18, 13, bc)
        cv.rect(14, 10, 17, 10, pele)
    # boca
    m = p.get("boca", "calmo")
    if not p.get("barba"):
        if m == "sorriso":
            cv.rect(14, 10, 17, 10, (150,70,60,255)); cv.set(13,9,(150,70,60,255)); cv.set(18,9,(150,70,60,255))
        elif m == "grin":
            cv.rect(13, 10, 18, 11, (120,50,45,255)); cv.rect(14,10,17,10,WHITE)
        else:
            cv.rect(14, 10, 16, 10, (150,90,80,255))

    # ============ TORSO / OMBROS ============
    cv.rect(10, 13, 21, 16, roupa)                        # ombros
    cv.rect(11, 16, 20, 27, roupa)                        # tronco
    cv.rect(11, 16, 12, 27, roupa_sh); cv.rect(19, 16, 20, 27, roupa_sh)
    if p.get("gola"):
        cv.rect(15, 13, 16, 18, p["gola"])
        cv.set(15, 15, out)
    if p.get("peito"):                                   # emblema/símbolo no peito
        sx, sy, sc = p["peito"]; cv.rect(sx, sy, sx+1, sy+1, sc)

    # ============ BRAÇOS ============
    cv.rect(8, 15, 10, 24, roupa); cv.rect(8, 15, 8, 24, roupa_sh)
    cv.rect(21, 15, 23, 24, roupa); cv.rect(23, 15, 23, 24, roupa_sh)
    cv.rect(8, 24, 10, 26, pele)                          # mão esq.
    cv.rect(21, 24, 23, 26, pele)                         # mão dir.

    # ============ CORPO BAIXO ============
    if p.get("corpo") == "tunica":                       # túnica + pernas
        cv.rect(11, 27, 20, 30, roupa)
        pant = p.get("calca", (60, 64, 80, 255)); pant_sh = _mix(pant,(0,0,0),0.3)
        cv.rect(12, 30, 15, 39, pant); cv.rect(16, 30, 19, 39, pant)
        cv.rect(12, 30, 12, 39, pant_sh); cv.rect(16, 30, 16, 39, pant_sh)
        boot = p.get("bota", (50, 40, 34, 255))
        cv.rect(11, 39, 15, 41, boot); cv.rect(16, 39, 20, 41, boot)
    else:                                                # robe longo até os pés
        cv.rect(10, 27, 21, 39, roupa)
        cv.rect(10, 27, 11, 39, roupa_sh); cv.rect(20, 27, 21, 39, roupa_sh)
        cv.rect(16, 28, 16, 38, roupa_sh)                # vinco central
        cv.rect(10, 38, 21, 39, _mix(roupa,(0,0,0),0.25))
        cv.rect(13, 39, 15, 41, pele_sh); cv.rect(17, 39, 19, 41, pele_sh)  # pés

    # ============ ITENS na frente ============
    if item == "espada_escudo":
        # escudo no braço esq.
        sc = p.get("escudo_col", (70, 120, 200, 255))
        cv.rect(5, 18, 9, 27, sc); cv.rect(5, 18, 5, 27, _mix(sc,(0,0,0),0.3))
        cv.set(7, 22, (255, 210, 63, 255))
        # espada na mão dir.
        cv.rect(22, 8, 23, 25, (210, 220, 235, 255)); cv.set(22, 8, WHITE)
        cv.rect(20, 24, 25, 24, (255, 210, 63, 255))     # guarda
    elif item == "orbe_mao":
        oc = p.get("item_col", aura)
        cv.disc(22, 23, 2.4, _alpha(oc, 130)); cv.disc(22, 23, 1.5, oc); cv.set(21, 22, WHITE)
    elif item == "carta":
        cv.rect(20, 23, 25, 27, WHITE); cv.rect(20, 23, 25, 23, (200,205,215,255))
        cv.rect(20, 23, 22, 25, None); cv.set(22, 25, (28,120,40,255))


# ================= PERSONAGENS (corpo inteiro) =================
MESTRES = {
    "mestre-willen": dict(aura=(91,140,255,255), pele="medio", corpo="robe",
        cabelo="longo", cabelo_col=(54,49,78,255), barba=True, barba_col=(51,46,71,255),
        oculos="redondo", oculos_col=(255,210,63,255),
        roupa=(55,86,168,255), roupa_sh=(34,51,110,255), gola=(207,224,255,255),
        item="cajado", item_col=(91,140,255,255), peito=(15,30,(157,180,255,255)), boca="calmo"),
    "mestre-clayton": dict(aura=(34,166,179,255), pele="medio", corpo="tunica",
        cabelo="curto", cabelo_col=(58,42,28,255), acc="capuz", acc_col=(47,127,153,255),
        roupa=(47,127,153,255), roupa_sh=(29,79,102,255), calca=(40,70,84,255),
        item="orbe_mao", item_col=(120,240,255,255), boca="sorriso"),
    "mestre-marcelo": dict(aura=(225,177,44,255), pele="medio", corpo="tunica",
        cabelo="curto", cabelo_col=(44,32,24,255), barba=True, barba_col=(58,44,30,255),
        roupa=(74,111,160,255), roupa_sh=(46,74,114,255), calca=(46,74,114,255),
        bota=(60,46,34,255), boca="sorriso"),
    "mestre-cesar": dict(aura=(156,136,255,255), pele="medio", corpo="robe",
        cabelo="careca", acc="headphones", acc_col=(124,92,255,255), olhos="fechado",
        roupa=(216,219,230,255), roupa_sh=(180,184,198,255), boca="calmo"),
    "mestre-cassandro": dict(aura=(68,189,50,255), pele="medio", corpo="tunica",
        cabelo="curto", cabelo_col=(36,26,20,255), oculos="redondo", oculos_col=(214,224,240,255),
        roupa=(245,247,251,255), roupa_sh=(210,215,226,255), calca=(120,140,160,255),
        item="carta", boca="grin"),
}

HEROIS = {
    "heroi-mago": dict(aura=(124,92,255,255), pele="claro", corpo="robe",
        cabelo="curto", cabelo_col=(70,55,40,255), acc="chapeu", acc_col=(124,92,255,255),
        roupa=(98,70,200,255), roupa_sh=(66,46,150,255), gola=(200,180,255,255),
        item="cajado", item_col=(180,150,255,255), boca="calmo"),
    "heroi-guerreiro": dict(aura=(255,122,89,255), pele="claro", corpo="tunica",
        cabelo="curto", cabelo_col=(80,55,35,255), acc="elmo", acc_col=(255,138,80,255),
        roupa=(196,96,54,255), roupa_sh=(150,70,40,255), calca=(110,70,45,255),
        bota=(70,48,34,255), item="espada_escudo", escudo_col=(74,140,210,255), boca="calmo"),
    "heroi-ranger": dict(aura=(46,204,113,255), pele="medio", corpo="tunica",
        cabelo="curto", cabelo_col=(60,45,30,255), acc="capuz", acc_col=(39,110,70,255),
        roupa=(46,150,90,255), roupa_sh=(29,100,60,255), calca=(50,60,45,255),
        bota=(55,45,34,255), item="arco", boca="calmo"),
}


def gerar(grupo, categoria):
    for nome, p in grupo.items():
        cv = Canvas(); desenhar(cv, p)
        cv.save(os.path.join(RAIZ, "public", "img", categoria, f"{nome}.png"))
        print(f"  ok  {categoria}/{nome}.png")


if __name__ == "__main__":
    print("Gerando pixel art de corpo inteiro...")
    gerar(MESTRES, "mestres")
    gerar(HEROIS, "herois")
    print("Concluido.")
