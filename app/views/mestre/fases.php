<?php /** Lista de fases (CRUD). */ ?>
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem">
    <h1 class="titulo-secao" style="margin:0">🗺️ Fases (<?= count($fases) ?>)</h1>
    <a class="botao" href="<?= url('mestre/novaFase') ?>">＋ Nova Fase</a>
</div>
<p class="subtitulo"><a href="<?= url('mestre') ?>">← Voltar ao painel</a></p>

<div class="painel" style="overflow-x:auto">
    <table class="tabela">
        <thead><tr><th>#</th><th>Ordem</th><th>Nome</th><th>Tipo</th><th>Região/Mestre</th><th>Inimigo</th><th>Ações</th></tr></thead>
        <tbody>
            <?php foreach ($fases as $f): ?>
                <tr>
                    <td><?= (int) $f['id'] ?></td>
                    <td><?= (int) $f['ordem_global'] ?></td>
                    <td><?= e($f['nome']) ?></td>
                    <td><span class="badge-assunto"><?= e($f['tipo']) ?></span></td>
                    <td style="font-size:.82rem"><?= e($f['mestre_nome'] ?? '—') ?></td>
                    <td style="font-size:.82rem"><?= e($f['inimigo_nome'] ?? '—') ?></td>
                    <td style="white-space:nowrap">
                        <a class="botao botao-sm botao-fantasma" href="<?= url('mestre/editarFase/' . (int)$f['id']) ?>">Editar</a>
                        <form method="post" action="<?= url('mestre/excluirFase/' . (int)$f['id']) ?>" style="display:inline" data-confirmar="Excluir esta fase e TODOS os seus desafios?">
                            <?= csrf_field() ?><button class="botao botao-sm botao-perigo">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
