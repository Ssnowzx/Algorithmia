/* ============================================================
   Algorithmia — Celebração global (confete) FORA da arena.
   Auditoria UX (Motion#6): generalizar o "juice" para os marcos fora do
   combate — conquista, compra, level-up. Canvas full-screen, autônomo,
   auto-limpo (some sozinho ao fim), pointer-events:none.
   Respeita prefers-reduced-motion: sem partículas (só o som, se já
   desbloqueado). Exposto como window.celebrar(); dispara sozinho nos
   flashes que representam um marco. Sem dependências.
   ============================================================ */
(function () {
    'use strict';
    var mqReduz = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : { matches: false };
    var CORES = ['#ffd23f', '#7c5cff', '#9d83ff', '#8ce6ff', '#2ecc71', '#ff5d6c'];

    // Burst radial de confete num canvas próprio; remove-se ao acabar.
    function burst(opts) {
        var canvas = document.createElement('canvas');
        var dpr = Math.min(2, window.devicePixelRatio || 1);
        canvas.width = Math.floor(innerWidth * dpr);
        canvas.height = Math.floor(innerHeight * dpr);
        canvas.style.cssText = 'position:fixed;inset:0;width:100%;height:100%;pointer-events:none;z-index:2000';
        document.body.appendChild(canvas);
        var ctx = canvas.getContext('2d');
        ctx.scale(dpr, dpr);

        var cx = opts.x != null ? opts.x : innerWidth / 2;
        var cy = opts.y != null ? opts.y : innerHeight * 0.38;
        var N = opts.n || 120, parts = [];
        for (var i = 0; i < N; i++) {
            var a = Math.PI * 2 * (i / N) + (i % 7) * 0.13;
            var sp = 4 + (i % 6) * 1.7;
            parts.push({
                x: cx, y: cy, vx: Math.cos(a) * sp, vy: Math.sin(a) * sp - 3.5,
                life: 0, max: 56 + (i % 36), cor: CORES[i % CORES.length], s: 3 + (i % 3)
            });
        }

        var raf, morto = false;
        function limpar() {
            if (morto) { return; }
            morto = true;
            cancelAnimationFrame(raf);
            if (canvas.parentNode) { canvas.remove(); }
        }
        function tick() {
            ctx.clearRect(0, 0, innerWidth, innerHeight);
            var vivos = 0;
            for (var k = 0; k < parts.length; k++) {
                var p = parts[k];
                if (p.life >= p.max) { continue; }
                vivos++;
                p.life++; p.vy += 0.22; p.vx *= 0.99; p.x += p.vx; p.y += p.vy;
                ctx.globalAlpha = Math.max(0, 1 - p.life / p.max);
                ctx.fillStyle = p.cor;
                ctx.fillRect(p.x, p.y, p.s, p.s * 1.7);
            }
            if (vivos > 0) { raf = requestAnimationFrame(tick); }
            else { limpar(); }
        }
        raf = requestAnimationFrame(tick);
        // Rede de segurança: se o rAF pausar (aba em background), remove mesmo assim.
        setTimeout(limpar, 4000);
    }

    /**
     * Dispara uma celebração. opts: {x, y, n, som:false, forcar:true}.
     * Sob prefers-reduced-motion não há partículas (a não ser opts.forcar);
     * o som de vitória é best-effort (pode estar bloqueado até o 1º gesto).
     */
    function celebrar(opts) {
        opts = opts || {};
        if (opts.som !== false && window.SOM && typeof window.SOM.vitoria === 'function') {
            try { window.SOM.vitoria(); } catch (e) { /* áudio ainda bloqueado */ }
        }
        if (mqReduz.matches && !opts.forcar) { return; }
        burst(opts);
    }
    window.celebrar = celebrar;

    // Auto-dispara quando a página abre com um flash de MARCO (conquista/compra).
    // Flashes mundanos (equipar, vender, CRUD do mestre) não casam o padrão.
    var f = document.querySelector('.flash');
    if (f && /🏆|[Cc]onquista|comprad/.test(f.textContent || '')) {
        requestAnimationFrame(function () { celebrar(); });
    }
})();
