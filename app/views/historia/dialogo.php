<?php
/** Cena de diálogo com efeito máquina de escrever. */
$linhas = [];
$slugsUnicos = [];
foreach ($dialogos as $d) {
    $slug = $d['svg_slug'] ?: '';
    $linhas[] = ['falante' => $d['falante'], 'texto' => $d['texto'], 'slug' => $slug];
    if ($slug && !in_array($slug, $slugsUnicos, true)) {
        $slugsUnicos[] = $slug;
    }
}

// Define o destino do botão de ação ao fim do diálogo.
if ($ehCombate) {
    $acaoHref = url('batalha/iniciar/' . (int) $fase['id']);
    $acaoTexto = '⚔️ Iniciar Batalha';
} else {
    $acaoHref = url('historia/concluir/' . (int) $fase['id']);
    $acaoTexto = 'Continuar →';
}

// Cenário de fundo e cor de destaque da cena (dão vida ao palco).
$corCena = $mestre['cor_tema'] ?? '#7c5cff';
$fundoCena = ($fase['tipo'] ?? '') === 'chefe_final'
    ? 'fundo-abismo'
    : (($ehCombate && empty($mestre)) ? 'fundo-batalha' : fundoRegiao($mestre['svg_slug'] ?? null));
$fundoCenaUrl = asset('img/fundos/' . $fundoCena . '.png');
?>
<div class="cena-dialogo">
    <div class="palco palco-cena" style="--cor-cena: <?= e($corCena) ?>; background-image: linear-gradient(180deg, rgba(10,12,28,.30), rgba(10,12,28,.72)), url('<?= e($fundoCenaUrl) ?>');">
        <!-- Reserva de atores: JS mostra o do falante atual. -->
        <div class="ator" id="ator">
            <?php if ($slugsUnicos): ?>
                <?php foreach ($slugsUnicos as $i => $slug): ?>
                    <div class="ator-svg" data-slug="<?= e($slug) ?>" style="display:<?= $i === 0 ? 'block' : 'none' ?>">
                        <?= svgSlug($slug) ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="font-size:5rem">📖</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="caixa-fala">
        <div class="falante" id="falante"><?= e($linhas[0]['falante'] ?? $fase['nome']) ?></div>
        <div class="texto-fala" id="textoFala"></div>

        <div class="dialogo-controles">
            <span class="dialogo-dica" id="dica">Clique para continuar ▸</span>
            <div id="acaoFinal" style="display:none">
                <a class="botao" href="<?= e($acaoHref) ?>"><?= e($acaoTexto) ?></a>
            </div>
        </div>
    </div>
</div>

<script>
window.DIALOGO = {
    linhas: <?= json_encode($linhas, JSON_UNESCAPED_UNICODE) ?>,
    semFalas: <?= empty($linhas) ? 'true' : 'false' ?>
};
</script>
<script src="<?= asset('js/dialogo.js') ?>"></script>
