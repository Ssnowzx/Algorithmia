# Algorithmia Platform

Nova base tecnica do Algorithmia, isolada do legado PHP/MVC/MySQL.

## Execucao local

```bash
./bin/platform bootstrap
./bin/platform up
```

## Comandos auxiliares

```bash
./bin/platform bootstrap
./bin/platform down
./bin/platform logs
./bin/platform test
./bin/platform lint
./bin/platform analyse
./bin/platform migrate
```

## URLs locais

- `http://localhost:8080`
- `http://localhost:8080/healthz`
- `http://localhost:8025`

## Nota

Esta pasta nao substitui o legado nesta etapa. O legado permanece na raiz do
repositorio.
O `platform/composer.json` usa `license: proprietary` porque nao existe licenca
aberta aprovada para a plataforma.

## Bootstrap e lock

- `bootstrap` executa Composer dentro do container `app`.
- A primeira execucao Docker deve gerar ou atualizar `platform/composer.lock`.
- `up` nao resolve dependencias silenciosamente e nao substitui o bootstrap.
