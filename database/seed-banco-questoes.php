<?php
/**
 * Seeder IDEMPOTENTE do banco de questões ampliado (anti-repetição).
 *
 * Carrega database/banco-questoes/*.php — um arquivo por matéria, cada um
 * devolvendo uma lista de perguntas — e insere as que ainda não existem.
 * A identidade de uma pergunta é o par (fase_id, pergunta): rodar de novo
 * NUNCA duplica, e adicionar perguntas novas é só rodar outra vez.
 *
 * Cada fase guarda um POOL maior do que o sorteado por batalha; o sorteio
 * (BatalhaService) escolhe um subconjunto a cada combate, priorizando inéditas.
 *
 * Uso:
 *   php database/seed-banco-questoes.php
 * (também é chamado automaticamente ao final de database/migrate.php)
 */

declare(strict_types=1);

// Só via linha de comando — nunca pela web.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script só pode ser executado via linha de comando.');
}

require_once __DIR__ . '/../config/db.php';

$pdo = getConnection();

// Rede de segurança: garante o assunto 'calculo' no ENUM mesmo que a migração
// 20250618-assunto-calculo.sql ainda não tenha sido aplicada (execução avulsa).
// ALTER MODIFY é idempotente — define a lista completa do ENUM.
try {
    $pdo->exec(
        "ALTER TABLE desafios
         MODIFY assunto ENUM('php','mvc','sql','poo','estruturas','redes','logica','calculo') NOT NULL"
    );
} catch (PDOException $e) {
    fwrite(STDERR, "Aviso: não foi possível ajustar o ENUM de assunto: {$e->getMessage()}\n");
}

/** Tipos cujo gabarito é um índice/inteiro (não um array nem booleano). */
const TIPOS_INDICE = ['multipla', 'erro'];

$arquivos = glob(__DIR__ . '/banco-questoes/*.php') ?: [];
sort($arquivos);
if (!$arquivos) {
    fwrite(STDERR, "Nenhum arquivo em database/banco-questoes/.\n");
    exit(1);
}

$existe = $pdo->prepare('SELECT 1 FROM desafios WHERE fase_id = ? AND pergunta = ? LIMIT 1');
$proximaOrdem = $pdo->prepare('SELECT COALESCE(MAX(ordem), 0) + 1 FROM desafios WHERE fase_id = ?');
$inserir = $pdo->prepare(
    'INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
     VALUES (:fase, :ordem, :tipo, :assunto, :pergunta, :codigo, :opcoes, :resposta, :explicacao, :dif)'
);

$inseridas = 0;
$puladas = 0;
$porMateria = [];

foreach ($arquivos as $arquivo) {
    $materia = basename($arquivo, '.php');
    $linhas = require $arquivo;
    if (!is_array($linhas)) {
        fwrite(STDERR, "  ! {$materia}: não devolveu uma lista de questões.\n");
        continue;
    }

    foreach ($linhas as $q) {
        $faseId = (int) $q['fase'];
        $pergunta = (string) $q['pergunta'];

        $existe->execute([$faseId, $pergunta]);
        if ($existe->fetchColumn()) {
            $puladas++;
            continue;
        }

        // Normaliza o gabarito conforme o tipo (espelha BatalhaService::verificar):
        //   multipla/erro → índice (int) | vf → bool | demais → array/string.
        $resposta = $q['resposta'];
        if (in_array($q['tipo'], TIPOS_INDICE, true)) {
            $resposta = (int) $resposta;
        }

        $proximaOrdem->execute([$faseId]);
        $ordem = (int) $proximaOrdem->fetchColumn();

        $inserir->execute([
            'fase'       => $faseId,
            'ordem'      => $ordem,
            'tipo'       => $q['tipo'],
            'assunto'    => $q['assunto'],
            'pergunta'   => $pergunta,
            'codigo'     => $q['codigo'] ?? null,
            'opcoes'     => isset($q['opcoes']) && $q['opcoes'] !== null ? json_encode($q['opcoes'], JSON_UNESCAPED_UNICODE) : null,
            'resposta'   => json_encode($resposta, JSON_UNESCAPED_UNICODE),
            'explicacao' => $q['explicacao'],
            'dif'        => (int) ($q['dif'] ?? 1),
        ]);

        $inseridas++;
        $porMateria[$materia] = ($porMateria[$materia] ?? 0) + 1;
    }
}

echo "🧠 Banco de questões aplicado.\n";
foreach ($porMateria as $materia => $qtd) {
    printf("   %-14s +%d\n", $materia, $qtd);
}
echo "   ────────────────\n";
printf("   %-14s %d inseridas, %d já existentes.\n", 'total', $inseridas, $puladas);
