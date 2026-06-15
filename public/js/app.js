/* Interações globais leves do Algorithmia. */
(function () {
    'use strict';

    // Esconde mensagens flash após alguns segundos.
    document.querySelectorAll('.flash').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity .5s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 500);
        }, 4000);
    });

    // A confirmação de formulários com [data-confirmar] é tratada pelo
    // modal temático em ui.js (que substitui o confirm() nativo do navegador).

    // ---------- Controle de som no topo (mute + volume) ----------
    var btnSom = document.getElementById('btnSom');
    var volSom = document.getElementById('volSom');
    if (btnSom && window.SOM) {
        var icones = { on: '🔊', off: '🔇' };
        function pintar() {
            var mudo = SOM.estaMudo();
            btnSom.textContent = mudo ? icones.off : icones.on;
            btnSom.classList.toggle('mudo', mudo);
            btnSom.setAttribute('aria-pressed', String(mudo));
        }
        if (volSom) { volSom.value = SOM.getVolume(); }
        pintar();
        btnSom.addEventListener('click', function () { SOM.alternarMudo(); pintar(); });
        if (volSom) {
            volSom.addEventListener('input', function () {
                SOM.volume(parseFloat(volSom.value));
                if (SOM.estaMudo() && parseFloat(volSom.value) > 0) { SOM.mudo(false); pintar(); }
            });
        }
    }

    // ---------- Revelação das regiões do mapa ao rolar ----------
    var regioes = document.querySelectorAll('.regiao');
    var semMovimento = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (regioes.length && !semMovimento && 'IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entradas) {
            entradas.forEach(function (en) {
                if (en.isIntersecting) { en.target.classList.add('revelada'); io.unobserve(en.target); }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
        regioes.forEach(function (r) { r.classList.add('pre-revelar'); io.observe(r); });
    }

    // ---------- Clique de UI sonoro (botões e links de ação) ----------
    document.addEventListener('click', function (e) {
        if (!window.SOM) { return; }
        var alvo = e.target.closest('.botao, .navegacao a, .opcao');
        // O botão de som não toca clique (evita ruído ao mutar) e .opcao toca
        // seu próprio som de acerto/erro no fluxo de batalha.
        if (alvo && alvo.id !== 'btnSom' && !alvo.classList.contains('opcao')) { SOM.clique(); }
    }, true);
})();
