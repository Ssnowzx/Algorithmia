<?php
/**
 * Cena vetorial de fundo por bioma (cards de região do mapa).
 * Recebe $bioma. Céu arcano compartilhado + neblina + textura/grão + vinheta,
 * e silhuetas específicas do bioma. Acento na cor da região (var --cor-regiao).
 * viewBox 1200x600, slice. Sem rAF: ambiente é CSS.
 *
 * NOTA: a FLORESTA está reformulada num nível mais alto (pinheiros em camadas,
 * copas orgânicas, profundidade atmosférica) como régua de qualidade. Os demais
 * biomas serão elevados ao mesmo padrão depois de aprovado.
 */

if (!function_exists('bTreeLine')) {
    function bTreeLine(int $x0, int $x1, int $base, int $hMin, int $hMax, int $passo, float $fase): string {
        $d = 'M' . $x0 . ',' . ($base + 80);
        $n = (int) (($x1 - $x0) / $passo);
        for ($i = 0; $i <= $n; $i++) {
            $x = $x0 + $i * $passo;
            $h = $hMin + ($hMax - $hMin) * (0.5 + 0.5 * sin($i * 0.9 + $fase));
            $d .= ' L' . $x . ',' . $base . ' L' . ($x + $passo / 2) . ',' . round($base - $h);
        }
        return $d . ' L' . $x1 . ',' . ($base + 80) . ' L' . $x1 . ',680 L' . $x0 . ',680 Z';
    }
}
if (!function_exists('bPinheiro')) {
    /** Pinheiro em camadas (tronco + 3 tiers de copa) com lado iluminado e ponta de luz. */
    function bPinheiro(float $x, float $y, float $s, string $fill, bool $solEsq = true): string {
        $lx = $solEsq ? -1 : 1; // direção da luz
        $o = '<g transform="translate(' . $x . ',' . $y . ') scale(' . $s . ')">';
        $o .= '<ellipse cx="0" cy="6" rx="70" ry="12" fill="#000" opacity="0.25"/>';        // sombra no chão
        $o .= '<rect x="-7" y="-38" width="14" height="46" rx="3" fill="#2a2018"/>';
        // tiers (de baixo p/ cima)
        $tiers = [[-70, 78, 0], [-108, 64, 0], [-150, 50, 0]];
        foreach ($tiers as $t) {
            $o .= '<polygon points="0,' . ($t[0] - 36) . ' ' . (-$t[1]) . ',' . $t[0] . ' ' . $t[1] . ',' . $t[0] . '" fill="' . $fill . '"/>';
            // lado iluminado
            $o .= '<polygon points="0,' . ($t[0] - 36) . ' ' . ($lx * $t[1]) . ',' . $t[0] . ' 0,' . $t[0] . '" fill="#7fae8f" opacity="0.35"/>';
        }
        // pontas pegando luz quente
        $o .= '<polygon points="0,-186 ' . ($lx * 16) . ',-150 0,-150" fill="#ffd9a0" opacity="0.55"/>';
        $o .= '</g>';
        return $o;
    }
}
$bioma = $bioma ?? 'vila';
?>
<svg class="cena-bioma" viewBox="0 0 1200 600" preserveAspectRatio="xMidYMid slice"
     aria-hidden="true" focusable="false">
    <!-- Céu + halo arcano da região + brilho quente no horizonte. -->
    <rect x="-50" y="-50" width="1300" height="700" fill="url(#grad-ceu)"/>
    <ellipse cx="600" cy="120" rx="620" ry="240" opacity="0.22" style="fill:var(--cor-regiao,#7c5cff)"/>
    <ellipse cx="640" cy="360" rx="460" ry="150" fill="url(#grad-sol)" opacity="0.5"/>

    <?php switch ($bioma):
        case 'floresta': ?>
        <!-- Profundidade: 3 fileiras de mata + neblina entre planos. -->
        <path filter="url(#dof)" fill="url(#grad-floresta-back)" opacity="0.7" d="<?= bTreeLine(-60, 1260, 300, 36, 84, 34, 0.4) ?>"/>
        <rect x="-50" y="280" width="1300" height="120" fill="url(#grad-bruma)"/>
        <path fill="url(#grad-floresta-back)" d="<?= bTreeLine(-60, 1260, 372, 52, 116, 44, 1.7) ?>"/>
        <rect x="-50" y="350" width="1300" height="120" fill="url(#grad-bruma)" opacity="0.7"/>
        <path fill="url(#grad-floresta-front)" d="<?= bTreeLine(-60, 1260, 452, 70, 150, 60, 3.2) ?>"/>
        <!-- Vaga-lumes. -->
        <g class="bioma-luzes" style="fill:var(--cor-regiao,#7c5cff)">
            <?php for ($i = 0; $i < 16; $i++) { printf('<circle cx="%d" cy="%d" r="%.1f" opacity="%.2f"/>', 60 + ($i*77)%1120, 320 + (int)(150*(0.5+0.5*sin($i*1.9))), 2 + $i%3, 0.4 + 0.12*($i%3)); } ?>
        </g>
        <!-- Pinheiros de primeiro plano (enquadram o card, com luz e sombra). -->
        <?= bPinheiro(70, 600, 1.25, 'url(#grad-floresta-front)', true) ?>
        <?= bPinheiro(210, 612, 0.9, 'url(#grad-floresta-front)', true) ?>
        <?= bPinheiro(1140, 600, 1.3, 'url(#grad-floresta-front)', false) ?>
        <?= bPinheiro(1010, 612, 0.85, 'url(#grad-floresta-front)', false) ?>
        <!-- Moitas/samambaias na base. -->
        <g fill="#0e241c">
            <path d="M-20,600 Q120,540 300,592 Q480,548 700,594 Q920,548 1120,590 Q1180,560 1240,600 L1240,640 L-20,640 Z"/>
        </g>
        <?php break; case 'montanha': ?>
        <path filter="url(#dof)" fill="url(#grad-mont-far)" d="M-50,400 L160,250 L320,330 L520,210 L720,320 L920,230 L1120,340 L1250,300 L1250,650 L-50,650 Z"/>
        <rect x="-50" y="300" width="1300" height="110" fill="url(#grad-bruma)"/>
        <path fill="url(#grad-mont-near)" d="M-50,440 L180,250 L300,300 L480,150 L640,300 L760,250 L980,420 L1250,300 L1250,680 L-50,680 Z"/>
        <path fill="url(#grad-mont-sombra)" d="M480,150 L640,300 L760,250 L980,420 L480,420 Z"/>
        <path fill="url(#grad-neve)" d="M430,210 L480,150 L534,214 L508,228 L482,196 L456,226 Z"/>
        <path fill="url(#grad-neve)" opacity="0.85" d="M150,288 L180,250 L214,292 L196,302 L178,278 L164,300 Z"/>
        <?php break; case 'porto': ?>
        <rect x="-50" y="360" width="1300" height="320" fill="url(#grad-vale)" opacity="0.9"/>
        <g stroke="#fff" opacity="0.18" stroke-width="3">
            <line x1="120" y1="410" x2="360" y2="410"/><line x1="500" y1="450" x2="820" y2="450"/>
            <line x1="220" y1="500" x2="560" y2="500"/><line x1="700" y1="540" x2="1040" y2="540"/>
        </g>
        <path fill="#0c0a1c" d="M980,360 L1000,360 L1012,150 L968,150 Z"/>
        <circle cx="990" cy="150" r="22" style="fill:var(--cor-regiao,#7c5cff)" filter="url(#glow)"/>
        <polygon points="990,150 1230,90 1230,210" style="fill:var(--cor-regiao,#7c5cff)" opacity="0.22"/>
        <g fill="#0c0a1c"><rect x="120" y="380" width="14" height="120"/><rect x="200" y="380" width="14" height="120"/><rect x="280" y="380" width="14" height="120"/><rect x="110" y="378" width="200" height="14"/></g>
        <?php break; case 'torre': ?>
        <g fill="#171633">
            <rect x="170" y="200" width="70" height="460"/><rect x="430" y="120" width="80" height="540"/>
            <rect x="700" y="240" width="64" height="420"/><rect x="930" y="160" width="76" height="500"/>
        </g>
        <g style="fill:var(--cor-regiao,#7c5cff)">
            <rect x="195" y="230" width="20" height="20"/><rect x="455" y="160" width="22" height="22"/>
            <rect x="716" y="270" width="18" height="18"/><rect x="952" y="200" width="20" height="20"/>
        </g>
        <g stroke="var(--cor-regiao,#7c5cff)" stroke-width="2.5" opacity="0.45" filter="url(#glow)">
            <line x1="205" y1="240" x2="466" y2="171"/><line x1="466" y1="171" x2="725" y2="279"/>
            <line x1="725" y1="279" x2="962" y2="210"/>
        </g>
        <g style="fill:var(--cor-regiao,#7c5cff)"><circle cx="335" cy="205" r="5"/><circle cx="595" cy="225" r="5"/><circle cx="843" cy="244" r="5"/></g>
        <?php break; case 'cidadela': ?>
        <path fill="#171633" d="M-50,660 L-50,360 L120,360 L120,300 L180,300 L180,360 L420,360 L420,250 L500,250 L500,360 L820,360 L820,300 L880,300 L880,360 L1120,360 L1120,280 L1200,280 L1200,360 L1250,360 L1250,660 Z"/>
        <g fill="#0c0a1c"><path d="M430,250 L460,210 L490,250 Z"/><path d="M1130,280 L1160,236 L1190,280 Z"/></g>
        <g style="fill:var(--cor-regiao,#7c5cff)" opacity="0.9">
            <rect x="450" y="290" width="18" height="26" rx="3"/><rect x="1146" y="312" width="16" height="24" rx="3"/><rect x="132" y="318" width="16" height="22" rx="3"/>
        </g>
        <?php break; case 'abismo': ?>
        <path fill="#0c0a1c" d="M-50,660 L-50,300 L260,360 L260,200 L420,260 L420,660 Z"/>
        <path fill="#0c0a1c" d="M1250,660 L1250,300 L940,360 L940,200 L780,260 L780,660 Z"/>
        <ellipse cx="600" cy="430" rx="130" ry="230" style="fill:var(--cor-regiao,#7c5cff)" opacity="0.5" filter="url(#glow)"/>
        <ellipse cx="600" cy="430" rx="50" ry="150" fill="#0c0a1c" opacity="0.7"/>
        <?php break; default: /* vila */ ?>
        <path fill="url(#grad-floresta-back)" d="M-50,420 C200,380 380,440 600,410 C820,380 1000,440 1250,410 L1250,680 L-50,680 Z"/>
        <path fill="url(#grad-vale)" d="M-50,500 C220,470 420,520 680,495 C940,470 1080,515 1250,498 L1250,680 L-50,680 Z"/>
        <g fill="#0c0a1c"><path d="M260,470 L310,420 L360,470 L360,520 L260,520 Z"/><path d="M800,480 L848,434 L896,480 L896,524 L800,524 Z"/></g>
        <g style="fill:var(--cor-regiao,#7c5cff)"><rect x="298" y="468" width="16" height="22" rx="2"/><rect x="836" y="478" width="16" height="22" rx="2"/></g>
    <?php endswitch; ?>

    <!-- Grão pintado + vinheta (todas as cenas). -->
    <rect x="0" y="0" width="1200" height="600" filter="url(#textura)" opacity="0.5" pointer-events="none"/>
    <rect x="0" y="0" width="1200" height="600" fill="url(#grad-escurece)" pointer-events="none"/>
</svg>
