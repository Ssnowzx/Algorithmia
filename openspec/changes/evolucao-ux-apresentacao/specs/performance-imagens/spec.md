## ADDED Requirements

### Requirement: Imagens com dimensões intrínsecas (anti-CLS)

As imagens renderizadas pelos helpers (`svg()` e `marcaHtml()`) SHALL incluir os atributos
`width`/`height` intrínsecos do arquivo servido (via `dimensoesImagem()`, getimagesize
memoizado), para o navegador reservar a proporção e eliminar o layout shift. Como o valor é
o tamanho NATURAL, o CSS continua mandando no tamanho exibido — o render NÃO SHALL mudar.

#### Scenario: Tag de imagem reserva espaço

- **WHEN** uma view renderiza uma imagem via `svg(...)`
- **THEN** a `<img>` sai com `width`/`height` do arquivo servido
- **AND** o tamanho exibido segue o CSS, sem salto de layout

### Requirement: Imagens no tamanho certo (right-size)

As `.webp` ilustradas servidas SHALL ter resolução compatível com o maior tamanho de
exibição real (com folga ~2.3× para retina), e os itens da loja/inventário SHALL ser servidos
como `.webp` leve. A ARTE NÃO SHALL mudar (mesma imagem, só menor resolução); os PNGs
originais SHALL ser preservados em `docs/evolucao-visual` como fonte. A ferramenta
`tools/imagens/right_size_imagens.py` SHALL ser idempotente (rodar de novo não altera nada).

#### Scenario: Arte continua nítida no maior uso

- **WHEN** uma arte (ex.: inimigo) é exibida no seu maior tamanho (arena ~270px)
- **THEN** ela aparece nítida, servida por um arquivo bem menor que o original

#### Scenario: Itens servidos como webp leve

- **WHEN** a loja ou o inventário exibem um item
- **THEN** o `srcImagem()` serve o `.webp` do item (não o PNG grande), com a mesma arte

#### Scenario: Re-rodar a ferramenta é no-op

- **WHEN** `tools/imagens/right_size_imagens.py` roda novamente num diretório já otimizado
- **THEN** nenhuma imagem é alterada
