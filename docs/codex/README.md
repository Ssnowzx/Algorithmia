# 📜 O Códex de Algorithmia

> *"Antes de correr, aprenda a indentar."* — Willen, o Arquiteto

Este é o **Códex**: o cânone consolidado de **Algorithmia — A Lenda dos Cinco
Mestres**. O lore do jogo nasce espalhado pelo código (seeds do banco, constantes
de configuração, helpers de mapa). Aqui ele é reunido, organizado e curado em um
só lugar — para quem quer entender o mundo sem garimpar `seeds.sql`.

O jogo é um RPG educativo em PHP: o herói acorda sem memória na **Vila Hello
World**, atravessa as regiões dos **cinco mestres** (cada uma uma disciplina de
Ciência da Computação) e enfrenta, no fim, o **Abismo do /dev/null** e o Lorde
Segfault. Aprender de verdade — sem ceder ao atalho do Fragmento da IA — é a
própria mecânica da vitória.

## 🗂️ Mapa do Códex

| Documento | O que contém |
|---|---|
| [**jornada.md**](jornada.md) | A jornada completa: as **35 fases** agrupadas por região/mestre, com `ordem_global`, tipo e descrição. O arco Vila Hello World → 5 regiões → Abismo do /dev/null. |
| [**mestres-e-classes.md**](mestres-e-classes.md) | Os **5 mestres** (título, disciplina, região, história, bordão) e as **6 classes jogáveis** (espécie e lore). |
| [**bestiario.md**](bestiario.md) | Lore dos inimigos do reino — dos slimes de sintaxe ao Lorde Segfault. *(mantido por outro processo)* |
| [**evolucao-visual.md**](evolucao-visual.md) | Resumo da evolução visual do jogo: v1 pixel art → v2 ilustrações. |

## 🔗 Fontes vivas do cânone

O Códex é um espelho curado. As fontes que ele consolida continuam sendo a
verdade do jogo:

- [`docs/materias/`](../materias/README.md) — mapeamento **mestre → matéria →
  banco de questões**: de qual disciplina veio cada desafio.
- [`docs/evolucao-visual/`](../evolucao-visual/README.md) — o histórico visual
  versionado completo (PNGs por versão + o PDF-galeria).
- `database/seeds.sql` — mestres, fases, desafios e diálogos (a narrativa-fonte).
- `config/config.php` — `CLASSES` (classes jogáveis), `ASSUNTOS` (matérias),
  `REGIOES_MESTRE` (região → cenário → conquista).
- `app/core/helpers.php` — `iconeFaseMapa()`, o mapa `ordem_global → fase`.

> **Curadoria:** este Códex documenta apenas o cânone real do jogo. Nada é
> inventado — cada nome, número e bordão vem direto dos arquivos-fonte acima.
