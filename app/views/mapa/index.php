<?php
/** Mapa-múndi com a trilha de fases por região. */
$iconePorTipo = [
    'historia'    => '📜',
    'licao'       => '⚔️',
    'secundaria'  => '⭐',
    'chefe'       => '👹',
    'chefe_final' => '💀',
];
?>
<?php require __DIR__ . '/../layout/svg-defs.php'; ?>
<div class="mapa-pano-fundo" aria-hidden="true"></div>
<div class="mapa-wrap">
    <div class="mapa-cabecalho">
        <?php /* §5.1 — ícone ilustrado ui/icones/icone-mapa.png (não usar emoji 🗺️) */ ?>
        <h1><?= svg('ui/icones/icone-mapa', 'icone-mapa-titulo') ?> Mapa de Algorithmia</h1>
        <p class="mapa-progresso-geral">
            <?= (int) $concluidas ?> / <?= (int) $totalFases ?> fases concluídas ·
            <span style="color:var(--xp)">★ <?= (int) $totalEstrelas ?> estrelas</span>
        </p>
        <?php $pctMapa = $totalFases > 0 ? round($concluidas / $totalFases * 100) : 0; ?>
        <div class="mapa-barra-progresso" role="progressbar"
             aria-valuenow="<?= (int) $concluidas ?>" aria-valuemin="0" aria-valuemax="<?= (int) $totalFases ?>"
             aria-label="Progresso da jornada: <?= $pctMapa ?>%">
            <div class="mapa-barra-fill" style="width: <?= $pctMapa ?>%"></div>
        </div>
    </div>

    <?php foreach ($regioes as $chaveRegiao => $regiao): ?>
        <?php
            $fundoBase = $regiao['fundo'] ?? fundoRegiao($regiao['svg_slug'] ?? null);
            $biomaReg = str_replace('fundo-', '', $fundoBase); // ex.: 'floresta', 'torre'
            $iconeRegiao = iconeRegiaoMapa((string) $chaveRegiao);
        ?>
        <section class="regiao bioma-<?= e($biomaReg) ?>" style="--cor-regiao: <?= e($regiao['cor']) ?>; border-color: <?= e($regiao['cor']) ?>55;">
            <?php
                // Cenário da região: usa a arte (PNG) se existir; senão, a cena vetorial.
                $temFundoImg = is_file(__DIR__ . '/../../../public/img/fundos/' . $fundoBase . '.png');
            ?>
            <?php if ($temFundoImg): ?>
                <?php
                    // Cards dos mestres são altos; início/fim são baixos.
                    $fundosRetrato = ['fundo-porto', 'fundo-cidadela', 'fundo-floresta', 'fundo-montanha', 'fundo-torre'];
                    $fmtFundo = in_array($fundoBase, $fundosRetrato, true) ? 'retrato' : 'paisagem';
                ?>
                <div class="cena-bioma cena-bioma-img-wrap cena-bioma-<?= e($fmtFundo) ?>" aria-hidden="true">
                    <img class="cena-bioma-img" src="<?= e(srcImagem('fundos/' . $fundoBase) ?? '') ?>" alt="" loading="eager" decoding="async">
                </div>
            <?php else: ?>
                <?php $bioma = $biomaReg; require __DIR__ . '/../layout/cena-bioma.php'; ?>
            <?php endif; ?>
            <div class="regiao-cabecalho">
                <?php if (!empty($regiao['svg_slug'])): ?>
                    <div class="retrato-mestre"><?= svg('mestres/' . $regiao['svg_slug']) ?></div>
                <?php elseif ($iconeRegiao): ?>
                    <div class="retrato-mestre retrato-icone"><?= svg($iconeRegiao) ?></div>
                <?php else: ?>
                    <div class="retrato-mestre"><span style="font-size:1.6rem"><?= $regiao['emoji'] ?? '🌍' ?></span></div>
                <?php endif; ?>
                <div>
                    <h2><?= e($regiao['regiao'] ?? 'Terras de Hello World') ?></h2>
                    <?php if (!empty($regiao['mestre_nome'])): ?>
                        <div class="mestre-nome">Mestre: <?= e($regiao['mestre_nome']) ?></div>
                    <?php else: ?>
                        <div class="mestre-nome"><?= e($regiao['subtitulo'] ?? 'Início e fim da jornada') ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="trilha">
                <?php foreach ($regiao['fases'] as $fase): ?>
                    <?php
                        $estado = $fase['estado'];
                        $icone = $iconePorTipo[$fase['tipo']] ?? '⚔️';
                        // Nó com inimigo mostra o SPRITE NOVO do inimigo (recortado);
                        // fases de história mantêm o emblema/cenário da fase.
                        $inimigoSlug = trim((string) ($fase['inimigo_svg'] ?? ''));
                        $emblemaFase = iconeFaseMapa((int) ($fase['ordem_global'] ?? 0));
                        $arteNo = $inimigoSlug !== ''
                            ? svgSlug($inimigoSlug, 'icone-fase icone-fase-inimigo')
                            : ($emblemaFase ? svg($emblemaFase, 'icone-fase') : '');
                        $clicavel = $estado !== 'bloqueada';
                        $tag = $clicavel ? 'a' : 'span';
                        $href = $clicavel ? 'href="' . url('historia/ver/' . (int) $fase['id']) . '"' : '';
                        // Leitura do inimigo: tooltip completo + chip curto p/ ameaças
                        // notáveis ainda não vencidas (motiva equipar antes de entrar).
                        $tatica = $inimigoSlug !== ''
                            ? taticaInimigo((int) ($fase['inimigo_hp'] ?? 0), (int) ($fase['inimigo_ataque'] ?? 0))
                            : '';
                        $taticaTag = $tatica !== ''
                            ? taticaTag((int) ($fase['inimigo_hp'] ?? 0), (int) ($fase['inimigo_ataque'] ?? 0))
                            : '';
                        $tituloNo = $fase['nome'] . ($tatica !== '' ? ' — ' . $tatica : '');
                    ?>
                    <div class="no-fase no-tipo-<?= e($fase['tipo']) ?> <?= e($estado) ?>">
                        <<?= $tag ?> class="no-bolha<?= $arteNo ? ' no-bolha-arte' : '' ?>" <?= $href ?> title="<?= e($tituloNo) ?>">
                            <?php if ($arteNo): ?>
                                <?= $arteNo ?>
                            <?php else: ?>
                                <span class="emoji"><?= $icone ?></span>
                            <?php endif; ?>
                            <?php if ($estado === 'bloqueada'): ?><span style="position:absolute;bottom:-2px;right:-2px;font-size:.9rem">🔒</span><?php endif; ?>
                        </<?= $tag ?>>
                        <div class="no-estrelas">
                            <?php if ($fase['estrelas'] > 0): ?>
                                <?= str_repeat('★', (int) $fase['estrelas']) . str_repeat('☆', 3 - (int) $fase['estrelas']) ?>
                            <?php endif; ?>
                        </div>
                        <div class="no-rotulo"><?= e($fase['nome']) ?></div>
                        <?php if ($taticaTag !== '' && $estado !== 'concluida'): ?>
                            <div class="no-tatica" title="<?= e($tatica) ?>"><?= e($taticaTag) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>
<script>
// Rola suavemente até a próxima fase disponível, para o jogador não se perder.
(function () {
    var atual = document.querySelector('.no-fase.atual');
    if (atual) {
        setTimeout(function () {
            atual.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 600);
    }
})();
</script>
