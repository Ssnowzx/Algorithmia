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

## 7. O console do operador — e por que ele não contradiz a D.2

> **Escrito na Etapa E, depois de o usuário pedir explicitamente a tela.** A resposta
> inicial foi "isso exige furar o RLS, e a D.2 recusou". **Estava errada.** Há um caminho
> que entrega o dashboard cross-tenant sem tocar na barreira, e ele estava à vista.

A Etapa D.2 recusou o `platform_admin` do roteiro v1 com uma frase: *"ele lê através das
instituições, e o RLS existe para impedir exatamente isso."* A frase continua verdadeira. O
que ela descreve, porém, é **um papel de banco** que lê todas as escolas de uma vez, sem
contexto. Não é a única forma de montar um painel.

**O RLS garante que um pedido não enxerga fora do seu contexto. Ele nunca prometeu que o
servidor não pode escolher o contexto** — é o servidor que o escolhe, a cada requisição, a
partir do `Host`. Um painel que entra em cada instituição, uma de cada vez, pelo mesmo
`ContextoDoTenant::usar()` que uma requisição HTTP usa, faz N leituras, cada uma dentro de um
contexto declarado, cada uma sujeita às policies. `FORCE ROW LEVEL SECURITY` continua de pé.
Não há conexão do dono. Não há `BYPASSRLS`.

| | papel `platform_admin` (recusado) | `PainelDeInstituicoes` (feito) |
|---|---|---|
| Conexão | dono, ou papel com `BYPASSRLS` | `algorithmia_app`, o de sempre |
| Leitura | uma consulta, sem contexto, todas as escolas | uma consulta por escola, cada uma no contexto dela |
| Se a policy tiver um bug | ninguém percebe: o papel a ignora | o painel quebra junto com o jogo |
| Custo | uma consulta | uma transação por instituição |

O custo é real e é aceitável: com seis escolas, seis transações. Quando forem seiscentas, isto
vira uma *materialized view* — e aí haverá medição para justificá-la.

### Quem é o operador

`usuarios` é tenant-scoped desde a Etapa C. Um "administrador da plataforma" dentro dela seria
o aluno de **alguma** escola — a primeira, por acidente histórico — com poder sobre as demais.
Por isso o operador mora em `operadores`: catálogo global, como `tenants` e `tenant_dominios`,
sem `tenant_id` e sem RLS. Guard separado. **Um mestre não entra no console; um operador não
entra no jogo, e não há caminho de código entre os dois.**

Ele não ganha poder de banco: continua sendo `algorithmia_app`.

### A porta que não existe

`CONSOLE_HOST` vazio ⇒ **as rotas não são registradas**, e `/console` responde 404 em todo
domínio. A alternativa — um middleware que compara o host — é uma linha de código que alguém
pode remover num refactor. Uma rota que não foi registrada não tem como ser alcançada.

O host do console **não** entra em `tenant_dominios`: ele não é uma instituição, e o
`ResolverTenant` é retirado dessas rotas de propósito.

### O que o console NÃO faz

**Não provisiona escolas.** Criar uma escola é criar uma escola vazia, e só o
`algorithmia:importar` sabe enchê-la — um comando que lê um dump e demora minutos. Um botão
"criar escola" produziria uma instituição desligada e inútil, e alguém acabaria ligando-a à
força. Provisionar mora no terminal, junto do comando que semeia.

## 8. O portão da ativação

Uma escola ativa e vazia reprova o `algorithmia:smoke`, que é o portão do `bin/deploy.sh` —
e portanto reprova **todo deploy**, até alguém desconfiar. Não é hipótese: foi o que o ensaio
do corte (C.6) fez com um build correto.

O `DEFAULT false` da coluna `ativo` impede o primeiro erro. `ProvisionamentoDeInstituicoes::ativar()`
impede o segundo: ele **roda o smoke daquela instituição antes de ligá-la**, e recusa. O
veredito é do smoke, e não de uma checagem paralela — duas definições de "jogável" divergem no
dia em que alguém mexer numa delas.

**Não há `--forcar`.** Um portão com botão de contornar é um portão que ninguém fecha. Se um
dia for mesmo necessário, o escape é um `UPDATE tenants SET ativo = true` escrito à mão — e
não uma opção que a próxima pessoa copia do histórico do shell.

