<?php
/**
 * Tela de início (splash): cena ilustrada cyber-fantasia + botão para entrar.
 * Aparece UMA vez por sessão; só sai quando o jogador clica em entrar.
 */
$heroiSplash = $heroi ?? (class_exists('Auth') ? Auth::personagem() : null);
$splashEntrarUrl = $heroiSplash ? url('mapa') : url('auth/registro');
?>
<div id="splash-inicio" class="splash-inicio">
    <div class="splash-cena-wrap" aria-hidden="true">
        <img class="splash-cena-img" src="<?= asset('img/ui/splash-cena.png') ?>" alt="">
        <div class="splash-cena-overlay"></div>
        <div class="splash-particulas"></div>
    </div>
    <div class="splash-conteudo">
        <?= marcaHtml('splash') ?>
        <p class="splash-sub"><?= SUBTITULO_JOGO ?></p>
        <a id="splash-entrar" class="splash-entrar" href="<?= e($splashEntrarUrl) ?>">
            <img src="<?= asset('img/ui/botao-entrar-mundo.png') ?>"
                 alt="Entrar no mundo" class="splash-entrar-img" loading="eager">
        </a>
    </div>
</div>
<script>
(function () {
    var s = document.getElementById('splash-inicio');
    var btn = document.getElementById('splash-entrar');
    if (!s || !btn) return;

    var params = new URLSearchParams(window.location.search);
    var forcarSplash = params.get('splash') === '1';
    if (forcarSplash) {
        sessionStorage.removeItem('splashVisto');
        params.delete('splash');
        var limpaUrl = window.location.pathname
            + (params.toString() ? '?' + params.toString() : '')
            + window.location.hash;
        window.history.replaceState(null, '', limpaUrl);
    }

    if (!forcarSplash && sessionStorage.getItem('splashVisto')) { s.remove(); return; }

    document.body.style.overflow = 'hidden';

    function fecharSplash(irPara) {
        if (s.classList.contains('splash-saindo')) return;
        sessionStorage.setItem('splashVisto', '1');
        s.classList.add('splash-saindo');
        document.body.style.overflow = '';
        setTimeout(function () {
            s.remove();
            if (irPara && irPara !== window.location.href) {
                window.location.href = irPara;
            }
        }, 600);
    }

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        var dest = btn.getAttribute('href');
        var ir = dest;
        try {
            var destUrl = new URL(dest, window.location.origin);
            if (destUrl.pathname === window.location.pathname
                && destUrl.search === window.location.search) {
                ir = null;
            }
        } catch (err) { /* mantém destino */ }
        fecharSplash(ir);
    });
})();
</script>
