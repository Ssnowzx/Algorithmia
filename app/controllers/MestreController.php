<?php
/**
 * Painel do Mestre (área administrativa). Acesso restrito a usuários com
 * papel 'mestre'. Oferece CRUD completo de fases, desafios e itens.
 */
class MestreController extends Controller
{
    public function __construct()
    {
        Auth::exigirMestre();
    }

    public function index(): void
    {
        $this->view('mestre/index', [
            'pageTitle' => 'Painel do Mestre',
            'totais' => [
                'fases'      => (new Fase())->count(),
                'desafios'   => (new Desafio())->count(),
                'itens'      => (new Item())->count(),
                'mestres'    => (new Mestre())->count(),
                'jogadores'  => (new Personagem())->count(),
            ],
        ]);
    }

    // ===================== DESAFIOS =====================

    public function desafios(): void
    {
        $desafios = (new Desafio())->todosComFase();

        $this->view('mestre/desafios', [
            'pageTitle' => 'Gerir Desafios',
            'desafios'  => $desafios,
        ]);
    }

    public function novoDesafio(): void
    {
        $this->view('mestre/desafio-form', [
            'pageTitle' => 'Novo Desafio',
            'desafio'   => null,
            'fases'     => (new Fase())->todasOrdenadas(),
        ]);
    }

    public function editarDesafio(string $id = '0'): void
    {
        $desafio = (new Desafio())->findById((int) $id);
        if (!$desafio) {
            $this->redirect('mestre/desafios');
        }
        $this->view('mestre/desafio-form', [
            'pageTitle' => 'Editar Desafio',
            'desafio'   => $desafio,
            'fases'     => (new Fase())->todasOrdenadas(),
        ]);
    }

    public function salvarDesafio(): void
    {
        $this->exigirCsrf();
        $dados = $this->lerDesafioDoPost();
        $id = (int) ($_POST['id'] ?? 0);

        $model = new Desafio();
        if ($id > 0) {
            $model->update($id, $dados);
            $this->flash('sucesso', 'Desafio atualizado.');
        } else {
            $model->create($dados);
            $this->flash('sucesso', 'Desafio criado.');
        }
        $this->redirect('mestre/desafios');
    }

    public function excluirDesafio(string $id = '0'): void
    {
        $this->exigirCsrf();
        (new Desafio())->delete((int) $id);
        $this->flash('info', 'Desafio removido.');
        $this->redirect('mestre/desafios');
    }

    /**
     * Converte os campos do formulário num registro pronto para o banco,
     * codificando opções e resposta como JSON conforme o tipo.
     */
    private function lerDesafioDoPost(): array
    {
        $tipo = $_POST['tipo'] ?? 'multipla';

        // Opções: uma por linha.
        $opcoesTexto = trim($_POST['opcoes'] ?? '');
        $opcoes = $opcoesTexto === '' ? [] : array_values(array_filter(array_map('trim', explode("\n", $opcoesTexto)), fn($x) => $x !== ''));

        // Resposta: interpretada conforme o tipo.
        $respostaBruta = trim($_POST['resposta'] ?? '');
        switch ($tipo) {
            case 'vf':
                $resposta = in_array(strtolower($respostaBruta), ['true', 'v', '1', 'verdadeiro'], true);
                break;
            case 'multipla':
            case 'erro':
                $resposta = (int) $respostaBruta;
                break;
            case 'completar':
                $resposta = array_values(array_filter(array_map('trim', explode(',', $respostaBruta)), fn($x) => $x !== ''));
                break;
            case 'ordenar':
            case 'arrastar':
                $resposta = array_map('intval', array_filter(array_map('trim', explode(',', $respostaBruta)), fn($x) => $x !== ''));
                break;
            default:
                $resposta = $respostaBruta;
        }

        return [
            'fase_id'     => (int) ($_POST['fase_id'] ?? 0),
            'ordem'       => (int) ($_POST['ordem'] ?? 1),
            'tipo'        => $tipo,
            'assunto'     => $_POST['assunto'] ?? 'logica',
            'pergunta'    => trim($_POST['pergunta'] ?? ''),
            'codigo'      => trim($_POST['codigo'] ?? '') ?: null,
            'opcoes'      => $opcoes ? json_encode($opcoes, JSON_UNESCAPED_UNICODE) : null,
            'resposta'    => json_encode($resposta, JSON_UNESCAPED_UNICODE),
            'explicacao'  => trim($_POST['explicacao'] ?? ''),
            'dificuldade' => max(1, min(5, (int) ($_POST['dificuldade'] ?? 1))),
        ];
    }

    // ===================== FASES =====================

    public function fases(): void
    {
        $this->view('mestre/fases', [
            'pageTitle' => 'Gerir Fases',
            'fases'     => (new Fase())->comMestre(),
        ]);
    }

