#!/usr/bin/env python3
"""Conversor Markdown -> HTML estilizado (tema Xiax), stdlib apenas.
Escopo limitado a sintaxe usada em Xiax-Plano-de-Produto-EdTech.md."""
import re, sys, html

SRC = "/Users/snows/AntiGravity/TrabalhoWillen2/docs/Xiax-Plano-de-Produto-EdTech.md"
OUT = "/Users/snows/AntiGravity/TrabalhoWillen2/build/xiax.html"

# Remove apenas emoji de marcação (mantem acentos, ·, ->, >=)
EMOJI = re.compile(
    "[\U0001F300-\U0001FAFF\U00002600-\U000027BF\U0001F000-\U0001F02F]"
)

def inline(text):
    text = EMOJI.sub("", text)
    text = html.escape(text, quote=False)
    # codigo inline
    text = re.sub(r"`([^`]+)`", r"<code>\1</code>", text)
    # negrito
    text = re.sub(r"\*\*([^*]+)\*\*", r"<strong>\1</strong>", text)
    # italico
    text = re.sub(r"(?<!\*)\*([^*]+)\*(?!\*)", r"<em>\1</em>", text)
    return text.strip()

def parse(md):
    lines = md.split("\n")
    out = []
    i = 0
    n = len(lines)
    while i < n:
        line = lines[i]
        s = line.strip()

        # linha vazia
        if s == "":
            i += 1
            continue

        # separador horizontal -> ignorado (page-break vem do h2)
        if s == "---":
            i += 1
            continue

        # blockquote (pode ter varias linhas e ### interno)
        if s.startswith(">"):
            buf = []
            while i < n and lines[i].strip().startswith(">"):
                content = re.sub(r"^\s*>\s?", "", lines[i])
                buf.append(content)
                i += 1
            # processa conteudo interno do blockquote
            inner = parse_block("\n".join(buf))
            out.append(f'<blockquote class="callout">{inner}</blockquote>')
            continue

        # tabela (linha com | e proxima linha separadora)
        if s.startswith("|") and i + 1 < n and re.match(r"^\s*\|[\s:\-|]+\|\s*$", lines[i + 1]):
            header = [c.strip() for c in s.strip().strip("|").split("|")]
            i += 2  # pula header e separador
            rows = []
            while i < n and lines[i].strip().startswith("|"):
                cells = [c.strip() for c in lines[i].strip().strip("|").split("|")]
                rows.append(cells)
                i += 1
            th = "".join(f"<th>{inline(c)}</th>" for c in header)
            body = ""
            for r in rows:
                tds = "".join(f"<td>{inline(c)}</td>" for c in r)
                body += f"<tr>{tds}</tr>"
            out.append(f'<table><thead><tr>{th}</tr></thead><tbody>{body}</tbody></table>')
            continue

        # headings
        m = re.match(r"^(#{1,6})\s+(.*)$", s)
        if m:
            lvl = len(m.group(1))
            txt = inline(m.group(2))
            cls = ""
            if lvl == 2:
                cls = ' class="section"'
            out.append(f"<h{lvl}{cls}>{txt}</h{lvl}>")
            i += 1
            continue

        # lista nao ordenada
        if re.match(r"^[-*]\s+", s):
            items = []
            while i < n and re.match(r"^[-*]\s+", lines[i].strip()):
                items.append(inline(re.sub(r"^[-*]\s+", "", lines[i].strip())))
                i += 1
            lis = "".join(f"<li>{it}</li>" for it in items)
            out.append(f"<ul>{lis}</ul>")
            continue

        # lista ordenada
        if re.match(r"^\d+\.\s+", s):
            items = []
            while i < n and re.match(r"^\d+\.\s+", lines[i].strip()):
                items.append(inline(re.sub(r"^\d+\.\s+", "", lines[i].strip())))
                i += 1
            lis = "".join(f"<li>{it}</li>" for it in items)
            out.append(f"<ol>{lis}</ol>")
            continue

        # paragrafo: junta linhas consecutivas (soft-wrap) ate linha em branco/bloco
        buf = [s]
        i += 1
        while i < n:
            nxt = lines[i].strip()
            if nxt == "" or nxt == "---":
                break
            if re.match(r"^(#{1,6})\s", nxt) or nxt.startswith(">") or nxt.startswith("|"):
                break
            if re.match(r"^[-*]\s+", nxt) or re.match(r"^\d+\.\s+", nxt):
                break
            buf.append(nxt)
            i += 1
        out.append(f"<p>{inline(' '.join(buf))}</p>")
    return "\n".join(out)

