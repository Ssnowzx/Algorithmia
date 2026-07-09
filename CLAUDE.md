# Algorithmia — Regras do Projeto

Instruções específicas deste projeto (complementam as regras globais do usuário).

## 🔀 Duas bases de código (REGRA PERMANENTE)

O repositório tem o **legado** (raiz, PHP puro + MySQL, em produção) e o **port**
(`platform/`, Laravel 13 + PostgreSQL 18, completo mas ainda não cortado).

- **Trabalho novo vai para o `platform/`.** O legado só recebe correção urgente —
  ele é o plano de rollback do corte e será aposentado.
- **As duas suítes ficam verdes.** 38 vetores-ouro no legado, 196 testes no port.
  A CI roda ambas.
- **Os 38 vetores-ouro (`tests/`) são o CONTRATO do motor.** Os números esperados
  foram derivados à mão das constantes de balanceamento, não capturados de snapshot.
  Se um deles falhar, o port está errado — **nunca "ajuste" o teste**.
- **Mudar um número de balanceamento é decisão de game design.** Ela quebra
  vetores-ouro de propósito, e o diff tem de mostrar a intenção.
- **No port, `pint` e `phpstan` (nível 6) ficam limpos** antes de qualquer commit.
- **Toda migration do port é ADITIVA** — criar tabela, criar coluna anulável, criar
  índice. Elas rodam contra o código velho ainda no ar, e o rollback de código não
  desfaz schema. Remover coluna exige dois deploys.

### Regras do jogo que já quase se perderam no port

- **Rejogar uma fase sem colar apaga a mancha da IA.** `ProgressoFase::registrar` só
  acumula o melhor nas *estrelas*; `acertos`, `erros` e `usou_ia` refletem a última
  partida. Parece bug; é o que permite reconquistar "Puro de Coração". **Redenção é
  regra do jogo.**
- **A batalha nunca termina por acabarem as perguntas** — cruzado o limite de ritmo,
  abre o Duelo Final e a fúria cresce.
- **O Fragmento da IA não se vende.** Transformá-lo em ouro faria da queda moral um
  negócio.
- **O gabarito nunca sai do servidor.**

Ver [`docs/migracao/PLANO.md`](docs/migracao/PLANO.md),
[`docs/operacao/RUNBOOK.md`](docs/operacao/RUNBOOK.md) e
[`openspec/changes/migracao-laravel-postgresql/`](openspec/changes/migracao-laravel-postgresql/).

> ⚠️ A auditoria em `docs/auditoria/` é um retrato de 2026-06-18 e está
> **desatualizada**: os débitos críticos que ela aponta já foram corrigidos.

## 🎨 Registro da evolução visual (REGRA PERMANENTE)

O usuário quer **documentar a evolução do jogo através de imagens**, de forma
versionada e curada.

- **Onde:** o histórico visual fica em [`docs/evolucao-visual/`](docs/evolucao-visual/README.md),
  separado em subpastas por versão (`v1-pixel-art/`, `v2-ilustracoes/`, `v3-…`).
  Os **assets vivos** do jogo continuam em `public/img/` (é o que o jogo serve).
- **Curadoria obrigatória — só entra no histórico imagem que vale a pena:**
  - ✅ Apenas imagens **relevantes e de qualidade**, que representem um marco visual real.
  - ❌ **Nunca** gerar nem salvar imagens aleatórias, de teste, rascunhos,
    *near-duplicates* ou sem valor documental.
  - 🚫 **Sem duplicatas de formato:** o histórico guarda **PNG** (fonte). Os `.webp`
    de `public/img/` são cópias de performance e **não** entram no arquivo.
- **Nunca apagar** uma versão anterior. Arte nova **não substitui** o registro da
  antiga — cria-se uma nova versão (`v3`, `v4`, …).
- **Ao fechar um marco visual novo:** snapshot dos PNGs em `docs/evolucao-visual/vN-<nome>/`
  e regerar o PDF-galeria com `python3 tools/pdf/gerar_galeria_evolucao.py`.

## 🪙 Identidade visual / marca (REGRA PERMANENTE)

Todos os documentos/PDFs do projeto devem ter **a mesma cara de produto**, para o
jogador reconhecer a marca. A identidade é centralizada em **`tools/pdf/marca_pdf.py`**
(kit compartilhado) — **todo PDF novo deve importá-lo**, nunca recriar do zero.

- **Logo:** usar SEMPRE a logo **oficial ilustrada** do jogo (`logo-marca-ilustrado.png` na capa,
  `logo-header.png` no cabeçalho), via `marca.logo_img('full'|'header')` — a mesma do jogo.
  Fallback vetorial: `emblema_svg()` (escudo arcano + runas `</>`, em `public/img/ui/logos/emblema-algorithmia.svg`).
- **Paleta:** navy `#0b0c1d` · roxo `#7c5cff`/`#9d83ff` · ouro `#ffce47` · runa ciano `#8ce6ff`
  (espelha `:root` de `public/css/style.css`). Cada mestre usa sua `cor_tema` como acento
  dentro do mesmo chrome de marca.
- **Tipografia:** Cinzel (títulos épicos) · Pixelify Sans (wordmark) · Rubik (corpo).
  Baixadas/cacheadas em `tools/.cache/` por `marca.garantir_fontes()`.
- **Layout dos PDFs:** capa navy emoldurada + páginas full-bleed (1 mestre por folha),
  faixa-topo (emblema+wordmark), moldura ornamental dourada com filetes de canto, rodapé com runa.

### Ferramentas
- `tools/pdf/marca_pdf.py` → **kit de marca** (emblema, fontes, CSS, ícones, cabeçalho/rodapé). Base de todo PDF.
- `tools/pdf/gerar_pdf_fases.py` → Códex dos Mestres: fases & tópicos (`docs/Fases-e-Topicos-por-Mestre.pdf`).
- `tools/pdf/gerar_galeria_evolucao.py` → galeria da evolução visual (`docs/evolucao-visual/Evolucao-Visual.pdf`).
