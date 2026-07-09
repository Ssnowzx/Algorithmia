<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Personagem;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class RankingController extends Controller
{
    public function index(Request $requisicao): View
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        return view('ranking.index', [
            'ranking' => Personagem::ranking(),
            'heroiId' => $heroi->id,
        ]);
    }
}
