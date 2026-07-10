## ADDED Requirements

### Requirement: A aplicação não conecta com um papel que ignora o RLS

A aplicação SHALL conectar ao PostgreSQL com um papel `NOSUPERUSER`, `NOBYPASSRLS` e que
não seja dono de nenhuma tabela. As migrations SHALL usar uma conexão separada, com o
papel dono.

Medido em 2026-07-10, o papel `algorithmia` é superusuário, tem `rolbypassrls` e é dono
de todas as tabelas — **três** razões independentes para as policies não se aplicarem a
ele. Ligar RLS sem trocar de papel entrega a sensação de uma barreira que não existe.

#### Scenario: Sem contexto de tenant, a aplicação não vê linha nenhuma

- **WHEN** a aplicação consulta uma tabela tenant-scoped sem `app.tenant_id` definido
- **THEN** o resultado é vazio
- **AND** não é levantado erro — a policy filtra, não recusa

#### Scenario: O papel de execução não pode contornar as policies

- **WHEN** se inspeciona o papel usado pela aplicação
- **THEN** ele não é superusuário
- **AND** ele não tem `rolbypassrls`
- **AND** ele não é dono de nenhuma tabela do schema `public`

#### Scenario: As tabelas tenant-scoped forçam RLS até para o dono

- **WHEN** se inspeciona uma tabela tenant-scoped
- **THEN** ela tem `rowsecurity` ligado
- **AND** ela tem `forcerowsecurity` ligado

### Requirement: O tenant é resolvido pelo host, nunca pelo cliente

O sistema SHALL resolver o tenant a partir do `Host` da requisição, contra a tabela
`tenant_dominios`. O sistema SHALL NOT aceitar `tenant_id` vindo de parâmetro, corpo,
cabeçalho ou sessão.

#### Scenario: Um host desconhecido não resolve tenant nenhum

- **WHEN** chega uma requisição para um host que não está em `tenant_dominios`
- **THEN** a requisição é recusada
- **AND** nenhum contexto de tenant é definido

#### Scenario: Um `tenant_id` no corpo da requisição é ignorado

- **WHEN** um cliente autenticado no tenant A envia `tenant_id` do tenant B
- **THEN** o contexto permanece o do tenant A
- **AND** nada do tenant B é lido ou escrito

#### Scenario: `X-Forwarded-Host` só é confiado atrás de um proxy declarado

- **WHEN** `TRUSTED_PROXIES` está vazio e chega um `X-Forwarded-Host` forjado
- **THEN** a resolução usa o `Host` real, e não o cabeçalho

### Requirement: O contexto do tenant não sobrevive à requisição

O sistema SHALL definir `app.tenant_id` por requisição e SHALL garantir que ele não
esteja visível para a requisição seguinte na mesma conexão.

O php-fpm reaproveita conexões. Um `SET` que sobreviva ao fim do pedido entrega os dados
da instituição A à próxima requisição, que pode ser da B — em silêncio.

#### Scenario: Duas requisições seguidas na mesma conexão

- **WHEN** uma requisição do tenant A termina
- **AND** uma requisição do tenant B chega na mesma conexão
- **THEN** a segunda enxerga apenas dados do tenant B

#### Scenario: Uma requisição que aborta não deixa contexto

- **WHEN** uma requisição do tenant A levanta exceção no meio
- **THEN** o contexto é desfeito junto com a transação

### Requirement: Nenhum tenant lê ou altera dados de outro

O sistema SHALL isolar tenants em toda via de acesso: Eloquent, query crua, e comandos
de console.

#### Scenario: Query crua não escapa da policy

- **WHEN** o tenant A executa `SELECT * FROM tenant_membros` sem cláusula `WHERE`
- **THEN** só as linhas do tenant A retornam

#### Scenario: Escrita cruzada é recusada

- **WHEN** o tenant A tenta atualizar uma linha do tenant B por id
- **THEN** nenhuma linha é afetada

### Requirement: Uma funcionalidade só existe na instituição que a habilitou

O sistema SHALL liberar funcionalidades por instituição. O catálogo de flags SHALL viver no
código; o banco SHALL guardar apenas as exceções de cada instituição.

Uma chave fora do catálogo SHALL levantar exceção. Devolver `false` transformaria um erro de
digitação em uma funcionalidade desligada para sempre, em produção, sem nada acusar.

Toda flag SHALL ser aplicada na rota, e não apenas na navegação. Esconder o link e deixar a
URL aberta é a mesma classe de erro de ligar o RLS com um papel que o ignora.

