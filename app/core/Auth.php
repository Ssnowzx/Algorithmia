<?php
/**
 * Gerencia autenticação e sessão do jogador.
 * Mantém em sessão apenas o id do usuário; os dados são relidos do banco.
 */
class Auth
{
    /**
     * Cache por requisição: usuario() e personagem() eram consultados várias
     * vezes na mesma página (exigirLogin + ehMestre + HUD do header), gerando
     * SELECTs idênticos repetidos. Memoizamos e invalidamos no login/logout.
     */
    private static ?array $usuarioMemo = null;
    private static bool $usuarioMemoFeito = false;
    private static ?array $personagemMemo = null;
    private static bool $personagemMemoFeito = false;

    private static function limparMemo(): void
    {
        self::$usuarioMemo = null;
        self::$usuarioMemoFeito = false;
        self::$personagemMemo = null;
        self::$personagemMemoFeito = false;
    }

    /**
     * Autentica por email/senha. Regenera o id de sessão no sucesso.
     */
    public static function login(string $email, string $senha): bool
    {
        $usuario = (new Usuario())->findBy('email', $email);
        if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
            return false;
        }
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int) $usuario['id'];
        self::limparMemo();
        return true;
    }

    public static function logout(): void
    {
        unset($_SESSION['usuario_id'], $_SESSION['batalha']);
        self::limparMemo();
    }

    public static function logado(): bool
    {
        return isset($_SESSION['usuario_id']);
    }

    /**
     * Usuário autenticado (linha completa) ou null. Memoizado por requisição.
     */
    public static function usuario(): ?array
    {
        if (self::$usuarioMemoFeito) {
            return self::$usuarioMemo;
        }
        self::$usuarioMemoFeito = true;
        if (!self::logado()) {
            return self::$usuarioMemo = null;
        }
        return self::$usuarioMemo = (new Usuario())->findById((int) $_SESSION['usuario_id']);
    }

    /**
     * Personagem do usuário logado, ou null se ainda não criou. Memoizado.
     */
    public static function personagem(): ?array
    {
        if (self::$personagemMemoFeito) {
            return self::$personagemMemo;
        }
        self::$personagemMemoFeito = true;
        if (!self::logado()) {
            return self::$personagemMemo = null;
        }
        return self::$personagemMemo = (new Personagem())->findBy('usuario_id', (int) $_SESSION['usuario_id']);
    }

    public static function ehMestre(): bool
    {
        $u = self::usuario();
        return $u !== null && $u['papel'] === 'mestre';
    }

    /**
     * Garante login; caso contrário redireciona para a tela de login.
     *
     * Também trata sessões órfãs: se o id em sessão não corresponde mais a um
     * usuário real (ex.: o banco foi recriado), encerra a sessão em vez de deixar
     * uma falha de chave estrangeira estourar adiante.
     */
    public static function exigirLogin(): void
    {
        if (!self::logado()) {
            header('Location: ' . url('auth/login'));
            exit;
        }
        if (self::usuario() === null) {
            self::logout();
            $_SESSION['flash'] = ['tipo' => 'info', 'mensagem' => 'Sua sessão expirou (os dados foram atualizados). Entre novamente.'];
            header('Location: ' . url('auth/login'));
            exit;
        }
    }

    /**
     * Garante que exista um personagem criado antes de jogar.
     */
    public static function exigirPersonagem(): array
    {
        self::exigirLogin();
        $p = self::personagem();
        if (!$p) {
            header('Location: ' . url('auth/criarPersonagem'));
            exit;
        }
        return $p;
    }

    /**
     * Garante papel de mestre para acessar o painel administrativo.
     */
    public static function exigirMestre(): void
    {
        self::exigirLogin();
        if (!self::ehMestre()) {
            http_response_code(403);
            die('<p style="font-family:monospace;padding:2rem;">403 — Apenas Mestres da Ordem podem acessar esta área.</p>');
        }
    }
}
