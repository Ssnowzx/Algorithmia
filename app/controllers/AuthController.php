<?php
/**
 * Cadastro, login, logout e criação do personagem inicial.
 */
class AuthController extends Controller
{
    /** Quantidade de Fragmentos da IA que todo aprendiz recebe no início. */
    private const FRAGMENTOS_INICIAIS = 3;

    public function login(): void
    {
        if (Auth::logado()) {
            $this->redirect('mapa');
        }

        $erro = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->exigirCsrf();
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';
            if (Auth::login($email, $senha)) {
                $this->redirect('mapa');
            }
            $erro = 'E-mail ou senha incorretos.';
        }

        $this->view('auth/login', ['_semLayout' => true, 'erro' => $erro]);
    }

    public function registro(): void
    {
        if (Auth::logado()) {
            $this->redirect('mapa');
        }

        $erros = [];
        $dados = ['nome' => '', 'email' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->exigirCsrf();
            $dados['nome']  = trim($_POST['nome'] ?? '');
            $dados['email'] = trim($_POST['email'] ?? '');
            $senha          = $_POST['senha'] ?? '';
            $confirma       = $_POST['confirma'] ?? '';

            if (mb_strlen($dados['nome']) < 3) {
                $erros[] = 'O nome deve ter ao menos 3 caracteres.';
            }
            if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                $erros[] = 'Informe um e-mail válido.';
            } elseif ((new Usuario())->emailExiste($dados['email'])) {
                $erros[] = 'Este e-mail já está cadastrado.';
            }
            if (mb_strlen($senha) < 6) {
                $erros[] = 'A senha deve ter ao menos 6 caracteres.';
            }
            if ($senha !== $confirma) {
                $erros[] = 'As senhas não conferem.';
            }

            if (!$erros) {
                $id = (new Usuario())->create([
                    'nome'       => $dados['nome'],
                    'email'      => $dados['email'],
                    'senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
                    'papel'      => 'jogador',
                ]);
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $id;
                $this->redirect('auth/criarPersonagem');
            }
        }

        $this->view('auth/registro', ['_semLayout' => true, 'erros' => $erros, 'dados' => $dados]);
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('');
    }

    /**
     * Tela de criação do herói: escolha de nome e classe.
     */
    public function criarPersonagem(): void
    {
        Auth::exigirLogin();
        if (Auth::personagem()) {
            $this->redirect('mapa');
        }

        $erros = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->exigirCsrf();
            $nome   = trim($_POST['nome'] ?? '');
            $classe = $_POST['classe'] ?? '';

            if (mb_strlen($nome) < 2) {
                $erros[] = 'Dê um nome ao seu herói (mínimo 2 caracteres).';
            }
            if (!isset(CLASSES[$classe])) {
                $erros[] = 'Escolha uma classe válida.';
            }

            if (!$erros) {
                $this->criarHeroi((int) $_SESSION['usuario_id'], $nome, $classe);
                $this->redirect('historia/ver/1');
            }
        }

        $this->view('auth/criar-personagem', ['_semLayout' => true, 'erros' => $erros, 'classes' => CLASSES]);
    }

    /**
     * Persiste o personagem com os atributos da classe e entrega os itens iniciais.
     */
    private function criarHeroi(int $usuarioId, string $nome, string $classe): void
    {
        $base = CLASSES[$classe];
        $personagemId = (new Personagem())->create([
            'usuario_id' => $usuarioId,
            'nome'       => $nome,
            'classe'     => $classe,
            'nivel'      => 1,
            'xp'         => 0,
            'hp_max'     => $base['hp'],
            'hp_atual'   => $base['hp'],
            'mp_max'     => $base['mp'],
            'mp_atual'   => $base['mp'],
            'ouro'       => 50,
            'reputacao'  => 0,
            'capitulo'   => 0,
        ]);

        $inventario = new Inventario();
        $itemModel = new Item();

        // Três Fragmentos da IA Ancestral (o item-chave da narrativa).
        $fragmento = $itemModel->findBy('svg_slug', 'item-fragmento-ia');
        if ($fragmento) {
            $inventario->adicionar($personagemId, (int) $fragmento['id'], self::FRAGMENTOS_INICIAIS);
        }
        // Duas poções de vida menores para começar.
        $pocao = $itemModel->findBy('svg_slug', 'item-pocao-hp');
        if ($pocao) {
            $inventario->adicionar($personagemId, (int) $pocao['id'], 2);
        }
    }
}
