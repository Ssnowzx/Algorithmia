# Auditoria — Performance & Entrega (imagens)

Esta dimensão avalia como o Algorithmia entrega seus bytes ao jogador — sobretudo as imagens, que dominam o peso de um RPG ilustrado servido por PHP MVC sem build. Hoje o jogo serve artes de 1024–1536px em slots de 50–270px (desperdício de 5–30×), tem ~48 PNGs sem `.webp`, emite `<img src>` único via `srcImagem()`/`svg()` (sem `<picture>`/`srcset`), não declara `width`/`height` (CLS) e carrega 5 folhas CSS globalmente. As recomendações abaixo são todas viáveis no HTML/servidor, sem ferramentas de build, e estão ordenadas por prioridade.

---

### 1. Artes oversize servidas em slots minúsculos (right-size offline)
- **Descrição:** O jogo serve PNG/WebP de 1024–1536px em slots de exibição de 50–270px (mestres, heróis, inimigos, itens, fundos). `srcImagem()` (`app/core/helpers.php:63-77`) só escolhe entre `.webp` e `.png` na resolução cheia — não existe variante redimensionada. O navegador baixa e decodifica 5–30× mais pixels do que pinta na tela.
- **Impacto na UX:** LCP alto e tela "pesada" justamente no primeiro contato (mapa, arena, loja), pior em mobile/3G — onde a maioria dos jogadores está. Decodificação de imagens gigantes também trava a thread e atrasa a interação inicial.
- **Evidencia / boa pratica:** [Consenso] O maior ganho de bytes vem do **redimensionamento (≥80–95%)**, não do formato (~25–50%) — fazer os dois, nessa ordem. [Boa prática] Gerar variantes **offline** (script Python/`cwebp`/`avifenc`), poucas larguras escalonadas, e deixar o PHP só montar o markup.
- **Referencia:** `pesquisa/05-arquitetura-performance.md` (Eixos 1, 2 e 4; síntese item 1). <https://developer.mozilla.org/en-US/docs/Web/HTML/Guides/Responsive_images> · <https://almanac.httparchive.org/en/2025/page-weight>
- **Solucao proposta:** Criar `tools/gerar_variantes.py` que gera offline: itens/ícones de tamanho fixo → density 64/128px; artes que variam (mestre/herói/fundo) → 3–4 larguras `w` (300/600/900/1200), preservando alpha. Salvar ao lado dos originais e versionar por `?v=filemtime` como já se faz. PHP apenas referencia as variantes (ver achado 3).
- **Prioridade:** Alta
- **Dificuldade:** Alta
- **Impacto esperado:** Retenção — é a maior alavanca de LCP e peso de página; reduz abandono nas telas iniciais, sobretudo em mobile.

---

### 2. Imagens sem `width`/`height` causam CLS
- **Descrição:** O helper `svg()` (`app/core/helpers.php:79-90`) e `marcaHtml()` (`:95-119`) emitem `<img>` sem atributos `width`/`height` (nem `aspect-ratio`). O navegador não reserva espaço, e o layout pula quando cada arte termina de carregar.
- **Impacto na UX:** Conteúdo "saltando" enquanto sprites/itens/retratos chegam — o jogador clica no lugar errado, a leitura do códex/loja é interrompida. É a causa clássica de CLS ruim.
- **Evidencia / boa pratica:** [Fato validado] `width`+`height` (ou `aspect-ratio`) em **TODA** `<img>` reserva espaço e mata o CLS (meta ≤0,1); dimensão intrínseca vai nos atributos, o CSS controla a exibição. [Fato validado] Imagem sem `width`/`height` = causa clássica de CLS.
- **Referencia:** `pesquisa/05-arquitetura-performance.md` (Eixos 3 e 4; síntese item 2). <https://web.dev/articles/optimize-cls>
- **Solucao proposta:** Adicionar `width`/`height` intrínsecos no markup gerado por `svg()`/`marcaHtml()`. Como o script offline (achado 1) já conhece as dimensões de cada variante, gravar um manifesto (slug → w×h) lido pelo helper; o CSS de cada slot continua ditando o tamanho visual.
- **Prioridade:** Alta
- **Dificuldade:** Baixa
- **Impacto esperado:** Usabilidade — elimina saltos de layout e cliques perdidos; melhora direta de CLS com esforço mínimo.

---

