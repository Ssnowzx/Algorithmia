<?php
/**
 * Centraliza ganho de XP, subida de nível e desbloqueio de fases/capítulos.
 */
class ProgressaoService
{
    private Personagem $personagens;
    private Fase $fases;
    private ProgressoFase $progresso;

    public function __construct()
    {
        $this->personagens = new Personagem();
        $this->fases = new Fase();
        $this->progresso = new ProgressoFase();
    }

    /**
     * Concede XP e processa todas as subidas de nível decorrentes.
     * Cada nível aumenta HP/MP máximos e ataque, e recupera os pontos.
     *
     * @return array{niveis_ganhos:int, nivel:int}
     */
    public function ganharXp(array $personagem, int $xp): array
    {
        $id = (int) $personagem['id'];
        $nivel = (int) $personagem['nivel'];
        $xpTotal = (int) $personagem['xp'] + $xp;
        $hpMax = (int) $personagem['hp_max'];
        $mpMax = (int) $personagem['mp_max'];
        $niveisGanhos = 0;

        while ($xpTotal >= xpParaNivel($nivel + 1)) {
            $nivel++;
            $niveisGanhos++;
            $hpMax += 15;
            $mpMax += 8;
        }

        $dados = ['xp' => $xpTotal, 'nivel' => $nivel, 'hp_max' => $hpMax, 'mp_max' => $mpMax];
        if ($niveisGanhos > 0) {
            // Subir de nível restaura totalmente vida e mana.
            $dados['hp_atual'] = $hpMax;
            $dados['mp_atual'] = $mpMax;
        }
        $this->personagens->update($id, $dados);

        return ['niveis_ganhos' => $niveisGanhos, 'nivel' => $nivel];
    }

    /**
     * Uma fase está liberada se não tem pré-requisito ou se o pré-requisito
     * já foi concluído pelo personagem.
     */
    public function faseLiberada(array $fase, array $mapaProgresso): bool
    {
        $requisito = $fase['requisito_fase_id'];
        if ($requisito === null) {
            return true;
        }
        return isset($mapaProgresso[(int) $requisito]);
    }

    /**
     * Avança o capítulo do personagem ao concluir o chefe de uma região.
     */
    public function atualizarCapitulo(array $personagem, array $fase): void
    {
        if (in_array($fase['tipo'], ['chefe', 'chefe_final'], true)) {
            $mestre = $fase['mestre_id'] ? (new Mestre())->findById((int) $fase['mestre_id']) : null;
            $novoCapitulo = $mestre ? (int) $mestre['ordem'] : (int) $personagem['capitulo'] + 1;
            if ($novoCapitulo > (int) $personagem['capitulo']) {
                $this->personagens->update((int) $personagem['id'], ['capitulo' => $novoCapitulo]);
            }
        }
    }

    /**
     * Calcula estrelas (1 a 3) com base em erros e uso de IA.
     */
    public function calcularEstrelas(int $erros, bool $usouIa): int
    {
        if ($usouIa) {
            return 1;
        }
        if ($erros === 0) {
            return 3;
        }
        return $erros <= 2 ? 2 : 1;
    }
}
