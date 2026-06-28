#!/usr/bin/env python3
"""
Kit de marca dos documentos do Algorithmia (identidade visual compartilhada).

Centraliza paleta, fontes, emblema vetorial (escudo arcano com runas </>),
ícones, cabeçalho/rodapé e o CSS-base — para que TODOS os PDFs do projeto
(fases por mestre, galeria de evolução, etc.) tenham a mesma cara de produto.

Alinhado a :root em public/css/style.css e ao emblema de tools/marca.py.
"""

import os
import urllib.request

RAIZ = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
CACHE = os.path.join(RAIZ, "tools", ".cache")
OUT_LOGO = os.path.join(RAIZ, "public", "img", "ui", "logos")

# ── Tokens (espelham o design system do jogo) ───────────────────────────────
T = {
    "bg":        "#0b0c1d",
    "bg2":       "#14162c",
    "painel":    "#1a1d38",
    "borda":     "#353c6b",
    "primaria":  "#7c5cff",
    "primaria2": "#9d83ff",
    "roxo_esc":  "#4834a8",
    "roxo_som":  "#2e2260",
    "ouro":      "#ffce47",
    "ouro_esc":  "#c4941c",
    "ciano":     "#8ce6ff",
    "texto":     "#eef0fb",
    "texto_fraco": "#9aa0c9",
    "papel":     "#f6f4ef",   # creme/pergaminho do miolo
    "papel_2":   "#efeadf",
    "tinta":     "#241f33",
}

# Fontes OFL (variáveis) — baixadas uma vez e embutidas via @font-face file://
_FONTES_REMOTAS = {
    "Pixelify Sans": "https://github.com/google/fonts/raw/main/ofl/pixelifysans/PixelifySans%5Bwght%5D.ttf",
    "Cinzel":        "https://github.com/google/fonts/raw/main/ofl/cinzel/Cinzel%5Bwght%5D.ttf",
    "Rubik":         "https://github.com/google/fonts/raw/main/ofl/rubik/Rubik%5Bwght%5D.ttf",
}


def garantir_fontes() -> dict:
    """Baixa (se preciso) as fontes da marca e devolve {nome: caminho_ttf}."""
    os.makedirs(CACHE, exist_ok=True)
    disponiveis = {}
    for nome, url in _FONTES_REMOTAS.items():
        dest = os.path.join(CACHE, nome.replace(" ", "") + ".ttf")
        if not os.path.isfile(dest):
            try:
                urllib.request.urlretrieve(url, dest)
            except Exception as e:  # offline: cai nas fontes de sistema
                print(f"  ! sem '{nome}' (fallback de sistema): {e}")
                continue
        disponiveis[nome] = dest
    return disponiveis


def _font_face(fontes: dict) -> str:
    return "\n".join(
        f"@font-face{{font-family:'{nome}';src:url('file://{p}');"
        f"font-weight:100 900;font-display:block;}}"
        for nome, p in fontes.items()
    )


# ── Emblema vetorial: escudo arcano + runas </> ─────────────────────────────
def emblema_svg(tam: int = 64, accent: str = None, idr: str = "emb") -> str:
    accent = accent or T["ouro"]
    h = round(tam * 1.16)
    escudo = ("M50 4 L92 18 L92 60 C92 88 73 104 50 112 "
              "C27 104 8 88 8 60 L8 18 Z")
    return f'''<svg width="{tam}" height="{h}" viewBox="0 0 100 116" fill="none" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="{idr}g" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="{T['primaria2']}"/><stop offset="1" stop-color="{T['roxo_esc']}"/>
    </linearGradient>
    <radialGradient id="{idr}gl" cx="0.5" cy="0.4" r="0.62">
      <stop offset="0" stop-color="{T['ciano']}" stop-opacity="0.45"/>
      <stop offset="1" stop-color="{T['ciano']}" stop-opacity="0"/>
    </radialGradient>
  </defs>
  <path d="{escudo}" fill="url(#{idr}g)" stroke="{T['roxo_som']}" stroke-width="3.5"/>
  <path d="{escudo}" fill="url(#{idr}gl)"/>
  <path d="M16 18 L50 9 L84 18" stroke="#cdbcff" stroke-width="2" opacity="0.55"/>
  <circle cx="50" cy="56" r="30" fill="none" stroke="{accent}" stroke-width="3.5"/>
  <circle cx="50" cy="56" r="30" fill="none" stroke="#fff" stroke-width="1" opacity="0.35"/>
  <g stroke="#eaf7ff" stroke-width="5.2" stroke-linecap="round" stroke-linejoin="round">
    <polyline points="40,44 28,56 40,68"/>
    <line x1="56" y1="40" x2="46" y2="72"/>
    <polyline points="62,44 74,56 62,68"/>
  </g>
</svg>'''


