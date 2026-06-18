#!/usr/bin/env python3
"""
Recorte de fundo PROFISSIONAL via rembg (modelo isnet-general-use, U²-Net).

Segmenta o objeto em primeiro plano por IA — entende a criatura/ícone em vez de
adivinhar por cor — removendo fundo branco/xadrez E a sombra de contato, com
bordas suaves anti-aliased. Preserva metal/prata e detalhe. Não redimensiona.

Aplica-se aos sprites que são RECORTE (fundo transparente): inimigos, heróis de
batalha, ícones de UI e troféus. As CARTAS de item (public/img/itens/) NÃO entram
aqui — são molduras inteiras; use tools/remover_fundo.py só para aparar a margem.

Requer o venv dedicado (rembg + onnxruntime):
  python3.11 -m venv tools/.venv-rembg
  tools/.venv-rembg/bin/pip install rembg onnxruntime pillow

Uso:
  tools/.venv-rembg/bin/python tools/recortar_rembg.py --grupos
  tools/.venv-rembg/bin/python tools/recortar_rembg.py <arquivo.png> ...
"""

import glob
import os
import sys

from PIL import Image
from rembg import new_session, remove

RAIZ = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
PUB = os.path.join(RAIZ, "public", "img")
MODELO = "isnet-general-use"

GRUPOS = (
    glob.glob(os.path.join(PUB, "inimigos", "*.png"))
    + [os.path.join(PUB, "herois", f"heroi-{c}.png")
       for c in ("guerreiro", "mago", "ranger", "xeno", "elfo", "draconato")]
    + glob.glob(os.path.join(PUB, "ui", "icones", "*.png"))
    + glob.glob(os.path.join(PUB, "ui", "trofeus", "*.png"))
)


def main(argv):
    if "--grupos" in argv:
        alvos = [p for p in GRUPOS if os.path.isfile(p)]
    else:
        alvos = [a for a in argv if a.endswith(".png")]
    if not alvos:
        sys.exit("Nada para processar. Use --grupos ou passe arquivos .png.")

    sessao = new_session(MODELO)
    for caminho in alvos:
        entrada = Image.open(caminho).convert("RGBA")
        # Máscara suave do modelo (sem alpha-matting: evita o solver instável e
        # mantém bordas naturais anti-aliased). post_process_mask limpa respingos.
        saida = remove(entrada, session=sessao, post_process_mask=True)
        if saida.size != entrada.size:
            saida = saida.resize(entrada.size, Image.LANCZOS)
        saida.save(caminho, "PNG")
        hist = saida.split()[3].histogram()
        n = sum(hist)
        transp = hist[0] * 100 // n
        print(f"  {os.path.relpath(caminho, RAIZ):45s} recortado ({transp}% transparente)")


if __name__ == "__main__":
    main(sys.argv[1:])
