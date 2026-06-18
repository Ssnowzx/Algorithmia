# Matérias-fonte dos desafios

As perguntas do jogo foram escritas a partir do conteúdo das disciplinas do
4º semestre de Ciência da Computação (UNIFACVEST, Lages). Cada mestre cobre uma
matéria; o banco de questões vive em
[`database/banco-questoes/`](../../database/banco-questoes/).

> Os PDFs originais das matérias foram a **fonte** do conteúdo. Depois de extraído
> e transformado nas perguntas do banco, eles foram removidos do repositório
> (eram material de referência pesado, não código). Este README preserva o
> mapeamento mestre → matéria → arquivo do banco.

| Mestre / Região | Matéria | Banco de questões |
|---|---|---|
| **Willen** · Porto da Sintaxe | Laboratório de Programação II (PHP · MVC · SQL) | `php-mvc-sql.php` |
| **Clayton** · Cidadela dos Objetos | Programação Orientada a Objetos | `poo.php` |
| **Marcelo** · Floresta das Estruturas | Estrutura de Dados II | `estruturas.php` |
| **Cesar** · Montanha do Cálculo | Cálculo (uma e múltiplas variáveis) | `calculo.php` |
| **Cassandro** · Torre das Conexões | Redes de Computadores | `redes.php` |
| Vila Hello World (Capítulo 0) | Lógica e Algoritmos (fundamentos) | `logica.php` |
| O Abismo do /dev/null (final) | Revisão das cinco disciplinas | `final.php` |

Para **adicionar/variar perguntas**, edite os arquivos em
`database/banco-questoes/` e rode `php database/seed-banco-questoes.php` — a
inserção é idempotente (identidade por `fase_id` + `pergunta`).