# Ícones de tipo de fase (24×24, currentColor)
_ICONES = {
    "historia":    '<path d="M12 7c-2-2-5-2-8-1v13c3-1 6-1 8 1 2-2 5-2 8-1V6c-3-1-6-1-8 1z"/><path d="M12 7v13"/>',
    "licao":       '<path d="M14.5 4H20v5.5"/><path d="M20 4 8.5 15.5"/><path d="m6.4 13.6 4 4"/><path d="M9 16l-5 5"/>',
    "secundaria":  '<circle cx="8" cy="8" r="3.6"/><path d="M10.6 10.6 19 19"/><path d="m15.4 15.4 2-2"/><path d="m18 18 1.8-1.8"/>',
    "chefe":       '<path d="M12 3c.8 3.6 4.2 5 3.3 9.2A4 4 0 1 1 8 12.2c0-1.6 1-2.6 1.6-3.2.4 1.8 1.8 2 2.2.8C12.4 7 10.8 5.4 12 3z"/>',
    "chefe_final": '<path d="M4 18h16"/><path d="M5 18 3.5 7l5 4.5L12 5l3.5 6.5 5-4.5L19 18"/>',
}


def icone_tipo(tipo: str, tam: int = 13) -> str:
    d = _ICONES.get(tipo, _ICONES["licao"])
    return (f'<svg class="ic" width="{tam}" height="{tam}" viewBox="0 0 24 24" fill="none" '
            f'stroke="currentColor" stroke-width="2" stroke-linecap="round" '
            f'stroke-linejoin="round">{d}</svg>')


def _canto(accent: str) -> str:
    """Filete ornamental de canto (vai nos 4 cantos da moldura via rotação)."""
    return (f'<svg class="canto" width="46" height="46" viewBox="0 0 46 46" fill="none">'
            f'<path d="M2 22 V6 Q2 2 6 2 H22" stroke="{T["ouro"]}" stroke-width="1.4"/>'
            f'<path d="M6 26 V10 Q6 6 10 6 H26" stroke="{accent}" stroke-width="1" opacity="0.7"/>'
            f'<path d="M2 2 l10 0 M2 2 l0 10" stroke="{T["ouro_esc"]}" stroke-width="2"/>'
            f'<circle cx="9" cy="9" r="2" fill="{accent}"/></svg>')


# Logo OFICIAL do jogo (asset ilustrado) — usada nos documentos para casar com o jogo.
LOGOS = {
    "header": os.path.join(RAIZ, "public", "img", "ui", "logos", "logo-header.png"),
    "full":   os.path.join(RAIZ, "public", "img", "ui", "logos", "logo-marca-ilustrado.png"),
}


def logo_img(variante: str = "header", classe: str = "") -> str:
    """<img> da logo oficial do jogo (vetor cairia em emblema_svg como fallback)."""
    src = LOGOS.get(variante, LOGOS["header"])
    cls = ("logo-jogo logo-" + variante + (" " + classe if classe else "")).strip()
    if not os.path.isfile(src):
        return emblema_svg(28, idr="fb" + variante)  # fallback: emblema vetorial
    return f'<img class="{cls}" src="file://{src}" alt="Algorithmia">'


def cabecalho(doc_label: str = "Fases & Tópicos") -> str:
    return f'''<div class="faixa-topo">
      {logo_img("header")}
      <div class="doc-label">{doc_label}</div>
    </div>'''


def rodape(pagina: str = "") -> str:
    runa = f'<span class="runa">&lt; / &gt;</span>'
    return f'''<div class="faixa-base">
      <span class="rod-esq">A Lenda dos Cinco Mestres</span>
      {runa}
      <span class="rod-dir">{pagina}</span>
    </div>'''


def moldura(accent: str) -> str:
    cantos = "".join(f'<div class="c c{i}">{_canto(accent)}</div>' for i in range(4))
    return f'<div class="moldura">{cantos}</div>'


