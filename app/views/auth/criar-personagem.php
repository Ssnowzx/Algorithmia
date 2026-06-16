<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crie seu herói | <?= NOME_JOGO ?></title>
    <link rel="stylesheet" href="<?= assetV('css/style.css') ?>">
</head>
<body>
<div class="conteudo conteudo-criar-heroi">
    <div class="auth-logo" style="margin-top:1rem">
        <?= marcaHtml('auth') ?>
        <h1>Forje o seu Herói</h1>
        <p>Escolha um nome e uma classe. Sim, você vai se arrepender da escolha em algum momento. Faz parte.</p>
    </div>

    <?php if (!empty($erros)): ?>
        <div class="lista-erros">
            <?php foreach ($erros as $msg): ?><div><?= e($msg) ?></div><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= url('auth/criarPersonagem') ?>">
        <?= csrf_field() ?>
        <div class="painel painel-selecao-classes">
            <div class="campo campo-nome-heroi">
                <label for="nome">Nome do herói</label>
                <input type="text" id="nome" name="nome" required autofocus placeholder="Ex.: Ada, Linus, Grace...">
            </div>

            <label class="titulo-escolha-classe">Escolha sua classe</label>
            <div class="classes-grid">
                <?php $primeiro = true; foreach ($classes as $chave => $c): ?>
                    <label class="classe-card">
                        <input type="radio" name="classe" value="<?= e($chave) ?>" <?= $primeiro ? 'checked' : '' ?>>
                        <div class="corpo" style="--classe-cor: <?= e($c['cor']) ?>">
                            <div class="classe-retrato-wrap">
                                <?= svg(retratoHud($chave), 'classe-retrato') ?>
                            </div>
                            <div class="classe-info">
                                <div class="classe-texto">
                                    <h3><?= e($c['nome']) ?></h3>
                                    <?php if (!empty($c['especie'])): ?>
                                        <div class="classe-especie"><?= e($c['especie']) ?></div>
                                    <?php endif; ?>
                                    <div class="desc"><?= e($c['descricao']) ?></div>
                                    <div class="stats">
                                        <span class="s-hp">❤ <?= $c['hp'] ?></span>
                                        <span class="s-mp">✦ <?= $c['mp'] ?></span>
                                        <span class="s-atk">⚔ <?= $c['ataque'] ?></span>
                                    </div>
                                    <?php if (!empty($c['lore'])): ?>
                                        <button type="button" class="classe-lore-btn" data-lore-classe="<?= e($chave) ?>">📖 Origem do povo ▸</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </label>
                <?php $primeiro = false; endforeach; ?>
            </div>

            <button type="submit" class="botao-sofrimento" aria-label="Que comece o sofrimento">
                <img src="<?= e(srcImagem('ui/botao-que-comece-sofrimento')) ?>" alt="Que comece o sofrimento">
            </button>
        </div>
    </form>
</div>

<?php
// Dados de lore para o popup (uma carta por classe; reaproveita o retrato ilustrado).
$loreData = [];
foreach ($classes as $chave => $c) {
    $loreData[$chave] = [
        'nome'    => $c['nome'],
        'especie' => $c['especie'] ?? '',
        'lore'    => $c['lore'] ?? '',
        'cor'     => $c['cor'] ?? '#7c5cff',
        'img'     => srcImagem(retratoHud($chave)) ?? '',
    ];
}
?>
<div id="loreModal" class="lore-modal-overlay" aria-hidden="true">
    <div class="lore-modal-card" role="dialog" aria-modal="true" aria-labelledby="loreModalNome">
        <button type="button" class="lore-modal-fechar" aria-label="Fechar">✕</button>
        <div class="lore-modal-retrato"><img id="loreModalImg" src="" alt="" decoding="async"></div>
        <div class="lore-modal-corpo">
            <h3 id="loreModalNome"></h3>
            <span id="loreModalEspecie" class="classe-especie"></span>
            <p id="loreModalTexto"></p>
        </div>
    </div>
</div>
<script>
window.LORE_CLASSES = <?= json_encode($loreData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
(function () {
    var modal = document.getElementById('loreModal');
    if (!modal) { return; }
    var card    = modal.querySelector('.lore-modal-card');
    var img     = document.getElementById('loreModalImg');
    var nome    = document.getElementById('loreModalNome');
    var especie = document.getElementById('loreModalEspecie');
    var texto   = document.getElementById('loreModalTexto');
    var dados   = window.LORE_CLASSES || {};

    function abrir(chave) {
        var d = dados[chave];
        if (!d) { return; }
        card.style.setProperty('--classe-cor', d.cor || '#7c5cff');
        if (d.img) { img.src = d.img; img.alt = d.nome; img.style.display = ''; }
        else { img.removeAttribute('src'); img.style.display = 'none'; }
        nome.textContent = d.nome;
        especie.textContent = d.especie;
        especie.style.display = d.especie ? '' : 'none';
        texto.textContent = d.lore;
        modal.classList.add('aberto');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
    function fechar() {
        modal.classList.remove('aberto');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.classe-lore-btn').forEach(function (b) {
        b.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            abrir(b.getAttribute('data-lore-classe'));
        });
    });
    modal.addEventListener('click', function (e) { if (e.target === modal) { fechar(); } });
    modal.querySelector('.lore-modal-fechar').addEventListener('click', fechar);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('aberto')) { fechar(); }
    });
})();
</script>
<script src="<?= assetV('js/app.js') ?>"></script>
</body>
</html>
