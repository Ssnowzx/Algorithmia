<?php
/**
 * Conexão com o banco de dados MySQL via PDO.
 * Jogo Algorithmia — A Lenda dos Cinco Mestres.
 */

/**
 * As credenciais podem vir de variáveis de ambiente (úteis ao hospedar em um
 * servidor/host) e caem para os padrões de desenvolvimento local se ausentes.
 * Em produção, defina DB_HOST/DB_NAME/DB_USER/DB_PASS no ambiente do servidor.
 */
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'algorithmia');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Abre (e reaproveita) uma conexão PDO única para a requisição.
 *
 * @param bool $semBanco Quando verdadeiro, conecta ao servidor sem selecionar
 *                       o schema — usado pelo migrador para criar o banco.
 */
function getConnection(bool $semBanco = false): PDO
{
    static $conexao = null;

    if ($conexao instanceof PDO && !$semBanco) {
        return $conexao;
    }

    $dsn = "mysql:host=" . DB_HOST . ($semBanco ? '' : ';dbname=' . DB_NAME) . ";charset=" . DB_CHARSET;

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        die('<div style="background:#13132b;color:#ff6b6b;padding:2rem;font-family:monospace;border-radius:12px;margin:2rem;max-width:640px;">
            <h2>⚠️ Erro de Conexão com o Banco de Dados</h2>
            <p>' . htmlspecialchars($e->getMessage(), ENT_QUOTES) . '</p>
            <p style="color:#9aa;">Verifique se o MySQL está rodando e execute <code>php database/migrate.php</code> para criar o banco <code>algorithmia</code>.</p>
        </div>');
    }

    if (!$semBanco) {
        $conexao = $pdo;
    }

    return $pdo;
}
