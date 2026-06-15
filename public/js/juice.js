/* ============================================================
   Algorithmia — Juice de batalha (partículas + screen-shake)
   Canvas único sobre a arena, object pooling e screen-shake
   trauma-based. Loop rAF que PARA de reagendar quando não há
   nada animando (poupa CPU/bateria). Exposto como window.JUICE.

   Anima só transform/opacity. Respeita prefers-reduced-motion
   tanto no spawn de partículas quanto no shake.
   ============================================================ */
(function () {
    'use strict';

    var arena = document.querySelector('.campo-batalha');
    if (!arena) { window.JUICE = stub(); return; } // fora da arena: no-op.

    // ---------- preferência de movimento ----------
    var mq = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : { matches: false, addEventListener: null };
    var movimento = !mq.matches;
    if (mq.addEventListener) {
        mq.addEventListener('change', function (e) { movimento = !e.matches; });
    }

    // ---------- canvas ----------
    var canvas = document.createElement('canvas');
    canvas.className = 'juice-canvas';
    arena.appendChild(canvas);
    var g = canvas.getContext('2d');
    var dpr = Math.max(1, window.devicePixelRatio || 1);

    function redimensionar() {
        var r = arena.getBoundingClientRect();
        canvas.width = Math.round(r.width * dpr);
        canvas.height = Math.round(r.height * dpr);
        canvas.style.width = r.width + 'px';
        canvas.style.height = r.height + 'px';
        g.setTransform(dpr, 0, 0, dpr, 0, 0);
    }
    redimensionar();
    window.addEventListener('resize', redimensionar);

    // ---------- pool de partículas ----------
    var POOL = 220;
    var TETO = 180;
    var ps = new Array(POOL);
    for (var i = 0; i < POOL; i++) {
        ps[i] = { ativo: false, x: 0, y: 0, vx: 0, vy: 0, vida: 0, maxv: 1, tam: 3, cor: '#fff', grav: 1 };
    }
    var ativas = 0;
    var GRAV = 480; // px/s²

    function pegar() {
        if (ativas >= TETO) { return null; }
        for (var k = 0; k < POOL; k++) { if (!ps[k].ativo) { ps[k].ativo = true; ativas++; return ps[k]; } }
        return null;
    }

    // ---------- shake (trauma-based) ----------
    var trauma = 0;
    var MAX_OFF = 9;     // px
    var MAX_ANG = 2.2;   // graus
    var DECAY = 1.3;     // por segundo

    function addTrauma(v) {
        if (!movimento) { return; }
        trauma = Math.min(1, trauma + v);
        ligar();
    }

    // ---------- loop ----------
    var rodando = false;
    var ultimo = 0;

    function ligar() {
        if (rodando) { return; }
        rodando = true;
        ultimo = performance.now();
        requestAnimationFrame(loop);
    }

    function loop(agora) {
        var dt = Math.min(0.05, (agora - ultimo) / 1000);
        ultimo = agora;

        // física das partículas
        g.clearRect(0, 0, canvas.width, canvas.height);
        for (var k = 0; k < POOL; k++) {
            var p = ps[k];
            if (!p.ativo) { continue; }
            p.vida -= dt;
            if (p.vida <= 0) { p.ativo = false; ativas--; continue; }
            p.vx *= 0.98;
            p.vy += GRAV * p.grav * dt;
            p.x += p.vx * dt;
            p.y += p.vy * dt;
            g.globalAlpha = Math.max(0, Math.min(1, p.vida / p.maxv));
            g.fillStyle = p.cor;
            g.fillRect(p.x, p.y, p.tam, p.tam);
        }
        g.globalAlpha = 1;

        // shake
        if (trauma > 0) {
            trauma = Math.max(0, trauma - DECAY * dt);
            var amp = trauma * trauma;
            var x = MAX_OFF * amp * (Math.random() * 2 - 1);
            var y = MAX_OFF * amp * (Math.random() * 2 - 1);
            var a = MAX_ANG * amp * (Math.random() * 2 - 1);
            arena.style.transform = 'translate3d(' + x.toFixed(2) + 'px,' + y.toFixed(2) + 'px,0) rotate(' + a.toFixed(2) + 'deg)';
        } else if (arena.style.transform) {
            arena.style.transform = '';
        }

        // para de reagendar quando não há nada animando
        if (ativas === 0 && trauma === 0) { rodando = false; return; }
        requestAnimationFrame(loop);
    }

    // ---------- helpers de cor (tokens de :root) ----------
    function token(nome) {
        return getComputedStyle(document.documentElement).getPropertyValue(nome).trim() || '#fff';
    }
    var COR = {
        dano: token('--hp'), inimigo: token('--xp'), cura: token('--sucesso'),
        ouro: token('--ouro'), arcano: token('--primaria-2'), bug: token('--erro')
    };

    /** Coordenadas centrais de um sprite (#spriteHeroi/#spriteInimigo) no espaço do canvas. */
    function centroDe(id) {
        var el = document.getElementById(id);
        var ra = arena.getBoundingClientRect();
        if (!el) { return { x: ra.width / 2, y: ra.height / 2 }; }
        var r = el.getBoundingClientRect();
        return { x: r.left - ra.left + r.width / 2, y: r.top - ra.top + r.height / 2 };
    }

    function emitir(x, y, n, cor, opt) {
        if (!movimento) { return; }
        opt = opt || {};
        for (var k = 0; k < n; k++) {
            var p = pegar();
            if (!p) { break; }
            var ang = opt.ang != null ? opt.ang + (Math.random() - 0.5) * (opt.espalho || 6.28) : Math.random() * 6.28;
            var vel = (opt.velMin || 60) + Math.random() * ((opt.velMax || 220) - (opt.velMin || 60));
            p.x = x; p.y = y;
            p.vx = Math.cos(ang) * vel;
            p.vy = Math.sin(ang) * vel - (opt.impulso || 0);
            p.maxv = p.vida = (opt.vidaMin || 0.4) + Math.random() * ((opt.vidaMax || 0.8) - (opt.vidaMin || 0.4));
            p.tam = (opt.tamMin || 2) + Math.floor(Math.random() * ((opt.tamMax || 4) - (opt.tamMin || 2) + 1));
            p.cor = cor;
            p.grav = opt.grav == null ? 1 : opt.grav;
        }
        ligar();
    }

    var JUICE = {
        /** Faíscas no ponto de impacto de um sprite. */
        faiscas: function (id, tipo) {
            var c = centroDe(id);
            emitir(c.x, c.y, 12, tipo === 'heroi' ? COR.dano : COR.inimigo,
                   { velMin: 80, velMax: 240, vidaMin: 0.3, vidaMax: 0.6, tamMin: 2, tamMax: 4, grav: 0.6 });
        },
        /** Explosão do inimigo derrotado. */
        explosao: function (id) {
            var c = centroDe(id || 'spriteInimigo');
            emitir(c.x, c.y, 32, COR.bug, { velMin: 60, velMax: 280, vidaMin: 0.5, vidaMax: 1.0, tamMin: 2, tamMax: 5, grav: 0.8 });
            emitir(c.x, c.y, 10, COR.arcano, { velMin: 40, velMax: 160, vidaMin: 0.4, vidaMax: 0.9, tamMin: 2, tamMax: 4, grav: 0.5 });
            addTrauma(0.6);
        },
        /** Partículas de ouro subindo (recompensa). */
        ouro: function () {
            var ra = arena.getBoundingClientRect();
            emitir(ra.width / 2, ra.height * 0.7, 12, COR.ouro,
                   { ang: -1.57, espalho: 1.2, velMin: 120, velMax: 260, impulso: 80, vidaMin: 0.6, vidaMax: 1.1, tamMin: 2, tamMax: 4, grav: 0.4 });
        },
        /** Brilho/faíscas arcanas do Especial em volta do herói. */
        especial: function () {
            var c = centroDe('spriteHeroi');
            emitir(c.x, c.y, 20, COR.arcano, { velMin: 30, velMax: 180, vidaMin: 0.5, vidaMax: 1.0, tamMin: 2, tamMax: 5, grav: 0.2 });
        },
        cura: function () {
            var c = centroDe('spriteHeroi');
            emitir(c.x, c.y, 14, COR.cura, { ang: -1.57, espalho: 1.6, velMin: 40, velMax: 140, vidaMin: 0.5, vidaMax: 1.0, tamMin: 2, tamMax: 4, grav: 0.1 });
        },
        shake: addTrauma,
        get movimento() { return movimento; }
    };

    function stub() {
        var nop = function () {};
        return { faiscas: nop, explosao: nop, ouro: nop, especial: nop, cura: nop, shake: nop, movimento: false };
    }

    window.JUICE = JUICE;
})();
