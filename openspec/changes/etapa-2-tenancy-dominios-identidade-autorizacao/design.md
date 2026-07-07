## Context

A plataforma Laravel em `/platform` já está isolada, com PostgreSQL, Redis, Docker
Compose, health check e CI verdes. A Etapa 2 precisa transformar isso em uma base
multitenant segura, onde o tenant é resolvido por domínio antes de qualquer consulta
tenant-aware e o isolamento é reforçado por RLS no PostgreSQL.

O legado PHP/MySQL continua na raiz do repositório e não deve ser alterado por esta
etapa de desenho.

## Goals / Non-Goals

**Goals:**
- Formalizar tenancy compartilhada por linha com `tenant_id`.
- Definir resolução de tenant por host confiável e `CurrentTenant` imutável.
- Definir identidade global, memberships e autorização em duas camadas.
- Definir a estratégia de RLS e de contexto transacional para a aplicação.
- Deixar o próximo passo de implementação inequívoco e auditável.

**Non-Goals:**
- Criar migrations, tabelas, seeds, endpoints, middlewares ou policies.
- Instalar pacotes de autenticação ou autorização.
- Alterar Docker, CI, legado ou qualquer comportamento de jogo.

## Decisions

1. **Tenant compartilhado por linha**
   - Cada tabela tenant-aware futura conterá `tenant_id`.
   - Alternativa rejeitada: banco por tenant. O custo operacional é maior e contradiz o
     monólito modular aprovado para a plataforma.

2. **Tenant resolvido por domínio antes do acesso a dados**
   - O host confiável será normalizado, validado e mapeado para `tenant_domains`.
   - Alternativa rejeitada: inferir tenant pelo usuário autenticado. Isso mistura
     autenticação com escopo de dados e enfraquece o isolamento.

3. **`CurrentTenant` imutável por requisição**
   - O resultado do resolver será congelado em um objeto de contexto para evitar mutação
     tardia e inconsistência entre middlewares.
   - Alternativa rejeitada: armazenar apenas estado global mutável.

4. **RLS como barreira de segurança obrigatória**
   - A aplicação não terá `SUPERUSER`, `BYPASSRLS` nem propriedade das tabelas de runtime.
   - O contexto do tenant será carregado via `SET LOCAL app.tenant_id` ou equivalente.
   - Alternativa rejeitada: confiar apenas em global scopes ou filtros de ORM.

5. **Identidade global, memberships por tenant**
   - `users` representa identidade da pessoa; o vínculo com tenants vive em
     `tenant_memberships`.
   - Alternativa rejeitada: usuário por tenant. Isso impede pertença múltipla e complica
     auditoria e migração.

6. **Autorização em camadas**
   - A camada de plataforma terá papel administrativo global restrito; a camada de tenant
     terá `owner`, `admin`, `manager` e `member`.
   - Alternativa rejeitada: um único papel global com permissões implícitas por cliente.

## Risks / Trade-offs

- [Risk] Resolver tenant cedo demais pode bloquear rotas globais como `/healthz` → Mitigation:
  manter uma lista explícita de rotas globais fora da resolução.
- [Risk] RLS mal configurado pode bloquear operações administrativas legítimas → Mitigation:
  separar runtime de manutenção/bootstrap e documentar contexto específico.
- [Risk] Autorização baseada apenas em papel pode vazar poder entre tenants → Mitigation:
  combinar papel + membership + tenant atual em toda decisão.
- [Risk] A migração futura pode exigir ajustes na ordem dos middlewares e na configuração de
  proxy confiável → Mitigation: documentar critérios de aceite e pontos de reversão agora.
