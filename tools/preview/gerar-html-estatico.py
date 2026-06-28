#!/usr/bin/env python3
"""
Gera uma cópia HTML estática (abrível por duplo-clique, offline) de uma view PHP
AUTOSSUFICIENTE — daquelas com CSS inline e que só usam asset() para imagens
(ex.: app/views/historia/lore.php). O .php continua sendo a versão servida pela
rota MVC; o .html é só para visualização rápida fora do servidor.

Uso:
  python3 tools/preview/gerar-html-estatico.py <view.php> <saida.html> <prefixo-public>

Ex.:
  python3 tools/preview/gerar-html-estatico.py \\
    app/views/historia/lore.php app/views/historia/historia.html ../../../public/

<prefixo-public> = caminho RELATIVO até a pasta public/ a partir de onde o .html
vai ficar (o navegador resolve as imagens por esse caminho ao abrir o arquivo).

Só funciona em views estáticas: se sobrar QUALQUER bloco PHP além de asset(),
o script avisa e não gera (a view depende de dados/lógica e precisa do servidor).
"""
import re
import sys
import os


def main() -> int:
    if len(sys.argv) != 4:
        print(__doc__)
        return 1
    entrada, saida, prefixo = sys.argv[1], sys.argv[2], sys.argv[3]
    s = open(entrada, encoding="utf-8").read()

    # asset('arg') / asset("arg")  ->  <prefixo>arg  (asset = BASE_URL + public/ + arg)
    s = re.sub(
        r"""<\?=\s*asset\(\s*['"]([^'"]+)['"]\s*\)\s*;?\s*\?>""",
        lambda m: prefixo + m.group(1),
        s,
    )

    resto = s.count("<?")
    if resto:
        print(f"⚠ {entrada}: sobraram {resto} bloco(s) PHP — esta view NÃO é estática "
              "(tem lógica/dados) e precisa do servidor. Nada gerado.")
        return 2

    nota = ("<!-- GERADO por tools/preview/gerar-html-estatico.py a partir de "
            f"{entrada}. NÃO edite à mão: edite a view e rode o gerador. -->")
    s = s.replace("<!doctype html>", "<!doctype html>\n" + nota, 1)

    os.makedirs(os.path.dirname(saida) or ".", exist_ok=True)
    open(saida, "w", encoding="utf-8").write(s)
    print(f"✓ {saida}  ({len(s)} bytes)  prefixo public/: {prefixo}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
