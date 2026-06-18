# Convencoes de Nomenclatura — Algorithmia

[← Inicio](README.md)

## Arquivos PHP

| Tipo | Convencao | Exemplos |
|---|---|---|
| Controllers | `PascalCase` + sufixo `Controller` | `BatalhaController.php`, `LojaController.php` |
| Models | `PascalCase` | `Personagem.php`, `ProgressoFase.php` |
| Services | `PascalCase` + sufixo `Service` | `BatalhaService.php`, `ReputacaoService.php` |
| Core | `PascalCase` | `Router.php`, `Auth.php`, `Controller.php`, `Model.php` |
| Config | `lowercase-com-hifen` | `config.php`, `db.php`, `bestiario.php` |
| Scripts CLI | `lowercase-com-hifen` | `seed-banco-questoes.php`, `patch-narrativa.php` |

## Arquivos de docs e scripts

Todos em `lowercase-com-hifen`:
- `docs/auditoria/backend-arquitetura.md`
- `docs/evolucao-visual/README.md`
- `tools/recortar_rembg.py` (underline e aceitavel em Python por convencao da linguagem)

> Docs em `SCREAMING_CASE` (ex.: `DEPLOY.md`, `REGRAS-DO-JOGO.md`) sao debito
> tecnico a ser normalizado — nao repetir esse padrao em arquivos novos.

## Classes e interfaces PHP

`PascalCase` sem excecao:

```php
class BatalhaService { ... }
class ProgressoFase extends Model { ... }
```

## Variaveis e funcoes PHP

`camelCase`:

```php
$personagemId = (int) $_SESSION['usuario_id'];
function colunaSegura(string $nome): string { ... }
```

## Constantes PHP

`UPPER_SNAKE_CASE`:

```php
const COMBO_BONUS = 0.25;
const DESAFIOS_POR_BATALHA = ['licao' => 4, 'chefe' => 5];
define('BASE_URL', ...);
```

## Slugs de assets (`public/img/`)

Formato: `prefixo-descricao-especifica` em `lowercase-com-hifen`.

| Prefixo | Pasta | Exemplo |
|---|---|---|
| `mestre-` | `mestres/` | `mestre-willen`, `mestre-clayton` |
| `inimigo-` | `inimigos/` | `inimigo-bug`, `inimigo-kraken` |
| `heroi-` | `herois/` | `heroi-mago`, `heroi-draconato` |
| `item-` | `itens/` | `item-espada-logica`, `item-fragmento-ia` |
| `icone-` | `ui/icones/` | `icone-bau`, `icone-mapa` |
| `conquista-` | `ui/trofeus/` | (slug bate com `codigo` na tabela `conquistas`) |
| `fundo-` | `fundos/` | `fundo-porto`, `fundo-floresta` |
| `fase-` | `mapas/` | `fase-bug-primordial`, `fase-lorde-segfault` |
| `hud-` | `herois/` | `hud-mago`, `hud-guerreiro` |

O helper `caminhoSvg()` em `helpers.php` mapeia prefixo -> subpasta automaticamente.
Slugs que ja contem `/` sao respeitados como caminho completo.

## Tabelas e colunas do banco

- Tabelas: `snake_case` plural — `usuarios`, `personagens`, `progresso_fases`, `respostas_log`.
- Colunas: `snake_case` — `usuario_id`, `hp_atual`, `svg_slug`, `ordem_global`.
- Chaves estrangeiras: `{tabela_singular}_id` — `fase_id`, `personagem_id`.
- Indices unicos: `uq_{colunas}` — `uq_inv` em `inventario(personagem_id, item_id)`.

## Branches Git

Formato: `kebab-case` com prefixo de tipo:

| Tipo | Formato | Exemplo |
|---|---|---|
| Feature | `feature/descricao` | `feature/conquistas-trigger` |
| Bugfix | `fix/descricao` | `fix/csrf-endpoints-batalha` |
| Refactor | `refactor/descricao` | `refactor/auditoria-qualidade-producao` |
| Docs | `docs/descricao` | `docs/padroes-desenvolvimento` |

Sempre partir de `main` atualizado.

## Conventional Commits

Formato obrigatorio: `tipo: descricao no imperativo`

```
feat: adiciona sistema de conquistas por evento
fix: corrige CSRF em endpoints GET de loja
refactor: extrai logica de ouro para UPDATE atomico
test: adiciona PHPUnit para BatalhaService
docs: cria documentacao de padroes de desenvolvimento
style: normaliza nomenclatura de docs para lowercase-com-hifen
perf: adiciona indice composto em respostas_log
```

Tipos validos: `feat`, `fix`, `refactor`, `test`, `docs`, `style`, `perf`.
Referencia issue quando houver: `fix: corrige CSRF em GET (#42)`.
