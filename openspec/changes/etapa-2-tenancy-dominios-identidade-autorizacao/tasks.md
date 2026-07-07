## 1. Tenancy e resolução

- [ ] 1.1 Documentar o modelo de `tenants`, `tenant_domains`, `users` e `tenant_memberships` na spec de tenancy
- [ ] 1.2 Formalizar o contrato de `TenantResolver`, host normalizado e `CurrentTenant` imutável
- [ ] 1.3 Definir rotas globais, rotas de plataforma e rotas tenant-aware sem implementação

## 2. RLS e isolamento

- [ ] 2.1 Especificar a estratégia de RLS no PostgreSQL com falha fechada e contexto por requisição
- [ ] 2.2 Registrar critérios para bootstrap, manutenção e migração sem bypass por `withoutGlobalScopes()`
- [ ] 2.3 Definir cenários positivos e negativos de isolamento cross-tenant

## 3. Identidade e autorização

- [ ] 3.1 Documentar identidade global, verificação de e-mail, redefinição de senha e proteção contra enumeração
- [ ] 3.2 Documentar memberships por tenant e papéis `owner`, `admin`, `manager` e `member`
- [ ] 3.3 Especificar regras de promoção, rebaixamento, suspensão e proteção do último owner

## 4. ADRs e validação

- [ ] 4.1 Criar ADRs 007, 008 e 009 com decisões, alternativas, riscos e reversibilidade
- [ ] 4.2 Validar a OpenSpec da etapa com `openspec validate etapa-2-tenancy-dominios-identidade-autorizacao`
- [ ] 4.3 Confirmar `git diff --check` e `git status --short` sem código executável novo
