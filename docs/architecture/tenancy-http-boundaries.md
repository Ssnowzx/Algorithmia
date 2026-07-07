# Tenancy HTTP Boundaries

## Fluxo de resolucao

1. A requisicao entra em uma rota tenant-scoped.
2. O middleware `ResolveTenantFromHost` le o `Host` efetivo da requisicao.
3. O host e normalizado para minusculas, com um ponto final removido quando existe e com
   porta de desenvolvimento descartada.
4. Se o host for estruturalmente invalido, a resposta e `400 Bad Request`.
5. Se o host estiver na lista de hosts globais da plataforma, a resposta e `404 Not Found`
   quando a rota exigir tenant.
6. Caso contrario, o middleware chama `public.resolve_active_tenant(text)` via
   `TenantResolver`.
7. Se nenhum tenant ativo for encontrado, a resposta e `404 Not Found`.
8. Quando o tenant existe, `CurrentTenant` e disponibilizado no container e
   `TenantDatabaseContext` abre uma transacao na conexao runtime.
9. O contexto PostgreSQL recebe `set_config('app.tenant_id', ..., true)` apenas durante a
   transacao da requisicao.
10. O contexto e limpo no `finally`, mesmo quando controller, service ou view lancam excecao.

## Rotas globais

Rotas globais nao passam por resolucao de tenant.

- `GET /healthz`
- `GET /`

Essas rotas podem responder em hosts globais como `localhost`, `127.0.0.1` e
`algorithmia.test`.

## Rotas tenant-scoped

Rotas tenant-scoped exigem host resolvido e contexto ativo. Nesta etapa existem rotas
de prova tecnica e de autenticacao:

- `GET /login`
- `POST /login`
- `POST /logout`
- `GET /__tenant/context`
- `GET /__tenant/auth-context`

O login, logout e o contexto autenticado compartilham o middleware `resolve.tenant`.
As rotas autenticadas tambem passam por `auth` e `ensure.active.membership`.

`GET /__tenant/context` continua como rota tecnica de prova e responde com payload
generico sem expor `tenant_id`, memberships ou dados sensiveis.

`GET /__tenant/auth-context` confirma, de forma segura, que o usuario autenticado possui
membership ativa no tenant resolvido e que o contexto de banco esta ativo.

## Normalizacao e rejeicao de host

O sistema aceita apenas o `Host` efetivo da requisicao. Nao usa `tenant_id`, `tenant`,
`slug`, cookies ou headers arbitrarios como fallback.

Regras:

- minusculas
- um ponto final residual opcional
- porta descartada do host de desenvolvimento
- sem protocolo
- sem caminho
- sem query string
- sem fragmento
- sem espacos
- sem host malformado

Respostas:

- `400 Bad Request` para host estruturalmente invalido
- `404 Not Found` para dominio inexistente, inativo, suspenso ou host global em rota tenant

## Proxy reverso

O projeto nao confia em `X-Forwarded-Host` por padrao. A resolucao usa o `Host` efetivo da
requisicao e so deve evoluir para proxies confiaveis quando houver configuracao explicita.

Hosts globais da plataforma sao definidos por ambiente:

```env
TENANCY_PLATFORM_HOSTS=localhost,127.0.0.1,algorithmia.test
TENANCY_TRUSTED_PROXIES=
```

## Ciclo de vida do CurrentTenant

- e criado somente depois que um dominio ativo valida o host
- fica disponivel durante a requisicao tenant-scoped
- e removido do container no `finally`
- nao deve sobreviver a requisicoes subsequentes no mesmo worker

## Ciclo de vida da transacao

- rotas globais nao abrem transacao de tenant
- rotas tenant-scoped abrem transacao antes da consulta de dominio e antes do controller
- `app.tenant_id` existe somente na transacao atual
- rollback acontece quando a rota falha
- a limpeza e automatico ao final da requisicao

## Ordem dos middlewares institucionais autenticados

Para rotas autenticadas do tenant, a ordem efetiva e:

1. `resolve.tenant`
2. `auth`
3. `ensure.active.membership`
4. controller

Essa ordem garante que:

- o tenant vem apenas do Host
- a sessao e validada depois do tenant resolvido
- a membership e revalidada a cada requisicao
- o contexto RLS permanece na transacao da requisicao

## Limitacoes conhecidas

- streaming, downloads e SSE nao foram modelados nesta etapa
- jobs de fila nao recebem propagacao automatica de tenant
- autenticacao funcional ainda nao existe
- a rota interna de prova tecnica e temporaria e nao substitui o desenho futuro de produto

## Homologacao local

Exemplos sem depender de DNS publico:

```bash
curl -i http://127.0.0.1:8080/healthz
curl -i -H 'Host: tenant-a.algorithmia.test' http://127.0.0.1:8080/__tenant/context
curl -i -H 'Host: host-inexistente.algorithmia.test' http://127.0.0.1:8080/__tenant/context
curl -i -H 'Host: localhost' http://127.0.0.1:8080/__tenant/context
```
