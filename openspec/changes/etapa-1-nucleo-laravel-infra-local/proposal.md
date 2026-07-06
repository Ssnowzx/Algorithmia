# Etapa 1 - Nucleo Laravel e infraestrutura local

## Why

O Algorithmia precisa de uma base nova em Laravel para evoluir sem romper o
legado PHP/MVC/MySQL. Esta etapa cria a fronteira inicial da nova plataforma em
`/platform`, com infraestrutura local reprodutivel, health check tecnico e
documentacao de coexistencia.

## What Changes

- Cria o novo nucleo em `/platform`.
- Adiciona Docker Compose local com PHP-FPM, Nginx, PostgreSQL, Redis e Mailpit.
- Adiciona wrapper `./bin/platform` para subir, derrubar, testar e analisar a
  plataforma.
- Adiciona health check `GET /healthz` sem vazamento de detalhes sensiveis.
- Adiciona Pest, Pint e Larastan/PHPStan como base de qualidade.
- Atualiza a governanca para separar claramente legado e platform.

## Fora de escopo

- Nenhuma alteracao no legado PHP/MVC/MySQL.
- Nenhuma migracao de dados, contas, personagens, fases, progresso ou inventario.
- Nenhuma implementacao de tenancy, RLS, autenticacao institucional ou fluxos
  de usuario.
- Nenhum starter kit de interface.
- Nenhum deploy de producao.
