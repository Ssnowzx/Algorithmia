<?php /** Dashboard do painel do mestre. */ ?>
<h1 class="titulo-secao">⚙️ Painel do Mestre</h1>
<p class="subtitulo">Área administrativa da Ordem do Código Limpo. Gerencie o conteúdo do jogo.</p>

<div class="grade-itens" style="margin-bottom:1.4rem">
    <div class="painel" style="text-align:center"><div style="font-size:2rem;color:var(--primaria-2)"><?= (int)$totais['fases'] ?></div><div class="subtitulo">Fases</div></div>
    <div class="painel" style="text-align:center"><div style="font-size:2rem;color:var(--xp)"><?= (int)$totais['desafios'] ?></div><div class="subtitulo">Desafios</div></div>
    <div class="painel" style="text-align:center"><div style="font-size:2rem;color:var(--ouro)"><?= (int)$totais['itens'] ?></div><div class="subtitulo">Itens</div></div>
    <div class="painel" style="text-align:center"><div style="font-size:2rem;color:var(--mp)"><?= (int)$totais['mestres'] ?></div><div class="subtitulo">Mestres</div></div>
    <div class="painel" style="text-align:center"><div style="font-size:2rem;color:var(--sucesso)"><?= (int)$totais['jogadores'] ?></div><div class="subtitulo">Jogadores</div></div>
</div>

<div class="grid-2">
    <div class="painel">
        <h3 style="margin-top:0">📜 Desafios</h3>
        <p class="subtitulo">Crie, edite e remova as perguntas de cada fase.</p>
        <a class="botao" href="<?= url('mestre/desafios') ?>">Gerir Desafios</a>
    </div>
    <div class="painel">
        <h3 style="margin-top:0">🗺️ Fases</h3>
        <p class="subtitulo">Configure os nós do mapa, inimigos e recompensas.</p>
        <a class="botao" href="<?= url('mestre/fases') ?>">Gerir Fases</a>
    </div>
    <div class="painel">
        <h3 style="margin-top:0">⚔️ Itens</h3>
        <p class="subtitulo">Administre armas, escudos, poções e itens especiais.</p>
        <a class="botao" href="<?= url('mestre/itens') ?>">Gerir Itens</a>
    </div>
    <div class="painel">
        <h3 style="margin-top:0">🎮 Jogar</h3>
        <p class="subtitulo">Volte para o mapa e teste o conteúdo como jogador.</p>
        <a class="botao botao-fantasma" href="<?= url('mapa') ?>">Ir ao Mapa</a>
    </div>
</div>
