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
    var dueloAtivo = false; // banner do Duelo Final já foi exibido?

    var elPainel = document.getElementById('painelDesafio');
    var elCombo = document.getElementById('comboInd');

    var LETRAS = ['A', 'B', 'C', 'D', 'E', 'F'];

    // ---------- utilidades ----------
    function req(url, corpo) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': (B && B.csrf) || '' },
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
        if (s.morte_subita) {
            // No Duelo Final o indicador mostra a fúria crescente em vez do combo.
            elCombo.textContent = '🔥 DUELO FINAL — Fúria x' + (s.rodada_subita || 1);
        } else {
            elCombo.textContent = s.combo > 1 ? ('COMBO x' + s.combo + '!') : '';
            if (s.especial_armado) { elCombo.textContent += ' ✦ESPECIAL'; }
        }
    }

    // Revela o banner dramático do Duelo Final uma única vez e tinge a arena.
    function checarDueloFinal(s) {
        if (!s || !s.morte_subita || dueloAtivo) { return; }
        dueloAtivo = true;
        var campo = document.getElementById('campo');
        if (campo) { campo.classList.add('morte-subita'); }
        var banner = document.getElementById('bannerDuelo');
        if (banner) { banner.hidden = false; banner.classList.add('mostrar'); }
        som('especial');
        if (J) { J.shake(0.7); }
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

    function flutuar(lado, texto, classe, critico, detalhe) {
        var alvo = document.getElementById(lado);
        var span = document.createElement('span');
        span.className = 'flutuante ' + classe + (critico ? ' critico' : '');
        span.style.setProperty('--drift', (Math.random() * 44 - 22).toFixed(0) + 'px');
        span.style.left = (40 + Math.random() * 20) + '%';
        span.style.top = '30%';
        var num = document.createElement('span');
        num.className = 'num';
        num.textContent = texto;
        span.appendChild(num);
        // Sublinha opcional: atribui o efeito a um item ("+5 da arma").
        if (detalhe) {
            var det = document.createElement('span');
            det.className = 'detalhe';
            det.textContent = detalhe;
            span.appendChild(det);
        }
        alvo.appendChild(span);
        span.addEventListener('animationend', function () { span.remove(); });
        setTimeout(function () { if (span.parentNode) { span.remove(); } }, 1400);
    }

    var J = window.JUICE;
    function som(nome, arg) { if (window.SOM && SOM[nome]) { SOM[nome](arg); } }

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
            case 'ordenar': renderOrdenar(zona, d.opcoes); break;
            case 'arrastar': renderArrastar(zona, d.opcoes); break;
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

    // Tipo "relacionar" (arrastar): opcoes = { itens: [...], alvos: [...] } e a
    // resposta é o índice do alvo correto de cada item. Renderiza um <select> de
    // alvos por item; o jogador liga cada item ao seu alvo.
    function renderArrastar(zona, opcoes) {
        var itens = (opcoes && opcoes.itens) || [];
        var alvos = (opcoes && opcoes.alvos) || [];

        var dica = document.createElement('p');
        dica.className = 'dialogo-dica';
        if (!itens.length || !alvos.length) {
            dica.textContent = 'Esta pergunta está sem itens/alvos configurados. Avise um Mestre.';
            zona.appendChild(dica);
            return;
        }
        dica.textContent = 'Relacione cada item ao alvo correto e confirme.';
        zona.appendChild(dica);

        // Embaralha a ORDEM DE EXIBIÇÃO dos alvos; o value guarda o índice
        // original, então a correção continua batendo com a resposta do banco.
        var ordemAlvos = alvos.map(function (_, i) { return i; });
        for (var k = ordemAlvos.length - 1; k > 0; k--) {
            var j = Math.floor(Math.random() * (k + 1));
            var t = ordemAlvos[k]; ordemAlvos[k] = ordemAlvos[j]; ordemAlvos[j] = t;
        }

        var lista = document.createElement('div');
        lista.className = 'relacionar-lista';
        var selects = [];
        itens.forEach(function (item) {
            var par = document.createElement('div');
            par.className = 'relacionar-par';

            var lbl = document.createElement('span');
            lbl.className = 'relacionar-item';
            lbl.textContent = item;

            var seta = document.createElement('span');
            seta.className = 'relacionar-seta';
            seta.textContent = '→';

            var sel = document.createElement('select');
            sel.className = 'relacionar-select';
            sel.setAttribute('aria-label', 'Alvo para: ' + item);
            var ph = document.createElement('option');
            ph.value = ''; ph.textContent = '— escolha —';
            ph.disabled = true; ph.selected = true;
            sel.appendChild(ph);
            ordemAlvos.forEach(function (origIdx) {
                var op = document.createElement('option');
                op.value = origIdx;
                op.textContent = alvos[origIdx];
                sel.appendChild(op);
            });

            selects.push(sel);
            par.appendChild(lbl);
            par.appendChild(seta);
            par.appendChild(sel);
            lista.appendChild(par);
        });
        zona.appendChild(lista);

        var btn = document.createElement('button');
        btn.className = 'botao';
        btn.textContent = 'Confirmar';
        btn.addEventListener('click', function () {
            var resp = selects.map(function (s) { return s.value === '' ? -1 : parseInt(s.value, 10); });
            if (resp.indexOf(-1) !== -1) {
                window.UI.alerta('Relacione todos os itens antes de confirmar.', { tipo: 'erro' });
                return;
            }
            enviar(resp, btn);
        });
        zona.appendChild(btn);
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

        // Som imediato do desfecho da resposta.
        if (r.via_ia) { som('fragmento'); }
        else if (r.correto) { som('acerto'); }
        else { som('erro'); }

        // Animações de ataque + dano: o atacante investe e o alvo reage logo depois.
        if (r.dano_inimigo) {
            var critIni = r.dano_inimigo >= 25;
            var detArma = (r.dano_equip && r.dano_equip > 0) ? ('+' + r.dano_equip + ' da arma') : '';
            atacar('spriteHeroi');
            setTimeout(function () {
                tremer('spriteInimigo');
                flutuar('ladoInimigo', '-' + r.dano_inimigo, 'dano', critIni, detArma);
                som('danoInimigo');
                if (J) { J.faiscas('spriteInimigo', 'inimigo'); J.shake(critIni ? 0.5 : 0.3); }
            }, 170);
        }
        if (r.dano_heroi) {
            var critHer = r.dano_heroi >= 20;
            var detEscudo = (r.bloqueado && r.bloqueado > 0) ? ('escudo evitou ' + r.bloqueado) : '';
            atacar('spriteInimigo');
            setTimeout(function () {
                tremer('spriteHeroi');
                flutuar('ladoHeroi', '-' + r.dano_heroi, 'dano', critHer, detEscudo);
                som('danoHeroi');
                if (J) { J.faiscas('spriteHeroi', 'heroi'); J.shake(critHer ? 0.5 : 0.35); }
            }, 170);
        }
        if (r.combo && r.combo > 1) { flutuar('ladoInimigo', 'x' + r.combo, 'combo'); som('combo', r.combo); }

        atualizarBarras(r.estado);
        checarDueloFinal(r.estado);
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
            som('especial');
            var sp = document.getElementById('spriteHeroi');
            if (sp) { sp.classList.add('especial-glow'); setTimeout(function () { sp.classList.remove('especial-glow'); }, 700); }
            if (J) { J.especial(); }
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
                    som('pocao');
                    if (J) { J.cura(); }
                    // Objetivo "1ª poção": mostra o ouro ganho sem interromper a luta.
                    if (r.objetivo) {
                        flutuar('ladoHeroi', '🏅 +' + r.objetivo.ouro + ' ouro', 'combo');
                        som('ouro');
                    }
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

    // ---------- resultado ----------
    function mostrarResultado(r) {
        var rec = r.recompensa || {};
        var venceu = r.resultado === 'vitoria';

        // Som + juice do desfecho. Adia a troca de tela para a explosão/shake
        // serem vistos na arena antes dela sumir (sem juice, revela na hora).
        var atraso = 0;
        if (venceu) {
            som('vitoria');
            if (J && J.movimento) { J.explosao('spriteInimigo'); J.shake(0.9); atraso = 700; }
            setTimeout(function () { if (rec.ouro && J) { J.ouro(); som('ouro'); } }, 360);
            if (rec.niveis > 0) { setTimeout(function () { som('nivel'); }, 720); }
        } else {
            som('derrota');
            if (J && J.movimento) { J.shake(0.8); atraso = 500; }
        }

        setTimeout(revelar, atraso);
        function revelar() {

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
            html += '<p class="subtitulo">Seus HP zeraram. A fase só é vencida derrotando o inimigo — estude a explicação, ajuste a estratégia e volte para o troco.</p>';
        }

        html += resumoHtml(r.resumo, venceu);

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
        } // fim revelar
    }

    // Relatório de combate: dá rosto aos itens (dano da arma, bloqueio do escudo,
    // cura das poções) e, na derrota, uma dica estratégica concreta.
    function resumoHtml(resumo, venceu) {
        if (!resumo) { return ''; }
        var h = '<div class="resumo-batalha">';
        h += '<h3>Relatório de combate</h3><ul>';
        if (resumo.tem_equip) {
            h += '<li>⚔ Sua arma somou <strong>' + (resumo.dano_arma || 0) + '</strong> de dano</li>';
            h += '<li>🛡 Seu escudo evitou <strong>' + (resumo.bloqueado || 0) + '</strong> de dano</li>';
        }
        if (resumo.hp_curado) { h += '<li>🧪 Poções recuperaram <strong>' + resumo.hp_curado + '</strong> de HP</li>'; }
        h += '<li>🎯 Acertos: <strong>' + (resumo.acertos || 0) + '</strong> · Erros: <strong>' + (resumo.erros || 0) + '</strong></li>';
        h += '</ul>';
        if (resumo.morte_subita) { h += '<p class="selo-duelo">⚔ Decidido no Duelo Final!</p>'; }
        h += '</div>';
        if (!venceu && resumo.dica) {
            h += '<div class="dica-derrota">💡 ' + escapeHtml(resumo.dica) + '</div>';
        }
        return h;
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
    checarDueloFinal(estado); // recarregou no meio de um Duelo Final?
    renderDesafio(estado.desafio);
})();
