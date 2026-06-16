<?php
/**
 * Patch de narrativa — aplica o refino de história (Voz do Fragmento, Logs do Zero,
 * amnésia, Eco dos Mestres e a conquista "O Arquivista do Vazio") em um banco que
 * JÁ TEM dados, SEM apagar contas, personagens nem progresso.
 *
 * Por que existe: a migração padrão só semeia conteúdo em banco vazio. Este patch
 * ressincroniza apenas a tabela `dialogos` (conteúdo puro, sem FK de jogador) a partir
 * de seeds.sql e adiciona a nova conquista de forma guardada.
 *
 * Uso:  php database/patch-narrativa.php
 * Seguro de rodar mais de uma vez (idempotente).
 */

declare(strict_types=1);

// Só via linha de comando — nunca pela web.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script só pode ser executado via linha de comando.');
}

require_once __DIR__ . '/../config/db.php';

/**
 * Quebra um script SQL em instruções, respeitando aspas e comentários "-- ...".
 * (Mesma lógica de migrate.php — necessária porque os seeds têm JSON com ';'.)
 *
 * @return string[]
 */
function dividirSqlPatch(string $sql): array
{
    $instrucoes = [];
    $buffer = '';
    $len = strlen($sql);
    $aspa = '';
    $comentarioLinha = false;

    for ($i = 0; $i < $len; $i++) {
        $c = $sql[$i];
        $prox = $i + 1 < $len ? $sql[$i + 1] : '';

        if ($comentarioLinha) {
            $buffer .= $c;
            if ($c === "\n") {
                $comentarioLinha = false;
            }
            continue;
        }
        if ($aspa === '' && $c === '-' && $prox === '-') {
            $comentarioLinha = true;
            $buffer .= $c;
            continue;
        }
        if ($aspa !== '') {
            $buffer .= $c;
            if ($c === '\\') {
                if ($prox !== '') {
                    $buffer .= $prox;
                    $i++;
                }
                continue;
            }
            if ($c === $aspa) {
                if ($c === "'" && $prox === "'") {
                    $buffer .= $prox;
                    $i++;
                    continue;
                }
                $aspa = '';
            }
            continue;
        }
        if ($c === "'" || $c === '"' || $c === '`') {
            $aspa = $c;
            $buffer .= $c;
            continue;
        }
        if ($c === ';') {
            $instrucao = trim($buffer);
            if ($instrucao !== '') {
                $instrucoes[] = $instrucao;
            }
            $buffer = '';
            continue;
        }
        $buffer .= $c;
    }
    $resto = trim($buffer);
    if ($resto !== '') {
        $instrucoes[] = $resto;
    }
    return $instrucoes;
}

/** Remove linhas de comentário "-- ..." de uma instrução antes de executar. */
function limparComentariosPatch(string $instrucao): string
{
    $linhas = array_filter(
        explode("\n", $instrucao),
        fn(string $linha) => !str_starts_with(trim($linha), '--')
    );
    return trim(implode("\n", $linhas));
}

// ---- Execução ----------------------------------------------------------

echo "🩹 Patch de narrativa — reaplicando diálogos e conquista (sem apagar progresso)...\n";

$pdo = getConnection();

// 1) Diálogos são conteúdo puro: nenhum dado de jogador depende deles, então
//    regravar a tabela a partir de seeds.sql é seguro e idempotente.
$pdo->exec('DELETE FROM dialogos');

$sql = file_get_contents(__DIR__ . '/seeds.sql');
if ($sql === false) {
    fwrite(STDERR, "Não foi possível ler seeds.sql\n");
    exit(1);
}

$blocos = 0;
foreach (dividirSqlPatch($sql) as $stmt) {
    $limpa = limparComentariosPatch($stmt);
    if ($limpa === '' || stripos($limpa, 'INTO dialogos') === false) {
        continue;
    }
    try {
        $pdo->exec($limpa);
        $blocos++;
    } catch (PDOException $e) {
        fwrite(STDERR, "Erro ao reaplicar diálogos:\n→ " . $e->getMessage() . "\n");
        exit(1);
    }
}

// 2) Conquista nova: inserção guardada (não toca nas conquistas já obtidas).
$pdo->exec(
    "INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
     SELECT 'arquivista_do_vazio', 'O Arquivista do Vazio',
            'Recuperou todos os Logs do Zero. Agora você sabe como um herói vira abismo.',
            'icone-ia', 1
     WHERE NOT EXISTS (SELECT 1 FROM conquistas WHERE codigo = 'arquivista_do_vazio')"
);

$totalDialogos = (int) $pdo->query('SELECT COUNT(*) FROM dialogos')->fetchColumn();
$totalConquistas = (int) $pdo->query('SELECT COUNT(*) FROM conquistas')->fetchColumn();

echo "   {$blocos} bloco(s) de diálogos reaplicado(s) — {$totalDialogos} falas no total.\n";
echo "   conquistas: {$totalConquistas} (inclui 'O Arquivista do Vazio').\n";
echo "✅ Patch concluído. Contas, personagens e progresso preservados.\n";
