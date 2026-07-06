# ADR-004 - Versionamento de Conteudo

## Status
Aceita

## Contexto
O jogo tem fases, desafios, dialogos, itens e conquistas que representam conteudo vivo.
Na nova arquitetura, esse conteudo precisa poder ser publicado, versionado e revertido sem perder progresso historico.

## Decisao
- Tratar conteudo como pacotes versionados.
- Permitir publicacao e rollback.
- Manter conteudo global com possibilidade de override por tenant quando necessario.
- Preservar historico de respostas, progresso e escolhas mesmo quando o conteudo muda.

## Alternativas consideradas
- Editar conteudo diretamente em producao sem versao.
- Copiar banco inteiro por campanha.
- Reescrever conteudo dentro da aplicacao sem historico.

## Consequencias
- Mudancas de pergunta ou narrativa ficam rastreaveis.
- Historico do aluno nao se perde com reescritas de conteudo.
- Exige chaves de versao e politica de compatibilidade.

## Riscos
- Incompatibilidade entre progresso antigo e conteudo novo.
- Duplicacao sem semantica de versao.
- Dificuldade em reverter um pacote se nao houver identificacao clara.

## Criterios de revisao futura
- Quando houver estrategia formal de publicacao de conteudo.
- Quando tenant-specific overrides precisarem de governance propria.

## Referencias internas
- `docs/canon/REGRAS-DO-JOGO.md`
- `docs/canon/ROTEIRO-NARRATIVO.md`
- `docs/migracao/00-inventario-dados-legado.md`

