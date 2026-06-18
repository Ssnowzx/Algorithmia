# Banco de questões (anti-repetição)

Um arquivo PHP por **matéria**, cada um devolvendo uma lista de perguntas. Juntos
formam o **pool** de desafios de cada fase — bem maior do que o sorteado por
batalha, para que refazer uma fase quase nunca repita as mesmas perguntas.

## Como funciona

1. **Pool por fase.** Cada fase guarda mais desafios do que entram num combate.
   Tamanho do combate (N) por tipo de fase está em `config/config.php`
   (`DESAFIOS_POR_BATALHA`): lição 4, secundária 3, chefe 5, chefe final 6.
2. **Sorteio anti-repetição.** A cada início de batalha,
   `BatalhaService::sortearDesafios()` sorteia N do pool **priorizando perguntas
   que o personagem ainda não viu** (consultando `respostas_log`); só recorre às
   já vistas para completar a quantidade.
3. **Progressão preservada.** Os N escolhidos são reordenados por dificuldade
   crescente, então a curva dentro da batalha continua suave mesmo com perguntas
   diferentes a cada vez.

## Formato de cada pergunta

```php
['fase' => 17, 'tipo' => 'multipla', 'assunto' => 'estruturas',
 'pergunta' => '...', 'codigo' => null,
 'opcoes' => ['a', 'b', 'c', 'd'], 'resposta' => 1,
 'explicacao' => '...', 'dif' => 2],
```

Gabarito (`resposta`) por tipo — espelha `BatalhaService::verificar()`:

| `tipo` | `resposta` | `opcoes` |
|---|---|---|
| `multipla`, `erro` | índice (int) da opção correta | lista de alternativas |
| `vf` | `true` / `false` | `null` |
| `completar` | lista de strings aceitas | `null` |
| `ordenar`, `arrastar` | lista de índices na ordem certa | lista a ordenar |

## Adicionar ou variar perguntas

1. Edite o arquivo da matéria (ou crie um novo `*.php` que devolva a lista).
2. Rode `php database/seed-banco-questoes.php` (ou `php database/migrate.php`).

A inserção é **idempotente**: a identidade de uma pergunta é o par
`(fase_id, pergunta)`, então rodar de novo nunca duplica e perguntas novas
entram sem tocar nas antigas. As fontes de conteúdo estão em
[`docs/materias/`](../../docs/materias/README.md).
