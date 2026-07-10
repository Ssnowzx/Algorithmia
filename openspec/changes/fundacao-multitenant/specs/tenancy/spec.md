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