### 3. `<img src>` único quebra em navegador sem WebP (migrar helper para `<picture>`)
- **Descrição:** `srcImagem()` seleciona server-side por **existência de arquivo** (`app/core/helpers.php:70-73`) e `svg()` emite um `<img src>` único. Como o `.webp` quase sempre existe, ele é escolhido e servido a todos — **não há** `<picture>`/`<source>`/`srcset` nem negociação por `Accept`. Navegadores sem suporte a WebP recebem `<img src="...webp">` e mostram **imagem quebrada**.
- **Impacto na UX:** Em clientes antigos/atípicos sem WebP, partes do jogo aparecem como ícone quebrado — falha visível, não degradação graciosa. Também bloqueia adotar AVIF e o right-size por `srcset` (achados 1 e 4).
- **Evidencia / boa pratica:** [Fato validado] Cadeia `<picture>` AVIF→WebP→PNG (o browser usa o 1º `type` suportado) — HTML puro, sem JS/build; o `<img>` PNG é o fallback real. Suporte WebP ~96% / AVIF ~93%, seguros **com** fallback. **[REFUTADA R2.1]:** a ideia de que "o PNG cobre os ~4%" **não vale no código atual**, pois não há `<picture>`; a recomendação só passa a valer **após** migrar o helper.
- **Referencia:** `pesquisa/05-arquitetura-performance.md` (Eixos 1 e 2; nota R2.1; síntese item 4). <https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/picture> · <https://caniuse.com/webp>
- **Solucao proposta:** Evoluir `srcImagem()`/`svg()` para emitir `<picture>` com `<source type="image/avif">` → `<source type="image/webp">` → `<img>` PNG, anexando `srcset`/`sizes`/`width`/`height`/`loading`. Manter o `?v=filemtime`. Este é pré-requisito dos achados 1 e 4.
- **Prioridade:** Alta
- **Dificuldade:** Media
- **Impacto esperado:** Usabilidade — corrige imagem quebrada (bug real) e destrava formatos modernos e responsividade.

---

### 4. `loading="lazy"` aplicado a tudo, inclusive ao LCP
- **Descrição:** `svg()` força `loading="lazy"` em **todas** as imagens (`app/core/helpers.php:89`). Não há distinção por posição na página, então a arte principal acima da dobra (LCP — retrato do mestre na arena, fundo da cena) também é adiada, e nenhuma imagem recebe `fetchpriority`.
- **Impacto na UX:** `lazy` no LCP é anti-padrão: perde o preload scanner e atrasa a maior imagem visível — exatamente o que o jogador espera ver primeiro. Telas iniciais demoram mais a "fechar".
- **Evidencia / boa pratica:** [Fato validado] **NUNCA** `loading="lazy"` no LCP/above-the-fold — A/B do web.dev: lazy em tudo regrediu o LCP; excluir o above-the-fold reverteu (−18%). [Fato validado] `loading="lazy"` **só abaixo da dobra**. [Fato validado] `fetchpriority="high"` no LCP elevou o LCP do Google Flights de 2,6→1,9s (~−27%); não usar em mais de 1–2 imagens.
- **Referencia:** `pesquisa/05-arquitetura-performance.md` (Eixo 3; síntese itens 3 e 5). <https://web.dev/articles/lcp-lazy-loading> · <https://web.dev/articles/fetch-priority>
- **Solucao proposta:** Adicionar parâmetro de posição ao `svg()`: above-the-fold → `loading="eager"` + (no LCP da rota) `fetchpriority="high"`; below-the-fold → `loading="lazy"`. Identificar o LCP **real** de cada rota antes de marcar `fetchpriority` (tratar LCP em `background-image` à parte). `decoding="async"` como default barato.
- **Prioridade:** Alta
- **Dificuldade:** Media
- **Impacto esperado:** Retenção — telas iniciais "fecham" mais rápido; menos espera percebida no primeiro contato.

---

### 5. Sem orçamento de performance (performance budget) p75 mobile
- **Descrição:** O projeto não define metas mensuráveis de Core Web Vitals nem teto de peso/requests por tela, então regressões de imagem passam despercebidas.
- **Impacto na UX:** Sem budget, cada arte nova pode degradar silenciosamente o carregamento; a percepção de lentidão cresce sem ninguém notar até o jogador reclamar.
- **Evidencia / boa pratica:** [Fato validado] Limiares 2026 (p75): LCP ≤2,5s, INP ≤200ms (substituiu FID em 12/03/2024), CLS ≤0,1. [Fato validado] CWV são avaliados no **p75 de campo (CrUX)** → otimizar p75 mobile, não média de lab. [Fato validado] Budget de peso: mediana ~2,3–2,9MB com imagens ~37–40%; **alvo abaixo da mediana: ≤1MB de imagem e ≤15 requests por tela**. Ressalva honesta: INP depende do JS da batalha, não das imagens.
- **Referencia:** `pesquisa/05-arquitetura-performance.md` (Eixo 4). <https://web.dev/articles/defining-core-web-vitals-thresholds> · <https://almanac.httparchive.org/en/2025/page-weight>
- **Solucao proposta:** Adotar a tabela-alvo (p75 mobile): LCP ≤2,5s (interno ≤2,0s), INP ≤200ms, CLS ≤0,1, TTFB ≤800ms, ≤1MB de imagem/tela, ≤15 requests de imagem/tela. Medir por rota (Lighthouse/CrUX) e registrar como checklist de QA antes de subir novas artes.
- **Prioridade:** Media
- **Dificuldade:** Baixa
- **Impacto esperado:** Engajamento — guarda-corpo que mantém a velocidade ganha nos achados 1–4 ao longo do tempo (efeito qualitativo, não garante número).

