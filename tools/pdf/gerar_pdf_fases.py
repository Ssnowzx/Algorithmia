#!/usr/bin/env python3
"""
Códex dos Mestres — PDF de FASES e TÓPICOS de cada mestre, com a arte (card)
de cada um e a identidade visual do Algorithmia (tools/marca_pdf.py).

Layout full-bleed: cada mestre ocupa EXATAMENTE uma folha A4 (sem quebras).

Fonte dos dados: database/seeds.sql (fases) + plano do banco de questões.
Saída: docs/Fases-e-Topicos-por-Mestre.pdf

Uso:  python3 tools/gerar_pdf_fases.py   (requer Google Chrome headless)
"""

import os
import subprocess
import sys
import tempfile

import marca_pdf as marca

RAIZ = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
IMG = os.path.join(RAIZ, "public", "img")
SAIDA_HTML = os.path.join(tempfile.gettempdir(), "_codex-mestres.html")
SAIDA_PDF = os.path.join(RAIZ, "docs", "Fases-e-Topicos-por-Mestre.pdf")
CHROME = "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"

TIPOS = {
    "historia":    "História",
    "licao":       "Lição",
    "secundaria":  "Secundária",
    "chefe":       "Chefe",
    "chefe_final": "Chefe Final",
}

# Capítulos: cada um = uma folha. (nome, tipo, [tópicos], sorteio)
CAPITULOS = [
    {
        "titulo": "Vila Hello World", "subtitulo": "Capítulo 0 — O Despertar do Aprendiz",
        "imagem": "atores/npc-anciao.png", "cor": "#8d6e63",
        "disciplina": "Lógica e Algoritmos (fundamentos)", "pdf": "Tutorial introdutório do reino",
        "fases": [
            ("Prólogo: O Despertar", "historia", ["Abertura: amnésia e o fragmento brilhante"], ""),
            ("Os Primeiros Passos", "licao",
             ["Precedência de operadores", "Operadores aritméticos/relacionais",
              "Condições verdadeiro/falso", "Laços (repetição)", "Incremento"], "4 de ~10"),
            ("O Bug Primordial", "chefe",
             ["Contagem de iterações", "Laço infinito / depuração", "Ordenar passos",
              "Conceito de depurar (debug)", "Pseudocódigo"], "5 de ~12"),
        ],
    },
    {
        "titulo": "Willen, o Arquiteto", "subtitulo": "Porto da Sintaxe",
        "imagem": "mestres/mestre-willen.png", "cor": "#5b8cff",
        "disciplina": "Laboratório de Programação II · PHP / MVC / SQL", "pdf": "Laboratório de Programação II",
        "fases": [
            ("Chegada ao Porto", "historia", ["Willen recebe o aprendiz nas docas de código"], ""),
            ("Variáveis e Eco", "licao",
             ["Declarar variáveis ($)", "echo / print", "Tipos de dados", "Concatenação (.)",
              "Interpolação", "var_dump"], "4 de ~12"),
            ("Estruturas de Controle", "licao",
             ["if / else / elseif", "switch / case", "for · while · foreach",
              "Operadores lógicos", "break / continue"], "4 de ~12"),
            ("O Padrão MVC", "licao",
             ["Model · View · Controller", "Responsabilidade de cada camada", "Fluxo de requisição",
              "Roteamento", "ORM (mapeamento O-R)"], "4 de ~12"),
            ("O Baú do SELECT", "secundaria",
             ["SELECT / FROM", "WHERE", "INSERT · UPDATE · DELETE", "ORDER BY", "JOIN", "COUNT"], "3 de ~9"),
            ("Parse Error, o Kraken", "chefe",
             ["Erros de sintaxe (;)", "function", "== vs ===", "Arrays", "Escopo", "Saída de código"], "5 de ~14"),
        ],
    },
    {
        "titulo": "Clayton, o Moldador", "subtitulo": "Cidadela dos Objetos",
        "imagem": "mestres/mestre-clayton.png", "cor": "#22a6b3",
        "disciplina": "Programação Orientada a Objetos · Java", "pdf": "Programação Orientada a Objetos",
        "fases": [
            ("A Cidadela dos Objetos", "historia", ["Tudo é classe e instância"], ""),
            ("Classes e Objetos", "licao",
             ["Classe × objeto", "Atributos e métodos", "new", "Construtor", "this", "static"], "4 de ~12"),
            ("Encapsulamento", "licao",
             ["public / private / protected", "Getters e setters", "Estado interno", "Pacotes"], "4 de ~12"),
            ("Herança vs Composição", "licao",
             ["extends / super", "Polimorfismo / @Override", "Composição × herança", "Reúso"], "4 de ~12"),
            ("Interfaces Secretas", "secundaria",
             ["interface / implements", "Métodos abstract", "Contrato", "Múltiplas interfaces"], "3 de ~9"),
            ("A Gárgula God-Class", "chefe",
             ["SRP", "Coesão × God Class", "Generics <T>", "Lambda", "Coleções", "Exceções"], "5 de ~14"),
        ],
    },
    {
        "titulo": "Marcelo, o Andarilho", "subtitulo": "Floresta das Estruturas",
        "imagem": "mestres/mestre-marcelo.png", "cor": "#e1b12c",
        "disciplina": "Estrutura de Dados II", "pdf": "Estruturas de dados",
        "fases": [
            ("A Floresta das Estruturas", "historia", ["Marcelo guia no Gol quadrado"], ""),
            ("Pilhas e Filas", "licao",
             ["Pilha LIFO · Fila FIFO", "push / pop / peek", "enqueue / dequeue", "Aplicações"], "4 de ~12"),
            ("Listas e Nós", "licao",
             ["Estática × encadeada", "Nó (valor + ponteiro)", "Inserção / remoção",
              "Dupla / circular", "Custo"], "4 de ~12"),
            ("Árvores e Big-O", "licao",
             ["Árvore binária / BST", "Percursos", "Altura", "Busca binária O(log n)",
              "Big-O", "Ordenação"], "4 de ~12"),
            ("O Atalho do Gol", "secundaria",
             ["Busca linear × binária", "Vetor ordenado", "Crescimento logarítmico"], "3 de ~9"),
            ("A Hidra Recursiva", "chefe",
             ["Recursão / caso base", "Fatorial / Fibonacci", "Hash e colisão",
              "Bubble / Insertion sort", "Pilha de chamadas"], "5 de ~14"),
        ],
    },
    {
        "titulo": "Cesar, o Oráculo do Ritmo", "subtitulo": "Montanha do Cálculo",
        "imagem": "mestres/mestre-cesar.png", "cor": "#9c88ff",
        "disciplina": "Cálculo I + II (uma e múltiplas variáveis)",
        "pdf": "Cálculo (uma variável) + (múltiplas variáveis)",
        "fases": [
            ("A Montanha do Cálculo", "historia", ["Cesar ouve o ritmo dos números"], ""),
            ("Sequências e Ritmo", "licao",
             ["Conceito de limite", "Limites laterais", "Limites no infinito", "Continuidade"], "4 de ~12"),
            ("Recursão e Limites", "licao",
             ["Derivada como limite", "Regras (potência/produto/quociente)", "Regra da cadeia",
              "Taxa de variação"], "4 de ~12"),
            ("Complexidade e Crescimento", "licao",
             ["Primitiva / antiderivada", "Integral definida/indefinida", "Teorema Fundamental",
              "Regra da potência", "Área sob a curva"], "4 de ~12"),
            ("A Verdade sobre Zero", "historia", ["Quem é realmente o Lorde Segfault"], ""),
            ("Limite, o Colosso", "chefe",
             ["Funções de 2 variáveis", "Derivadas parciais (∂)", "Vetor gradiente",
              "Integrais duplas", "Máximos e mínimos"], "5 de ~14"),
        ],
    },
    {
        "titulo": "Cassandro, o Mensageiro", "subtitulo": "Torre das Conexões",
        "imagem": "mestres/mestre-cassandro.png", "cor": "#44bd32",
        "disciplina": "Redes de Computadores", "pdf": "Redes de computadores",
        "fases": [
            ("A Torre das Conexões", "historia", ["Sete andares, sete camadas"], ""),
            ("As Sete Camadas (OSI)", "licao",
             ["7 camadas OSI", "OSI × TCP/IP", "Função de cada camada", "Encapsulamento", "LAN/MAN/WAN"], "4 de ~12"),
            ("IP, DNS e Rotas", "licao",
             ["IPv4 e octetos", "Máscara / sub-rede", "Roteamento", "DNS", "Loopback", "Topologias"], "4 de ~12"),
            ("TCP, UDP e HTTP", "licao",
             ["TCP × UDP", "Portas (80/443/53)", "HTTP / HTTPS", "Status (200/404/500)", "Handshake"], "4 de ~12"),
            ("O Pacote Perdido", "secundaria",
             ["ACK", "Retransmissão", "Timeout", "Controle de fluxo"], "3 de ~9"),
            ("DDoS, o Enxame", "chefe",
             ["Ataque DDoS", "Firewall", "Criptografia", "Ataques comuns", "Segurança"], "5 de ~14"),
        ],
    },
    {
        "titulo": "O Abismo do /dev/null", "subtitulo": "Confronto Final",
        "imagem": "mapas/fase-lorde-segfault.png", "cor": "#b33939",
        "disciplina": "Mix de todas as matérias", "pdf": "Revisão geral das 5 disciplinas",
        "fases": [
            ("O Abismo do /dev/null", "historia", ["Márcio, o Lorde Segfault, aguarda no vazio"], ""),
            ("Márcio, o Lorde Segfault & a IA Ancestral", "chefe_final",
             ["MVC (camadas)", "POO (herança)", "Estruturas (busca binária)", "Cálculo (limites)",
              "Redes (TCP)", "Lógica (recursão)"], "6 de ~16"),
        ],
    },
]


