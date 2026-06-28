#!/usr/bin/env python3
"""
Gera uma GALERIA VISUAL única (galeria.html na raiz) com todos os assets de
imagem do jogo, organizados por categoria, com menu de navegação. Abre offline
por duplo-clique (caminhos relativos).

Uso:
  python3 tools/preview/gerar-galeria-imagens.py

Reescaneia as pastas a cada execução — regere quando a arte mudar.
"""
import os
import html

RAIZ = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
SAIDA = os.path.join(RAIZ, "galeria.html")
EXTS = (".png", ".webp", ".jpg", ".jpeg", ".svg")
PREF = [".png", ".webp", ".jpg", ".jpeg", ".svg"]  # ordem de preferência ao deduplicar

# (id, emoji, título, descrição, [pastas], recursivo, filtro-substring|None)
SECOES = [
    ("marca", "🪙", "Marca & Logos", "A identidade visual do jogo.",
     ["public/img/ui/logos", "public/img/ui"], False, None),
    ("mestres", "⚔️", "Os Cinco Mestres", "Os professores que guardam cada região.",
     ["public/img/mestres"], False, None),
    ("herois", "🧝", "Heróis & Classes", "As 6 classes jogáveis (cartas, sprites e HUD).",
     ["public/img/herois"], False, None),
    ("bestiario", "👹", "Bestiário — Monstros", "Cada inimigo é um erro de código corrompido.",
     ["public/img/inimigos"], False, None),
    ("itens", "💍", "Itens", "Armas, escudos, acessórios e poções.",
     ["public/img/itens"], False, None),
    ("cenarios", "🏞️", "Cenários & Fundos", "Os biomas e telas do reino.",
     ["public/img/fundos"], False, None),
    ("mapa", "🗺️", "Mapa — Regiões & Fases", "Emblemas das regiões e ícones das 35 fases.",
     ["public/img/mapas"], False, None),
    ("atores", "🎭", "Atores de Diálogo", "Retratos usados nas cenas de história.",
     ["public/img/atores"], False, None),
    ("ui", "🧩", "UI — Ícones, Botões, Molduras, Troféus", "Peças da interface.",
     ["public/img/ui/icones", "public/img/ui/botoes", "public/img/ui/molduras", "public/img/ui/trofeus"], False, None),
    ("cinematicas", "🎬", "Cinemáticas — Keyframes", "Os quadros-fonte das 7 intros narradas.",
     ["docs/cinematics"], True, "/keyframes/"),
    ("evolucao", "👾", "Evolução — v1 Pixel Art", "A arte antiga (pixel art). O v2 atual está nas seções acima. Histórico completo em docs/evolucao-visual/.",
     ["docs/evolucao-visual/v1-pixel-art"], True, None),
]


def coletar(pastas, recursivo, filtro):
    """Junta imagens das pastas, deduplica por (dir, nome-base) preferindo PNG."""
    achados = {}  # (dir, stem) -> caminho relativo escolhido
    for pasta in pastas:
        base = os.path.join(RAIZ, pasta)
        if not os.path.isdir(base):
            continue
        if recursivo:
            it = (os.path.join(r, f) for r, _, fs in os.walk(base) for f in fs)
        else:
            it = (os.path.join(base, f) for f in os.listdir(base))
        for full in it:
            if not os.path.isfile(full):
                continue
            # ignora pastas de referência/contact-sheet (começam com "_")
            rel = os.path.relpath(full, RAIZ)
            if any(p.startswith("_") for p in rel.split(os.sep)):
                continue
            ext = os.path.splitext(full)[1].lower()
            if ext not in EXTS:
                continue
            if filtro and filtro not in "/" + rel.replace(os.sep, "/"):
                continue
            stem = os.path.splitext(os.path.basename(full))[0]
            chave = (os.path.dirname(rel), stem)
            atual = achados.get(chave)
            if atual is None or PREF.index(ext) < PREF.index(os.path.splitext(atual)[1].lower()):
                achados[chave] = rel
    return sorted(achados.values(), key=lambda p: p.lower())


def legenda(rel, secao_pastas):
    """Nome amigável: caminho relativo à pasta-raiz da seção, sem extensão."""
    nome = os.path.splitext(rel)[0]
    for p in secao_pastas:
        pref = p + "/"
        if (nome + "/").startswith(pref):
            nome = nome[len(pref):]
            break
    return html.escape(nome)


