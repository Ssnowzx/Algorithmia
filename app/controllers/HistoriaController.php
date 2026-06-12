<?php
/**
 * Conduz a narrativa: diálogos antes das fases, conclusão de fases de história
 * e a sequência de finais múltiplos.
 */
class HistoriaController extends Controller
{
    /**
     * Mostra o diálogo de abertura de uma fase. Em seguida o jogador parte
     * para a batalha (fases de combate) ou conclui (fases de história).
     */
    public function ver(string $faseId = '0'): void
    {
        $heroi = Auth::exigirPersonagem();
        $fase = (new Fase())->findById((int) $faseId);
        if (!$fase) {
            $this->redirect('mapa');
        }

        // Garante que a fase está liberada para este personagem.
        $mapaProgresso = (new ProgressoFase())->mapaDoPersonagem((int) $heroi['id']);
        if (!(new ProgressaoService())->faseLiberada($fase, $mapaProgresso)) {
            $this->flash('erro', 'Calma, herói. Essa fase ainda está trancada — termine a anterior antes de bancar o atrevido.');
            $this->redirect('mapa');
        }

        $variante = (new ReputacaoService())->variante($heroi);
        $dialogos = (new Dialogo())->paraMomento((int) $fase['id'], 'antes', $variante);

        $ehCombate = in_array($fase['tipo'], ['licao', 'chefe', 'chefe_final', 'secundaria'], true);

        // Mestre da região: define o cenário de fundo e a cor de destaque da cena.
        $mestre = !empty($fase['mestre_id']) ? (new Mestre())->findById((int) $fase['mestre_id']) : null;

        $this->view('historia/dialogo', [
            'pageTitle' => $fase['nome'],
            'fase'      => $fase,
            'mestre'    => $mestre,
            'dialogos'  => $dialogos,
            'ehCombate' => $ehCombate,
            'momento'   => 'antes',
        ]);
    }

    /**
     * Conclui uma fase de história (sem batalha): registra progresso e avança.
     */
    public function concluir(string $faseId = '0'): void
    {
        $heroi = Auth::exigirPersonagem();
        $fase = (new Fase())->findById((int) $faseId);
        if (!$fase || $fase['tipo'] !== 'historia') {
            $this->redirect('mapa');
        }

        $progressoModel = new ProgressoFase();
        if (!$progressoModel->concluiu((int) $heroi['id'], (int) $fase['id'])) {
            $progressoModel->registrar((int) $heroi['id'], (int) $fase['id'], 3, 0, 0, false);
            if ((int) $fase['xp_recompensa'] > 0) {
                (new ProgressaoService())->ganharXp($heroi, (int) $fase['xp_recompensa']);
            }
        }

        // A fase que precede o confronto final leva ao desfecho.
        if ((int) $fase['ordem_global'] === 34) {
            $this->redirect('historia/ver/35');
        }
        $this->redirect('mapa');
    }

    /**
     * Tela de escolha do final, exibida após vencer o confronto derradeiro.
     */
    public function final(): void
    {
        $heroi = Auth::exigirPersonagem();

        // Só acessível depois de concluir a fase final (id 35).
        if (!(new ProgressoFase())->concluiu((int) $heroi['id'], 35)) {
            $this->redirect('mapa');
        }

        // Se já escolheu, mostra direto o epílogo.
        $escolha = (new Escolha())->valor((int) $heroi['id'], 'final');
        if ($escolha !== null) {
            $this->mostrarEpilogo($heroi);
            return;
        }

        $this->view('historia/escolha-final', [
            'pageTitle' => 'O Destino de Algorithmia',
            'heroi'     => $heroi,
            'reputacao' => (int) $heroi['reputacao'],
        ]);
    }

    /**
     * Registra a escolha do jogador e exibe o epílogo correspondente.
     */
    public function escolherFinal(): void
    {
        $heroi = Auth::exigirPersonagem();
        $this->exigirCsrf();

        $opcao = $_POST['escolha'] ?? '';
        if (!in_array($opcao, ['destruir', 'fundir', 'reescrever'], true)) {
            $this->redirect('historia/final');
        }

        (new Escolha())->definir((int) $heroi['id'], 'final', $opcao);
        $this->mostrarEpilogo($heroi);
    }

    /**
     * Resolve qual dos três finais será exibido e concede a conquista secreta.
     */
    private function mostrarEpilogo(array $heroi): void
    {
        $final = (new ReputacaoService())->finalDeterminado($heroi);

        $conquistas = ['mestre' => 'final_mestre', 'singularidade' => 'final_singularidade', 'equilibrio' => 'final_equilibrio'];
        if (isset($conquistas[$final])) {
            (new ConquistaService())->conceder((int) $heroi['id'], $conquistas[$final]);
        }

        $this->view('historia/final', [
            'pageTitle' => 'Epílogo',
            'final'     => $final,
            'heroi'     => $heroi,
        ]);
    }
}
