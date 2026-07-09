<?php

declare(strict_types=1);

namespace Algorithmia\Testes\Motor;

use Algorithmia\Testes\Suporte\Mundo;
use Algorithmia\Testes\Suporte\MotorComSorteioFixo;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Vetores-ouro do motor de batalha.
 *
 * Este arquivo é o CONTRATO que o port em Laravel terá de reproduzir número a
 * número. Cada valor esperado é derivado à mão das constantes de
 * config/config.php — não de um snapshot cego do comportamento atual. Se a
 * regra mudar de propósito, o teste muda junto e o diff mostra a decisão.
 *
 * Herói padrão: mago (ataque 12, defesa 4, HP 80, MP 60), nível 1, sem itens.
 * Dano de um acerto = round(base * multCombo * multEspecial * furia), onde
 *   base       = heroi_ataque + nivel * DANO_BASE_POR_NIVEL + dificuldade * 2
 *   multCombo  = max(1, 1 + (combo - 1) * COMBO_BONUS)   [combo já incrementado]
 *   furia      = 1.0 fora da morte súbita
 * Com dificuldade 1: base = 12 + 3 + 2 = 17.
 */
final class CombateTest extends TestCase
{
    private Mundo $mundo;

    protected function setUp(): void
    {
        $this->mundo = new Mundo();
        $this->mundo->limpar();
    }

    #[Test]
    public function deve_escalar_o_dano_com_o_combo_e_saturar_no_teto(): void
    {
        // ARRANGE: inimigo robusto o bastante para sobreviver a cinco acertos.
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase(['inimigo_hp' => 1000]);
        $motor = $this->motorCom($heroi, $fase, 5);

        // ACT + ASSERT: o combo entra no cálculo já incrementado.
        $esperado = [17, 21, 26, 30, 30]; // 17 * {1.0, 1.25, 1.5, 1.75, 1.75}
        $combos = [1, 2, 3, 4, 4];        // COMBO_MAX = 4

        foreach ($esperado as $turno => $dano) {
            $r = $motor->responder(0);
            $this->assertTrue($r['correto'], "turno {$turno} deveria ser acerto");
            $this->assertSame($dano, $r['dano_inimigo'], "dano do turno {$turno}");
            $this->assertSame($combos[$turno], $r['combo'], "combo do turno {$turno}");
        }
    }

    #[Test]
    public function deve_zerar_o_combo_no_erro_e_aplicar_dano_ao_heroi(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi('mago'); // defesa 4 → intdiv(4,2) = 2
        $fase = $this->mundo->fase(['inimigo_hp' => 1000, 'inimigo_ataque' => 10]);
        $motor = $this->motorCom($heroi, $fase, 3);

        // ACT
        $motor->responder(0);                 // acerto: combo 1
        $erro = $motor->responder(1);         // erro: combo zera, herói apanha
        $depois = $motor->responder(0);       // acerto: combo recomeça em 1

        // ASSERT: dano recebido = max(1, ataque_inimigo - intdiv(defesa, 2)) = 8
        $this->assertFalse($erro['correto']);
        $this->assertSame(8, $erro['dano_heroi']);
        $this->assertSame(0, $erro['bloqueado'], 'sem escudo, nada é bloqueado');
        $this->assertSame(80 - 8, $erro['estado']['heroi_hp']);
        $this->assertSame(0, $erro['estado']['combo']);
        $this->assertSame(17, $depois['dano_inimigo'], 'combo reiniciado → sem bônus');
    }

    #[Test]
    public function deve_dobrar_o_dano_do_especial_consumindo_mana(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi('mago'); // MP 60; CUSTO_MP_ESPECIAL = 15
        $fase = $this->mundo->fase(['inimigo_hp' => 1000]);
        $motor = $this->motorCom($heroi, $fase, 3);

        // ACT
        $armar = $motor->armarEspecial($heroi);
        $comEspecial = $motor->responder(0);
        $seguinte = $motor->responder(0);

        // ASSERT
        $this->assertTrue($armar['ok']);
        $this->assertSame(45, $armar['estado']['heroi_mp'], 'MP debitado ao armar');
        $this->assertSame(34, $comEspecial['dano_inimigo'], '17 * 1.0 * MULTIPLICADOR_ESPECIAL');
        $this->assertFalse($comEspecial['estado']['especial_armado'], 'especial se gasta no acerto');
        $this->assertSame(21, $seguinte['dano_inimigo'], 'volta ao normal: 17 * 1.25 (combo 2)');

        // O MP debitado é persistido no personagem, não só na sessão.
        $this->assertSame(45, (int) $this->mundo->recarregarHeroi((int) $heroi['id'])['mp_atual']);
    }

