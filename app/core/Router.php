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

        // Só roteia para métodos PÚBLICOS de instância do próprio controller.
        // method_exists() sozinho deixava chamar métodos protected herdados de
        // Controller (view/redirect/json/...), causando erro fatal exposto.
        if (str_starts_with($metodo, '_') || !$this->metodoRoteavel($nomeController, $metodo)) {
            $this->naoEncontrado();
            return;
        }

        $controller = new $nomeController();
        $controller->$metodo(...$params);
    }

    /**
     * Verdadeiro só para métodos públicos, não estáticos e não herdados da
     * classe base Controller (que são utilidades internas, não rotas).
     */
    private function metodoRoteavel(string $classe, string $metodo): bool
    {
        if (!method_exists($classe, $metodo)) {
            return false;
        }
        $ref = new ReflectionMethod($classe, $metodo);
        return $ref->isPublic()
            && !$ref->isStatic()
            && $ref->getDeclaringClass()->getName() !== Controller::class;
    }

    private function naoEncontrado(): void
    {
        http_response_code(404);
        require __DIR__ . '/../views/errors/404.php';
    }
}
