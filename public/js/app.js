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
})();
