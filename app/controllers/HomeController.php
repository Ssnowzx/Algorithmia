<?php
/**
 * Página inicial: vitrine da história para visitantes e porta de entrada do jogo.
 */
class HomeController extends Controller
{
    public function index(): void
    {
        if (Auth::logado()) {
            // Já tem personagem? Vai para o mapa. Senão, cria um.
            if (Auth::personagem()) {
                $this->redirect('mapa');
            }
            $this->redirect('auth/criarPersonagem');
        }

        $mestres = (new Mestre())->todosOrdenados();
        $this->view('home/index', [
            '_semLayout' => true,
            'mestres'    => $mestres,
        ]);
    }
}