def parse_block(md):
    """Versao usada dentro de blockquote (sem page-break em h)."""
    return parse(md)

CSS = """
@page { size: A4; margin: 18mm 16mm; }
* { box-sizing: border-box; }
body {
  font-family: -apple-system, "Helvetica Neue", Arial, sans-serif;
  color: #1d1d2b; font-size: 10.5pt; line-height: 1.5; margin: 0;
  -webkit-print-color-adjust: exact; print-color-adjust: exact;
}
h1 {
  font-size: 26pt; color: #6320ee; line-height: 1.15; margin: 0 0 4pt;
  letter-spacing: -0.5px;
}
h2.section {
  font-size: 17pt; color: #fff; background: #6320ee;
  padding: 7pt 12pt; border-radius: 8px; margin: 0 0 14pt;
  page-break-before: always; page-break-after: avoid;
}
h2.section:first-of-type { page-break-before: avoid; }
h3 {
  font-size: 12pt; color: #4b1fb3; margin: 16pt 0 6pt;
  page-break-after: avoid;
}
p { margin: 6pt 0; }
strong { color: #2a1a55; }
code {
  font-family: "SF Mono", Menlo, Consolas, monospace; font-size: 9pt;
  background: #f1edff; color: #4b1fb3; padding: 1px 5px; border-radius: 4px;
}
ul, ol { margin: 6pt 0 6pt 0; padding-left: 20pt; }
li { margin: 3pt 0; page-break-inside: avoid; }
table {
  width: 100%; border-collapse: collapse; margin: 10pt 0;
  font-size: 9.5pt; page-break-inside: avoid;
}
thead tr { background: #6320ee; color: #fff; }
th { text-align: left; padding: 7pt 9pt; font-weight: 600; }
td { padding: 6pt 9pt; border-bottom: 1px solid #e6e1f5; vertical-align: top; }
tbody tr:nth-child(even) { background: #faf8ff; }
tr { page-break-inside: avoid; }
blockquote.callout {
  margin: 10pt 0; padding: 9pt 14pt; background: #f6f3ff;
  border-left: 4px solid #f5a623; border-radius: 0 8px 8px 0;
  page-break-inside: avoid;
}
blockquote.callout p:first-child { margin-top: 0; }
blockquote.callout p:last-child { margin-bottom: 0; }
blockquote.callout h3 { margin-top: 0; color: #6320ee; }
blockquote.callout code {
  display: block; background: #1d1d2b; color: #d9d2ff; padding: 9pt 12pt;
  border-radius: 6px; margin: 4pt 0; white-space: pre-wrap; font-size: 8.5pt;
}
.cover { page-break-after: always; }
.cover .tag {
  color: #f5a623; letter-spacing: 3px; font-weight: 700; font-size: 9pt;
  text-transform: uppercase; margin-bottom: 8pt;
}
hr { border: none; border-top: 1px solid #e6e1f5; margin: 14pt 0; }
"""

def main():
    with open(SRC, encoding="utf-8") as f:
        md = f.read()
    body = parse(md)
    doc = f"""<!DOCTYPE html>
<html lang="pt-BR"><head><meta charset="utf-8">
<title>Xiax - Plano de Produto & Negocio</title>
<style>{CSS}</style></head>
<body>{body}</body></html>"""
    with open(OUT, "w", encoding="utf-8") as f:
        f.write(doc)
    print(f"HTML gerado: {OUT} ({len(doc)} bytes)")

if __name__ == "__main__":
    main()
