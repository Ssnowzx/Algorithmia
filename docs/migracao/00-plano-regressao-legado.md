# Plano de Regressao Funcional do Legado

## ID do cenario: REG-001
**Objetivo:** validar login valido.
**Pre-condicoes:** usuario existente com senha correta.
**Estado inicial:** sem sessao autenticada.
**Acoes:** enviar formulario de login.
**Resultado esperado:** sessao criada, `session_regenerate_id(true)` executado e redirecionamento para area logada.
**Dados afetados:** `usuarios`, sessao PHP.
**Arquivos/regra do legado:** `app/controllers/AuthController.php`, `app/core/Auth.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-002
**Objetivo:** validar login invalido.
**Pre-condicoes:** usuario existe, senha incorreta.
**Estado inicial:** sem sessao autenticada.
**Acoes:** enviar formulario com credenciais invalidas.
**Resultado esperado:** autenticacao negada, mensagem de erro, sem sessao.
**Dados afetados:** nenhum persistido.
**Arquivos/regra do legado:** `app/core/Auth.php`, `app/controllers/AuthController.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-003
**Objetivo:** validar criacao de personagem.
**Pre-condicoes:** usuario autenticado sem personagem.
**Estado inicial:** sem registro em `personagens`.
**Acoes:** preencher nome e classe.
**Resultado esperado:** personagem criado com stats iniciais, itens iniciais no inventario e redirecionamento para a narrativa inicial.
**Dados afetados:** `personagens`, `inventario`.
**Arquivos/regra do legado:** `app/controllers/AuthController.php`, `config/config.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-004
**Objetivo:** validar tentativa de acesso sem autenticacao.
**Pre-condicoes:** sessao inexistente.
**Estado inicial:** usuario anonimo.
**Acoes:** abrir uma rota protegida como mapa ou batalha.
**Resultado esperado:** redirecionamento para login.
**Dados afetados:** nenhum.
**Arquivos/regra do legado:** `app/core/Auth.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-005
**Objetivo:** validar acesso a fase bloqueada.
**Pre-condicoes:** personagem autenticado sem prerequisito concluido.
**Estado inicial:** fase anterior nao concluida.
**Acoes:** abrir `historia/ver/{fase}` ou `batalha/iniciar/{fase}`.
**Resultado esperado:** bloqueio com flash de erro e retorno ao mapa.
**Dados afetados:** nenhum.
**Arquivos/regra do legado:** `app/controllers/HistoriaController.php`, `app/controllers/BatalhaController.php`, `app/services/ProgressaoService.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-006
**Objetivo:** validar acesso a fase desbloqueada.
**Pre-condicoes:** prerequisito concluido.
**Estado inicial:** progresso salvo.
**Acoes:** abrir a fase via mapa.
**Resultado esperado:** tela da fase carrega normalmente.
**Dados afetados:** nenhum.
**Arquivos/regra do legado:** `ProgressaoService::faseLiberada()`.
**Prioridade:** Alta.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-007
**Objetivo:** validar desafio respondido corretamente.
**Pre-condicoes:** batalha ativa.
**Estado inicial:** desafio atual definido.
**Acoes:** enviar resposta correta.
**Resultado esperado:** inimigo sofre dano, combo aumenta e o turno avanca.
**Dados afetados:** `respostas_log`, `personagens` durante a batalha.
**Arquivos/regra do legado:** `app/services/BatalhaService.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-008
**Objetivo:** validar desafio respondido incorretamente.
**Pre-condicoes:** batalha ativa.
**Estado inicial:** desafio atual definido.
**Acoes:** enviar resposta errada.
**Resultado esperado:** heroi sofre dano, combo zera e o turno avanca.
**Dados afetados:** `respostas_log`, `personagens`.
**Arquivos/regra do legado:** `app/services/BatalhaService.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-009
**Objetivo:** validar combo.
**Pre-condicoes:** batalha ativa com sequencia de acertos.
**Estado inicial:** combo zerado.
**Acoes:** acertar respostas em sequencia.
**Resultado esperado:** dano crescente conforme combo.
**Dados afetados:** estado da batalha em sessao.
**Arquivos/regra do legado:** `app/services/BatalhaService.php`, `config/config.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-010
**Objetivo:** validar uso de ataque especial.
**Pre-condicoes:** MP suficiente.
**Estado inicial:** batalha ativa.
**Acoes:** armar especial.
**Resultado esperado:** MP reduzido e proximo acerto dobrado.
**Dados afetados:** `personagens.mp_atual`.
**Arquivos/regra do legado:** `app/services/BatalhaService.php`, `config/config.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-011
**Objetivo:** validar uso de poção.
**Pre-condicoes:** item de cura no inventario.
**Estado inicial:** HP/MP abaixo do maximo.
**Acoes:** usar poção.
**Resultado esperado:** cura aplicada e item consumido.
**Dados afetados:** `inventario`, `personagens`.
**Arquivos/regra do legado:** `app/controllers/InventarioController.php`, `app/services/BatalhaService.php`.
**Prioridade:** Alta.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-012
**Objetivo:** validar uso de Fragmento da IA.
**Pre-condicoes:** fragmento no inventario e batalha ativa.
**Estado inicial:** reputacao atual conhecida.
**Acoes:** acionar o fragmento.
**Resultado esperado:** item consumido, reputacao cai e a resposta em andamento e processada com a marca de IA.
**Dados afetados:** `inventario`, `personagens.reputacao`, `respostas_log`.
**Arquivos/regra do legado:** `app/services/BatalhaService.php`, `config/config.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-013
**Objetivo:** validar morte subita.
**Pre-condicoes:** pool de desafios quase esgotado e inimigo ainda vivo.
**Estado inicial:** batalha em andamento.
**Acoes:** consumir a ultima rodada sem vencer.
**Resultado esperado:** estado especial de morte subita ativa ate o desfecho.
**Dados afetados:** estado da batalha.
**Arquivos/regra do legado:** `app/services/BatalhaService.php`.
**Prioridade:** Alta.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-014
**Objetivo:** validar vitoria.
**Pre-condicoes:** HP do inimigo pode chegar a zero.
**Estado inicial:** batalha ativa.
**Acoes:** concluir a batalha com acertos suficientes.
**Resultado esperado:** recompensas concedidas uma unica vez e retorno ao fluxo de conclusao.
**Dados afetados:** `personagens`, `progresso_fases`, `inventario`, `conquistas_personagem`.
**Arquivos/regra do legado:** `app/services/RecompensaService.php`, `app/controllers/BatalhaController.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-015
**Objetivo:** validar derrota.
**Pre-condicoes:** HP do heroi pode chegar a zero.
**Estado inicial:** batalha ativa.
**Acoes:** provocar erros ou dano suficiente.
**Resultado esperado:** batalha termina sem recompensa de vitoria.
**Dados afetados:** `personagens`.
**Arquivos/regra do legado:** `app/services/BatalhaService.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-016
**Objetivo:** validar concessao unica de recompensa.
**Pre-condicoes:** vitoria ja processada.
**Estado inicial:** estado da batalha marcado como recompensado.
**Acoes:** repetir a mesma resposta final ou recarregar a tela.
**Resultado esperado:** nenhuma recompensa duplicada.
**Dados afetados:** `progresso_fases`, `inventario`, `personagens`.
**Arquivos/regra do legado:** `app/controllers/BatalhaController.php`, `app/services/BatalhaService.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-017
**Objetivo:** validar persistencia de inventario.
**Pre-condicoes:** item comprado ou recebido.
**Estado inicial:** inventario salvo.
**Acoes:** recarregar sessao e abrir inventario.
**Resultado esperado:** item continua presente com a quantidade correta.
**Dados afetados:** `inventario`.
**Arquivos/regra do legado:** `app/models/Inventario.php`, `app/controllers/InventarioController.php`.
**Prioridade:** Alta.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-018
**Objetivo:** validar persistencia de reputacao.
**Pre-condicoes:** reputacao alterada por IA ou conclusao.
**Estado inicial:** personagem salvo.
**Acoes:** abrir perfil ou recarregar.
**Resultado esperado:** reputacao continua coerente com o ultimo evento.
**Dados afetados:** `personagens.reputacao`.
**Arquivos/regra do legado:** `app/services/ReputacaoService.php`, `app/services/RecompensaService.php`.
**Prioridade:** Media.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-019
**Objetivo:** validar persistencia de progresso.
**Pre-condicoes:** fase concluida.
**Estado inicial:** linha em `progresso_fases`.
**Acoes:** recarregar mapa.
**Resultado esperado:** fase aparece como concluida e/ou estrelada.
**Dados afetados:** `progresso_fases`.
**Arquivos/regra do legado:** `app/models/ProgressoFase.php`, `app/controllers/MapaController.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-020
**Objetivo:** validar conclusao de fase de historia.
**Pre-condicoes:** fase tipo `historia`.
**Estado inicial:** historia ainda nao concluida.
**Acoes:** concluir a fase.
**Resultado esperado:** progresso registrado e redirecionamento correto.
**Dados afetados:** `progresso_fases`, possivel XP.
**Arquivos/regra do legado:** `app/controllers/HistoriaController.php`.
**Prioridade:** Alta.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-021
**Objetivo:** validar ranking.
**Pre-condicoes:** multiplos personagens com niveis/XP distintos.
**Estado inicial:** ranking vazio ou recarregado.
**Acoes:** abrir ranking.
**Resultado esperado:** ordenacao por nivel e XP.
**Dados afetados:** nenhum.
**Arquivos/regra do legado:** `app/models/Personagem.php`, `app/controllers/RankingController.php`.
**Prioridade:** Media.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-022
**Objetivo:** validar acesso ao painel mestre.
**Pre-condicoes:** conta mestre e conta aluno.
**Estado inicial:** ambas sem sessao.
**Acoes:** tentar abrir `?url=mestre`.
**Resultado esperado:** mestre entra; aluno recebe 403.
**Dados afetados:** nenhum.
**Arquivos/regra do legado:** `app/core/Auth.php`, `app/controllers/MestreController.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-023
**Objetivo:** validar tentativa de acesso administrativo indevido.
**Pre-condicoes:** conta de aluno autenticada.
**Estado inicial:** area administrativa fechada.
**Acoes:** acessar CRUD do mestre.
**Resultado esperado:** bloqueio por papel.
**Dados afetados:** nenhum.
**Arquivos/regra do legado:** `app/core/Auth.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-024
**Objetivo:** validar repeticao de requisicao mutavel.
**Pre-condicoes:** requisicao POST ou AJAX com token valido.
**Estado inicial:** saldo ou item alteravel.
**Acoes:** repetir a mesma acao rapidamente.
**Resultado esperado:** nenhuma duplicacao indevida de recompensa, compra ou consumo.
**Dados afetados:** `personagens`, `inventario`, `progresso_fases`.
**Arquivos/regra do legado:** `app/controllers/BatalhaController.php`, `app/controllers/LojaController.php`, `app/controllers/InventarioController.php`, `app/services/RecompensaService.php`.
**Prioridade:** Critica.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-025
**Objetivo:** validar conteudo sem desafio.
**Pre-condicoes:** fase cadastrada sem desafios.
**Estado inicial:** fase acessivel.
**Acoes:** iniciar a batalha.
**Resultado esperado:** bloqueio com erro informando ausencia de desafios.
**Dados afetados:** nenhum.
**Arquivos/regra do legado:** `app/controllers/BatalhaController.php`.
**Prioridade:** Alta.
**Fase futura responsavel:** Fase 5.

## ID do cenario: REG-026
**Objetivo:** validar inconsistência de conteudo ou progresso.
**Pre-condicoes:** banco com fase, desafio ou progresso parcial.
**Estado inicial:** dados divergentes.
**Acoes:** abrir mapa/perfil/batalha.
**Resultado esperado:** o sistema deve falhar de forma previsivel; qualquer divergencia observada deve ser registrada como lacuna.
**Dados afetados:** `fases`, `desafios`, `progresso_fases`, `respostas_log`.
**Arquivos/regra do legado:** `app/models/*`, `app/services/*`.
**Prioridade:** Alta.
**Fase futura responsavel:** Fase 5.

## Observacao
Os cenarios acima refletem o comportamento encontrado no legado e nas checagens manuais documentadas em `docs/desenvolvimento/qa-e-testes.md`. Onde houver lacuna ou ambiguidade, o proprio cenario deve servir de gatilho para definir a regra na fase futura, nao para inventa-la agora.

