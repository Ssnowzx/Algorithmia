# ADR-006 - Plataforma Laravel em `/platform`

## Status
Aceita

## Contexto
O Algorithmia precisa evoluir para uma nova base tecnica sem quebrar o legado
PHP/MVC/MySQL que continua em producao e manutencao. A etapa 1 define a
fundacao da nova plataforma de forma isolada, para evitar colisao com o front
controller, `app/`, `config/` e o banco MySQL existentes.

## Decisao
Criar o novo nucleo Laravel dentro de `/platform`, em paralelo ao legado.
Essa pasta contem infra local, documentacao, contrato de ambiente e o primeiro
endpoint tecnico (`/healthz`), sem migrar dados, usuarios, personagens ou
regras de jogo.

## Alternativas consideradas
- Criar o Laravel na raiz do repositorio.
- Reaproveitar o MySQL legado para o novo backend.
- Misturar regras novas com o MVC artesanal atual.

## Consequencias
- Evita conflito estrutural com o legado.
- Permite evolucao incremental com fronteira clara de responsabilidade.
- Exige disciplina para nao misturar credenciais, rotas ou bancos.
- Exige um bootstrap inicial em Docker para gerar e versionar `composer.lock`
  antes da homologacao completa.

## Riscos
- Confusao entre instrucoes do legado e da plataforma nova.
- Implementacao prematura de tenancy, autenticacao institucional ou migracao de
  dados.
- Divergencia entre documentos se a fronteira nao for explicita.

## Revisao futura
Revisar esta decisao apenas quando o nucleo novo estiver estavel e houver
motivo real para aproximar ou substituir partes do legado.

## Referencias internas
- `CODEX.md`
- `AGENTS.md`
- `docs/migracao/00-governanca-e-inventario.md`
- `docs/migracao/00-riscos-inconsistencias-seguranca.md`
