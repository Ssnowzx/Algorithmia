<?php
/** Perfil: atributos, estatísticas por matéria e conquistas. */
$classe = CLASSES[$heroi['classe']] ?? [];
?>
<h1 class="titulo-secao">📊 Perfil de <?= e($heroi['nome']) ?></h1>

<?php if (is_file(__DIR__ . '/../../../public/img/ui/molduras/moldura-status.png')): ?>
<?php $xaV = xpParaNivel((int) $heroi['nivel']); $xpV = xpParaNivel((int) $heroi['nivel'] + 1); ?>
<div class="perfil-vitrine" style="--hud-cor: <?= e($classe['cor'] ?? '#7c5cff') ?>; background-image: url('<?= e(srcImagem('ui/molduras/moldura-status')) ?>')">
    <div class="vitrine-avatar"><?= svg(retratoHud($heroi['classe']), 'hud-retrato') ?></div>
    <div class="vitrine-centro">
        <div class="vitrine-nome"><?= e($heroi['nome']) ?></div>
        <div class="vitrine-classe"><?= e($classe['nome'] ?? '') ?> · Nível <?= (int) $heroi['nivel'] ?></div>
        <div class="vitrine-barras">
            <?= barra((int) $heroi['hp_atual'], (int) $heroi['hp_max'], 'hp') ?>
            <?= barra((int) $heroi['mp_atual'], (int) $heroi['mp_max'], 'mp') ?>
            <?= barra((int) $heroi['xp'] - $xaV, max(1, $xpV - $xaV), 'xp') ?>
        </div>
    </div>
    <div class="vitrine-recurso vitrine-ouro" title="Ouro"><?= svg('ui/icones/icone-ouro', 'ico') ?><span><?= (int) $heroi['ouro'] ?></span></div>
    <div class="vitrine-recurso vitrine-rep" title="Reputação: <?= e(rotuloReputacao((int) $heroi['reputacao'])) ?>">
        <span class="emoji"><?= (int) $heroi['reputacao'] >= 0 ? '⚖️' : '🤖' ?></span><span><?= (int) $heroi['reputacao'] ?></span>
    </div>
</div>
<?php endif; ?>

<div class="grid-2">
    <div class="painel">
        <div class="perfil-id">
            <div class="hud-avatar perfil-id-avatar" style="--hud-cor:<?= e($classe['cor'] ?? '#2ecc71') ?>"><?= svg(retratoHud($heroi['classe']), 'hud-retrato') ?></div>
            <div>
                <h2 class="perfil-id-nome"><?= e($heroi['nome']) ?></h2>
                <div class="subtitulo" style="margin:0"><?= e($classe['nome'] ?? '') ?> · Nível <?= (int) $heroi['nivel'] ?></div>
                <div class="perfil-id-email"><?= e($usuario['email']) ?></div>
            </div>
        </div>

        <div class="perfil-stats">
            <div class="stat-tile"><span class="stat-ic">❤</span><span class="stat-val"><?= (int)$heroi['hp_max'] ?></span><span class="stat-lbl">Vida</span></div>
            <div class="stat-tile"><span class="stat-ic">✦</span><span class="stat-val"><?= (int)$heroi['mp_max'] ?></span><span class="stat-lbl">Mana</span></div>
            <div class="stat-tile"><span class="stat-ic">⚔</span><span class="stat-val"><?= (int)$atributos['ataque'] ?></span><span class="stat-lbl">Ataque</span></div>
            <div class="stat-tile"><span class="stat-ic">🛡</span><span class="stat-val"><?= (int)$atributos['defesa'] ?></span><span class="stat-lbl">Defesa</span></div>
            <div class="stat-tile is-ouro"><span class="stat-ic">⛃</span><span class="stat-val"><?= (int)$heroi['ouro'] ?></span><span class="stat-lbl">Ouro</span></div>
            <div class="stat-tile is-xp"><span class="stat-ic">★</span><span class="stat-val"><?= (int)$totalEstrelas ?></span><span class="stat-lbl">Estrelas</span></div>
            <div class="stat-tile is-poder"><span class="stat-ic">💪</span><span class="stat-val"><?= poderTotal($atributos) ?></span><span class="stat-lbl">Poder</span></div>
        </div>

        <?php $rep = (int) $heroi['reputacao']; $repPos = max(0, min(100, ($rep + 100) / 2)); ?>
        <div class="rep-bloco">
            <div class="rep-cabecalho">
                <span class="rep-titulo">Alinhamento</span>
                <strong class="rep-rotulo"><?= e(rotuloReputacao($rep)) ?> (<?= $rep ?>)</strong>
            </div>
            <div class="rep-medidor" style="--rep-pos: <?= $repPos ?>%">
                <div class="rep-escala"><span class="rep-marcador"></span></div>
                <div class="rep-extremos">
                    <span class="rep-ia">🤖 Singularidade</span>
                    <span class="rep-disc">⚖️ Disciplina</span>
                </div>
            </div>
            <div class="subtitulo rep-meta">Respostas dadas: <?= (int)$totalRespostas ?> · Recorreu à IA: <strong><?= (int)$totalUsosIa ?></strong></div>
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
                <?php
                    // Progresso parcial (X/Y) só em conquista contável, não-obtida e não-secreta.
                    $pgc = (!$temConquista && !$secretaOculta) ? ($progressoConquistas[$c['codigo']] ?? null) : null;
                ?>
                <?php if ($pgc): ?>
                    <?php $pgcPct = $pgc['alvo'] > 0 ? round($pgc['atual'] / $pgc['alvo'] * 100) : 0; ?>
                    <div class="conquista-progresso">
                        <div class="trilha"><div class="preenche" style="width:<?= $pgcPct ?>%"></div></div>
                        <span class="conquista-progresso-txt"><?= (int) $pgc['atual'] ?>/<?= (int) $pgc['alvo'] ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