Desligar, ao contrário, **nunca** roda o smoke: exigir que a escola esteja jogável para poder
desligá-la trancaria por dentro exatamente a escola quebrada que se quer tirar do ar.

## 9. Três defeitos que a Etapa E revelou

Nenhum tinha teste; nenhum era visível pela leitura. Estão aqui porque a próxima pessoa vai
querer saber por que estas linhas são assim.

1. **`ContextoDoTenant` não era singleton.** Cada `app()` devolvia uma instância nova, e
   `atual()` respondia `null` a quem não o definira. Só não quebrou porque ninguém lia
   `atual()` fora de quem acabara de escrevê-lo. As flags precisam ler.

2. **O catálogo (`tenants`, `tenant_dominios`) era lido pela conexão do DONO**, "para não
   depender de ele ter ficado sem RLS". O argumento não se sustenta: o `ResolverTenant` lê
   essas mesmas tabelas pela conexão da aplicação, a cada requisição — se um dia ganharem RLS,
   o jogo inteiro para muito antes de um comando de console. E cobrava caro: uma escola criada
   pela aplicação era **invisível** ao smoke que deveria aprová-la, porque as duas conexões
   enxergam mundos diferentes dentro de uma transação.

3. **O `finally` de `usar()` mascarava a exceção original.** Um erro SQL aborta a transação do
   PostgreSQL; o `SET LOCAL` de restauro é o próximo comando, estoura com `25P02`, e a sua
   exceção toma o lugar da real. Quem investiga lê "current transaction is aborted" no lugar do
   erro que aconteceu. Agora o caminho de erro não roda SQL nenhum — o rollback já desfaz o
   `SET LOCAL` —, e só a cópia em memória volta.

## 10. Quatro defeitos de isolamento, achados e corrigidos

> Todos vieram de exercitar a Etapa E contra um banco real. Nenhum tinha teste. Três eram a
> **mesma classe**: uma restrição do banco que ignora `tenant_id` enquanto o RLS a esconde.

### 10.1 `auditoria` não era tenant-scoped

Sem `tenant_id`, sem RLS. Não vazava — nenhuma rota a lê. Mas as linhas de duas escolas
conviviam sem barreira, e a primeira tela que as mostrasse as misturaria.

O que adiava o conserto era real: há **dois tipos de autor**, em mundos diferentes. O usuário
de uma escola age dentro de um contexto; o operador da plataforma age fora de qualquer um — o
`/console` roda sem `ResolverTenant`. Uma policy `tenant_id = current_setting(...)` recusaria a
escrita do operador, porque `NULL = NULL` é NULL.

A resposta é `IS NOT DISTINCT FROM`, que trata NULL como um valor:

| quem escreve | contexto | `tenant_id` | quem enxerga |
|---|---|---|---|
| aluno, professor, mestre | a escola dele | o id da escola | só aquela escola |
| operador da plataforma | nenhum | `NULL` | só quem está sem contexto: o console |

`NULL` aqui significa **"a plataforma"**, e não "esqueceram de preencher".

### 10.2 `usuarios.email` era único global — e isso era explorável

```
-- como algorithmia_app, no contexto da escola B:
SELECT count(*) FROM usuarios WHERE lower(email) = 'ana@escola-a.test';   -->  0
INSERT INTO usuarios (nome, email, senha_hash) VALUES ('Outra', 'ana@escola-a.test', 'y');
ERROR:  duplicate key value violates unique constraint "usuarios_email_unique"
```

`AutenticacaoController::registrar` faz exatamente essas duas coisas, nessa ordem. Sob RLS a
checagem devolvia "e-mail livre"; o `INSERT` estourava. O visitante recebia **500 em vez de um
erro de validação**, e aprendia que aquele e-mail existe **em outra instituição**. Enumeração
de contas entre escolas, numa rota que não exige conta.

O índice virou `(tenant_id, lower(email))`. Isto **não** é a conta global do roteiro v1 §5 —
aquela exige resolver a identidade antes de saber o tenant, e continua sem demanda.

### 10.3 `conquistas.codigo` era único global

`arquivista_do_vazio` só podia existir em **uma** escola no banco inteiro. O
`ServicoDeConquistas` sempre a procurou por `codigo` sob RLS, ou seja, dentro da instituição:
o único global nunca foi necessário, e sempre foi um bloqueio.

### 10.4 A sessão não estava amarrada à instituição

