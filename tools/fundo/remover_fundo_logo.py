#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Remove fundo escuro dos logos ilustrados e exporta PNG com alpha."""
import os
from collections import deque
from PIL import Image

RAIZ = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))


def _lum(r, g, b):
    return 0.2126 * r + 0.7152 * g + 0.0722 * b


def _is_background(r, g, b, a):
    if a < 8:
        return True
    l = _lum(r, g, b)
    sat = max(r, g, b) - min(r, g, b)
    if l < 38:
        return True
    if l < 62 and sat < 42:
        return True
    return False


def remover_fundo(im: Image.Image) -> Image.Image:
    im = im.convert("RGBA")
    w, h = im.size
    px = im.load()
    bg = [[False] * w for _ in range(h)]
    seen = [[False] * w for _ in range(h)]
    q = deque()

    for x in range(w):
        for y in (0, h - 1):
            if not seen[y][x]:
                seen[y][x] = True
                q.append((x, y))
    for y in range(h):
        for x in (0, w - 1):
            if not seen[y][x]:
                seen[y][x] = True
                q.append((x, y))

    while q:
        x, y = q.popleft()
        r, g, b, a = px[x, y]
        if _is_background(r, g, b, a):
            bg[y][x] = True
            for nx, ny in ((x + 1, y), (x - 1, y), (x, y + 1), (x, y - 1)):
                if 0 <= nx < w and 0 <= ny < h and not seen[ny][nx]:
                    seen[ny][nx] = True
                    q.append((nx, ny))

    out = Image.new("RGBA", (w, h), (0, 0, 0, 0))
    opx = out.load()
    for y in range(h):
        for x in range(w):
            if not bg[y][x]:
                opx[x, y] = px[x, y]
    return out


def _mascara_circular(im: Image.Image, raio_pct: float = 0.36, suav: float = 0.04) -> Image.Image:
    """Mantém só o emblema central; remove auréola/nebulosa que forma caixa escura."""
    im = im.convert("RGBA")
    w, h = im.size
    bbox = im.getbbox()
    if not bbox:
        return im
    cx = (bbox[0] + bbox[2]) / 2
    cy = (bbox[1] + bbox[3]) / 2
    raio = min(w, h) * raio_pct
    borda = min(w, h) * suav
    px = im.load()
    for y in range(h):
        for x in range(w):
            r, g, b, a = px[x, y]
            if a == 0:
                continue
            d = ((x - cx) ** 2 + (y - cy) ** 2) ** 0.5
            if d > raio + borda:
                px[x, y] = (0, 0, 0, 0)
            elif d > raio:
                t = (d - raio) / max(borda, 1)
                px[x, y] = (r, g, b, int(a * (1 - t)))
            elif _lum(r, g, b) < 28 and a < 200:
                # resíduos escuros dentro do círculo
                px[x, y] = (0, 0, 0, 0)
    return im


def _trim(im: Image.Image, pad: int = 8) -> Image.Image:
    bbox = im.getbbox()
    if not bbox:
        return im
    x0, y0, x1, y1 = bbox
    x0 = max(0, x0 - pad)
    y0 = max(0, y0 - pad)
    x1 = min(im.width, x1 + pad)
    y1 = min(im.height, y1 + pad)
    return im.crop((x0, y0, x1, y1))


