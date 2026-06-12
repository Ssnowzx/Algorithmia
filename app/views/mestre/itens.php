<?php /** Lista de itens (CRUD). */ ?>
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem">
    <h1 class="titulo-secao" style="margin:0">⚔️ Itens (<?= count($itens) ?>)</h1>
    <a class="botao" href="<?= url('mestre/novoItem') ?>">＋ Novo Item</a>
</div>
<p class="subtitulo"><a href="<?= url('mestre') ?>">← Voltar ao painel</a></p>

<div class="painel" style="overflow-x:auto">
    <table class="tabela">
        <thead><tr><th>#</th><th>Nome</th><th>Tipo</th><th>Raridade</th><th>Preço</th><th>Efeito</th><th>Ações</th></tr></thead>
        <tbody>
            <?php foreach ($itens as $it): ?>
                <tr>
                    <td><?= (int) $it['id'] ?></td>
                    <td><?= e($it['nome']) ?></td>
                    <td><?= e($it['tipo']) ?></td>
                    <td><span class="raridade raridade-<?= e($it['raridade']) ?>"><?= e($it['raridade']) ?></span></td>
                    <td><?= (int) $it['preco'] ?></td>
                    <td style="font-size:.78rem;font-family:var(--pixel)"><?= e($it['efeito'] ?? '—') ?></td>
                    <td style="white-space:nowrap">
                        <a class="botao botao-sm botao-fantasma" href="<?= url('mestre/editarItem/' . (int)$it['id']) ?>">Editar</a>
                        <form method="post" action="<?= url('mestre/excluirItem/' . (int)$it['id']) ?>" style="display:inline" data-confirmar="Excluir este item?">
                            <?= csrf_field() ?><button class="botao botao-sm botao-perigo">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
