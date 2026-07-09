<?php

declare(strict_types=1);

namespace App\Dominio\Combate;

use App\Dominio\Progressao\ServicoDeConquistas;
use App\Dominio\Progressao\ServicoDeReputacao;
use App\Models\Fase;
use App\Models\Item;
use App\Models\ItemDoInventario;
use App\Models\Personagem;
use App\Models\RespostaLog;
use Illuminate\Support\Str;

/**
 * Motor de batalha por turnos. Porte de `app/services/BatalhaService.php`.
 *
 * Não conhece HTTP nem sessão: recebe onde guardar o estado e como sortear as
 * perguntas. Tudo o mais é a aritmética que os vetores-ouro protegem, número a
 * número — ver `tests/Dominio/CombateTest.php`.
 *
 * Duas regras que parecem detalhe e não são:
 * - A batalha SÓ termina quando um HP zera. Acabarem as perguntas abre o Duelo
 *   Final (morte súbita, com fúria crescente), nunca decide o combate.
 * - A fúria de um turno é calculada ANTES de o índice avançar, e a rodada súbita
 *   só é incrementada depois. Logo, o estado devolvido por um turno já anuncia a
 *   rodada que valerá no turno seguinte.
 */
final class MotorDeBatalha
{
    public function __construct(
        private readonly RepositorioDeBatalha $repositorio,
        private readonly SorteadorDeDesafios $sorteador,
        private readonly CorretorDeRespostas $corretor,
        private readonly ServicoDeReputacao $reputacao,
        private readonly ServicoDeConquistas $conquistas,
    ) {}

    public function iniciar(Personagem $personagem, Fase $fase): EstadoDeBatalha
    {
        $sorteio = $this->sorteador->sortear($personagem, $fase);
        $bonus = ItemDoInventario::bonusEquipados($personagem->id);

        $estado = new EstadoDeBatalha(
            batalhaId: (string) Str::uuid(),
            faseId: $fase->id,
            personagemId: $personagem->id,
            desafios: $sorteio['lista'],
            total: $sorteio['limite'],
            inimigoNome: $fase->inimigo_nome ?: 'Bug Selvagem',
            inimigoSvg: $fase->inimigo_svg ?: 'inimigo-bug',
            inimigoHpMax: $fase->inimigo_hp,
            inimigoHp: $fase->inimigo_hp,
            inimigoAtaque: $fase->inimigo_ataque,
            heroiHp: $personagem->hp_atual,
            heroiHpMax: $personagem->hp_max,
            heroiMp: $personagem->mp_atual,
            heroiMpMax: $personagem->mp_max,
            heroiAtaque: $personagem->ataqueDaClasse() + $bonus['ataque'],
            heroiDefesa: $personagem->defesaDaClasse() + $bonus['defesa'],
            heroiDefesaBase: $personagem->defesaDaClasse(),
            bonusEquipAtaque: $bonus['ataque'],
            bonusEquipDefesa: $bonus['defesa'],
            heroiNivel: $personagem->nivel,
        );

        $this->repositorio->salvar($estado);

        return $estado;
    }

    public function estado(): ?EstadoDeBatalha
    {
        return $this->repositorio->carregar();
    }

    public function limpar(): void
    {
        $this->repositorio->limpar();
    }

    /**
     * Processa a resposta do jogador ao desafio atual.
     *
     * @return array<string,mixed> resultado do turno
     */
    public function responder(mixed $resposta, bool $viaIa = false): array
    {
        $estado = $this->estado();
        if ($estado === null || $estado->finalizada) {
            return ['erro' => 'Nenhuma batalha ativa.'];
        }

        $this->garantirDesafioAtual($estado);
        $desafio = $estado->desafioAtual();
        if ($desafio === null) {
            return ['erro' => 'Nenhuma batalha ativa.'];
        }

        $correto = $viaIa || $this->corretor->acertou($desafio, $resposta);
        RespostaLog::registrar($estado->personagemId, $desafio['id'], $correto, $viaIa);

        $furia = $this->multiplicadorFuria($estado);

        $retorno = [
            'correto' => $correto,
            'via_ia' => $viaIa,
            'explicacao' => $desafio['explicacao'],
            'eventos' => [],
        ];

        if ($correto) {
            $estado->combo = min((int) config('jogo.combate.combo_max'), $estado->combo + 1);
            $estado->acertos++;
            $dano = $this->calcularDano($estado, $desafio['dificuldade'], $furia);
            $estado->inimigoHp = max(0, $estado->inimigoHp - $dano['total']);
            $estado->especialArmado = false;
            $estado->danoTotal += $dano['total'];
            $estado->danoEquipTotal += $dano['equip'];
            $retorno['dano_inimigo'] = $dano['total'];
            $retorno['dano_equip'] = $dano['equip'];
            $retorno['combo'] = $estado->combo;
        } else {
            $estado->combo = 0;
            $estado->erros++;
            // A diferença entre os dois danos é o que o escudo bloqueou.
            $comEquip = max(1, $estado->inimigoAtaque - intdiv($estado->heroiDefesa, 2));
            $semEquip = max(1, $estado->inimigoAtaque - intdiv($estado->heroiDefesaBase, 2));
            $recebido = max(1, (int) round($comEquip * $furia));
            $bloqueado = max(0, (int) round(($semEquip - $comEquip) * $furia));
            $estado->heroiHp = max(0, $estado->heroiHp - $recebido);
            $estado->bloqueadoTotal += $bloqueado;
            $retorno['dano_heroi'] = $recebido;
            $retorno['bloqueado'] = $bloqueado;
        }

        $estado->indice++;

        if ($estado->inimigoHp <= 0) {
            $retorno['resultado'] = 'vitoria';
            $this->finalizar($estado, 'vitoria');
        } elseif ($estado->heroiHp <= 0) {
            $retorno['resultado'] = 'derrota';
            $this->finalizar($estado, 'derrota');
        } else {
            // Ninguém caiu. Ao atingir o limite de ritmo, abre ou segue o Duelo Final.
            if ($estado->indice >= $estado->total) {
                $estado->morteSubita = true;
                $estado->rodadaSubita++;
            }
            $this->garantirDesafioAtual($estado);
            $this->repositorio->salvar($estado);
            $retorno['resultado'] = null;
        }

        $retorno['estado'] = $estado->paraCliente();
        if (! empty($retorno['resultado'])) {
            $retorno['resumo'] = $this->resumo($estado, (string) $retorno['resultado']);
        }

        return $retorno;
    }

