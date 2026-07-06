# Inventario de Dados do Legado

| Entidade/Tabela | Finalidade | Chave primaria | Relacionamentos | Colunas criticas | Dados sensiveis | Possivel destino na arquitetura nova | Risco de migracao | Observacoes |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| `usuarios` | Conta de acesso e papel do usuario | `id` | 1:1 com `personagens` | `nome`, `email`, `senha_hash`, `papel`, `criado_em` | `senha_hash` | `users` global + vinculacao institucional futura | Medio | `email` e unico; o legado usa `papel` para mestre/jogador |
| `personagens` | Avatar jogavel do usuario | `id` | FK unica para `usuarios`, 1:N com inventario, progresso, escolhas, respostas, conquistas | `usuario_id`, `nome`, `classe`, `nivel`, `xp`, `hp_max`, `hp_atual`, `mp_max`, `mp_atual`, `ouro`, `reputacao`, `capitulo` | Nao ha segredo, mas ha perfil de jogo | `characters` com `tenant_id` futuro e vinculo a user global | Alto | Campo central para a futura separacao entre usuario global e instituiçao |
| `mestres` | NPCs principais e base do mapa/regioes | `id` | 1:N com `fases` | `nome`, `titulo`, `disciplina`, `regiao`, `svg_slug`, `cor_tema`, `ordem` | Nao | `content_masters` ou tabela de conteudo global versionado | Medio | Conteudo, nao dado pessoal |
| `fases` | Estrutura da jornada, desbloqueio e recompensa | `id` | FK para `mestres`, auto-relacao via `requisito_fase_id`, 1:N com desafios/dialogos/progresso | `ordem_global`, `tipo`, `inimigo_nome`, `inimigo_svg`, `inimigo_hp`, `inimigo_ataque`, `xp_recompensa`, `ouro_recompensa`, `item_drop_id`, `requisito_fase_id` | Nao | `content_phases` / `chapter_phases` | Alto | Ha ids e ordens hardcoded em regras de dominio |
| `desafios` | Banco de perguntas do jogo | `id` | FK para `fases` | `ordem`, `tipo`, `assunto`, `pergunta`, `codigo`, `opcoes`, `resposta`, `explicacao`, `dificuldade` | `resposta` e `opcoes` (gabarito) | `content_challenges` separado de runtime | Critico | O gabarito nao pode ir ao cliente; exige cuidado na migração |
| `respostas_log` | Historico de respostas e estatisticas | `id` | FK para `personagens` e `desafios` | `correta`, `usou_ia`, `respondido_em` | Historico pedagógico sensivel | `answer_attempts` / `responses_log` com tenant future | Alto | Base para anti-repeticao, maestria e missoes |
| `progresso_fases` | Progresso por fase e estrelas | `id` | FK para `personagens` e `fases` | `estrelas`, `acertos`, `erros`, `usou_ia`, `concluida_em` | Historico de aprendizagem | `phase_progress` | Alto | Tem unique `(personagem_id, fase_id)` |
| `inventario` | Itens do personagem | `id` | FK para `personagens` e `itens` | `quantidade`, `equipado` | Perfil de jogo | `inventories` | Alto | `uq_inv` protege duplicacao por item, mas a logica de incremento tem risco de concorrencia |
| `itens` | Catalogo de itens, armas, potions e especiais | `id` | 1:N com inventario; fase pode referenciar `item_drop_id` | `tipo`, `efeito`, `preco`, `svg_slug`, `raridade`, `compravel` | Nao | `items` de conteudo global | Medio | `efeito` em JSON precisa de equivalencia em PostgreSQL |
| `conquistas` | Catalogo de conquistas | `id` | 1:N com `conquistas_personagem` | `codigo`, `nome`, `descricao`, `svg_slug`, `secreta` | Nao | `achievements` | Medio | `codigo` e a chave semantica real |
| `conquistas_personagem` | Conquistas obtidas | PK composta `(personagem_id, conquista_id)` | FK para personagens e conquistas | `obtida_em` | Historico de progresso | `achievement_awards` | Medio | Tabela de vinculacao, ideal para auditoria de recompensas |
| `dialogos` | Falas de narrativa por fase e variante | `id` | FK para `fases` | `momento`, `variante`, `ordem`, `falante`, `svg_slug`, `texto` | Pode conter narrativa sensivel do produto | `dialogues` / `content_dialogues` | Medio | Variante `ia` depende de reputacao |
| `escolhas` | Decisoes narrativas persistidas | `id` | FK para `personagens` | `codigo`, `valor`, `criado_em` | Historico de escolhas | `player_choices` | Medio | Nao ha unique; risco de multipla linha por codigo/personagem |
| `migracoes_aplicadas` | Controle das migracoes executadas | desconhecida no schema atual da leitura parcial, mas referenciada em `migrate.php` | sistema | status de migracao | Nao | controle tecnico interno | Baixo | Precisa ser revalidada no schema completo durante a fase 1 |

## Entidades pedidas e equivalente encontrado
- `usuarios`: encontrado.
- `personagens`: encontrado.
- `mestres`: encontrado.
- `fases`: encontrado.
- `desafios`: encontrado.
- `respostas`: equivalente operacional em `respostas_log`.
- `progresso_fases`: encontrado.
- `respostas_log`: encontrado.
- `inventario`: encontrado.
- `itens`: encontrado.
- `conquistas`: encontrado.
- `escolhas`: encontrado.
- `dialogos`: encontrado.
- `ranking`: nao existe tabela; e uma visao calculada a partir de `personagens`.
- `configuracoes`: nao encontrada como tabela.
- `administradores`: nao encontrada como tabela; o papel mestre e armazenado em `usuarios.papel`.

## Riscos de dados identificados
- Chaves estrangeiras ausentes ou insuficientes em `escolhas` e possivelmente em outras tabelas auxiliares nao vistas no recorte.
- Campos textuais usados como identificadores sem `UNIQUE` em alguns fluxos semanticos.
- Datas em `DATETIME` sem timezone.
- Enums implícitos em `tipo`, `assunto`, `classe`, `papel`, `momento` e `variante`.
- `resposta` em `desafios` e dado critico de gabarito.
- Necessidade futura de `tenant_id` em praticamente todas as tabelas de dado institucional e de progresso.

## Observacoes para a nova arquitetura
- Conteudo global e progresso do aluno devem ser separados conceitualmente.
- O gabarito pertence ao lado servidor, nao a qualquer cliente.
- Tabelas de historico e progresso devem nascer com estrategia clara de auditoria e isolamento por tenant.
- A modelagem PostgreSQL provavelmente exigira revisar JSON, enums e constraints MySQL especificas.

