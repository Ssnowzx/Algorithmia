## ADDED Requirements

### Requirement: Tela de início única (sem overlay duplicado)

O jogo SHALL ter uma única tela de início: a seção `home-entrada` em
`app/views/home/index.php` (logo, subtítulo, botão "Entrar no mundo", link de login e a grade
dos 5 mestres). NÃO SHALL existir um overlay de splash separado sobre a home, o login ou o
layout interno.

#### Scenario: Visitante não logado vê a home com os mestres

- **WHEN** um visitante não autenticado acessa a raiz do jogo
- **THEN** a `home-entrada` é exibida com logo, botão de entrada e a grade dos 5 mestres
- **AND** nenhum overlay cobre o conteúdo

#### Scenario: Logout retorna à tela de início

- **WHEN** o jogador autenticado aperta "Sair"
- **THEN** ele é redirecionado para a home limpa (sem o parâmetro `?splash=1`)
- **AND** vê a `home-entrada` com a grade dos mestres

### Requirement: Fundo da home confiável em qualquer dispositivo

A `home-entrada` SHALL exibir a cena de fundo por meio de um elemento `<img>` posicionado
atrás do conteúdo (não via `background-image` CSS). O `body.pagina-home` SHALL ter uma cor de
fundo sólida (`var(--bg)`), de modo que a página NUNCA renderize branca caso a imagem não
carregue.

#### Scenario: Fundo aparece em mobile

- **WHEN** a home é aberta em um navegador mobile (iOS/Android)
- **THEN** a cena de fundo é renderizada (elemento `<img>`, sem depender de `background-attachment: fixed`)

#### Scenario: Sem imagem, nunca fica branco

- **WHEN** a imagem de fundo não carrega (rede, MIME ou cache)
- **THEN** a página exibe a cor de fundo escura (`var(--bg)`), não branco

### Requirement: Entrada sem trilha de som de fundo

A tela de início SHALL tocar apenas efeitos de clique. NÃO SHALL iniciar a trilha/pad de som
ambiente.

#### Scenario: Nenhuma música ao entrar

- **WHEN** o visitante interage com a home
- **THEN** apenas sons de clique são emitidos
- **AND** nenhum pad/trilha de fundo é iniciado
