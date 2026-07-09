## ADDED Requirements

### Requirement: Toda rota que escreve é POST

Nenhuma rota que altere estado SHALL responder a GET. O CSRF SHALL vir do middleware
do framework, e não de checagens escritas à mão em cada método.

No legado, `historia/concluir` gravava progresso e XP, `loja/vender/5` transformava
um item em ouro, `inventario/descartar/5` o destruía para sempre e
`mestre/excluirFase/5` apagava a fase e seus desafios em cascata — todas alcançáveis
por um `<img src>` numa página qualquer.

#### Scenario: As onze rotas de escrita recusam GET

- **WHEN** um GET é feito a qualquer rota de escrita (turnos de batalha, loja, inventário, conclusão de história, exclusões do Painel do Mestre)
- **THEN** a resposta é 405
- **AND** nenhum estado do jogador ou do conteúdo é alterado

#### Scenario: Um POST sem token CSRF é recusado

- **WHEN** um POST chega sem o token CSRF válido
- **THEN** a resposta é 419

### Requirement: Sessão em banco, nunca em cookie

O estado da batalha SHALL viver em sessão com driver `database`. Ele carrega os
desafios completos, com gabarito, para que a correção aconteça no servidor — não
cabe nos 4 KB de um cookie, e mandá-lo ao cliente entregaria as respostas.

#### Scenario: Um jogador não alcança a batalha de outro

- **WHEN** outro usuário assume uma sessão limpa e tenta responder um turno
- **THEN** o motor responde "Nenhuma batalha ativa"
- **AND** nenhuma resposta é registrada no log do jogador original

### Requirement: Erros de rotas AJAX respondem JSON

Requisições que pedem JSON SHALL receber JSON também nos erros. O padrão do skeleton
do Laravel restringe isso a `api/*`, e os endpoints de turno vivem sob `/batalha`:
uma sessão expirada devolveria uma página HTML no meio de um `fetch()`, e o
`batalha.js` quebraria ao interpretá-la.

#### Scenario: Visitante em endpoint de turno recebe 401 em JSON

- **WHEN** uma requisição JSON não autenticada chega a `/batalha/responder`
- **THEN** a resposta é 401 em JSON, não um redirecionamento HTML

### Requirement: O Painel do Mestre recusa desafios insolúveis

A validação por tipo de desafio SHALL espelhar exatamente o que o corretor espera.
Um `ordenar` sem opções deixa o jogador sem nada para mover; um índice de gabarito
fora da faixa faz o corretor recusar a resposta certa **para sempre**. O legado
salvava os dois.

#### Scenario: Gabarito fora da faixa das opções é recusado

- **WHEN** um desafio de múltipla escolha com 2 opções é salvo com gabarito 5
- **THEN** a validação falha e nada é gravado

#### Scenario: Ordenar exige uma permutação completa dos índices

- **WHEN** um `ordenar` com 3 opções é salvo com resposta `1, 0`
- **THEN** a validação falha, pois falta o índice 2
- **AND** com `2, 0, 1` o desafio é aceito

### Requirement: Apresentação preservada, não redesenhada

O port SHALL reaproveitar o CSS e o JS do jogo em PHP puro. `public/js/batalha.js`
SHALL ser usado **sem alteração**: ele já era parametrizado por
`window.BATALHA = {csrf, estado, urls}`.

Portar não é hora de mexer na identidade visual. O texto canônico — bestiário, os
três epílogos, a lore — SHALL ser copiado, nunca reescrito.

#### Scenario: A arena entrega o mesmo contrato que o JS legado espera

- **WHEN** a arena é renderizada pelo port
- **THEN** `window.BATALHA` traz `csrf`, `estado` e as URLs dos cinco endpoints de turno
- **AND** `public/js/batalha.js` roda sem uma linha alterada

#### Scenario: O cânone não é reescrito

- **WHEN** a lore, o bestiário ou os epílogos são portados
- **THEN** o texto é idêntico ao do jogo em PHP puro
- **AND** vive em `config/`, não dentro de uma view — uma mudança ali é decisão narrativa, não ajuste de layout

### Requirement: Leituras derivadas não mentem ao jogador

As barras de progresso SHALL refletir o fator que de fato limita o avanço.

- A barra de maestria SHALL medir o **mais atrasado** entre acumular acertos e
  sustentar precisão. Medir só os acertos a encheria enquanto a precisão despenca.
- A missão de precisão SHALL medir **volume** até o mínimo exigido, e só então
  precisão. Uma barra de precisão cheia com duas respostas não mede nada.
- "Região dominada" SHALL exigir 3 estrelas em todas as fases principais; as
  secundárias, sendo opcionais, não podem impedir o domínio.
- Uma conquista secreta SHALL ocultar nome e descrição até ser obtida.

#### Scenario: Muitos acertos com precisão baixa não sobem de faixa

- **WHEN** uma matéria tem 40 respostas e 20 acertos (50%)
- **THEN** a faixa trava em Aprendiz, que não exige piso de precisão
- **AND** a barra rumo a Praticante reflete a precisão, não o volume