CSS = """
:root{--bg:#0a0c1a;--painel:#12152c;--painel2:#171a36;--borda:#2a335f;--texto:#eef0fb;
  --fraco:#9aa0c9;--roxo:#7c5cff;--roxo2:#9d83ff;--ouro:#ffce47;--ciano:#8ce6ff;
  --titulo:'Cinzel',Georgia,serif;--fonte:'Rubik',system-ui,sans-serif}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--texto);font-family:var(--fonte);line-height:1.5}
img{image-rendering:auto}
a{color:var(--ciano);text-decoration:none}
.topo{position:sticky;top:0;z-index:10;background:rgba(8,10,26,.92);backdrop-filter:blur(8px);
  border-bottom:1px solid var(--borda);padding:14px 20px}
.topo .marca{display:flex;align-items:center;gap:14px;flex-wrap:wrap;justify-content:space-between;max-width:1280px;margin:0 auto}
.topo img.logo{height:54px;width:auto;filter:drop-shadow(0 0 14px rgba(124,92,255,.5))}
.topo .tit{font-family:var(--titulo);font-size:1.15rem;color:#fff}
.topo .tot{color:var(--fraco);font-size:.82rem}
.nav{display:flex;gap:8px;flex-wrap:wrap;max-width:1280px;margin:12px auto 0}
.nav a{font-size:.8rem;padding:5px 11px;border-radius:999px;background:var(--painel);
  border:1px solid var(--borda);color:var(--texto)}
.nav a:hover{border-color:var(--roxo);color:#fff}
.wrap{max-width:1280px;margin:0 auto;padding:20px}
section{padding:26px 0;border-top:1px solid rgba(42,51,95,.5);scroll-margin-top:120px}
.sec-tit{display:flex;align-items:baseline;gap:10px;flex-wrap:wrap}
.sec-tit h2{font-family:var(--titulo);font-size:1.5rem;margin:0;color:#fff}
.sec-tit .cont{color:var(--ouro);font-size:.85rem;font-weight:700}
.sec-sub{color:var(--fraco);margin:.2rem 0 16px;font-size:.9rem}
.grade{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px}
figure{margin:0;background:var(--painel2);border:1px solid var(--borda);border-radius:12px;
  overflow:hidden;transition:.15s}
figure:hover{border-color:var(--roxo);transform:translateY(-2px);box-shadow:0 8px 22px rgba(0,0,0,.45)}
figure .tile{height:150px;display:flex;align-items:center;justify-content:center;
  background:radial-gradient(circle at 50% 30%,#15183200,#0c0d1c);padding:8px}
figure img{max-width:100%;max-height:100%;object-fit:contain;display:block}
figcaption{padding:7px 9px;font-size:.72rem;color:var(--fraco);border-top:1px solid var(--borda);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.hero{padding:40px 20px;text-align:center;background:linear-gradient(180deg,rgba(124,92,255,.10),transparent)}
.hero h1{font-family:var(--titulo);font-size:2rem;margin:.2rem 0;color:#fff}
.hero p{color:var(--fraco);margin:.2rem 0 0}
.hero .cta{display:inline-block;margin-top:14px;padding:9px 16px;border-radius:999px;
  background:color-mix(in srgb,var(--roxo) 26%,transparent);border:1px solid var(--roxo);color:#fff}
footer{text-align:center;color:var(--fraco);padding:30px;border-top:1px solid var(--borda);font-size:.82rem}
"""


def main():
    grupos = []
    total = 0
    for sid, emoji, titulo, desc, pastas, rec, filt in SECOES:
        imgs = coletar(pastas, rec, filt)
        if not imgs:
            continue
        total += len(imgs)
        grupos.append((sid, emoji, titulo, desc, pastas, imgs))

    out = []
    out.append("<!doctype html>")
    out.append("<!-- GERADO por tools/preview/gerar-galeria-imagens.py. NÃO edite à mão: rode o script. -->")
    out.append('<html lang="pt-br"><head><meta charset="utf-8">')
    out.append('<meta name="viewport" content="width=device-width,initial-scale=1">')
    out.append("<title>Galeria Visual — Algorithmia</title>")
    out.append('<link rel="preconnect" href="https://fonts.googleapis.com">')
    out.append('<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Rubik:wght@400;500;700&display=swap">')
    out.append("<style>" + CSS + "</style></head><body>")

    # topo fixo com menu
    out.append('<div class="topo"><div class="marca">')
    out.append('<div style="display:flex;align-items:center;gap:14px">'
               '<img class="logo" src="public/img/ui/logos/logo-header.png" alt="Algorithmia">'
               '<div><div class="tit">Galeria Visual</div>'
               f'<div class="tot">{total} imagens · {len(grupos)} categorias</div></div></div>')
    out.append('</div><div class="nav">')
    for sid, emoji, titulo, *_ in grupos:
        out.append(f'<a href="#{sid}">{emoji} {html.escape(titulo)}</a>')
    out.append("</div></div>")

    # hero
    out.append('<div class="hero"><h1>🎨 Códex Visual de Algorithmia</h1>'
               "<p>Toda a arte do jogo em um só lugar — mestres, monstros, itens, cenários e a evolução visual.</p>"
               '<a class="cta" href="app/views/historia/historia.html">📖 Ler a História ilustrada →</a></div>')

    out.append('<div class="wrap">')
    for sid, emoji, titulo, desc, pastas, imgs in grupos:
        out.append(f'<section id="{sid}"><div class="sec-tit"><h2>{emoji} {html.escape(titulo)}</h2>'
                   f'<span class="cont">{len(imgs)} itens</span></div>')
        out.append(f'<p class="sec-sub">{html.escape(desc)}</p>')
        out.append('<div class="grade">')
        for rel in imgs:
            src = html.escape(rel.replace(os.sep, "/"))
            cap = legenda(rel.replace(os.sep, "/"), pastas)
            out.append(f'<figure><div class="tile"><img loading="lazy" src="{src}" alt="{cap}"></div>'
                       f'<figcaption title="{cap}">{cap}</figcaption></figure>')
        out.append("</div></section>")
    out.append("</div>")

    out.append('<footer>Gerado por <code>tools/preview/gerar-galeria-imagens.py</code> · '
               "Algorithmia — A Lenda dos Cinco Mestres</footer>")
    out.append("</body></html>")

    open(SAIDA, "w", encoding="utf-8").write("\n".join(out))
    print(f"✓ galeria.html gerada na raiz · {total} imagens em {len(grupos)} categorias")
    for sid, emoji, titulo, desc, pastas, imgs in grupos:
        print(f"   {len(imgs):4}  {titulo}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
