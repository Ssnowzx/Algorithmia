# Design — Migração para Laravel 13 + PostgreSQL

## Camada MVC (onde cada coisa entra)

O port não reorganiza o domínio: ele o extrai. O legado já separava
`models / services / controllers / views`; o Laravel dá nomes melhores aos mesmos
papéis.

| Legado | Port | Papel |
|---|---|---|
| `app/services/BatalhaService.php` | `platform/app/Dominio/Combate/MotorDeBatalha.php` | regra de combate, sem HTTP |
| `app/services/{Recompensa,Progressao,Reputacao,Conquista}Service.php` | `platform/app/Dominio/Progressao/Servico*.php` | regra de progressão |
| `app/services/{Maestria,Missao,Regiao,Onboarding}Service.php` | `platform/app/Dominio/Progressao/ServicoDe*.php` | leituras derivadas, read-only |
| `app/models/*` | `platform/app/Models/*` | Eloquent, colunas em português |
| `app/controllers/*` | `platform/app/Http/Controllers/*` | HTTP, nada de regra |
| `app/views/*.php` | `platform/resources/views/*.blade.php` | apresentação |
| `config/config.php` | `platform/config/jogo.php` | balanceamento (fonte única) |
| `config/bestiario.php` | `platform/config/bestiario.php` | cânone (inalterado) |

## A decisão que sustenta tudo: vetores-ouro antes do port

O roteiro original mandava documentar o domínio na Fase 0 e "provar equivalência
funcional" na Fase 4. Não havia contra o que comparar — o repositório tinha zero
testes. Provar equivalência contra uma descrição em prosa das regras, escrita lendo
o mesmo código que se quer substituir, é reescrita por fé.

Então a Fase 0 virou **testes de caracterização executáveis sobre o legado**. Os
números esperados (17, 21, 26, 30 de dano; fúria saturando em 4,0; 3 estrelas sem
erro e sem IA) são derivados à mão das constantes. Se uma regra mudar de propósito,
o teste muda junto e o diff mostra a decisão.

Para provar que a rede não é decorativa, duas mutações deliberadas em
`config/jogo.php`: `combo_bonus` 0.25→0.30 quebra 3 testes; `rage_max` 4.0→5.0
quebra 1.

## As duas costuras do motor

O motor recebeu exatamente **duas** injeções de dependência, e por um motivo único:
são as duas coisas que impediriam um teste de medir a aritmética do combate.

1. **`RepositorioDeBatalha`** — onde a batalha vive entre turnos. Sessão em
   produção, memória nos testes e no smoke. Sem isso, testar exigiria HTTP.
2. **`SorteadorDeDesafios`** — como as perguntas são escolhidas. O sorteio real
   embaralha, e amarrar asserções à permutação do Mt19937 travaria a
   *implementação* do sorteio, não as *regras* de combate.

Tudo o mais é regra, e regra é testada. As invariantes do sorteio (priorizar
inéditos, ordenar por dificuldade) são cobertas à parte, sem depender de semente.

## A única divergência deliberada: idempotência da recompensa

No legado, a guarda contra duplo-crédito é o flag `recompensado` de
`$_SESSION['batalha']`. Chamar `RecompensaService::conceder` duas vezes duplica a
reputação — XP e ouro escapam **por acidente**, porque são gravados como valor
absoluto a partir de uma linha lida antes.

No port, cada batalha nasce com um `batalhaId` gerado no servidor. A concessão o
insere em `recompensas_batalha` **antes** de creditar qualquer coisa, na mesma
transação. A chave primária serializa a disputa no banco, não na aplicação.

Registrado nos dois lados:

| Legado | Port |
|---|---|
| `conceder_duas_vezes_duplica_a_reputacao_a_guarda_vive_na_sessao` | `conceder_a_mesma_batalha_duas_vezes_nao_credita_de_novo` |

## Regras que o port precisou preservar, não "consertar"

- **`ProgressoFase::registrar` só acumula o melhor nas ESTRELAS.** `acertos`,
  `erros` e `usou_ia` refletem a última partida. Rejogar limpo apaga a mancha do
  Fragmento, e é isso que permite reconquistar "Puro de Coração". Redenção é regra
  do jogo. Ia escrever `usou_ia = antigo OR novo` e teria removido uma mecânica.
- **A batalha nunca termina por acabarem as perguntas.** Cruzado o limite de ritmo,
  abre o Duelo Final e a fúria cresce.
- **A escolha diante da IA pesa mais que a reputação, mas não a apaga.** Quem manda
  destruir o Fragmento sem nunca ter recusado sua ajuda recebe o final de
  equilíbrio, não o de mestre.
- **O Fragmento não se vende.** Transformá-lo em ouro faria da queda moral um
  negócio.

## Traduções MySQL → PostgreSQL

| MySQL | PostgreSQL | Por quê |
|---|---|---|
| `ENUM(...)` | varchar + `CHECK` | ×9 |
| `TINYINT(1)` | `boolean` | o PG recusa `0` numa coluna booleana |
| `JSON` | `jsonb` | permite índice e operadores |
| `email UNIQUE` (collation `_ci`) | índice sobre `lower(email)` | evita a extensão `citext`, que exigiria superusuário |
| `ON DUPLICATE KEY UPDATE` | `ON CONFLICT … DO UPDATE` | |
| `INSERT IGNORE` + `rowCount()` | `ON CONFLICT DO NOTHING` + linhas afetadas | |
| `FIELD(tipo, 'arma', …)` | `array_position(ARRAY[…]::text[], tipo)` | ordem do inventário |
| `YEARWEEK(x, 3)` | `date_trunc('week', x)` | ambos começam na segunda |
| `SUM(estrelas = 3)` | `COUNT(*) FILTER (WHERE estrelas = 3)` | o PG não soma booleanos |

**Números mágicos que foram derivados, não repetidos:** o confronto final se declara
pelo `tipo = 'chefe_final'` (era o id 35 fixo) e a véspera pergunta quem é a próxima
fase (era `ordem_global == 34`). Um seeder diferente teria deixado a tela de finais
inalcançável sem nada acusar.

**Número mágico que NÃO pôde ser derivado:** `config('jogo.fases_secundarias')` =
`[8, 14, 20, 32]`. É por isso que a importação preserva IDs, e o smoke verifica.

## Robustez / não-regressão

- **Duas suítes verdes durante todo o port**: 38 vetores-ouro sobre o legado (MySQL)
  e 196 testes sobre o port (PostgreSQL). A CI roda as duas.
- **A suíte do port roda contra PostgreSQL de verdade**, não SQLite em memória. Um
  SQLite esconderia exatamente o que a migração precisa expor.
- **`algorithmia:importar --dry-run`** executa a importação inteira numa transação e
  a desfaz — exercitando chaves estrangeiras e conversão de tipos.
- **`algorithmia:smoke`** joga uma fase real numa transação e a desfaz. Verifica o
  que um health check não verifica.
- **O legado permanece de pé**, e a importação nunca escreve nele.
