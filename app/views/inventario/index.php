<?php
/** Inventário do herói. Cards no mesmo idioma visual da Loja (.carta-loja). */
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
    <h2 class="titulo-secao titulo-grupo-inv"><?= $rotulo ?></h2>
    <div class="grade-inventario">
        <?php foreach ($grupos[$tipo] as $it): ?>
            <?php
                $efeito = $it['efeito'] ? json_decode($it['efeito'], true) : [];
                $equipado = (int) $it['equipado'] === 1;
                $qtd = (int) $it['quantidade'];
                $podeEquipar = in_array($it['tipo'], ['arma', 'escudo', 'acessorio'], true);
                $podeUsar = $it['tipo'] === 'pocao';
                $podeDescartar = $it['svg_slug'] !== 'item-fragmento-ia';
                $temAcao = $podeEquipar || $podeUsar || $podeDescartar;
            ?>
            <div class="carta-loja item-rar-<?= e($it['raridade']) ?><?= $equipado ? ' carta-equipada' : '' ?>">
                <div class="carta-loja__arte">
                    <?= svgSlug($it['svg_slug'], 'carta-loja__img') ?>
                    <span class="carta-loja__raridade raridade raridade-<?= e($it['raridade']) ?>"><?= e($it['raridade']) ?></span>
                    <?php if ($equipado): ?><span class="carta-loja__equipado">● Equipado</span><?php endif; ?>
                    <?php if ($qtd > 1): ?><span class="carta-loja__qtd">×<?= $qtd ?></span><?php endif; ?>
                </div>
                <div class="carta-loja__corpo">
                    <h4 class="carta-loja__nome"><?= e($it['nome']) ?></h4>
                    <p class="desc-item"><?= e($it['descricao']) ?></p>
                    <?php if (!empty($efeito['ataque']) || !empty($efeito['defesa'])): ?>
                        <div class="carta-loja__efeitos">
                            <?php if (!empty($efeito['ataque'])): ?><span style="color:var(--xp)">⚔ +<?= (int) $efeito['ataque'] ?></span><?php endif; ?>
                            <?php if (!empty($efeito['defesa'])): ?><span style="color:var(--mp)">🛡 +<?= (int) $efeito['defesa'] ?></span><?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($temAcao): ?>
                    <div class="carta-loja__rodape">
                        <div class="carta-loja__acoes">
                            <?php if ($podeEquipar): ?>
                                <?php if ($equipado): ?>
                                    <form method="post" action="<?= url('inventario/desequipar/' . (int) $it['item_id']) ?>"><?= csrf_field() ?>
                                        <button class="botao botao-sm botao-fantasma">Desequipar</button></form>
                                <?php else: ?>
                                    <form method="post" action="<?= url('inventario/equipar/' . (int) $it['item_id']) ?>"><?= csrf_field() ?>
                                        <button class="botao botao-sm">Equipar</button></form>
                                <?php endif; ?>
                            <?php elseif ($podeUsar): ?>
                                <form method="post" action="<?= url('inventario/usar/' . (int) $it['item_id']) ?>"><?= csrf_field() ?>
                                    <button class="botao botao-sm botao-ouro">Usar</button></form>
                            <?php endif; ?>
                            <?php if ($podeDescartar): ?>
                                <form method="post" action="<?= url('inventario/descartar/' . (int) $it['item_id']) ?>" data-confirmar="Descartar este item?"><?= csrf_field() ?>
                                    <button class="botao botao-sm botao-fantasma">Descartar</button></form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