#### Scenario: A rota de uma funcionalidade desligada não existe

- **WHEN** um usuário autorizado acessa a URL de uma funcionalidade que a instituição dele não habilitou
- **THEN** a resposta é 404
- **AND** não é 403 — a página não existe para esta escola, e um código de status também é informação

#### Scenario: A flag de uma instituição não vale para outra

- **WHEN** a instituição A habilita uma funcionalidade
- **THEN** a instituição B continua com o valor padrão do código

#### Scenario: Uma chave desconhecida falha alto

- **WHEN** o código pergunta por uma flag que não está no catálogo
- **THEN** uma exceção é levantada
- **AND** a funcionalidade não é silenciosamente desligada

#### Scenario: Voltar ao padrão remove a opinião da instituição

- **WHEN** uma instituição devolve uma flag ao padrão do código
- **AND** o padrão do código muda depois
- **THEN** a instituição acompanha a mudança

### Requirement: Uma instituição só entra no ar quando está jogável

Uma instituição SHALL nascer desligada. O sistema SHALL recusar ativar uma instituição que o
smoke reprove, e SHALL NOT oferecer opção de contornar essa recusa.

Provisionar e semear são dois atos, e entre eles a escola é injogável. Uma escola ativa e
injogável reprova o smoke — que é o portão do deploy —, e portanto reprova todo deploy, até
alguém desconfiar de uma escola pela metade.

Desligar SHALL NOT exigir que a instituição esteja jogável.

#### Scenario: Provisionar não põe no ar

- **WHEN** uma instituição é provisionada
- **THEN** ela nasce desligada
- **AND** ela nasce com o seu domínio primário, na mesma transação

#### Scenario: Ativar uma instituição sem conteúdo é recusado

- **WHEN** se tenta ativar uma instituição que o smoke reprova
- **THEN** ela permanece desligada
- **AND** a mensagem diz o comando que a semeia

#### Scenario: Desligar uma instituição não toca nas demais

- **WHEN** uma instituição é desligada
- **THEN** o domínio dela devolve 404
- **AND** as demais seguem no ar
- **AND** o progresso dos alunos dela não é apagado

### Requirement: A administração da plataforma não usa um papel que ignora o RLS

O sistema SHALL permitir administrar todas as instituições sem um papel de banco que leia
através delas. Toda leitura de dado de instituição SHALL acontecer dentro do contexto daquela
instituição.

Quem administra a plataforma SHALL NOT ser um usuário de instituição alguma.

O console SHALL NOT existir enquanto o seu host não for declarado — as rotas não são
registradas, e não apenas recusadas por um middleware.

#### Scenario: As métricas de uma escola não contam os alunos de outra

- **WHEN** o painel lê as métricas de uma instituição
- **THEN** ele entra no contexto dela
- **AND** os números não somam os de nenhuma outra

#### Scenario: As duas portas não se abrem uma para a outra

- **WHEN** um mestre de instituição tenta entrar no console com as credenciais dele
- **THEN** o acesso é recusado
- **AND** um operador da plataforma tampouco entra no jogo

#### Scenario: Sem host declarado, o console não é alcançável

- **WHEN** o host do console não está configurado
- **THEN** as rotas do console não existem
- **AND** o caminho `/console` devolve 404 em qualquer domínio

### Requirement: O smoke prova o caminho do aluno, e não só o motor

O smoke SHALL verificar que o host do `APP_URL` resolve uma instituição ativa, que cada
instituição ativa tem domínio, que o progresso do aluno persiste na instituição correta, e
que o relatório de turma responde. Ele SHALL NOT deixar nada no banco.

O que o smoke não prova, o deploy promove: ele é o portão do `bin/deploy.sh`.

#### Scenario: Um APP_URL que não resolve instituição nenhuma reprova o deploy

- **WHEN** o host do `APP_URL` não está em `tenant_dominios`
- **THEN** o smoke falha
- **AND** o deploy não é promovido, embora o healthcheck esteja verde

#### Scenario: Uma instituição ativa sem domínio reprova o deploy

- **WHEN** uma instituição ativa não tem domínio primário
- **THEN** o smoke falha — ela está no ar e ninguém a alcança

#### Scenario: O progresso nasce dentro da instituição que o criou

- **WHEN** o smoke grava progresso e o relê
- **THEN** a linha pertence à instituição corrente

#### Scenario: O smoke não suja o banco

- **WHEN** o smoke termina
- **THEN** nenhuma linha nova permanece nas tabelas que ele escreveu
