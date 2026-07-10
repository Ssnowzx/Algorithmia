<?php

declare(strict_types=1);

namespace Tests\Dominio;

use App\Dominio\Combate\SorteioAntiRepeticao;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * Invariantes do sorteio anti-repetição, portadas de `tests/Motor/SorteioTest.php`.
 *
 * O sorteio embaralha, então a permutação exata é indeterminada. Estes testes
 * checam só o que a REGRA promete — quais desafios entram, quantos, e em que
 * ordem de dificuldade. Nada aqui depende de semente.
 */
final class SorteioTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    private SorteioAntiRepeticao $sorteio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mundo = new Mundo;
        $this->sorteio = new SorteioAntiRepeticao;
    }

    #[Test]
    public function deve_preferir_desafios_ineditos_e_empurrar_os_vistos_para_a_reserva(): void
    {
        // ARRANGE: pool de 6; a lição sorteia 4 (jogo.desafios_por_batalha.licao).
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase(['tipo' => 'licao']);

        $vistos = [
            $this->mundo->desafio($fase->id, dificuldade: 1, ordem: 0),
            $this->mundo->desafio($fase->id, dificuldade: 1, ordem: 1),
        ];
        $ineditos = [
            $this->mundo->desafio($fase->id, dificuldade: 3, ordem: 2),
            $this->mundo->desafio($fase->id, dificuldade: 1, ordem: 3),
            $this->mundo->desafio($fase->id, dificuldade: 2, ordem: 4),
            $this->mundo->desafio($fase->id, dificuldade: 1, ordem: 5),
        ];
        foreach ($vistos as $desafio) {
            $this->mundo->marcarVisto($heroi, $desafio);
        }

        // ACT
        $sorteio = $this->sorteio->sortear($heroi, $fase);

        // ASSERT
        $this->assertSame(4, $sorteio['limite'], 'a lição joga 4 perguntas antes do Duelo Final');

        $principal = array_slice($sorteio['lista'], 0, 4);
        $idsPrincipal = array_column($principal, 'id');
        $idsIneditos = array_map(fn ($d): int => $d->id, $ineditos);
        sort($idsPrincipal);
        sort($idsIneditos);
        $this->assertSame($idsIneditos, $idsPrincipal, 'só entram perguntas que o herói nunca viu');

        // A curva didática sobrevive ao embaralhamento.
        $this->assertSame([1, 1, 2, 3], array_column($principal, 'dificuldade'));

        // Os já vistos viram reserva do Duelo Final, sem sumir do pool.
        $idsReserva = array_column(array_slice($sorteio['lista'], 4), 'id');
        sort($idsReserva);
        $this->assertSame([$vistos[0]->id, $vistos[1]->id], $idsReserva);
    }

    #[Test]
    public function com_pool_menor_que_a_batalha_usa_tudo_ordenado_por_dificuldade(): void
    {
        // ARRANGE: 3 desafios para uma lição que pediria 4.
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase(['tipo' => 'licao']);
        $this->mundo->desafio($fase->id, dificuldade: 3, ordem: 0);
        $this->mundo->desafio($fase->id, dificuldade: 1, ordem: 1);
        $this->mundo->desafio($fase->id, dificuldade: 2, ordem: 2);

        // ACT
        $sorteio = $this->sorteio->sortear($heroi, $fase);

        // ASSERT
        $this->assertSame(3, $sorteio['limite']);
        $this->assertSame([1, 2, 3], array_column($sorteio['lista'], 'dificuldade'));
    }

    #[Test]
    public function o_chefe_sorteia_mais_perguntas_que_a_licao(): void
    {
        // ARRANGE: desafios_por_batalha = licao 4, chefe 5.
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase(['tipo' => 'chefe']);
        for ($i = 0; $i < 8; $i++) {
            $this->mundo->desafio($fase->id, dificuldade: 1, ordem: $i);
        }

        // ACT
        $sorteio = $this->sorteio->sortear($heroi, $fase);

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
            $this->mundo->marcarVisto($heroi, $this->mundo->desafio($fase->id, dificuldade: 1, ordem: $i));
        }

        // ACT
        $sorteio = $this->sorteio->sortear($heroi, $fase);

        // ASSERT
        $this->assertSame(4, $sorteio['limite'], 'recorre aos vistos em vez de encurtar a batalha');
        $this->assertCount(6, $sorteio['lista']);
    }
}