def _quadrado(im: Image.Image, size: int) -> Image.Image:
    im = _trim(im)
    side = max(im.size)
    canvas = Image.new("RGBA", (side, side), (0, 0, 0, 0))
    canvas.paste(im, ((side - im.width) // 2, (side - im.height) // 2), im)
    return canvas.resize((size, size), Image.LANCZOS)


def processar_entrada(src: str, dst: str, size: int = 512) -> None:
    im = Image.open(src)
    limpo = remover_fundo(im)
    limpo = _mascara_circular(limpo)
    final = _quadrado(limpo, size)
    os.makedirs(os.path.dirname(dst), exist_ok=True)
    final.save(dst, optimize=True)
    print(f"  ok  {os.path.relpath(dst, RAIZ)} ({final.size[0]}px, alpha)")


def processar_retrato_hud(src: str, dst: str, size: int = 256) -> None:
    """Remove fundo preto dos bustos HUD (sem máscara circular)."""
    im = Image.open(src)
    limpo = remover_fundo(im)
    final = _quadrado(limpo, size)
    os.makedirs(os.path.dirname(dst), exist_ok=True)
    final.save(dst, optimize=True)
    print(f"  ok  {os.path.relpath(dst, RAIZ)} ({final.size[0]}px, alpha)")


def processar_retrato_classe(src: str, dst: str, width: int = 512) -> None:
    """Exporta card de classe — largura fixa, altura proporcional, sem crop nem letterbox."""
    im = Image.open(src).convert("RGB")
    w, h = im.size
    if w <= 0 or h <= 0:
        return
    nh = max(1, round(h * width / w))
    final = im.resize((width, nh), Image.LANCZOS)
    os.makedirs(os.path.dirname(dst), exist_ok=True)
    final.save(dst, optimize=True, quality=92)
    print(f"  ok  {os.path.relpath(dst, RAIZ)} ({final.size[0]}×{final.size[1]}px, inteiro)")


def processar_marca(src: str | None = None) -> None:
    """Remove fundo preto/nebulosa do wordmark ilustrado."""
    if src is None:
        src = os.path.join(RAIZ, "tools", ".cache", "logo-marca-ilustrado-src.png")
    if not os.path.isfile(src):
        return
    dst = os.path.join(RAIZ, "public", "img", "ui", "logos", "logo-marca-ilustrado.png")
    os.makedirs(os.path.dirname(dst), exist_ok=True)

    def is_bg(r, g, b, a):
        if a < 5:
            return True
        l = _lum(r, g, b)
        sat = max(r, g, b) - min(r, g, b)
        if l < 32:
            return True
        if l < 50 and sat < 22:
            return True
        return False

    im = Image.open(src).convert("RGBA")
    w, h = im.size
    px = im.load()
    bg = [[False] * w for _ in range(h)]
    seen = [[False] * w for _ in range(h)]
    q = deque()
    for x in range(w):
        for y in (0, h - 1):
            if not seen[y][x]:
                seen[y][x] = True
                q.append((x, y))
    for y in range(h):
        for x in (0, w - 1):
            if not seen[y][x]:
                seen[y][x] = True
                q.append((x, y))
    while q:
        x, y = q.popleft()
        if is_bg(*px[x, y]):
            bg[y][x] = True
            for nx, ny in ((x + 1, y), (x - 1, y), (x, y + 1), (x, y - 1)):
                if 0 <= nx < w and 0 <= ny < h and not seen[ny][nx]:
                    seen[ny][nx] = True
                    q.append((nx, ny))

    out = Image.new("RGBA", (w, h), (0, 0, 0, 0))
    opx = out.load()
    for y in range(h):
        for x in range(w):
            if bg[y][x]:
                continue
            r, g, b, a = px[x, y]
            if _lum(r, g, b) < 28:
                continue
            opx[x, y] = (r, g, b, a)

    for y in range(h):
        for x in range(w):
            r, g, b, a = opx[x, y]
            if a == 0:
                continue
            if _lum(r, g, b) < 42 and a < 220:
                opx[x, y] = (0, 0, 0, 0)

    bbox = out.getbbox()
    if bbox:
        pad = 12
        x0 = max(0, bbox[0] - pad)
        y0 = max(0, bbox[1] - pad)
        x1 = min(w, bbox[2] + pad)
        y1 = min(h, bbox[3] + pad)
        out = out.crop((x0, y0, x1, y1))

    out = out.resize((1280, round(1280 * out.height / out.width)), Image.LANCZOS)
    out.save(dst, optimize=True)
    print(f"  ok  {os.path.relpath(dst, RAIZ)} ({out.size[0]}×{out.size[1]}px, alpha)")


def processar_botao(src: str | None = None) -> None:
    """Remove xadrez de transparencia baked-in do botao da splash."""
    if src is None:
        src = os.path.join(RAIZ, "tools", ".cache", "botao-entrar-mundo-src.png")
    if not os.path.isfile(src):
        src = os.path.join(RAIZ, "public", "img", "ui", "botoes", "botao-entrar-mundo.png")
    dst = os.path.join(RAIZ, "public", "img", "ui", "botoes", "botao-entrar-mundo.png")
    os.makedirs(os.path.dirname(dst), exist_ok=True)

    def is_xadrez(r, g, b, a):
        if a < 8:
            return True
        mx, mn = max(r, g, b), min(r, g, b)
        return _lum(r, g, b) > 175 and (mx - mn) < 35

    im = Image.open(src).convert("RGBA")
    w, h = im.size
    px = im.load()
    bg = [[False] * w for _ in range(h)]
    seen = [[False] * w for _ in range(h)]
    q = deque()
    for x in range(w):
        for y in (0, h - 1):
            if not seen[y][x]:
                seen[y][x] = True
                q.append((x, y))
    for y in range(h):
        for x in (0, w - 1):
            if not seen[y][x]:
                seen[y][x] = True
                q.append((x, y))
    while q:
        x, y = q.popleft()
        if is_xadrez(*px[x, y]):
            bg[y][x] = True
            for nx, ny in ((x + 1, y), (x - 1, y), (x, y + 1), (x, y - 1)):
                if 0 <= nx < w and 0 <= ny < h and not seen[ny][nx]:
                    seen[ny][nx] = True
                    q.append((nx, ny))

    out = Image.new("RGBA", (w, h), (0, 0, 0, 0))
    opx = out.load()
    for y in range(h):
        for x in range(w):
            if not bg[y][x]:
                opx[x, y] = px[x, y]
    out = _trim(out, pad=4)
    out = out.resize((520, round(520 * out.height / out.width)), Image.LANCZOS)
    out.save(dst, optimize=True)
    print(f"  ok  {os.path.relpath(dst, RAIZ)} ({out.size[0]}×{out.size[1]}px, alpha)")


def main():
    src_icon = os.path.join(RAIZ, "tools", ".cache", "logo-ilustrado-src.png")
    if not os.path.isfile(src_icon):
        src_icon = os.path.join(RAIZ, "public", "img", "ui", "logo-ilustrado.png")

    src_marca = os.path.join(RAIZ, "tools", ".cache", "logo-marca-ilustrado-src.png")
    if not os.path.isfile(src_marca):
        src_marca = os.path.join(RAIZ, "public", "img", "ui", "logos", "logo-marca-ilustrado.png")

    print("Removendo fundo dos logos ilustrados...")
    processar_entrada(src_icon, os.path.join(RAIZ, "public", "img", "ui", "logo-ilustrado.png"))
    fav = Image.open(os.path.join(RAIZ, "public", "img", "ui", "logo-ilustrado.png"))
    fav.resize((32, 32), Image.LANCZOS).save(os.path.join(RAIZ, "public", "favicon.png"), optimize=True)
    print("  ok  public/favicon.png")

    # Wordmark limpo (fundo preto sólido no original)
    processar_marca(src_marca)

    btn_src = os.path.join(RAIZ, "tools", ".cache", "botao-entrar-mundo-src.png")
    if os.path.isfile(btn_src):
        processar_botao(btn_src)


if __name__ == "__main__":
    main()
