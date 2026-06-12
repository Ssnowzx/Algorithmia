<?php
/** Formulário de criação/edição de item. */
$ed = $item !== null;
$efeito = $ed && $item['efeito'] ? json_decode($item['efeito'], true) : [];
$tipos = ['arma' => 'Arma', 'escudo' => 'Escudo', 'acessorio' => 'Acessório', 'pocao' => 'Poção', 'especial' => 'Especial'];
$raridades = ['comum' => 'Comum', 'raro' => 'Raro', 'epico' => 'Épico', 'lendario' => 'Lendário'];
?>
<h1 class="titulo-secao"><?= $ed ? '✏️ Editar' : '＋ Novo' ?> Item</h1>
<p class="subtitulo"><a href="<?= url('mestre/itens') ?>">← Voltar à lista</a></p>

<form method="post" action="<?= url('mestre/salvarItem') ?>" class="painel">
    <?= csrf_field() ?>
    <?php if ($ed): ?><input type="hidden" name="id" value="<?= (int) $item['id'] ?>"><?php endif; ?>

    <div class="grid-2">
        <div class="campo"><label>Nome</label><input type="text" name="nome" value="<?= $ed ? e($item['nome']) : '' ?>" required></div>
        <div class="campo"><label>SVG (slug)</label><input type="text" name="svg_slug" value="<?= $ed ? e($item['svg_slug']) : 'item-generico' ?>"></div>
    </div>

    <div class="campo"><label>Descrição</label><textarea name="descricao"><?= $ed ? e($item['descricao']) : '' ?></textarea></div>

    <div class="grid-2">
        <div class="campo">
            <label>Tipo</label>
            <select name="tipo">
                <?php foreach ($tipos as $val => $rot): ?>
                    <option value="<?= $val ?>" <?= $ed && $item['tipo'] === $val ? 'selected' : '' ?>><?= e($rot) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo">
            <label>Raridade</label>
            <select name="raridade">
                <?php foreach ($raridades as $val => $rot): ?>
                    <option value="<?= $val ?>" <?= $ed && $item['raridade'] === $val ? 'selected' : '' ?>><?= e($rot) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <h3>Efeitos (deixe 0 quando não se aplica)</h3>
    <div class="grid-2">
        <div class="campo"><label>⚔ Ataque</label><input type="number" name="ef_ataque" value="<?= (int)($efeito['ataque'] ?? 0) ?>"></div>
        <div class="campo"><label>🛡 Defesa</label><input type="number" name="ef_defesa" value="<?= (int)($efeito['defesa'] ?? 0) ?>"></div>
        <div class="campo"><label>❤ Cura HP</label><input type="number" name="ef_cura_hp" value="<?= (int)($efeito['cura_hp'] ?? 0) ?>"></div>
        <div class="campo"><label>✦ Cura MP</label><input type="number" name="ef_cura_mp" value="<?= (int)($efeito['cura_mp'] ?? 0) ?>"></div>
    </div>

    <div class="grid-2">
        <div class="campo"><label>Preço (ouro)</label><input type="number" name="preco" value="<?= $ed ? (int)$item['preco'] : 0 ?>"></div>
        <div class="campo" style="display:flex;align-items:center;gap:.5rem;margin-top:1.6rem">
            <input type="checkbox" name="compravel" id="compravel" style="width:auto" <?= !$ed || (int)$item['compravel'] === 1 ? 'checked' : '' ?>>
            <label for="compravel" style="margin:0">Disponível na loja</label>
        </div>
    </div>

    <button class="botao" type="submit">💾 Salvar Item</button>
</form>