def img_uri(rel: str) -> str:
    return "file://" + os.path.join(IMG, rel)


def render_sorteio(s: str) -> str:
    if not s:
        return '<span class="sorteio dim">—</span>'
    num, _, resto = s.partition(" ")
    return f'<span class="sorteio"><span class="num">{num}</span><span class="lbl">{resto}</span></span>'


def render_fase(fase) -> str:
    nome, tipo, topicos, sorteio = fase
    pills = "".join(f'<span class="t">{t}</span>' for t in topicos)
    badge = f'<span class="badge">{marca.icone_tipo(tipo)}{TIPOS[tipo]}</span>'
    return f"""<tr class="{tipo}">
        <td>{badge}</td>
        <td class="nome-f">{nome}</td>
        <td class="topicos">{pills}</td>
        <td class="sort">{render_sorteio(sorteio)}</td>
      </tr>"""


def render_pagina(cap, num, total) -> str:
    fases = "".join(render_fase(f) for f in cap["fases"])
    return f"""
    <div class="pagina" style="--accent:{cap['cor']}">
      {marca.cabecalho('Fases & Tópicos')}
      <div class="miolo">
        {marca.moldura(cap['cor'])}
        <div class="corpo">
          <div class="watermark">{marca.logo_img('full', 'wm')}</div>
          <header class="titulo-mestre">
            <h1 class="epico nome">{cap['titulo']}</h1>
            <div class="regiao epico">{cap['subtitulo']}</div>
            <div class="divisor"><span></span>◆<span></span></div>
            <div class="chips">
              <span class="chip"><b>Disciplina</b>{cap['disciplina']}</span>
              <span class="chip"><b>PDF-fonte</b>{cap['pdf']}</span>
            </div>
          </header>
          <div class="conteudo">
            <div class="card"><img src="{img_uri(cap['imagem'])}"></div>
            <table class="fases">
              <thead><tr><th>Tipo</th><th>Fase</th><th>Tópicos do banco de questões</th><th>Sorteio</th></tr></thead>
              <tbody>{fases}</tbody>
            </table>
          </div>
        </div>
      </div>
      {marca.rodape(f'{num} / {total}')}
    </div>"""


