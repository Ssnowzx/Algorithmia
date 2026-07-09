# 🛡️ Auditoria Técnica — Algorithmia (Fase 1)

> ## ⚠️ Este documento está DESATUALIZADO
>
> Ele é um retrato de **2026-06-18**. Os dois débitos que marca como críticos **já
> foram corrigidos**:
>
> - **CSRF via GET** — `Controller::exigirCsrf` hoje bloqueia qualquer método que não
>   seja POST. No port, todas as 11 rotas de escrita são POST, com teste de 405 no GET.
> - **Sem transação no fluxo de recompensa** — `RecompensaService::conceder` envolve as
>   sete escritas numa transação. No port, a concessão ainda ganhou uma **chave de
>   idempotência no banco**, no lugar do flag de sessão.
>
> **Leia o código antes de citar esta auditoria.** As demais observações podem valer;
> confirme cada uma.

Auditoria profunda conduzida por **6 agentes especialistas em paralelo** (arquitetura/backend,
frontend/UX, segurança, performance, organização/docs/assets, game design), com **validação
cruzada** e consolidação por um líder técnico. Cada área tem um laudo detalhado:

| Área | Laudo |
|---|---|
| Backend & Arquitetura | [backend-arquitetura.md](backend-arquitetura.md) |
| Frontend & UX | [frontend-ux.md](frontend-ux.md) |
| Segurança | [seguranca.md](seguranca.md) |
| Performance | [performance.md](performance.md) |
| Organização, Docs & Assets | [organizacao-docs-assets.md](organizacao-docs-assets.md) |
| Game Design & Conteúdo | [game-design-conteudo.md](game-design-conteudo.md) |

> **Cross-validação:** achados confirmados por ≥2 agentes independentes recebem confiança alta —
> ex.: *race condition no ouro* (backend + segurança), *webp ausente* (performance + organização),
> *XSS em href* (frontend + segurança). Marcados com 🔁 abaixo.

---

## 1) Problemas encontrados (por severidade)

### 🔴 Críticos
- **CSRF via GET** 🔁 — `exigirCsrf()` só valida POST; rotas como `loja/comprar/5`, `inventario/descartar/5`, `mestre/excluirFase/5` são acionáveis por link/`<img>` de terceiro. (`app/core/Auth.php`, controllers)
- **CSRF ausente nos endpoints AJAX de batalha** — `responder/fragmento/pocao/especial/fugir` recebem JSON e não checam token. (`BatalhaController.php`)
- **Sem transação em `concederRecompensas`** — 7 escritas (XP, ouro, progresso, drop, capítulo, conquistas, reputação) sem `beginTransaction`; crash no meio corrompe o personagem.
- **Elfo inviável nos chefes** — HP 75 / def 4 → morre em 3–4 erros em F27/F33/F35. (`config.php` CLASSES + balanceamento de chefes)
- ~~**10 de 17 conquistas sem trigger**~~ → **CORRIGIDO na re-verificação: era apenas 1 (`colecionador`)**. As de região, `puro_de_coracao` e os 3 finais JÁ eram concedidas (BatalhaController + HistoriaController). O laudo de game design fez passe raso; ver "Atualização Fase 2". ✅ `colecionador` implementada.

### 🟠 Altos
- **Race condition no ouro (TOCTOU)** 🔁 — saldo lido e gravado em statements separados; compras concorrentes corrompem o ouro. (`LojaController`)
- **`Escolha::definir` não-atômica** — DELETE+INSERT sem `UNIQUE(personagem_id, codigo)`; pode duplicar a escolha do final.
- **XSS potencial no resultado de batalha** 🔁 — `batalha.js:~372` injeta `rec.redirect_final` em `href` sem escape.
- **Logout incompleto** — só `unset`, sem `session_destroy` (cookie seguia válido). ✅ *corrigido nesta rodada.*
- **Sem rate-limit no login** + senha mínima de 6 — força-bruta irrestrita.
- **Guerreiro vs Mago desbalanceado** — especial 1× (Guerreiro) vs 4× (Mago); desincentiva o corpo-a-corpo.
- **Dois sistemas de card** (`.item-card` × `.carta-loja`) sem tokens compartilhados — sem padrão visual global.
- **SQL subrepresentado** — só 7 questões, sem JOIN/subconsulta/DDL.