    /**
     * Usa o Fragmento da IA Ancestral: acerta o desafio atual, mas cobra
     * reputação e deixa marca permanente na fase.
     *
     * @return array<string,mixed>
     */
    public function usarFragmentoIa(Personagem $personagem): array
    {
        // Valida a batalha ANTES de consumir item e reputação: sem esta guarda,
        // usar o Fragmento fora de combate gastava os dois à toa.
        $estado = $this->estado();
        if ($estado === null || $estado->finalizada) {
            return ['erro' => 'Nenhuma batalha ativa.'];
        }

        $fragmento = Item::fragmentoDaIa();
        if ($fragmento === null || ItemDoInventario::quantidade($personagem->id, $fragmento->id) < 1) {
            return ['erro' => 'Você não possui Fragmentos da IA Ancestral.'];
        }

        ItemDoInventario::remover($personagem->id, $fragmento->id);

        $estado->usouIa = true;
        $this->repositorio->salvar($estado);

        $novaReputacao = $this->reputacao->ajustar($personagem, (int) config('jogo.reputacao.uso_ia'));

        $retorno = $this->responder(null, viaIa: true);
        $retorno['reputacao'] = $novaReputacao;

        return $retorno;
    }

    /**
     * Arma o ataque especial: consome MP e dobra o dano do próximo acerto.
     *
     * @return array<string,mixed>
     */
    public function armarEspecial(Personagem $personagem): array
    {
        $estado = $this->estado();
        if ($estado === null) {
            return ['erro' => 'Nenhuma batalha ativa.'];
        }
        if ($estado->especialArmado) {
            return ['erro' => 'O especial já está carregado.'];
        }

        $custo = (int) config('jogo.combate.custo_mp_especial');
        if ($estado->heroiMp < $custo) {
            return ['erro' => 'Mana insuficiente.'];
        }

        $estado->heroiMp -= $custo;
        $estado->especialArmado = true;
        $this->repositorio->salvar($estado);

        $personagem->update(['mp_atual' => $estado->heroiMp]);

        return ['ok' => true, 'estado' => $estado->paraCliente()];
    }

    /**
     * Usa uma poção durante a batalha (cura HP ou MP, respeitando o teto).
     *
     * @return array<string,mixed>
     */
    public function usarPocao(Personagem $personagem, int $itemId): array
    {
        $estado = $this->estado();
        if ($estado === null) {
            return ['erro' => 'Nenhuma batalha ativa.'];
        }
        if (ItemDoInventario::quantidade($personagem->id, $itemId) < 1) {
            return ['erro' => 'Você não possui esse item.'];
        }

        $item = Item::query()->find($itemId);
        if ($item === null || ! $item->ehPocao()) {
            return ['erro' => 'Item inválido para uso em batalha.'];
        }

        // Acumula o quanto a poção realmente recuperou, respeitando o teto: o
        // resumo de fim de batalha usa isso para mostrar o valor do consumível.
        if ($item->efeito('cura_hp') > 0) {
            $antes = $estado->heroiHp;
            $estado->heroiHp = min($estado->heroiHpMax, $estado->heroiHp + $item->efeito('cura_hp'));
            $estado->hpCuradoTotal += $estado->heroiHp - $antes;
        }
        if ($item->efeito('cura_mp') > 0) {
            $antes = $estado->heroiMp;
            $estado->heroiMp = min($estado->heroiMpMax, $estado->heroiMp + $item->efeito('cura_mp'));
            $estado->mpCuradoTotal += $estado->heroiMp - $antes;
        }

        ItemDoInventario::remover($personagem->id, $itemId);
        $this->repositorio->salvar($estado);

        $retorno = ['ok' => true, 'estado' => $estado->paraCliente()];

        $objetivo = $this->conquistas->concederObjetivo($personagem, 'primeira_pocao');
        if ($objetivo !== null) {
            $retorno['objetivo'] = ['nome' => $objetivo['conquista']->nome, 'ouro' => $objetivo['ouro']];
        }

        return $retorno;
    }