def render_capa() -> str:
    cores = [c["cor"] for c in CAPITULOS if c["imagem"].startswith("mestres/")]
    return f"""
    <div class="capa">
      <div class="moldura-capa"></div>
      <div class="logo-capa">{marca.logo_img('full')}</div>
      <div class="titulo-doc epico">A Lenda dos Cinco Mestres</div>
      <div class="sub">Guia de Fases &amp; Tópicos do Reino</div>
      <div class="constelacao">{constelacao_svg(cores)}</div>
      <div class="selo">4º Semestre · Ciência da Computação · UNIFACVEST Lages</div>
    </div>"""


def constelacao_svg(cores) -> str:
    """Fileira 'cinco mestres' em SVG puro (vetor): gemas nas cores + fios dourados.
    SVG evita box-shadow/transform-CSS, que alguns leitores de PDF (Preview/Quartz)
    renderizam como retângulos escuros."""
    dw, cy, h, sp, fio, gap = 8, 11, 22, 26, 54, 16
    xs = [6 + fio + gap + i * sp for i in range(len(cores))]
    rl = xs[-1] + gap
    w = rl + fio + 6
    gemas = "".join(
        f'<polygon points="{x},{cy-dw} {x+dw},{cy} {x},{cy+dw} {x-dw},{cy}" '
        f'fill="{cor}" stroke="#ffffff" stroke-opacity="0.6" stroke-width="1"/>'
        for x, cor in zip(xs, cores)
    )
    return (f'<svg width="{w}" height="{h}" viewBox="0 0 {w} {h}" xmlns="http://www.w3.org/2000/svg">'
            f'<line x1="6" y1="{cy}" x2="{6+fio}" y2="{cy}" stroke="#ffce47" stroke-opacity="0.5" stroke-width="1.3"/>'
            f'{gemas}'
            f'<line x1="{rl}" y1="{cy}" x2="{rl+fio}" y2="{cy}" stroke="#ffce47" stroke-opacity="0.5" stroke-width="1.3"/>'
            f'</svg>')


