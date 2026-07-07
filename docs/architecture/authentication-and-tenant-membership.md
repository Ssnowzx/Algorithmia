# Autenticacao global e membership por tenant

## 1. Identidade global

- `users` representa identidade global da plataforma.
- O e-mail e normalizado para minusculas antes de persistencia e consulta.
- A senha e armazenada com hash do Laravel.
- Nao existe cadastro publico nesta etapa.
- Nao existe credencial padrao, admin inicial ou usuario seedado permanente.

## 2. Sequencia de uma rota institucional autenticada

1. O `Host` e recebido pela middleware `ResolveTenantFromHost`.
2. O tenant ativo e resolvido pelo dominio.
3. O contexto PostgreSQL recebe `app.tenant_id` somente durante a transacao.
4. O Laravel autentica o usuario pela sessao web.
5. O middleware `EnsureActiveTenantMembership` revalida a membership ativa no tenant.
6. O controller executa somente se tenant, sessao e membership estiverem validos.

## 3. Login depende do tenant resolvido

- `GET /login` existe apenas em host institucional valido.
- `POST /login` verifica credenciais globais e membership ativa no tenant resolvido.
- Host global, dominio inexistente, dominio inativo e tenant suspenso retornam `404`.
- Credenciais invalidas, usuario inexistente, membership ausente ou suspensa retornam erro
  generico sem enumeraçao.

## 4. Sessao host-only

- O cookie de sessao nao usa dominio amplo.
- `SESSION_DOMAIN` permanece vazio por padrao.
- `HttpOnly` permanece ativo.
- `SameSite=Lax` e usado por padrao.
- Produção deve operar com `SESSION_SECURE_COOKIE=true` e HTTPS real.
- O login renova o identificador de sessao.
- O logout invalida a sessao e regenera o token CSRF.

## 5. Protecao contra enumeraçao

- Respostas de falha usam mensagem generica.
- O sistema nao informa se o e-mail existe.
- O sistema nao revela se a membership esta em outro tenant.
- O sistema nao revela se o tenant tem papel, dono ou qualquer dado interno.

## 6. Rate limiting

- A limitação de login usa tenant resolvido, IP e e-mail normalizado.
- Tentativas em um tenant nao bloqueiam outro tenant.
- Excesso de tentativas retorna `429 Too Many Requests`.

## 7. Revogacao e suspensao

- A membership e verificada em cada request institucional autenticada.
- Membership suspensa ou revogada bloqueia a proxima requisicao.
- O middleware limpa a sessao local quando detecta perda de acesso.
- O tenant continua sendo resolvido apenas pelo Host.

## 8. Limites desta etapa

- Sem roles de autorizacao de negocio.
- Sem convites.
- Sem recuperacao de senha.
- Sem SSO, Sanctum ou tokens API.
- Sem propagacao de identidade para jobs.
- Sem tabela de permissao granular.

## 9. Teste local seguro

Exemplos com Host explicito:

```bash
curl -i -H 'Host: tenant-a.algorithmia.test' http://127.0.0.1:8080/login
curl -i -H 'Host: tenant-a.algorithmia.test' http://127.0.0.1:8080/__tenant/auth-context
curl -i -H 'Host: host-inexistente.algorithmia.test' http://127.0.0.1:8080/login
```

## 10. Observacao operacional

Login e membership sao institucionais e permanecem dentro do grupo tenant-scoped. A rota
`/healthz` continua global e sem dependencia de tenant ou sessao.