### 🟡 Médios
- **48 PNGs sem `.webp`** 🔁 (~40–70 MB/sessão). ✅ *parcial: 29 sprites finais convertidos; itens pendem de redesign.*
- **Imagens gigantes em slots pequenos** — 1024–1536px servidas em 50–270px (desperdício 5–30×).
- **Google Fonts carregado 2×** (link no header + `@import` no `style.css`) — *adiado: `registro.php` depende do `@import`; exige correção coordenada.*
- **CSS global** — `batalha.css`/`cena.css` carregados em todas as páginas.
- **Índices faltando** em `respostas_log` (`personagem_id, usou_ia` / `personagem_id, desafio_id`).
- **IDs de fase hardcoded** — `HistoriaController` (`id=35`/`ordem=34`), `ConquistaService` (`[8,14,20,32]`).
- **`color-mix()` sem fallback** (47×, quebra Safari ≤15).
- **Schema ENUM `assunto`** sem `calculo` em `schema.sql`. ✅ *corrigido nesta rodada.*
- **Sem CSP**; **IP de produção hardcoded** em `seed_remote.sh`.
- ~~**Reputação invisível** ao jogador~~ → **INCORRETO: a reputação aparece no HUD (header), no perfil e no ranking.** (claim equivocado do laudo de game design)
- **A11y:** labels sem `for/id`, `alt` ausente em imagens.
- **9 docs SCREAMING_CASE** violam `lowercase-com-hífen`; **3 changes OpenSpec** não arquivadas.

### ✅ Pontos fortes confirmados
PDO `EMULATE_PREPARES=false` (sem SQLi), `password_hash/verify`, `session_regenerate_id(true)` no login, `colunaSegura()`, `e()`/`htmlspecialchars(ENT_QUOTES)` em toda saída, gabarito nunca exposto ao cliente, scripts de banco com guarda `PHP_SAPI==='cli'`, `.htaccess` bloqueando pastas sensíveis, anti-repetição com pool por fase.

---

## 2) Correções aplicadas (seguras, nesta rodada)
- 🔒 **Logout completo** — `Auth::logout()` agora zera a sessão, expira o cookie e `session_destroy()`.
- 🧩 **Schema alinhado** — `schema.sql` ENUM `assunto` ganhou `calculo` (instalações limpas não quebram mais).
- 🧹 **Assets mortos removidos** — `public/img/atores/mestre-*.{png,webp}` (10, pixel art órfã).
- 🧹 **Pasta duplicada removida** — `docs/galeria/` (cópia MD5-idêntica de `evolucao-visual/.../_galeria-contact-sheets`).
- 📦 **Raiz limpa** — `Xiax-Plano-de-Produto-EdTech-v1.1.pdf` → `docs/produto/`.
- ⚡ **WebP dos sprites finais** — 29 PNGs (inimigos + heróis) → ~34 MB mais leve no que é servido (`srcImagem()` já prefere `.webp`).

### Atualização — Fase 2 (aplicado depois da consolidação)
- 🔒 **Lote 1 (segurança+integridade):** `exigirCsrf()` exige POST+token (fecha CSRF via GET); novo `exigirCsrfAjax()` (header `X-CSRF-Token`) nos 5 endpoints de batalha; `concederRecompensas()` em **transação**.
- 🎨 **Lote 4 (design system, fundação):** `public/css/tokens.css` (aditivo) + `docs/desenvolvimento/design-system.md`.
- 📚 **Lote 3 (processo):** `docs/desenvolvimento/` — padrões, convenções, fluxos, criação de conteúdo/assets, QA, releases, checklists e timeline.
- 🎮 **Lote 2 (jogo):** conquista `colecionador` implementada (era a única órfã real).
- ⚠️ **Nota de confiabilidade:** o laudo de **game design** se mostrou o menos confiável (passe raso) — superestimou conquistas órfãs (10→1) e errou ao dizer que a reputação é invisível. Trate suas conclusões de **balanceamento** (Elfo/chefes/especial) como hipóteses a validar por playtest, não fatos.

