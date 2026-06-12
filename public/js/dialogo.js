/* Efeito máquina de escrever para as cenas de diálogo. */
(function () {
    'use strict';
    var dados = window.DIALOGO || { linhas: [], semFalas: true };
    var linhas = dados.linhas || [];
    var idx = 0, digitando = false, textoCompleto = '';

    var elTexto = document.getElementById('textoFala');
    var elFalante = document.getElementById('falante');
    var elDica = document.getElementById('dica');
    var elAcao = document.getElementById('acaoFinal');
    var atores = document.querySelectorAll('.ator-svg');

    function mostrarAtor(slug) {
        atores.forEach(function (a) {
            a.style.display = (a.getAttribute('data-slug') === slug) ? 'block' : 'none';
        });
    }

    function digitar(texto) {
        digitando = true;
        textoCompleto = texto;
        elTexto.textContent = '';
        var i = 0;
        var timer = setInterval(function () {
            elTexto.textContent += texto.charAt(i);
            i++;
            if (i >= texto.length) {
                clearInterval(timer);
                digitando = false;
            }
        }, 18);
        elTexto._timer = timer;
    }

    function mostrarLinha(n) {
        var linha = linhas[n];
        if (!linha) { return; }
        elFalante.textContent = linha.falante;
        if (linha.slug) { mostrarAtor(linha.slug); }
        digitar(linha.texto);
    }

    function avancar() {
        if (digitando) {
            // Completa a linha atual imediatamente.
            clearInterval(elTexto._timer);
            elTexto.textContent = textoCompleto;
            digitando = false;
            return;
        }
        idx++;
        if (idx < linhas.length) {
            mostrarLinha(idx);
        } else {
            // Fim do diálogo: revela o botão de ação.
            elDica.style.display = 'none';
            elAcao.style.display = 'block';
        }
    }

    if (dados.semFalas || linhas.length === 0) {
        elDica.style.display = 'none';
        elAcao.style.display = 'block';
        if (elTexto) { elTexto.textContent = ''; }
    } else {
        mostrarLinha(0);
        document.querySelector('.cena-dialogo').addEventListener('click', avancar);
    }
})();
