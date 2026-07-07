## 1. Autenticacao e sessao

- [x] 1.1 Criar configuracao `config/auth.php` para autenticacao web por sessao com o model global de usuario
- [x] 1.2 Ajustar configuracao de sessao e `.env.example` para cookie host-only e valores seguros
- [x] 1.3 Implementar login, logout e rota tecnica de contexto autenticado no escopo tenant-scoped

## 2. Membership e middleware

- [x] 2.1 Implementar middleware de membership ativa com limpeza segura em caso de revogacao
- [x] 2.2 Integrar o middleware nas rotas institucionais autenticadas
- [x] 2.3 Ajustar grants do runtime para leitura de `users` se necessario para autenticar

## 3. Rate limiting e testes

- [x] 3.1 Registrar rate limiting de login por tenant, IP e e-mail normalizado
- [ ] 3.2 Cobrir login, logout, membership, isolamento entre tenants e limpeza de contexto com testes
- [ ] 3.3 Validar `./bin/platform test`, `./bin/platform lint` e `./bin/platform analyse`

## 4. Documentacao e validacao

- [x] 4.1 Criar documentacao de autenticacao e membership por tenant
- [x] 4.2 Atualizar a documentacao de fronteiras HTTP de tenancy quando necessario
- [x] 4.3 Validar a nova OpenSpec com `openspec validate etapa-2-0d-autenticacao-sessao-vinculo-tenant`
