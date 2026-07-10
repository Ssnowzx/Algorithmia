<?php

declare(strict_types=1);

namespace Tests\Dominio;

use App\Dominio\Combate\BatalhaEmMemoria;
use App\Dominio\Combate\CorretorDeRespostas;
use App\Dominio\Combate\MotorDeBatalha;
use App\Dominio\Progressao\ServicoDeConquistas;
use App\Dominio\Progressao\ServicoDeReputacao;
use App\Models\ItemDoInventario;
use App\Models\RespostaLog;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\Suporte\SorteioFixo;
use Tests\TestCase;

/**
 * Vetores-ouro do motor de batalha, portados de `tests/Motor/CombateTest.php`
 * do legado. **Os mesmos números.** Divergência aqui é bug do port, não licença
 * para rebalancear o jogo.
 *
 * Herói padrão: mago (ataque 12, defesa 4, HP 80, MP 60), nível 1, sem itens.
 * Dano de um acerto = round(base * multCombo * multEspecial * furia), onde
 *   base      = heroiAtaque + nivel * dano_base_por_nivel + dificuldade * 2
 *   multCombo = max(1, 1 + (combo - 1) * combo_bonus)   [combo já incrementado]
 *   furia     = 1.0 fora da morte súbita
 * Com dificuldade 1: base = 12 + 3 + 2 = 17.
 */
