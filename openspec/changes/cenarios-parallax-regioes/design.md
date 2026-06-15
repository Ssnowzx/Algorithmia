## Context

Fase 2 da evolução visual. O mapa (`mapa.css` + `mapa/index.php`) já dá a cada `.regiao` um
fundo pixel art e `--cor-regiao`. A arena (`batalha.css` + `batalha/arena.php`) usa
`fundo-batalha.png` fixo, com chão em perspectiva (`.campo-batalha::before`) e brilho arcano.
Há 7 fundos por bioma em `public/img/fundos/` e o helper `fundoRegiao(mestreSlug)` mapeia
mestre → fundo. Camadas afetadas: **view** (mapa, arena), **controller** (BatalhaController só
para passar o fundo) e **assets** (`public/css`, `public/js`). Nenhum model/service muda.

## Goals / Non-Goals

**Goals:**
- Profundidade/parallax leve + partículas ambientais por bioma no mapa.
- Transição visível ao percorrer regiões (IntersectionObserver).
- Arena com fundo por bioma, preservando chão e brilho.
- `prefers-reduced-motion` respeitado; só tokens de `:root`/`--cor-regiao`.

**Non-Goals:**
- Microanimações de itens; estados de vitória/derrota dos sprites; trilha ambiente.
- Qualquer mudança em regra de batalha, progressão, banco ou validação.

## Decisions

### Partículas ambientais em CSS, não em rAF
- Ambientação é loop contínuo decorativo → **CSS declarativo** (pseudo-elementos animando
  `transform`/`opacity`), no padrão já usado por `body::before` (estrelas) e
  `.palco-cena::after` (partículas mágicas). **Por quê:** rAF permanente drenaria bateria; o
  sistema `JUICE` (Fase 1) é episódico e desliga quando ocioso — reaproveitá-lo para
  ambiente o manteria sempre ligado. CSS roda no compositor, sem custo de main thread.
- **Bioma → efeito** por classe na seção/arena (`.bioma-floresta`, `.bioma-montanha`,
  `.bioma-torre`, `.bioma-porto`, `.bioma-cidadela`, `.bioma-vila`): folhas caindo, brasas
  subindo, pacotes/linhas de rede, maresia/respingo, runas/glow. Cores via `--cor-regiao` e
  tokens (`--xp`, `--mp`, `--sucesso`, `--ouro`).

### Parallax leve sem JS de scroll
- Profundidade por **camadas empilhadas** na `.regiao` (fundo do bioma já existente + uma
  camada de névoa/brilho em pseudo-elemento com leve flutuação). Evita listener de `scroll`
  (jank); o "parallax" é sugerido por flutuação/opacidade das camadas, não por
  reposicionamento por scroll.

### Transição de região via IntersectionObserver
- Pequeno script (em `app.js`, com guarda de página de mapa) adiciona `.revelada` quando a
  `.regiao` entra na viewport; CSS faz fade/slide-up curto. **Guard:** se
  `prefers-reduced-motion`, marca todas como reveladas imediatamente (sem animação).
  Substitui/!convive com o scroll suave já existente até a fase atual.

### Arena por bioma (única mudança PHP)
- Em `BatalhaController::iniciar`, carregar o `Mestre` da fase (`$fase['mestre_id']`) e
  computar `$fundoBioma = fundoRegiao($mestre['svg_slug'])` e um `$bioma` (slug sem o
  prefixo `fundo-`). Passar ambos para a view. Fallback: `fundo-batalha` / sem classe de
  bioma quando a fase não tem mestre.
- `arena.php` aplica `style="--fundo-bioma:url(...)"` e `class="campo-batalha bioma-X"`.
  `batalha.css` troca o `url(...)` fixo por `var(--fundo-bioma, url(fundo-batalha))`,
  preservando os gradientes/relevo já existentes; a camada de partículas do bioma entra como
  pseudo-elemento atrás dos combatentes (`z-index` abaixo dos sprites, acima do fundo).

## Risks / Trade-offs

- **Legibilidade dos nós/combatentes sobre fundos mais vivos** → manter as sobreposições
  escuras (gradientes já presentes) e `pointer-events:none`/`z-index` baixo nas partículas.
- **Excesso de animação ambiental competindo com o juice de batalha** → partículas ambientais
  são poucas e lentas (CSS, opacidade baixa); o `JUICE` episódico continua por cima.
- **Mudança em PHP** (controller) → restrita a montar dados de view; coberta por fallback e
  sem alterar fluxo de batalha. Lint PHP + teste manual da arena de cada região.
- **IntersectionObserver em navegadores muito antigos** → guarda `('IntersectionObserver' in
  window)`; sem ele, regiões aparecem já reveladas (degradação graciosa).
