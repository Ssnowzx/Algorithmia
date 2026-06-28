#!/usr/bin/env python3
"""
Gera uma GALERIA VISUAL única (galeria.html na raiz) com todos os assets de
imagem do jogo, organizados por categoria, com menu de navegação. Abre offline
por duplo-clique (caminhos relativos). Inclui botões de vídeo por região
(cinemáticas) e links para os PDFs do projeto.

Uso:
  python3 tools/preview/gerar-galeria-imagens.py

Reescaneia as pastas a cada execução — regere quando a arte mudar.
"""
import os
import html

RAIZ = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
SAIDA = os.path.join(RAIZ, "galeria.html")
EXTS = (".png", ".webp", ".jpg", ".jpeg", ".svg")
PREF = [".png", ".webp", ".jpg", ".jpeg", ".svg"]  # preferência ao deduplicar

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
    ("cinematicas", "🎬", "Cinemáticas — Intros das Fases", "Keyframes das 7 intros — clique em ▶ para assistir o vídeo.",
     ["docs/cinematics"], True, "/keyframes/"),
    ("evolucao", "👾", "Evolução — v1 Pixel Art", "A arte antiga (pixel art). O v2 atual está nas seções acima. Histórico completo em docs/evolucao-visual/.",
     ["docs/evolucao-visual/v1-pixel-art"], True, None),
]

# Cinemáticas: slug -> nome da região (ordem canônica da jornada)
REGIOES_CINE = [
    ("terras-hello-world", "🌅 Terras de Hello World"),
    ("willen", "⚓ Porto da Sintaxe — Willen"),
    ("clayton", "🏛️ Cidadela dos Objetos — Clayton"),
    ("marcelo", "🌲 Floresta das Estruturas — Marcelo"),
    ("cesar", "⛰️ Montanha do Cálculo — César"),
    ("cassandro", "🗼 Torre das Conexões — Cassandro"),
    ("abismo-devnull", "🕳️ O Abismo — Lorde Segfault"),
]

# PDFs do projeto (só entram os que existirem no disco)
PDFS = [
    ("📘 Códex dos Mestres (PDF)", "docs/Fases-e-Topicos-por-Mestre.pdf"),
    ("📗 Evolução Visual (PDF)", "docs/evolucao-visual/Evolucao-Visual.pdf"),
]


def coletar(pastas, recursivo, filtro):
    """Junta imagens das pastas, deduplica por (dir, nome-base) preferindo PNG."""
    achados = {}
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
            rel = os.path.relpath(full, RAIZ)
            if any(p.startswith("_") for p in rel.split(os.sep)):
                continue  # ignora _referencia-fotos / _contact-sheets
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
    nome = os.path.splitext(rel.replace(os.sep, "/"))[0]
    for p in secao_pastas:
        pref = p + "/"
        if (nome + "/").startswith(pref):
            nome = nome[len(pref):]
            break
    return html.escape(nome)


def card(rel, pastas):
    src = html.escape(rel.replace(os.sep, "/"))
    cap = legenda(rel, pastas)
    return (f'<figure><div class="tile"><img loading="lazy" src="{src}" alt="{cap}"></div>'
            f'<figcaption title="{cap}">{cap}</figcaption></figure>')


