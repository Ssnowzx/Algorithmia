<?php
/** Perfil: atributos, estatísticas por matéria e conquistas. */
$classe = CLASSES[$heroi['classe']] ?? [];
?>
<h1 class="titulo-secao">📊 Perfil de <?= e($heroi['nome']) ?></h1>

<div class="grid-2">
    <div class="painel">
        <div style="display:flex;gap:1rem;align-items:center">
            <div class="hud-avatar" style="width:80px;height:80px"><?= svg('herois/' . ($classe['svg'] ?? 'heroi-ranger')) ?></div>
            <div>
                <h2 style="margin:.1rem 0"><?= e($heroi['nome']) ?></h2>
                <div class="subtitulo" style="margin:0"><?= e($classe['nome'] ?? '') ?> · Nível <?= (int) $heroi['nivel'] ?></div>
                <div style="font-size:.85rem;color:var(--texto-fraco)"><?= e($usuario['email']) ?></div>
            </div>
        </div>
        <hr style="border-color:var(--borda);margin:1rem 0">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;font-size:.95rem">
            <div>❤ HP: <strong><?= (int)$heroi['hp_atual'] ?>/<?= (int)$heroi['hp_max'] ?></strong></div>
            <div>✦ MP: <strong><?= (int)$heroi['mp_atual'] ?>/<?= (int)$heroi['mp_max'] ?></strong></div>
            <div>⚔ Ataque: <strong><?= (int)$atributos['ataque'] ?></strong></div>
            <div>🛡 Defesa: <strong><?= (int)$atributos['defesa'] ?></strong></div>
            <div>⛃ Ouro: <strong style="color:var(--ouro)"><?= (int)$heroi['ouro'] ?></strong></div>
            <div>★ Estrelas: <strong style="color:var(--xp)"><?= (int)$totalEstrelas ?></strong></div>
        </div>
        <hr style="border-color:var(--borda);margin:1rem 0">
        <div>
            <div class="topo-stat" style="display:flex;justify-content:space-between">
                <span>Alinhamento</span><strong><?= rotuloReputacao((int)$heroi['reputacao']) ?> (<?= (int)$heroi['reputacao'] ?>)</strong>
            </div>
            <div class="subtitulo" style="font-size:.82rem">
                Respostas dadas: <?= (int)$totalRespostas ?> · Vezes que recorreu à IA: <strong><?= (int)$totalUsosIa ?></strong>
            </div>
        </div>
    </div>

    <div class="painel">
        <h3 style="margin-top:0">📚 Domínio por matéria</h3>
        <?php if (empty($estatisticas)): ?>
            <p class="subtitulo">Responda desafios para acompanhar seu domínio em cada matéria.</p>
        <?php endif; ?>
        <?php foreach (ASSUNTOS as $chave => $rotulo): ?>
            <?php
                $st = $estatisticas[$chave] ?? null;
                $total = $st['total'] ?? 0;
                $acertos = $st['acertos'] ?? 0;
                $pct = $total > 0 ? round($acertos / $total * 100) : 0;
            ?>
            <div class="barra-stat">
                <div class="topo-stat">
                    <span><?= e($rotulo) ?></span>
                    <span><?= $acertos ?>/<?= $total ?> (<?= $pct ?>%)</span>
                </div>
                <div class="trilha"><div class="preenche" style="width:<?= $pct ?>%"></div></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<h2 class="titulo-secao" style="margin-top:1.6rem">🏅 Conquistas (<?= count($obtidas) ?>/<?= count($conquistas) ?>)</h2>
<div class="grade-itens">
    <?php foreach ($conquistas as $c): ?>
        <?php
            $temConquista = in_array((int) $c['id'], $obtidas, true);
            $secretaOculta = (int) $c['secreta'] === 1 && !$temConquista;
        ?>
        <div class="conquista <?= $temConquista ? '' : 'bloqueada' ?>">
            <div class="medalha"><?= svgSlug($c['svg_slug']) ?></div>
            <div>
                <strong><?= $secretaOculta ? '??? (Secreta)' : e($c['nome']) ?></strong>
                <div class="subtitulo" style="font-size:.82rem;margin:0"><?= $secretaOculta ? 'Conquista secreta — descubra jogando.' : e($c['descricao']) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
