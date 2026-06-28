# 🎨 Evolução Visual — Algorithmia

Histórico **versionado** da identidade visual do jogo. Cada versão é um marco da
arte ao longo do desenvolvimento, para acompanhar a evolução sem perder nada.

> **Assets vivos do jogo** ficam em `public/img/` (é o que o jogo serve e referencia).
> Esta pasta é **só arquivo histórico** — nunca substitui nem apaga os assets vivos.

## 📁 Versões

| Versão | Marco | Conteúdo |
|--------|-------|----------|
| **v1 — Pixel Art** (`v1-pixel-art/`) | Commit inicial `1423d38` | Sprites autorais em pixel art (mestres, inimigos, itens, fundos, herói, ícones de UI). Inclui as **fotos reais dos professores** (`_referencia-fotos-professores/`) e as fichas de galeria (`_galeria-contact-sheets/`). |
| **v2 — Ilustrações** (`v2-ilustracoes/`) | Atual | Arte ilustrada de alta resolução: cards dos mestres, cenários, cartas de herói, mapa e UI ornamentada. Espelha `public/img/` (somente PNG). |

Um **PDF-galeria** (`Evolucao-Visual.pdf`) reúne miniaturas de todas as versões
lado a lado, para folhear a evolução rapidamente.

## 📜 Regra de curadoria (PERMANENTE)

Este histórico documenta a evolução do jogo **através de imagens**. Vale a regra:

- ✅ **Só imagens relevantes e de qualidade** — arte que represente de fato um
  marco visual do jogo.
- ❌ **Nunca** salvar imagens aleatórias, de teste, rascunhos, near-duplicates
  ou sem valor documental.
- 🚫 **Sem duplicatas de formato**: o histórico guarda **PNG** (fonte). Os `.webp`
  de `public/img/` são cópias de performance e **não** entram aqui.
- 🔒 **Nunca apagar** uma versão anterior. Uma arte nova **não substitui** o
  registro da antiga — vira uma nova versão.

## ➕ Como registrar uma nova versão (v3, v4, …)

1. Quando a identidade visual mudar de forma relevante, crie `v3-<nome>/` e
   coloque ali um snapshot dos PNGs daquele marco (espelhando `public/img/`).
2. Regere o PDF-galeria:
   ```bash
   python3 tools/pdf/gerar_galeria_evolucao.py
   ```
3. Atualize a tabela de versões acima.

> Recuperar arte antiga do histórico do git:
> `git archive <commit>:public/img | tar -x -C destino/`
