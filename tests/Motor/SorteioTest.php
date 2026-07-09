<?php

declare(strict_types=1);

namespace Algorithmia\Testes\Motor;

use Algorithmia\Testes\Suporte\Mundo;
use Algorithmia\Testes\Suporte\MotorExposto;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Invariantes do sorteio anti-repetição.
 *
 * O sorteio usa shuffle(), então a permutação exata é indeterminada. Estes
 * testes checam apenas o que a REGRA promete, e não a permutação: quais
 * desafios entram na batalha, quantos, e em que ordem de dificuldade. Nada aqui
 * depende de semente — o port em Laravel pode embaralhar de outro jeito e ainda
 * assim passar.
 */
final class SorteioTest extends TestCase
{
    private Mundo $mundo;

    protected function setUp(): void
    {
        $this->mundo = new Mundo();
        $this->mundo->limpar();
    }

    #[Test]
    public function deve_preferir_desafios_ineditos_e_empurrar_os_vistos_para_a_reserva(): void
    {
        // ARRANGE: pool de 6; a lição sorteia 4 (DESAFIOS_POR_BATALHA['licao']).
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase(['tipo' => 'licao']);

        $vistos = [
            $this->mundo->desafio((int) $fase['id'], dificuldade: 1, ordem: 0),
            $this->mundo->desafio((int) $fase['id'], dificuldade: 1, ordem: 1),
        ];
        $ineditos = [
            $this->mundo->desafio((int) $fase['id'], dificuldade: 3, ordem: 2),
            $this->mundo->desafio((int) $fase['id'], dificuldade: 1, ordem: 3),
            $this->mundo->desafio((int) $fase['id'], dificuldade: 2, ordem: 4),
            $this->mundo->desafio((int) $fase['id'], dificuldade: 1, ordem: 5),
        ];
        foreach ($vistos as $d) {
            $this->mundo->marcarVisto((int) $heroi['id'], (int) $d['id']);
        }

        // ACT
        $sorteio = (new MotorExposto())->sortear($heroi, $fase);

        // ASSERT
        $this->assertSame(4, $sorteio['limite'], 'a lição joga 4 perguntas antes do Duelo Final');

        $principal = array_slice($sorteio['lista'], 0, 4);
        $idsPrincipal = array_map(static fn (array $d): int => (int) $d['id'], $principal);
        $idsIneditos = array_map(static fn (array $d): int => (int) $d['id'], $ineditos);
        sort($idsPrincipal);
        sort($idsIneditos);
        $this->assertSame($idsIneditos, $idsPrincipal, 'só entram perguntas que o herói nunca viu');

        // A curva didática sobrevive ao embaralhamento.
        $dificuldades = array_map(static fn (array $d): int => (int) $d['dificuldade'], $principal);
        $this->assertSame([1, 1, 2, 3], $dificuldades, 'principal em dificuldade crescente');

        // Os já vistos viram reserva do Duelo Final, sem sumir do pool.
        $reserva = array_slice($sorteio['lista'], 4);
        $idsReserva = array_map(static fn (array $d): int => (int) $d['id'], $reserva);
        sort($idsReserva);
        $this->assertSame([(int) $vistos[0]['id'], (int) $vistos[1]['id']], $idsReserva);
    }

    #[Test]
    public function com_pool_menor_que_a_batalha_usa_tudo_ordenado_por_dificuldade(): void
    {
        // ARRANGE: 3 desafios para uma lição que pediria 4.
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase(['tipo' => 'licao']);
        $this->mundo->desafio((int) $fase['id'], dificuldade: 3, ordem: 0);
        $this->mundo->desafio((int) $fase['id'], dificuldade: 1, ordem: 1);
        $this->mundo->desafio((int) $fase['id'], dificuldade: 2, ordem: 2);

        // ACT
        $sorteio = (new MotorExposto())->sortear($heroi, $fase);

        // ASSERT
        $this->assertSame(3, $sorteio['limite']);
        $dificuldades = array_map(static fn (array $d): int => (int) $d['dificuldade'], $sorteio['lista']);
        $this->assertSame([1, 2, 3], $dificuldades);
    }

    #[Test]
    public function o_chefe_sorteia_mais_perguntas_que_a_licao(): void
    {
        // ARRANGE: DESAFIOS_POR_BATALHA = licao 4, chefe 5.
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase(['tipo' => 'chefe']);
        for ($i = 0; $i < 8; $i++) {
            $this->mundo->desafio((int) $fase['id'], dificuldade: 1, ordem: $i);
        }

        // ACT
        $sorteio = (new MotorExposto())->sortear($heroi, $fase);

        // ASSERT
        $this->assertSame(5, $sorteio['limite']);
        $this->assertCount(8, $sorteio['lista'], 'o resto do pool vira reserva, não é descartado');
    }

    #[Test]
    public function quando_todo_o_pool_ja_foi_visto_o_sorteio_ainda_completa_a_batalha(): void
    {
        // ARRANGE: nenhum inédito sobrou — o herói já respondeu tudo.
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase(['tipo' => 'licao']);
        for ($i = 0; $i < 6; $i++) {
            $d = $this->mundo->desafio((int) $fase['id'], dificuldade: 1, ordem: $i);
            $this->mundo->marcarVisto((int) $heroi['id'], (int) $d['id']);
        }

        // ACT
        $sorteio = (new MotorExposto())->sortear($heroi, $fase);

        // ASSERT
        $this->assertSame(4, $sorteio['limite'], 'recorre aos vistos em vez de encurtar a batalha');
        $this->assertCount(6, $sorteio['lista']);
    }
}
