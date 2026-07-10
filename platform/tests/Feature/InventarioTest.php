<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ItemDoInventario;
use App\Models\Personagem;
use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

final class InventarioTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mundo = new Mundo;
    }

    #[Test]
    public function equipar_uma_arma_desequipa_a_anterior(): void
    {
        // Sem isso, duas espadas somariam bônus e o herói viraria uma pilha delas.

        // ARRANGE
        $heroi = $this->entrarComHeroi();
        $velha = $this->mundo->item('arma', ['ataque' => 2]);
        $nova = $this->mundo->item('arma', ['ataque' => 9]);
        $this->mundo->darItem($heroi, $velha, equipado: true);
        $this->mundo->darItem($heroi, $nova);

        // ACT
        $this->post(route('inventario.equipar', $nova))->assertRedirect();

        // ASSERT: o bônus é o da nova, não a soma das duas.
        $this->assertSame(9, ItemDoInventario::bonusEquipados($heroi->id)['ataque']);
        $this->assertSame(['arma'], ItemDoInventario::tiposEquipados($heroi->id));
    }

    #[Test]
    public function equipar_arma_e_escudo_nao_disputa_o_mesmo_slot(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi();
        $arma = $this->mundo->item('arma', ['ataque' => 5]);
        $escudo = $this->mundo->item('escudo', ['defesa' => 4]);
        $this->mundo->darItem($heroi, $arma);
        $this->mundo->darItem($heroi, $escudo);

        // ACT
        $this->post(route('inventario.equipar', $arma));
        $this->post(route('inventario.equipar', $escudo));

        // ASSERT
        $bonus = ItemDoInventario::bonusEquipados($heroi->id);
        $this->assertSame(['ataque' => 5, 'defesa' => 4], $bonus);
    }

    #[Test]
    public function uma_pocao_nao_pode_ser_equipada(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi();
        $pocao = $this->mundo->item('pocao', ['cura_hp' => 30]);
        $this->mundo->darItem($heroi, $pocao);

        // ACT + ASSERT
        $this->from(route('inventario'))->post(route('inventario.equipar', $pocao))
            ->assertSessionHas('erro');

        $this->assertSame([], ItemDoInventario::tiposEquipados($heroi->id));
    }

    #[Test]
    public function nao_se_equipa_um_item_que_nao_se_possui(): void
    {
        // ARRANGE: o item existe no catálogo, mas não no inventário dele.
        $heroi = $this->entrarComHeroi();
        $alheia = $this->mundo->item('arma', ['ataque' => 99]);

        // ACT + ASSERT
        $this->from(route('inventario'))->post(route('inventario.equipar', $alheia))
            ->assertSessionHas('erro');

        $this->assertSame(0, ItemDoInventario::bonusEquipados($heroi->id)['ataque']);
    }

    #[Test]
    public function equipar_a_primeira_arma_concede_o_objetivo_e_credita_ouro(): void
    {
        // ARRANGE: objetivos_ouro.primeira_arma = 30.
        $this->mundo->conquistas('primeira_arma');
        $heroi = $this->entrarComHeroi(['ouro' => 0]);
        $arma = $this->mundo->item('arma', ['ataque' => 3]);
        $this->mundo->darItem($heroi, $arma);

        // ACT
        $this->from(route('inventario'))->post(route('inventario.equipar', $arma));

        // ASSERT
        $this->assertStringContainsString('Primeira arma', (string) session('sucesso'));
        $this->assertSame(30, $heroi->refresh()->ouro);
    }

    #[Test]
    public function o_objetivo_da_primeira_arma_so_paga_uma_vez(): void
    {
        // ARRANGE
        $this->mundo->conquistas('primeira_arma');
        $heroi = $this->entrarComHeroi(['ouro' => 0]);
        $arma = $this->mundo->item('arma', ['ataque' => 3]);
        $outra = $this->mundo->item('arma', ['ataque' => 4]);
        $this->mundo->darItem($heroi, $arma);
        $this->mundo->darItem($heroi, $outra);

        // ACT
        $this->post(route('inventario.equipar', $arma));
        $this->post(route('inventario.equipar', $outra));

        // ASSERT
        $this->assertSame(30, $heroi->refresh()->ouro, 'a conquista é idempotente');
    }

    #[Test]
    public function arma_escudo_e_acessorio_juntos_concedem_arsenal_completo(): void
    {
        // ARRANGE: objetivos_ouro.arsenal_completo = 60.
        $this->mundo->conquistas('primeira_arma', 'arsenal_completo');
        $heroi = $this->entrarComHeroi(['ouro' => 0]);

        $itens = [
            'arma' => $this->mundo->item('arma', ['ataque' => 3]),
            'escudo' => $this->mundo->item('escudo', ['defesa' => 3]),
            'acessorio' => $this->mundo->item('acessorio', ['ataque' => 1]),
        ];
        foreach ($itens as $item) {
            $this->mundo->darItem($heroi, $item);
        }

        // ACT
        $this->post(route('inventario.equipar', $itens['arma']));
        $this->post(route('inventario.equipar', $itens['escudo']));
        $this->from(route('inventario'))->post(route('inventario.equipar', $itens['acessorio']));

        // ASSERT: 30 da primeira arma + 60 do arsenal.
        $this->assertStringContainsString('Arsenal completo', (string) session('sucesso'));
        $this->assertSame(90, $heroi->refresh()->ouro);
    }

    #[Test]
    public function usar_pocao_fora_de_batalha_cura_respeitando_o_teto(): void
    {
        // ARRANGE: HP 50/80, poção de 50 → cura só 30.
        $this->mundo->conquistas('primeira_pocao');
        $heroi = $this->entrarComHeroi(['hp_atual' => 50, 'ouro' => 0]);
        $pocao = $this->mundo->item('pocao', ['cura_hp' => 50]);
        $this->mundo->darItem($heroi, $pocao);

        // ACT
        $this->from(route('inventario'))->post(route('inventario.usar', $pocao));

        // ASSERT
        $heroi->refresh();
        $this->assertSame(80, $heroi->hp_atual);
        $this->assertSame(20, $heroi->ouro, 'objetivos_ouro.primeira_pocao');
        $this->assertSame(0, ItemDoInventario::quantidade($heroi->id, $pocao->id));
    }

    #[Test]
    public function uma_arma_nao_se_bebe(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi(['hp_atual' => 10]);
        $arma = $this->mundo->item('arma', ['ataque' => 5]);
        $this->mundo->darItem($heroi, $arma);

        // ACT + ASSERT
        $this->from(route('inventario'))->post(route('inventario.usar', $arma))
            ->assertSessionHas('erro');

        $this->assertSame(10, $heroi->refresh()->hp_atual);
        $this->assertSame(1, ItemDoInventario::quantidade($heroi->id, $arma->id));
    }

    #[Test]
    public function descartar_remove_o_item(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi();
        $item = $this->mundo->item('acessorio', ['defesa' => 1]);
        $this->mundo->darItem($heroi, $item);

        // ACT
        $this->post(route('inventario.descartar', $item))->assertRedirect();

        // ASSERT
        $this->assertSame(0, ItemDoInventario::quantidade($heroi->id, $item->id));
    }

    #[Test]
    public function desequipar_zera_os_bonus_do_slot(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi();
        $arma = $this->mundo->item('arma', ['ataque' => 7]);
        $this->mundo->darItem($heroi, $arma, equipado: true);

        // ACT
        $this->post(route('inventario.desequipar', $arma))->assertRedirect();

        // ASSERT
        $this->assertSame(0, ItemDoInventario::bonusEquipados($heroi->id)['ataque']);
    }

    #[Test]
    public function o_inventario_lista_os_itens_na_ordem_do_jogo(): void
    {
        // O legado usava `FIELD()`, que só existe no MySQL. A ordem é parte da UI.

        // ARRANGE
        $heroi = $this->entrarComHeroi();
        $this->mundo->darItem($heroi, $this->mundo->item('pocao', ['cura_hp' => 10], ['nome' => 'Poção']));
        $this->mundo->darItem($heroi, $this->mundo->item('arma', ['ataque' => 1], ['nome' => 'Espada']));
        $this->mundo->darItem($heroi, $this->mundo->item('escudo', ['defesa' => 1], ['nome' => 'Escudo']));

        // ACT
        $tipos = ItemDoInventario::doPersonagem($heroi->id)->map(fn ($l): string => $l->item->tipo)->all();

        // ASSERT
        $this->assertSame(['arma', 'escudo', 'pocao'], $tipos);
    }

    /** @param  array<string,mixed>  $sobrescritas */
    private function entrarComHeroi(array $sobrescritas = []): Personagem
    {
        $heroi = $this->mundo->heroi('mago', $sobrescritas);
        $this->actingAs(Usuario::query()->findOrFail($heroi->usuario_id));

        return $heroi;
    }
}
