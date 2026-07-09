<?php

declare(strict_types=1);

namespace App\Dominio\Progressao;

use App\Models\Conquista;
use App\Models\ItemDoInventario;
use App\Models\Personagem;
use App\Models\ProgressoFase;
use Illuminate\Support\Facades\DB;

/**
 * "Primeiros passos": o checklist do novato.
 *
 * O primeiro item já nasce concluído — o jogador acabou de forjar o herói. Isso é
 * *endowed progress*: uma jornada que já começou é muito mais provável de ser
 * terminada do que uma que ainda não. Read-only, não concede nada.
 */
final class ServicoDeOnboarding
{
    /**
     * @param  array{batalha:bool,item:bool,conquista:bool}  $fatos
     * @return array{passos:list<array{chave:string,label:string,feito:bool}>,completos:int,total:int,mostrar:bool}
     */
    public static function montar(array $fatos, int $nivel): array
    {
        $passos = [
            ['chave' => 'heroi', 'label' => 'Forjar seu herói', 'feito' => true],
            ['chave' => 'batalha', 'label' => 'Vencer a primeira batalha', 'feito' => $fatos['batalha']],
            ['chave' => 'item', 'label' => 'Equipar um item', 'feito' => $fatos['item']],
            ['chave' => 'conquista', 'label' => 'Desbloquear uma conquista', 'feito' => $fatos['conquista']],
        ];

        $completos = count(array_filter($passos, static fn (array $p): bool => $p['feito']));
        $total = count($passos);

        return [
            'passos' => $passos,
            'completos' => $completos,
            'total' => $total,
            // Some quando o jogador cresce ou termina a lista: um checklist de
            // novato exibido ao veterano é ruído.
            'mostrar' => $nivel <= (int) config('jogo.onboarding_nivel_max') && $completos < $total,
        ];
    }

    /** @return array{passos:list<array{chave:string,label:string,feito:bool}>,completos:int,total:int,mostrar:bool} */
    public static function primeirosPassos(Personagem $heroi): array
    {
        return self::montar([
            'batalha' => ProgressoFase::query()->where('personagem_id', $heroi->id)->exists(),
            'item' => ItemDoInventario::temEquipado($heroi->id),
            'conquista' => DB::table('conquistas_personagem')->where('personagem_id', $heroi->id)->exists(),
        ], $heroi->nivel);
    }

    /**
     * Conquistas já obtidas, para o perfil marcar o que falta.
     *
     * @return list<int>
     */
    public static function conquistasObtidas(int $personagemId): array
    {
        return Conquista::obtidasIds($personagemId);
    }
}
