<?php
/**
 * Cena SVG de título — entardecer épico sobre as terras de Algorithmia.
 * Camadas empilhadas por profundidade (data-parallax: distante baixo .. perto alto).
 * O motor window.CENA anima o viewBox (câmera) e o transform das camadas.
 * Arte 100% vetorial: montanhas facetadas com neve, nuvens volumétricas,
 * floresta em camadas, vale em sombra e plataformas de pedra em primeiro plano.
 */

if (!function_exists('cenaNuvem')) {
    /** Nuvem volumétrica = aglomerado de "puffs" com base em sombra. */
    function cenaNuvem(float $cx, float $cy, float $s, float $op): string {
        $puffs = [[-95, 12, 46], [-45, -10, 60], [5, -26, 70], [58, -8, 58], [104, 10, 46], [0, 16, 74]];
        $out = '<g transform="translate(' . $cx . ',' . $cy . ') scale(' . $s . ')" opacity="' . $op . '">';
        $out .= '<ellipse cx="0" cy="28" rx="140" ry="30" fill="url(#grad-nuvem-sombra)" opacity="0.45"/>';
        foreach ($puffs as $p) {
            $out .= '<circle cx="' . $p[0] . '" cy="' . $p[1] . '" r="' . $p[2] . '" fill="url(#grad-nuvem)"/>';
        }
        return $out . '</g>';
    }
}
if (!function_exists('cenaFloresta')) {
    /** Linha de árvores (serrilha de copas) preenchendo até a base. */
    function cenaFloresta(int $x0, int $x1, int $base, int $hMin, int $hMax, int $passo, float $fase): string {
        $d = 'M' . $x0 . ',' . ($base + 80);
        $n = (int) (($x1 - $x0) / $passo);
        for ($i = 0; $i <= $n; $i++) {
            $x = $x0 + $i * $passo;
            $h = $hMin + ($hMax - $hMin) * (0.5 + 0.5 * sin($i * 0.9 + $fase));
            $d .= ' L' . $x . ',' . $base . ' L' . ($x + $passo / 2) . ',' . round($base - $h);
        }
        $d .= ' L' . $x1 . ',' . ($base + 80) . ' L' . $x1 . ',1160 L' . $x0 . ',1160 Z';
        return $d;
    }
}
?>
<svg id="<?= e($cenaId ?? 'cenaTitulo') ?>" class="cena-svg" viewBox="0 0 1920 1080"
     preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false">

    <!-- ========== CÉU + SOL + NUVENS (fundo) ========== -->
    <g class="cena-camada" data-parallax="0.04">
        <rect x="-200" y="-200" width="2320" height="1480" fill="url(#grad-ceu)"/>
        <?php
        // Estrelas tênues no alto (entardecer).
        $estrelas = [[180,80,0.8],[420,60,0.6],[700,110,0.7],[1020,70,0.6],[1300,100,0.7],[1600,60,0.6],[1820,120,0.7]];
        foreach ($estrelas as $s) {
            printf('<use href="#sym-estrela" x="%d" y="%d" width="%.1f" height="%.1f" opacity="%.2f"/>',
                $s[0], $s[1], $s[2] * 8, $s[2] * 8, 0.3 + $s[2] * 0.2);
        }
        ?>
        <!-- Sol baixo no horizonte (backlight dourado). -->
        <ellipse cx="780" cy="610" rx="520" ry="380" fill="url(#grad-sol)"/>
        <circle cx="780" cy="600" r="66" fill="#fff3cf" opacity="0.92"/>
        <!-- Nuvens volumétricas. -->
        <g class="cena-nuvens">
            <?= cenaNuvem(360, 200, 1.15, 0.96) ?>
            <?= cenaNuvem(1120, 150, 1.4, 0.98) ?>
            <?= cenaNuvem(1640, 250, 1.05, 0.92) ?>
            <?= cenaNuvem(820, 320, 0.8, 0.78) ?>
            <?= cenaNuvem(180, 380, 0.7, 0.7) ?>
        </g>
    </g>

    <!-- ========== MONTANHAS DISTANTES (hazy, DOF) ========== -->
    <g class="cena-camada" data-parallax="0.12" filter="url(#dof)">
        <path fill="url(#grad-mont-far)" d="M-100,680 L160,470 L300,540 L520,360 L700,500 L900,400
              L1120,560 L1320,420 L1540,540 L1760,440 L2020,580 L2020,720 L-100,720 Z"/>
        <!-- Toques de neve nos cumes distantes. -->
        <g fill="#e8e2f4" opacity="0.7">
            <path d="M520,360 L556,402 L500,406 L470,402 Z"/>
            <path d="M900,400 L936,448 L876,452 L856,440 Z"/>
            <path d="M1320,420 L1356,466 L1296,468 L1284,452 Z"/>
        </g>
    </g>

    <!-- ========== MONTANHAS PRÓXIMAS (facetadas, com neve) ========== -->
    <g class="cena-camada" data-parallax="0.22">
        <!-- Pico secundário à direita (atrás). -->
        <path fill="url(#grad-mont-near)" d="M1500,680 L1600,500 L1640,380 L1720,520 L1880,680 Z"/>
        <path fill="url(#grad-neve)" d="M1612,430 L1640,380 L1672,438 L1652,452 L1640,430 L1626,452 Z"/>

        <!-- PICO 1 (esquerda) — sol à direita: face direita iluminada. -->
        <path fill="url(#grad-mont-near)" d="M120,680 L300,470 L380,520 L520,210 L620,400 L700,360 L820,520 L980,680 Z"/>
        <path fill="url(#grad-mont-sombra)" d="M520,210 L380,520 L300,470 L120,680 L520,680 Z"/>
        <path fill="url(#grad-mont-luz)" d="M520,210 L620,400 L700,360 L820,520 L980,680 L520,680 Z"/>
        <!-- Neve do cume com linha irregular. -->
        <path fill="url(#grad-neve)" d="M430,332 L470,300 L520,210 L566,296 L612,362 L586,378 L560,332 L534,374 L506,322 L478,372 L452,336 Z"/>
        <!-- Línguas de neve descendo gargantas. -->
        <g fill="url(#grad-neve)" opacity="0.85">
            <path d="M520,300 L532,372 L508,372 Z"/>
            <path d="M566,330 L578,430 L560,430 Z"/>
            <path d="M500,372 L506,470 L490,470 Z"/>
        </g>
        <!-- Cumeada iluminada (linha quente no lado do sol). -->
        <path fill="none" stroke="#ffd9a0" stroke-width="3" stroke-linejoin="round" opacity="0.65"
              d="M520,210 L620,400 L700,360 L820,520"/>

        <!-- PICO 2 (direita, o mais alto) — sol à esquerda: face esquerda iluminada. -->
        <path fill="url(#grad-mont-near)" d="M860,680 L1010,430 L1090,480 L1230,165 L1340,420 L1420,380 L1540,520 L1760,680 Z"/>
        <path fill="url(#grad-mont-sombra)" d="M1230,165 L1340,420 L1420,380 L1540,520 L1760,680 L1230,680 Z"/>
        <path fill="url(#grad-mont-luz)" d="M1230,165 L1090,480 L1010,430 L860,680 L1230,680 Z"/>
        <path fill="url(#grad-neve)" d="M1140,292 L1182,254 L1230,165 L1282,250 L1326,332 L1300,350 L1272,300 L1245,346 L1212,288 L1185,342 L1158,300 Z"/>
        <g fill="url(#grad-neve)" opacity="0.85">
            <path d="M1230,256 L1244,360 L1218,360 Z"/>
            <path d="M1282,300 L1296,410 L1276,410 Z"/>
            <path d="M1200,300 L1208,400 L1190,400 Z"/>
        </g>
        <path fill="none" stroke="#ffd9a0" stroke-width="3" stroke-linejoin="round" opacity="0.65"
              d="M1230,165 L1090,480 L1010,430"/>
    </g>

    <!-- ========== FLORESTA (camada de trás) ========== -->
    <g class="cena-camada" data-parallax="0.40">
        <path fill="url(#grad-floresta-back)" d="<?= cenaFloresta(-120, 2040, 760, 70, 130, 38, 0.0) ?>"/>
    </g>

    <!-- ========== FLORESTA (camada da frente) ========== -->
    <g class="cena-camada" data-parallax="0.52">
        <path fill="url(#grad-floresta-front)" d="<?= cenaFloresta(-120, 2040, 850, 90, 160, 52, 2.3) ?>"/>
    </g>

    <!-- ========== VALE EM SOMBRA + flores/vaga-lumes ========== -->
    <g class="cena-camada" data-parallax="0.66">
        <path fill="url(#grad-vale)" d="M-100,940 C320,890 560,960 900,930 C1240,900 1500,965 1780,930
              C1880,920 1980,936 2020,930 L2020,1160 L-100,1160 Z"/>
        <?php
        // Flores/brilhos esparsos no vale.
        for ($i = 0; $i < 26; $i++) {
            $x = 80 + ($i * 71) % 1840;
            $y = 956 + (int) (38 * (0.5 + 0.5 * sin($i * 1.7)));
            $c = ($i % 3 === 0) ? '#ffe49a' : (($i % 3 === 1) ? '#cfeede' : '#ffffff');
            printf('<circle cx="%d" cy="%d" r="%.1f" fill="%s" opacity="%.2f"/>', $x, $y, 1.6 + ($i % 3), $c, 0.5 + 0.15 * ($i % 3));
        }
        ?>
    </g>

    <!-- ========== VAGA-LUMES (motas pooled, preenchidas pelo motor) ========== -->
    <g class="cena-camada" data-parallax="0.6"><g class="cena-motas"></g></g>

    <!-- ========== PRIMEIRO PLANO: plataformas de pedra ========== -->
    <g class="cena-camada" data-parallax="0.86">
        <!-- Penhasco esquerdo. -->
        <path fill="url(#grad-rocha)" d="M-100,1080 L-100,876 L120,876 L156,910 L250,898 L300,930 L320,1080 Z"/>
        <path fill="url(#grad-rocha-topo)" d="M-100,876 L120,876 L156,910 L250,898 L300,930 L300,952 L250,920 L156,932 L120,898 L-100,898 Z"/>
        <g stroke="#06120f" stroke-width="4" opacity="0.6"><line x1="40" y1="900" x2="40" y2="1080"/><line x1="180" y1="924" x2="190" y2="1080"/></g>
        <path fill="none" stroke="#6f9183" stroke-width="2.5" opacity="0.5" d="M-100,879 L120,879 L156,912 L250,900 L300,931"/>

        <!-- Penhasco direito. -->
        <path fill="url(#grad-rocha)" d="M2020,1080 L2020,852 L1800,852 L1762,888 L1660,876 L1612,912 L1600,1080 Z"/>
        <path fill="url(#grad-rocha-topo)" d="M2020,852 L1800,852 L1762,888 L1660,876 L1612,912 L1612,936 L1660,898 L1762,910 L1800,874 L2020,874 Z"/>
        <g stroke="#06120f" stroke-width="4" opacity="0.6"><line x1="1880" y1="876" x2="1872" y2="1080"/><line x1="1700" y1="900" x2="1690" y2="1080"/></g>
        <path fill="none" stroke="#6f9183" stroke-width="2.5" opacity="0.5" d="M2020,855 L1800,855 L1762,890 L1660,878 L1612,913"/>

        <!-- Plataforma/pilar central. -->
        <path fill="url(#grad-rocha)" d="M858,1080 L876,944 L1044,944 L1064,1080 Z"/>
        <path fill="url(#grad-rocha-topo)" d="M876,944 L1044,944 L1050,968 L1040,962 L880,962 L872,968 Z"/>
        <g stroke="#06120f" stroke-width="3.5" opacity="0.55"><line x1="930" y1="962" x2="924" y2="1080"/><line x1="1000" y1="962" x2="1006" y2="1080"/></g>
        <path fill="none" stroke="#6f9183" stroke-width="2" opacity="0.45" d="M876,947 L1044,947"/>
    </g>

    <!-- Vinheta para foco. -->
    <rect x="0" y="0" width="1920" height="1080" fill="url(#grad-escurece)" pointer-events="none"/>
</svg>
