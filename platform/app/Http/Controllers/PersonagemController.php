<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Personagem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class PersonagemController extends Controller
{
    public function mostrarCriacao(): RedirectResponse|View
    {
        if (Personagem::query()->where('usuario_id', Auth::id())->exists()) {
            return redirect()->route('mapa');
        }

        return view('personagem.criar', ['classes' => config('jogo.classes')]);
    }

    public function criar(Request $requisicao): RedirectResponse
    {
        /** @var array<string,mixed> $classes */
        $classes = config('jogo.classes');

        $dados = $requisicao->validate([
            'nome' => ['required', 'string', 'max:80'],
            // A lista vem de config/jogo.php, e não do formulário: aceitar a
            // classe crua deixaria o jogador inventar atributos.
            'classe' => ['required', Rule::in(array_keys($classes))],
        ]);

        // Um herói por conta. `usuario_id` é UNIQUE, mas errar aqui daria um 500.
        if (Personagem::query()->where('usuario_id', Auth::id())->exists()) {
            return redirect()->route('mapa');
        }

        /** @var array{hp:int,mp:int} $base */
        $base = $classes[$dados['classe']];

        Personagem::create([
            'usuario_id' => Auth::id(),
            'nome' => $dados['nome'],
            'classe' => $dados['classe'],
            'nivel' => 1,
            'xp' => 0,
            'hp_max' => $base['hp'],
            'hp_atual' => $base['hp'],
            'mp_max' => $base['mp'],
            'mp_atual' => $base['mp'],
            'ouro' => 50,
            'reputacao' => 0,
            'capitulo' => 0,
        ]);

        return redirect()->route('mapa');
    }
}
