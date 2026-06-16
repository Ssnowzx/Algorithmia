<?php
/**
 * Cria ou atualiza a conta demo com mapa desbloqueado e papel mestre.
 *
 * Uso:
 *   php database/seed-conta-demo.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

const DEMO_EMAIL = 'masterboss@boss.com';
const DEMO_SENHA = 'qwe123';
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
$nivel = 15;
$xp = xpParaNivel($nivel);
$hpMax = $base['hp'] + ($nivel - 1) * 15;
$mpMax = $base['mp'] + ($nivel - 1) * 8;

$stmt = $pdo->prepare('SELECT id FROM personagens WHERE usuario_id = ?');
$stmt->execute([$usuarioId]);
$personagemId = $stmt->fetchColumn();

$dadosPers = [
    'nome'       => DEMO_NOME_HEROI,
    'classe'     => $classe,
    'nivel'      => $nivel,
    'xp'         => $xp,
    'hp_max'     => $hpMax,
    'hp_atual'   => $hpMax,
    'mp_max'     => $mpMax,
    'mp_atual'   => $mpMax,
    'ouro'       => 99999,
    'reputacao'  => 0,
    'capitulo'   => 5,
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

$faseIds = $pdo->query('SELECT id FROM fases ORDER BY ordem_global')->fetchAll(PDO::FETCH_COLUMN);

$pdo->prepare('DELETE FROM progresso_fases WHERE personagem_id = ?')->execute([$personagemId]);

$insProg = $pdo->prepare(
    'INSERT INTO progresso_fases (personagem_id, fase_id, estrelas, acertos, erros, usou_ia)
     VALUES (?, ?, 3, 10, 0, 0)'
);
foreach ($faseIds as $fid) {
    $insProg->execute([$personagemId, (int) $fid]);
}

$pdo->prepare('DELETE FROM inventario WHERE personagem_id = ?')->execute([$personagemId]);

$insInv = $pdo->prepare(
    'INSERT INTO inventario (personagem_id, item_id, quantidade, equipado) VALUES (?, ?, ?, ?)'
);
foreach ($pdo->query('SELECT id, tipo FROM itens')->fetchAll() as $row) {
    $qtd = match ($row['tipo']) {
        'pocao'    => 10,
        'especial' => 5,
        default    => 1,
    };
    $equip = in_array($row['tipo'], ['arma', 'escudo', 'acessorio'], true) ? 1 : 0;
    $insInv->execute([$personagemId, (int) $row['id'], $qtd, $equip]);
}

$pdo->prepare('DELETE FROM conquistas_personagem WHERE personagem_id = ?')->execute([$personagemId]);

$insConq = $pdo->prepare(
    'INSERT IGNORE INTO conquistas_personagem (personagem_id, conquista_id) VALUES (?, ?)'
);
foreach (
    $pdo->query("SELECT id FROM conquistas WHERE codigo NOT IN ('final_mestre','final_singularidade','final_equilibrio')")->fetchAll(PDO::FETCH_COLUMN) as $cid
) {
    $insConq->execute([$personagemId, (int) $cid]);
}

$pdo->prepare('DELETE FROM escolhas WHERE personagem_id = ?')->execute([$personagemId]);

echo "Conta demo pronta.\n";
echo "  E-mail: " . DEMO_EMAIL . "\n";
echo "  Senha:  " . DEMO_SENHA . "\n";
echo "  Papel:  mestre — Painel em /mestre\n";
echo "  Fases:  " . count($faseIds) . " concluidas (3 estrelas)\n";
echo "  Final:  acesse /historia/final para testar os 3 epilogos\n";
