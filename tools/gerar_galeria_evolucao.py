#!/usr/bin/env python3
"""
Galeria da EVOLUÇÃO VISUAL do jogo — contact-sheet por versão/categoria, com a
identidade visual do Algorithmia (tools/marca_pdf.py), consistente com o Códex.

Varre docs/evolucao-visual/v*/, miniaturiza as imagens e gera um PDF folheável.

Uso:  python3 tools/gerar_galeria_evolucao.py   (requer Pillow + Chrome headless)
"""

import glob
import os
import subprocess
import sys
import tempfile

from PIL import Image

import marca_pdf as marca

RAIZ = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
BASE = os.path.join(RAIZ, "docs", "evolucao-visual")
SAIDA_PDF = os.path.join(BASE, "Evolucao-Visual.pdf")
THUMBS = os.path.join(tempfile.gettempdir(), "evolucao_thumbs")
SAIDA_HTML = os.path.join(tempfile.gettempdir(), "_evolucao-galeria.html")
CHROME = "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"
THUMB_MAX = 360


def rotulo_versao(nome: str) -> str:
    p = nome.split("-")
    return p[0].upper() + " — " + " ".join(x.capitalize() for x in p[1:])


def rotulo_categoria(nome: str) -> str:
    return nome.lstrip("_").replace("-", " ").capitalize()


def preparar_thumb(caminho: str):
    try:
        with Image.open(caminho) as im:
            w, h = im.size
            pixel = max(w, h) <= 256
            if max(w, h) <= THUMB_MAX:
                return "file://" + caminho, pixel
            im = im.convert("RGBA")
            im.thumbnail((THUMB_MAX, THUMB_MAX), Image.LANCZOS)
            dest = os.path.join(THUMBS, os.path.relpath(caminho, BASE).replace(os.sep, "__"))
            im.save(dest, "PNG")
            return "file://" + dest, pixel
    except Exception as e:
        print(f"  ! ignorando {caminho}: {e}", file=sys.stderr)
        return "", False


def cards_da_pasta(pasta: str) -> str:
    cards = []
    for caminho in sorted(f for f in glob.glob(os.path.join(pasta, "*")) if f.lower().endswith(".png")):
        uri, pixel = preparar_thumb(caminho)
        if not uri:
            continue
        cls = "thumb pixel" if pixel else "thumb"
        cards.append(f'<figure class="{cls}"><img src="{uri}"><figcaption>{os.path.basename(caminho)}</figcaption></figure>')
    return "".join(cards)


def render_versao(vdir: str) -> str:
    nome = os.path.basename(vdir.rstrip("/"))
    subpastas = sorted(
        (d for d in glob.glob(os.path.join(vdir, "*")) if os.path.isdir(d)),
        key=lambda d: (os.path.basename(d).startswith("_"), os.path.basename(d)),
    )
    blocos = []
    soltas = cards_da_pasta(vdir)
    if soltas:
        blocos.append(f'<h3>Geral</h3><div class="grid">{soltas}</div>')
    for sub in subpastas:
        cards = cards_da_pasta(sub)
        if cards:
            blocos.append(f'<h3>{rotulo_categoria(os.path.basename(sub))}</h3><div class="grid">{cards}</div>')
    qtd = sum(len(glob.glob(os.path.join(d, "*.png"))) for d in [vdir, *subpastas])
    return f"""
      <section class="versao">
        <div class="abre-versao">
          {marca.logo_img('header')}
          <div class="vtit"><span class="epico">{rotulo_versao(nome)}</span><span class="vqtd">{qtd} imagens</span></div>
        </div>
        {''.join(blocos)}
      </section>"""


