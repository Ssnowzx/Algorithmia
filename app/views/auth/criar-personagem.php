<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crie seu herói | <?= NOME_JOGO ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
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

            <label style="font-weight:600;color:var(--texto-fraco)">Escolha sua classe</label>
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
                                        <details class="classe-lore">
                                            <summary>📖 Origem do povo</summary>
                                            <p><?= e($c['lore']) ?></p>
                                        </details>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </label>
                <?php $primeiro = false; endforeach; ?>
            </div>

            <button type="submit" class="botao-sofrimento" aria-label="Que comece o sofrimento">
                <img src="<?= asset('img/ui/botao-que-comece-sofrimento.png') ?>" alt="Que comece o sofrimento">
            </button>
        </div>
    </form>
</div>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
