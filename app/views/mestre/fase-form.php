<?php
/** Formulário de criação/edição de fase. */
$ed = $fase !== null;
$v = fn($campo, $padrao = '') => $ed && $fase[$campo] !== null ? $fase[$campo] : $padrao;
$tipos = ['historia' => 'História', 'licao' => 'Lição', 'secundaria' => 'Secundária', 'chefe' => 'Chefe', 'chefe_final' => 'Chefe Final'];
?>
<h1 class="titulo-secao"><?= $ed ? '✏️ Editar' : '＋ Nova' ?> Fase</h1>
<p class="subtitulo"><a href="<?= url('mestre/fases') ?>">← Voltar à lista</a></p>

<form method="post" action="<?= url('mestre/salvarFase') ?>" class="painel">
    <?= csrf_field() ?>
    <?php if ($ed): ?><input type="hidden" name="id" value="<?= (int) $fase['id'] ?>"><?php endif; ?>

    <div class="grid-2">
        <div class="campo"><label>Nome</label><input type="text" name="nome" value="<?= e($v('nome')) ?>" required></div>
        <div class="campo"><label>Ordem global (posição no mapa)</label><input type="number" name="ordem_global" value="<?= (int)$v('ordem_global', 1) ?>"></div>
    </div>

    <div class="grid-2">
        <div class="campo">
            <label>Tipo</label>
            <select name="tipo">
                <?php foreach ($tipos as $val => $rot): ?>
                    <option value="<?= $val ?>" <?= $ed && $fase['tipo'] === $val ? 'selected' : '' ?>><?= e($rot) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo">
            <label>Mestre/Região</label>
            <select name="mestre_id">
                <option value="">— Sem mestre (intro/final) —</option>
                <?php foreach ($mestres as $m): ?>
                    <option value="<?= (int)$m['id'] ?>" <?= $ed && (int)$fase['mestre_id'] === (int)$m['id'] ? 'selected' : '' ?>><?= e($m['regiao']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="campo"><label>Descrição</label><textarea name="descricao"><?= e($v('descricao')) ?></textarea></div>

    <div class="grid-2">
        <div class="campo"><label>Nome do inimigo</label><input type="text" name="inimigo_nome" value="<?= e($v('inimigo_nome')) ?>"></div>
        <div class="campo"><label>SVG do inimigo (slug)</label><input type="text" name="inimigo_svg" value="<?= e($v('inimigo_svg')) ?>" placeholder="ex.: inimigo-bug"></div>
    </div>
    <div class="grid-2">
        <div class="campo"><label>HP do inimigo</label><input type="number" name="inimigo_hp" value="<?= (int)$v('inimigo_hp', 60) ?>"></div>
        <div class="campo"><label>Ataque do inimigo</label><input type="number" name="inimigo_ataque" value="<?= (int)$v('inimigo_ataque', 10) ?>"></div>
    </div>
    <div class="grid-2">
        <div class="campo"><label>XP de recompensa</label><input type="number" name="xp_recompensa" value="<?= (int)$v('xp_recompensa', 50) ?>"></div>
        <div class="campo"><label>Ouro de recompensa</label><input type="number" name="ouro_recompensa" value="<?= (int)$v('ouro_recompensa', 20) ?>"></div>
    </div>
    <div class="grid-2">
        <div class="campo">
            <label>Item dropado (opcional)</label>
            <select name="item_drop_id">
                <option value="">— Nenhum —</option>
                <?php foreach ($itens as $it): ?>
                    <option value="<?= (int)$it['id'] ?>" <?= $ed && (int)$fase['item_drop_id'] === (int)$it['id'] ? 'selected' : '' ?>><?= e($it['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo">
            <label>Fase pré-requisito (libera esta)</label>
            <select name="requisito_fase_id">
                <option value="">— Nenhuma (sempre liberada) —</option>
                <?php foreach ($fases as $f2): ?>
                    <?php if ($ed && (int)$f2['id'] === (int)$fase['id']) continue; ?>
                    <option value="<?= (int)$f2['id'] ?>" <?= $ed && (int)$fase['requisito_fase_id'] === (int)$f2['id'] ? 'selected' : '' ?>>
                        <?= (int)$f2['ordem_global'] ?>. <?= e($f2['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <button class="botao" type="submit">💾 Salvar Fase</button>
</form>