    public function novaFase(): void
    {
        $this->view('mestre/fase-form', [
            'pageTitle' => 'Nova Fase',
            'fase'      => null,
            'mestres'   => (new Mestre())->todosOrdenados(),
            'fases'     => (new Fase())->todasOrdenadas(),
            'itens'     => (new Item())->findAll(),
        ]);
    }

    public function editarFase(string $id = '0'): void
    {
        $fase = (new Fase())->findById((int) $id);
        if (!$fase) {
            $this->redirect('mestre/fases');
        }
        $this->view('mestre/fase-form', [
            'pageTitle' => 'Editar Fase',
            'fase'      => $fase,
            'mestres'   => (new Mestre())->todosOrdenados(),
            'fases'     => (new Fase())->todasOrdenadas(),
            'itens'     => (new Item())->findAll(),
        ]);
    }

    public function salvarFase(): void
    {
        $this->exigirCsrf();
        $dados = [
            'mestre_id'        => ($_POST['mestre_id'] ?? '') !== '' ? (int) $_POST['mestre_id'] : null,
            'ordem_global'     => (int) ($_POST['ordem_global'] ?? 1),
            'nome'             => trim($_POST['nome'] ?? ''),
            'tipo'             => $_POST['tipo'] ?? 'licao',
            'descricao'        => trim($_POST['descricao'] ?? ''),
            'inimigo_nome'     => trim($_POST['inimigo_nome'] ?? '') ?: null,
            'inimigo_svg'      => trim($_POST['inimigo_svg'] ?? '') ?: null,
            'inimigo_hp'       => (int) ($_POST['inimigo_hp'] ?? 60),
            'inimigo_ataque'   => (int) ($_POST['inimigo_ataque'] ?? 10),
            'xp_recompensa'    => (int) ($_POST['xp_recompensa'] ?? 50),
            'ouro_recompensa'  => (int) ($_POST['ouro_recompensa'] ?? 20),
            'item_drop_id'     => ($_POST['item_drop_id'] ?? '') !== '' ? (int) $_POST['item_drop_id'] : null,
            'requisito_fase_id'=> ($_POST['requisito_fase_id'] ?? '') !== '' ? (int) $_POST['requisito_fase_id'] : null,
        ];
        $id = (int) ($_POST['id'] ?? 0);
        $model = new Fase();
        if ($id > 0) {
            $model->update($id, $dados);
            $this->flash('sucesso', 'Fase atualizada.');
        } else {
            $model->create($dados);
            $this->flash('sucesso', 'Fase criada.');
        }
        $this->redirect('mestre/fases');
    }

    public function excluirFase(string $id = '0'): void
    {
        $this->exigirCsrf();
        (new Fase())->delete((int) $id);
        $this->flash('info', 'Fase removida (e seus desafios).');
        $this->redirect('mestre/fases');
    }

    // ===================== ITENS =====================

    public function itens(): void
    {
        $this->view('mestre/itens', [
            'pageTitle' => 'Gerir Itens',
            'itens'     => (new Item())->findAll(),
        ]);
    }

    public function novoItem(): void
    {
        $this->view('mestre/item-form', ['pageTitle' => 'Novo Item', 'item' => null]);
    }

    public function editarItem(string $id = '0'): void
    {
        $item = (new Item())->findById((int) $id);
        if (!$item) {
            $this->redirect('mestre/itens');
        }
        $this->view('mestre/item-form', ['pageTitle' => 'Editar Item', 'item' => $item]);
    }

    public function salvarItem(): void
    {
        $this->exigirCsrf();
        // Monta o efeito JSON a partir dos campos numéricos.
        $efeito = [];
        foreach (['ataque', 'defesa', 'cura_hp', 'cura_mp'] as $chave) {
            $v = (int) ($_POST['ef_' . $chave] ?? 0);
            if ($v !== 0) { $efeito[$chave] = $v; }
        }

        $dados = [
            'nome'      => trim($_POST['nome'] ?? ''),
            'descricao' => trim($_POST['descricao'] ?? ''),
            'tipo'      => $_POST['tipo'] ?? 'arma',
            'efeito'    => $efeito ? json_encode($efeito) : null,
            'preco'     => (int) ($_POST['preco'] ?? 0),
            'svg_slug'  => trim($_POST['svg_slug'] ?? 'item-generico'),
            'raridade'  => $_POST['raridade'] ?? 'comum',
            'compravel' => isset($_POST['compravel']) ? 1 : 0,
        ];
        $id = (int) ($_POST['id'] ?? 0);
        $model = new Item();
        if ($id > 0) {
            $model->update($id, $dados);
            $this->flash('sucesso', 'Item atualizado.');
        } else {
            $model->create($dados);
            $this->flash('sucesso', 'Item criado.');
        }
        $this->redirect('mestre/itens');
    }

    public function excluirItem(string $id = '0'): void
    {
        $this->exigirCsrf();
        (new Item())->delete((int) $id);
        $this->flash('info', 'Item removido.');
        $this->redirect('mestre/itens');
    }
}
