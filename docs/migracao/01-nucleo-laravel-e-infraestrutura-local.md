# Etapa 1 - Nucleo Laravel e infraestrutura local

## Arquitetura local

- **Legado:** permanece na raiz do repositorio, em PHP puro + MySQL.
- **Nova plataforma:** vive em `/platform`, com Laravel 13+, PHP 8.4+,
  PostgreSQL 17+, Redis, Nginx e Mailpit.
- A plataforma nova nao depende do banco MySQL legado e nao compartilha
  credenciais com ele.
- O endpoint tecnico desta etapa e `GET /healthz`.

## Pre-requisitos

- Git.
- Docker Desktop ou Docker Engine com Docker Compose disponiveis localmente.
- Shell compativel com `./bin/platform` (Git Bash, WSL ou Linux/macOS).
- Nenhum PHP ou Composer no host e necessario para a operacao normal.
- Ambiente de rede capaz de puxar as imagens Docker na primeira execucao.

## Comandos de execucao

```bash
./bin/platform bootstrap
./bin/platform up
./bin/platform down
./bin/platform logs
./bin/platform test
./bin/platform lint
./bin/platform analyse
./bin/platform migrate
```

`bootstrap` executa Composer dentro do container `app` e faz a primeira
resolucao/revisao de dependencias, gerando ou atualizando `platform/composer.lock`
na primeira execucao real em Docker.

`up` deve criar `platform/.env` a partir de `platform/.env.example` se ele nao
existir, gerar segredos locais e subir apenas os servicos da plataforma nova.
`up` nao executa migrations.

`migrate` e explicito e nunca roda automaticamente no boot.

## URLs locais

- `http://localhost:8080` - Nginx da plataforma nova.
- `http://localhost:8080/healthz` - health check.
- `http://localhost:8025` - Mailpit.

## Variaveis obrigatorias

As variaveis vivem somente em `platform/.env`.

- `APP_KEY`
- `APP_URL`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `REDIS_HOST`, `REDIS_PORT`
- `MAIL_HOST`, `MAIL_PORT`

## Regras de seguranca

- PostgreSQL e Redis nao expoem portas para o host.
- Segredos reais nao sao versionados.
- O health check nao expõe DSN, usuario, senha, stack trace ou path interno.
- Nao ha seed de usuarios administrativos nem contas padrao de producao.

## Testes

Quando o ambiente permitir, executar:

```bash
./bin/platform bootstrap
./bin/platform test
./bin/platform lint
./bin/platform analyse
docker compose -f platform/compose.yaml config
```

E validar manualmente:

```bash
curl http://localhost:8080/healthz
```

## O que nao entra nesta etapa

- Tenancy, RLS e isolamento institucional.
- Autenticacao, cadastro, papeis ou convites.
- Migracao de conteudo, progresso, inventario, batalha ou ranking do legado.
- Starter kits de interface.
- Deploy em VPS, alteracao do legado ou conexao com MySQL existente.

## Homologacao

A homologacao completa da etapa depende de Docker/Compose funcionando no
ambiente local. Sem isso, a validacao fica restrita a inspeção estÃ¡tica e de
contrato dos arquivos.
