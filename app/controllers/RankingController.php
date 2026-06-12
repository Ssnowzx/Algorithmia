<?php
/**
 * Ranking dos heróis por nível e XP.
 */
class RankingController extends Controller
{
    public function index(): void
    {
        $heroi = Auth::exigirPersonagem();
        $ranking = (new Personagem())->ranking(50);

        $this->view('ranking/index', [
            'pageTitle' => 'Ranking de Algorithmia',
            'ranking'   => $ranking,
            'heroiId'   => (int) $heroi['id'],
        ]);
    }
}
