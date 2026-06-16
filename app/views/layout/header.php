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
    <link rel="icon" href="<?= asset('favicon.png') ?>" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400;500;600;700&family=Rubik:wght@400;500;700;800&display=swap">
    <link rel="stylesheet" href="<?= assetV('css/style.css') ?>">
    <link rel="stylesheet" href="<?= assetV('css/cena.css') ?>">
    <link rel="stylesheet" href="<?= assetV('css/mapa.css') ?>">
    <link rel="stylesheet" href="<?= assetV('css/batalha.css') ?>">
</head>
<body class="<?= e($bodyClass ?? '') ?>">
<?php $temMolduraBarra = is_file(__DIR__ . '/../../../public/img/ui/moldura-barra.png'); ?>
<header class="topo topo-jogo<?= $temMolduraBarra ? ' topo-moldurado' : '' ?>">
    <a class="marca" href="<?= url('mapa') ?>" aria-label="<?= NOME_JOGO ?> — ir para o mapa">
        <?= marcaHtml('header') ?>
    </a>

    <?php if ($heroi): ?>
    <?php $cfgClasse = CLASSES[$heroi['classe']] ?? CLASSES['ranger']; ?>
    <div class="hud hud-heroi hud-classe-<?= e($heroi['classe']) ?>"
         style="--hud-cor: <?= e($cfgClasse['cor']) ?>">
        <div class="hud-placa hud-placa-click" id="hudPlaca" role="button" tabindex="0"
             aria-haspopup="dialog" title="Ver ficha do herói">
            <div class="hud-avatar">
                <?= svg(retratoHud($heroi['classe']), 'hud-retrato') ?>
                <span class="hud-nivel-badge">Nv <?= (int) $heroi['nivel'] ?></span>
            </div>
            <div class="hud-info">
                <div class="hud-nome">
                    <strong><?= e($heroi['nome']) ?></strong>
                    <span class="hud-classe"><?= e($cfgClasse['nome']) ?></span>
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
    </div>
    <?php endif; ?>

    <nav class="navegacao topo-nav">
        <span class="som-controle">
            <button type="button" id="btnSom" class="btn-som" aria-label="Ligar ou desligar o som" title="Som">🔊</button>
            <input type="range" id="volSom" class="som-volume" min="0" max="1" step="0.05" value="0.6" aria-label="Volume do som">
        </span>
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

<?php if ($heroi): ?>
<?php
    // Ficha do herói (popup ao clicar no status). Imagem de fundo opcional do GPT.
    $fichaFundo = is_file(__DIR__ . '/../../../public/img/ui/ficha-fundo.png') ? srcImagem('ui/ficha-fundo') : '';
    // Carta do herói no popup: usa herois/card-<classe> se existir; senão a carta hud-*.
    $cardHeroi = is_file(__DIR__ . '/../../../public/img/herois/card-' . $heroi['classe'] . '.png')
        ? 'herois/card-' . $heroi['classe']
        : retratoHud($heroi['classe']);
    $xpNivelAtual = xpParaNivel((int) $heroi['nivel']);
    $xpNivelProx  = xpParaNivel((int) $heroi['nivel'] + 1);
?>
<div id="fichaModal" class="ficha-modal-overlay" aria-hidden="true">
    <div class="ficha-modal-card<?= $fichaFundo ? ' ficha-com-fundo' : '' ?>" role="dialog" aria-modal="true" aria-labelledby="fichaNome"
         style="--hud-cor: <?= e($cfgClasse['cor']) ?><?= $fichaFundo ? '; background-image: linear-gradient(180deg, rgba(8,8,20,.28), rgba(8,8,20,.78) 58%, rgba(10,8,22,.93)), url(\'' . e($fichaFundo) . '\')' : '' ?>">
        <button type="button" class="ficha-modal-fechar" aria-label="Fechar">✕</button>
        <div class="ficha-banner">
            <div class="ficha-retrato"><?= svg($cardHeroi) ?></div>
            <span class="ficha-nivel">Nível <?= (int) $heroi['nivel'] ?></span>
        </div>
        <div class="ficha-corpo">
            <h3 id="fichaNome"><?= e($heroi['nome']) ?></h3>
            <div class="ficha-classe-linha"><?= e($cfgClasse['nome']) ?> · <?= e($cfgClasse['especie'] ?? '') ?></div>
            <div class="ficha-stats">
                <div class="ficha-stat"><span>❤ Vida</span><strong><?= (int) $heroi['hp_atual'] ?> / <?= (int) $heroi['hp_max'] ?></strong></div>
                <div class="ficha-stat"><span>✦ Mana</span><strong><?= (int) $heroi['mp_atual'] ?> / <?= (int) $heroi['mp_max'] ?></strong></div>
                <div class="ficha-stat"><span>★ XP</span><strong><?= max(0, (int) $heroi['xp'] - $xpNivelAtual) ?> / <?= max(1, $xpNivelProx - $xpNivelAtual) ?></strong></div>
                <div class="ficha-stat"><span><?= svg('ui/icone-ouro', 'ico') ?> Ouro</span><strong><?= (int) $heroi['ouro'] ?></strong></div>
                <div class="ficha-stat ficha-stat-larga"><span><?= (int) $heroi['reputacao'] >= 0 ? '⚖️' : '🤖' ?> Reputação</span><strong><?= e(rotuloReputacao((int) $heroi['reputacao'])) ?> (<?= (int) $heroi['reputacao'] ?>)</strong></div>
            </div>
            <a class="botao ficha-perfil-link" href="<?= url('perfil') ?>">Ver perfil completo →</a>
        </div>
    </div>
</div>
<script>
(function () {
    var modal = document.getElementById('fichaModal');
    var abre = document.getElementById('hudPlaca');
    if (!modal || !abre) { return; }
    function abrir() { modal.classList.add('aberto'); modal.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; }
    function fechar() { modal.classList.remove('aberto'); modal.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; }
    abre.addEventListener('click', abrir);
    abre.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); abrir(); } });
    modal.addEventListener('click', function (e) { if (e.target === modal) { fechar(); } });
    modal.querySelector('.ficha-modal-fechar').addEventListener('click', fechar);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && modal.classList.contains('aberto')) { fechar(); } });
})();
</script>
<?php endif; ?>

<?php if ($flash): ?>
<div class="flash flash-<?= e($flash['tipo']) ?>"><?= e($flash['mensagem']) ?></div>
<?php endif; ?>

<main class="conteudo">
