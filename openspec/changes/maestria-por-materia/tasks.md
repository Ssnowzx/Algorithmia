# Tasks — Maestria por matéria

## 1. Modelo (dados + lógica)
- [x] 1.1 `config/config.php`: `const MAESTRIA_FAIXAS` (rótulo, ícone, classe de cor, mín. acertos, piso de precisão)
- [x] 1.2 `app/services/MaestriaService.php`: `faixaDe(total, acertos)` pura → tier/rótulo/ícone/precisão + goal-gradient
- [x] 1.3 `MaestriaService::porMateria($estatisticas)` mapeia os 8 `ASSUNTOS` (defensivo → `[]`)

## 2. Teste
- [x] 2.1 `tools/diagnostico/verificar_maestria.php` (CLI): asserts AAA dos casos-limite (0 respostas, baixo volume/alta precisão, alto volume/baixa precisão, fronteiras de cada faixa)
- [x] 2.2 Rodar `php tools/diagnostico/verificar_maestria.php` — verde

## 3. Integração (apresentação)
- [x] 3.1 `PerfilController`: passa `$maestria` reusando `$estatisticas` (sem query nova)
- [x] 3.2 `app/views/perfil/index.php`: evolui o painel "Domínio por matéria" (selo de faixa + barra de próximo tier + resumo X/8); fallback se vazio
- [x] 3.3 `public/css/style.css`: estilos `.maestria-*` (escada de cor por tier) reusando `.barra-stat/.trilha`

## 4. Verificação
- [x] 4.1 Visual no navegador (perfil) — selos e barras corretos; sem regressão no resto do perfil
- [x] 4.2 `openspec validate maestria-por-materia` OK
