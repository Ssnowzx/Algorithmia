<?php
/**
 * Algorithmia — A Lenda dos Cinco Mestres
 * Ponto de entrada único (Front Controller).
 *
 * Todas as requisições passam por aqui: index.php?url=controller/metodo/param
 */

declare(strict_types=1);

// Cookie de sessão endurecido: HttpOnly (bloqueia leitura via JS/XSS),
// SameSite=Lax (mitiga CSRF) e Secure quando a conexão é HTTPS.
$ehHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'secure'   => $ehHttps,
    'samesite' => 'Lax',
]);
session_start();

// Headers de segurança aplicados a toda resposta.
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/core/helpers.php';

// Autoload do núcleo, models e services. Controllers são carregados pelo Router.
spl_autoload_register(function (string $classe): void {
    foreach (['core', 'models', 'services'] as $pasta) {
        $arquivo = __DIR__ . '/app/' . $pasta . '/' . $classe . '.php';
        if (is_file($arquivo)) {
            require_once $arquivo;
            return;
        }
    }
});

$url = $_GET['url'] ?? '';
(new Router())->despachar($url);
