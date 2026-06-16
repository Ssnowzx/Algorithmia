<?php
/**
 * Tela de início (splash): cena ilustrada cyber-fantasia + botão para entrar.
 *
 * Gate no SERVIDOR: o splash (e seu PNG de ~3 MB) só é emitido na primeira
 * página da sessão ou quando forçado via ?splash=1 (ex.: após logout). Nas
 * páginas seguintes este include retorna cedo e nada é baixado.
 */
$forcarSplash = isset($_GET['splash']) && $_GET['splash'] === '1';
if (!$forcarSplash && !empty($_SESSION['splash_visto'])) {
    return;
}
$_SESSION['splash_visto'] = true;

$heroiSplash = $heroi ?? (class_exists('Auth') ? Auth::personagem() : null);
$splashEntrarUrl = $heroiSplash ? url('mapa') : url('auth/registro');
?>
<div id="splash-inicio" class="splash-inicio">
    <div class="splash-cena-wrap" aria-hidden="true">
        <img class="splash-cena-img" src="<?= asset('img/ui/splash-cena.png') ?>" alt="" decoding="async">
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

    // Limpa ?splash=1 da barra de endereço (o servidor já decidiu mostrar).
    var params = new URLSearchParams(window.location.search);
    if (params.get('splash') === '1') {
        params.delete('splash');
        var limpaUrl = window.location.pathname
            + (params.toString() ? '?' + params.toString() : '')
            + window.location.hash;
        window.history.replaceState(null, '', limpaUrl);
    }

    document.body.style.overflow = 'hidden';

    function fecharSplash(irPara) {
        if (s.classList.contains('splash-saindo')) return;
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
