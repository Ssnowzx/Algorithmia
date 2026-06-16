<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= NOME_JOGO ?> — <?= SUBTITULO_JOGO ?></title>
    <link rel="icon" href="<?= asset('favicon.png') ?>" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400;500;600;700&family=Rubik:wght@400;500;700;800&display=swap">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/cena.css') ?>">
</head>
<body class="pagina-home">
<?php require __DIR__ . '/../layout/splash.php'; ?>

<section class="home-entrada">
    <div class="home-entrada-overlay"></div>
    <div class="splash-particulas" aria-hidden="true"></div>
    <div class="home-entrada-conteudo">
        <?= marcaHtml('hero') ?>
        <p class="home-entrada-sub"><?= SUBTITULO_JOGO ?> — onde <strong>programar é magia</strong> e aprender é na marra.</p>
        <a class="home-entrada-botao" href="<?= url('auth/registro') ?>">
            <img src="<?= asset('img/ui/botao-entrar-mundo.png') ?>" alt="Entrar no mundo" loading="eager">
        </a>
        <a class="home-login-link" href="<?= url('auth/login') ?>">Já vendi minha alma aqui</a>
        <a class="seta-rolar" href="#saibaMais" aria-label="Rolar para saber mais">▾</a>
    </div>
</section>

<div class="conteudo" id="saibaMais">
    <div class="secao-historia">
        <h2>O Reino de Algorithmia</h2>
        <p>Houve um tempo em que uma <strong>IA Ancestral</strong> dava todas as respostas. Maravilhoso, até os
        programadores esquecerem como pensar. Aí ela travou no <em>Grande Timeout</em>, o mundo quase virou um
        <code>500 Internal Server Error</code> e os Cinco Mestres tiveram que selar a coitada no Abismo do
        <code>/dev/null</code> — e fundar um culto à indentação, porque é claro que fundaram.</p>
        <p>Agora os Fragmentos da IA reapareceram (de novo) e os bugs voltaram a escapar das fendas (de novo). Você,
        mais um aprendiz genérico da Vila Hello World, herda um Fragmento e vai treinar com os Cinco Mestres. A cada
        cola que der, mais perto fica do destino sombrio de <strong>Lorde Segfault</strong>. Ou da redenção. Sem pressão.</p>
    </div>

    <h2 class="titulo-secao" style="text-align:center">Os Cinco Mestres</h2>
    <div class="mestres-grid">
        <?php foreach ($mestres as $m): ?>
            <div class="mestre-card">
                <div class="retrato"><?= svg('mestres/' . $m['svg_slug']) ?></div>
                <h3><?= e($m['nome']) ?></h3>
                <div class="titulo-m"><?= e($m['titulo']) ?></div>
                <div class="disc"><?= e($m['disciplina']) ?></div>
                <div class="disc">📍 <?= e($m['regiao']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="text-align:center;margin-top:2.5rem">
        <a class="botao" href="<?= url('auth/registro') ?>">Criar meu herói agora</a>
    </div>
</div>

<footer class="rodape">
    <div class="rodape-conteudo">
        <span class="rodape-marca"><?= marcaHtml('rodape') ?></span>
        <span class="rodape-sep">·</span>
        <span><?= SUBTITULO_JOGO ?></span>
        <span class="rodape-sep">·</span>
        <span class="rodape-fraco">Projeto MVC em PHP puro</span>
    </div>
</footer>
<script src="<?= asset('js/som.js') ?>"></script>
<script src="<?= asset('js/ui.js') ?>"></script>
<script>
(function () {
    // Pad ambiente + som de entrada: só após o 1º gesto (política de áudio).
    var armado = false;
    function destravar() {
        if (armado) { return; }
        armado = true;
        if (window.SOM) { window.SOM.ambienteIniciar(); }
        ['pointerdown', 'keydown', 'touchstart'].forEach(function (e) { document.removeEventListener(e, destravar); });
    }
    ['pointerdown', 'keydown', 'touchstart'].forEach(function (e) { document.addEventListener(e, destravar, { passive: true }); });

    // Som nos botões de entrada.
    document.querySelectorAll('.home-entrada-botao, .home-login-link').forEach(function (b) {
        b.addEventListener('mouseenter', function () { if (window.SOM) { window.SOM.clique(); } });
        b.addEventListener('click', function () { if (window.SOM) { window.SOM.pressStart(); } });
    });
})();
</script>
</body>
</html>
