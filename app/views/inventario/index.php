<?php
/** Inventário do herói. */
$tipoLabel = ['arma' => 'Armas', 'escudo' => 'Escudos', 'acessorio' => 'Acessórios', 'pocao' => 'Poções', 'especial' => 'Itens Especiais'];
$grupos = [];
foreach ($itens as $it) { $grupos[$it['tipo']][] = $it; }
?>
<h1 class="titulo-secao">🎒 Inventário de <?= e($heroi['nome']) ?></h1>
<p class="subtitulo">Equipe armas e escudos para fortalecer seus golpes nas batalhas.</p>

<?php if (empty($itens)): ?>
    <div class="painel">Seu inventário está vazio. Vença batalhas e visite a <a href="<?= url('loja') ?>">Loja</a>.</div>
<?php endif; ?>

<?php foreach ($tipoLabel as $tipo => $rotulo): ?>
    <?php if (empty($grupos[$tipo])) continue; ?>
    <h2 class="titulo-secao" style="font-size:1.15rem;margin-top:1.4rem"><?= $rotulo ?></h2>
    <div class="grade-itens">
        <?php foreach ($grupos[$tipo] as $it): ?>
            <?php $efeito = $it['efeito'] ? json_decode($it['efeito'], true) : []; ?>
            <div class="item-card">
                <div class="icone-item"><?= svgSlug($it['svg_slug']) ?></div>
                <div style="flex:1">
                    <h4><?= e($it['nome']) ?>
                        <?php if ((int) $it['equipado'] === 1): ?><span class="tag-equipado">● Equipado</span><?php endif; ?>
                    </h4>
                    <span class="raridade raridade-<?= e($it['raridade']) ?>"><?= e($it['raridade']) ?></span>
                    <?php if ((int) $it['quantidade'] > 1): ?><span style="font-size:.78rem;color:var(--texto-fraco)"> ×<?= (int) $it['quantidade'] ?></span><?php endif; ?>
                    <p class="desc-item"><?= e($it['descricao']) ?></p>
                    <?php if (!empty($efeito['ataque'])): ?><span style="color:var(--xp);font-size:.8rem">⚔ +<?= (int)$efeito['ataque'] ?></span> <?php endif; ?>
                    <?php if (!empty($efeito['defesa'])): ?><span style="color:var(--mp);font-size:.8rem">🛡 +<?= (int)$efeito['defesa'] ?></span><?php endif; ?>

                    <div style="display:flex;gap:.4rem;margin-top:.6rem;flex-wrap:wrap">
                        <?php if (in_array($it['tipo'], ['arma','escudo','acessorio'], true)): ?>
                            <?php if ((int) $it['equipado'] === 1): ?>
                                <form method="post" action="<?= url('inventario/desequipar/' . (int)$it['item_id']) ?>"><?= csrf_field() ?>
                                    <button class="botao botao-sm botao-fantasma">Desequipar</button></form>
                            <?php else: ?>
                                <form method="post" action="<?= url('inventario/equipar/' . (int)$it['item_id']) ?>"><?= csrf_field() ?>
                                    <button class="botao botao-sm">Equipar</button></form>
                            <?php endif; ?>
                        <?php elseif ($it['tipo'] === 'pocao'): ?>
                            <form method="post" action="<?= url('inventario/usar/' . (int)$it['item_id']) ?>"><?= csrf_field() ?>
                                <button class="botao botao-sm botao-ouro">Usar</button></form>
                        <?php endif; ?>
                        <?php if ($it['svg_slug'] !== 'item-fragmento-ia'): ?>
                            <form method="post" action="<?= url('inventario/descartar/' . (int)$it['item_id']) ?>" data-confirmar="Descartar este item?"><?= csrf_field() ?>
                                <button class="botao botao-sm botao-fantasma">Descartar</button></form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
