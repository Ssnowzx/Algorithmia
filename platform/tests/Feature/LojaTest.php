<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ItemDoInventario;
use App\Models\Personagem;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Suporte\Mundo;
use Tests\TestCase;

final class LojaTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mundo = new Mundo;
    }

    #[Test]
    public function comprar_debita_o_ouro_e_entrega_o_item(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi(['ouro' => 100]);
        $espada = $this->mundo->item('arma', ['ataque' => 5], ['nome' => 'Espada', 'preco' => 40]);

        // ACT
        $this->post(route('loja.comprar', $espada))->assertRedirect();

        // ASSERT
        $this->assertSame(60, $heroi->refresh()->ouro);
        $this->assertSame(1, ItemDoInventario::quantidade($heroi->id, $espada->id));
    }

    #[Test]
    public function sem_ouro_suficiente_a_compra_e_recusada(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi(['ouro' => 10]);
        $caro = $this->mundo->item('arma', ['ataque' => 9], ['preco' => 500]);

        // ACT
        $this->from(route('loja'))->post(route('loja.comprar', $caro))
            ->assertRedirect(route('loja'))
            ->assertSessionHas('erro');

        // ASSERT
        $this->assertSame(10, $heroi->refresh()->ouro);
        $this->assertSame(0, ItemDoInventario::quantidade($heroi->id, $caro->id));
    }

    #[Test]
    public function um_item_nao_compravel_nao_pode_ser_comprado_pela_url(): void
    {
        // ARRANGE: o Fragmento existe no catálogo, mas não está à venda.
        $heroi = $this->entrarComHeroi(['ouro' => 9999]);
        $fragmento = $this->mundo->item('especial', [], [
            'svg_slug' => config('jogo.item_fragmento_ia'), 'preco' => 0, 'compravel' => false,
        ]);

        // ACT + ASSERT
        $this->from(route('loja'))->post(route('loja.comprar', $fragmento))->assertSessionHas('erro');
        $this->assertSame(0, ItemDoInventario::quantidade($heroi->id, $fragmento->id));
    }

    #[Test]
    public function vender_devolve_metade_do_preco(): void
    {
        // ARRANGE: fator_venda = 0.5 → 41 vira round(20.5) = 21.
        $heroi = $this->entrarComHeroi(['ouro' => 0]);
        $item = $this->mundo->item('escudo', ['defesa' => 3], ['preco' => 41]);
        $this->mundo->darItem($heroi, $item);

        // ACT
        $this->post(route('loja.vender', $item))->assertRedirect();

        // ASSERT
        $this->assertSame(21, $heroi->refresh()->ouro);
        $this->assertSame(0, ItemDoInventario::quantidade($heroi->id, $item->id));
    }

    #[Test]
    public function o_fragmento_da_ia_nao_pode_ser_vendido(): void
    {
        // A tentação do jogo não vira dinheiro: transformá-la em ouro faria da
        // queda moral um negócio.

        // ARRANGE
        $heroi = $this->entrarComHeroi(['ouro' => 0]);
        $fragmento = $this->mundo->item('especial', [], [
            'svg_slug' => config('jogo.item_fragmento_ia'), 'preco' => 200,
        ]);
        $this->mundo->darItem($heroi, $fragmento);

        // ACT + ASSERT
        $this->from(route('loja'))->post(route('loja.vender', $fragmento))
            ->assertSessionHas('erro');

        $this->assertSame(0, $heroi->refresh()->ouro);
        $this->assertSame(1, ItemDoInventario::quantidade($heroi->id, $fragmento->id));
    }

    #[Test]
    public function nao_se_vende_o_que_nao_se_tem(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi(['ouro' => 0]);
        $item = $this->mundo->item('arma', ['ataque' => 5], ['preco' => 100]);

        // ACT + ASSERT
        $this->from(route('loja'))->post(route('loja.vender', $item))->assertSessionHas('erro');
        $this->assertSame(0, $heroi->refresh()->ouro);
    }

    #[Test]
    public function juntar_oito_itens_distintos_concede_colecionador(): void
    {
        // ARRANGE
        $this->mundo->conquistas('colecionador');
        $heroi = $this->entrarComHeroi(['ouro' => 1000]);

        for ($i = 0; $i < 7; $i++) {
            $this->mundo->darItem($heroi, $this->mundo->item('pocao', ['cura_hp' => 10]));
        }
        $oitavo = $this->mundo->item('arma', ['ataque' => 1], ['preco' => 10]);

        // ACT
        $this->from(route('loja'))->post(route('loja.comprar', $oitavo));

        // ASSERT
        $this->assertSame(8, ItemDoInventario::itensDistintos($heroi->id));
        $this->assertStringContainsString('Colecionador', (string) session('sucesso'));
    }

    #[Test]
    public function a_loja_mostra_o_poder_que_o_heroi_teria_ao_trocar(): void
    {
        // O número absoluto do item não diz se vale trocar. O que importa é o Poder
        // (ataque + defesa) que o herói terá DEPOIS, descontando o que sai do slot.

        // ARRANGE: mago (ataque 12, defesa 4) com uma espada fraca equipada.
        $heroi = $this->entrarComHeroi(['ouro' => 500]);
        $fraca = $this->mundo->item('arma', ['ataque' => 2], ['nome' => 'Espada Enferrujada', 'preco' => 10]);
        $this->mundo->item('arma', ['ataque' => 9], ['nome' => 'Lâmina do Kernel', 'preco' => 200]);
        $this->mundo->darItem($heroi, $fraca, equipado: true);

        // ACT + ASSERT: poder atual = (12+2) + 4 = 18; com a lâmina = (12+9) + 4 = 25.
        $this->get(route('loja'))->assertOk()
            ->assertSee('💪 Poder 18')
            ->assertSee('Lâmina do Kernel')
            ->assertSee('+7 em relação a Espada Enferrujada');
    }

    #[Test]
    public function um_item_pior_mostra_a_perda_de_poder(): void
    {
        // Mentir sobre a troca seria pior do que não comparar.

        // ARRANGE
        $heroi = $this->entrarComHeroi(['ouro' => 500]);
        $forte = $this->mundo->item('arma', ['ataque' => 9], ['nome' => 'Lâmina do Kernel', 'preco' => 200]);
        $this->mundo->item('arma', ['ataque' => 2], ['nome' => 'Espada Enferrujada', 'preco' => 10]);
        $this->mundo->darItem($heroi, $forte, equipado: true);

        // ACT + ASSERT
        $this->get(route('loja'))->assertOk()->assertSee('-7 em relação a Lâmina do Kernel');
    }

    /** @param  array<string,mixed>  $sobrescritas */
    private function entrarComHeroi(array $sobrescritas = []): Personagem
    {
        $heroi = $this->mundo->heroi('mago', $sobrescritas);
        $this->actingAs(Usuario::query()->findOrFail($heroi->usuario_id));

        return $heroi;
    }
}
