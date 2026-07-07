## ADDED Requirements

### Requirement: Autorização em duas camadas
O sistema SHALL definir autorização em camada de plataforma e camada de tenant. A camada
de plataforma SHALL possuir um administrador global restrito. A camada de tenant SHALL
usar papéis `owner`, `admin`, `manager` e `member`.

#### Scenario: Admin global não administra tenant automaticamente
- **WHEN** um administrador global acessa dados tenant-aware
- **THEN** ele SHALL continuar sujeito ao tenant resolvido e às memberships

#### Scenario: Papéis tenant são distintos
- **WHEN** um usuário possui papel em um tenant
- **THEN** esse papel SHALL não conceder privilégios em outro tenant

### Requirement: Gestão de memberships e papéis
O sistema SHALL permitir gerenciar usuários e memberships de acordo com o papel do tenant.
`owner` SHALL poder administrar memberships e domínios do próprio tenant; `admin` SHALL
administrar membros conforme permissões delegadas; `manager` e `member` SHALL ter escopo
limitado.

#### Scenario: Owner adiciona novo member
- **WHEN** um owner adiciona um usuário ao tenant
- **THEN** o membership SHALL ser criado com papel permitido

#### Scenario: Admin não altera owner sem permissão
- **WHEN** um admin tenta alterar o papel de um owner sem autorização explícita
- **THEN** a operação SHALL ser negada

#### Scenario: Membro não administra outro tenant
- **WHEN** um membro tenta gerenciar usuários de outro tenant
- **THEN** a operação SHALL ser negada

### Requirement: Proteção do último owner ativo
O sistema SHALL proibir a remoção do último owner ativo de um tenant. Suspensão,
rebaixamento ou remoção SHALL respeitar a existência mínima de um owner ativo.

#### Scenario: Último owner não pode ser removido
- **WHEN** uma ação tenta remover o único owner ativo do tenant
- **THEN** o sistema SHALL negar a operação

### Requirement: Criação segura de tenant e primeiro owner
O sistema SHALL prever a criação de um tenant com seu primeiro owner de forma segura,
sem expor dados de outros tenants e sem permitir posse implícita por dados do cliente.

#### Scenario: Primeiro owner é criado junto ao tenant
- **WHEN** um tenant novo é cadastrado
- **THEN** o primeiro owner SHALL ser vinculado de forma atômica

### Requirement: Autorização não substitui RLS
O sistema SHALL usar papel, membership e tenant atual como critérios complementares.
Papéis SHALL não substituir RLS e SHALL não autorizar leitura ou escrita cross-tenant
sem a política de isolamento correspondente.

#### Scenario: Papel não burlar isolamento
- **WHEN** um usuário com papel elevado tenta acessar dados cross-tenant
- **THEN** a autorização SHALL ser barrada pelo isolamento do tenant

