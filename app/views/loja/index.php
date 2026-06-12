<?php
/** Loja: compra e venda de itens. */
$possui = [];
foreach ($inventario as $i) { $possui[(int) $i['item_id']] = (int) $i['quantidade']; }
?>
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem">
    <h1 class="titulo-secao" style="margin:0">🏪 Loja do Reino</h1>
    <div class="recurso ouro" style="font-size:1.2rem;font-weight:800;color:var(--ouro)">
        <?= svg('ui/icone-ouro', 'ico') ?> <?= (int) $heroi['ouro'] ?> de ouro
    </div>
</div>
<p class="subtitulo">Equipamentos e poções para a jornada. A venda devolve metade do valor.</p>

<div class="grade-itens">
    <?php foreach ($itens as $it): ?>
        <?php
            $efeito = $it['efeito'] ? json_decode($it['efeito'], true) : [];
            $temOuro = (int) $heroi['ouro'] >= (int) $it['preco'];
        ?>
        <div class="item-card">
            <div class="icone-item"><?= svgSlug($it['svg_slug']) ?></div>
            <div style="flex:1">
                <h4><?= e($it['nome']) ?></h4>
                <span class="raridade raridade-<?= e($it['raridade']) ?>"><?= e($it['raridade']) ?></span>
                <p class="desc-item"><?= e($it['descricao']) ?></p>
                <?php if (!empty($efeito['ataque'])): ?><span style="color:var(--xp);font-size:.8rem">⚔ +<?= (int)$efeito['ataque'] ?></span> <?php endif; ?>
                <?php if (!empty($efeito['defesa'])): ?><span style="color:var(--mp);font-size:.8rem">🛡 +<?= (int)$efeito['defesa'] ?></span> <?php endif; ?>
                <?php if (!empty($efeito['cura_hp'])): ?><span style="color:var(--hp);font-size:.8rem">❤ +<?= (int)$efeito['cura_hp'] ?></span> <?php endif; ?>
                <?php if (!empty($efeito['cura_mp'])): ?><span style="color:var(--mp);font-size:.8rem">✦ +<?= (int)$efeito['cura_mp'] ?></span> <?php endif; ?>

                <div style="display:flex;align-items:center;gap:.5rem;margin-top:.6rem;flex-wrap:wrap">
                    <strong style="color:var(--ouro)"><?= (int) $it['preco'] ?> ⛃</strong>
                    <form method="post" action="<?= url('loja/comprar/' . (int)$it['id']) ?>"><?= csrf_field() ?>
                        <button class="botao botao-sm" <?= $temOuro ? '' : 'disabled' ?>>Comprar</button></form>
                    <?php if (!empty($possui[(int)$it['id']])): ?>
                        <form method="post" action="<?= url('loja/vender/' . (int)$it['id']) ?>"><?= csrf_field() ?>
                            <button class="botao botao-sm botao-fantasma">Vender (<?= $possui[(int)$it['id']] ?>)</button></form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
