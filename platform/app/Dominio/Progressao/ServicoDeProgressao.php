<?php

declare(strict_types=1);

namespace App\Dominio\Progressao;

use App\Models\Fase;
use App\Models\Personagem;

/**
 * Ganho de XP, subida de nível, estrelas e avanço de capítulo.
 * Porte de `app/services/ProgressaoService.php`.
 */
final class ServicoDeProgressao
{
    /** XP total acumulado necessário para alcançar o nível N. */
    public static function xpParaNivel(int $nivel): int
    {
        if ($nivel <= 1) {
            return 0;
        }

        return (int) round(100 * pow($nivel - 1, 1.5));
    }

    /**
     * Concede XP e processa todas as subidas de nível decorrentes.
     * Cada nível amplia HP/MP máximos e restaura os pontos por completo.
     *
     * @return array{niveis_ganhos:int,nivel:int}
     */
    public function ganharXp(Personagem $personagem, int $xp): array
    {
        $nivel = $personagem->nivel;
        $xpTotal = $personagem->xp + $xp;
        $hpMax = $personagem->hp_max;
        $mpMax = $personagem->mp_max;
        $niveisGanhos = 0;

        while ($xpTotal >= self::xpParaNivel($nivel + 1)) {
            $nivel++;
            $niveisGanhos++;
            $hpMax += (int) config('jogo.progressao.hp_por_nivel');
            $mpMax += (int) config('jogo.progressao.mp_por_nivel');
        }

        $dados = ['xp' => $xpTotal, 'nivel' => $nivel, 'hp_max' => $hpMax, 'mp_max' => $mpMax];
        if ($niveisGanhos > 0) {
            $dados['hp_atual'] = $hpMax;
            $dados['mp_atual'] = $mpMax;
        }
        $personagem->update($dados);

        return ['niveis_ganhos' => $niveisGanhos, 'nivel' => $nivel];
    }

    /** Estrelas de 1 a 3. Usar a IA anula o mérito, por mais limpa que tenha sido a luta. */
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

    /** Avança o capítulo ao concluir o chefe de uma região. */
    public function atualizarCapitulo(Personagem $personagem, Fase $fase): void
    {
        if (! $fase->ehChefe()) {
            return;
        }

        $mestre = $fase->mestre;
        $novoCapitulo = $mestre !== null ? $mestre->ordem : $personagem->capitulo + 1;

        if ($novoCapitulo > $personagem->capitulo) {
            $personagem->update(['capitulo' => $novoCapitulo]);
        }
    }

    /** @param  array<int,mixed>  $mapaProgresso progresso do personagem, chaveado por fase_id */
    public function faseLiberada(Fase $fase, array $mapaProgresso): bool
    {
        return $fase->requisito_fase_id === null || isset($mapaProgresso[$fase->requisito_fase_id]);
    }
}
