# Design — Fundação multitenant

## 1. O problema real do RLS aqui

Medido no banco de produção local, em 2026-07-10:

| Fato | Consequência |
|---|---|
| `algorithmia` é **superusuário** | superusuário ignora RLS, sempre |
| `algorithmia` tem `rolbypassrls` | segundo bypass, independente do primeiro |
| `algorithmia` é **dono** das tabelas | o dono ignora policies sem `FORCE ROW LEVEL SECURITY` |

O papel vem do `docker-entrypoint` do PostgreSQL: `POSTGRES_USER` é criado como
superusuário, e o banco nasce sob ele. Não é um erro de configuração nosso — é o
padrão da imagem, e ele é perfeitamente adequado enquanto não há multitenancy.

### Topologia proposta

```
  papel                  usa para              RLS
  ─────────────────────  ────────────────────  ──────────────────────────
  algorithmia (dono)     migrations, backup    ignora (é dono + super)
  algorithmia_app        a aplicação           SUJEITO às policies
```

- `algorithmia_app`: `NOSUPERUSER NOCREATEDB NOCREATEROLE NOBYPASSRLS`, sem `OWNER` de
  nada, com `GRANT SELECT, INSERT, UPDATE, DELETE` nas tabelas e `USAGE` nas sequências.
- Toda tabela tenant-scoped recebe `ENABLE ROW LEVEL SECURITY` **e**
  `FORCE ROW LEVEL SECURITY`. O `FORCE` é cinto-e-suspensório: se um dia alguém
  conectar a aplicação com o dono por engano, as policies continuam valendo.
- Duas conexões no `config/database.php`: `pgsql` (a aplicação, como `algorithmia_app`)
  e `pgsql_dono` (migrations). O `deploy.sh` já roda o migrate num container separado;
  ele passa `--database=pgsql_dono`.

> **Corrigido depois de implementar.** O plano dizia que `algorithmia:importar` também
> usaria o dono. Não usa: o único obstáculo era o `RESTART IDENTITY` do `TRUNCATE`, que
> exige *ser dono da sequência* — e era redundante, porque `sincronizarSequencias()` já
> punha cada sequência no `MAX(id)` importado. Removida a cláusula, a importação roda
> como a aplicação, e continuará rodando enquanto as tabelas do jogo não tiverem RLS.
> Na **Etapa C** isso muda: ou a importação passa a definir `app.tenant_id`, ou ela
> assume a conexão do dono. A decisão fica para lá, com o teste que a força.

**Verificação obrigatória, e ela é um teste:** conectado como `algorithmia_app`, sem
`app.tenant_id` definido, um `SELECT * FROM tenant_membros` devolve **zero linhas** —
e não um erro. Se devolver linhas, a barreira não existe, e nenhuma outra parte desta
mudança tem valor.

## 2. Como o contexto do tenant entra e sai

O ponto de vazamento não é a policy. É o **reaproveitamento de conexão**: o php-fpm
mantém a conexão viva entre requisições, e um `SET app.tenant_id` que sobreviva ao fim
do pedido entrega os dados da instituição A ao próximo pedido, que pode ser da B.

Duas opções, e escolhemos a segunda:

| | `SET LOCAL` em transação | `SET` + reset no `terminate` |
|---|---|---|
| Vaza se a requisição morrer? | Não (a transação morre junto) | Sim, se o reset não rodar |
| Funciona fora de transação? | Não | Sim |
| Custo | uma transação por requisição | um `SET` |

**Escolha: `SET LOCAL`, dentro de uma transação por requisição.** O custo é real, mas o
modo de falha da outra opção é "os dados de outra escola aparecem na tela", e ele é
silencioso. Um `DB::transaction()` que envolva a requisição inteira também dá
atomicidade de graça ao que hoje não a tem.

> Consequência: endpoints que fazem I/O longo (nenhum hoje) não podem viver dentro da
> transação. Se algum aparecer, ele sai do middleware e assume `SET LOCAL` explícito.

## 3. Resolução do tenant