def css_doc() -> str:
    return """
    body { background: radial-gradient(120% 80% at 50% -8%, #fbfaf6, var(--papel) 48%, var(--papel2)); }
    .capa { width:210mm; height:297mm; page-break-after:always; position:relative; overflow:hidden;
      display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;
      background:radial-gradient(140% 100% at 50% 2%, #241c52, #0e0f28 52%, #07081a); color:#fff; }
    .moldura-capa { position:absolute; inset:9mm; border:1.5px solid rgba(255,206,71,.55); border-radius:5px; }
    .moldura-capa::before { content:''; position:absolute; inset:3.5mm; border:1px solid rgba(124,92,255,.45); border-radius:4px; }
    .logo-capa img { width:135mm; height:auto; display:block; filter:drop-shadow(0 10px 38px rgba(124,92,255,.55)); }
    .capa .titulo-doc { font-family:'Cinzel',serif; font-size:29px; letter-spacing:9px; color:var(--ouro); margin-top:26px; }
    .capa .sub { font-family:'Cinzel',serif; font-size:12px; letter-spacing:4px; color:#cfd2ff; margin-top:9px; text-transform:uppercase; }
    .capa .selo { position:absolute; bottom:16mm; font-family:'Cinzel',serif; font-size:10.5px; letter-spacing:2.5px; color:#9499c0; }

    .versao { page-break-before:always; padding:6mm 10mm 0; }
    .abre-versao { display:flex; align-items:center; justify-content:space-between;
      background:linear-gradient(100deg,#0c0d22,#1a1640,#0c0d22); border-radius:8px;
      border:1px solid rgba(255,206,71,.4); padding:8px 16px; color:#fff; margin-bottom:12px; }
    .abre-versao .logo-jogo { height:9mm; width:auto; filter:drop-shadow(0 1px 4px rgba(0,0,0,.4)); }
    .vtit { text-align:right; }
    .vtit .epico { font-family:'Cinzel',serif; font-size:18px; color:var(--ouro); display:block; }
    .vtit .vqtd { font-size:9px; color:#b9a7ff; letter-spacing:1px; }

    h3 { font-family:'Cinzel',serif; font-size:12px; text-transform:uppercase; letter-spacing:1.5px; color:#5a4a8a;
      border-left:3px solid var(--primaria); background:#efeadf; padding:5px 12px; border-radius:0 6px 6px 0; margin:16px 0 10px; }
    .grid { display:grid; grid-template-columns:repeat(5,1fr); gap:10px; }
    .thumb { margin:0; text-align:center; page-break-inside:avoid; }
    .thumb img { width:100%; height:96px; object-fit:contain; border:1px solid #e2dcce; border-radius:6px;
      background:repeating-conic-gradient(#f1efe6 0% 25%, #fff 0% 50%) 50% / 14px 14px; }
    .thumb.pixel img { image-rendering:pixelated; }
    .thumb figcaption { font-size:7.5px; color:#7a7488; margin-top:3px; word-break:break-all; line-height:1.2; }
    """


def render_capa() -> str:
    return f"""
    <div class="capa">
      <div class="moldura-capa"></div>
      <div class="logo-capa">{marca.logo_img('full')}</div>
      <div class="titulo-doc">EVOLUÇÃO VISUAL</div>
      <div class="sub">Histórico da Arte do Reino · Versão a Versão</div>
      <div class="selo">Marca registrada do projeto · pixel art → ilustrações</div>
    </div>"""


def build_html(versoes) -> str:
    fontes = marca.garantir_fontes()
    corpo = "".join(render_versao(v) for v in versoes)
    return f"""<!DOCTYPE html><html lang="pt-BR"><head><meta charset="utf-8">
<style>{marca.css_marca(fontes)}{css_doc()}</style></head>
<body>{render_capa()}{corpo}</body></html>"""


def main():
    versoes = sorted(
        d for d in glob.glob(os.path.join(BASE, "v*"))
        if os.path.isdir(d) and os.path.basename(d)[1:2].isdigit()
    )
    if not versoes:
        sys.exit("Nenhuma versão encontrada em docs/evolucao-visual/.")
    os.makedirs(THUMBS, exist_ok=True)
    with open(SAIDA_HTML, "w", encoding="utf-8") as fp:
        fp.write(build_html(versoes))
    print(f"Versões: {', '.join(os.path.basename(v) for v in versoes)}")
    if not os.path.exists(CHROME):
        sys.exit(f"Chrome não encontrado em {CHROME}.")
    subprocess.run(
        [CHROME, "--headless=new", "--disable-gpu", "--no-pdf-header-footer",
         "--virtual-time-budget=30000", f"--print-to-pdf={SAIDA_PDF}", "file://" + SAIDA_HTML],
        check=True, capture_output=True,
    )
    print(f"PDF gerado: {SAIDA_PDF}")


if __name__ == "__main__":
    main()
