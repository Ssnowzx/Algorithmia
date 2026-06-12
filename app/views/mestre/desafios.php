<?php /** Lista de desafios (CRUD). */ ?>
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem">
    <h1 class="titulo-secao" style="margin:0">📜 Desafios (<?= count($desafios) ?>)</h1>
    <a class="botao" href="<?= url('mestre/novoDesafio') ?>">＋ Novo Desafio</a>
</div>
<p class="subtitulo"><a href="<?= url('mestre') ?>">← Voltar ao painel</a></p>

<div class="painel" style="overflow-x:auto">
    <table class="tabela">
        <thead><tr><th>#</th><th>Fase</th><th>Tipo</th><th>Assunto</th><th>Pergunta</th><th>Dif.</th><th>Ações</th></tr></thead>
        <tbody>
            <?php foreach ($desafios as $d): ?>
                <tr>
                    <td><?= (int) $d['id'] ?></td>
                    <td style="font-size:.82rem"><?= e($d['fase_nome']) ?></td>
                    <td><span class="badge-assunto"><?= e($d['tipo']) ?></span></td>
                    <td><?= e($d['assunto']) ?></td>
                    <td style="max-width:320px"><?= e(mb_strimwidth($d['pergunta'], 0, 70, '…')) ?></td>
                    <td><?= str_repeat('◆', (int) $d['dificuldade']) ?></td>
                    <td style="white-space:nowrap">
                        <a class="botao botao-sm botao-fantasma" href="<?= url('mestre/editarDesafio/' . (int)$d['id']) ?>">Editar</a>
                        <form method="post" action="<?= url('mestre/excluirDesafio/' . (int)$d['id']) ?>" style="display:inline" data-confirmar="Excluir este desafio?">
                            <?= csrf_field() ?><button class="botao botao-sm botao-perigo">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
