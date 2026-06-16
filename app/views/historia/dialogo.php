<?php
/** Cena de diálogo com efeito máquina de escrever. */
$linhas = [];
$slugsUnicos = [];
foreach ($dialogos as $d) {
    $slug = slugAtorDialogo($d['svg_slug'] ?: slugAtorPorFalante($d['falante']), $fase, $mestre ?? null);
    $linhas[] = ['falante' => $d['falante'], 'texto' => $d['texto'], 'slug' => $slug];
    if ($slug && !in_array($slug, $slugsUnicos, true)) {
        $slugsUnicos[] = $slug;
    }
}
$slugInicial = $linhas[0]['slug'] ?? '';
$personagemFase = personagemFase($fase, $mestre ?? null);

if (empty($slugsUnicos) && $personagemFase['slug'] !== '') {
    $slugsUnicos = [$personagemFase['slug']];
    $slugInicial = $personagemFase['slug'];
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
$fundoCenaUrl = srcImagem('fundos/' . $fundoCena) ?? asset('img/fundos/' . $fundoCena . '.png');
$styleFundo = 'background-image: linear-gradient(180deg, rgba(10,12,28,.28), rgba(10,12,28,.68)), url(' . e($fundoCenaUrl) . ')';
?>
<div class="cena-dialogo" style="--cor-cena: <?= e($corCena) ?>">
    <div class="palco palco-cena" style="<?= $styleFundo ?>">
        <!-- Reserva de atores: JS mostra o do falante atual. -->
        <div class="ator" id="ator">
            <?php if ($slugsUnicos): ?>
                <?php foreach ($slugsUnicos as $slug): ?>
                    <div class="ator-svg" data-slug="<?= e($slug) ?>" style="display:<?= $slug === $slugInicial ? 'block' : 'none' ?>">
                        <?= svgAtor($slug) ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="ator-svg" data-slug="<?= e($personagemFase['slug']) ?>" style="display:block">
                    <?= svgAtor($personagemFase['slug']) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="caixa-fala">
        <div class="falante" id="falante"><?= e($linhas[0]['falante'] ?? $personagemFase['nome']) ?></div>
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
    semFalas: <?= empty($linhas) ? 'true' : 'false' ?>,
    falanteFallback: <?= json_encode($personagemFase['nome'], JSON_UNESCAPED_UNICODE) ?>,
    textoFallback: <?= json_encode(empty($linhas) ? ($fase['descricao'] ?? '') : '', JSON_UNESCAPED_UNICODE) ?>
};
</script>
<script src="<?= assetV('js/dialogo.js') ?>"></script>
