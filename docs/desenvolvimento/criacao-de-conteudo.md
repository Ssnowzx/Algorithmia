# Criacao de Conteudo — Algorithmia

[← Inicio](README.md)

Conteudo do jogo = fases, desafios/questoes, itens e lore. Tudo guiado por dados —
nenhum ID de fase deve ser hardcoded no codigo PHP (debito tecnico existente em
`HistoriaController` e `ConquistaService`; nao perpetuar).

## Estrutura de fases

O jogo tem 35 fases divididas em 6 regioes. Cada fase tem um `tipo`:

| Tipo | Desafios por batalha | Descricao |
|---|---|---|
| `historia` | 0 | Cenas narrativas/dialogos sem combate |
| `licao` | 4 | Batalha de ensino basico |
| `secundaria` | 3 | Missao opcional com recompensa extra |
| `chefe` | 5 | Chefe de regiao |
| `chefe_final` | 6 | Lorde Segfault (fase 35) |

Valores em `config/config.php` (`DESAFIOS_POR_BATALHA`).

## Adicionar ou editar questoes

### 1. Localizar o arquivo da materia

```
database/banco-questoes/
  logica.php       -- Vila Hello World (fases 2-3)
  php-mvc-sql.php  -- Porto da Sintaxe / Willen (fases 4-9)
  poo.php          -- Cidadela dos Objetos / Clayton (fases 10-15)
  estruturas.php   -- Floresta das Estruturas / Marcelo (fases 16-21)
  calculo.php      -- Montanha do Calculo / Cesar (fases 22-27)
  redes.php        -- Torre das Conexoes / Cassandro (fases 28-33)
  final.php        -- Abismo do /dev/null (fases 34-35)
```

O mapeamento completo de mestre -> materia -> arquivo esta em `docs/materias/README.md`.

### 2. Formato de cada pergunta

```php
['fase' => 17, 'tipo' => 'multipla', 'assunto' => 'estruturas',
 'pergunta' => 'Qual e a complexidade de busca em uma lista encadeada?',
 'codigo' => null,
 'opcoes' => ['O(1)', 'O(log n)', 'O(n)', 'O(n^2)'],
 'resposta' => 2,        // indice da opcao correta (base 0)
 'explicacao' => 'Listas encadeadas nao tem acesso direto; e preciso percorrer desde o inicio: O(n).',
 'dif' => 2],            // 1=facil, 2=medio, 3=dificil
```

#### Tipos e gabarito

| `tipo` | `resposta` | `opcoes` |
|---|---|---|
| `multipla` | int (indice) | lista de alternativas |
| `erro` | int (indice) | lista de alternativas (uma e o codigo com erro) |
| `vf` | `true` ou `false` | `null` |
| `completar` | lista de strings aceitas | `null` |
| `ordenar` | lista de indices na ordem correta | lista a ordenar |
| `arrastar` | lista de indices na ordem correta | lista a ordenar |

#### Campos obrigatorios vs opcionais

- Obrigatorios: `fase`, `tipo`, `assunto`, `pergunta`, `resposta`, `explicacao`, `dif`.
- `codigo`: trecho de codigo exibido abaixo da pergunta (ou `null`).
- `opcoes`: obrigatorio para `multipla`, `erro`, `ordenar`, `arrastar`; `null` para o resto.

#### Valores validos de `assunto`

`php`, `mvc`, `sql`, `poo`, `estruturas`, `redes`, `logica`, `calculo`.
Definido no ENUM da coluna `assunto` em `database/schema.sql`.

### 3. Inserir no banco

```bash
php database/seed-banco-questoes.php
```

A insercao e idempotente: identidade = `(fase_id, pergunta)`. Rodar de novo
nunca duplica; questoes novas entram sem tocar nas existentes.

O `migrate.php` chama esse seeder automaticamente ao final.

### 4. Como funciona o anti-repeticao

O pool de cada fase tem mais desafios do que o sorteado por batalha. A cada
combate, `BatalhaService::sortearDesafios()`:

1. Busca todos os desafios da fase (`Desafio::poolDaFase()`).
2. Separa os que o personagem ainda nao viu (`Desafio::idsVistos()` via `respostas_log`).
3. Sorteia N do pool priorizando ineditos; completa com vistos se necessario.
4. Reordena os N escolhidos por `dificuldade` crescente.

Resultado: refazer a mesma fase raramente repete as mesmas perguntas, mas a
curva de dificuldade dentro da batalha e sempre suave.

## Adicionar fases novas

Fases vivem no banco, em `seeds.sql` ou via painel do mestre. Para adicionar
via SQL diretamente (ambiente de desenvolvimento):

```sql
INSERT INTO fases (mestre_id, ordem, ordem_global, nome, tipo, inimigo_nome,
                   inimigo_svg, inimigo_hp, inimigo_ataque, descricao)
VALUES (1, 7, 10, 'Nova Fase', 'licao', 'Novo Bug', 'inimigo-bug', 120, 15,
        'Descricao da fase.');
```

Depois adicione as questoes no arquivo `.php` da materia e rode o seeder.

Se a fase tiver icone no mapa, adicione o mapeamento em `iconeFaseMapa()` em
`app/core/helpers.php` e crie o asset em `public/img/mapas/fase-nome-da-fase.png`.

## Lore: bestiario e dialogos

### Bestiario (`config/bestiario.php`)

Constante `BESTIARIO` indexada pelo `svg_slug` do inimigo. Estrutura:

```php
'inimigo-novo' => [
    'nome'     => 'Nome do inimigo',
    'titulo'   => 'Epiteto sarcastico',
    'regiao'   => 'Regiao (Mestre)',
    'conceito' => 'Conceito tecnico que o inimigo representa (sempre correto).',
    'lore'     => 'Historia acida e zombeteira — sabor do projeto.',
    'fraqueza' => 'Como derrota-lo (tema tecnico).',
],
```

Tom do sabor: acido e zombeteiro (regra do projeto). O `conceito` deve ser tecnicamente correto.
Ver `docs/codex/bestiario.md` para a documentacao narrativa completa.

### Dialogos

Tabela `dialogos` no banco. Campos: `fase_id`, `momento` (antes/durante/depois),
`variante` (padrao/venceu/perdeu), `falante`, `texto`, `svg_slug`, `ordem`.

Para adicionar cenas narrativas novas: inserir via SQL ou pelo painel do mestre
(`?url=mestre/index`).
