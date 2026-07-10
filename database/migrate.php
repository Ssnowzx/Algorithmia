<?php
/**
 * Migrador / Seeder de linha de comando.
 *
 * Uso:
 *   php database/migrate.php          → instala/atualiza PRESERVANDO os dados
 *   php database/migrate.php --reset  → APAGA TUDO (DROP DATABASE) e recria do zero
 *   php database/migrate.php --schema → apenas o schema (sem seeds de conteúdo)
 *
 * NÃO-DESTRUTIVO por padrão (idempotente): o schema usa CREATE TABLE IF NOT EXISTS,
 * as seeds de conteúdo (seeds.sql) só rodam quando o banco está vazio, e o banco de
 * questões ampliado (seed-banco-questoes.php) roda sempre sem duplicar. Contas,
 * personagens e progresso são preservados. Só `--reset` apaga dados.
 */

declare(strict_types=1);

// Blindagem: scripts de banco NUNCA podem ser disparados pela web (o document
// root pode acabar incluindo database/). Só rodam via linha de comando.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script só pode ser executado via linha de comando.');
}

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

/**
 * Aplica migrações incrementais de database/migrations/*.sql, cada uma uma única
 * vez, registrando as já aplicadas em `migracoes_aplicadas`. Permite evoluir o
 * schema de bancos que JÁ TÊM dados (ex.: novas colunas/ENUMs), sem --reset.
 */
function aplicarMigracoes(PDO $pdo): int
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS migracoes_aplicadas (
            arquivo     VARCHAR(255) PRIMARY KEY,
            aplicada_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $dir = __DIR__ . '/migrations';
    if (!is_dir($dir)) {
        return 0;
    }
    $arquivos = glob($dir . '/*.sql') ?: [];
    sort($arquivos); // ordem por nome — use prefixo de data (AAAAMMDD-...).

    $jaAplicadas = $pdo->query('SELECT arquivo FROM migracoes_aplicadas')->fetchAll(PDO::FETCH_COLUMN);
    $aplicadas = 0;
    foreach ($arquivos as $caminho) {
        $nome = basename($caminho);
        if (in_array($nome, $jaAplicadas, true)) {
            continue;
        }
        echo "   🧩 {$nome}\n";
        $sql = (string) file_get_contents($caminho);
        foreach (dividirSql($sql) as $instrucao) {
            $limpa = limparComentarios($instrucao);
            if ($limpa === '') {
                continue;
            }
            try {
                $pdo->exec($limpa);
            } catch (PDOException $e) {
                fwrite(STDERR, "Erro na migração {$nome}:\n" . substr($limpa, 0, 200) . "...\n→ " . $e->getMessage() . "\n");
                exit(1);
            }
        }
        $pdo->prepare('INSERT INTO migracoes_aplicadas (arquivo) VALUES (?)')->execute([$nome]);
        $aplicadas++;
    }
    return $aplicadas;
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

echo "🧩 Aplicando migrações incrementais (migrations/)...\n";
$migs = aplicarMigracoes($pdoBanco);
echo $migs === 0 ? "   nenhuma pendente.\n" : "   {$migs} migração(ões) aplicada(s).\n";

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
if (!$apenasSchema) {
    // Banco de questões ampliado (anti-repetição). Idempotente: roda sempre,
    // inclusive em bancos já populados, sem duplicar perguntas existentes.
    echo "\n🧠 Aplicando banco de questões ampliado (anti-repetição)...\n";
    require __DIR__ . '/seed-banco-questoes.php';

    // Conteúdo de relacionar (tipo arrastar), versionado a partir do host.
    echo "\n🧠 Aplicando questões de relacionar (arrastar)...\n";
    executarArquivo($pdoBanco, __DIR__ . '/relacionar.sql');

    // Banco completo de desafios, versionado a partir do host.
    echo "\n🧠 Aplicando banco completo de desafios...\n";
    executarArquivo($pdoBanco, __DIR__ . '/desafios.sql');

    // Conquistas e diálogos idempotentes: o seeds.sql só roda em banco vazio, então
    // instalações antigas (ex.: o host) não recebiam conquistas/diálogos novos.
    // Estes aplicam só o que falta, sem duplicar nem sobrescrever.
    echo "\n🏆 Sincronizando conquistas...\n";
    executarArquivo($pdoBanco, __DIR__ . '/conquistas.sql');
    echo "\n💬 Sincronizando diálogos...\n";
    executarArquivo($pdoBanco, __DIR__ . '/dialogos.sql');

    // `require_once` apenas define a função. Semeamos com `forcar = false`: se a conta
    // já existe, nada é tocado. Antes, este arquivo era executado ao ser carregado e
    // reescrevia a senha e apagava o progresso da conta de administrador — a cada
    // deploy, em produção.
    echo "\n🎮 Conta mestre admin (sem progresso automático)...\n";
    require_once __DIR__ . '/seed-conta-demo.php';
    echo semearContaDemo($pdoBanco, false), "\n";
}

echo "\nInicie o jogo com:  php -S localhost:8001\n";
