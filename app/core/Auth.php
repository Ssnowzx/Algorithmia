<?php
/**
 * Gerencia autenticação e sessão do jogador.
 * Mantém em sessão apenas o id do usuário; os dados são relidos do banco.
 */
class Auth
{
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
        return true;
    }

    public static function logout(): void
    {
        unset($_SESSION['usuario_id'], $_SESSION['batalha']);
    }

    public static function logado(): bool
    {
        return isset($_SESSION['usuario_id']);
    }

    /**
     * Usuário autenticado (linha completa) ou null.
     */
    public static function usuario(): ?array
    {
        if (!self::logado()) {
            return null;
        }
        return (new Usuario())->findById((int) $_SESSION['usuario_id']);
    }

    /**
     * Personagem do usuário logado, ou null se ainda não criou.
     */
    public static function personagem(): ?array
    {
        if (!self::logado()) {
            return null;
        }
        return (new Personagem())->findBy('usuario_id', (int) $_SESSION['usuario_id']);
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
