<?php
/**
 * Tela de início (splash): logo pixel art animada + nome do jogo.
 * Aparece UMA vez por sessão do navegador, por ~2 segundos, e some.
 */
?>
<div id="splash-inicio" class="splash-inicio">
    <div class="splash-conteudo">
        <div class="splash-logo"><?= svg('ui/logo', 'splash-logo-img') ?></div>
        <h1 class="splash-titulo"><?= NOME_JOGO ?></h1>
        <p class="splash-sub"><?= SUBTITULO_JOGO ?></p>
    </div>
</div>
<script>
(function () {
    var s = document.getElementById('splash-inicio');
    if (!s) return;
    // Mostra só uma vez por sessão (não repete a cada navegação).
    if (sessionStorage.getItem('splashVisto')) { s.remove(); return; }
    sessionStorage.setItem('splashVisto', '1');
    document.body.style.overflow = 'hidden';
    setTimeout(function () { s.classList.add('splash-saindo'); }, 2000);
    setTimeout(function () { s.remove(); document.body.style.overflow = ''; }, 2600);
})();
</script>
