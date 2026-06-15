## 1. Arena por bioma (controller + view)

- [x] 1.1 `BatalhaController::iniciar`: carregar o `Mestre` da fase e computar `$fundoBioma = fundoRegiao($mestre['svg_slug'])` + `$bioma` (slug sem `fundo-`); fallback `fundo-batalha`/sem bioma quando não houver mestre; passar à view
- [x] 1.2 `batalha/arena.php`: aplicar `class="campo-batalha bioma-<slug>"` e `style="--fundo-bioma:url('<asset>')"`
- [x] 1.3 `batalha.css`: trocar o `url(...)` fixo do `.campo-batalha` por `var(--fundo-bioma, url(../img/fundos/fundo-batalha.png))`, preservando gradientes, chão em perspectiva e brilho arcano

## 2. Partículas ambientais por bioma (CSS)

- [x] 2.1 Definir as classes de bioma e os keyframes ambientais (folhas/Floresta, brasas/Montanha, pacotes-luz/Torre, maresia/Porto, runas-glow/Cidadela, neutro/Vila), animando só `transform`/`opacity`, com cores de `--cor-regiao`/tokens
- [x] 2.2 Aplicar a camada ambiental no mapa (`.regiao.bioma-*`) atrás dos nós (pseudo-elemento, `pointer-events:none`, `z-index` baixo)
- [x] 2.3 Aplicar a camada ambiental na arena (`.campo-batalha.bioma-*`) atrás dos combatentes, acima do fundo

## 3. Profundidade e transição no mapa

- [x] 3.1 `mapa.css`: camada de névoa/brilho com flutuação leve sobre o fundo do bioma da `.regiao` (parallax sugerido, sem listener de scroll)
- [x] 3.2 `mapa/index.php`: adicionar a classe de bioma por região (derivada de `svg_slug`/`fundo`) e marcar `.regiao` para o observer
- [x] 3.3 `app.js`: `IntersectionObserver` que adiciona `.revelada` às regiões ao entrarem na viewport; guarda de `prefers-reduced-motion` (revela tudo na hora) e de suporte ao observer; `mapa.css`: estilo de `.regiao`→`.regiao.revelada` (fade/slide curto)

## 4. Acessibilidade e coerência

- [x] 4.1 `@media (prefers-reduced-motion: reduce)` em `mapa.css` e `batalha.css`: desligar parallax, partículas ambientais e revelação, mantendo fundos estáticos legíveis
- [x] 4.2 Revisar que nenhum hex novo foi introduzido (só tokens de `:root` e `--cor-regiao`)

## 5. Verificação

- [ ] 5.1 Rodar `php -S localhost:8001`: abrir o mapa e conferir profundidade, partículas por bioma e a revelação ao rolar
- [ ] 5.2 Entrar em fases de regiões diferentes e confirmar que a arena usa o fundo do bioma, com chão/brilho preservados e partículas ambientais atrás dos combatentes
- [ ] 5.3 Ativar `prefers-reduced-motion` e confirmar que a ambientação desliga sem quebrar a legibilidade
- [ ] 5.4 `php -l` nas views/controller alterados e checar zero regressão na batalha (Fase 1 intacta)
