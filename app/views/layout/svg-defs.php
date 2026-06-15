<?php
/**
 * Defs SVG compartilhados das cenas vetoriais (sistema window.CENA).
 * Paleta CREPÚSCULO (entardecer épico): céu quente, montanhas nevadas pegando
 * luz dourada, vale em sombra. Cores específicas de cena convivem com os tokens
 * de :root onde fazem sentido (ouro/arcano/verde).
 *
 * IMPORTANTE: o container NÃO pode usar display:none — quebra refs de
 * gradiente/filtro. Mantemos 0x0 fora de tela.
 */
?>
<svg class="svg-defs" width="0" height="0" aria-hidden="true" focusable="false"
     style="position:absolute;width:0;height:0;overflow:hidden">
    <defs>
        <!-- Céu de crepúsculo: indigo no alto -> magenta -> coral -> ouro no horizonte. -->
        <linearGradient id="grad-ceu" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0"    stop-color="#1a1340"/>
            <stop offset="0.30" stop-color="#3c2566"/>
            <stop offset="0.52" stop-color="#7a3a76"/>
            <stop offset="0.70" stop-color="#c25a6a"/>
            <stop offset="0.84" stop-color="#e98a5a"/>
            <stop offset="1"    stop-color="#f6c478"/>
        </linearGradient>

        <!-- Brilho do sol baixo no horizonte (dourado). -->
        <radialGradient id="grad-sol" cx="0.5" cy="0.5" r="0.5">
            <stop offset="0"   stop-color="#fff2cf" stop-opacity="0.98"/>
            <stop offset="0.35" stop-color="#ffd98a" stop-opacity="0.8"/>
            <stop offset="0.7" stop-color="#ffb866" stop-opacity="0.35"/>
            <stop offset="1"   stop-color="#ffb866" stop-opacity="0"/>
        </radialGradient>

        <!-- Nuvens: topo quente iluminado, base fria/roxa. -->
        <linearGradient id="grad-nuvem" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0"   stop-color="#fff0dc"/>
            <stop offset="0.55" stop-color="#f3cdb0"/>
            <stop offset="1"   stop-color="#b07e9e"/>
        </linearGradient>
        <linearGradient id="grad-nuvem-sombra" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#caa0b4"/>
            <stop offset="1" stop-color="#8c6792"/>
        </linearGradient>

        <!-- Montanhas distantes (hazy, dessaturadas, lilás claro). -->
        <linearGradient id="grad-mont-far" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#b89ec4"/>
            <stop offset="1" stop-color="#8f7aad"/>
        </linearGradient>

        <!-- Montanhas próximas: corpo (cume mais claro -> base em sombra). -->
        <linearGradient id="grad-mont-near" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0"   stop-color="#6a5d9a"/>
            <stop offset="0.5" stop-color="#4b4080"/>
            <stop offset="1"   stop-color="#2a2350"/>
        </linearGradient>
        <!-- Face em sombra (lado oposto ao sol). -->
        <linearGradient id="grad-mont-sombra" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#352b5e"/>
            <stop offset="1" stop-color="#1d1840"/>
        </linearGradient>
        <!-- Face iluminada pelo sol (quente). -->
        <linearGradient id="grad-mont-luz" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0"   stop-color="#e79a6a" stop-opacity="0.55"/>
            <stop offset="1"   stop-color="#e79a6a" stop-opacity="0"/>
        </linearGradient>
        <!-- Neve: highlight quente no topo, sombra fria embaixo. -->
        <linearGradient id="grad-neve" x1="0" y1="0" x2="0.3" y2="1">
            <stop offset="0"   stop-color="#fff4e6"/>
            <stop offset="0.5" stop-color="#ffe2c2"/>
            <stop offset="1"   stop-color="#bcc1e6"/>
        </linearGradient>

        <!-- Banda de névoa atmosférica na base das montanhas. -->
        <linearGradient id="grad-haze" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#e9a878" stop-opacity="0"/>
            <stop offset="0.6" stop-color="#d98f8a" stop-opacity="0.45"/>
            <stop offset="1" stop-color="#7a4a72" stop-opacity="0.2"/>
        </linearGradient>

        <!-- Floresta (duas camadas, teal-verde de entardecer). -->
        <linearGradient id="grad-floresta-back" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#2f5a4e"/>
            <stop offset="1" stop-color="#214239"/>
        </linearGradient>
        <linearGradient id="grad-floresta-front" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#1d3f33"/>
            <stop offset="1" stop-color="#102a22"/>
        </linearGradient>

        <!-- Vale em sombra (verde escuro de crepúsculo). -->
        <linearGradient id="grad-vale" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#2b5742"/>
            <stop offset="1" stop-color="#15301f"/>
        </linearGradient>

        <!-- Rocha do primeiro plano (quase preta, teal). -->
        <linearGradient id="grad-rocha" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#1b302d"/>
            <stop offset="1" stop-color="#081512"/>
        </linearGradient>
        <linearGradient id="grad-rocha-topo" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#33524a"/>
            <stop offset="1" stop-color="#1b302d"/>
        </linearGradient>

        <!-- Profundidade (DOF): blur fraco nas camadas distantes. -->
        <filter id="dof" x="-5%" y="-5%" width="110%" height="110%">
            <feGaussianBlur stdDeviation="3"/>
        </filter>
        <!-- Suavização de nuvens. -->
        <filter id="suave" x="-20%" y="-20%" width="140%" height="140%">
            <feGaussianBlur stdDeviation="6"/>
        </filter>
        <!-- Glow arcano. -->
        <filter id="glow" x="-60%" y="-60%" width="220%" height="220%">
            <feGaussianBlur stdDeviation="3" result="b"/>
            <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
        <!-- Névoa fractal (região limitada; movida por transform, não por frame). -->
        <filter id="nevoa" x="-20%" y="-20%" width="140%" height="140%">
            <feTurbulence type="fractalNoise" baseFrequency="0.01 0.018" numOctaves="2" seed="11" result="t"/>
            <feColorMatrix in="t" type="matrix"
                values="0 0 0 0 0.92
                        0 0 0 0 0.7
                        0 0 0 0 0.62
                        0 0 0 0.45 0"/>
        </filter>

        <!-- Grão/textura sutil (quebra o "flat" do vetor, dá ar pintado). -->
        <filter id="textura" x="0" y="0" width="100%" height="100%">
            <feTurbulence type="fractalNoise" baseFrequency="0.85" numOctaves="2" stitchTiles="stitch" result="n"/>
            <feColorMatrix in="n" type="matrix"
                values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 0.05 0"/>
        </filter>
        <!-- Neblina volumétrica macia entre planos. -->
        <linearGradient id="grad-bruma" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0"   stop-color="#e8b6a0" stop-opacity="0"/>
            <stop offset="0.5" stop-color="#caa6c0" stop-opacity="0.35"/>
            <stop offset="1"   stop-color="#caa6c0" stop-opacity="0"/>
        </linearGradient>

        <!-- Vinheta como preenchimento direto (transparente no centro -> escuro nas bordas). -->
        <radialGradient id="grad-escurece" cx="0.5" cy="0.46" r="0.75">
            <stop offset="0"    stop-color="#0a0612" stop-opacity="0"/>
            <stop offset="0.6"  stop-color="#0a0612" stop-opacity="0"/>
            <stop offset="1"    stop-color="#0a0612" stop-opacity="0.62"/>
        </radialGradient>

        <!-- Símbolos. -->
        <symbol id="sym-estrela" viewBox="0 0 10 10"><circle cx="5" cy="5" r="1.6" fill="#fff"/></symbol>
        <symbol id="sym-mota" viewBox="0 0 12 12">
            <circle cx="6" cy="6" r="2.6" fill="#ffe49a" filter="url(#glow)"/>
        </symbol>
    </defs>
</svg>
