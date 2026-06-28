# Protótipo de direção visual — Algorithmia (Mapa + Perfil)

Pré-visualização **estática, isolada e descartável** da direção de produto proposta. **Não toca no
jogo vivo** (nenhum PHP, nenhuma rota, nenhum CSS de produção) — só lê os assets reais de
`../../public/img`. Serve para você **julgar a direção antes** de qualquer mudança no jogo.

## Como abrir
- **Recomendado:** na raiz do repositório, `php -S localhost:8000` e acesse
  <http://localhost:8000/docs/prototipo/index.html> (o servidor embutido serve os `.html` direto, sem
  passar pelo router do jogo).
- **Alternativa:** abrir `index.html` direto no navegador (`file://`). A View Transition entre páginas
  exige um servidor same-origin (HTTP) para ativar; em `file://` a navegação ainda funciona (degrada
  para corte seco, exatamente como o fallback de produção).
- Para sentir a **transição de página**, use um navegador Chromium 126+ ou Safari 18.2+ (suporte a
  View Transitions cross-document — ver `../auditoria-ux/pesquisa/01-navegacao-e-motion.md`).

## O que demonstra (microinterações-assinatura)
1. **Navegação inferior** — 5 setores na faixa do polegar, alvos ≥44px, `aria-current="page"` e
   indicador ativo animado com `scaleX`.
2. **View Transitions cross-document** — ao navegar Mapa ↔ Perfil, o shell (topo + nav) permanece e o
   conteúdo faz cross-fade (continuidade tipo-SPA, **zero JS**, puro `@view-transition` em CSS).
3. **Reveal escalonado** — regiões/nós/cards entram via `IntersectionObserver` com `stagger`.
4. **Preenchimento de barras** — HP/MP/XP e domínio por matéria animam via `transform: scaleX` (compositor).
5. **Burst de recompensa** — botão "subir de nível"/"testar recompensa" dispara um burst de partículas
   em `<canvas>` (pool simples, sem dependência) + `pop` com `ease-spring`.

E a peça central da pesquisa de gamificação: o **medidor de Reputação visível** (Disciplina ↔
Singularidade) no Perfil — hoje a mecânica é invisível no jogo.

## Acessibilidade — teste obrigatório
Ative `prefers-reduced-motion: reduce` (DevTools → **Rendering** → *Emulate CSS
prefers-reduced-motion*) e recarregue. **Tudo deve colapsar sem nada fora do lugar:** sem
movimento/parallax/partículas/pulso; os fades de opacidade e as cores permanecem (fora do escopo de
WCAG 2.3.3); o conteúdo aparece imediatamente no estado final.

## O que é intencionalmente "fake"
- Dados (nome, níveis, fases, %), estáticos e ilustrativos.
- Apenas 3 regiões no mapa e um recorte do perfil (suficiente para a direção).
- Ícones da nav são SVG inline simples (no jogo, idem — não os PNGs ilustrados, que não tingem por estado).
- Não há back-end: botões de demo só acionam as microinterações.

## Fundamentação
Cada decisão visual/motion vem dos relatórios verificados em
[`../auditoria-ux/pesquisa/`](../auditoria-ux/pesquisa/README.md) (P1–P5, verificação adversarial
3-votos). O CSS (`prototipo.css`) documenta no topo o **contrato de classes** e a **fonte** de cada
decisão. Este protótipo é um **espelho da direção**, não código de produção — ao aprovar, as decisões
migram para `tokens.css`/`components.css`/`header.php` do jogo de forma incremental e segura
(ver `roadmap-priorizado.md`).
