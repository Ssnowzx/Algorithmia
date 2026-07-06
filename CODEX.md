# CODEX.md — Regras de Execução para Algorithmia

> Este arquivo define como agentes Codex devem interpretar prompts, analisar o repositório e executar mudanças no projeto **Algorithmia**.
>
> Objetivo: conduzir a migração incremental do jogo atual em PHP/MVC para uma plataforma **Laravel + PostgreSQL + Redis**, com arquitetura **multitenant segura**, sem decisões implícitas, sem vazamento entre instituições e sem reescritas desnecessárias.

---

## 1. Princípios inegociáveis

1. **Segurança e isolamento entre tenants são requisitos funcionais**, não detalhes de implementação.
2. **Nenhuma mudança deve quebrar regras validadas do jogo** sem uma decisão explícita e documentada.
3. **Nenhum dado de um tenant pode ser acessado, inferido, alterado ou excluído por outro tenant.**
4. O backend é a fonte de verdade para autenticação, permissões, estado de batalha, correção de desafios, recompensas, progresso, reputação, inventário e ranking.
5. O cliente nunca recebe respostas corretas, segredos, hashes, tokens, credenciais, dados internos de auditoria ou informações de outros tenants.
6. Não criar credenciais padrão, contas administrativas inseguras ou seeds com senha pública.
7. Não executar tarefas destrutivas, migrações irreversíveis, resets, drops ou alterações em massa sem uma instrução explícita no prompt.
8. Não expandir o escopo da tarefa por iniciativa própria.
9. Toda decisão técnica não óbvia deve ficar documentada.
10. Toda entrega deve ser comprovada por testes ou evidências verificáveis.

---

## 2. Arquitetura-alvo estabelecida

A direção arquitetural do projeto é:

```text
Backend:       Laravel
Banco:         PostgreSQL
Cache/filas:   Redis
Tenancy:       tenant_id + resolução por domínio/subdomínio + RLS no PostgreSQL
Arquitetura:   monólito modular, API-first quando aplicável
Segurança:     políticas de autorização, RLS, auditoria e testes de isolamento
```

### Regras de arquitetura

- Não transformar o sistema em microserviços sem decisão explícita.
- Não criar um backend Node, Spring, Go ou outra tecnologia paralela sem pedido expresso.
- Não mudar o frontend, biblioteca UI ou estratégia de renderização sem uma etapa própria aprovada.
- Não duplicar regras de domínio entre controllers, componentes de UI e jobs.
- Regras de jogo devem residir em serviços/domínios testáveis, não em views ou JavaScript do cliente.
- Controllers, actions, commands e jobs devem ser finos: validar entrada, autorizar, chamar serviços e devolver resposta.
- Todo acesso a dados deve usar modelos, repositórios, query objects ou serviços definidos pelo padrão adotado no repositório.

---

## 3. Leitura obrigatória antes de alterar código

Antes de criar, editar ou excluir qualquer arquivo, o Codex deve:

1. Ler `README.md` e a documentação diretamente relacionada à tarefa.
2. Ler `CODEX.md`, regras de agentes, `.cursor/rules/`, `.codex/`, `openspec/` e demais instruções locais existentes.
3. Inspecionar os módulos, migrations, testes e convenções afetados.
4. Identificar dependências, rotas, modelos, políticas, jobs, eventos e contratos que serão impactados.
5. Informar, antes da implementação, qualquer ambiguidade ou conflito real entre o prompt e a arquitetura existente.

Não assumir que um arquivo, regra ou padrão exista. Confirmar pelo repositório.

---

## 4. Fluxo obrigatório de trabalho

Para tarefas relevantes, seguir esta sequência:

1. **Entender**: localizar código, documentação, regras, testes e dependências afetadas.
2. **Delimitar**: declarar o escopo, os arquivos prováveis e o que ficará fora da tarefa.
3. **Especificar**: criar ou atualizar a proposta OpenSpec quando a mudança alterar domínio, segurança, fluxo de usuário, banco, APIs ou arquitetura.
4. **Implementar**: fazer a menor alteração coerente que satisfaça o escopo.
5. **Testar**: adicionar ou ajustar testes automatizados apropriados.
6. **Validar**: executar os comandos disponíveis de teste, lint, análise estática e build.
7. **Documentar**: atualizar documentação, ADRs, contratos ou runbooks necessários.
8. **Entregar**: apresentar resumo, arquivos alterados, decisões, testes executados e pendências reais.

