## 1. Resolucao HTTP de tenant

- [x] 1.1 Criar ou ajustar normalizacao segura de host para aceitar porta de desenvolvimento sem armazenala no dominio
- [x] 1.2 Implementar middleware de resolucao de tenant por host com respostas 400 e 404 por classe de erro
- [x] 1.3 Garantir limpeza de `CurrentTenant` e de `app.tenant_id` ao final da requisicao

## 2. Fronteiras de rota

- [x] 2.1 Separar rotas globais de plataforma e rotas tenant-scoped
- [x] 2.2 Criar rota interna minima tenant-scoped para prova tecnica
- [x] 2.3 Manter `/healthz` global e fora da resolucao de tenant

## 3. Configuracao e documentacao

- [x] 3.1 Ajustar configuracao de hosts globais e trusted proxies
- [x] 3.2 Documentar o fluxo HTTP, codigos de resposta e limites conhecidos

## 4. Testes e validacao

- [x] 4.1 Cobrir resolucao, rejeicoes, contexto e limpeza entre requisicoes
- [ ] 4.2 Validar OpenSpec, testes, Pint e PHPStan
