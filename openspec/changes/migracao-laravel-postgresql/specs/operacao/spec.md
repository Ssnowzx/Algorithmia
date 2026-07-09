## ADDED Requirements

### Requirement: A importação preserva os IDs do legado

A importação SHALL copiar as 13 tabelas preservando os identificadores originais, e
SHALL verificar isso ao final.

`ServicoDeConquistas` referencia as fases secundárias por ID fixo
(`config('jogo.fases_secundarias')`). Com IDs novos, a conquista
`arquivista_do_vazio` ficaria inalcançável **e nada acusaria o erro**: nenhuma
exceção, nenhum log — apenas um jogador que nunca a recebe.

#### Scenario: O ensaio exercita tudo e não grava nada

- **WHEN** a importação roda com `--dry-run`
- **THEN** as chaves estrangeiras e a conversão de tipos são exercitadas dentro de uma transação
- **AND** a transação é desfeita e o destino continua vazio

#### Scenario: Referência órfã aborta e desfaz

- **WHEN** o legado contém um desafio apontando para uma fase inexistente
- **THEN** a importação aborta
- **AND** nem as tabelas já copiadas sobrevivem

#### Scenario: O legado nunca é escrito

- **WHEN** a importação roda, em ensaio ou para valer
- **THEN** o banco MySQL permanece intacto, pois ele é o plano de rollback do corte

### Requirement: O smoke prova que o jogo é jogável

Após o deploy, uma verificação SHALL jogar uma fase real dentro de uma transação e
desfazê-la. Um health check prova que o PHP respondeu e que o banco aceitou um
`SELECT 1`; não prova que o conteúdo está lá, que os IDs sobreviveram à importação,
nem que o motor consegue jogar.

#### Scenario: O smoke joga e não deixa rastro

- **WHEN** `algorithmia:smoke` roda contra o banco de produção
- **THEN** um herói descartável vence uma lição real respondendo o gabarito do servidor
- **AND** a transação é desfeita: nenhuma linha nova em `respostas_log`, nenhum usuário-sonda

#### Scenario: O smoke recusa promover um deploy com o conteúdo faltando

- **WHEN** o banco está migrado mas sem conteúdo importado
- **THEN** a verificação completa falha e o comando devolve erro
- **AND** o modo `--sem-conteudo` (usado pela CI, com banco vazio) verifica apenas schema e configuração, e diz isso na mensagem

### Requirement: O deploy se reverte sozinho quando o smoke reprova

O deploy SHALL etiquetar a imagem com o SHA do commit, recusar árvore de trabalho
suja, tirar um dump antes de qualquer alteração e, se o smoke reprovar, voltar
sozinho para a tag anterior — **esperando a versão revertida ficar de pé** antes de
declarar qualquer coisa.

#### Scenario: Deploy ruim reverte com o site no ar

- **WHEN** o smoke reprova a imagem recém-construída
- **THEN** a tag anterior sobe de novo
- **AND** o script espera `/healthz` responder 200 através do nginx
- **AND** roda um smoke na versão revertida antes de sair com erro

#### Scenario: A versão anterior também reprova o smoke

- **WHEN** a versão revertida não passa no smoke
- **THEN** o script informa que o problema é o **banco**, não o código
- **AND** aponta o dump a restaurar

### Requirement: Migrations aditivas

Toda migration SHALL ser aditiva — criar tabela, criar coluna anulável, criar
índice. Elas rodam **antes** de o código novo subir, contra o código velho ainda no
ar; e o rollback de código **não desfaz schema**.

Remover ou renomear coluna SHALL ser feito em dois deploys: primeiro parar de usar,
depois remover.

#### Scenario: Migration aditiva sobrevive ao rollback de código

- **WHEN** um deploy cria uma tabela nova e depois é revertido
- **THEN** o código antigo simplesmente ignora a tabela
- **AND** nenhum dado de jogador é perdido

#### Scenario: Migration destrutiva quebra o código velho

- **WHEN** uma migration remove uma coluna que o código no ar ainda lê
- **THEN** o jogo quebra no intervalo entre a migration e a subida do código novo
- **AND** o rollback de código não resolve — só restaurar o dump, perdendo o que os jogadores fizeram desde então

### Requirement: A imagem recusa configuração insegura

O entrypoint SHALL recusar subir com `APP_KEY` vazio ou com `APP_DEBUG=true` sob
`APP_ENV=production`. O `config:cache` do Laravel não valida nenhum dos dois: o
primeiro só quebraria no primeiro login, e o segundo entregaria caminhos de arquivo
a qualquer aluno que provocasse um erro.

#### Scenario: Sem APP_KEY, o container não sobe

- **WHEN** o container é iniciado sem `APP_KEY`
- **THEN** ele falha imediatamente, com a instrução de como gerar a chave

### Requirement: Um backup nunca restaurado não é um backup

O procedimento de backup SHALL verificar o gzip e recusar um dump suspeito de tão
pequeno. SHALL existir um modo de **ensaio** que restaura num banco descartável e
confere as contagens, sem tocar na produção.

#### Scenario: O ensaio prova que o dump é restaurável

- **WHEN** `bin/restore.sh --ensaio` roda sobre o dump mais recente
- **THEN** um banco descartável é criado, restaurado e conferido
- **AND** o banco de produção não é tocado
- **AND** o banco de ensaio é removido ao final
