# Tasks — Domínio das Regiões (maestria horizontal)

## 1. Modelo (dados + lógica)
- [x] 1.1 `config/config.php`: `REGIAO_FAIXAS` (rótulos/cores dos 4 estados) + título lenda ("Mestre dos Cinco")
- [x] 1.2 `Mestre::progressoPorRegiao()` — query agregada partindo de `fases` (robusta à duplicação), só fases jogáveis
- [x] 1.3 `app/services/RegiaoService.php`: `faixaDe(total,concluidas,perfeitas,estrelas)` pura + `dominio()` defensivo + `totalDominadas()` + `tituloLenda()`

## 2. Teste
- [x] 2.1 `tools/verificar_regioes.php` (CLI): faixaDe nos casos — 0 concluídas (A explorar), parcial (Em jornada), todas 2★ (Conquistada), todas 3★ (Dominada), barra de perfeição
- [x] 2.2 Rodar — verde

## 3. Integração (apresentação)
- [x] 3.1 `PerfilController`: passa `$regioes`
- [x] 3.2 `app/views/perfil/index.php`: painel "🏰 Domínio das Regiões" (cor do mestre, selo de estado, barra, "X/5 dominadas" + título lenda)
- [x] 3.3 `public/css/style.css`: estilos `.regiao-*`

## 4. Verificação
- [x] 4.1 Visual no navegador (perfil) com dados injetados/revertidos
- [x] 4.2 `openspec validate dominio-regioes` OK
