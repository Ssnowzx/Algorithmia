<?php
/**
 * Onboarding "Primeiros passos": checklist de marcos iniciais com o PRIMEIRO já
 * concluído ("Forjar seu herói") — o jogador acabou de criar o personagem —,
 * para dar ao novato a sensação de jornada já começada (endowed progress).
 * A montagem é PURA (testável); só primeirosPassos() faz I/O. Read-only — não
 * concede recompensa nem grava nada.
 */
class OnboardingService
{
    /**
     * Monta o checklist a partir dos fatos (booleanos) de cada passo + o nível.
     * O passo "herói" é sempre concluído (head start). Pura.
     *
     * @param array{batalha?:bool,item?:bool,conquista?:bool} $flags
     * @return array{passos:array,completos:int,total:int,mostrar:bool}
     */
    public static function montar(array $flags, int $nivel): array
    {
        $passos = [
            ['chave' => 'heroi',     'label' => 'Forjar seu herói',          'feito' => true],
            ['chave' => 'batalha',   'label' => 'Vencer a primeira batalha', 'feito' => !empty($flags['batalha'])],
            ['chave' => 'item',      'label' => 'Equipar um item',           'feito' => !empty($flags['item'])],
            ['chave' => 'conquista', 'label' => 'Desbloquear uma conquista', 'feito' => !empty($flags['conquista'])],
        ];
        $completos = 0;
        foreach ($passos as $p) {
            if ($p['feito']) {
                $completos++;
            }
        }
        $total = count($passos);
        // Some quando o jogador evolui (passa do nível) ou conclui tudo.
        $mostrar = $nivel <= ONBOARDING_NIVEL_MAX && $completos < $total;

        return ['passos' => $passos, 'completos' => $completos, 'total' => $total, 'mostrar' => $mostrar];
    }

    /**
     * Checklist de primeiros passos de um personagem (read-only, derivado de
     * progresso_fases / inventário / conquistas). Defensivo: qualquer falha
     * devolve um painel oculto, sem quebrar o perfil.
     *
     * @return array{passos:array,completos:int,total:int,mostrar:bool}
     */
    public static function primeirosPassos(int $personagemId, int $nivel): array
    {
        try {
            $temFase      = count((new ProgressoFase())->mapaDoPersonagem($personagemId)) > 0;
            $temItem      = (new Inventario())->temEquipado($personagemId);
            $temConquista = count((new Conquista())->obtidasIds($personagemId)) > 0;
            return self::montar(
                ['batalha' => $temFase, 'item' => $temItem, 'conquista' => $temConquista],
                $nivel
            );
        } catch (\Throwable $e) {
            return ['passos' => [], 'completos' => 0, 'total' => 0, 'mostrar' => false];
        }
    }
}