def css_doc() -> str:
    return """
    /* ── Capa ───────────────────────────────────────────── */
    .capa { width:210mm; height:297mm; page-break-after:always; position:relative; overflow:hidden;
      display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;
      background:radial-gradient(140% 100% at 50% 2%, #241c52 0%, #0e0f28 52%, #07081a 100%); color:#fff; }
    .moldura-capa { position:absolute; inset:9mm; border:1.5px solid rgba(255,206,71,.55); border-radius:5px; }
    .moldura-capa::before { content:''; position:absolute; inset:3.5mm; border:1px solid rgba(124,92,255,.45); border-radius:4px; }
    .logo-capa img { width:140mm; height:auto; display:block;
      filter:drop-shadow(0 10px 38px rgba(124,92,255,.55)); }
    .capa .titulo-doc { font-size:27px; letter-spacing:5px; color:var(--ouro); margin-top:24px;
      white-space:nowrap; }
    .watermark img { width:82mm; height:auto; }
    .capa .sub { font-family:'Cinzel',serif; font-size:12.5px; letter-spacing:4px; color:#cfd2ff;
      margin-top:10px; text-transform:uppercase; }
    .capa .constelacao { display:flex; justify-content:center; margin-top:28px; }
    .capa .constelacao svg { width:78mm; height:auto; }
    .capa .selo { position:absolute; bottom:16mm; font-family:'Cinzel',serif; font-size:10.5px;
      letter-spacing:2.5px; color:#9499c0; }

    /* ── Título do mestre ───────────────────────────────── */
    .titulo-mestre { text-align:center; flex:0 0 auto; }
    .titulo-mestre .nome { font-size:29px; color:var(--accent); margin:0; letter-spacing:.5px; }
    .titulo-mestre .regiao { font-size:10px; letter-spacing:3.5px; color:var(--ouro-esc);
      text-transform:uppercase; margin-top:3px; }
    .divisor { display:flex; align-items:center; justify-content:center; gap:9px; color:var(--accent);
      margin:6px 0 8px; font-size:10px; }
    .divisor span { height:1px; width:78px; background:linear-gradient(90deg,transparent,var(--ouro),transparent); }
    .chips { display:flex; justify-content:center; gap:9px; flex-wrap:wrap; }
    .chip { font-size:9.5px; background:#fff; border:1px solid #e6e0d2; border-radius:999px;
      padding:3px 12px; color:#4a4660; }
    .chip b { color:var(--accent); text-transform:uppercase; letter-spacing:.5px; font-size:7.5px; margin-right:5px; }

    /* ── Conteúdo: card + tabela ────────────────────────── */
    .conteudo { display:grid; grid-template-columns:46mm 1fr; gap:7mm; align-items:start;
      margin-top:6mm; flex:1 1 auto; min-height:0; }
    .card img { width:46mm; border-radius:7px; display:block; border:3px solid var(--accent);
      outline:1px solid var(--ouro); outline-offset:2px; filter:drop-shadow(0 6px 14px rgba(20,16,40,.45)); }

    table.fases { width:100%; border-collapse:separate; border-spacing:0 4px; font-size:10.5px; }
    table.fases thead th { font-family:'Cinzel',serif; font-size:8px; letter-spacing:1.3px;
      text-transform:uppercase; color:#fff; background:var(--accent); padding:6px 9px; text-align:left; }
    table.fases thead th:first-child { border-radius:6px 0 0 6px; }
    table.fases thead th:last-child { border-radius:0 6px 6px 0; text-align:center; }
    table.fases tbody td { background:#fffdf8; border-top:1px solid #ece6d8; border-bottom:1px solid #ece6d8;
      padding:6px 9px; vertical-align:middle; }
    table.fases tbody td:first-child { border-left:1px solid #ece6d8; border-radius:6px 0 0 6px; }
    table.fases tbody td:last-child { border-right:1px solid #ece6d8; border-radius:0 6px 6px 0; }
    tr.historia td { background:#f4f1ea; color:#8a8597; font-style:italic; }
    .badge { display:inline-flex; align-items:center; gap:4px; white-space:nowrap; font-size:9px;
      font-weight:600; padding:3px 9px; border-radius:999px; color:var(--accent);
      background:color-mix(in srgb,var(--accent) 13%,#fff); border:1px solid color-mix(in srgb,var(--accent) 38%,#fff); }
    td.nome-f { font-weight:600; width:33mm; color:#2a2640; }
    .topicos .t { display:inline-block; background:#f3f0e8; border:1px solid #e4ddcb; color:#46415c;
      border-radius:5px; padding:1.5px 7px; margin:2px 3px 2px 0; font-size:9px; }
    td.sort { width:18mm; text-align:center; }
    .sorteio { display:inline-flex; flex-direction:column; align-items:center; line-height:1.05; }
    .sorteio .num { font-family:'Cinzel',serif; font-weight:700; color:var(--accent); font-size:13px; }
    .sorteio .lbl { font-size:7px; color:#9a93a8; letter-spacing:.3px; }
    .sorteio.dim { color:#ccc5b6; font-size:14px; }
    """


def build_html() -> str:
    fontes = marca.garantir_fontes()
    total = len(CAPITULOS)
    paginas = "".join(render_pagina(c, i + 1, total) for i, c in enumerate(CAPITULOS))
    return f"""<!DOCTYPE html><html lang="pt-BR"><head><meta charset="utf-8">
<style>{marca.css_marca(fontes)}{css_doc()}</style></head>
<body>{render_capa()}{paginas}</body></html>"""


def main():
    os.makedirs(os.path.dirname(SAIDA_PDF), exist_ok=True)
    with open(SAIDA_HTML, "w", encoding="utf-8") as fp:
        fp.write(build_html())
    print(f"HTML: {SAIDA_HTML}")
    if not os.path.exists(CHROME):
        sys.exit(f"Chrome não encontrado em {CHROME}.")
    subprocess.run(
        [CHROME, "--headless=new", "--disable-gpu", "--no-pdf-header-footer",
         "--virtual-time-budget=20000", f"--print-to-pdf={SAIDA_PDF}", "file://" + SAIDA_HTML],
        check=True, capture_output=True,
    )
    print(f"PDF gerado: {SAIDA_PDF}")


if __name__ == "__main__":
    main()