    #[Test]
    public function deve_recusar_o_especial_sem_mana_suficiente(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi('mago', ['mp_atual' => 14]); // abaixo do custo (15)
        $fase = $this->mundo->fase();
        $motor = $this->motorCom($heroi, $fase, 2);

        // ACT
        $r = $motor->armarEspecial($heroi);

        // ASSERT
        $this->assertSame('Mana insuficiente.', $r['erro']);
    }

    #[Test]
    public function deve_creditar_ataque_e_defesa_dos_itens_equipados(): void
    {
        // ARRANGE: arma +5 de ataque, escudo +4 de defesa.
        $heroi = $this->mundo->heroi('mago');
        $arma = $this->mundo->item('arma', ['ataque' => 5]);
        $escudo = $this->mundo->item('escudo', ['defesa' => 4]);
        $this->mundo->darItem((int) $heroi['id'], (int) $arma['id'], 1, true);
        $this->mundo->darItem((int) $heroi['id'], (int) $escudo['id'], 1, true);

        $fase = $this->mundo->fase(['inimigo_hp' => 1000, 'inimigo_ataque' => 10]);
        $motor = $this->motorCom($heroi, $fase, 3);

        // ACT
        $acerto = $motor->responder(0);
        $erro = $motor->responder(1);

        // ASSERT: base = (12+5) + 3 + 2 = 22; a fatia da arma é 5 * multCombo(1.0).
        $this->assertSame(22, $acerto['dano_inimigo']);
        $this->assertSame(5, $acerto['dano_equip'], 'quanto do dano veio da arma');

        // Defesa 4+4=8 → recebe max(1, 10 - 4) = 6; sem escudo receberia 10-2 = 8.
        $this->assertSame(6, $erro['dano_heroi']);
        $this->assertSame(2, $erro['bloqueado'], 'o escudo evitou 2 de dano');
    }

    #[Test]
    public function deve_entrar_em_morte_subita_com_furia_crescente_apos_o_limite(): void
    {
        // ARRANGE: limite de ritmo = 1 → a partir da 2ª resposta é Duelo Final.
        $heroi = $this->mundo->heroi('mago', ['hp_atual' => 1000, 'hp_max' => 1000]);
        $fase = $this->mundo->fase(['inimigo_hp' => 1000]);
        $motor = $this->motorCom($heroi, $fase, 3, limite: 1);

        // ACT
        $t1 = $motor->responder(0);
        $t2 = $motor->responder(0);
        $t3 = $motor->responder(0);

        // ASSERT
        // A fúria de um turno é calculada ANTES de o índice avançar, e a rodada
        // súbita só é incrementada DEPOIS. Logo, o estado devolvido por um turno
        // já anuncia a rodada que valerá no turno SEGUINTE — o turno que cruza o
        // limite ainda bate com fúria 1.0, mas volta com morte_subita = true.
        $this->assertTrue($t1['estado']['morte_subita'], 'o gatilho arma ao cruzar o limite');
        $this->assertSame(1, $t1['estado']['rodada_subita']);
        $this->assertSame(17, $t1['dano_inimigo'], 'este turno ainda não sofre fúria');

        $this->assertSame(2, $t2['estado']['rodada_subita']);
        $this->assertSame(32, $t2['dano_inimigo'], '17 * combo 1.25 * furia 1.5 = 31.875');

        $this->assertSame(3, $t3['estado']['rodada_subita']);
        $this->assertSame(51, $t3['dano_inimigo'], '17 * combo 1.5 * furia 2.0');
    }

    #[Test]
    public function deve_limitar_a_furia_ao_teto_configurado(): void
    {
        // ARRANGE: MORTE_SUBITA_RAGE_MAX = 4.0 → alcançado na 6ª rodada súbita.
        $heroi = $this->mundo->heroi('mago', ['hp_atual' => 5000, 'hp_max' => 5000]);
        $fase = $this->mundo->fase(['inimigo_hp' => 100000, 'inimigo_ataque' => 10]);
        $motor = $this->motorCom($heroi, $fase, 12, limite: 1);

        // ACT: erra sempre — o dano recebido isola a fúria (não há combo no erro).
        $motor->responder(0); // acerto inicial só para abrir a morte súbita
        $danos = [];
        for ($i = 0; $i < 9; $i++) {
            $danos[] = $motor->responder(1)['dano_heroi'];
        }

        // ASSERT: dano base recebido = 8; cresce 8*furia até saturar em 8*4.0 = 32.
        $this->assertSame([12, 16, 20, 24, 28, 32, 32, 32, 32], $danos);
    }