    /** Fúria do Duelo Final; 1.0 fora da morte súbita. */
    private function multiplicadorFuria(EstadoDeBatalha $estado): float
    {
        if (! $estado->morteSubita) {
            return 1.0;
        }

        return min(
            (float) config('jogo.morte_subita.rage_max'),
            1 + (float) config('jogo.morte_subita.rage_step') * $estado->rodadaSubita
        );
    }

    /**
     * Dano de um acerto, com nível, dificuldade, combo, especial e fúria.
     * Devolve também a fatia vinda do equipamento, para o "+X da arma".
     *
     * @return array{total:int,equip:int}
     */
    private function calcularDano(EstadoDeBatalha $estado, int $dificuldade, float $furia): array
    {
        $multCombo = max(1, 1 + ($estado->combo - 1) * (float) config('jogo.combate.combo_bonus'));
        $multEspecial = $estado->especialArmado ? (float) config('jogo.combate.multiplicador_especial') : 1.0;
        $mult = $multCombo * $multEspecial * $furia;

        $base = $estado->heroiAtaque
            + $estado->heroiNivel * (int) config('jogo.combate.dano_base_por_nivel')
            + $dificuldade * 2;

        $total = (int) round($base * $mult);

        // O bônus dos itens já está embutido em heroiAtaque; sua contribuição ao
        // dano é esse bônus vezes os mesmos multiplicadores.
        $equip = (int) round($estado->bonusEquipAtaque * $mult);
        $equip = max(0, min($equip, $total));

        return ['total' => $total, 'equip' => $equip];
    }

    /**
     * Quando o Duelo Final esgota a reserva, recicla a sequência reembaralhada
     * para nunca faltar pergunta. A fúria crescente encerra o combate em poucas
     * rodadas, então a repetição é rara.
     */
    private function garantirDesafioAtual(EstadoDeBatalha $estado): void
    {
        if ($estado->desafioAtual() !== null || $estado->desafios === []) {
            return;
        }

        $reciclar = $estado->desafios;
        shuffle($reciclar);
        foreach ($reciclar as $desafio) {
            $estado->desafios[] = $desafio;
            if ($estado->desafioAtual() !== null) {
                break;
            }
        }
    }

    /** A recompensa é responsabilidade do ServicoDeRecompensa, não do motor. */
    private function finalizar(EstadoDeBatalha $estado, string $resultado): void
    {
        $estado->finalizada = true;
        $estado->resultado = $resultado;

        // O herói nunca fica com 0 no banco: reviveria travado.
        Personagem::query()->whereKey($estado->personagemId)->update([
            'hp_atual' => max(1, $estado->heroiHp),
            'mp_atual' => $estado->heroiMp,
        ]);

        $this->repositorio->salvar($estado);
    }

    /** @return array<string,mixed> */
    private function resumo(EstadoDeBatalha $estado, string $resultado): array
    {
        $resumo = [
            'dano_total' => $estado->danoTotal,
            'dano_arma' => $estado->danoEquipTotal,
            'bloqueado' => $estado->bloqueadoTotal,
            'hp_curado' => $estado->hpCuradoTotal,
            'mp_curado' => $estado->mpCuradoTotal,
            'acertos' => $estado->acertos,
            'erros' => $estado->erros,
            'morte_subita' => $estado->morteSubita,
            'tem_equip' => ($estado->bonusEquipAtaque + $estado->bonusEquipDefesa) > 0,
        ];

        if ($resultado === 'derrota') {
            $resumo['dica'] = $this->dicaDerrota($estado);
        }

        return $resumo;
    }

    /** Dica concreta, calculada do estado: mostra o caminho sem forçar a loja. */
    private function dicaDerrota(EstadoDeBatalha $estado): string
    {
        $hpInimigo = max(0, $estado->inimigoHp);

        if ($hpInimigo > 0 && $hpInimigo <= $estado->inimigoHpMax * 0.25) {
            return "Faltavam só {$hpInimigo} de HP para derrubá-lo! Uma arma mais forte fecharia a conta antes que ele revidasse.";
        }

        if ($estado->inimigoAtaque >= (int) config('jogo.inimigo_ataque_alto')) {
            return "Esse bate {$estado->inimigoAtaque} por erro. Um bom escudo derruba esse número pela metade — cada deslize machuca bem menos.";
        }

        return 'Mantenha o combo (erre menos) e leve uma arma melhor: a luta acaba antes de o inimigo ter chance de revidar.';
    }
}
