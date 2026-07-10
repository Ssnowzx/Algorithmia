# Fluxo de Desenvolvimento — Algorithmia

[← Inicio](README.md)

## 1. Antes de comecar

```bash
git checkout main
git pull origin main
git checkout -b feature/minha-feature   # ou fix/, refactor/, docs/
```

Confirme que o banco esta atualizado localmente:

```bash
php database/migrate.php
```

Suba o servidor de desenvolvimento:

```bash
php -S localhost:8001
```

Login de desenvolvimento: `masterboss@boss.com`. A senha é sorteada na criação da
conta e impressa uma vez. Para fixá-la: `DEMO_SENHA=qwe123 php database/migrate.php`.

## 2. Durante o desenvolvimento

### Regras de codigo (ver [padroes-de-codigo.md](padroes-de-codigo.md))

- Toda saida em HTML passa por `e()`.
- SQL apenas em Models e Services, nunca em controllers ou views.
- Operacoes de escrita multipla: usar `beginTransaction`.
- Novos endpoints de escrita: apenas POST + `exigirCsrf()`.

### Quando mudar o schema do banco

1. **Nao edite** `database/schema.sql` para adicionar uma coluna nova. O schema
   e o estado do zero (instalacoes limpas); migrations sao o caminho de evolucao.
2. Crie um arquivo em `database/migrations/` com nome `AAAAMMDD-descricao.sql`.
   Exemplo: `20251201-adiciona-indice-respostas-log.sql`.
3. O arquivo deve ser idempotente: use `IF NOT EXISTS`, `IF EXISTS`, `ADD COLUMN IF NOT EXISTS`, etc.
4. Apos criar a migration, atualize tambem `database/schema.sql` para refletir
   o estado final (instalacoes limpas ficam consistentes).
5. Execute localmente para verificar:
   ```bash
   php database/migrate.php
   ```

### Quando adicionar questoes ao banco

Ver [criacao-de-conteudo.md](criacao-de-conteudo.md). O seeder e idempotente
— pode rodar quantas vezes quiser sem duplicar perguntas.

### Sem `console.log` em commits

Remova todos os `console.log` antes de commitar. Use `grep -r 'console.log' public/js/`
para verificar.

## 3. Antes do commit

Checklist rapido:

```
[ ] Sem console.log em public/js/
[ ] Sem SQL em controllers ou views
[ ] Toda saida de dados do usuario passa por e()
[ ] Formularios POST tem csrf_field() e exigirCsrf()
[ ] Sem var_dump / print_r esquecidos
[ ] Se mudou schema: migration criada e schema.sql atualizado
[ ] Se adicionou questoes: seed-banco-questoes.php testado
```

## 4. Commit

Formato Conventional Commits (obrigatorio):

```bash
git add app/services/BatalhaService.php app/models/Inventario.php
git commit -m "fix: usa UPDATE atomico no inventario para evitar race condition"
```

Um commit = uma razao logica. Nao agrupar feature + bugfix no mesmo commit.

## 5. Migrations em bancos com dados

O migrador detecta automaticamente o que ja foi aplicado (tabela `migracoes_aplicadas`).
Rodar `php database/migrate.php` em banco existente e seguro — so aplica o que e novo.

O seed de conteudo (`seeds.sql`) so roda quando o banco esta vazio ou com `--reset`.
O seeder de questoes (`seed-banco-questoes.php`) e sempre idempotente — roda sempre.

```bash
# Opcoes do migrador
php database/migrate.php            # instala/atualiza, preserva dados
php database/migrate.php --schema   # so o schema + migrations (sem seeds)
php database/migrate.php --reset    # APAGA TUDO e recria (dev/staging apenas)
```

## 6. Push e PR

Antes do push:
```
[ ] php database/migrate.php (banco consistente)
[ ] Testar fluxo manual do que foi alterado (ver qa-e-testes.md)
[ ] Sem arquivos de .env, chaves ou senhas commitados
```

Push (requer confirmacao per regra global):
```bash
git push -u origin feature/minha-feature
```

Abra PR apontando para `main`. Descreva o que muda e como testar.

## 7. Deploy (producao)

Ver `docs/processo/DEPLOY.md` para o fluxo completo de VPS Ubuntu/Apache.

Resumo de update:
```bash
cd /var/www/algorithmia
sudo -u www-data git pull
sudo -u www-data DB_HOST=... DB_USER=... DB_PASS=... php database/migrate.php
sudo systemctl reload apache2
```

A migration e nao-destrutiva — preserva contas e progresso dos jogadores.