---

### 6. Cache em camadas + Brotli (sem `immutable` arriscado)
- **Descrição:** O versionamento é por query-string `?v=filemtime` (`assetV()` em `app/core/helpers.php:35-40`; `srcImagem()` em `:73`), não por fingerprint no nome. Não há política explícita de `Cache-Control` por tipo de recurso nem confirmação de Brotli em produção.
- **Impacto na UX:** Sem cache longo nos assets, jogadores recorrentes rebaixam imagens/CSS a cada visita; sem Brotli, HTML/CSS/JS chegam maiores. Por outro lado, aplicar `immutable` em URL estável serviria conteúdo obsoleto.
- **Evidencia / boa pratica:** [Fato validado] Cache em camadas: assets com fingerprint → `max-age=31536000` (+ `immutable` **só** se a URL muda a cada conteúdo); HTML do PHP → `no-cache`. [Fato validado] Brotli (fallback gzip) reduz HTML/CSS/JS ~15–25% vs gzip. **[REFUTADA R5.1]:** `immutable` **não** é "seguro sem risco" — com `?v=filemtime` (URL estável), browsers que o honram servem conteúdo obsoleto por todo o `max-age`. Usar só `max-age=31536000`; `immutable` apenas com fingerprint consistente em 100% das referências.
- **Referencia:** `pesquisa/05-arquitetura-performance.md` (Eixo 5; nota R5.1; síntese item 6). <https://csswizardry.com/2019/03/cache-control-for-civilians/>
- **Solucao proposta:** No servidor (Apache/Nginx ou cabeçalhos PHP): `Cache-Control: public, max-age=31536000` para `public/img|css|js` (**sem** `immutable` enquanto for `?v=filemtime`); `Cache-Control: no-cache` para o HTML renderizado pelo PHP. Habilitar Brotli (pré-comprimido offline ou on-the-fly) com fallback gzip. Confirmar se já está ativo antes de mexer.
- **Prioridade:** Media
- **Dificuldade:** Media
- **Impacto esperado:** Usabilidade — visitas recorrentes mais rápidas e payload menor; ganho real sem risco de servir versão velha.

---

### 7. CSS global em vez de por rota + `<script>` sem `defer`
- **Descrição:** `app/views/layout/header.php:27-31` carrega 5 folhas CSS em **todas** as telas (`tokens`, `style`, `cena`, `mapa`, `batalha`), inclusive onde não se usa mapa nem batalha. Os `<script>` próprios (som → juice → batalha) devem ser carregados na ordem certa e sem bloquear o parse.
- **Impacto na UX:** CSS não usado atrasa o render inicial de telas simples (login, registro, códex); scripts sem `defer` podem bloquear o parser. Custo desnecessário em mobile.
- **Evidencia / boa pratica:** [Consenso] HTTP/2 dispensa concatenar os 5 CSS (multiplexing), mas convém **incluir CSS/JS por rota** no PHP e auditar a aba Coverage do DevTools. [Fato validado] `defer` em todo `<script>` próprio, mantendo a ordem. [Opinião] Critical CSS **não** é prioridade aqui. **[REFUTADA R5.2]:** PE total **não** se aplica à batalha — `arena.php` preenche o painel por JS e as ações retornam JSON; tratar a batalha como app JS deliberada (não re-arquitetar o loop), aplicando PE só ao conteúdo/forms (login/registro/nav/códex).
- **Referencia:** `pesquisa/05-arquitetura-performance.md` (Eixo 5; nota R5.2; síntese item 7). <https://csswizardry.com/2019/03/cache-control-for-civilians/>
- **Solucao proposta:** Tornar os includes de CSS/JS condicionais por rota no header/layout (ex.: só carregar `batalha.css` na arena, `mapa.css` no mapa); rodar a aba Coverage para confirmar o corte. Garantir `defer` em todos os `<script>` próprios na ordem som → juice → batalha. Manter a batalha como aplicação JS deliberada.
- **Prioridade:** Baixa
- **Dificuldade:** Media
- **Impacto esperado:** Usabilidade — render inicial mais leve em telas simples; sem bloqueio de parser (efeito moderado, depende da rota).
