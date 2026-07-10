<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dominio\Operacao\ServicoDeAuditoria;
use App\Dominio\Relatorios\RelatorioDeTurma;
use App\Models\Turma;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Turmas e relatórios pedagógicos.
 *
 * O RLS já garante que nada daqui atravessa a fronteira entre instituições. O que este
 * controller precisa garantir é a fronteira **dentro** da instituição: um professor não
 * abre a turma de outro. Isso é `Gate`/`TurmaPolicy`, e é a primeira linha de cada método
 * — não uma checagem escondida num `where` de consulta, que a próxima consulta esqueceria.
 */
final class TurmaController extends Controller
{
    public function __construct(private readonly ServicoDeAuditoria $auditoria) {}

    public function index(): View
    {
        Gate::authorize('verQualquer', Turma::class);

        /** @var Usuario $usuario */
        $usuario = Auth::user();

        // O mestre administra a escola inteira; o professor vê o que leciona. A consulta
        // difere, mas a policy é quem decide — aqui só a obedecemos.
        $turmas = $usuario->ehMestre()
            ? Turma::query()->orderBy('nome')->withCount('alunos')->get()
            : $usuario->turmasQueLeciona()->orderBy('nome')->withCount('alunos')->get();

        return view('turmas.index', ['turmas' => $turmas, 'ehMestre' => $usuario->ehMestre()]);
    }

    public function ver(Turma $turma, RelatorioDeTurma $relatorio): View
    {
        Gate::authorize('ver', $turma);

        return view('turmas.relatorio', ['relatorio' => $relatorio->gerar($turma), 'turma' => $turma]);
    }

    // ------------------------------------------------------------------ administração

    public function criar(Request $requisicao): RedirectResponse
    {
        Gate::authorize('administrar', Turma::class);

        $dados = $requisicao->validate([
            'nome' => ['required', 'string', 'max:120'],
            // O índice único é por (tenant_id, codigo). A regra do Laravel não enxerga o
            // RLS, então ela precisa dizer o mesmo — senão o erro vira 500 no banco.
            'codigo' => ['required', 'string', 'max:20', Rule::unique('turmas', 'codigo')],
        ]);

        $turma = Turma::create($dados);

        $this->auditoria->registrar('turma.criar', 'turma', $turma->id, ['nome' => $turma->nome]);

        return redirect()->route('turmas.index')->with('sucesso', 'Turma criada.');
    }

    public function vincularProfessor(Request $requisicao, Turma $turma): RedirectResponse
    {
        Gate::authorize('administrar', Turma::class);

        $usuario = $this->usuarioComPapel($requisicao, 'professor');

        $turma->professores()->syncWithoutDetaching([$usuario->id]);

        // Quem passou a poder ler o progresso de quais alunos é exatamente o tipo de
        // mudança que ninguém lembra de ter feito, três meses depois.
        $this->auditoria->registrar('turma.professor.vincular', 'turma', $turma->id, [
            'turma' => $turma->nome,
            'professor_id' => $usuario->id,
            'professor_email' => $usuario->email,
        ]);

        return back()->with('sucesso', 'Professor vinculado.');
    }

    public function matricular(Request $requisicao, Turma $turma): RedirectResponse
    {
        Gate::authorize('administrar', Turma::class);

        $usuario = $this->usuarioComPapel($requisicao, 'jogador');

        $turma->alunos()->syncWithoutDetaching([$usuario->id]);

        $this->auditoria->registrar('turma.aluno.matricular', 'turma', $turma->id, [
            'turma' => $turma->nome,
            'aluno_id' => $usuario->id,
        ]);

        return back()->with('sucesso', 'Aluno matriculado.');
    }

    /**
     * O `Rule::exists` roda sob RLS: um id de outra instituição simplesmente não existe
     * para esta consulta. A validação de papel impede matricular um professor como aluno.
     */
    private function usuarioComPapel(Request $requisicao, string $papel): Usuario
    {
        $dados = $requisicao->validate([
            'usuario_id' => ['required', 'integer', Rule::exists('usuarios', 'id')->where('papel', $papel)],
        ]);

        return Usuario::query()->findOrFail($dados['usuario_id']);
    }
}
