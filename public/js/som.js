/* ============================================================
   Algorithmia — Design de som procedural (Web Audio API)
   Sem bibliotecas e sem arquivos de áudio: todo som nasce de
   osciladores + envelope + filtro. Exposto como window.SOM.

   Roteamento por som:
     osc(s) -> [BiquadFilter opcional] -> GainNode(envelope) -> masterGain -> destination

   Receitas por evento (waveform | freq f0→f1 | dur | filtro):
     acerto      square   880→1320   120ms
     erro        sawtooth 220→110     260ms  lowpass 800
     danoHeroi   square+triangle 140→70 / 80 300ms lowpass 400
     danoInimigo square   300→160     110ms  highpass 200
     combo(n)    square   660*2^(n/12) 90ms (teto +12 semitons)
     especial    sawtooth+sine 440→1760 / 880 600ms lowpass varrendo
     pocao       triangle 660→1320    500ms  highpass 400
     ouro        square   988 + 1319  80+250ms (duas notas)
     nivel       square   523/659/784/1047 arpejo
     vitoria     square+triangle arpejo asc. G5..G6
     derrota     sawtooth 330→82      900ms  lowpass varrendo
     fragmento   sawtooth+square 220 + 311 (trítono) 700ms lowpass 1500
     clique      square   1200        40ms
     modalAbrir  sine     660→990     2x60ms
     modalFechar sine     990→660     2x60ms
     tic         square   ~1800       14ms (polifonia limitada)
   ============================================================ */