    #[Test]
    public function a_batalha_nunca_termina_por_acabarem_as_perguntas(): void
    {
        // ARRANGE: dois desafios, mas ninguém morre.
        $heroi = $this->mundo->heroi('mago', ['hp_atual' => 5000, 'hp_max' => 5000]);
        $fase = $this->mundo->fase(['inimigo_hp' => 100000]);
        $motor = $this->motorCom($heroi, $fase, 2);

        // ACT: responde muito além do número de perguntas disponíveis.
        for ($i = 0; $i < 8; $i++) {
            $r = $motor->responder(0);
            $this->assertArrayNotHasKey('erro', $r, "turno {$i} ficou sem desafio");
        }

        // ASSERT: segue viva, em morte súbita, reciclando perguntas.
        $this->assertNull($r['resultado']);
        $this->assertFalse($r['estado']['finalizada']);
        $this->assertTrue($r['estado']['morte_subita']);
    }

    #[Test]
    public function deve_vencer_quando_o_hp_do_inimigo_zera(): void
    {
        // ARRANGE: 17 + 21 = 38 ≥ 35 → dois acertos derrubam o inimigo.
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase(['inimigo_hp' => 35]);
        $motor = $this->motorCom($heroi, $fase, 4);

        // ACT
        $motor->responder(0);
        $final = $motor->responder(0);

        // ASSERT
        $this->assertSame('vitoria', $final['resultado']);
        $this->assertSame(0, $final['estado']['inimigo_hp']);
        $this->assertTrue($final['estado']['finalizada']);
        $this->assertSame(2, $final['resumo']['acertos']);
        $this->assertSame(0, $final['resumo']['erros']);
    }

    #[Test]
    public function deve_perder_quando_o_hp_do_heroi_zera_e_persistir_ao_menos_1_de_hp(): void
    {
        // ARRANGE: HP 8 → um único erro (dano 8) derruba o herói.
        $heroi = $this->mundo->heroi('mago', ['hp_atual' => 8]);
        $fase = $this->mundo->fase(['inimigo_ataque' => 10]);
        $motor = $this->motorCom($heroi, $fase, 3);

        // ACT
        $final = $motor->responder(1);

        // ASSERT
        $this->assertSame('derrota', $final['resultado']);
        $this->assertSame(0, $final['estado']['heroi_hp']);
        $this->assertNotEmpty($final['resumo']['dica']);

        // O personagem nunca fica com 0 no banco — reviveria travado.
        $this->assertSame(1, (int) $this->mundo->recarregarHeroi((int) $heroi['id'])['hp_atual']);
    }

    #[Test]
    public function o_fragmento_da_ia_acerta_sozinho_e_cobra_reputacao(): void
    {
        // ARRANGE: o motor acha o Fragmento pelo svg_slug, não pelo nome.
        $heroi = $this->mundo->heroi('mago');
        $fragmento = $this->mundo->item('especial', [], ['svg_slug' => 'item-fragmento-ia']);
        $this->mundo->darItem((int) $heroi['id'], (int) $fragmento['id'], 2);

        $fase = $this->mundo->fase(['inimigo_hp' => 1000]);
        $motor = $this->motorCom($heroi, $fase, 3);

        // ACT: resposta nula, mas conta como acerto.
        $r = $motor->usarFragmentoIa($heroi);

        // ASSERT
        $this->assertTrue($r['correto']);
        $this->assertTrue($r['via_ia']);
        $this->assertSame(17, $r['dano_inimigo'], 'acerto via IA causa o mesmo dano');
        $this->assertSame(REPUTACAO_USO_IA, $r['reputacao'], 'reputação foi de 0 para -10');

        $inventario = new \Inventario();
        $this->assertSame(1, $inventario->quantidade((int) $heroi['id'], (int) $fragmento['id']));

        // A marca fica registrada: define estrelas e a conquista "Puro de Coração".
        $this->assertTrue($motor->estado()['usou_ia']);

        // E o log distingue o acerto honesto do acerto comprado.
        $log = $this->mundo->logDeRespostas((int) $heroi['id']);
        $this->assertSame(1, (int) $log[0]['correta']);
        $this->assertSame(1, (int) $log[0]['usou_ia']);
    }

