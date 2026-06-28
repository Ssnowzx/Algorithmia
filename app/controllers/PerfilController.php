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
            // Progresso parcial das conquistas contáveis (goal-gradient na ficha).
            'progressoConquistas' => (new ConquistaService())->progressoParcial((int) $heroi['id'], (int) $heroi['nivel']),
            // Recap dos últimos 7 dias (respostas/acertos/usos_ia + fases/estrelas).
            'recapSemana' => $logModel->resumoSemana((int) $heroi['id'])
                + (new ProgressoFase())->resumoSemana((int) $heroi['id']),
            // Maestria por matéria derivada das MESMAS estatísticas (read-only,
            // sem query nova): faixa de domínio + progresso rumo ao próximo selo.
            'maestria' => MaestriaService::porMateria($estatisticas),
            // Missões da semana ISO corrente (read-only, sem recompensa/persistência):
            // metas de curto prazo derivadas da atividade da semana.
            'missoes' => MissaoService::daSemana((int) $heroi['id']),
            // Domínio das regiões (maestria horizontal): perfeição da jornada por
            // mestre (fases concluídas + estrelas). Read-only, sem migration.
            'regioes' => RegiaoService::dominio((int) $heroi['id']),
            // Onboarding "Primeiros passos" (endowed progress) — só p/ novato.
            'onboarding' => OnboardingService::primeirosPassos((int) $heroi['id'], (int) $heroi['nivel']),
        ]);
    }
}
