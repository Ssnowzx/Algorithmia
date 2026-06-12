<?php
/**
 * Migrador / Seeder de linha de comando.
 *
 * Uso:
 *   php database/migrate.php          → cria o banco, as tabelas e os dados iniciais
 *   php database/migrate.php --schema → apenas o schema (sem seeds)
 *
 * Idempotente: o schema dropa e recria as tabelas; rodar de novo recomeça do zero.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

/**
 * Quebra um script SQL em instruções individuais, respeitando strings entre
 * aspas (simples/duplas), identificadores em crase e comentários "-- ...".
 * Necessário porque os seeds contêm JSON com ';' dentro de strings.
 *
 * @return string[]
 */
function dividirSql(string $sql): array
{
    $instrucoes = [];
    $buffer = '';
    $len = strlen($sql);
    $aspa = '';          // '', "'", '"' ou '`' quando dentro de uma string
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
            // Aspas escapadas dentro da string.
            if ($c === '\\') {
                if ($prox !== '') {
                    $buffer .= $prox;
                    $i++;
                }
                continue;
            }
            if ($c === $aspa) {
                // Aspa simples duplicada ('') é escape em SQL.
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

/**
 * Remove linhas de comentário "-- ..." de uma instrução antes de executar.
 */
function limparComentarios(string $instrucao): string
{
    $linhas = array_filter(
        explode("\n", $instrucao),
        fn(string $linha) => !str_starts_with(trim($linha), '--')
    );
    return trim(implode("\n", $linhas));
}

function executarArquivo(PDO $pdo, string $caminho): int
{
    $sql = file_get_contents($caminho);
    if ($sql === false) {
        fwrite(STDERR, "Não foi possível ler {$caminho}\n");
        exit(1);
    }

    $instrucoes = dividirSql($sql);
    $contador = 0;
    foreach ($instrucoes as $instrucao) {
        $limpa = limparComentarios($instrucao);
        if ($limpa === '') {
            continue;
        }
        try {
            $pdo->exec($limpa);
            $contador++;
        } catch (PDOException $e) {
            fwrite(STDERR, "Erro ao executar instrução:\n" . substr($limpa, 0, 200) . "...\n→ " . $e->getMessage() . "\n");
            exit(1);
        }
    }
    return $contador;
}

// ---- Execução ----------------------------------------------------------
//
// Por padrão a migração é NÃO-DESTRUTIVA: cria o que falta e preserva contas,
// personagens e progresso. As seeds de conteúdo só rodam quando o banco está
// vazio (primeira instalação).
//
//   php database/migrate.php            → instala/atualiza sem apagar dados
//   php database/migrate.php --reset    → APAGA TUDO e recria do zero
//   php database/migrate.php --schema   → apenas o schema (sem seeds)

$apenasSchema = in_array('--schema', $argv, true);
$reset        = in_array('--reset', $argv, true);

echo "🛠  Conectando ao servidor MySQL...\n";
$pdo = getConnection(true); // sem selecionar banco (o schema cria o banco)

if ($reset) {
    echo "♻️  --reset: recriando o banco DO ZERO (todas as contas e progresso serão apagados)...\n";
    $pdo->exec('DROP DATABASE IF EXISTS ' . DB_NAME);
}

echo "📦 Executando schema.sql (CREATE IF NOT EXISTS)...\n";
$n = executarArquivo($pdo, __DIR__ . '/schema.sql');
echo "   {$n} instruções aplicadas.\n";

$pdoBanco = getConnection();

if (!$apenasSchema) {
    // Só semeia o conteúdo se o banco estiver vazio (ou em --reset). Assim,
    // rodar a migração de novo nunca apaga as contas dos jogadores.
    $temConteudo = (int) $pdoBanco->query('SELECT COUNT(*) FROM mestres')->fetchColumn() > 0;
    if ($reset || !$temConteudo) {
        echo "🌱 Semeando conteúdo do jogo (seeds.sql)...\n";
        $n = executarArquivo($pdoBanco, __DIR__ . '/seeds.sql');
        echo "   {$n} instruções aplicadas.\n";
    } else {
        $jogadores = (int) $pdoBanco->query('SELECT COUNT(*) FROM personagens')->fetchColumn();
        echo "✔️  Conteúdo já existe — preservando {$jogadores} personagem(ns) e todas as contas.\n";
        echo "   (Para zerar tudo de propósito, use: php database/migrate.php --reset)\n";
    }
}

// Relatório rápido de contagens.
echo "\n✅ Banco 'algorithmia' pronto.\n";
foreach (['usuarios', 'mestres', 'fases', 'desafios', 'itens', 'conquistas', 'dialogos'] as $tabela) {
    try {
        $total = (int) $pdoBanco->query("SELECT COUNT(*) FROM {$tabela}")->fetchColumn();
        printf("   %-14s %d\n", $tabela, $total);
    } catch (PDOException $e) {
        // tabela ausente: ignora no relatório
    }
}
echo "\nInicie o jogo com:  php -S localhost:8001\n";
