#!/usr/bin/env python3
"""Right-size das .webp ilustradas servidas em public/img/.

Reduz a RESOLUÇÃO das webp para o tamanho máximo em que cada imagem
realmente aparece na tela (com folga ~2.3x para telas retina). A ARTE NÃO
muda — só o peso do arquivo cai, porque hoje várias artes são 1280–1536px
exibidas em slots de 88–320px (5–30x maiores que o necessário).

- Idempotente: só reduz quem está acima do cap; rodar de novo é no-op.
- Os PNGs originais (public/img + docs/evolucao-visual) ficam INTACTOS, então
  dá para regerar tudo a qualquer momento.
- fundos/ (cenários full-bleed) NÃO entram: são mostrados grandes.

Caps por pasta = maior tamanho de exibição no CSS x ~2.3 (lado maior, px):
  mapas    384  (nós do mapa ~108px, retrato da região ~64px)
  inimigos 640  (sprite da arena ~270px)
  mestres  720  (card do mestre na home ~300px)
  atores   900  (retrato grande no diálogo)
  herois   720  (card na ficha ~320px; pega card-*/hud-*, não o pixel art)

Uso:  python3 tools/right_size_imagens.py
"""
import glob
import os

from PIL import Image

BASE = os.path.join(os.path.dirname(__file__), '..', 'public', 'img')
CAPS = {
    'mapas': 384,
    'inimigos': 640,
    'mestres': 720,
    'atores': 900,
    'herois': 720,
}
QUALITY = 82


def main() -> None:
    antes = depois = 0
    reduzidas = 0
    for pasta, cap in CAPS.items():
        for caminho in sorted(glob.glob(os.path.join(BASE, pasta, '*.webp'))):
            tam = os.path.getsize(caminho)
            antes += tam
            with Image.open(caminho) as im:
                w, h = im.size
                maior = max(w, h)
                if maior <= cap:
                    depois += tam
                    continue
                escala = cap / maior
                nw, nh = round(w * escala), round(h * escala)
                novo = im.convert('RGBA').resize((nw, nh), Image.LANCZOS)
                novo.save(caminho, 'WEBP', quality=QUALITY, method=6)
            nt = os.path.getsize(caminho)
            depois += nt
            reduzidas += 1
            print(f"  {pasta}/{os.path.basename(caminho)}: {w}x{h} -> {nw}x{nh}  "
                  f"({tam // 1024}KB -> {nt // 1024}KB)")
    print(f"\n{reduzidas} imagens reduzidas.")
    print(f"Total webp (pastas afetadas): {antes // 1024}KB -> {depois // 1024}KB "
          f"(-{(antes - depois) // 1024}KB)")


if __name__ == '__main__':
    main()
