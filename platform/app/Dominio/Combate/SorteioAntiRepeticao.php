<?php

declare(strict_types=1);

namespace App\Dominio\Combate;

use App\Models\Desafio;
use App\Models\Fase;
use App\Models\Personagem;

/**
 * Sorteio de produção.
 *
 * Sempre que sobra pool, prioriza perguntas que o personagem ainda NÃO viu; só
 * recorre às já vistas para completar a quantidade. Os escolhidos saem
 * embaralhados e depois reordenados por dificuldade crescente: cada replay traz
 * combinações novas, mas a curva didática se mantém. O resto do pool não é
 * descartado — vira a reserva que abastece o Duelo Final.
 */
final class SorteioAntiRepeticao implements SorteadorDeDesafios
{
    public function sortear(Personagem $personagem, Fase $fase): array
    {
        $pool = Desafio::poolDaFase($fase->id)->map($this->paraLinha(...))->all();

        $quantos = (int) (config("jogo.desafios_por_batalha.{$fase->tipo}")
            ?? config('jogo.desafios_por_batalha_padrao'));

        if ($quantos <= 0 || count($pool) <= $quantos) {
            usort($pool, fn (array $a, array $b): int => $a['dificuldade'] <=> $b['dificuldade']);

            return ['lista' => $pool, 'limite' => count($pool)];
        }

        $vistos = array_flip(Desafio::idsVistos($personagem->id, $fase->id));
        $ineditos = [];
        $revisao = [];
        foreach ($pool as $desafio) {
            if (isset($vistos[$desafio['id']])) {
                $revisao[] = $desafio;
            } else {
                $ineditos[] = $desafio;
            }
        }

        shuffle($ineditos);
        shuffle($revisao);
        $ordenados = array_merge($ineditos, $revisao);

        $principal = array_slice($ordenados, 0, $quantos);
        $reserva = array_slice($ordenados, $quantos);

        // usort é estável no PHP 8: empates de dificuldade preservam a ordem já
        // embaralhada, então a sequência muda a cada batalha.
        usort($principal, fn (array $a, array $b): int => $a['dificuldade'] <=> $b['dificuldade']);
        shuffle($reserva); // a reserva só aparece no Duelo Final; ordem livre

        return ['lista' => array_merge($principal, $reserva), 'limite' => count($principal)];
    }

    /** @return array<string,mixed> */
    private function paraLinha(Desafio $desafio): array
    {
        return [
            'id' => $desafio->id,
            'tipo' => $desafio->tipo,
            'assunto' => $desafio->assunto,
            'pergunta' => $desafio->pergunta,
            'codigo' => $desafio->codigo,
            'opcoes' => $desafio->opcoes,
            'resposta' => $desafio->resposta,
            'explicacao' => $desafio->explicacao,
            'dificuldade' => $desafio->dificuldade,
        ];
    }
}