final class CombateTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    private BatalhaEmMemoria $repositorio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mundo = new Mundo;
        $this->repositorio = new BatalhaEmMemoria;
    }

    #[Test]
    public function deve_escalar_o_dano_com_o_combo_e_saturar_no_teto(): void
    {
        // ARRANGE: inimigo robusto o bastante para sobreviver a cinco acertos.
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase(['inimigo_hp' => 1000]);
        $motor = $this->mundo->motor($heroi, $fase, 5, null, $this->repositorio);

        // ACT + ASSERT
        $esperado = [17, 21, 26, 30, 30]; // 17 * {1.0, 1.25, 1.5, 1.75, 1.75}
        $combos = [1, 2, 3, 4, 4];        // combo_max = 4

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
        // ARRANGE: defesa 4 → intdiv(4, 2) = 2
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase(['inimigo_hp' => 1000, 'inimigo_ataque' => 10]);
        $motor = $this->mundo->motor($heroi, $fase, 3, null, $this->repositorio);

        // ACT
        $motor->responder(0);
        $erro = $motor->responder(1);
        $depois = $motor->responder(0);

        // ASSERT: dano recebido = max(1, ataque_inimigo - intdiv(defesa, 2)) = 8
        $this->assertFalse($erro['correto']);
        $this->assertSame(8, $erro['dano_heroi']);
        $this->assertSame(0, $erro['bloqueado'], 'sem escudo, nada é bloqueado');
        $this->assertSame(72, $erro['estado']['heroi_hp']);
        $this->assertSame(0, $erro['estado']['combo']);
        $this->assertSame(17, $depois['dano_inimigo'], 'combo reiniciado → sem bônus');
    }

    #[Test]
    public function deve_dobrar_o_dano_do_especial_consumindo_mana(): void
    {
        // ARRANGE: MP 60; custo_mp_especial = 15
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase(['inimigo_hp' => 1000]);
        $motor = $this->mundo->motor($heroi, $fase, 3, null, $this->repositorio);

        // ACT
        $armar = $motor->armarEspecial($heroi);
        $comEspecial = $motor->responder(0);
        $seguinte = $motor->responder(0);

        // ASSERT
        $this->assertTrue($armar['ok']);
        $this->assertSame(45, $armar['estado']['heroi_mp'], 'MP debitado ao armar');
        $this->assertSame(34, $comEspecial['dano_inimigo'], '17 * 1.0 * multiplicador_especial');
        $this->assertFalse($comEspecial['estado']['especial_armado'], 'o especial se gasta no acerto');
        $this->assertSame(21, $seguinte['dano_inimigo'], 'volta ao normal: 17 * 1.25 (combo 2)');

        // O MP debitado é persistido no personagem, não só no estado da batalha.
        $this->assertSame(45, $heroi->refresh()->mp_atual);
    }

    #[Test]
    public function deve_recusar_o_especial_sem_mana_suficiente(): void
    {
        // ARRANGE: abaixo do custo (15)
        $heroi = $this->mundo->heroi('mago', ['mp_atual' => 14]);
        $fase = $this->mundo->fase();
        $motor = $this->mundo->motor($heroi, $fase, 2, null, $this->repositorio);

        // ACT + ASSERT
        $this->assertSame('Mana insuficiente.', $motor->armarEspecial($heroi)['erro']);
    }

    #[Test]
    public function deve_creditar_ataque_e_defesa_dos_itens_equipados(): void
    {
        // ARRANGE: arma +5 de ataque, escudo +4 de defesa.
        $heroi = $this->mundo->heroi('mago');
        $this->mundo->darItem($heroi, $this->mundo->item('arma', ['ataque' => 5]), equipado: true);
        $this->mundo->darItem($heroi, $this->mundo->item('escudo', ['defesa' => 4]), equipado: true);

        $fase = $this->mundo->fase(['inimigo_hp' => 1000, 'inimigo_ataque' => 10]);
        $motor = $this->mundo->motor($heroi, $fase, 3, null, $this->repositorio);

        // ACT
        $acerto = $motor->responder(0);
        $erro = $motor->responder(1);

        // ASSERT: base = (12 + 5) + 3 + 2 = 22; a fatia da arma é 5 * multCombo(1.0).
        $this->assertSame(22, $acerto['dano_inimigo']);
        $this->assertSame(5, $acerto['dano_equip'], 'quanto do dano veio da arma');

        // Defesa 4+4 = 8 → recebe max(1, 10 - 4) = 6; sem escudo receberia 10 - 2 = 8.
        $this->assertSame(6, $erro['dano_heroi']);
        $this->assertSame(2, $erro['bloqueado'], 'o escudo evitou 2 de dano');
    }

    #[Test]
    public function deve_entrar_em_morte_subita_com_furia_crescente_apos_o_limite(): void
    {
        // ARRANGE: limite de ritmo = 1 → a partir da 2ª resposta é Duelo Final.
        $heroi = $this->mundo->heroi('mago', ['hp_atual' => 1000, 'hp_max' => 1000]);
        $fase = $this->mundo->fase(['inimigo_hp' => 1000]);
        $motor = $this->mundo->motor($heroi, $fase, 3, 1, $this->repositorio);

        // ACT
        $t1 = $motor->responder(0);
        $t2 = $motor->responder(0);
        $t3 = $motor->responder(0);

        // ASSERT
        // A fúria de um turno é calculada ANTES de o índice avançar, e a rodada
        // súbita só é incrementada depois. Logo, o estado devolvido por um turno
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
        // ARRANGE: rage_max = 4.0 → alcançado na 6ª rodada súbita.
        $heroi = $this->mundo->heroi('mago', ['hp_atual' => 5000, 'hp_max' => 5000]);
        $fase = $this->mundo->fase(['inimigo_hp' => 100000, 'inimigo_ataque' => 10]);
        $motor = $this->mundo->motor($heroi, $fase, 12, 1, $this->repositorio);

        // ACT: erra sempre — o dano recebido isola a fúria (não há combo no erro).
        $motor->responder(0); // acerto inicial só para abrir a morte súbita
        $danos = [];
        for ($i = 0; $i < 9; $i++) {
            $danos[] = $motor->responder(1)['dano_heroi'];
        }

        // ASSERT: dano base recebido = 8; cresce 8 * furia até saturar em 8 * 4.0 = 32.
        $this->assertSame([12, 16, 20, 24, 28, 32, 32, 32, 32], $danos);
    }

    #[Test]
    public function a_batalha_nunca_termina_por_acabarem_as_perguntas(): void
    {
        // ARRANGE: dois desafios, mas ninguém morre.
        $heroi = $this->mundo->heroi('mago', ['hp_atual' => 5000, 'hp_max' => 5000]);
        $fase = $this->mundo->fase(['inimigo_hp' => 100000]);
        $motor = $this->mundo->motor($heroi, $fase, 2, null, $this->repositorio);

        // ACT: responde muito além do número de perguntas disponíveis.
        $r = [];
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
        $motor = $this->mundo->motor($heroi, $fase, 4, null, $this->repositorio);

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
        $motor = $this->mundo->motor($heroi, $fase, 3, null, $this->repositorio);

        // ACT
        $final = $motor->responder(1);

        // ASSERT
        $this->assertSame('derrota', $final['resultado']);
        $this->assertSame(0, $final['estado']['heroi_hp']);
        $this->assertNotEmpty($final['resumo']['dica']);

        // O personagem nunca fica com 0 no banco: reviveria travado.
        $this->assertSame(1, $heroi->refresh()->hp_atual);
    }

    #[Test]
    public function o_fragmento_da_ia_acerta_sozinho_e_cobra_reputacao(): void
    {
        // ARRANGE: o motor acha o Fragmento pelo svg_slug, não pelo nome.
        $heroi = $this->mundo->heroi('mago');
        $fragmento = $this->mundo->item('especial', [], ['svg_slug' => 'item-fragmento-ia']);
        $this->mundo->darItem($heroi, $fragmento, quantidade: 2);

        $fase = $this->mundo->fase(['inimigo_hp' => 1000]);
        $motor = $this->mundo->motor($heroi, $fase, 3, null, $this->repositorio);

        // ACT: resposta nula, mas conta como acerto.
        $r = $motor->usarFragmentoIa($heroi);

        // ASSERT
        $this->assertTrue($r['correto']);
        $this->assertTrue($r['via_ia']);
        $this->assertSame(17, $r['dano_inimigo'], 'acerto via IA causa o mesmo dano');
        $this->assertSame(-10, $r['reputacao'], 'reputação foi de 0 para -10');

        $this->assertSame(1, ItemDoInventario::quantidade($heroi->id, $fragmento->id));

        // A marca fica registrada: define estrelas e a conquista "Puro de Coração".
        $this->assertTrue($motor->estado()?->usouIa);

        // E o log distingue o acerto honesto do acerto comprado.
        $log = RespostaLog::query()->where('personagem_id', $heroi->id)->firstOrFail();
        $this->assertTrue($log->correta);
        $this->assertTrue($log->usou_ia);
    }

    #[Test]
    public function o_fragmento_sem_batalha_ativa_nao_consome_item_nem_reputacao(): void
    {
        // ARRANGE: item em mãos, mas nenhuma batalha em andamento.
        $heroi = $this->mundo->heroi('mago');
        $fragmento = $this->mundo->item('especial', [], ['svg_slug' => 'item-fragmento-ia']);
        $this->mundo->darItem($heroi, $fragmento);

        $motor = new MotorDeBatalha(
            $this->repositorio,
            new SorteioFixo([]),
            new CorretorDeRespostas,
            new ServicoDeReputacao,
            new ServicoDeConquistas,
        );

        // ACT
        $r = $motor->usarFragmentoIa($heroi);

        // ASSERT
        $this->assertSame('Nenhuma batalha ativa.', $r['erro']);
        $this->assertSame(1, ItemDoInventario::quantidade($heroi->id, $fragmento->id));
        $this->assertSame(0, $heroi->refresh()->reputacao);
    }

    #[Test]
    public function a_pocao_cura_respeitando_o_teto_e_contabiliza_o_efetivo(): void
    {
        // ARRANGE: HP 50/80; poção de 50 só pode curar 30.
        $heroi = $this->mundo->heroi('mago', ['hp_atual' => 50]);
        $pocao = $this->mundo->item('pocao', ['cura_hp' => 50]);
        $this->mundo->darItem($heroi, $pocao);

        $fase = $this->mundo->fase(['inimigo_hp' => 1000]);
        $motor = $this->mundo->motor($heroi, $fase, 3, null, $this->repositorio);

        // ACT
        $r = $motor->usarPocao($heroi, $pocao->id);

        // ASSERT
        $this->assertTrue($r['ok']);
        $this->assertSame(80, $r['estado']['heroi_hp'], 'não passa do hp_max');
        $this->assertSame(30, $motor->estado()?->hpCuradoTotal, 'só o que curou de fato');
        $this->assertSame(0, ItemDoInventario::quantidade($heroi->id, $pocao->id));
    }

    #[Test]
    public function o_estado_publico_nunca_vaza_o_gabarito(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase();
        $motor = $this->mundo->motor($heroi, $fase, 2, null, $this->repositorio);

        // ACT
        $publico = $motor->estado()?->paraCliente() ?? [];

        // ASSERT: o desafio vai para a tela sem 'resposta' nem 'explicacao'.
        $this->assertArrayHasKey('desafio', $publico);
        $this->assertArrayNotHasKey('resposta', $publico['desafio']);
        $this->assertArrayNotHasKey('explicacao', $publico['desafio']);
        $this->assertArrayNotHasKey('desafios', $publico, 'a sequência inteira fica no servidor');

        // E o JSON serializado também não o contém, por nenhum caminho.
        $this->assertStringNotContainsString('resposta', (string) json_encode($publico));
    }

    /** @return array<string,array{0:string,1:mixed,2:bool}> [tipo do desafio, resposta enviada, esperado] */
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
    #[DataProvider('respostasPorTipo')]
    public function deve_verificar_cada_tipo_de_desafio(string $tipo, mixed $resposta, bool $esperado): void
    {
        // ARRANGE
        $gabaritos = ['vf' => true, 'completar' => 'echo "oi"', 'ordenar' => [2, 0, 1]];

        $heroi = $this->mundo->heroi('mago', ['hp_atual' => 1000, 'hp_max' => 1000]);
        $fase = $this->mundo->fase(['inimigo_hp' => 1000]);
        $desafio = $this->mundo->desafio($fase->id, sobrescritas: [
            'tipo' => $tipo,
            'resposta' => $gabaritos[$tipo],
        ]);

        $motor = new MotorDeBatalha(
            $this->repositorio,
            new SorteioFixo([$this->mundo->linha($desafio)]),
            new CorretorDeRespostas,
            new ServicoDeReputacao,
            new ServicoDeConquistas,
        );
        $motor->iniciar($heroi, $fase);

        // ACT + ASSERT
        $this->assertSame($esperado, $motor->responder($resposta)['correto']);
    }
}
