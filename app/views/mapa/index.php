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
<div class="mapa-wrap">
    <div class="mapa-cabecalho">
        <h1>🗺️ Mapa de Algorithmia</h1>
        <p class="mapa-progresso-geral">
            <?= (int) $concluidas ?> / <?= (int) $totalFases ?> fases concluídas ·
            <span style="color:var(--xp)">★ <?= (int) $totalEstrelas ?> estrelas</span>
        </p>
    </div>

    <?php foreach ($regioes as $regiao): ?>
        <?php $fundoReg = asset('img/fundos/' . ($regiao['fundo'] ?? fundoRegiao($regiao['svg_slug'] ?? null)) . '.png'); ?>
        <section class="regiao" style="--cor-regiao: <?= e($regiao['cor']) ?>; border-color: <?= e($regiao['cor']) ?>55; background-image: linear-gradient(180deg, rgba(13,16,38,.86), rgba(13,16,38,.94)), url('<?= e($fundoReg) ?>'); background-size: cover; background-position: center; image-rendering: pixelated;">
            <div class="regiao-cabecalho">
                <?php if (!empty($regiao['svg_slug'])): ?>
                    <div class="retrato-mestre"><?= svg('mestres/' . $regiao['svg_slug']) ?></div>
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
                        $clicavel = $estado !== 'bloqueada';
                        $tag = $clicavel ? 'a' : 'span';
                        $href = $clicavel ? 'href="' . url('historia/ver/' . (int) $fase['id']) . '"' : '';
                    ?>
                    <div class="no-fase no-tipo-<?= e($fase['tipo']) ?> <?= e($estado) ?>">
                        <<?= $tag ?> class="no-bolha" <?= $href ?> title="<?= e($fase['nome']) ?>">
                            <span class="emoji"><?= $icone ?></span>
                            <?php if ($estado === 'bloqueada'): ?><span style="position:absolute;bottom:-2px;right:-2px;font-size:.9rem">🔒</span><?php endif; ?>
                        </<?= $tag ?>>
                        <div class="no-estrelas">
                            <?php if ($fase['estrelas'] > 0): ?>
                                <?= str_repeat('★', (int) $fase['estrelas']) . str_repeat('☆', 3 - (int) $fase['estrelas']) ?>
                            <?php endif; ?>
                        </div>
                        <div class="no-rotulo"><?= e($fase['nome']) ?></div>
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
