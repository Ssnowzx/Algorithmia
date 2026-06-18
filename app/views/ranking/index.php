<?php /** Ranking dos heróis. */ ?>
<h1 class="titulo-secao">🏆 Ranking de Algorithmia</h1>
<p class="subtitulo">Os aprendizes mais dedicados do reino, por nível e experiência.</p>

<div class="painel painel-ranking">
    <table class="tabela tabela-ranking">
        <thead>
            <tr><th>#</th><th>Herói</th><th>Classe</th><th>Nível</th><th>XP</th><th>Alinhamento</th></tr>
        </thead>
        <tbody>
            <?php foreach ($ranking as $i => $p): ?>
                <?php
                    $ehVoce = (int) $p['id'] === $heroiId;
                    $rankCls = $i < 3 ? ' rank-top rank-' . ($i + 1) : '';
                ?>
                <tr class="<?= ($ehVoce ? 'destaque' : '') . $rankCls ?>">
                    <td class="rank-pos">
                        <?php if ($i === 0): ?>🥇<?php elseif ($i === 1): ?>🥈<?php elseif ($i === 2): ?>🥉<?php else: ?><?= $i + 1 ?><?php endif; ?>
                    </td>
                    <td><strong><?= e($p['nome']) ?></strong><?= $ehVoce ? ' <span style="color:var(--xp)">(você)</span>' : '' ?></td>
                    <td><?= e(CLASSES[$p['classe']]['nome'] ?? $p['classe']) ?></td>
                    <td><?= (int) $p['nivel'] ?></td>
                    <td><?= (int) $p['xp'] ?></td>
                    <td><?= rotuloReputacao((int) $p['reputacao']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
