# Tasks — Missões da semana

## 1. Modelo (dados + lógica)
- [x] 1.1 `config/config.php`: `MISSOES_SEMANAIS` (pool: codigo/titulo/desc/icone/metrica/alvo[/min]) + `MISSOES_POR_SEMANA`
- [x] 1.2 `RespostaLog::metricasSemana()` + `ProgressoFase::fasesSemana()` (janela ISO)
- [x] 1.3 `app/services/MissaoService.php`: `selecionar(indice)`, `avaliar(missao, metricas)` puros + `daSemana()` defensivo

## 2. Teste
- [x] 2.1 `tools/verificar_missoes.php` (CLI): seleção determinística (mesma semana → mesmas 3; rotação) + avaliação (contável e precisão, fronteiras, completa)
- [x] 2.2 Rodar — verde

## 3. Integração (apresentação)
- [x] 3.1 `PerfilController`: passa `$missoes`
- [x] 3.2 `app/views/perfil/index.php`: painel "🎯 Missões da semana" (após o recap), barra + ✓ + "X/3 concluídas"
- [x] 3.3 `public/css/style.css`: estilos `.missao-*`

## 4. Verificação
- [x] 4.1 Visual no navegador (perfil) com dados injetados/revertidos
- [x] 4.2 `openspec validate missoes-semanais` OK
