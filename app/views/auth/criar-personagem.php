<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crie seu herói | <?= NOME_JOGO ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<div class="conteudo" style="max-width:760px">
    <div class="auth-logo" style="margin-top:1rem">
        <?= svg('ui/logo') ?>
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
        <div class="painel">
            <div class="campo">
                <label for="nome">Nome do herói</label>
                <input type="text" id="nome" name="nome" required autofocus placeholder="Ex.: Ada, Linus, Grace...">
            </div>

            <label style="font-weight:600;color:var(--texto-fraco)">Escolha sua classe</label>
            <div class="classes-grid">
                <?php $primeiro = true; foreach ($classes as $chave => $c): ?>
                    <label class="classe-card">
                        <input type="radio" name="classe" value="<?= e($chave) ?>" <?= $primeiro ? 'checked' : '' ?>>
                        <div class="corpo">
                            <?= svg('herois/' . $c['svg']) ?>
                            <h3><?= e($c['nome']) ?></h3>
                            <div class="desc"><?= e($c['descricao']) ?></div>
                            <div class="stats">
                                <span class="s-hp">❤ <?= $c['hp'] ?></span>
                                <span class="s-mp">✦ <?= $c['mp'] ?></span>
                                <span class="s-atk">⚔ <?= $c['ataque'] ?></span>
                            </div>
                        </div>
                    </label>
                <?php $primeiro = false; endforeach; ?>
            </div>

            <button type="submit" class="botao" style="width:100%;margin-top:1rem">Que comece o sofrimento ⚔</button>
        </div>
    </form>
</div>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
