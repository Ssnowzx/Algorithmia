<?php
/**
 * Controller base: utilidades de renderização, redirecionamento e respostas.
 */
class Controller
{
    /**
     * Renderiza uma view dentro do layout principal (header + footer).
     * Passe ['_semLayout' => true] em $data para renderizar a view crua.
     */
    protected function view(string $caminho, array $data = []): void
    {
        $semLayout = !empty($data['_semLayout']);
        unset($data['_semLayout']);
        extract($data, EXTR_SKIP);

        $arquivoView = __DIR__ . '/../views/' . $caminho . '.php';
        if (!is_file($arquivoView)) {
            die('View não encontrada: ' . e($caminho));
        }

        if ($semLayout) {
            require $arquivoView;
            return;
        }

        require __DIR__ . '/../views/layout/header.php';
        require $arquivoView;
        require __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Redireciona para uma rota interna.
     */
    protected function redirect(string $rota): void
    {
        header('Location: ' . url($rota));
        exit;
    }

    /**
     * Resposta JSON (usada pelos endpoints AJAX da batalha).
     */
    protected function json(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Guarda uma mensagem flash para exibir após um redirect.
     */
    protected function flash(string $tipo, string $mensagem): void
    {
        $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
    }

    /**
     * Lê o corpo JSON de uma requisição AJAX.
     */
    protected function corpoJson(): array
    {
        $bruto = file_get_contents('php://input');
        $dados = json_decode($bruto, true);
        return is_array($dados) ? $dados : [];
    }

    /**
     * Exige POST com token CSRF válido para ações que mudam estado.
     * Bloqueia CSRF via GET (antes só validava o POST; um GET passava direto).
     */
    protected function exigirCsrf(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valido()) {
            http_response_code(419);
            die('Sessão expirada ou requisição inválida. Volte e tente novamente.');
        }
    }

    /**
     * Valida o token CSRF de endpoints AJAX (JSON), lido do cabeçalho
     * X-CSRF-Token — pois o corpo JSON não popula $_POST. Responde 419 em JSON.
     */
    protected function exigirCsrfAjax(): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!is_string($token) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
            $this->json(['erro' => 'Sessão expirada. Recarregue a página (F5) e tente de novo.'], 419);
        }
    }
}
