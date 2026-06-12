<?php
/** Cabeçalho global: HUD do herói + navegação. */
$heroi = Auth::personagem();
$ehMestre = Auth::ehMestre();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? NOME_JOGO) ?> | <?= NOME_JOGO ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400;500;600;700&family=Rubik:wght@400;500;700;800&display=swap">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/mapa.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/batalha.css') ?>">
</head>
<body class="<?= e($bodyClass ?? '') ?>">
<?php require __DIR__ . '/splash.php'; ?>
<header class="topo">
    <a class="marca" href="<?= url('mapa') ?>">
        <span class="marca-icone"><?= svg('ui/logo', 'logo-svg') ?></span>
        <span class="marca-texto"><?= NOME_JOGO ?></span>
    </a>

    <?php if ($heroi): ?>
    <div class="hud">
        <div class="hud-avatar"><?= svg('herois/' . (CLASSES[$heroi['classe']]['svg'] ?? 'heroi-ranger'), 'hud-heroi-svg') ?></div>
        <div class="hud-info">
            <div class="hud-nome">
                <strong><?= e($heroi['nome']) ?></strong>
                <span class="hud-classe"><?= e(CLASSES[$heroi['classe']]['nome'] ?? '') ?> · Nv <?= (int) $heroi['nivel'] ?></span>
            </div>
            <div class="hud-barras">
                <?= barra((int) $heroi['hp_atual'], (int) $heroi['hp_max'], 'hp') ?>
                <?= barra((int) $heroi['mp_atual'], (int) $heroi['mp_max'], 'mp') ?>
                <?php
                    $xpAtualNivel = xpParaNivel((int) $heroi['nivel']);
                    $xpProx = xpParaNivel((int) $heroi['nivel'] + 1);
                    echo barra((int) $heroi['xp'] - $xpAtualNivel, max(1, $xpProx - $xpAtualNivel), 'xp');
                ?>
            </div>
        </div>
        <div class="hud-recursos">
            <span class="recurso ouro"><?= svg('ui/icone-ouro', 'ico') ?> <?= (int) $heroi['ouro'] ?></span>
            <span class="recurso rep" title="Reputação: <?= rotuloReputacao((int) $heroi['reputacao']) ?>">
                <?= (int) $heroi['reputacao'] >= 0 ? '⚖️' : '🤖' ?> <?= (int) $heroi['reputacao'] ?>
            </span>
        </div>
    </div>
    <?php endif; ?>

    <nav class="navegacao">
        <?php if ($heroi): ?>
            <a href="<?= url('mapa') ?>">Mapa</a>
            <a href="<?= url('inventario') ?>">Inventário</a>
            <a href="<?= url('loja') ?>">Loja</a>
            <a href="<?= url('perfil') ?>">Perfil</a>
            <a href="<?= url('ranking') ?>">Ranking</a>
        <?php endif; ?>
        <?php if ($ehMestre): ?>
            <a href="<?= url('mestre') ?>" class="link-mestre">⚙ Painel</a>
        <?php endif; ?>
        <?php if (Auth::logado()): ?>
            <a href="<?= url('auth/logout') ?>" class="link-sair">Sair</a>
        <?php endif; ?>
    </nav>
</header>

<?php if ($flash): ?>
<div class="flash flash-<?= e($flash['tipo']) ?>"><?= e($flash['mensagem']) ?></div>
<?php endif; ?>

<main class="conteudo">
