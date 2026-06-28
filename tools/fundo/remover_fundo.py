#!/usr/bin/env python3
"""
Remove o fundo (branco/xadrez + sombra de contato) dos sprites v2 gerados por IA,
deixando recorte limpo com bordas SUAVES (anti-aliased), no padrão dos personagens.

Estratégia (validada sobre o fundo real da arena):
  1. Flood-fill das bordas pelo fundo: pixels DESSATURADOS e claros (inclui a
     sombra cinza de contato, não só o branco) — para no contorno colorido/escuro
     da arte. Preserva metal/prata e seres etéreos (têm cor/gradiente).
  2. Bolsões claros e UNIFORMES presos na arte (sombra interna) também são removidos.
  3. Erosão de 1px + leve blur na máscara → bordas suaves, sem halo branco nem
     serrilhado (~poucos % de pixels semitransparentes, como os narradores).

Não redimensiona: a resolução do PNG é preservada.

Uso:
  python3 tools/remover_fundo.py --grupos          # os 60 assets v2
  python3 tools/remover_fundo.py <arquivo.png> ...
  python3 tools/remover_fundo.py --saida /tmp x.png # grava cópia (não sobrescreve)
"""

import glob
import os
import sys
from collections import deque

from PIL import Image, ImageFilter

RAIZ = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
PUB = os.path.join(RAIZ, "public", "img")

GRUPOS = (
    glob.glob(os.path.join(PUB, "inimigos", "*.png"))
    + [os.path.join(PUB, "herois", f"heroi-{c}.png")
       for c in ("guerreiro", "mago", "ranger", "xeno", "elfo", "draconato")]
    + glob.glob(os.path.join(PUB, "itens", "*.png"))
    + glob.glob(os.path.join(PUB, "ui", "icones", "*.png"))
    + glob.glob(os.path.join(PUB, "ui", "trofeus", "*.png"))
)

BG_DESSAT = 30     # fundo/sombra: dessaturado (max-min <= isto)
BG_LUM_MIN = 125   # ...e claridade média >= isto (capta a sombra cinza, não só branco)
POCO_CLARO_MIN = 150   # bolsão interno: canal mínimo >= isto
POCO_DESSAT = 34
POCO_MIN_PIXELS = 64
POCO_BRILHO_MIN = 210  # ...claro o bastante (fundo), não meio-tom de arte
POCO_DESVIO_MAX = 22   # ...e uniforme (liso): metal/prata têm desvio maior


def _dessat(t) -> int:
    return max(t[0], t[1], t[2]) - min(t[0], t[1], t[2])


def _lum(t) -> int:
    return (t[0] + t[1] + t[2]) // 3


def _eh_fundo(t) -> bool:
    return _dessat(t) <= BG_DESSAT and _lum(t) >= BG_LUM_MIN


def _eh_claro(t) -> bool:
    return min(t[0], t[1], t[2]) >= POCO_CLARO_MIN and _dessat(t) <= POCO_DESSAT


def ja_transparente(im: Image.Image) -> bool:
    if im.mode not in ("RGBA", "LA") and "transparency" not in im.info:
        return False
    rgba = im.convert("RGBA")
    w, h = rgba.size
    px = rgba.load()
    cantos = [(1, 1), (w - 2, 1), (1, h - 2), (w - 2, h - 2)]
    return any(px[x, y][3] < 20 for x, y in cantos)


def remover_fundo(caminho: str) -> str:
    im = Image.open(caminho)
    if ja_transparente(im):
        return "já-transparente"

    rgb = im.convert("RGB")
    w, h = rgb.size
    n = w * h
    dados = list(rgb.getdata())
    alpha = bytearray(b"\xff" * n)

    def vizinhos(idx, x):
        if x > 0:
            yield idx - 1
        if x < w - 1:
            yield idx + 1
        if idx >= w:
            yield idx - w
        if idx < n - w:
            yield idx + w

    # 1) flood das bordas pelo fundo (branco + sombra dessaturada).
    dq = deque()
    for x in range(w):
        for idx in (x, (h - 1) * w + x):
            if alpha[idx] and _eh_fundo(dados[idx]):
                alpha[idx] = 0
                dq.append(idx)
    for y in range(h):
        for idx in (y * w, y * w + w - 1):
            if alpha[idx] and _eh_fundo(dados[idx]):
                alpha[idx] = 0
                dq.append(idx)
    if not dq:
        return "sem-fundo claro (intacto)"
    while dq:
        idx = dq.popleft()
        x = idx % w
        for j in vizinhos(idx, x):
            if alpha[j] and _eh_fundo(dados[j]):
                alpha[j] = 0
                dq.append(j)

    # 2) bolsões claros uniformes presos na arte (sombra interna de contato).
    visitado = bytearray(n)
    for inicio in range(n):
        if not alpha[inicio] or visitado[inicio] or not _eh_claro(dados[inicio]):
            continue
        comp = [inicio]
        visitado[inicio] = 1
        pilha = [inicio]
        while pilha:
            idx = pilha.pop()
            x = idx % w
            for j in vizinhos(idx, x):
                if not visitado[j] and alpha[j] and _eh_claro(dados[j]):
                    visitado[j] = 1
                    pilha.append(j)
                    comp.append(j)
        if len(comp) < POCO_MIN_PIXELS:
            continue
        brilhos = [_lum(dados[i]) for i in comp]
        media = sum(brilhos) / len(brilhos)
        desvio = (sum((b - media) ** 2 for b in brilhos) / len(brilhos)) ** 0.5
        if media >= POCO_BRILHO_MIN and desvio <= POCO_DESVIO_MAX:
            for i in comp:
                alpha[i] = 0

    # 3) bordas suaves: erode 1px (tira o anel branco do anti-aliasing) + blur leve.
    mascara = Image.frombytes("L", (w, h), bytes(alpha))
    mascara = mascara.filter(ImageFilter.MinFilter(3)).filter(ImageFilter.GaussianBlur(0.8))

    rgba = Image.merge("RGBA", (*rgb.split(), mascara))
    rgba.save(caminho, "PNG")
    hist = mascara.histogram()
    transp = hist[0] * 100 // n
    return f"fundo removido ({transp}% transparente, bordas suaves)"


def main(argv):
    saida = None
    if "--saida" in argv:
        i = argv.index("--saida")
        saida = argv[i + 1]
        argv = argv[:i] + argv[i + 2:]

    if "--grupos" in argv:
        alvos = [p for p in GRUPOS if os.path.isfile(p)]
    else:
        alvos = [a for a in argv if a.endswith(".png")]
    if not alvos:
        sys.exit("Nada para processar. Use --grupos ou passe arquivos .png.")

    for caminho in alvos:
        destino = caminho
        if saida:
            os.makedirs(saida, exist_ok=True)
            destino = os.path.join(saida, os.path.basename(caminho))
            Image.open(caminho).save(destino)
        status = remover_fundo(destino)
        print(f"  {os.path.relpath(destino, RAIZ):45s} {status}")


if __name__ == "__main__":
    main(sys.argv[1:])
