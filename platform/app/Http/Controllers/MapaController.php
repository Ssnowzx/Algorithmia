<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dominio\Progressao\ServicoDeProgressao;
use App\Models\Fase;
use App\Models\Personagem;
use App\Models\ProgressoFase;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class MapaController extends Controller
{
    public function __construct(private readonly ServicoDeProgressao $progressao) {}

    public function index(Request $requisicao): View
    {
        /** @var Personagem $heroi */
        $heroi = $requisicao->attributes->get('personagem');

        $progresso = ProgressoFase::mapaDoPersonagem($heroi->id);

        $fases = Fase::query()
            ->with('mestre')
            ->orderBy('ordem_global')
            ->get()
            ->map(fn (Fase $fase): array => [
                'fase' => $fase,
                'liberada' => $this->progressao->faseLiberada($fase, $progresso),
                'progresso' => $progresso[$fase->id] ?? null,
            ]);

        return view('mapa.index', [
            'heroi' => $heroi,
            'fases' => $fases,
            'estrelas' => array_sum(array_map(fn (ProgressoFase $p): int => $p->estrelas, $progresso)),
        ]);
    }
}
