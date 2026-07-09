<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Mestre;
use App\Models\Personagem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * A porta de entrada: vitrine para o visitante, atalho para quem já joga.
 */
final class HomeController extends Controller
{
    public function index(): RedirectResponse|View
    {
        if (Auth::check()) {
            return Personagem::query()->where('usuario_id', Auth::id())->exists()
                ? redirect()->route('mapa')
                : redirect()->route('personagem.criar');
        }

        return view('home.index', ['mestres' => Mestre::query()->orderBy('ordem')->get()]);
    }
}
