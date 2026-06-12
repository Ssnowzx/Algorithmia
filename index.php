<?php
/**
 * Algorithmia — A Lenda dos Cinco Mestres
 * Ponto de entrada único (Front Controller).
 *
 * Todas as requisições passam por aqui: index.php?url=controller/metodo/param
 */

declare(strict_types=1);

session_start();

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
