<?php
/** Formulário de criação/edição de desafio. */
$ed = $desafio !== null;
$opcoesTexto = '';
$respostaTexto = '';
if ($ed) {
    $ops = $desafio['opcoes'] ? json_decode($desafio['opcoes'], true) : [];
    $opcoesTexto = is_array($ops) ? implode("\n", $ops) : '';
    $resp = json_decode($desafio['resposta'], true);
    if (is_bool($resp)) {
        $respostaTexto = $resp ? 'true' : 'false';
    } elseif (is_array($resp)) {
        $respostaTexto = implode(',', $resp);
    } else {
        $respostaTexto = (string) $resp;
    }
}
$tipos = ['multipla' => 'Múltipla escolha', 'vf' => 'Verdadeiro/Falso', 'completar' => 'Completar código', 'erro' => 'Encontrar erro', 'ordenar' => 'Ordenar', 'arrastar' => 'Arrastar'];
?>
<h1 class="titulo-secao"><?= $ed ? '✏️ Editar' : '＋ Novo' ?> Desafio</h1>
<p class="subtitulo"><a href="<?= url('mestre/desafios') ?>">← Voltar à lista</a></p>

<form method="post" action="<?= url('mestre/salvarDesafio') ?>" class="painel">
    <?= csrf_field() ?>
    <?php if ($ed): ?><input type="hidden" name="id" value="<?= (int) $desafio['id'] ?>"><?php endif; ?>

    <div class="grid-2">
        <div class="campo">
            <label>Fase</label>
            <select name="fase_id" required>
                <?php foreach ($fases as $f): ?>
                    <option value="<?= (int)$f['id'] ?>" <?= $ed && (int)$desafio['fase_id'] === (int)$f['id'] ? 'selected' : '' ?>>
                        <?= (int)$f['ordem_global'] ?>. <?= e($f['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo">
            <label>Ordem na fase</label>
            <input type="number" name="ordem" value="<?= $ed ? (int)$desafio['ordem'] : 1 ?>" min="1">
        </div>
    </div>

    <div class="grid-2">
        <div class="campo">
            <label>Tipo</label>
            <select name="tipo" required>
                <?php foreach ($tipos as $val => $rot): ?>
                    <option value="<?= $val ?>" <?= $ed && $desafio['tipo'] === $val ? 'selected' : '' ?>><?= e($rot) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo">
            <label>Assunto</label>
            <select name="assunto" required>
                <?php foreach (ASSUNTOS as $val => $rot): ?>
                    <option value="<?= $val ?>" <?= $ed && $desafio['assunto'] === $val ? 'selected' : '' ?>><?= e($rot) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="campo">
        <label>Pergunta</label>
        <textarea name="pergunta" required><?= $ed ? e($desafio['pergunta']) : '' ?></textarea>
    </div>

    <div class="campo">
        <label>Código (opcional — aparece num bloco monoespaçado)</label>
        <textarea name="codigo" placeholder="Deixe vazio se não houver código"><?= $ed ? e($desafio['codigo']) : '' ?></textarea>
    </div>

    <div class="campo">
        <label>Opções (uma por linha — para múltipla escolha, erro, ordenar)</label>
        <textarea name="opcoes" placeholder="Opção A&#10;Opção B&#10;Opção C"><?= e($opcoesTexto) ?></textarea>
    </div>

    <div class="campo">
        <label>Resposta correta</label>
        <input type="text" name="resposta" value="<?= e($respostaTexto) ?>" required>
        <p class="subtitulo" style="font-size:.78rem;margin-top:.3rem">
            • Múltipla/Erro: índice da opção correta (0 = primeira).<br>
            • Verdadeiro/Falso: <code>true</code> ou <code>false</code>.<br>
            • Completar: texto(s) aceito(s), separados por vírgula.<br>
            • Ordenar/Arrastar: índices das opções na ordem correta, ex.: <code>2,0,1</code>.
        </p>
    </div>

    <div class="campo">
        <label>Explicação (mostrada após responder)</label>
        <textarea name="explicacao" required><?= $ed ? e($desafio['explicacao']) : '' ?></textarea>
    </div>

    <div class="campo" style="max-width:200px">
        <label>Dificuldade (1 a 5)</label>
        <input type="number" name="dificuldade" min="1" max="5" value="<?= $ed ? (int)$desafio['dificuldade'] : 1 ?>">
    </div>

    <button class="botao" type="submit">💾 Salvar Desafio</button>
</form>
