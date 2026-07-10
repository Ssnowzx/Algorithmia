<?php
/**
 * Cria a conta mestre de administração (Painel do Mestre).
 *
 * Uso:
 *   php database/seed-conta-demo.php            → cria se não existir; NUNCA toca numa existente
 *   php database/seed-conta-demo.php --forcar   → recria: zera progresso e redefine a senha
 *
 * O `migrate.php` chama a função sem `--forcar`. Não é preciosismo: até 2026-07-09
 * este arquivo era carregado incondicionalmente pelo migrador e, quando a conta já
 * existia, ele reescrevia a senha e apagava progresso, inventário, conquistas e
 * escolhas. Todo deploy que rodasse o migrador destruía a conta de administrador em
 * produção e a devolvia com a senha padrão — que está publicada neste repositório.
 */

declare(strict_types=1);

// Blindagem: scripts de banco NUNCA podem ser disparados pela web (o document root
// pode acabar incluindo database/). Só rodam via linha de comando.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script só pode ser executado via linha de comando.');
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

define('DEMO_EMAIL', getenv('DEMO_EMAIL') ?: 'masterboss@boss.com');
const DEMO_NOME_USUARIO = 'Master Boss';
const DEMO_NOME_HEROI = 'Boss Explorer';

/**
 * A senha só é escolhida quando a conta é criada.
 *
 * Sem `DEMO_SENHA` no ambiente, sorteia-se uma. O padrão fixo que vivia aqui criava,
 * em qualquer instalação nova, um administrador com senha conhecida por qualquer um
 * que lesse o repositório.
 *
 * @return array{0:string,1:bool} a senha e se ela foi sorteada
 */
function senhaDaContaDemo(): array
{
    $doAmbiente = getenv('DEMO_SENHA');
    if (is_string($doAmbiente) && $doAmbiente !== '') {
        return [$doAmbiente, false];
    }

    return [bin2hex(random_bytes(9)), true];
}

/**
 * Garante a conta mestre.
 *
 * @param  bool  $forcar  Zera progresso e redefine a senha de uma conta existente.
 * @return string Uma linha de relatório para o operador.
 */
function semearContaDemo(PDO $pdo, bool $forcar = false): string
{
    $totalFases = (int) $pdo->query('SELECT COUNT(*) FROM fases')->fetchColumn();
    if ($totalFases === 0) {
        return 'Banco sem conteúdo — rode o migrador antes. Conta mestre não criada.';
    }

    $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
    $stmt->execute([DEMO_EMAIL]);
    $usuarioId = $stmt->fetchColumn();

    if ($usuarioId && ! $forcar) {
        return sprintf(
            "Conta mestre já existe (%s) — preservada, com senha e progresso intactos.\n"
            . '  Para zerá-la de propósito: php database/seed-conta-demo.php --forcar',
            DEMO_EMAIL
        );
    }

    [$senha, $sorteada] = senhaDaContaDemo();
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    if ($usuarioId) {
        $usuarioId = (int) $usuarioId;
        $pdo->prepare('UPDATE usuarios SET nome = ?, senha_hash = ?, papel = ? WHERE id = ?')
            ->execute([DEMO_NOME_USUARIO, $senhaHash, 'mestre', $usuarioId]);
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
        'nome' => DEMO_NOME_HEROI,
        'classe' => $classe,
        'nivel' => 1,
        'xp' => 0,
        'hp_max' => $base['hp'],
        'hp_atual' => $base['hp'],
        'mp_max' => $base['mp'],
        'mp_atual' => $base['mp'],
        'ouro' => 50,
        'reputacao' => 0,
        'capitulo' => 0,
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

    $relatorio = sprintf(
        "Conta mestre %s.\n  E-mail: %s\n  Senha:  %s\n  Papel:  mestre — Painel em /mestre\n"
        . '  Jogo:   progresso zerado (só o Prólogo liberado)',
        $forcar ? 'recriada' : 'criada',
        DEMO_EMAIL,
        $senha
    );

    if ($sorteada) {
        $relatorio .= "\n\n  ⚠ A senha foi SORTEADA e não será mostrada de novo. Guarde-a agora.\n"
            . '    Para escolhê-la você mesmo, defina DEMO_SENHA no ambiente antes de rodar.';
    }

    return $relatorio;
}

// Só executa quando este arquivo é o script chamado. Sob `require` (o migrador), o
// arquivo apenas define as funções, e quem decide se semeia é o chamador.
$entrada = realpath($_SERVER['SCRIPT_FILENAME'] ?? ($argv[0] ?? ''));
if ($entrada === realpath(__FILE__)) {
    echo semearContaDemo(getConnection(), in_array('--forcar', $argv, true)), "\n";
}
