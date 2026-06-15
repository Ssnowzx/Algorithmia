<?php
/**
 * Tela de início (splash): cena SVG cinemática curta com leve push-in de câmera.
 * Aparece UMA vez por sessão (~2,4s) e some. O push-in é puramente CSS (sem
 * depender do motor window.CENA), para custar pouco em qualquer página.
 */
?>
<div id="splash-inicio" class="splash-inicio">
    <?php require __DIR__ . '/svg-defs.php'; ?>
    <svg class="splash-cena cena-svg" viewBox="0 0 1920 1080" preserveAspectRatio="xMidYMid slice"
         aria-hidden="true" focusable="false">
        <g class="splash-mundo">
            <rect x="-100" y="-100" width="2120" height="1280" fill="url(#grad-ceu)"/>
            <ellipse cx="900" cy="640" rx="540" ry="380" fill="url(#grad-sol)"/>
            <circle cx="900" cy="630" r="60" fill="#fff3cf" opacity="0.9"/>
            <!-- Montanhas distantes. -->
            <path filter="url(#dof)" fill="url(#grad-mont-far)" d="M-100,720 L220,500 L460,600 L720,430
                  L1000,580 L1280,440 L1560,600 L1840,480 L2020,620 L2020,760 L-100,760 Z"/>
            <!-- Pico próximo com neve. -->
            <path fill="url(#grad-mont-near)" d="M520,760 L760,470 L900,520 L1080,330 L1240,520 L1360,470 L1560,700 L1820,760 Z"/>
            <path fill="url(#grad-mont-sombra)" d="M1080,330 L1240,520 L1360,470 L1560,700 L1080,700 Z"/>
            <path fill="url(#grad-neve)" d="M1006,430 L1046,392 L1080,330 L1126,402 L1166,462 L1140,478 L1112,430 L1086,470 L1054,418 L1028,464 Z"/>
            <!-- Floresta + vale em sombra. -->
            <path fill="url(#grad-floresta-front)" d="M-100,820 C300,770 560,840 900,810 C1240,780 1500,852 1820,812 L2020,808 L2020,1180 L-100,1180 Z"/>
            <path fill="url(#grad-vale)" d="M-100,980 C320,930 560,1000 900,968 C1240,936 1500,1004 1820,968 L2020,962 L2020,1180 L-100,1180 Z"/>
            <rect x="0" y="0" width="1920" height="1080" fill="url(#grad-escurece)"/>
        </g>
    </svg>
    <div class="splash-conteudo">
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
    setTimeout(function () { s.classList.add('splash-saindo'); }, 2200);
    setTimeout(function () { s.remove(); document.body.style.overflow = ''; }, 2800);
})();
</script>
