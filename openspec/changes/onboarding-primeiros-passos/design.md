# Design — Onboarding "Primeiros passos" (endowed progress)

## Camada MVC

| Camada | Arquivo | Papel |
|--------|---------|-------|
| Config | `config/config.php` | `ONBOARDING_NIVEL_MAX` (até que nível o painel aparece) |
| Model | `app/models/Inventario.php` | `temEquipado()` (read-only) |
| Service | `app/services/OnboardingService.php` | `montar()` pura + `primeirosPassos()` defensivo |
| Controller | `app/controllers/PerfilController.php` | injeta `$onboarding` |
| View / CSS | `app/views/perfil/index.php`, `public/css/style.css` | painel "🌟 Primeiros passos" |

## Os 4 marcos (o 1º é o head start dotado)

| # | Marco | Feito quando |
|---|-------|--------------|
| 1 | Forjar seu herói | **sempre** (o personagem existe) — *endowed* |
| 2 | Vencer a primeira batalha | `≥ 1` fase concluída (`progresso_fases`) |
| 3 | Equipar um item | há item com `equipado = 1` |
| 4 | Desbloquear uma conquista | `≥ 1` em `conquistas_personagem` |

Todos derivam de dados existentes; nenhuma escrita.

## Visibilidade

`mostrar = (nível <= ONBOARDING_NIVEL_MAX) && (completos < total)`. Assim o painel é um
companheiro do início e **desaparece sozinho** quando o jogador evolui (passa do nível) ou
conclui os 4 passos — sem incomodar quem já jogou.

## Robustez

- `montar(flags, nivel)` é **pura** (testável): mapeia os booleanos dos passos + nível na
  estrutura `{passos, completos, total, mostrar}`.
- `primeirosPassos()` faz o I/O em `try/catch` → `{mostrar:false,…}`; a view só desenha quando
  `mostrar` é verdadeiro. Nunca quebra o perfil.
