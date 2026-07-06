# Design - Etapa 1, nucleo Laravel e infraestrutura local

## Camada

Nova base em `/platform`, isolada do legado.

## Estrutura

- `app/Domain/Tenancy` - fronteira futura de isolamento.
- `app/Domain/Identity` - identidade global e vinculos.
- `app/Domain/Content` - conteudo publicavel.
- `app/Domain/Game` - motor de batalha e recompensas.
- `app/Domain/Progress` - progresso e maestria.
- `app/Domain/Classrooms` - turmas e matriculas.
- `app/Domain/Reports` - relatorios e exportacoes.
- `app/Domain/Platform` - saude, operacao e utilitarios tecnicos.

## Estrategia

1. Criar o esqueleto Laravel compativel com PHP 8.4.
2. Implementar o health check com verificacao de aplicacao, PostgreSQL e Redis.
3. Criar Docker Compose local e o wrapper `./bin/platform`.
4. Adicionar testes de feature para saude, falha controlada e ausencia de
   vazamento de segredos.
5. Documentar a convivencia com o legado e a fronteira das etapas seguintes.

## Robusteza

- A plataforma nova nao aponta para o MySQL legado.
- PostgreSQL e Redis permanecem internos a rede Docker.
- `up` nao executa migrations automaticamente.
- `migrate` e comando explicito.

## Fora de escopo

- Tenancy, RLS e autenticacao.
- Regras de jogo e migracao de conteudo.
- UI nova.