### Não permitido

- Pular testes porque a alteração “parece simples”.
- Fazer refatorações amplas não solicitadas.
- Substituir código funcional por abstrações genéricas sem benefício mensurável.
- Alterar API, schema ou comportamento do jogo silenciosamente.
- Declarar sucesso sem executar ou explicar claramente a impossibilidade de executar validações.

---

## 5. Regras de multitenancy

### 5.1 Fonte de verdade do tenant

- O tenant deve ser resolvido exclusivamente no servidor, por domínio, subdomínio, rota interna protegida ou contexto de plataforma explicitamente autorizado.
- `tenant_id` nunca pode ser aceito como fonte de verdade em query string, formulário, payload JSON, header livre ou cookie controlado pelo cliente.
- O contexto do tenant deve ser imutável durante a requisição.
- A ausência de tenant em uma rota tenant-scoped deve falhar de modo seguro.

### 5.2 Dados tenant-scoped

Toda tabela que contenha informação exclusiva de uma instituição deve possuir `tenant_id` obrigatório, chave estrangeira, índice apropriado e política RLS.

Exemplos típicos:

```text
tenant_domains
tenant_memberships
tenant_settings
tenant_branding
classes
enrollments
content_packages
tenant_content_overrides
characters
phase_progress
answer_logs
leaderboards
reports
audit_logs
```

### 5.3 PostgreSQL RLS

Para tabelas tenant-scoped:

1. Habilitar `ROW LEVEL SECURITY`.
2. Aplicar `FORCE ROW LEVEL SECURITY` quando apropriado.
3. Criar policies para `SELECT`, `INSERT`, `UPDATE` e `DELETE`.
4. Usar contexto definido por transação com `SET LOCAL app.tenant_id = ...`.
5. Usar uma role de aplicação sem `BYPASSRLS` e, sempre que possível, distinta da role proprietária das tabelas.
6. Garantir que conexões reutilizadas não preservem contexto de tenant entre requisições.
7. Criar testes que provem que consultas sem filtro explícito continuam bloqueadas pelo RLS.

### 5.4 Consultas e escopos

- Todo modelo tenant-scoped deve ter escopo de tenant ou mecanismo equivalente.
- Não usar `withoutGlobalScopes()`, query bruta, conexão sem contexto ou acesso cross-tenant sem justificativa explícita, autorização de plataforma e teste correspondente.
- Consultas administrativas globais devem rodar em fluxo separado e auditado.
- Índices devem refletir os padrões reais de consulta, normalmente começando por `tenant_id`.

### 5.5 Testes mínimos de isolamento

Para qualquer novo módulo multitenant, criar testes que provem:

1. Tenant A não lê dados do tenant B.
2. Tenant A não altera nem remove dados do tenant B.
3. Tenant A não cria registros vinculados ao tenant B.
4. Um usuário com vínculo em A não recebe acesso automático a B.
5. Requisições sem contexto de tenant falham com segurança.
6. Uma conexão reutilizada não herda o contexto do tenant anterior.
7. RLS bloqueia acesso cruzado mesmo quando o código esquece o filtro de tenant.

---

## 6. Autenticação, autorização e papéis

### Papéis mínimos

```text
platform_admin  → operação global da plataforma
tenant_admin    → administrador da instituição
teacher         → professor/autor autorizado
student         → jogador/aluno
```

### Regras

- Um usuário pode pertencer a múltiplos tenants, com papéis diferentes em cada um.
- Autorização deve considerar **usuário + membership + tenant + recurso**.
- Não usar apenas verificações de papel global para proteger recursos tenant-scoped.
- Alterações de papel, domínio, vínculo, convite, acesso administrativo e ações sensíveis devem gerar audit log.
- Senhas devem usar o mecanismo recomendado pelo framework; nunca armazenar senha em texto puro.
- Tokens devem ser de escopo mínimo, expiração adequada e nunca aparecer em logs ou respostas de erro.

---

## 7. Motor de jogo e integridade pedagógica

### Fonte de verdade

O backend decide:

- correção de respostas;
- dano recebido e causado;
- combos;
- ataques especiais;
- consumo de poções e itens;
- uso de Fragmentos da IA;
- reputação;
- XP, ouro, conquistas e drops;
- desbloqueio de fases;
- progressão e maestria;
- ranking e estatísticas.