def css_marca(fontes: dict) -> str:
    """CSS-base da identidade visual, comum a todos os documentos."""
    return f"""
    {_font_face(fontes)}
    :root {{
      --bg:{T['bg']}; --bg2:{T['bg2']}; --painel:{T['painel']}; --borda:{T['borda']};
      --primaria:{T['primaria']}; --primaria2:{T['primaria2']}; --ouro:{T['ouro']};
      --ouro-esc:{T['ouro_esc']}; --ciano:{T['ciano']}; --texto:{T['texto']};
      --texto-fraco:{T['texto_fraco']}; --papel:{T['papel']}; --papel2:{T['papel2'] if 'papel2' in T else T['papel_2']};
      --tinta:{T['tinta']};
    }}
    * {{ box-sizing: border-box; }}
    @page {{ size: A4; margin: 0; }}
    html, body {{ margin: 0; padding: 0; }}
    body {{ font-family:'Rubik','Segoe UI',system-ui,-apple-system,sans-serif; color:var(--tinta);
           -webkit-print-color-adjust:exact; print-color-adjust:exact; }}
    .epico {{ font-family:'Cinzel','Hoefler Text','Didot',serif; }}
    .wordmark {{ font-family:'Pixelify Sans','Rubik',monospace; }}

    /* Folha A4 full-bleed — garante 1 conteúdo por folha */
    .pagina {{ width:210mm; height:297mm; page-break-after:always; position:relative;
              overflow:hidden; display:flex; flex-direction:column;
              background:
                radial-gradient(120% 80% at 50% -10%, #fbfaf6 0%, var(--papel) 46%, var(--papel2) 100%); }}
    .pagina:last-child {{ page-break-after:auto; }}

    /* Faixa superior (navy) com emblema + wordmark */
    .faixa-topo {{ height:18mm; flex:0 0 18mm; display:flex; align-items:center;
      justify-content:space-between; padding:0 12mm;
      background:linear-gradient(100deg,#0c0d22 0%,#1a1640 55%,#0c0d22 100%);
      border-bottom:2.5px solid var(--ouro); color:#fff; }}
    .faixa-topo .marca {{ display:flex; align-items:center; gap:8px; }}
    .logo-jogo {{ display:block; }}
    .faixa-topo .logo-jogo {{ height:11mm; width:auto;
      filter:drop-shadow(0 1px 4px rgba(0,0,0,.4)); }}
    .wordmark {{ font-size:19px; font-weight:700; letter-spacing:1.5px;
      background:linear-gradient(90deg,#b9a7ff 0%,#ffe9a8 100%);
      -webkit-background-clip:text; background-clip:text; color:transparent; }}
    .doc-label {{ font-family:'Cinzel',serif; font-size:11px; letter-spacing:3.5px;
      text-transform:uppercase; color:var(--ouro); }}

    /* Faixa inferior */
    .faixa-base {{ height:11mm; flex:0 0 11mm; display:flex; align-items:center;
      justify-content:space-between; padding:0 14mm;
      background:linear-gradient(100deg,#0c0d22,#1a1640,#0c0d22);
      border-top:2px solid var(--ouro); color:#cdbcff; font-size:9px; letter-spacing:1px; }}
    .faixa-base .runa {{ font-family:'JetBrains Mono',monospace; color:var(--ouro);
      letter-spacing:2px; border:1px solid rgba(255,206,71,.5); border-radius:5px; padding:2px 8px; }}
    .rod-dir {{ font-family:'Cinzel',serif; letter-spacing:2px; color:#e7dcff; }}

    /* Miolo + moldura ornamental */
    .miolo {{ flex:1 1 auto; position:relative; padding:9mm 13mm 6mm; min-height:0; }}
    .moldura {{ position:absolute; inset:4mm; border:1.5px solid var(--ouro);
      border-radius:3px; pointer-events:none; }}
    .moldura::before {{ content:''; position:absolute; inset:1.5mm; border:1px solid rgba(124,92,255,.28);
      border-radius:2px; }}
    .moldura .c {{ position:absolute; }}
    .moldura .c0 {{ top:-2px; left:-2px; }}
    .moldura .c1 {{ top:-2px; right:-2px; transform:scaleX(-1); }}
    .moldura .c2 {{ bottom:-2px; left:-2px; transform:scaleY(-1); }}
    .moldura .c3 {{ bottom:-2px; right:-2px; transform:scale(-1,-1); }}
    .miolo > .corpo {{ position:relative; height:100%; display:flex; flex-direction:column; padding:5mm 6mm; }}

    /* Selo de runa de fundo (marca d'água) */
    .watermark {{ position:absolute; right:8mm; bottom:10mm; opacity:.05; pointer-events:none; }}
    """


if __name__ == "__main__":
    # Exporta o emblema vetorial como asset de marca reutilizável do jogo.
    os.makedirs(OUT_LOGO, exist_ok=True)
    garantir_fontes()
    with open(os.path.join(OUT_LOGO, "emblema-algorithmia.svg"), "w", encoding="utf-8") as fp:
        fp.write(emblema_svg(256))
    print("ok  public/img/ui/logos/emblema-algorithmia.svg")