## 3) Melhorias propostas (priorizadas)
1. **Segurança:** CSRF em GET e nos endpoints AJAX; `escapeHtml` no redirect do `batalha.js`; rate-limit no login; CSP.
2. **Integridade:** envolver `concederRecompensas`, compra/venda e inventário em **transações**; `UNIQUE(personagem_id, codigo)` em `escolhas`.
3. **Balanceamento:** revisar HP/def do Elfo e dano dos chefes (F27/F33/F35); equilibrar especial entre classes; implementar **triggers das 10 conquistas** órfãs; tornar a **reputação visível** (HUD).
4. **Conteúdo:** ampliar SQL (JOIN/subconsulta/DDL) no banco de questões.
5. **Performance:** **versões responsivas** das imagens (downscale por contexto); CSS por rota; índices em `respostas_log`; unificar 3 COUNTs do `PerfilController`.
6. **Frontend:** **design system** (tokens + componente único de card/botão); fallback de `color-mix()`; corrigir fontes/a11y do `registro` e do form do mestre.

## 4) Débitos técnicos restantes
IDs de fase hardcoded (acoplamento conteúdo↔código); ausência de testes automatizados; `schema.sql` e `migrations/` exigem disciplina manual de sincronização; itens da loja ainda no formato errado (redesign via [briefing-arte-v3](../arte/briefing-arte-v3.md)); changes OpenSpec não arquivadas; docs em SCREAMING_CASE.

## 5) Mudanças de arquitetura (propostas, não aplicadas)
- **Camada transacional** nos services que fazem múltiplas escritas (Batalha/Loja/Inventário).
- **Conteúdo dirigido por dados** — substituir IDs de fase hardcoded por consultas por `tipo`/flag.
- **Sistema de conquistas por evento** — um despachante que avalia gatilhos, eliminando conquistas órfãs.
- **Design system de UI** — `public/css/tokens.css` + componentes `.card`/`.botao` reaproveitáveis.
- **Pipeline de imagem responsiva** — variantes por tamanho + `srcset`/`<picture>`.

## 6) Organização de docs e assets
- **Aplicado:** raiz sem PDF solto; `atores/mestre-*` e `docs/galeria/` removidos.
- **Proposto:** padronizar docs para `lowercase-com-hífen`; consolidar docs soltas sob `docs/` por assunto (já temos `docs/codex`, `docs/materias`, `docs/evolucao-visual`, `docs/auditoria`, `docs/produto`); arquivar changes OpenSpec concluídas; mover/normalizar nomes de scripts em `tools/`.

## 7) Próximos passos recomendados
1. Aprovar e aplicar o **lote de segurança** (CSRF + transações) — maior risco × maior valor.
2. **Balanceamento + conquistas** (bugs de jogabilidade que afetam o jogador agora).
3. **Design system** + redesign dos itens (briefing-arte-v3) → resolve o "padrão global".
4. **Imagens responsivas** (maior ganho de performance percebida).
5. Criar **skills, specs e checklists** (padrões de código, criação de conteúdo/assets, QA, releases) — Fase 3.

## 8) Roadmap técnico
- **Curto prazo (sprint 1):** segurança (CSRF/transações), balanceamento, conquistas, redesign de itens.
- **Médio prazo:** design system, imagens responsivas, índices/queries, testes automatizados (PHPUnit) dos services críticos (Batalha/Progressão/Reputação), CI básico.
- **Longo prazo:** conteúdo dirigido por dados (editor de fases sem tocar código), painel do mestre completo, telemetria de aprendizado, internacionalização, separar `public/` como document-root real.
