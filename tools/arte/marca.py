#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Símbolo e wordmark oficiais do Algorithmia.
Paleta alinhada a :root em public/css/style.css e docs/PROMPT-EVOLUCAO-VISUAL.md
"""
import os
import urllib.request

from PIL import Image, ImageDraw, ImageFont

from pixelart import Canvas, _mix, _alpha, WHITE

RAIZ = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
OUT_UI = os.path.join(RAIZ, "public", "img", "ui")

# Tokens do design system
ROXO = (124, 92, 255, 255)
ROXO_2 = (157, 131, 255, 255)
ROXO_ESC = (72, 52, 168, 255)
ROXO_SOMBRA = (46, 34, 96, 255)
OURO = (255, 206, 71, 255)
OURO_ESC = (196, 148, 28, 255)
CIANO_RUNA = (140, 230, 255, 255)
BRILHO = (245, 247, 251, 255)


def desenhar_simbolo(cv: Canvas, cx: float, cy: float, escala: float = 1.0) -> None:
    """Escudo arcano com anel dourado e runas </> — emblema central do jogo."""

    def s(v: float) -> int:
        return round(v * escala)

    def set_rel(x: float, y: float, cor) -> None:
        cv.set(s(cx + x), s(cy + y), cor)

    def rect_rel(x0, y0, x1, y1, cor) -> None:
        cv.rect(s(cx + x0), s(cy + y0), s(cx + x1), s(cy + y1), cor)

    r = ROXO_2
    rs = ROXO_ESC

    # Escudo clássico (topo reto → base em ponta)
    rect_rel(-8, -10, 8, 3, r)
    rect_rel(-8, -10, -5, 8, rs)
    rect_rel(-6, 4, 6, 8, r)
    rect_rel(-3, 8, 3, 12, r)
    rect_rel(-8, -10, 8, -8, _mix(r, BRILHO, 0.42))

    # Contorno escuro
    for x in range(-8, 9):
        set_rel(x, -10, ROXO_SOMBRA)
    for y in range(-9, 4):
        set_rel(-8, y, ROXO_SOMBRA)
    for y in range(-9, 9):
        set_rel(8, y, ROXO_SOMBRA)
    for (x, y) in [(-6, 8), (-3, 12), (3, 12), (6, 8)]:
        set_rel(x, y, ROXO_SOMBRA)

    # Anel dourado
    cv.ring(s(cx), s(cy - 1), s(11), OURO)

    # Runas </> em branco-ciano (referência visual + design system)
    runa = _mix(CIANO_RUNA, BRILHO, 0.35)
    for (x, y) in [(-5, -2), (-4, -1), (-3, 0), (-4, 1), (-5, 2)]:
        set_rel(x, y, runa)
    for (x, y) in [(-1, -3), (0, -2), (1, -1), (2, 0), (3, 1)]:
        set_rel(x, y, runa)
    for (x, y) in [(5, -2), (4, -1), (3, 0), (4, 1), (5, 2)]:
        set_rel(x, y, runa)

    # Brilhos + aura
    cv.disc(s(cx), s(cy), s(12), _alpha(ROXO, 30))
    set_rel(-4, -8, BRILHO)
    set_rel(5, -7, _alpha(BRILHO, 150))
    set_rel(6, -6, _alpha(CIANO_RUNA, 90))


def gerar_logo(scale: int = 8) -> None:
    """Ícone quadrado 32×32 → PNG para header, HUD, auth."""
    cv = Canvas(32, 32)
    desenhar_simbolo(cv, 16, 16, 1.0)
    caminho = os.path.join(OUT_UI, "logo.png")
    os.makedirs(os.path.dirname(caminho), exist_ok=True)
    cv.img.resize((32 * scale, 32 * scale), Image.NEAREST).save(caminho)
    print(f"  ok  ui/logo.png ({32 * scale}px)")


def _fonte_pixelify(tamanho: int) -> ImageFont.FreeTypeFont:
    cache = os.path.join(RAIZ, "tools", ".cache", "PixelifySans-Bold.ttf")
    os.makedirs(os.path.dirname(cache), exist_ok=True)
    if not os.path.isfile(cache):
        url = (
            "https://github.com/google/fonts/raw/main/ofl/pixelifysans/"
            "PixelifySans%5Bwght%5D.ttf"
        )
        urllib.request.urlretrieve(url, cache)
    return ImageFont.truetype(cache, tamanho)


def gerar_logo_marca() -> None:
    """Wordmark horizontal: escudo + 'Algorithmia' com gradiente roxo→ouro."""
    escala = 4
    largura, altura = 320, 72
    w, h = largura * escala, altura * escala
    base = Image.new("RGBA", (w, h), (0, 0, 0, 0))

    # Ícone ampliado à esquerda
    icone = Canvas(48, 48)
    desenhar_simbolo(icone, 24, 24, 1.35)
    icone_img = icone.img.resize((52 * escala, 52 * escala), Image.NEAREST)
    iy = (h - icone_img.height) // 2
    base.paste(icone_img, (8, iy), icone_img)

    texto = "Algorithmia"
    fonte = _fonte_pixelify(44)
    draw = ImageDraw.Draw(base)

    bbox = draw.textbbox((0, 0), texto, font=fonte)
    tw, th = bbox[2] - bbox[0], bbox[3] - bbox[1]
    tx = 68 * escala
    ty = (h - th) // 2 - bbox[1]

    # Sombra 3D (canto inferior direito)
    sombra = Image.new("RGBA", (w, h), (0, 0, 0, 0))
    ds = ImageDraw.Draw(sombra)
    for dx, dy in [(3, 3), (2, 3), (3, 2)]:
        ds.text((tx + dx, ty + dy), texto, font=fonte, fill=(18, 14, 36, 220))
    base = Image.alpha_composite(base, sombra)

    # Gradiente roxo → ouro aplicado via máscara do texto
    mask = Image.new("L", (w, h), 0)
    md = ImageDraw.Draw(mask)
    md.text((tx, ty), texto, font=fonte, fill=255)

    grad = Image.new("RGBA", (w, h))
    gd = ImageDraw.Draw(grad)
    x0, x1 = tx, tx + tw
    for x in range(w):
        t = max(0.0, min(1.0, (x - x0) / max(1, x1 - x0)))
        cor = tuple(round(ROXO_2[i] + (OURO[i] - ROXO_2[i]) * t) for i in range(3)) + (255,)
        gd.line([(x, 0), (x, h)], fill=cor)

    grad.putalpha(mask)
    final = Image.alpha_composite(base, grad)

    caminho = os.path.join(OUT_UI, "logo-marca.png")
    final.save(caminho)
    print(f"  ok  ui/logo-marca.png ({w}×{h}px)")


def gerar_favicon() -> None:
    """Favicon 32×32 com fundo navy do jogo."""
    cv = Canvas(32, 32)
    navy = (11, 12, 29, 255)
    cv.rect(0, 0, 31, 31, navy)
    desenhar_simbolo(cv, 16, 16, 0.92)
    caminho = os.path.join(RAIZ, "public", "favicon.png")
    cv.img.resize((32, 32), Image.NEAREST).save(caminho)
    print("  ok  public/favicon.png (32px)")


if __name__ == "__main__":
    print("Gerando marca Algorithmia...")
    gerar_logo()
    gerar_logo_marca()
    gerar_favicon()
    print("Concluído.")
