/* ============================================================
   Algorithmia — Motor de cena SVG (window.CENA)
   Câmera por viewBox + parallax de camadas + partículas pooled,
   tudo num único loop requestAnimationFrame. Anima só transform/
   viewBox. Pausa fora da viewport e sob prefers-reduced-motion.

   Uso:
     var cena = CENA.iniciar(svgEl, {
       motas: 18,            // partículas mágicas (0 desliga)
       pushIn: 1200,         // ms de zoom-in inicial (0 desliga)
       parallaxPonteiro: true
     });
     // cena.parar() quando sair da tela.

   No SVG: camadas recebem data-parallax="0.2" (distante) .. "1" (próxima).
   ============================================================ */
(function () {
    'use strict';

    var mq = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : { matches: false };
    var reduzido = !!mq.matches;
    if (mq.addEventListener) { mq.addEventListener('change', function (e) { reduzido = e.matches; }); }

    function iniciar(svg, opcoes) {
        if (!svg) { return { parar: function () {} }; }
        opcoes = opcoes || {};

        // viewBox base (unidades de mundo).
        var base = (svg.getAttribute('viewBox') || '0 0 1920 1080').split(/\s+/).map(Number);
        var VB = { x: base[0], y: base[1], w: base[2], h: base[3] };

        var camadas = [].slice.call(svg.querySelectorAll('[data-parallax]'));
        var alvoPx = 0, alvoPy = 0, curPx = 0, curPy = 0; // ponteiro normalizado [-1,1]
        var t0 = performance.now();
        var rodando = false, rafId = 0;

        // ---------- partículas (motas) com pooling ----------
        var motas = [];
        var camadaMotas = svg.querySelector('.cena-motas');
        var qtdMotas = reduzido ? 0 : (opcoes.motas || 0);
        if (qtdMotas && camadaMotas) {
            for (var i = 0; i < qtdMotas; i++) {
                var u = document.createElementNS('http://www.w3.org/2000/svg', 'use');
                u.setAttributeNS('http://www.w3.org/1999/xlink', 'href', '#sym-mota');
                u.setAttribute('href', '#sym-mota');
                u.setAttribute('width', '12'); u.setAttribute('height', '12');
                camadaMotas.appendChild(u);
                motas.push(resetMota({ el: u }, true));
            }
        }
        function resetMota(m, inicial) {
            m.x = Math.random() * VB.w;
            m.y = inicial ? Math.random() * VB.h : VB.h + 20;
            m.vy = 8 + Math.random() * 16;       // sobe lentamente (unid/s)
            m.amp = 10 + Math.random() * 26;      // amplitude do balanço
            m.fase = Math.random() * 6.28;
            m.esc = 0.5 + Math.random() * 1.1;
            return m;
        }

        // ---------- ponteiro (parallax) ----------
        function onMove(e) {
            var pt = e.touches ? e.touches[0] : e;
            alvoPx = (pt.clientX / window.innerWidth) * 2 - 1;
            alvoPy = (pt.clientY / window.innerHeight) * 2 - 1;
        }
        var usaPonteiro = opcoes.parallaxPonteiro !== false && !reduzido;
        if (usaPonteiro) {
            window.addEventListener('pointermove', onMove, { passive: true });
        }

        // ---------- loop ----------
        function frame(now) {
            var t = now - t0;

            // Câmera: deriva ambiente + fração do ponteiro (+ push-in opcional).
            curPx += (alvoPx - curPx) * 0.05;
            curPy += (alvoPy - curPy) * 0.05;
            var ambX = Math.sin(t * 0.00025) * 22;
            var ambY = Math.sin(t * 0.0003 + 1) * 12;

            var zoom = 1;
            if (opcoes.pushIn) {
                var p = Math.min(1, t / opcoes.pushIn);
                zoom = 1.12 - 0.12 * easeOut(p); // de 1.12 -> 1.0
            }
            var w = VB.w * zoom, h = VB.h * zoom;
            var cx = VB.x + (VB.w - w) / 2 + ambX + curPx * 28;
            var cy = VB.y + (VB.h - h) / 2 + ambY + curPy * 16;
            svg.setAttribute('viewBox', cx.toFixed(1) + ' ' + cy.toFixed(1) + ' ' + w.toFixed(1) + ' ' + h.toFixed(1));

            // Parallax: cada camada desloca conforme seu fator (profundidade).
            for (var k = 0; k < camadas.length; k++) {
                var f = parseFloat(camadas[k].getAttribute('data-parallax')) || 0;
                var tx = -curPx * 46 * f;
                var ty = -curPy * 26 * f;
                camadas[k].setAttribute('transform', 'translate(' + tx.toFixed(2) + ' ' + ty.toFixed(2) + ')');
            }

            // Motas: sobem com balanço; reciclam ao sair pelo topo.
            if (motas.length) {
                var dt = 1 / 60;
                for (var j = 0; j < motas.length; j++) {
                    var m = motas[j];
                    m.y -= m.vy * dt;
                    if (m.y < -20) { resetMota(m, false); }
                    var sx = m.x + Math.sin(t * 0.001 + m.fase) * m.amp;
                    m.el.setAttribute('transform', 'translate(' + sx.toFixed(1) + ' ' + m.y.toFixed(1) + ') scale(' + m.esc.toFixed(2) + ')');
                    m.el.setAttribute('opacity', (0.25 + 0.5 * Math.abs(Math.sin(t * 0.0008 + m.fase))).toFixed(2));
                }
            }

            // Encerra o push-in estático se não há mais nada a animar.
            if (reduzido) { rodando = false; return; }
            rafId = requestAnimationFrame(frame);
        }
        function easeOut(p) { return 1 - Math.pow(1 - p, 3); }

        function ligar() {
            if (rodando) { return; }
            rodando = true; t0 = performance.now();
            rafId = requestAnimationFrame(frame);
        }
        function desligar() {
            rodando = false;
            if (rafId) { cancelAnimationFrame(rafId); rafId = 0; }
        }

        // Render estático único quando há movimento reduzido.
        if (reduzido) { frame(performance.now()); }
        else { ligar(); }

        // Pausa fora da viewport (economia de CPU/bateria).
        var io = null;
        if ('IntersectionObserver' in window && !reduzido) {
            io = new IntersectionObserver(function (ents) {
                ents.forEach(function (en) { if (en.isIntersecting) { ligar(); } else { desligar(); } });
            }, { threshold: 0.01 });
            io.observe(svg);
        }

        return {
            parar: function () {
                desligar();
                if (io) { io.disconnect(); }
                if (usaPonteiro) { window.removeEventListener('pointermove', onMove); }
            }
        };
    }

    window.CENA = { iniciar: iniciar };
})();
