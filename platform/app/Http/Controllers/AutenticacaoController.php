<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Entrada e saída do reino.
 *
 * O CSRF e a exigência de POST vêm do middleware do Laravel, e não de uma
 * checagem escrita à mão em cada método — que era como o legado fazia, e como
 * `historia/concluir` acabou gravando XP por GET.
 */
final class AutenticacaoController extends Controller
{
    public function mostrarLogin(): View
    {
        return view('auth.login');
    }

    public function entrar(Request $requisicao): RedirectResponse
    {
        $credenciais = $requisicao->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credenciais, remember: false)) {
            // Mensagem única para e-mail inexistente e senha errada: dizer qual
            // dos dois falhou entrega uma lista de contas válidas a quem tenta.
            throw ValidationException::withMessages([
                'email' => 'Credenciais inválidas. Ou você esqueceu, ou nunca soube.',
            ]);
        }

        // Sem isto, um id de sessão capturado antes do login continua valendo depois.
        $requisicao->session()->regenerate();

        return redirect()->intended(route('mapa'));
    }

    public function mostrarRegistro(): View
    {
        return view('auth.registro');
    }

    public function registrar(Request $requisicao): RedirectResponse
    {
        $dados = $requisicao->validate([
            'nome' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // A unicidade do e-mail é insensível a maiúsculas (índice sobre
        // lower(email)); a regra `unique` do Laravel não é. Checamos igual.
        $jaExiste = Usuario::query()->whereRaw('lower(email) = ?', [mb_strtolower($dados['email'])])->exists();
        if ($jaExiste) {
            throw ValidationException::withMessages(['email' => 'Este e-mail já foi usado.']);
        }

        $usuario = Usuario::create([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'senha_hash' => Hash::make($dados['password']),
            'papel' => 'jogador',
        ]);

        Auth::login($usuario);
        $requisicao->session()->regenerate();

        return redirect()->route('personagem.criar');
    }

    public function sair(Request $requisicao): RedirectResponse
    {
        Auth::logout();

        // Invalidar sem regenerar o token deixaria o CSRF antigo válido.
        $requisicao->session()->invalidate();
        $requisicao->session()->regenerateToken();

        return redirect()->route('login');
    }
}
