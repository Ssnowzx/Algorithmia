## ADDED Requirements

### Requirement: Números de dano flutuantes melhorados

A arena SHALL exibir números de dano flutuantes com arco de movimento, pop de escala no
spawn e cor por tipo (dano, cura, combo), com crítico visualmente maior. O movimento SHALL
ser animado apenas via `transform`/`opacity` (CSS declarativo), com o JS apenas criando,
posicionando e removendo o elemento.

#### Scenario: Número aparece com pop e cor por tipo

- **WHEN** o herói ou o inimigo recebe dano
- **THEN** um número flutuante surge sobre o alvo com um pop de escala
- **AND** a cor corresponde ao tipo (dano, cura ou combo)
- **AND** ao terminar a animação o elemento é removido do DOM

### Requirement: Partículas de combate com object pooling

A arena SHALL exibir partículas (faíscas no impacto, explosão do inimigo derrotado,
partículas ao coletar ouro) por meio de um sistema leve em `requestAnimationFrame` com
object pooling e delta-time, sem alocar objetos por partícula em runtime.

#### Scenario: Loop para quando não há nada a animar

- **WHEN** não existem partículas ativas nem shake em andamento
- **THEN** o loop de `requestAnimationFrame` deixa de ser reagendado
- **AND** volta a rodar quando um novo efeito é disparado

#### Scenario: Teto de partículas respeitado

- **WHEN** muitos efeitos são disparados em sequência
- **THEN** o número de partículas simultâneas não ultrapassa um teto seguro
- **AND** spawns excedentes são descartados

### Requirement: Screen-shake contido no contêiner da arena

A arena SHALL aplicar um screen-shake trauma-based (amplitude proporcional a `trauma²`,
com decaimento) ao contêiner da arena — nunca ao `body` — em grande dano e na
vitória/derrota, animando apenas `transform`.

#### Scenario: Shake aplicado sem reflow do body

- **WHEN** ocorre grande dano ou o fim da batalha (vitória/derrota)
- **THEN** o contêiner da arena treme por `transform` e converge suavemente para zero
- **AND** o `body` e o scroll da página não são afetados

### Requirement: Respeito a prefers-reduced-motion

Quando o usuário preferir movimento reduzido, o jogo SHALL desligar shake, partículas e
flutuações, mantendo o feedback essencial por mudança de opacity/cor. A preferência SHALL
ser respeitada tanto no CSS quanto no JS (gating do sistema de partículas via `matchMedia`).

#### Scenario: Movimento reduzido desliga efeitos não essenciais

- **WHEN** `prefers-reduced-motion: reduce` está ativo
- **THEN** screen-shake, partículas e flutuações idle não são executados
- **AND** o feedback de acerto/dano/vitória continua perceptível por cor/opacity

#### Scenario: Mudança de preferência em runtime é respeitada

- **WHEN** a preferência de movimento muda durante a sessão
- **THEN** o sistema de partículas passa a respeitar o novo valor sem recarregar a página

### Requirement: Uso exclusivo dos tokens de cor existentes

Todo efeito visual novo SHALL usar somente as variáveis CSS de `:root` já definidas em
`public/css/style.css`, sem cores hardcoded fora delas.

#### Scenario: Nenhuma cor nova hardcoded

- **WHEN** os efeitos de juice são estilizados
- **THEN** as cores vêm das variáveis `--hp`, `--mp`, `--xp`, `--primaria`, `--sucesso`, etc.
- **AND** nenhum hex solto fora dos tokens é introduzido (cores derivadas viram novos tokens)
