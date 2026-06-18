<?php
/** Loja: compra e venda de itens, em formato de cartas colecionáveis. */
$possui = [];
foreach ($inventario as $i) { $possui[(int) $i['item_id']] = (int) $i['quantidade']; }
?>
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem">
    <h1 class="titulo-secao" style="margin:0">🏪 Loja do Reino</h1>
    <div class="recurso ouro" style="font-size:1.2rem;font-weight:800;color:var(--ouro)">
        <?= svg('ui/icones/icone-ouro', 'ico') ?> <?= (int) $heroi['ouro'] ?> de ouro
    </div>
</div>
<p class="subtitulo">Cartas de equipamento e poção para a jornada. A venda devolve metade do valor.</p>

<div class="grade-cartas-loja">
    <?php foreach ($itens as $it): ?>
        <?php
            $efeito = $it['efeito'] ? json_decode($it['efeito'], true) : [];
            $temOuro = (int) $heroi['ouro'] >= (int) $it['preco'];
        ?>
        <div class="carta-loja item-rar-<?= e($it['raridade']) ?>">
            <div class="carta-loja__arte">
                <?= svgSlug($it['svg_slug'], 'carta-loja__img') ?>
                <span class="carta-loja__raridade raridade raridade-<?= e($it['raridade']) ?>"><?= e($it['raridade']) ?></span>
            </div>
            <div class="carta-loja__corpo">
                <h4 class="carta-loja__nome"><?= e($it['nome']) ?></h4>
                <p class="desc-item"><?= e($it['descricao']) ?></p>
                <div class="carta-loja__efeitos">
                    <?php if (!empty($efeito['ataque'])): ?><span style="color:var(--xp)">⚔ +<?= (int)$efeito['ataque'] ?></span><?php endif; ?>
                    <?php if (!empty($efeito['defesa'])): ?><span style="color:var(--mp)">🛡 +<?= (int)$efeito['defesa'] ?></span><?php endif; ?>
                    <?php if (!empty($efeito['cura_hp'])): ?><span style="color:var(--hp)">❤ +<?= (int)$efeito['cura_hp'] ?></span><?php endif; ?>
                    <?php if (!empty($efeito['cura_mp'])): ?><span style="color:var(--mp)">✦ +<?= (int)$efeito['cura_mp'] ?></span><?php endif; ?>
                </div>
                <div class="carta-loja__rodape">
                    <strong class="carta-loja__preco"><?= (int) $it['preco'] ?> ⛃</strong>
                    <div class="carta-loja__acoes">
                        <form method="post" action="<?= url('loja/comprar/' . (int)$it['id']) ?>"><?= csrf_field() ?>
                            <button class="botao botao-sm" <?= $temOuro ? '' : 'disabled' ?>>Comprar</button></form>
                        <?php if (!empty($possui[(int)$it['id']])): ?>
                            <form method="post" action="<?= url('loja/vender/' . (int)$it['id']) ?>"><?= csrf_field() ?>
                                <button class="botao botao-sm botao-fantasma">Vender (<?= $possui[(int)$it['id']] ?>)</button></form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
