<?php
/** Arena de batalha. O JS controla os turnos e renderiza os desafios. */
$heroi = Auth::personagem();
$heroiSvg = CLASSES[$heroi['classe']]['svg'] ?? 'heroi-ranger';
?>
<div class="arena">
    <div class="campo-batalha" id="campo">
        <div class="combo-indicador" id="comboInd"></div>

        <div class="combatente inimigo" id="ladoInimigo">
            <div class="sprite" id="spriteInimigo"><?= svgSlug($estado['inimigo_svg']) ?></div>
            <div class="nome-combatente"><?= e($estado['inimigo_nome']) ?></div>
            <div class="barra barra-hp"><div class="barra-fill" id="hpInimigoFill" style="width:100%"></div>
                <span class="barra-label" id="hpInimigoLabel"><?= (int) $estado['inimigo_hp'] ?> / <?= (int) $estado['inimigo_hp_max'] ?></span></div>
        </div>

        <div class="combatente heroi" id="ladoHeroi">
            <div class="sprite" id="spriteHeroi"><?= svg('herois/' . $heroiSvg) ?></div>
            <div class="nome-combatente"><?= e($heroi['nome']) ?></div>
            <div class="barra barra-hp"><div class="barra-fill" id="hpHeroiFill" style="width:100%"></div>
                <span class="barra-label" id="hpHeroiLabel"><?= (int) $estado['heroi_hp'] ?> / <?= (int) $estado['heroi_hp_max'] ?></span></div>
            <div class="barra barra-mp" style="margin-top:3px"><div class="barra-fill" id="mpHeroiFill" style="width:100%"></div>
                <span class="barra-label" id="mpHeroiLabel"><?= (int) $estado['heroi_mp'] ?> / <?= (int) $estado['heroi_mp_max'] ?></span></div>
        </div>
    </div>

    <div class="painel-desafio" id="painelDesafio">
        <!-- preenchido pelo JS -->
    </div>

    <div class="barra-acoes-secundarias" id="acoesSecundarias">
        <button class="botao botao-sm" id="btnEspecial" title="Gasta mana e dobra o dano do próximo acerto">✦ Especial</button>
        <?php foreach ($itensUsaveis as $item): ?>
            <?php $ehFragmento = $item['svg_slug'] === 'item-fragmento-ia'; ?>
            <button class="botao botao-sm <?= $ehFragmento ? 'btn-ia' : 'botao-fantasma' ?>"
                    data-acao="<?= $ehFragmento ? 'fragmento' : 'pocao' ?>"
                    data-item="<?= (int) $item['item_id'] ?>">
                <?= $ehFragmento ? '🤖' : '🧪' ?> <?= e($item['nome']) ?> (<?= (int) $item['quantidade'] ?>)
            </button>
        <?php endforeach; ?>
        <button class="botao botao-sm botao-fantasma" id="btnFugir" style="margin-left:auto">🏃 Fugir</button>
    </div>
</div>

<!-- Tela de resultado (oculta até o fim) -->
<div id="telaResultado" style="display:none"></div>

<script>
window.BATALHA = {
    estado: <?= json_encode($estado, JSON_UNESCAPED_UNICODE) ?>,
    urls: {
        responder:  <?= json_encode(url('batalha/responder')) ?>,
        especial:   <?= json_encode(url('batalha/especial')) ?>,
        pocao:      <?= json_encode(url('batalha/pocao')) ?>,
        fragmento:  <?= json_encode(url('batalha/fragmento')) ?>,
        fugir:      <?= json_encode(url('batalha/fugir')) ?>,
        mapa:       <?= json_encode(url('mapa')) ?>,
        reiniciar:  <?= json_encode(url('batalha/iniciar/' . (int) $fase['id'])) ?>
    }
};
</script>
<script src="<?= asset('js/batalha.js') ?>"></script>
