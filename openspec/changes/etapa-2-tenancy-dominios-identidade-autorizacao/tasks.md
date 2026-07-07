## 1. Tenancy e resolucao

- [x] 1.1 Documentar o modelo de `tenants`, `tenant_domains`, `users` e `tenant_memberships` na spec de tenancy
- [x] 1.2 Formalizar o contrato de `TenantResolver`, host normalizado e `CurrentTenant` imutavel
- [x] 1.3 Definir rotas globais, rotas de plataforma e rotas tenant-aware sem implementacao

## 2. RLS e isolamento

- [x] 2.1 Especificar a estrategia de RLS no PostgreSQL com falha fechada e contexto por requisicao
- [x] 2.2 Registrar criterios para bootstrap, manutencao e migracao sem bypass por `withoutGlobalScopes()`
- [x] 2.3 Definir cenarios positivos e negativos de isolamento cross-tenant

## 3. Identidade e autorizacao

- [x] 3.1 Documentar identidade global, verificacao de e-mail, redefinicao de senha e protecao contra enumeracao
- [x] 3.2 Documentar memberships por tenant e papeis `owner`, `admin`, `manager` e `member`
- [x] 3.3 Especificar regras de promocao, rebaixamento, suspensao e protecao do ultimo owner

## 4. ADRs e validacao

- [x] 4.1 Criar ADRs 007, 008 e 009 com decisoes, alternativas, riscos e reversibilidade
- [x] 4.2 Validar a OpenSpec da etapa com `openspec validate etapa-2-tenancy-dominios-identidade-autorizacao`
- [x] 4.3 Confirmar `git diff --check` e `git status --short` sem codigo executavel novo
