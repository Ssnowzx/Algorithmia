# ADR-005 - Estrategia de API e Integracao

## Status
Aceita

## Contexto
O produto permanecerá como monolito modular na fase inicial da migracao.
Mesmo assim, o backend precisa ser pronto para exposicao gradual de contratos claros.

## Decisao
- Manter monolito modular.
- Usar API versionada quando houver consumo adequado.
- Tratar o backend como fonte de verdade para regras de jogo.
- Nao expor gabaritos nem respostas corretas antes da resolucao.
- Migrar o frontend de forma gradual depois de estabilizar o dominio.

## Alternativas consideradas
- Microservicos precoces.
- API-first total desde o primeiro dia.
- Frontend novo acoplado ao legado.

## Consequencias
- Menor custo de coordenacao no inicio.
- Possibilidade de expor contratos limpos por modulo.
- Facilita evolucao sem quebrar o fluxo atual.

## Riscos
- Contratos instaveis se nao houver versionamento.
- Vazamento de regra no cliente.
- Integracoes futuras dependentes de comportamento interno nao documentado.

## Criterios de revisao futura
- Quando houver consumo externo real da API.
- Quando o frontend precisar ser desacoplado por dominio.
- Quando o legado ja estiver estabilizado em Laravel.

## Referencias internas
- `docs/processo/FLUXO-OPENSPEC.md`
- `docs/migracao/00-matriz-rastreabilidade-regras.md`
- `docs/migracao/00-riscos-inconsistencias-seguranca.md`