    #[Test]
    public function o_fragmento_sem_batalha_ativa_nao_consome_item_nem_reputacao(): void
    {
        // ARRANGE: item em mãos, mas nenhuma batalha em andamento.
        $heroi = $this->mundo->heroi('mago');
        $fragmento = $this->mundo->item('especial', [], ['svg_slug' => 'item-fragmento-ia']);
        $this->mundo->darItem((int) $heroi['id'], (int) $fragmento['id'], 1);

        // ACT
        $r = (new MotorComSorteioFixo())->usarFragmentoIa($heroi);

        // ASSERT
        $this->assertSame('Nenhuma batalha ativa.', $r['erro']);
        $this->assertSame(1, (new \Inventario())->quantidade((int) $heroi['id'], (int) $fragmento['id']));
        $this->assertSame(0, (int) $this->mundo->recarregarHeroi((int) $heroi['id'])['reputacao']);
    }

    #[Test]
    public function a_pocao_cura_respeitando_o_teto_e_contabiliza_o_efetivo(): void
    {
        // ARRANGE: HP 50/80; poção de 50 só pode curar 30.
        $heroi = $this->mundo->heroi('mago', ['hp_atual' => 50]);
        $pocao = $this->mundo->item('pocao', ['cura_hp' => 50]);
        $this->mundo->darItem((int) $heroi['id'], (int) $pocao['id'], 1);

        $fase = $this->mundo->fase(['inimigo_hp' => 1000]);
        $motor = $this->motorCom($heroi, $fase, 3);

        // ACT
        $r = $motor->usarPocao($heroi, (int) $pocao['id']);

        // ASSERT
        $this->assertTrue($r['ok']);
        $this->assertSame(80, $r['estado']['heroi_hp'], 'não passa do hp_max');
        $this->assertSame(30, $motor->estado()['hp_curado_total'], 'só o que curou de fato');
        $this->assertSame(0, (new \Inventario())->quantidade((int) $heroi['id'], (int) $pocao['id']));
    }

    #[Test]
    public function o_estado_publico_nunca_vaza_o_gabarito(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase();
        $motor = $this->motorCom($heroi, $fase, 2);

        // ACT
        $publico = $motor->estadoPublico();

        // ASSERT: o desafio vai para a tela sem 'resposta' nem 'explicacao'.
        $this->assertArrayHasKey('desafio', $publico);
        $this->assertArrayNotHasKey('resposta', $publico['desafio']);
        $this->assertArrayNotHasKey('explicacao', $publico['desafio']);
        $this->assertArrayNotHasKey('desafios', $publico, 'a sequência inteira fica no servidor');
    }

    /**
     * @return array<array{0:string,1:mixed,2:bool}> [tipo do desafio, resposta enviada, esperado]
     */
    public static function respostasPorTipo(): array
    {
        return [
            'vf verdadeiro como string' => ['vf', 'true', true],
            'vf com "v"' => ['vf', 'v', true],
            'vf negativo' => ['vf', 'false', false],
            'completar ignora espaços extras' => ['completar', '  echo   "oi" ;  ', true],
            'completar exige o texto certo' => ['completar', 'print "oi"', false],
            'ordenar na sequência correta' => ['ordenar', ['2', '0', '1'], true],
            'ordenar fora de ordem' => ['ordenar', ['0', '1', '2'], false],
        ];
    }

    #[Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('respostasPorTipo')]
    public function deve_verificar_cada_tipo_de_desafio(string $tipo, mixed $resposta, bool $esperado): void
    {
        // ARRANGE
        $gabaritos = [
            'vf' => json_encode(true, JSON_THROW_ON_ERROR),
            'completar' => json_encode('echo "oi"', JSON_THROW_ON_ERROR),
            'ordenar' => json_encode([2, 0, 1], JSON_THROW_ON_ERROR),
        ];
        $heroi = $this->mundo->heroi('mago', ['hp_atual' => 1000, 'hp_max' => 1000]);
        $fase = $this->mundo->fase(['inimigo_hp' => 1000]);
        $desafio = $this->mundo->desafioCru((int) $fase['id'], [
            'tipo' => $tipo,
            'resposta' => $gabaritos[$tipo],
        ]);
        $motor = (new MotorComSorteioFixo())->comSequencia([$desafio]);
        $motor->iniciar($heroi, $fase);

        // ACT
        $r = $motor->responder($resposta);

        // ASSERT
        $this->assertSame($esperado, $r['correto']);
    }

    /** Monta uma batalha com N desafios idênticos de dificuldade 1, em ordem fixa. */
    private function motorCom(array $heroi, array $fase, int $quantos, ?int $limite = null): MotorComSorteioFixo
    {
        $desafios = [];
        for ($i = 0; $i < $quantos; $i++) {
            $desafios[] = $this->mundo->desafio((int) $fase['id'], dificuldade: 1, ordem: $i);
        }
        $motor = (new MotorComSorteioFixo())->comSequencia($desafios, $limite);
        $motor->iniciar($heroi, $fase);
        return $motor;
    }
}
