<?php
/**
 * Cria ou atualiza a conta mestre de administração (Painel do Mestre).
 * Progresso normal — sem desbloquear fases automaticamente.
 *
 * Uso:
 *   php database/seed-conta-demo.php
 */

declare(strict_types=1);

// Só via linha de comando — nunca pela web. Sem esta guarda, qualquer um
// poderia (re)criar a conta de administrador acessando este arquivo pela URL.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script só pode ser executado via linha de comando.');
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

// Credenciais por variável de ambiente; os valores abaixo são apenas o padrão
// de conveniência para desenvolvimento local. Defina DEMO_EMAIL/DEMO_SENHA no
// ambiente antes de rodar em qualquer lugar que não seja a sua máquina.
define('DEMO_EMAIL', getenv('DEMO_EMAIL') ?: 'masterboss@boss.com');
define('DEMO_SENHA', getenv('DEMO_SENHA') ?: 'qwe123');
const DEMO_NOME_USUARIO = 'Master Boss';
const DEMO_NOME_HEROI = 'Boss Explorer';

$pdo = getConnection();

$totalFases = (int) $pdo->query('SELECT COUNT(*) FROM fases')->fetchColumn();
if ($totalFases === 0) {
    fwrite(STDERR, "Banco vazio. Rode: php database/migrate.php\n");
    exit(1);
}

$senhaHash = password_hash(DEMO_SENHA, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
$stmt->execute([DEMO_EMAIL]);
$usuarioId = $stmt->fetchColumn();

if ($usuarioId) {
    $pdo->prepare('UPDATE usuarios SET nome = ?, senha_hash = ?, papel = ? WHERE id = ?')
        ->execute([DEMO_NOME_USUARIO, $senhaHash, 'mestre', $usuarioId]);
    $usuarioId = (int) $usuarioId;
} else {
    $pdo->prepare('INSERT INTO usuarios (nome, email, senha_hash, papel) VALUES (?, ?, ?, ?)')
        ->execute([DEMO_NOME_USUARIO, DEMO_EMAIL, $senhaHash, 'mestre']);
    $usuarioId = (int) $pdo->lastInsertId();
}

$classe = 'ranger';
$base = CLASSES[$classe];

$stmt = $pdo->prepare('SELECT id FROM personagens WHERE usuario_id = ?');
$stmt->execute([$usuarioId]);
$personagemId = $stmt->fetchColumn();

$dadosPers = [
    'nome'       => DEMO_NOME_HEROI,
    'classe'     => $classe,
    'nivel'      => 1,
    'xp'         => 0,
    'hp_max'     => $base['hp'],
    'hp_atual'   => $base['hp'],
    'mp_max'     => $base['mp'],
    'mp_atual'   => $base['mp'],
    'ouro'       => 50,
    'reputacao'  => 0,
    'capitulo'   => 0,
];

if ($personagemId) {
    $personagemId = (int) $personagemId;
    $pdo->prepare(
        'UPDATE personagens SET nome = ?, classe = ?, nivel = ?, xp = ?, hp_max = ?, hp_atual = ?,
         mp_max = ?, mp_atual = ?, ouro = ?, reputacao = ?, capitulo = ? WHERE id = ?'
    )->execute([...array_values($dadosPers), $personagemId]);
} else {
    $pdo->prepare(
        'INSERT INTO personagens
         (usuario_id, nome, classe, nivel, xp, hp_max, hp_atual, mp_max, mp_atual, ouro, reputacao, capitulo)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    )->execute([$usuarioId, ...array_values($dadosPers)]);
    $personagemId = (int) $pdo->lastInsertId();
}

$pdo->prepare('DELETE FROM progresso_fases WHERE personagem_id = ?')->execute([$personagemId]);
$pdo->prepare('DELETE FROM inventario WHERE personagem_id = ?')->execute([$personagemId]);
$pdo->prepare('DELETE FROM conquistas_personagem WHERE personagem_id = ?')->execute([$personagemId]);
$pdo->prepare('DELETE FROM escolhas WHERE personagem_id = ?')->execute([$personagemId]);

$insInv = $pdo->prepare(
    'INSERT INTO inventario (personagem_id, item_id, quantidade, equipado) VALUES (?, ?, ?, 0)'
);
$fragmento = $pdo->query("SELECT id FROM itens WHERE svg_slug = 'item-fragmento-ia'")->fetchColumn();
if ($fragmento) {
    $insInv->execute([$personagemId, (int) $fragmento, 3]);
}
$pocao = $pdo->query("SELECT id FROM itens WHERE svg_slug = 'item-pocao-hp'")->fetchColumn();
if ($pocao) {
    $insInv->execute([$personagemId, (int) $pocao, 2]);
}

echo "Conta mestre pronta.\n";
echo "  E-mail: " . DEMO_EMAIL . "\n";
echo "  Senha:  " . DEMO_SENHA . "\n";
echo "  Papel:  mestre — Painel em /mestre\n";
echo "  Jogo:   progresso zerado (só o Prólogo liberado)\n";
