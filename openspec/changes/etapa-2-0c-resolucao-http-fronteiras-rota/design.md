## Context

A plataforma Laravel ja possui roles PostgreSQL separadas, RLS em `tenant_memberships`,
resolver de tenant por funcao SQL e `CurrentTenant` imutavel. A fronteira que ainda falta e
o ciclo HTTP: host confiavel, rotas globais versus tenant-scoped e limpeza de contexto entre
requisicoes.

## Goals / Non-Goals

**Goals:**
- Resolver tenant somente por `Host` confiavel.
- Negar host estruturalmente invalido com `400` e host desconhecido/inativo/suspenso com `404`.
- Garantir que rotas tenant-scoped executem com `CurrentTenant` e `app.tenant_id` ativos.
- Manter `/healthz` global e sem dependencia de tenant.
- Documentar proxy confiavel, hosts globais e exemplos de homologacao local.

**Non-Goals:**
- Criar autenticacao funcional, policies, gates ou CRUD administrativo.
- Criar novas migrations ou alterar RLS existente.
- Implementar jobs, filas, streaming ou propagacao de tenant fora da requisicao HTTP.

## Decisions

1. **Host e a unica entrada de tenant no HTTP**
   - O tenant sera resolvido apenas pelo host efetivo da requisicao, normalizado e validado.
   - Alternativa rejeitada: aceitar `tenant_id`, `slug` ou headers customizados como fallback.

2. **Rotas globais fora do middleware de tenant**
   - `/healthz` e outras rotas tecnicas globais nao passam por resolucao de tenant.
   - Alternativa rejeitada: aplicar o mesmo middleware em toda a aplicacao e tentar bypass por rota.

3. **Tenant-scoped com contexto transacional explicito**
   - O middleware tenant abre contexto com `TenantDatabaseContext` e limpa estado ao final.
   - Alternativa rejeitada: armazenar tenant em estado global ou reaproveitar contexto entre requests.

4. **Proxy confiavel opt-in**
   - `X-Forwarded-Host` so pode influenciar a resolucao quando proxies confiaveis estiverem configurados.
   - Alternativa rejeitada: aceitar forwarded headers por padrao.

5. **Resposta fechada por classe de erro**
   - Host malformado retorna `400`; dominio ausente, inativo, suspenso ou host global em rota tenant retorna `404`.
   - Alternativa rejeitada: redirecionar, revelar existencia do tenant ou retornar erro tecnico detalhado.

## Risks / Trade-offs

- [Risk] A ordem dos middlewares pode deixar o tenant inacessivel antes da abertura do contexto.
  Mitigation: middleware dedicado e rotas separadas por grupo.
- [Risk] Requisicoes consecutivas no mesmo worker podem vazar contexto se o cleanup falhar.
  Mitigation: `finally` obrigatorio para limpar `CurrentTenant` e depender de transacao local.
- [Risk] Configuracao errada de proxy pode aceitar host externo indevido.
  Mitigation: proxies confiaveis vazios por padrao e documentacao explicita.
