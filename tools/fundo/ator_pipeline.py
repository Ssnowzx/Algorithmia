#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Pipeline compartilhado: personagem 100% opaco, sem buracos nem halos."""
from __future__ import annotations

from collections import deque

from PIL import Image


def _lum(r: int, g: int, b: int) -> float:
    return 0.2126 * r + 0.7152 * g + 0.0722 * b


def _dist(c1: tuple[int, int, int], c2: tuple[int, int, int]) -> float:
    return ((c1[0] - c2[0]) ** 2 + (c1[1] - c2[1]) ** 2 + (c1[2] - c2[2]) ** 2) ** 0.5


def _amostra_fundo(img: Image.Image) -> tuple[int, int, int]:
    px = img.load()
    w, h = img.size
    amostras: list[tuple[int, int, int]] = []
    for x, y in (
        (0, 0), (w - 1, 0), (0, h - 1), (w - 1, h - 1),
        (w // 2, 0), (w // 2, h - 1), (0, h // 2), (w - 1, h // 2),
    ):
        r, g, b, a = px[x, y]
        if a > 10:
            amostras.append((r, g, b))
    if not amostras:
        return (16, 16, 24)
    return (
        sum(c[0] for c in amostras) // len(amostras),
        sum(c[1] for c in amostras) // len(amostras),
        sum(c[2] for c in amostras) // len(amostras),
    )


def _eh_fundo_escuro(
    r: int, g: int, b: int, a: int,
    ref: tuple[int, int, int], tol: float,
) -> bool:
    if a < 12:
        return True
    if _lum(r, g, b) < 20:
        return True
    if _dist((r, g, b), ref) <= tol:
        return True
    mx, mn = max(r, g, b), min(r, g, b)
    if _lum(r, g, b) < 52 and (mx - mn) < 32:
        return True
    # Céu/cenário roxo-escuro ligado às bordas (ícones de mapa)
    if b > r + 8 and b > g + 4 and _lum(r, g, b) < 72:
        return True
    return False


def _eh_branco(r: int, g: int, b: int, a: int, limiar: int = 248) -> bool:
    if a < 10:
        return True
    return r >= limiar and g >= limiar and b >= limiar


def remover_fundo_escuro(img: Image.Image, tol: float = 48.0) -> Image.Image:
    img = img.convert("RGBA")
    px = img.load()
    w, h = img.size
    ref = _amostra_fundo(img)
    visitado: set[tuple[int, int]] = set()
    fila: deque[tuple[int, int]] = deque()

    for x in range(w):
        fila.append((x, 0))
        fila.append((x, h - 1))
    for y in range(h):
        fila.append((0, y))
        fila.append((w - 1, y))

    while fila:
        x, y = fila.popleft()
        if (x, y) in visitado:
            continue
        visitado.add((x, y))
        r, g, b, a = px[x, y]
        if not _eh_fundo_escuro(r, g, b, a, ref, tol):
            continue
        px[x, y] = (0, 0, 0, 0)
        for nx, ny in ((x + 1, y), (x - 1, y), (x, y + 1), (x, y - 1)):
            if 0 <= nx < w and 0 <= ny < h:
                fila.append((nx, ny))

    return img


def remover_fundo_branco(img: Image.Image, limiar: int = 248) -> Image.Image:
    img = img.convert("RGBA")
    px = img.load()
    w, h = img.size
    visitado: set[tuple[int, int]] = set()
    fila: deque[tuple[int, int]] = deque()

    for x in range(w):
        for y in (0, h - 1):
            fila.append((x, y))
    for y in range(h):
        for x in (0, w - 1):
            fila.append((x, y))

    while fila:
        x, y = fila.popleft()
        if (x, y) in visitado:
            continue
        visitado.add((x, y))
        r, g, b, a = px[x, y]
        if not _eh_branco(r, g, b, a, limiar):
            continue
        px[x, y] = (255, 255, 255, 0)
        for nx, ny in ((x + 1, y), (x - 1, y), (x, y + 1), (x, y - 1)):
            if 0 <= nx < w and 0 <= ny < h:
                fila.append((nx, ny))

    return img


def solidificar(img: Image.Image, limiar: int = 40) -> Image.Image:
    """Personagem 100% opaco; véus fracos viram transparente."""
    img = img.convert("RGBA")
    px = img.load()
    w, h = img.size
    for y in range(h):
        for x in range(w):
            r, g, b, a = px[x, y]
            if a <= limiar:
                px[x, y] = (0, 0, 0, 0)
                continue
            if a < 255:
                r = min(255, int(r * 255 / a))
                g = min(255, int(g * 255 / a))
                b = min(255, int(b * 255 / a))
            px[x, y] = (r, g, b, 255)
    return img


def preencher_buracos(img: Image.Image, limiar_alpha: int = 128) -> Image.Image:
    """Preenche buracos internos (alpha baixo cercados pelo personagem)."""
    img = img.convert("RGBA")
    px = img.load()
    w, h = img.size
    externo: set[tuple[int, int]] = set()
    fila: deque[tuple[int, int]] = deque()

    for x in range(w):
        for y in (0, h - 1):
            if px[x, y][3] < limiar_alpha:
                fila.append((x, y))
    for y in range(h):
        for x in (0, w - 1):
            if px[x, y][3] < limiar_alpha:
                fila.append((x, y))

    while fila:
        x, y = fila.popleft()
        if (x, y) in externo:
            continue
        if px[x, y][3] >= limiar_alpha:
            continue
        externo.add((x, y))
        for nx, ny in ((x + 1, y), (x - 1, y), (x, y + 1), (x, y - 1)):
            if 0 <= nx < w and 0 <= ny < h:
                fila.append((nx, ny))

    for y in range(h):
        for x in range(w):
            if (x, y) in externo or px[x, y][3] >= limiar_alpha:
                continue
            rs, gs, bs, n = 0, 0, 0, 0
            for nx, ny in ((x + 1, y), (x - 1, y), (x, y + 1), (x, y - 1)):
                if 0 <= nx < w and 0 <= ny < h and px[nx, ny][3] >= limiar_alpha:
                    r, g, b, _ = px[nx, ny]
                    rs += r
                    gs += g
                    bs += b
                    n += 1
            if n:
                px[x, y] = (rs // n, gs // n, bs // n, 255)

    return img


def remover_cenario_colorido(img: Image.Image) -> Image.Image:
    """Remove céu/chão roxo-ciano dos ícones de mapa; preserva criatura colorida."""
    img = img.convert("RGBA")
    px = img.load()
    w, h = img.size
    for y in range(h):
        for x in range(w):
            r, g, b, a = px[x, y]
            if a < 40:
                continue
            lum = _lum(r, g, b)
            sat = max(r, g, b) - min(r, g, b)
            # Vermelho/laranja (bug, demônios)
            if r > 130 and r > g + 25 and sat > 35:
                continue
            # Verde (slime, floresta)
            if g > r + 18 and g > b + 10 and lum > 55:
                continue
            # Dourado/ciano brilhante (runas, cristais)
            if lum > 140 and sat > 45:
                continue
            if sat > 70 and lum > 80:
                continue
            # Cenário roxo/azul escuro
            if lum < 130 and b >= r * 0.82 and b > g:
                px[x, y] = (0, 0, 0, 0)
            elif lum < 55:
                px[x, y] = (0, 0, 0, 0)
            elif g > r + 12 and b > g + 8 and lum < 150:
                px[x, y] = (0, 0, 0, 0)
    return img


def manter_componente_central(img: Image.Image, limiar_alpha: int = 40, min_pixels: int = 350) -> Image.Image:
    """Mantém o blob principal perto do centro (personagem), descarta cenário."""
    img = img.convert("RGBA")
    px = img.load()
    w, h = img.size
    cx, cy = w / 2, h / 2
    visitado_global: set[tuple[int, int]] = set()
    melhor: list[tuple[int, int]] = []
    melhor_score = -1.0

    for sy in range(h):
        for sx in range(w):
            if (sx, sy) in visitado_global or px[sx, sy][3] < limiar_alpha:
                continue
            componente: list[tuple[int, int]] = []
            fila: deque[tuple[int, int]] = deque([(sx, sy)])
            local: set[tuple[int, int]] = set()
            while fila:
                x, y = fila.popleft()
                if (x, y) in local or px[x, y][3] < limiar_alpha:
                    continue
                local.add((x, y))
                componente.append((x, y))
                for nx, ny in ((x + 1, y), (x - 1, y), (x, y + 1), (x, y - 1)):
                    if 0 <= nx < w and 0 <= ny < h:
                        fila.append((nx, ny))
            visitado_global.update(local)
            if len(componente) < min_pixels:
                continue
            mx = sum(x for x, _ in componente) / len(componente)
            my = sum(y for _, y in componente) / len(componente)
            dist = ((mx - cx) ** 2 + (my - cy) ** 2) ** 0.5
            score = len(componente) / (1.0 + dist * 0.015)
            if score > melhor_score:
                melhor_score = score
                melhor = componente

    if not melhor:
        return img

    manter = set(melhor)
    for y in range(h):
        for x in range(w):
            if px[x, y][3] >= limiar_alpha and (x, y) not in manter:
                px[x, y] = (0, 0, 0, 0)

    return img


def fechar_silhueta(img: Image.Image, raio: int = 4, passes: int = 1) -> Image.Image:
    """Dilata levemente a silhueta para fechar fendas finas."""
    img = img.convert("RGBA")
    for _ in range(passes):
        px = img.load()
        w, h = img.size
        copia = img.copy()
        cpx = copia.load()
        for y in range(h):
            for x in range(w):
                if px[x, y][3] >= 200:
                    continue
                viz: list[tuple[int, int, int, int]] = []
                for dy in range(-raio, raio + 1):
                    for dx in range(-raio, raio + 1):
                        if dx * dx + dy * dy > raio * raio:
                            continue
                        nx, ny = x + dx, y + dy
                        if 0 <= nx < w and 0 <= ny < h and px[nx, ny][3] >= 200:
                            viz.append(px[nx, ny])
                if len(viz) >= 3:
                    r = sum(v[0] for v in viz) // len(viz)
                    g = sum(v[1] for v in viz) // len(viz)
                    b = sum(v[2] for v in viz) // len(viz)
                    cpx[x, y] = (r, g, b, 255)
        img = copia
    return img


def _tem_vizinho_transparente(px, w: int, h: int, x: int, y: int, raio: int = 2) -> bool:
    for dy in range(-raio, raio + 1):
        for dx in range(-raio, raio + 1):
            nx, ny = x + dx, y + dy
            if 0 <= nx < w and 0 <= ny < h and px[nx, ny][3] < 20:
                return True
    return False


def remover_halos(img: Image.Image) -> Image.Image:
    img = img.convert("RGBA")
    px = img.load()
    w, h = img.size
    for _ in range(2):
        for y in range(h):
            for x in range(w):
                r, g, b, a = px[x, y]
                if a < 20:
                    continue
                mx, mn = max(r, g, b), min(r, g, b)
                if not _tem_vizinho_transparente(px, w, h, x, y):
                    continue
                if mx >= 195 and (mx - mn) <= 40:
                    px[x, y] = (0, 0, 0, 0)
                    continue
                if a < 250:
                    r = min(255, int(r * 255 / max(a, 1)))
                    g = min(255, int(g * 255 / max(a, 1)))
                    b = min(255, int(b * 255 / max(a, 1)))
                    px[x, y] = (r, g, b, 255)
    return img


def recortar_conteudo(img: Image.Image, pad: int = 10, altura_max: int = 680) -> Image.Image:
    px = img.load()
    w, h = img.size
    xs, ys = [], []
    for y in range(h):
        for x in range(w):
            if px[x, y][3] > 20:
                xs.append(x)
                ys.append(y)
    if not xs:
        return img
    x0 = max(0, min(xs) - pad)
    y0 = max(0, min(ys) - pad)
    x1 = min(w - 1, max(xs) + pad)
    y1 = min(h - 1, max(ys) + pad)
    img = img.crop((x0, y0, x1 + 1, y1 + 1))
    if img.height > altura_max:
        escala = altura_max / img.height
        img = img.resize((max(1, int(img.width * escala)), altura_max), Image.LANCZOS)
    return img


def processar_palco(img: Image.Image, *, fundo_branco: bool = False) -> Image.Image:
    """Pipeline completo: fundo removido, personagem sólido, sem buracos."""
    if fundo_branco:
        img = remover_fundo_branco(img)
    else:
        img = solidificar(img, limiar=25)
        img = remover_fundo_escuro(img)
        img = remover_cenario_colorido(img)
    img = solidificar(img)
    img = preencher_buracos(img)
    img = fechar_silhueta(img)
    img = remover_halos(img)
    img = recortar_conteudo(img)
    img = solidificar(img)
    img = remover_halos(img)
    return img
