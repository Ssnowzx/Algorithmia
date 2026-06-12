<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= NOME_JOGO ?> — <?= SUBTITULO_JOGO ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400;500;600;700&family=Rubik:wght@400;500;700;800&display=swap">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<?php require __DIR__ . '/../layout/splash.php'; ?>
<div class="hero">
    <div class="hero-logo"><?= svg('ui/logo') ?></div>
    <h1><span class="grad"><?= NOME_JOGO ?></span></h1>
    <p class="lema">Um RPG onde <strong>programar é magia</strong> — e aprender é, surpreendentemente, na marra.
       Domine PHP, MVC, POO, estruturas de dados, cálculo e redes socando bugs, subindo de nível e resistindo
       (ou não) à tentação de uma IA que adora dar a resposta pronta. Spoiler: o atalho cobra caro.</p>
    <div class="hero-acoes">
        <a class="botao" href="<?= url('auth/registro') ?>">⚔ Aceitar o inevitável</a>
        <a class="botao botao-fantasma" href="<?= url('auth/login') ?>">Já vendi minha alma aqui</a>
    </div>
</div>

<div class="conteudo">
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
        <span class="rodape-marca"><?= svg('ui/logo', 'rodape-logo') ?> <?= NOME_JOGO ?></span>
        <span class="rodape-sep">·</span>
        <span><?= SUBTITULO_JOGO ?></span>
        <span class="rodape-sep">·</span>
        <span class="rodape-fraco">Projeto MVC em PHP puro</span>
    </div>
</footer>
<script src="<?= asset('js/ui.js') ?>"></script>
</body>
</html>
