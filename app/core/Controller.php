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
     * Aborta requisições POST sem token CSRF válido.
     */
    protected function exigirCsrf(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_valido()) {
            http_response_code(419);
            die('Sessão expirada ou token inválido. Volte e tente novamente.');
        }
    }
}
