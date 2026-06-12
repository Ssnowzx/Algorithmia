<?php
/**
 * Roteador simples no padrão Front Controller.
 * Traduz ?url=controller/metodo/param em uma chamada de método.
 */
class Router
{
    public function despachar(string $url): void
    {
        $url = trim(filter_var($url, FILTER_SANITIZE_URL), '/');
        $partes = $url === '' ? [] : explode('/', $url);

        $nomeController = !empty($partes[0]) ? ucfirst($partes[0]) . 'Controller' : 'HomeController';
        $metodo = $partes[1] ?? 'index';
        $params = array_slice($partes, 2);

        $arquivo = __DIR__ . '/../controllers/' . $nomeController . '.php';
        if (!is_file($arquivo)) {
            $this->naoEncontrado();
            return;
        }

        require_once $arquivo;
        if (!class_exists($nomeController)) {
            $this->naoEncontrado();
            return;
        }

        $controller = new $nomeController();
        if (!method_exists($controller, $metodo) || str_starts_with($metodo, '_')) {
            $this->naoEncontrado();
            return;
        }

        $controller->$metodo(...$params);
    }

    private function naoEncontrado(): void
    {
        http_response_code(404);
        require __DIR__ . '/../views/errors/404.php';
    }
}