### Regras obrigatórias

- O gabarito nunca pode ser enviado ao frontend antes da resolução do desafio.
- O cliente não escolhe dano, recompensa, reputação, nível, inventário, status de batalha ou tenant.
- Recompensas devem ser idempotentes.
- Ações críticas devem ser protegidas contra repetição de requisições e concorrência.
- O estado de batalha deve ter uma política explícita de persistência, expiração e recuperação.
- Qualquer portabilidade da lógica antiga deve começar por testes de caracterização/golden master.

### Testes de regressão de domínio

Antes de substituir uma regra existente, criar cenários que comparem:

```text
estado inicial + sequência de ações + resultado esperado
```

Cobrir, no mínimo:

- acerto e erro;
- combo;
- ataque especial;
- poções;
- uso de Fragmento da IA;
- reputação;
- morte súbita;
- vitória e derrota;
- recompensa única;
- persistência de progresso;
- repetição de requisição;
- conteúdo sem desafios;
- tentativa de acesso a fase bloqueada.

---

## 8. Banco de dados, migrations e dados legados

### Migrations

- Migrations devem ser pequenas, reversíveis quando tecnicamente possível e nomeadas de forma clara.
- Nunca editar uma migration já aplicada em ambiente compartilhado; criar uma nova.
- Não incluir dados sensíveis em migrations.
- Alterações de tabelas grandes devem considerar impacto de lock, índices e rollback.
- Para mudanças de dados, preferir comandos versionados, jobs controlados ou scripts explícitos.

### Migração do legado

- Não apagar, sobrescrever ou alterar o banco legado sem instrução expressa.
- Tratar dados legados como fonte de importação, não como banco de produção do novo sistema.
- Cada importador deve ser idempotente, auditável e capaz de gerar relatório de inconsistências.
- Toda importação deve declarar como resolve:
  - tenant de destino;
  - duplicidade de e-mail;
  - usuários em múltiplas instituições;
  - chaves antigas;
  - conteúdo incompleto;
  - registros órfãos;
  - charset e normalização;
  - rollback ou reexecução segura.

### Seeds

- Seeds de desenvolvimento devem ser explicitamente marcados como dev/test.
- Não criar admin padrão em produção.
- Secrets de demonstração só podem existir por variáveis de ambiente seguras e nunca como padrão público.

---

## 9. APIs, eventos e contratos

- Toda API nova deve ter versionamento coerente, por exemplo `/api/v1/...`.
- Validar entrada com Form Requests ou mecanismo equivalente.
- Autorizar a ação antes de acessar ou alterar recursos.
- Respostas devem ter formato consistente e erros seguros.
- Não retornar stack traces, SQL, paths internos ou mensagens sensíveis em produção.
- Mudanças de contrato devem atualizar documentação e testes de integração.
- Para rotas críticas, documentar payload, resposta, códigos de erro e autorização exigida.

### Regras para AJAX/WebSocket

- CSRF, autenticação e autorização devem ser tratados conforme o canal utilizado.
- Eventos em tempo real precisam carregar contexto de tenant e autorizar assinaturas de canais privados.
- Rankings, notificações e estado de batalha não podem vazar entre tenants.

---

## 10. Qualidade, testes e validação

### Obrigatório para cada tarefa

- Teste unitário para regra de domínio relevante.
- Teste de feature/integration para fluxo HTTP, autorização ou persistência relevante.
- Teste de isolamento multitenant quando o módulo for tenant-scoped.
- Teste de regressão quando alterar regra do jogo existente.

### Comandos esperados

Executar os comandos realmente configurados no repositório. Em um projeto Laravel típico, considerar:

```bash
php artisan test
php artisan pint --test
phpstan analyse
npm run lint
npm run build
```

Não afirmar que algum comando foi executado se ele não foi executado. Caso uma ferramenta não exista, registrar isso como pendência e não inventar resultado.

---

## 11. OpenSpec, ADRs e documentação

Criar ou atualizar uma proposta OpenSpec antes de implementar mudanças que afetem:

- multitenancy;
- autenticação e autorização;
- RLS;
- schema;
- APIs públicas;
- fluxo do jogo;
- regras de batalha;
- dados legados;
- billing;
- observabilidade;
- deploy;
- arquitetura.

Criar ADR em `docs/adr/` quando houver decisão estrutural, por exemplo:

- estratégia de resolução de tenant;
- modelo RLS;
- estratégia de autenticação;
- persistência de batalhas;
- arquitetura de API/frontend;
- versionamento de conteúdo;
- critérios de ranking.

Documentação deve explicar o **porquê**, não apenas listar arquivos.

---

## 12. Formato obrigatório para prompts de implementação

Todo prompt enviado ao Codex deve conter, nesta ordem, os blocos abaixo:

```text
1. Contexto do projeto
2. Objetivo da etapa
3. Decisões já fechadas
4. Escopo permitido
5. Fora de escopo
6. Requisitos técnicos obrigatórios
7. Requisitos de segurança e tenancy
8. Dados, migrations e compatibilidade
9. Testes obrigatórios
10. Critérios objetivos de aceite
11. Documentação esperada
12. Formato da entrega final
```

### Template de prompt

```text
Você está trabalhando no repositório Algorithmia.

Leia primeiro README.md, CODEX.md, docs/, openspec/ e regras locais relevantes.
Não altere arquivos fora do escopo sem explicar a necessidade.

## Contexto
[descrever o módulo e o estado atual]

## Objetivo
[resultado observável que deve existir ao fim]

## Decisões já fechadas
- Backend: Laravel
- Banco: PostgreSQL
- Cache/filas: Redis
- Multitenancy: tenant_id + resolução segura + RLS
- [demais decisões específicas]

## Escopo permitido
- [itens claros]

## Fora de escopo
- [itens proibidos nesta etapa]

## Requisitos técnicos
- [migrations, models, services, APIs, jobs, UI, etc.]

## Segurança e multitenancy
- tenant_id nunca vem do cliente;
- [policies/RLS/auditoria específicas]

## Dados e migrations
- [regras de importação, idempotência, rollback]

## Testes obrigatórios
- [cenários concretos]

## Critérios de aceite
- [condições verificáveis]

## Documentação esperada
- [arquivos, ADRs, OpenSpec, API docs]

## Entrega final
Informe:
1. resumo da implementação;
2. decisões tomadas;
3. arquivos criados e alterados;
4. migrations e dados afetados;
5. testes executados e resultados reais;
6. pendências, riscos ou bloqueios reais.
```

---

## 13. Formato obrigatório da resposta final do Codex

Ao concluir uma tarefa, o Codex deve responder com:

```text
## Implementado
- [mudanças concluídas]

## Decisões técnicas
- [decisões e justificativas curtas]

## Arquivos alterados
- [lista de arquivos]

## Banco e migrations
- [migrations criadas, dados afetados, reversibilidade]

## Testes executados
- [comandos e resultados reais]

## Segurança e tenancy
- [como o isolamento foi aplicado e testado]

## Pendências ou riscos
- [somente fatos reais]
```

Não usar afirmações vagas como “feito”, “seguro”, “pronto para produção” ou “testado” sem evidência concreta.

---

## 14. Sinais de parada obrigatória

O Codex deve interromper a implementação e relatar o problema quando:

- o prompt exige decisão de produto ainda não definida;
- a tarefa conflita com este arquivo ou documentação superior;
- há risco de perda de dados;
- não é possível garantir isolamento entre tenants;
- a alteração exigiria reescrever módulos fora do escopo;
- há ambiguidade sobre comportamento de gameplay que muda resultados existentes;
- há credenciais, segredos ou dados pessoais reais expostos no repositório;
- um teste essencial não pode ser criado ou executado por limitação concreta.

Ao parar, deve apresentar opções técnicas claras e o impacto de cada uma. Não inventar decisões silenciosamente.

---

## 15. Regra final

A melhor alteração não é a maior, a mais abstrata ou a mais rápida de escrever.

A melhor alteração é a menor mudança verificável que preserva o jogo, protege os dados das instituições e deixa o Algorithmia mais preparado para operar como uma plataforma multitenant confiável.
---

## Addendum - convivencia legado e platform

- **Legado:** raiz do repositÃ³rio, PHP puro, MVC artesanal, MySQL e regras ja
  validadas do jogo.
- **Platform:** `/platform`, nova base Laravel 13+, PHP 8.4+, PostgreSQL 17+ e
  Redis.
- Regras do legado continuam validas onde o codigo legado vive.
- Regras de platform valem somente para arquivos dentro de `/platform` e para a
  sua documentacao especifica.
- Nao misturar banco, rotas, credenciais, deploy ou infraestrutura entre os
  dois contextos.