`sessions` não tem `tenant_id`, e **não pode ter**: o `StartSession` roda antes do
`ResolverTenant` — o resolvedor precisa da sessão para fazer o que faz. Uma policy ali
deixaria o site sem sessão nenhuma.

Assumir a identidade de outra pessoa não era possível, e é bom saber por quê: `usuarios.id` é
chave primária **global**, então o usuário 3 nunca existe em duas escolas, e o RLS o esconde da
segunda. **A chave primária global, que causa o problema do conteúdo, aqui protege por
acidente** — e some no dia em que os ids virarem `(tenant_id, id)`.

Mas o **resto** da sessão atravessava: o estado da batalha (que carrega o gabarito), o flash, o
`intended`, o CSRF. E sem ataque nenhum, no cenário mais natural que existe: um
`SESSION_DOMAIN=.exemplo.com`, que é o que se escreve quando as escolas são subdomínios.

O `ResolverTenant` passou a marcar a sessão com o tenant e a descartar a que vier de outro.

### 10.5 O teste que impede a próxima

`IntegridadeDaTenancyTest` **não lista tabelas**. Ele pergunta ao PostgreSQL quais têm
`tenant_id` e exige de todas: RLS + `FORCE`, uma policy, uma FK para `tenants`, e nenhum índice
único que ignore `tenant_id`. Uma tabela sem `tenant_id` reprova até alguém escrever, no
próprio teste, por que ela pode ficar de fora.

Os três defeitos acima teriam sido pegos por ele no dia em que nasceram. É o que os testes
anteriores não faziam: cada um olhava para a lista de tabelas que o seu autor lembrou.

## 11. O conteúdo: a primeira escola importa, as seguintes copiam

> Achado ao exercitar a E.2 contra um banco real, com as 955 linhas de `desafios` do legado.
> Nenhum teste o pegaria: a suíte semeava uma escola de cada vez.

**A chave primária de `fases` é `id`, e não `(tenant_id, id)`.** O `algorithmia:importar`
preserva os ids do legado de propósito: o progresso que ele traz junto aponta para eles.
Verificado no banco, como app role, no contexto do tenant 2:

```
BEGIN; SET LOCAL app.tenant_id = '2';
INSERT INTO fases (id, …) VALUES (8, …);
ERROR:  duplicate key value violates unique constraint "fases_pkey"
```

**E o RUNBOOK §10.6b mandava rodar exatamente esse comando.** O ensaio do corte (C.6) criou
uma segunda instituição e provou o *isolamento* — tenant 2 vê zero desafios —, mas nunca tentou
*semeá-la*.

### A resposta não é `content_packages`

Uma escola nova não quer os alunos da primeira. Quer o **conteúdo**. O `SemeadorDeConteudo`
copia mestres, itens, conquistas, fases, desafios e diálogos — com **ids novos**, reescrevendo
as chaves estrangeiras —, numa transação, com reconciliação por contagem. Nenhuma pessoa é
copiada.

Para isso, três amarras caíram, e as duas primeiras eram defeitos por conta própria:

1. **`arquivista_do_vazio` procurava as fases 8, 14, 20 e 32 por id.** Herança do
   `ConquistaService.php:86` do legado. Amarrar uma regra de jogo à chave primária do banco
   tornava a conquista inalcançável na segunda escola — em silêncio —, **e fazia uma quinta
   secundária criada pelo mestre não contar**. Agora são as fases de `tipo = 'secundaria'` da
   instituição. No conteúdo do legado são exatamente aquelas quatro: o jogo de ninguém muda.
2. **`conquistas.codigo` era único global** (ver §10.3).
3. O `Smoke` verificava os **ids** das secundárias. Passou a verificar que elas **existem** —
   uma escola semeada tem as quatro, com outros ids.

Verificado com o conteúdo real: 35 fases e 955 desafios copiados, **zero ids compartilhados**,
34 requisitos e 11 `item_drop_id` religados dentro da escola certa, zero contas copiadas, e as
duas escolas passando no smoke completo.

### O que ainda não existe

Conteúdo **diferente** por instituição — o `content_packages` do roteiro v1 (Fase 3), que o
`§5` deixou de fora de propósito. Hoje toda escola começa com uma cópia do mesmo mundo, e o
mestre dela o edita a partir dali. Isso basta para o piloto, e a proposta própria continua de
pé para o dia em que não bastar.
