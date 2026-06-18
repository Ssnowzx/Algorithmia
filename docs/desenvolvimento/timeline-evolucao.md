# Timeline de Evolucao — Algorithmia

[← Inicio](README.md)

Linha do tempo da evolucao visual e tecnica do jogo. Preserva marcos sem apagar
o passado — arte nova nao substitui o registro da antiga.

Para folhear a evolucao visual em PDF: `docs/evolucao-visual/Evolucao-Visual.pdf`.
Arquivos curados por versao: `docs/evolucao-visual/`.

---

## v1 — Pixel Art (commit `1423d38`)

**Marco:** primeiro conjunto de assets autorais gerados via pipeline Python.

**Arte:**
- Sprites em pixel art de todos os personagens: mestres, inimigos, itens, heroi, icones de UI.
- Gerados por scripts em `tools/`: `pixelart.py`, `bestiario.py`, `itens.py`, `ui.py`.
- Helper `svg()` em `helpers.php` integra os assets nas views.
- Fotos reais dos professores como referencia (`docs/evolucao-visual/v1-pixel-art/_referencia-fotos-professores/`).
- Fichas de galeria (`_galeria-contact-sheets/`).

**Tecnico:**
- Estrutura MVC artesanal estabelecida: `index.php`, `Router`, `Controller`, `Model`, `Auth`.
- Banco de dados: `schema.sql` + `seeds.sql` + `migrate.php` idempotente.
- Pool de desafios por fase com anti-repeticao (`BatalhaService::sortearDesafios`).
- 6 classes jogaveis com lore: mago, guerreiro, ranger, xeno, elfo, draconato.
- Sistema de reputacao (-100..+100) ligado aos 3 finais narrativos.
- Som procedural (`window.SOM`) e juice de batalha (`window.JUICE`).

**Arquivo historico:** `docs/evolucao-visual/v1-pixel-art/`

---

## v2 — Ilustracoes (atual)

**Marco:** substituicao do pixel art por ilustracao digital de alta resolucao.

**Arte:**
- Cards dos 5 mestres pintados (`public/img/mestres/`).
- Cenarios de fundo por regiao (`public/img/fundos/`): fundo-porto, fundo-cidadela, etc.
- Retratos HUD dos herois para o header (`public/img/herois/hud-*.png`).
- Mapas de fase: 35 icones ilustrados (`public/img/mapas/fase-*.png`).
- Atores de dialogo (`public/img/atores/`).
- Logos da marca: `logo-marca-ilustrado.png` (splash/auth) e `logo-header.png`.
- Emblema vetorial SVG: `public/img/ui/logos/emblema-algorithmia.svg`.

**Pipeline de recorte:**
- Introducao do `tools/recortar_rembg.py` com modelo `isnet-general-use`.
- Inimigos e herois recortados com transparencia real (sem fundo branco).
- 29 sprites finais convertidos para WebP (reducao de ~34 MB por sessao).

**Tecnico:**
- `srcImagem()` prefere `.webp` sobre `.png` com versioning por `filemtime`.
- `caminhoSvg()` e `svgAtor()` para resolucao de slugs por prefixo.
- `marcaHtml()` centraliza a logo em todas as variantes (header/auth/splash).
- Kit de marca PDF: `tools/marca_pdf.py` (emblema, fontes Cinzel/Pixelify/Rubik, paleta).
- Codex narrativo: `docs/codex/` (bestiario, mestres, jornada, evolucao visual).
- 4 camadas de historia + lore das 6 classes integradas.
- Novas classes via migration ENUM: `database/migrations/20250616-novas-classes.sql`.
- Banco de questoes ampliado com materia de calculo: `database/banco-questoes/calculo.php`.

**Arquivo historico:** `docs/evolucao-visual/v2-ilustracoes/`

---

## Auditoria tecnica (2026-06-18)

**Marco:** auditoria profunda conduzida por 6 agentes especialistas em paralelo.

**Correcoes aplicadas:**
- Logout completo: `Auth::logout()` agora destroi a sessao e expira o cookie.
- Schema alinhado: ENUM `assunto` ganhou valor `calculo` em `schema.sql`.
- Assets mortos removidos: `public/img/atores/mestre-*.{png,webp}` (pixel art orfao).
- Pasta duplicada removida: `docs/galeria/` (copia MD5-identica de `evolucao-visual/`).
- Raiz limpa: PDF de produto movido para `docs/produto/`.
- WebP dos sprites finais: 29 PNGs (inimigos + herois) convertidos.
- Assets de UI reorganizados em subpastas (`ui/icones/`, `ui/trofeus/`, `ui/logos/`).
- Titulo do mapa limpo (moldura ornamental removida).

**Debitos identificados (pendentes):**
- CSRF em GET e em endpoints AJAX de batalha.
- `beginTransaction` em `concederRecompensas`.
- UPDATE atomico no ouro (TOCTOU na loja).
- Rate limit no login.
- 10 conquistas sem trigger.
- Reputacao invisivel ao jogador.
- 48+ PNGs sem WebP (itens aguardam redesign).
- CSP ausente.
- `color-mix()` sem fallback para Safari <= 15.

Ver laudo completo em `docs/auditoria/README.md`.

---

## Proxima versao (planejada)

**v3 — Itens redesenhados + Seguranca**

- Redesign dos 19 itens: objeto transparente, sem moldura (briefing v3: `docs/briefing-arte-v3.md`).
- Lote de seguranca: CSRF completo, transacoes, UPDATE atomico.
- Balanceamento: Elfo, especial das classes, triggers das conquistas.
- Design system: `public/css/tokens.css` + componentes unificados de card/botao.

Quando aplicado: criar `docs/evolucao-visual/v3-itens-redesenhados/` com snapshot dos PNGs.
