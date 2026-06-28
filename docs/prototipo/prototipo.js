/* Algorithmia — protótipo: microinterações nativas, sem dependências.
   Tudo respeita prefers-reduced-motion. View Transitions são puro CSS (não exigem JS). */
(function () {
  'use strict';
  var SEM_MOVIMENTO = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---- Reveal escalonado (assinatura 3) ---- */
  var alvos = document.querySelectorAll('.revelar');
  if (SEM_MOVIMENTO || !('IntersectionObserver' in window)) {
    alvos.forEach(function (el) { el.classList.add('is-visivel'); });
  } else {
    var io = new IntersectionObserver(function (entradas) {
      entradas.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('is-visivel'); io.unobserve(e.target); }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });
    alvos.forEach(function (el) { io.observe(el); });
    /* segurança: revela tudo após 1.2s caso o IO não dispare */
    setTimeout(function () { alvos.forEach(function (el) { el.classList.add('is-visivel'); }); }, 1200);
  }

  /* ---- Preenchimento de barras via --v (assinatura 4) ---- */
  function preencherBarras(escopo) {
    (escopo || document).querySelectorAll('.barra-fill[data-v]').forEach(function (f) {
      var alvo = Math.max(0, Math.min(1, parseFloat(f.getAttribute('data-v')) || 0));
      if (SEM_MOVIMENTO) { f.style.setProperty('--v', alvo); return; }
      f.style.setProperty('--v', 0);
      requestAnimationFrame(function () {
        requestAnimationFrame(function () { f.style.setProperty('--v', alvo); });
      });
    });
  }
  preencherBarras(document);

  /* ---- Marcador de Reputação (posição -100..100 → 0..100%) ---- */
  document.querySelectorAll('.reputacao[data-rep]').forEach(function (r) {
    var v = parseFloat(r.getAttribute('data-rep')) || 0;        // -100 (IA) .. +100 (Disciplina)
    var pct = ((100 - v) / 200) * 100;                           // +100 → 0% (esquerda=Disciplina)
    var m = r.querySelector('.marcador');
    if (m) { if (SEM_MOVIMENTO) { m.style.left = pct + '%'; } else { requestAnimationFrame(function(){ m.style.left = pct + '%'; }); } }
  });

  /* ---- Contador animado (ex.: ouro) ---- */
  document.querySelectorAll('[data-contador]').forEach(function (el) {
    var fim = parseInt(el.getAttribute('data-contador'), 10) || 0;
    if (SEM_MOVIMENTO) { el.textContent = fim.toLocaleString('pt-BR'); return; }
    var ini = 0, t0 = null, dur = 900;
    function passo(t) {
      if (t0 === null) t0 = t;
      var p = Math.min(1, (t - t0) / dur);
      var e = 1 - Math.pow(1 - p, 3); // easeOutCubic
      el.textContent = Math.round(ini + (fim - ini) * e).toLocaleString('pt-BR');
      if (p < 1) requestAnimationFrame(passo);
    }
    requestAnimationFrame(passo);
  });

  /* ---- Rolagem suave até o nó atual do mapa ---- */
  var atual = document.querySelector('.no-fase.atual');
  if (atual) {
    setTimeout(function () {
      atual.scrollIntoView({ behavior: SEM_MOVIMENTO ? 'auto' : 'smooth', block: 'center' });
    }, 500);
  }

  /* ---- Burst de recompensa (assinatura 5) — canvas leve, pool simples ---- */
  function burstRecompensa() {
    var fx = document.getElementById('fx');
    if (!fx) return;
    // pop num elemento alvo, se houver
    var alvoPop = document.querySelector('[data-pop]');
    if (alvoPop && !SEM_MOVIMENTO) {
      alvoPop.classList.remove('pop-recompensa'); void alvoPop.offsetWidth; alvoPop.classList.add('pop-recompensa');
    }
    if (SEM_MOVIMENTO) return; // sob reduced-motion, sem partículas
    var canvas = document.createElement('canvas');
    var dpr = Math.min(2, window.devicePixelRatio || 1);
    canvas.width = innerWidth * dpr; canvas.height = innerHeight * dpr;
    canvas.style.cssText = 'position:absolute;inset:0;width:100%;height:100%';
    fx.appendChild(canvas);
    var ctx = canvas.getContext('2d'); ctx.scale(dpr, dpr);
    var cores = ['#ffd23f', '#7c5cff', '#9d83ff', '#8ce6ff', '#2ecc71'];
    var cx = innerWidth / 2, cy = innerHeight * 0.42, N = 90, parts = [];
    for (var i = 0; i < N; i++) {
      var a = Math.PI * 2 * (i / N) + (i % 7) * 0.13;
      var sp = 4 + (i % 5) * 1.7;
      parts.push({ x: cx, y: cy, vx: Math.cos(a) * sp, vy: Math.sin(a) * sp - 3,
        life: 0, max: 60 + (i % 30), cor: cores[i % cores.length], s: 3 + (i % 3) });
    }
    var raf;
    function tick() {
      ctx.clearRect(0, 0, innerWidth, innerHeight);
      var vivos = 0;
      for (var k = 0; k < parts.length; k++) {
        var p = parts[k]; if (p.life >= p.max) continue; vivos++;
        p.life++; p.vy += 0.22; p.vx *= 0.99; p.x += p.vx; p.y += p.vy;
        ctx.globalAlpha = Math.max(0, 1 - p.life / p.max);
        ctx.fillStyle = p.cor;
        ctx.fillRect(p.x, p.y, p.s, p.s * 1.6);
      }
      if (vivos > 0) { raf = requestAnimationFrame(tick); }
      else { cancelAnimationFrame(raf); fx.removeChild(canvas); }
    }
    raf = requestAnimationFrame(tick);
  }
  var btn = document.getElementById('btnRecompensa');
  if (btn) btn.addEventListener('click', burstRecompensa);
  // expõe para a página de perfil acionar o burst ao "subir de nível"
  window.__burstRecompensa = burstRecompensa;
})();