def slug_keyframe(rel):
    parts = rel.replace(os.sep, "/").split("/")
    i = parts.index("cinematics")
    return parts[i + 1] if i + 1 < len(parts) else ""


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
figure{margin:0;background:var(--painel2);border:1px solid var(--borda);border-radius:12px;overflow:hidden;transition:.15s}
figure:hover{border-color:var(--roxo);transform:translateY(-2px);box-shadow:0 8px 22px rgba(0,0,0,.45)}
figure .tile{height:150px;display:flex;align-items:center;justify-content:center;background:radial-gradient(circle at 50% 30%,#15183200,#0c0d1c);padding:8px}
figure img{max-width:100%;max-height:100%;object-fit:contain;display:block}
figcaption{padding:7px 9px;font-size:.72rem;color:var(--fraco);border-top:1px solid var(--borda);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.hero{padding:40px 20px;text-align:center;background:linear-gradient(180deg,rgba(124,92,255,.10),transparent)}
.hero h1{font-family:var(--titulo);font-size:2rem;margin:.2rem 0;color:#fff}
.hero p{color:var(--fraco);margin:.2rem 0 0}
.acoes{display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin-top:16px}
.acoes a{padding:9px 16px;border-radius:999px;background:color-mix(in srgb,var(--roxo) 22%,transparent);
  border:1px solid var(--roxo);color:#fff;font-size:.9rem}
.acoes a.pdf{background:color-mix(in srgb,var(--ouro) 16%,transparent);border-color:var(--ouro);color:#ffe9a8}
.acoes a:hover{filter:brightness(1.15)}
/* Cinemáticas por região */
.cine-reg{margin:0 0 22px;background:rgba(18,21,44,.5);border:1px solid var(--borda);border-radius:14px;padding:14px 16px}
.cine-reg-top{display:flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:space-between;margin-bottom:12px}
.cine-reg-top h3{font-family:var(--titulo);font-size:1.1rem;margin:0;color:#fff}
.btnvid{display:inline-flex;align-items:center;gap:7px;padding:7px 14px;border-radius:999px;
  background:radial-gradient(circle at 35% 30%,#ffe79a,var(--ouro));color:#0b0c1d;font-weight:700;
  font-size:.85rem;border:1px solid color-mix(in srgb,var(--roxo) 50%,#0b0c1d)}
.btnvid:hover{filter:brightness(1.08);transform:translateY(-1px)}
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

    pdfs = [(lbl, p) for (lbl, p) in PDFS if os.path.isfile(os.path.join(RAIZ, p))]

    o = []
    o.append("<!doctype html>")
    o.append("<!-- GERADO por tools/preview/gerar-galeria-imagens.py. NÃO edite à mão: rode o script. -->")
    o.append('<html lang="pt-br"><head><meta charset="utf-8">')
    o.append('<meta name="viewport" content="width=device-width,initial-scale=1">')
    o.append("<title>Galeria Visual — Algorithmia</title>")
    o.append('<link rel="preconnect" href="https://fonts.googleapis.com">')
    o.append('<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Rubik:wght@400;500;700&display=swap">')
    o.append("<style>" + CSS + "</style></head><body>")

    # topo fixo + menu
    o.append('<div class="topo"><div class="marca">')
    o.append('<div style="display:flex;align-items:center;gap:14px">'
             '<img class="logo" src="public/img/ui/logos/logo-header.png" alt="Algorithmia">'
             '<div><div class="tit">Galeria Visual</div>'
             f'<div class="tot">{total} imagens · {len(grupos)} categorias</div></div></div>')
    o.append("</div><div class=\"nav\">")
    for sid, emoji, titulo, *_ in grupos:
        o.append(f'<a href="#{sid}">{emoji} {html.escape(titulo)}</a>')
    o.append("</div></div>")

    # hero + ações (história + PDFs)
    o.append('<div class="hero"><h1>🎨 Códex Visual de Algorithmia</h1>'
             "<p>Toda a arte do jogo em um só lugar — mestres, monstros, itens, cenários e a evolução visual.</p>")
    o.append('<div class="acoes"><a href="app/views/historia/historia.html">📖 Ler a História ilustrada</a>')
    for lbl, p in pdfs:
        o.append(f'<a class="pdf" href="{html.escape(p)}" target="_blank">{html.escape(lbl)}</a>')
    o.append("</div></div>")

    o.append('<div class="wrap">')
    for sid, emoji, titulo, desc, pastas, imgs in grupos:
        o.append(f'<section id="{sid}"><div class="sec-tit"><h2>{emoji} {html.escape(titulo)}</h2>'
                 f'<span class="cont">{len(imgs)} itens</span></div>')
        o.append(f'<p class="sec-sub">{html.escape(desc)}</p>')

        if sid == "cinematicas":
            # agrupa keyframes por região e adiciona botão de vídeo
            porslug = {}
            for rel in imgs:
                porslug.setdefault(slug_keyframe(rel), []).append(rel)
            ordem = [s for s, _ in REGIOES_CINE] + [s for s in porslug if s not in dict(REGIOES_CINE)]
            nomes = dict(REGIOES_CINE)
            for slug in ordem:
                if slug not in porslug:
                    continue
                nome = nomes.get(slug, slug)
                mp4 = f"public/video/cinematics/{slug}.mp4"
                tem_video = os.path.isfile(os.path.join(RAIZ, mp4))
                o.append('<div class="cine-reg"><div class="cine-reg-top">'
                         f'<h3>{html.escape(nome)}</h3>')
                if tem_video:
                    o.append(f'<a class="btnvid" href="{mp4}" target="_blank">▶ Assistir o vídeo</a>')
                o.append("</div><div class=\"grade\">")
                for rel in porslug[slug]:
                    o.append(card(rel, pastas))
                o.append("</div></div>")
        else:
            o.append('<div class="grade">')
            for rel in imgs:
                o.append(card(rel, pastas))
            o.append("</div>")
        o.append("</section>")
    o.append("</div>")

    o.append('<footer>Gerado por <code>tools/preview/gerar-galeria-imagens.py</code> · '
             "Algorithmia — A Lenda dos Cinco Mestres</footer>")
    o.append("</body></html>")

    open(SAIDA, "w", encoding="utf-8").write("\n".join(o))
    print(f"✓ galeria.html · {total} imagens · {len(grupos)} categorias · {len(pdfs)} PDFs")
    for sid, emoji, titulo, desc, pastas, imgs in grupos:
        print(f"   {len(imgs):4}  {titulo}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
