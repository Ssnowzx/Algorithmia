# Releases e Versionamento — Algorithmia

[← Inicio](README.md)

## Versionamento semantico

O projeto usa versionamento semantico: `MAJOR.MINOR.PATCH`.

| Incremento | Quando |
|---|---|
| `MAJOR` | Quebra de compatibilidade com dados existentes (ex.: schema incompativel sem migration) |
| `MINOR` | Nova feature sem quebrar nada (nova regiao de fases, nova classe jogavel) |
| `PATCH` | Bugfix, ajuste de balanceamento, melhoria de assets |

Versao atual: ver commit mais recente em `main` com tag `vX.Y.Z`.

## Fluxo de release

```
feature/* ou fix/* ou refactor/*
      |
      |  PR -> main (code review)
      v
    main
      |
      |  Testes manuais (qa-e-testes.md)
      |  Tag de versao
      v
   Deploy VPS (docs/processo/DEPLOY.md)
```

## Checklist de publicacao (release)

```
[ ] Branch mergeada em main via PR aprovado
[ ] git tag vX.Y.Z -m "descricao do release"
[ ] php database/migrate.php (local e staging, se houver)
[ ] Testes manuais das areas alteradas (qa-e-testes.md)
[ ] Sem console.log em public/js/
[ ] Sem var_dump / print_r no PHP
[ ] Sem credenciais ou .env commitados
[ ] docs/processo/HANDOFF-SESSAO.md atualizado com o que mudou (se sessao relevante)
[ ] Se mudanca visual relevante: snapshot em docs/evolucao-visual/
[ ] Deploy: git pull + php database/migrate.php + apache reload
[ ] Verificar no navegador em producao
```

## Historico de releases relevantes

| Versao/Marco | Descricao |
|---|---|
| v1 — Pixel Art | Sprites autorais em pixel art (mestres, inimigos, itens, heroi). |
| v2 — Ilustracoes | Arte ilustrada de alta resolucao; recorte com rembg; bestiario e codex. |
| Auditoria (2026-06) | Auditoria de 6 areas; logout corrigido; schema sincronizado; assets reorganizados. |
| Fix deploy (2026-06) | Banco de questoes ampliado no host: `banco-questoes.sql` idempotente, `seed_remote.sh` corrigido, docs de deploy alinhados (perguntas deixaram de repetir). |

Ver historico detalhado em [timeline-evolucao.md](timeline-evolucao.md).

## Deploy de atualizacao em producao

Sem downtime — a migration e nao-destrutiva:

```bash
cd /var/www/algorithmia
sudo -u www-data git pull
sudo -u www-data DB_HOST=127.0.0.1 DB_NAME=algorithmia \
     DB_USER=algorithmia DB_PASS='SENHA' php database/migrate.php
sudo systemctl reload apache2
```

Para adicionar HTTPS depois: ver secao em `docs/processo/DEPLOY.md`.
