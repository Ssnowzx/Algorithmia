<?php
/**
 * Perfil do herói: atributos, estatísticas por matéria e conquistas.
 */
class PerfilController extends Controller
{
    public function index(): void
    {
        $heroi = Auth::exigirPersonagem();
        $usuario = Auth::usuario();

        $logModel = new RespostaLog();
        $estatisticas = $logModel->estatisticasPorAssunto((int) $heroi['id']);

        // Catálogo de conquistas + as que o herói já obteve.
        $conquistaModel = new Conquista();
        $todas = $conquistaModel->findAll('id ASC');
        $obtidas = $conquistaModel->obtidasIds((int) $heroi['id']);

        $atributos = (new BatalhaService())->atributosCombate($heroi);

        $this->view('perfil/index', [
            'pageTitle'     => 'Perfil de ' . $heroi['nome'],
            'heroi'         => $heroi,
            'usuario'       => $usuario,
            'estatisticas'  => $estatisticas,
            'conquistas'    => $todas,
            'obtidas'       => $obtidas,
            'atributos'     => $atributos,
            'totalRespostas'=> $logModel->totalRespostas((int) $heroi['id']),
            'totalUsosIa'   => $logModel->totalUsosIa((int) $heroi['id']),
            'totalEstrelas' => (new ProgressoFase())->totalEstrelas((int) $heroi['id']),
        ]);
    }
}
