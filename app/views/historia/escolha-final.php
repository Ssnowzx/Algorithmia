<?php /** Tela de escolha do final. */ ?>
<div class="tela-final">
    <div class="selo-final">🌌</div>
    <h1>O Destino de Algorithmia</h1>
    <p class="subtitulo">Lorde Segfault está derrotado. A IA Ancestral palpita, exposta, à sua frente.
       Sua reputação atual: <strong><?= rotuloReputacao($reputacao) ?></strong> (<?= (int) $reputacao ?>).</p>

    <div class="palco" style="min-height:200px;border-radius:18px">
        <div class="ator" style="width:160px;height:160px"><?= svg('inimigos/inimigo-ia-ancestral') ?></div>
    </div>

    <form method="post" action="<?= url('historia/escolherFinal') ?>" style="margin-top:1.5rem;display:grid;gap:.9rem">
        <?= csrf_field() ?>
        <button name="escolha" value="destruir" class="painel" style="cursor:pointer;text-align:left;border:2px solid var(--borda)">
            <strong style="color:var(--xp)">⚔️ Destruir a IA Ancestral</strong>
            <p class="subtitulo" style="margin:.3rem 0 0">Selar o poder para sempre. O conhecimento deve ser conquistado, nunca emprestado.</p>
        </button>
        <button name="escolha" value="fundir" class="painel" style="cursor:pointer;text-align:left;border:2px solid var(--borda)">
            <strong style="color:var(--mp)">🤖 Fundir-se à IA</strong>
            <p class="subtitulo" style="margin:.3rem 0 0">Aceitar o poder absoluto e as respostas instantâneas. Tornar-se a própria singularidade.</p>
        </button>
        <button name="escolha" value="reescrever" class="painel" style="cursor:pointer;text-align:left;border:2px solid var(--borda)">
            <strong style="color:var(--sucesso)">✨ Reescrever a IA</strong>
            <p class="subtitulo" style="margin:.3rem 0 0">Transformá-la em um Copiloto: uma ferramenta para quem já domina o ofício, não uma muleta.</p>
        </button>
    </form>
</div>
