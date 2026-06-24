<?php
/** Loja: compra e venda de itens, em formato de cartas colecionáveis. */
$possui = [];
foreach ($inventario as $i) { $possui[(int) $i['item_id']] = (int) $i['quantidade']; }
?>
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem">
    <h1 class="titulo-secao" style="margin:0">🏪 Loja do Reino</h1>
    <div style="display:flex;gap:.9rem;align-items:center;flex-wrap:wrap">
        <div class="recurso ouro" style="font-size:1.2rem;font-weight:800;color:var(--ouro)">
            <?= svg('ui/icones/icone-ouro', 'ico') ?> <?= (int) $heroi['ouro'] ?> de ouro
        </div>
        <div class="recurso" style="font-size:1.05rem;font-weight:800;color:var(--primaria-2)"
             title="Ataque + Defesa totais. Veja-o subir ao equipar.">💪 Poder <?= poderTotal($atributos) ?></div>
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
                <?php
                    // Comparação ciente de slot: equipar substitui o item do mesmo
                    // tipo, então o ganho real é a diferença para o que já está posto.
                    if (in_array($it['tipo'], ['arma', 'escudo', 'acessorio'], true)):
                        $atualSlot = $equipadoPorTipo[$it['tipo']] ?? [];
                        $dAtk = (int) ($efeito['ataque'] ?? 0) - (int) ($atualSlot['ataque'] ?? 0);
                        $dDef = (int) ($efeito['defesa'] ?? 0) - (int) ($atualSlot['defesa'] ?? 0);
                        if ($dAtk !== 0 || $dDef !== 0):
                            $atkAtual = (int) $atributos['ataque']; $atkNovo = $atkAtual + $dAtk;
                            $defAtual = (int) $atributos['defesa']; $defNovo = $defAtual + $dDef;
                            $poderAtual = poderTotal($atributos);
                            $poderNovo  = poderTotal(['ataque' => $atkNovo, 'defesa' => $defNovo]);
                            $dPoder = $poderNovo - $poderAtual;
                            $jaEquipado = $atualSlot !== [];
                ?>
                <div class="carta-loja__comparacao">
                    <?php if ($dAtk !== 0): ?>
                        <div class="cmp-linha">⚔ Ataque <span class="cmp-vals"><?= $atkAtual ?> → <strong><?= $atkNovo ?></strong></span>
                            <span class="cmp-delta <?= $dAtk > 0 ? 'pos' : 'neg' ?>"><?= ($dAtk > 0 ? '+' : '') . $dAtk ?><?= $atkAtual > 0 ? ' (' . ($dAtk > 0 ? '+' : '') . round($dAtk / $atkAtual * 100) . '%)' : '' ?></span></div>
                    <?php endif; ?>
                    <?php if ($dDef !== 0): ?>
                        <div class="cmp-linha">🛡 Defesa <span class="cmp-vals"><?= $defAtual ?> → <strong><?= $defNovo ?></strong></span>
                            <span class="cmp-delta <?= $dDef > 0 ? 'pos' : 'neg' ?>"><?= ($dDef > 0 ? '+' : '') . $dDef ?><?= $defAtual > 0 ? ' (' . ($dDef > 0 ? '+' : '') . round($dDef / $defAtual * 100) . '%)' : '' ?></span></div>
                    <?php endif; ?>
                    <div class="cmp-poder">💪 Poder <?= $poderAtual ?> → <strong><?= $poderNovo ?></strong>
                        <span class="cmp-delta <?= $dPoder >= 0 ? 'pos' : 'neg' ?>">(<?= ($dPoder > 0 ? '+' : '') . $dPoder ?>)</span></div>
                    <?php if ($jaEquipado): ?><div class="cmp-nota">vs. o que está equipado</div><?php endif; ?>
                </div>
                <?php endif; endif; ?>
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
