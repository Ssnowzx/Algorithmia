# Fundação multitenant

## Why

O [`roteiro-v1.html`](../../../docs/migracao/roteiro-v1.html) sempre teve como alvo uma
plataforma SaaS multi-institucional. O [`PLANO.md`](../../../docs/migracao/PLANO.md) v2.0
**adiou** essa parte, de propósito: sem escola interessada, `tenant_id` e RLS seriam
complexidade paga adiantado. O port foi construído sem eles, e por isso ficou pronto.

Adiar não é cancelar. Esta proposta traz a multitenancy de volta, com três diferenças
em relação à tentativa anterior (o branch `codex/fundacao-multitenant`, apagado em
2026-07-09):

1. **Ela nasce depois do motor, não antes.** Os 38 vetores-ouro existem, e o jogo
   inteiro está portado e testado. Aquele branch forkou antes de os testes existirem.
2. **Ela não bloqueia o corte.** O jogo vai para produção primeiro; a fundação entra
   depois, por deploys aditivos, com rollback pronto.
3. **Ela parte de um fato medido**, e não de uma suposição sobre o PostgreSQL.

### O fato medido, e ele muda tudo

O `PLANO.md §6` já avisava que "no PostgreSQL, o dono da tabela ignora as policies a
menos que se declare `FORCE ROW LEVEL SECURITY`, e um papel com `BYPASSRLS` passa
direto". A realidade do nosso banco é pior:

```
 conectado_como | superusuario | bypassrls
----------------+--------------+-----------
 algorithmia    | t            | t
```

E `algorithmia` é o **dono** de todas as tabelas. São **três** razões independentes
para o RLS ser completamente inerte hoje. Ligá-lo assim entregaria a pior coisa que
uma barreira de segurança pode entregar: a sensação de que existe.

> **Consequência de projeto:** RLS não é uma migration. É uma mudança de topologia de
> acesso — um papel de execução separado do papel que roda migrations. Sem isso, todo o
> resto é teatro.

## What Changes

> ⚠️ **Esta seção descreve o plano de 2026-07-09. Duas coisas mudaram ao construí-lo, e as
> letras das etapas junto.** O que valeu está em [`tasks.md`](tasks.md) e
> [`design.md`](design.md), e é lá que se olha:
>
> - **A ordem foi invertida:** `tenant_id` nas 13 tabelas entrou **antes** do corte, e não
>   depois. A regra dos três deploys é regra de *coexistência*, e antes do corte não há
>   código velho no ar com que coexistir. O rollback, ali, é trocar o DNS. Ver `design.md §4`.
> - **As etapas viraram cinco:** A (topologia), B (fundação), C (`tenant_id` no jogo),
>   D (turmas e papéis), E (piloto).
>
> **Todas as cinco foram entregues.** O corte em produção continua pendente, à espera da VPS.

Quatro etapas, e a ordem entre a segunda e o corte **não é negociável**.

### Etapa A — Topologia de acesso e fundação de tenancy (aditiva, segura)

- Papel de execução `algorithmia_app`: `NOSUPERUSER`, `NOBYPASSRLS`, sem `OWNER` de
  nada. A aplicação passa a conectar como ele. Migrations continuam rodando como dono,
  por uma conexão separada.
- Tabelas `tenants`, `tenant_dominios`, `tenant_membros`, `convites`, com
  `ENABLE` + `FORCE ROW LEVEL SECURITY` e policies sobre `current_setting('app.tenant_id')`.
- Resolução do tenant pelo `Host` da requisição. **Nunca** por parâmetro do cliente.
- O contexto do tenant é definido por requisição e **limpo ao final** — uma conexão
  reaproveitada pelo php-fpm que carregue o contexto do pedido anterior é um vazamento
  entre instituições.
- Testes de acesso cruzado: dois tenants, e nenhum enxerga o outro por Eloquent, por
  query crua, nem por comando de console.

### Etapa B — `tenant_id` nas 13 tabelas do jogo (invasiva)

**Só depois do corte em produção.** Cada tabela recebe `tenant_id` anulável, um
backfill para o tenant padrão, e só então a coluna vira obrigatória — três deploys
aditivos, e não um. RLS por tabela, com os mesmos testes cruzados.

### Etapa C — Turmas, papéis pedagógicos e relatórios

Fase 5 do roteiro v1. Depende de B.

### Etapa D — Piloto, feature flags e go-live

Fase 9 do roteiro v1. Depende de C.

**Entregue como Etapa E**, com uma correção de rumo que vale registrar: o roteiro v1 pedia
também um "dashboard administrativo mínimo", e a primeira leitura foi que ele exigiria o
`platform_admin` que a Etapa D.2 recusou. **Estava errada.** O painel entra em cada
instituição, uma de cada vez, pelo mesmo `ContextoDoTenant::usar()` de uma requisição HTTP —
sem conexão do dono, sem `BYPASSRLS`, com as policies de pé. O que a D.2 recusou era um papel
de banco que lê *através* das instituições, sem contexto; esse continua não existindo. Ver
`design.md §7`.

## Impact

- **Afeta:** `platform/` inteiro. O legado não é tocado.
- **Sequência obrigatória:** corte em produção → Etapa B. O corte carrega o schema que
  existir no dia; um schema recém-nascido, nunca exercitado contra dados reais, dobra o
  risco da janela.
- **Reversibilidade:** a Etapa A é puramente aditiva (tabelas novas, papel novo). A
  Etapa B não é: `tenant_id` obrigatório em 13 tabelas não se desfaz com um
  `git checkout`. Daí os três deploys.
- **Não entra agora:** Redis, filas, Horizon, Reverb, billing. Eles servem a
  necessidades que ainda não existem — a mesma razão pela qual saíram do v2.0.
