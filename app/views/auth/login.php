<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar | <?= NOME_JOGO ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400;500;600;700&family=Rubik:wght@400;500;700;800&display=swap">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<?php require __DIR__ . '/../layout/splash.php'; ?>
<div class="tela-auth">
    <div class="cartao-auth">
        <div class="auth-logo">
            <?= svg('ui/logo') ?>
            <h1><?= NOME_JOGO ?></h1>
            <p><?= SUBTITULO_JOGO ?></p>
        </div>
        <div class="painel">
            <?php $flashLogin = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); ?>
            <?php if ($flashLogin): ?>
                <div class="flash flash-<?= e($flashLogin['tipo']) ?>" style="margin:0 0 1rem"><?= e($flashLogin['mensagem']) ?></div>
            <?php endif; ?>
            <?php if (!empty($erro)): ?>
                <div class="lista-erros"><?= e($erro) ?></div>
            <?php endif; ?>
            <form method="post" action="<?= url('auth/login') ?>">
                <?= csrf_field() ?>
                <div class="campo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required autofocus>
                </div>
                <div class="campo">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                <button type="submit" class="botao" style="width:100%">Entrar no reino</button>
            </form>
            <p class="auth-troca">Ainda não é um aprendiz? <a href="<?= url('auth/registro') ?>">Crie sua conta</a></p>
        </div>
    </div>
</div>
</body>
</html>
