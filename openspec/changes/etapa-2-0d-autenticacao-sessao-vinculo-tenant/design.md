## Context

A plataforma já resolve o tenant por host confiável e aplica RLS em `tenant_memberships`.
Esta etapa adiciona autenticação global por usuário com sessão segura, sempre dentro do
tenant resolvido, sem permitir que o cliente escolha o escopo institucional por dados
enviados no request.

## Goals / Non-Goals

**Goals:**
- Autenticar usuários globais por e-mail e senha.
- Exigir membership ativa no tenant resolvido antes de manter sessão institucional.
- Manter sessão host-only e renová-la em login e logout.
- Aplicar throttling por tenant, IP e e-mail normalizado.
- Preservar o isolamento por host e o contexto RLS já homologados.

**Non-Goals:**
- Cadastro público de usuários.
- Sanctum, tokens, OAuth, SSO, MFA ou recuperação de senha.
- Policies/Gates de negócio.
- Autorização por papel.
- Alterações no legado, MySQL ou infraestrutura pública.

## Decisions

1. **Identidade global**
   - `users` continua global e sem `tenant_id`.
   - O e-mail é a chave lógica da identidade e é normalizado para minúsculas.

2. **Sessão web host-only**
   - A sessão usa o driver já aprovado para a plataforma e não compartilha cookie entre domínios.
   - O login renova o identificador da sessão para reduzir risco de session fixation.

3. **Membership como requisito recorrente**
   - A autenticação só é aceita se o usuário tiver membership ativa no tenant resolvido.
   - A membership é revalidada em toda rota institucional autenticada.

4. **Falha fechada**
   - Login inválido, usuário inexistente, membership ausente ou suspensa retornam erro genérico.
   - Não há enumeração de usuário nem fallback para outro tenant.

5. **Throttling contextual**
   - A limitação de login é calculada com tenant resolvido, IP e hash do e-mail.
   - Tentativas em um tenant não afetam outro tenant.

## Risks / Trade-offs

- [Risk] Exigir membership a cada request aumenta o custo da rota autenticada. Mitigation: a
  consulta permanece curta e coberta por RLS.
- [Risk] A troca de contexto entre tenants pode confundir o armazenamento de cookie. Mitigation:
  cookie host-only e ausência de `SESSION_DOMAIN` amplo.
- [Risk] Rate limiting contextual pode ser sensível a mudanças de host em staging. Mitigation:
  chave explícita por tenant e IP, com testes de isolamento.
