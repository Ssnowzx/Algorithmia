<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Criar conta | <?= NOME_JOGO ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<div class="tela-auth">
    <div class="cartao-auth">
        <div class="auth-logo">
            <?= svg('ui/logo') ?>
            <h1>Torne-se um Aprendiz</h1>
            <p>Sua jornada em <?= NOME_JOGO ?> começa aqui. Não diga que ninguém avisou.</p>
        </div>
        <div class="painel">
            <?php if (!empty($erros)): ?>
                <div class="lista-erros">
                    <?php foreach ($erros as $msg): ?><div><?= e($msg) ?></div><?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="post" action="<?= url('auth/registro') ?>">
                <?= csrf_field() ?>
                <div class="campo">
                    <label for="nome">Seu nome</label>
                    <input type="text" id="nome" name="nome" value="<?= e($dados['nome']) ?>" required autofocus>
                </div>
                <div class="campo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?= e($dados['email']) ?>" required>
                </div>
                <div class="campo">
                    <label for="senha">Senha (mín. 6 caracteres)</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                <div class="campo">
                    <label for="confirma">Confirme a senha</label>
                    <input type="password" id="confirma" name="confirma" required>
                </div>
                <button type="submit" class="botao" style="width:100%">Criar conta</button>
            </form>
            <p class="auth-troca">Já tem conta? <a href="<?= url('auth/login') ?>">Entrar</a></p>
        </div>
    </div>
</div>
</body>
</html>