(function () {
    'use strict';

    var AC = window.AudioContext || window.webkitAudioContext;
    if (!AC) { window.SOM = stub(); return; } // navegador sem Web Audio: vira no-op.

    var ctx = null;
    var master = null;
    var destravado = false;
    var ticsAtivos = 0;          // polifonia do typewriter
    var TIC_MAX = 6;

    // Pad ambiente (trilha em loop) — nós próprios e ganho independente.
    var ambGain = null;
    var ambNodes = null;
    var ambVol = lerNum('som_amb_vol', 0.18);
    var mqReduz = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : { matches: false };

    // Estado persistido entre páginas.
    var mudo = lerBool('som_mudo', false);
    var volume = lerNum('som_vol', 0.6);

    function lerBool(k, d) { try { var v = localStorage.getItem(k); return v === null ? d : v === '1'; } catch (e) { return d; } }
    function lerNum(k, d) { try { var v = parseFloat(localStorage.getItem(k)); return isNaN(v) ? d : v; } catch (e) { return d; } }
    function salvar(k, v) { try { localStorage.setItem(k, v); } catch (e) { /* modo privado */ } }

    function garantirCtx() {
        if (ctx) { return; }
        ctx = new AC();
        master = ctx.createGain();
        master.gain.value = mudo ? 0 : volume;
        master.connect(ctx.destination);
        ambGain = ctx.createGain();    // volume independente do ambiente
        ambGain.gain.value = 0;
        ambGain.connect(master);
    }

    /** Destrava o AudioContext no 1º gesto do usuário (política de autoplay). */
    function unlock() {
        if (destravado) { return; }
        garantirCtx();
        var ev = ['click', 'keydown', 'touchstart', 'touchend'];
        function tentar() {
            ctx.resume().then(function () {
                if (ctx.state === 'running' && !destravado) {
                    destravado = true;
                    ev.forEach(function (e) { document.removeEventListener(e, tentar); });
                }
            }).catch(function () { /* tenta de novo no próximo gesto */ });
        }
        ev.forEach(function (e) { document.addEventListener(e, tentar, { passive: true }); });
    }

    function ativo() {
        if (!ctx || mudo) { return false; }
        // iOS pode re-suspender ao voltar de background.
        if (ctx.state === 'suspended') { ctx.resume(); return false; }
        return ctx.state === 'running';
    }

    // ---------- núcleo de síntese ----------
    /**
     * Toca uma voz: oscilador com glide de frequência e envelope anti-click.
     * @param {object} o - { tipo, f0, f1, dur, pico, atk, atraso, filtro }
     */
    function voz(o) {
        if (!ativo()) { return; }
        var t = ctx.currentTime + (o.atraso || 0);
        var dur = o.dur / 1000;
        var pico = o.pico == null ? 0.3 : o.pico;

        var osc = ctx.createOscillator();
        osc.type = o.tipo || 'square';
        osc.frequency.setValueAtTime(o.f0, t);
        if (o.f1 && o.f1 !== o.f0) {
            osc.frequency.exponentialRampToValueAtTime(Math.max(1, o.f1), t + dur);
        }

        var g = ctx.createGain();
        g.gain.setValueAtTime(0.0001, t);
        g.gain.exponentialRampToValueAtTime(pico, t + (o.atk || 0.005));
        g.gain.exponentialRampToValueAtTime(0.0001, t + dur);

        var no = osc;
        if (o.filtro) {
            var f = ctx.createBiquadFilter();
            f.type = o.filtro.tipo || 'lowpass';
            f.frequency.setValueAtTime(o.filtro.f0, t);
            if (o.filtro.f1) { f.frequency.linearRampToValueAtTime(o.filtro.f1, t + dur); }
            osc.connect(f); f.connect(g);
        } else {
            osc.connect(g);
        }
        g.connect(master);

        osc.start(t);
        osc.stop(t + dur + 0.03);
    }

    /** Toca várias vozes de uma vez (acordes / camadas). */
    function vozes(lista) { lista.forEach(voz); }

    function nota(midi) { return 440 * Math.pow(2, (midi - 69) / 12); }

    // ---------- mapa de eventos ----------
    var SOM = {
        unlock: unlock,

        acerto: function () {
            voz({ tipo: 'square', f0: 880, f1: 1320, dur: 120, pico: 0.28, atk: 0.005 });
        },
        erro: function () {
            voz({ tipo: 'sawtooth', f0: 220, f1: 110, dur: 260, pico: 0.26, atk: 0.005,
                  filtro: { tipo: 'lowpass', f0: 800 } });
        },
        danoHeroi: function () {
            voz({ tipo: 'square', f0: 140, f1: 70, dur: 300, pico: 0.3, atk: 0.002,
                  filtro: { tipo: 'lowpass', f0: 400 } });
            voz({ tipo: 'triangle', f0: 80, f1: 80, dur: 300, pico: 0.22, atk: 0.002 });
        },
        danoInimigo: function () {
            voz({ tipo: 'square', f0: 300, f1: 160, dur: 110, pico: 0.26, atk: 0.001,
                  filtro: { tipo: 'highpass', f0: 200 } });
        },
        combo: function (n) {
            var semis = Math.min(12, Math.max(0, (n || 1) - 1) * 2);
            var f = 660 * Math.pow(2, semis / 12);
            voz({ tipo: 'square', f0: f, f1: f * 1.05, dur: 90, pico: 0.24, atk: 0.003 });
        },
        especial: function () {
            voz({ tipo: 'sawtooth', f0: 440, f1: 1760, dur: 600, pico: 0.22, atk: 0.03,
                  filtro: { tipo: 'lowpass', f0: 600, f1: 4000 } });
            voz({ tipo: 'sine', f0: 880, f1: 880, dur: 600, pico: 0.16, atk: 0.03 });
        },
        pocao: function () {
            voz({ tipo: 'triangle', f0: 660, f1: 1320, dur: 500, pico: 0.24, atk: 0.04,
                  filtro: { tipo: 'highpass', f0: 400 } });
        },
        ouro: function () {
            voz({ tipo: 'square', f0: 988, f1: 988, dur: 80, pico: 0.22, atk: 0.002 });
            voz({ tipo: 'square', f0: 1319, f1: 1319, dur: 250, pico: 0.22, atk: 0.002, atraso: 0.07 });
        },
        nivel: function () {
            [60, 64, 67, 72].forEach(function (m, i) {
                voz({ tipo: 'square', f0: nota(m), f1: nota(m), dur: 130, pico: 0.22, atk: 0.003, atraso: i * 0.11 });
            });
        },
        vitoria: function () {
            [67, 72, 76, 79].forEach(function (m, i) {
                voz({ tipo: 'square', f0: nota(m), f1: nota(m), dur: i === 3 ? 320 : 130, pico: 0.24, atk: 0.004, atraso: i * 0.12 });
            });
            voz({ tipo: 'triangle', f0: nota(48), f1: nota(48), dur: 700, pico: 0.18, atk: 0.01 });
        },
        derrota: function () {
            voz({ tipo: 'sawtooth', f0: 330, f1: 82, dur: 900, pico: 0.24, atk: 0.01,
                  filtro: { tipo: 'lowpass', f0: 1200, f1: 300 } });
        },
        fragmento: function () {
            // Trítono dissonante (220 + 311) — desconforto proposital.
            voz({ tipo: 'sawtooth', f0: 220, f1: 208, dur: 700, pico: 0.2, atk: 0.05,
                  filtro: { tipo: 'lowpass', f0: 1500 } });
            voz({ tipo: 'square', f0: 311, f1: 296, dur: 700, pico: 0.16, atk: 0.05 });
        },
        clique: function () {
            voz({ tipo: 'square', f0: 1200, f1: 1200, dur: 40, pico: 0.13, atk: 0.001 });
        },
        modalAbrir: function () {
            voz({ tipo: 'sine', f0: 660, f1: 660, dur: 60, pico: 0.16, atk: 0.002 });
            voz({ tipo: 'sine', f0: 990, f1: 990, dur: 70, pico: 0.16, atk: 0.002, atraso: 0.06 });
        },
        modalFechar: function () {
            voz({ tipo: 'sine', f0: 990, f1: 990, dur: 60, pico: 0.16, atk: 0.002 });
            voz({ tipo: 'sine', f0: 660, f1: 660, dur: 70, pico: 0.16, atk: 0.002, atraso: 0.06 });
        },
        tic: function () {
            if (ticsAtivos >= TIC_MAX) { return; } // limita polifonia do typewriter
            ticsAtivos++;
            var f = 1700 + Math.random() * 400;
            voz({ tipo: 'square', f0: f, f1: f, dur: 14, pico: 0.06, atk: 0.001 });
            setTimeout(function () { ticsAtivos--; }, 30);
        },
        pressStart: function () {
            voz({ tipo: 'square', f0: 523, f1: 1047, dur: 180, pico: 0.24, atk: 0.004 });
            voz({ tipo: 'square', f0: 784, f1: 1568, dur: 260, pico: 0.2, atk: 0.006, atraso: 0.08 });
        },

        // ---------- controle ----------
        mudo: function (v) {
            mudo = !!v;
            salvar('som_mudo', mudo ? '1' : '0');
            if (master) { master.gain.value = mudo ? 0 : volume; }
            return mudo;
        },
        alternarMudo: function () { return SOM.mudo(!mudo); },
        estaMudo: function () { return mudo; },
        volume: function (v) {
            volume = Math.max(0, Math.min(1, v));
            salvar('som_vol', volume);
            if (master && !mudo) { master.gain.value = volume; }
            return volume;
        },
        getVolume: function () { return volume; },

        /**
         * Tema de abertura ÉPICO e SOMBRIO (Ré menor): drone grave + naipe de
         * "cordas" (sawtooth filtrado) com swell cinematográfico, arpejo lento
         * melancólico e batida grave (tímpano) distante. Sem arquivos; ganho
         * próprio (respeita o MUTE pelo master).
         */
        ambienteIniciar: function () {
            garantirCtx();
            if (!ctx || ambNodes) { return; }            // já tocando
            var t = ctx.currentTime;

            // Barramento do pad com swell de volume (entrada cinematográfica).
            var filtro = ctx.createBiquadFilter();
            filtro.type = 'lowpass';
            filtro.frequency.value = 1100;               // escuro, "cordas" abafadas
            filtro.Q.value = 0.6;
            var swell = ctx.createGain();
            swell.gain.setValueAtTime(0.0001, t);
            swell.gain.exponentialRampToValueAtTime(0.85, t + 4);   // sobe em 4s
            filtro.connect(swell); swell.connect(ambGain);

            // Swell lento contínuo (respiração épica) — desligado sob reduced-motion.
            var lfo = null;
            if (!mqReduz.matches) {
                lfo = ctx.createOscillator(); lfo.type = 'sine'; lfo.frequency.value = 0.05;
                var ldepth = ctx.createGain(); ldepth.gain.value = 0.18;
                lfo.connect(ldepth); ldepth.connect(swell.gain); lfo.start(t + 4);
            }

            var osc = []; // tudo que precisa stop()

            // Drones graves (peso): D2 + A2 (tônica + quinta), sine puro.
            [73.42, 110].forEach(function (f, i) {
                var o = ctx.createOscillator(); o.type = 'sine'; o.frequency.value = f;
                var g = ctx.createGain(); g.gain.value = i === 0 ? 0.55 : 0.3;
                o.connect(g); g.connect(filtro); o.start(t); osc.push(o);
            });

            // Naipe de cordas: acorde Ré menor (D3 F3 A3 D4) em sawtooth, com leve
            // desafinação (espessura) e ataque lento.
            var acorde = [146.83, 174.61, 220, 293.66];
            acorde.forEach(function (f, i) {
                [-6, 6].forEach(function (det) {          // par detunado = "supersaw" leve
                    var o = ctx.createOscillator(); o.type = 'sawtooth';
                    o.frequency.value = f; o.detune.value = det;
                    var g = ctx.createGain();
                    g.gain.setValueAtTime(0.0001, t);
                    g.gain.exponentialRampToValueAtTime(i === 3 ? 0.05 : 0.08, t + 3.5);
                    o.connect(g); g.connect(filtro); o.start(t); osc.push(o);
                });
            });

            // Arpejo lento e melancólico (Ré menor, registro médio, notas-sino).
            var frase = [220, 293.66, 349.23, 329.63, 293.66, 261.63, 246.94, 220]; // A3 D4 F4 E4 D4 C4 B2.. Bb
            var passo = 0;
            function ambNota(freq, pico, dur) {
                if (!ctx || mudo) { return; }
                var tn = ctx.currentTime;
                var o = ctx.createOscillator(); o.type = 'triangle'; o.frequency.value = freq;
                var o2 = ctx.createOscillator(); o2.type = 'sine'; o2.frequency.value = freq * 2;
                var g = ctx.createGain();
                g.gain.setValueAtTime(0.0001, tn);
                g.gain.exponentialRampToValueAtTime(pico, tn + 0.05);
                g.gain.exponentialRampToValueAtTime(0.0001, tn + dur);
                var g2 = ctx.createGain(); g2.gain.value = 0.25; o2.connect(g2); g2.connect(g);
                o.connect(g); g.connect(ambGain);
                o.start(tn); o2.start(tn); o.stop(tn + dur + 0.1); o2.stop(tn + dur + 0.1);
            }
            var arp = setInterval(function () { ambNota(frase[passo % frase.length], 0.07, 2.6); passo++; }, 2100);
            setTimeout(function () { ambNota(frase[0], 0.07, 2.6); }, 900);

            // Batida grave distante (tímpano): peso épico a cada ~3,8s.
            function tambor() {
                if (!ctx || mudo) { return; }
                var tn = ctx.currentTime;
                var o = ctx.createOscillator(); o.type = 'sine';
                o.frequency.setValueAtTime(90, tn);
                o.frequency.exponentialRampToValueAtTime(42, tn + 0.22);
                var g = ctx.createGain();
                g.gain.setValueAtTime(0.0001, tn);
                g.gain.exponentialRampToValueAtTime(0.5, tn + 0.012);
                g.gain.exponentialRampToValueAtTime(0.0001, tn + 0.5);
                o.connect(g); g.connect(ambGain); o.start(tn); o.stop(tn + 0.55);
            }
            var pulso = setInterval(tambor, 3800);
            setTimeout(tambor, 1600);

            ambNodes = { osc: osc, filtro: filtro, swell: swell, lfo: lfo, arp: arp, pulso: pulso };
            ambGain.gain.cancelScheduledValues(t);
            ambGain.gain.setValueAtTime(Math.max(0.0001, ambGain.gain.value), t);
            ambGain.gain.exponentialRampToValueAtTime(Math.max(0.0001, ambVol), t + 3.5);
        },
        ambienteParar: function () {
            if (!ctx || !ambNodes) { return; }
            var t = ctx.currentTime;
            var n = ambNodes; ambNodes = null;
            if (n.arp) { clearInterval(n.arp); }
            if (n.pulso) { clearInterval(n.pulso); }
            ambGain.gain.cancelScheduledValues(t);
            ambGain.gain.setValueAtTime(Math.max(0.0001, ambGain.gain.value), t);
            ambGain.gain.exponentialRampToValueAtTime(0.0001, t + 1.4);
            setTimeout(function () {
                try {
                    n.osc.forEach(function (o) { o.stop(); });
                    if (n.lfo) { n.lfo.stop(); }
                } catch (e) { /* já parado */ }
            }, 1600);
        },
        ambienteVolume: function (v) {
            ambVol = Math.max(0, Math.min(1, v));
            salvar('som_amb_vol', ambVol);
            if (ambGain && ambNodes) { ambGain.gain.value = ambVol; }
            return ambVol;
        }
    };

    function stub() {
        var nop = function () {};
        return {
            unlock: nop, acerto: nop, erro: nop, danoHeroi: nop, danoInimigo: nop,
            combo: nop, especial: nop, pocao: nop, ouro: nop, nivel: nop, vitoria: nop,
            derrota: nop, fragmento: nop, clique: nop, modalAbrir: nop, modalFechar: nop,
            tic: nop, pressStart: nop, mudo: function (v) { return !!v; }, alternarMudo: function () { return false; },
            estaMudo: function () { return false; }, volume: nop, getVolume: function () { return 0; },
            ambienteIniciar: nop, ambienteParar: nop, ambienteVolume: nop
        };
    }

    window.SOM = SOM;

    // Destrava assim que o script carrega (efetiva-se no 1º gesto).
    unlock();
})();