Pelo `Host` da requisição, contra `tenant_dominios`. Nunca por parâmetro, cabeçalho ou
sessão — o `roteiro-v1 §4` chama isso de bloqueador de produção, e está certo: um
`tenant_id` vindo do cliente é um IDOR com nome bonito.

O `Host` chega ao Laravel via `trustProxies`. Já resolvemos isso no corte
([`RUNBOOK §9`](../../../docs/operacao/RUNBOOK.md)): com `TRUSTED_PROXIES` vazio e TLS
direto, `$request->getHost()` é o que o nginx recebeu. **Com um proxy na frente e
`TRUSTED_PROXIES` mal configurado, o `X-Forwarded-Host` é forjável** — e aí a resolução
de tenant se torna forjável junto. Isto liga a segurança do multitenant à correção do
§9, e o teste de proxy reverso passa a ser um teste de isolamento.

## 4. `tenant_id` nas tabelas do jogo — antes do corte, não depois

> **Revisado em 2026-07-10, depois de construir A e B. A versão anterior deste
> parágrafo dizia o contrário, e estava errada.**

O plano original mandava três deploys (`ADD COLUMN NULL` → backfill → `SET NOT NULL`) e
só **depois** do corte. As duas coisas caem pelo mesmo motivo.

**A regra dos três deploys existe porque migrations rodam contra o código velho ainda no
ar.** É uma regra de coexistência. Antes do corte, o port **nunca esteve em produção**:
não há código velho a preservar, e a coexistência não existe. Uma migration só basta.

**E o argumento de risco se inverte quando se olha o rollback.**

| | `tenant_id` antes do corte | `tenant_id` depois do corte |
|---|---|---|
| O que existe no banco do port | o que a importação trouxer | progresso real de alunos |
| Rollback | trocar o DNS de volta ao legado, cujo MySQL está intacto | restaurar dump; perde-se o que foi jogado desde então |
| Custo de um erro | reimportar | irreversível |

O que sustentava "depois" era: *"o corte carrega o schema que existir no dia; um schema
recém-nascido, nunca exercitado contra dados reais, dobra o risco da janela."* Isso teria
peso se o corte não fosse ensaiado. Ele é — o `§8` e o `§10` foram percorridos ponta a
ponta em Docker local, com as 1.306 linhas reais do legado, mais de uma vez.

**Portanto: a Etapa C acontece antes do corte, numa migration.** O que ela acopla, e não
dá para desacoplar:

RLS nas tabelas do jogo ⇒ toda requisição precisa de contexto ⇒ `TENANCY_ATIVA=true` ⇒
o host de produção precisa existir em `tenant_dominios` **antes** do primeiro pedido ⇒
e os comandos de console (`algorithmia:smoke`, `algorithmia:importar`) precisam definir
o contexto explicitamente, porque não têm `Host`.

Esse acoplamento é a razão de C ser uma etapa, e não uma migration solta.

## 5. O que NÃO entra

- **Redis, filas, Horizon, Reverb.** Saíram do v2.0 porque não há trabalho para eles.
  Continuam sem. Relatórios caros (roteiro §7) podem exigi-los; quando exigirem, entram
  com uma proposta que mostre a medição.
- **Billing e planos.** Não há cliente.
- **`content_packages` e versionamento de conteúdo** (roteiro Fase 3). É uma mudança de
  modelo de conteúdo, ortogonal a tenancy, e merece proposta própria. Sem ela, um
  override por instituição não existe — o que é aceitável para o piloto.

## 6. Riscos

| Risco | Controle |
|---|---|
| RLS inerte (papel errado) | teste que exige zero linhas sem contexto, rodando na CI |
| Contexto vazando entre requisições | `SET LOCAL` em transação; teste de duas requisições seguidas |
| `X-Forwarded-Host` forjado | `ProxyReversoTest` vira teste de isolamento |
| Backfill errado | `tenant_id` anulável por dois deploys; reconciliação por contagem |
| Migration não-aditiva no meio do corte | Etapa B só depois do corte, e o `tasks.md` trava a ordem |
