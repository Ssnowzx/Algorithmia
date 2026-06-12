/* ============================================================
   Algorithmia — Lógica da arena de batalha (turnos + desafios)
   ============================================================ */
(function () {
    'use strict';
    var B = window.BATALHA;
    if (!B) { return; }

    var estado = B.estado;
    var urls = B.urls;
    var respondendo = false;

    var elPainel = document.getElementById('painelDesafio');
    var elCombo = document.getElementById('comboInd');

    var LETRAS = ['A', 'B', 'C', 'D', 'E', 'F'];

    // ---------- utilidades ----------
    function req(url, corpo) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(corpo || {})
        }).then(function (r) { return r.json(); });
    }

    function pct(atual, max) { return Math.max(0, Math.min(100, Math.round(atual / Math.max(1, max) * 100))); }

    function atualizarBarras(s) {
        if (!s) { return; }
        document.getElementById('hpInimigoFill').style.width = pct(s.inimigo_hp, s.inimigo_hp_max) + '%';
        document.getElementById('hpInimigoLabel').textContent = s.inimigo_hp + ' / ' + s.inimigo_hp_max;
        document.getElementById('hpHeroiFill').style.width = pct(s.heroi_hp, s.heroi_hp_max) + '%';
        document.getElementById('hpHeroiLabel').textContent = s.heroi_hp + ' / ' + s.heroi_hp_max;
        document.getElementById('mpHeroiFill').style.width = pct(s.heroi_mp, s.heroi_mp_max) + '%';
        document.getElementById('mpHeroiLabel').textContent = s.heroi_mp + ' / ' + s.heroi_mp_max;
        elCombo.textContent = s.combo > 1 ? ('COMBO x' + s.combo + '!') : '';
        if (s.especial_armado) { elCombo.textContent += ' ✦ESPECIAL'; }
    }

    function tremer(qualSprite) {
        var el = document.getElementById(qualSprite);
        el.classList.add('tremer');
        setTimeout(function () { el.classList.remove('tremer'); }, 400);
    }

    function atacar(qualSprite) {
        var el = document.getElementById(qualSprite);
        if (!el) return;
        el.classList.add('atacando');
        setTimeout(function () { el.classList.remove('atacando'); }, 400);
    }

    function flutuar(lado, texto, classe) {
        var alvo = document.getElementById(lado);
        var span = document.createElement('span');
        span.className = 'flutuante ' + classe;
        span.textContent = texto;
        span.style.left = (40 + Math.random() * 20) + '%';
        span.style.top = '30%';
        alvo.appendChild(span);
        setTimeout(function () { span.remove(); }, 1000);
    }

    // ---------- renderização dos desafios ----------
    function renderDesafio(d) {
        if (!d) { return; }
        var html = '';
        html += '<div class="desafio-meta">';
        html += '<span class="badge-assunto">' + escapeHtml(d.assunto) + '</span>';
        html += '<span class="badge-tipo">' + tipoLabel(d.tipo) + '</span>';
        html += '<span class="dificuldade-pontos">' + repetir('◆', d.dificuldade) + '</span>';
        html += '</div>';
        html += '<div class="pergunta-texto">' + escapeHtml(d.pergunta) + '</div>';
        if (d.codigo) { html += '<pre class="codigo-bloco">' + escapeHtml(d.codigo) + '</pre>'; }
        html += '<div id="zonaResposta"></div>';
        html += '<div class="feedback" id="feedback"></div>';
        elPainel.innerHTML = html;

        var zona = document.getElementById('zonaResposta');
        switch (d.tipo) {
            case 'vf': renderVF(zona); break;
            case 'completar': renderCompletar(zona); break;
            case 'ordenar':
            case 'arrastar': renderOrdenar(zona, d.opcoes); break;
            default: renderMultipla(zona, d.opcoes); // multipla, erro
        }
    }

    function renderMultipla(zona, opcoes) {
        var lista = document.createElement('div');
        lista.className = 'opcoes-lista';
        (opcoes || []).forEach(function (op, i) {
            var b = document.createElement('button');
            b.className = 'opcao';
            b.innerHTML = '<span class="letra">' + LETRAS[i] + '</span><span>' + escapeHtml(op) + '</span>';
            b.addEventListener('click', function () { enviar(i, b); });
            lista.appendChild(b);
        });
        zona.appendChild(lista);
    }

    function renderVF(zona) {
        var lista = document.createElement('div');
        lista.className = 'opcoes-lista';
        [['Verdadeiro', true], ['Falso', false]].forEach(function (par) {
            var b = document.createElement('button');
            b.className = 'opcao';
            b.innerHTML = '<span class="letra">' + (par[1] ? 'V' : 'F') + '</span><span>' + par[0] + '</span>';
            b.addEventListener('click', function () { enviar(par[1], b); });
            lista.appendChild(b);
        });
        zona.appendChild(lista);
    }

    function renderCompletar(zona) {
        var wrap = document.createElement('div');
        wrap.className = 'completar-campo';
        var input = document.createElement('input');
        input.type = 'text';
        input.placeholder = 'Digite a resposta...';
        input.id = 'inputCompletar';
        input.autocomplete = 'off';
        var btn = document.createElement('button');
        btn.className = 'botao';
        btn.textContent = 'Responder';
        btn.addEventListener('click', function () { enviar(input.value, btn); });
        input.addEventListener('keydown', function (e) { if (e.key === 'Enter') { btn.click(); } });
        wrap.appendChild(input); wrap.appendChild(btn);
        zona.appendChild(wrap);
        setTimeout(function () { input.focus(); }, 50);
    }

    function renderOrdenar(zona, opcoes) {
        var dica = document.createElement('p');
        dica.className = 'dialogo-dica';
        dica.textContent = 'Use ▲ ▼ para ordenar e depois confirme.';
        zona.appendChild(dica);

        var lista = document.createElement('div');
        lista.className = 'ordenar-lista';
        lista.id = 'listaOrdenar';
        // Cada item carrega seu índice ORIGINAL em data-idx.
        (opcoes || []).forEach(function (op, i) {
            var item = document.createElement('div');
            item.className = 'token';
            item.setAttribute('data-idx', i);
            item.innerHTML = '<span class="pos"></span><span style="flex:1">' + escapeHtml(op) + '</span>'
                + '<button class="botao botao-sm botao-fantasma" data-dir="-1">▲</button>'
                + '<button class="botao botao-sm botao-fantasma" data-dir="1">▼</button>';
            lista.appendChild(item);
        });
        zona.appendChild(lista);

        lista.addEventListener('click', function (e) {
            var btn = e.target.closest('button[data-dir]');
            if (!btn) { return; }
            var item = btn.closest('.token');
            var dir = parseInt(btn.getAttribute('data-dir'), 10);
            if (dir === -1 && item.previousElementSibling) {
                lista.insertBefore(item, item.previousElementSibling);
            } else if (dir === 1 && item.nextElementSibling) {
                lista.insertBefore(item.nextElementSibling, item);
            }
            numerar();
        });
        numerar();

        var btn = document.createElement('button');
        btn.className = 'botao';
        btn.textContent = 'Confirmar ordem';
        btn.addEventListener('click', function () {
            var ordem = [].map.call(lista.children, function (c) { return parseInt(c.getAttribute('data-idx'), 10); });
            enviar(ordem, btn);
        });
        zona.appendChild(btn);
    }

    function numerar() {
        var itens = document.querySelectorAll('#listaOrdenar .token .pos');
        itens.forEach(function (p, i) { p.textContent = (i + 1); });
    }

    // ---------- envio de resposta ----------
    function enviar(resposta, elemento) {
        if (respondendo) { return; }
        respondendo = true;
        bloquearZona();
        req(urls.responder, { resposta: resposta }).then(function (r) { tratarTurno(r, elemento); });
    }

    function tratarTurno(r, elemento) {
        respondendo = false;
        if (r.erro) { window.UI.alerta(r.erro, { tipo: 'erro' }); return; }

        // Animações de ataque + dano: o atacante investe e o alvo reage logo depois.
        if (r.dano_inimigo) {
            atacar('spriteHeroi');
            setTimeout(function () { tremer('spriteInimigo'); flutuar('ladoInimigo', '-' + r.dano_inimigo, 'dano'); }, 170);
        }
        if (r.dano_heroi) {
            atacar('spriteInimigo');
            setTimeout(function () { tremer('spriteHeroi'); flutuar('ladoHeroi', '-' + r.dano_heroi, 'dano'); }, 170);
        }
        if (r.combo && r.combo > 1) { flutuar('ladoInimigo', 'x' + r.combo, 'combo'); }

        atualizarBarras(r.estado);
        marcarOpcao(elemento, r.correto);
        mostrarFeedback(r);

        // Botão para prosseguir.
        var fb = document.getElementById('feedback');
        var cont = document.createElement('button');
        cont.className = 'botao';
        cont.style.marginTop = '.8rem';
        cont.textContent = r.resultado ? 'Ver desfecho' : 'Próximo desafio →';
        cont.addEventListener('click', function () {
            if (r.resultado) { mostrarResultado(r); }
            else { renderDesafio(r.proximo || (r.estado && r.estado.desafio)); }
        });
        fb.appendChild(cont);
        atualizarBotoesItens();
    }

    function bloquearZona() {
        document.querySelectorAll('#zonaResposta button, #zonaResposta input').forEach(function (b) { b.disabled = true; });
    }

    function marcarOpcao(elemento, correto) {
        if (elemento && elemento.classList.contains('opcao')) {
            elemento.classList.add(correto ? 'correta' : 'errada');
        }
    }

    function mostrarFeedback(r) {
        var fb = document.getElementById('feedback');
        fb.className = 'feedback mostrar ' + (r.correto ? 'ok' : 'nao');
        var titulo = r.via_ia ? '🤖 A IA respondeu por você...' : (r.correto ? '✓ Correto!' : '✗ Incorreto');
        fb.innerHTML = '<div class="titulo-fb">' + titulo + '</div>'
            + '<div class="explicacao-fb">' + escapeHtml(r.explicacao || '') + '</div>';
    }

    // ---------- ações secundárias ----------
    document.getElementById('btnEspecial').addEventListener('click', function () {
        req(urls.especial, {}).then(function (r) {
            if (r.erro) { flutuar('ladoHeroi', r.erro, 'cura'); return; }
            atualizarBarras(r.estado);
            flutuar('ladoHeroi', '✦ Especial!', 'combo');
        });
    });

    document.querySelectorAll('[data-acao]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var acao = btn.getAttribute('data-acao');
            if (acao === 'pocao') {
                req(urls.pocao, { item_id: parseInt(btn.getAttribute('data-item'), 10) }).then(function (r) {
                    if (r.erro) { window.UI.alerta(r.erro, { tipo: 'erro' }); return; }
                    atualizarBarras(r.estado);
                    var cura = (r.efeito && (r.efeito.cura_hp || r.efeito.cura_mp)) || '';
                    flutuar('ladoHeroi', '+' + cura, 'cura');
                    consumirBotao(btn);
                });
            } else if (acao === 'fragmento') {
                window.UI.confirmar(
                    'O Fragmento sussurrará a resposta deste desafio. Mas a tentação cobra seu preço: sua reputação cairá e o abismo se aproximará.',
                    { titulo: 'Fragmento da IA Ancestral', arcano: true, icone: '🤖', okTexto: 'Ceder à tentação', cancelarTexto: 'Resistir' }
                ).then(function (ok) {
                    if (!ok) { return; }
                    req(urls.fragmento, {}).then(function (r) {
                        if (r.erro) { window.UI.alerta(r.erro, { tipo: 'erro' }); return; }
                        consumirBotao(btn);
                        tratarTurno(r, null);
                    });
                });
            }
        });
    });

    document.getElementById('btnFugir').addEventListener('click', function () {
        window.UI.confirmar('Fugir da batalha e voltar ao mapa? Nenhum progresso desta luta será salvo.', {
            titulo: 'Bater em retirada', perigo: true, icone: '🏃', okTexto: 'Fugir', cancelarTexto: 'Continuar lutando'
        }).then(function (ok) {
            if (!ok) { return; }
            req(urls.fugir, {}).then(function (r) { window.location = r.redirect || urls.mapa; });
        });
    });

    function consumirBotao(btn) {
        var m = btn.textContent.match(/\((\d+)\)/);
        if (m) {
            var n = parseInt(m[1], 10) - 1;
            if (n <= 0) { btn.remove(); }
            else { btn.textContent = btn.textContent.replace(/\(\d+\)/, '(' + n + ')'); }
        }
    }

    function atualizarBotoesItens() { /* hooks futuros */ }

    // ---------- resultado ----------
    function mostrarResultado(r) {
        var rec = r.recompensa || {};
        var venceu = r.resultado === 'vitoria';
        var el = document.getElementById('telaResultado');
        document.querySelector('.arena').style.display = 'none';

        var html = '<div class="tela-resultado ' + (venceu ? 'vitoria' : 'derrota') + '">';
        html += '<div class="selo">' + (venceu ? '🏆' : '💀') + '</div>';
        html += '<h1>' + (venceu ? 'Vitória!' : 'Derrota...') + '</h1>';

        if (venceu) {
            html += '<div class="estrelas-resultado">' + repetir('★', rec.estrelas || 0) + repetir('☆', 3 - (rec.estrelas || 0)) + '</div>';
            html += '<div class="recompensas">';
            html += '<span class="recompensa" style="color:var(--xp)">+' + (rec.xp || 0) + ' XP</span>';
            html += '<span class="recompensa" style="color:var(--ouro)">+' + (rec.ouro || 0) + ' Ouro</span>';
            html += '</div>';
            if (rec.niveis > 0) { html += '<p style="color:var(--primaria-2);font-weight:800">⬆ Subiu para o nível ' + rec.nivel + '!</p>'; }
            if (rec.item_drop) { html += '<p>🎁 Item obtido: <strong>' + escapeHtml(rec.item_drop.nome) + '</strong></p>'; }
            (rec.conquistas || []).forEach(function (c) {
                html += '<div class="conquista-popup">🏅 Conquista desbloqueada: <strong>' + escapeHtml(c.nome) + '</strong></div>';
            });
        } else {
            html += '<p class="subtitulo">O inimigo continuou de pé — ou seus HP zeraram, ou os desafios acabaram antes de derrubá-lo. Estude a explicação e tente novamente: a fase só é vencida derrotando o inimigo.</p>';
        }

        html += '<div style="margin-top:1.2rem;display:flex;gap:.6rem;justify-content:center;flex-wrap:wrap">';
        if (venceu && rec.redirect_final) {
            html += '<a class="botao" href="' + rec.redirect_final + '">🌌 Ver o Desfecho</a>';
        } else {
            html += '<a class="botao" href="' + urls.mapa + '">🗺️ Voltar ao Mapa</a>';
            if (!venceu) { html += '<a class="botao botao-fantasma" href="' + urls.reiniciar + '">↻ Tentar de novo</a>'; }
        }
        html += '</div></div>';

        el.innerHTML = html;
        el.style.display = 'block';
        window.scrollTo(0, 0);
    }

    // ---------- helpers ----------
    function escapeHtml(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
    function repetir(c, n) { return new Array(Math.max(0, n) + 1).join(c); }
    function tipoLabel(t) {
        return ({ multipla: 'Múltipla escolha', vf: 'Verdadeiro ou Falso', completar: 'Complete o código',
            erro: 'Encontre o erro', ordenar: 'Ordene', arrastar: 'Arraste' })[t] || t;
    }

    // ---------- início ----------
    atualizarBarras(estado);
    renderDesafio(estado.desafio);
})();
