<?php
/**
 * Bootstrap dos testes de caracterização.
 *
 * Espelha o que index.php faz (configs + autoload), menos sessão e headers —
 * em CLI, $_SESSION é apenas um array, o que torna o motor de batalha
 * executável sem servidor web.
 */

declare(strict_types=1);

// O banco de teste é sempre um *_test. Definido ANTES de config/db.php, que lê
// as credenciais via getenv() e as congela em constantes.
putenv('DB_NAME=' . (getenv('ALGORITHMIA_DB_TESTE') ?: 'algorithmia_test'));
putenv('DB_USER=' . (getenv('DB_USER') ?: 'root'));
if (getenv('DB_PASS') === false) {
    putenv('DB_PASS=');
}
putenv('APP_ENV=test');

$raiz = dirname(__DIR__);

// config/config.php monta BASE_URL a partir do SCRIPT_NAME da requisição.
$_SERVER['SCRIPT_NAME'] ??= '/index.php';
$_SESSION = [];

require_once $raiz . '/config/db.php';

// Trava de segurança: um DB_NAME errado apagaria o banco de desenvolvimento nos
// truncates de Mundo::limpar(). Sem banco *_test, nenhum teste roda.
if (!str_ends_with(DB_NAME, '_test')) {
    fwrite(STDERR, sprintf(
        "ABORTADO: os testes só rodam contra um banco terminado em '_test' (DB_NAME=%s).\n",
        DB_NAME
    ));
    exit(1);
}

require_once $raiz . '/config/config.php';
require_once $raiz . '/config/bestiario.php';
require_once $raiz . '/app/core/helpers.php';

spl_autoload_register(static function (string $classe) use ($raiz): void {
    foreach (['core', 'models', 'services'] as $pasta) {
        $arquivo = $raiz . '/app/' . $pasta . '/' . $classe . '.php';
        if (is_file($arquivo)) {
            require_once $arquivo;
            return;
        }
    }
});

require_once $raiz . '/vendor/autoload.php';
