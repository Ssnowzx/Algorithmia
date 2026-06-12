/* ============================================================
   Algorithmia — Componentes de UI temáticos (modal, toast)
   Substitui window.confirm / window.alert nativos por diálogos
   no estilo do jogo. Exposto como window.UI.
   ============================================================ */
(function () {
    'use strict';

    var overlay, painel, elTitulo, elMensagem, elBotoes, elIcone;
    var resolverAtual = null;

    function montar() {
        if (overlay) { return; }
        overlay = document.createElement('div');
        overlay.className = 'modal-overlay';
        overlay.setAttribute('aria-hidden', 'true');
        overlay.innerHTML =
            '<div class="modal-painel" role="dialog" aria-modal="true">' +
            '  <div class="modal-borda-topo"></div>' +
            '  <div class="modal-icone" aria-hidden="true"></div>' +
            '  <h3 class="modal-titulo"></h3>' +
            '  <p class="modal-mensagem"></p>' +
            '  <div class="modal-botoes"></div>' +
            '</div>';
        document.body.appendChild(overlay);

        painel = overlay.querySelector('.modal-painel');
        elTitulo = overlay.querySelector('.modal-titulo');
        elMensagem = overlay.querySelector('.modal-mensagem');
        elBotoes = overlay.querySelector('.modal-botoes');
        elIcone = overlay.querySelector('.modal-icone');

        // Fecha ao clicar fora ou apertar Esc (equivale a cancelar).
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) { decidir(false); }
        });
        document.addEventListener('keydown', function (e) {
            if (overlay.classList.contains('aberto')) {
                if (e.key === 'Escape') { decidir(false); }
                if (e.key === 'Enter') {
                    var ok = elBotoes.querySelector('.modal-ok');
                    if (ok) { ok.click(); }
                }
            }
        });
    }

    function abrir() {
        montar();
        overlay.classList.add('aberto');
        overlay.setAttribute('aria-hidden', 'false');
    }

    function fechar() {
        if (!overlay) { return; }
        overlay.classList.remove('aberto');
        overlay.setAttribute('aria-hidden', 'true');
    }

    function decidir(valor) {
        fechar();
        if (resolverAtual) {
            var r = resolverAtual;
            resolverAtual = null;
            r(valor);
        }
    }

    /**
     * Diálogo de confirmação temático.
     * @returns Promise<boolean>
     */
    function confirmar(mensagem, opcoes) {
        opcoes = opcoes || {};
        montar();
        painel.classList.toggle('perigo', !!opcoes.perigo);
        painel.classList.toggle('arcano', !!opcoes.arcano);
        elIcone.textContent = opcoes.icone || (opcoes.perigo ? '⚠️' : (opcoes.arcano ? '🤖' : '❔'));
        elTitulo.textContent = opcoes.titulo || 'Confirmar';
        elMensagem.textContent = mensagem;
        elBotoes.innerHTML = '';

        var btnCancelar = document.createElement('button');
        btnCancelar.className = 'botao botao-fantasma';
        btnCancelar.textContent = opcoes.cancelarTexto || 'Cancelar';
        btnCancelar.addEventListener('click', function () { decidir(false); });

        var btnOk = document.createElement('button');
        btnOk.className = 'botao modal-ok' + (opcoes.perigo ? ' botao-perigo' : (opcoes.arcano ? ' btn-ia' : ''));
        btnOk.textContent = opcoes.okTexto || 'Confirmar';
        btnOk.addEventListener('click', function () { decidir(true); });

        elBotoes.appendChild(btnCancelar);
        elBotoes.appendChild(btnOk);

        return new Promise(function (resolve) {
            resolverAtual = resolve;
            abrir();
            setTimeout(function () { btnOk.focus(); }, 60);
        });
    }

    /**
     * Aviso temático (apenas botão OK).
     * @returns Promise<void>
     */
    function alerta(mensagem, opcoes) {
        opcoes = opcoes || {};
        montar();
        painel.classList.remove('perigo', 'arcano');
        painel.classList.toggle('perigo', opcoes.tipo === 'erro');
        elIcone.textContent = opcoes.icone || (opcoes.tipo === 'erro' ? '✗' : 'ℹ️');
        elTitulo.textContent = opcoes.titulo || (opcoes.tipo === 'erro' ? 'Atenção' : 'Aviso');
        elMensagem.textContent = mensagem;
        elBotoes.innerHTML = '';

        var btnOk = document.createElement('button');
        btnOk.className = 'botao modal-ok';
        btnOk.textContent = opcoes.okTexto || 'Entendi';
        btnOk.addEventListener('click', function () { decidir(true); });
        elBotoes.appendChild(btnOk);

        return new Promise(function (resolve) {
            resolverAtual = resolve;
            abrir();
            setTimeout(function () { btnOk.focus(); }, 60);
        });
    }

    /**
     * Notificação flutuante breve (toast).
     */
    function toast(mensagem, tipo) {
        var t = document.createElement('div');
        t.className = 'toast toast-' + (tipo || 'info');
        t.textContent = mensagem;
        document.body.appendChild(t);
        requestAnimationFrame(function () { t.classList.add('mostrar'); });
        setTimeout(function () {
            t.classList.remove('mostrar');
            setTimeout(function () { t.remove(); }, 400);
        }, 2600);
    }

    window.UI = { confirmar: confirmar, alerta: alerta, toast: toast };

    // Auto-wire de formulários com data-confirmar (substitui o confirm nativo).
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirmar')) { return; }
        if (form.dataset.confirmado === '1') { return; }
        e.preventDefault();
        confirmar(form.getAttribute('data-confirmar'), { perigo: true, okTexto: 'Sim, confirmar' })
            .then(function (ok) {
                if (ok) { form.dataset.confirmado = '1'; form.submit(); }
            });
    }, true);
})();
