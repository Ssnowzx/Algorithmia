<?php
/** Cabeçalho global: HUD do herói + navegação. */
$heroi = Auth::personagem();
$ehMestre = Auth::ehMestre();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
// Seção atual (1º segmento da rota) — marca o link de nav ativo com
// aria-current="page" (orientação + leitor de tela + destaque visual).
$secao = strtok((string) ($_GET['url'] ?? ''), '/') ?: 'mapa';
$navAtual = static fn (string $rota): string => $secao === $rota ? ' aria-current="page"' : '';
// Carta do herói usada TANTO no avatar da barra QUANTO no popup (mesma imagem,
// para não mostrar artes diferentes do mesmo personagem). Prefere a carta
// dedicada herois/card-<classe>; senão cai na carta hud-<classe>.
$cardHeroi = '';
if ($heroi) {
    $cardHeroi = is_file(__DIR__ . '/../../../public/img/herois/card-' . $heroi['classe'] . '.png')
        ? 'herois/card-' . $heroi['classe']
        : retratoHud($heroi['classe']);
}
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
    <link rel="stylesheet" href="<?= assetV('css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= assetV('css/style.css') ?>">
    <link rel="stylesheet" href="<?= assetV('css/cena.css') ?>">
    <link rel="stylesheet" href="<?= assetV('css/mapa.css') ?>">
    <link rel="stylesheet" href="<?= assetV('css/batalha.css') ?>">
</head>
<body class="<?= e($bodyClass ?? '') ?>">
<?php $temMolduraBarra = is_file(__DIR__ . '/../../../public/img/ui/molduras/moldura-barra-fina.png'); ?>
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
                <?= svg($cardHeroi, 'hud-retrato') ?>
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
                <span class="recurso ouro"><?= svg('ui/icones/icone-ouro', 'ico') ?> <?= (int) $heroi['ouro'] ?></span>
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
            <a href="<?= url('mapa') ?>"<?= $navAtual('mapa') ?>>Mapa</a>
            <a href="<?= url('inventario') ?>"<?= $navAtual('inventario') ?>>Inventário</a>
            <a href="<?= url('loja') ?>"<?= $navAtual('loja') ?>>Loja</a>
            <a href="<?= url('perfil') ?>"<?= $navAtual('perfil') ?>>Perfil</a>
            <a href="<?= url('ranking') ?>"<?= $navAtual('ranking') ?>>Ranking</a>
        <?php endif; ?>
        <?php if ($ehMestre): ?>
            <a href="<?= url('mestre') ?>" class="link-mestre"<?= $navAtual('mestre') ?>>⚙ Painel</a>
        <?php endif; ?>
        <?php if (Auth::logado()): ?>
            <a href="<?= url('auth/logout') ?>" class="link-sair">Sair</a>
        <?php endif; ?>
    </nav>
</header>

<?php if ($heroi): ?>
<?php
    $fichaFundo = is_file(__DIR__ . '/../../../public/img/ui/molduras/ficha-fundo.png') ? srcImagem('ui/molduras/ficha-fundo') : '';
    $temCardStatus = is_file(__DIR__ . '/../../../public/img/ui/molduras/card-status-heroi.png');
    // $cardHeroi já definido no topo (mesma carta do avatar da barra).
    $xpNivelAtual = xpParaNivel((int) $heroi['nivel']);
    $xpNivelProx  = xpParaNivel((int) $heroi['nivel'] + 1);
    $repVal = (int) $heroi['reputacao'];
?>
<div id="fichaModal" class="ficha-modal-overlay" aria-hidden="true">
<?php if ($temCardStatus): ?>
    <div class="fcs-wrap">
        <div class="ficha-card-status" role="dialog" aria-modal="true" aria-labelledby="fichaNome"
             style="--hud-cor: <?= e($cfgClasse['cor']) ?>; background-image: url('<?= e(srcImagem('ui/molduras/card-status-heroi')) ?>')">
            <button type="button" class="ficha-modal-fechar" aria-label="Fechar">✕</button>
            <div class="fcs-personagem"><?= svg($cardHeroi) ?></div>
            <div class="fcs-nome" id="fichaNome"><?= e($heroi['nome']) ?> <span class="fcs-nivel">Nv <?= (int) $heroi['nivel'] ?></span></div>
            <div class="fcs-slot fcs-hp"><span class="ic">❤</span><b><?= (int) $heroi['hp_atual'] ?>/<?= (int) $heroi['hp_max'] ?></b></div>
            <div class="fcs-slot fcs-mp"><span class="ic">✦</span><b><?= (int) $heroi['mp_atual'] ?>/<?= (int) $heroi['mp_max'] ?></b></div>
            <div class="fcs-slot fcs-xp"><span class="ic">★</span><b><?= max(0, (int) $heroi['xp'] - $xpNivelAtual) ?>/<?= max(1, $xpNivelProx - $xpNivelAtual) ?></b></div>
            <div class="fcs-slot fcs-ouro"><span class="ic"><?= svg('ui/icones/icone-ouro', 'ico') ?></span><b><?= (int) $heroi['ouro'] ?></b></div>
            <div class="fcs-slot fcs-rep"><span class="ic"><?= $repVal >= 0 ? '⚖️' : '🤖' ?></span><b><?= e(rotuloReputacao($repVal)) ?> (<?= $repVal ?>)</b></div>
        </div>
        <a class="botao ficha-perfil-link" href="<?= url('perfil') ?>">Ver perfil completo →</a>
    </div>
<?php else: ?>
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
                <div class="ficha-stat"><span><?= svg('ui/icones/icone-ouro', 'ico') ?> Ouro</span><strong><?= (int) $heroi['ouro'] ?></strong></div>
                <div class="ficha-stat ficha-stat-larga"><span><?= $repVal >= 0 ? '⚖️' : '🤖' ?> Reputação</span><strong><?= e(rotuloReputacao($repVal)) ?> (<?= $repVal ?>)</strong></div>
            </div>
            <a class="botao ficha-perfil-link" href="<?= url('perfil') ?>">Ver perfil completo →</a>
        </div>
    </div>
<?php endif; ?>
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
