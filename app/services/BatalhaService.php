<?php
/**
 * Máquina de estados da batalha por turnos, persistida em $_SESSION['batalha'].
 *
 * Princípio anti-cola: as respostas corretas NUNCA são enviadas ao cliente.
 * O estado guarda os desafios completos no servidor; o que vai para a tela
 * passa por estadoPublico(), que remove o gabarito.
 */
class BatalhaService
{
    private Personagem $personagens;
    private Desafio $desafios;
    private Inventario $inventario;
    private RespostaLog $log;
    private ReputacaoService $reputacao;

    /** Id do item "Fragmento da IA Ancestral" (resolvido pelo nome no seed). */
    private const ITEM_FRAGMENTO_IA = 'item-fragmento-ia';

    public function __construct()
    {
        $this->personagens = new Personagem();
        $this->desafios = new Desafio();
        $this->inventario = new Inventario();
        $this->log = new RespostaLog();
        $this->reputacao = new ReputacaoService();
    }

    /**
     * Monta o estado inicial da batalha para uma fase e o grava em sessão.
     */
    public function iniciar(array $personagem, array $fase): array
    {
        $sorteio = $this->sortearDesafios($personagem, $fase);
        $combate = $this->atributosDetalhados($personagem);

        $estado = [
            'fase_id'         => (int) $fase['id'],
            'personagem_id'   => (int) $personagem['id'],
            // Sequência inteira jogável: os N principais (curva didática) +
            // o resto do pool embaralhado, que abastece o Duelo Final.
            'desafios'        => $sorteio['lista'],
            'indice'          => 0,
            // 'total' é só o LIMITE DE RITMO (gatilho da morte súbita), não mais
            // uma condição de derrota — a batalha só acaba quando um HP zera.
            'total'           => $sorteio['limite'],
            'inimigo_nome'    => $fase['inimigo_nome'] ?: 'Bug Selvagem',
            'inimigo_svg'     => $fase['inimigo_svg'] ?: 'inimigo-bug',
            'inimigo_hp_max'  => (int) $fase['inimigo_hp'],
            'inimigo_hp'      => (int) $fase['inimigo_hp'],
            'inimigo_ataque'  => (int) $fase['inimigo_ataque'],
            'heroi_hp'        => (int) $personagem['hp_atual'],
            'heroi_hp_max'    => (int) $personagem['hp_max'],
            'heroi_mp'        => (int) $personagem['mp_atual'],
            'heroi_mp_max'    => (int) $personagem['mp_max'],
            'heroi_ataque'    => $combate['ataque'],
            'heroi_defesa'    => $combate['defesa'],
            // Separação classe vs. itens — usada para mostrar ao jogador o quanto
            // o equipamento contribuiu (dano da arma / dano bloqueado pelo escudo).
            'heroi_ataque_base'  => $combate['ataque_classe'],
            'heroi_defesa_base'  => $combate['defesa_classe'],
            'bonus_equip_ataque' => $combate['bonus_ataque'],
            'bonus_equip_defesa' => $combate['bonus_defesa'],
            'heroi_nivel'     => (int) $personagem['nivel'],
            'combo'           => 0,
            'especial_armado' => false,
            'acertos'         => 0,
            'erros'           => 0,
            'usou_ia'         => false,
            'finalizada'      => false,
            'resultado'       => null,
            // Duelo Final.
            'morte_subita'    => false,
            'rodada_subita'   => 0,
            // Acumuladores para o resumo de fim de batalha.
            'dano_total'        => 0,
            'dano_equip_total'  => 0,
            'bloqueado_total'   => 0,
            'hp_curado_total'   => 0,
            'mp_curado_total'   => 0,
        ];

        $_SESSION['batalha'] = $estado;
        return $estado;
    }

    /**
     * Sorteia o conjunto de desafios desta batalha a partir do POOL da fase.
     *
     * Anti-repetição: sempre que sobra pool, prioriza perguntas que o personagem
     * ainda NÃO viu (via respostas_log); só recorre às já vistas para completar a
     * quantidade. Os escolhidos saem embaralhados e depois reordenados por
     * dificuldade crescente — replays mostram combinações novas, mas a curva de
     * dificuldade dentro da batalha continua suave.
     *
     * @return array{lista:array<int,array>,limite:int} 'lista' = N principais
     *   (ordenados por dificuldade) seguidos do resto do pool embaralhado (reserva
     *   para o Duelo Final); 'limite' = N (o limite de ritmo que abre a morte súbita).
     */
    protected function sortearDesafios(array $personagem, array $fase): array
    {
        $pool = array_map(
            [Desafio::class, 'decodificar'],
            $this->desafios->poolDaFase((int) $fase['id'])
        );

        $quantos = DESAFIOS_POR_BATALHA[$fase['tipo']] ?? DESAFIOS_POR_BATALHA_PADRAO;
        if ($quantos <= 0 || count($pool) <= $quantos) {
            usort($pool, fn($a, $b) => (int) $a['dificuldade'] <=> (int) $b['dificuldade']);
            return ['lista' => $pool, 'limite' => count($pool)];
        }

        $vistos = array_flip($this->desafios->idsVistos((int) $personagem['id'], (int) $fase['id']));
        $ineditos = [];
        $revisao = [];
        foreach ($pool as $d) {
            if (isset($vistos[(int) $d['id']])) {
                $revisao[] = $d;
            } else {
                $ineditos[] = $d;
            }
        }

        shuffle($ineditos);
        shuffle($revisao);
        $ordenados = array_merge($ineditos, $revisao);
        $principal = array_slice($ordenados, 0, $quantos);
        $reserva = array_slice($ordenados, $quantos);

        // usort é estável no PHP 8: empates de dificuldade preservam a ordem
        // já embaralhada, então a sequência muda a cada batalha.
        usort($principal, fn($a, $b) => (int) $a['dificuldade'] <=> (int) $b['dificuldade']);
        shuffle($reserva); // a reserva só aparece no Duelo Final; ordem livre
        return ['lista' => array_merge($principal, $reserva), 'limite' => count($principal)];
    }

    /**
     * Garante que existe um desafio na posição atual do índice. Quando o Duelo
     * Final esgota a reserva, recicla a sequência (reembaralhada) para nunca
     * faltar pergunta — a fúria crescente encerra o combate em poucas rodadas,
     * então a repetição é rara.
     */
    private function garantirDesafioAtual(array &$estado): void
    {
        if (isset($estado['desafios'][$estado['indice']]) || empty($estado['desafios'])) {
            return;
        }
        $reciclar = $estado['desafios'];
        shuffle($reciclar);
        foreach ($reciclar as $d) {
            $estado['desafios'][] = $d;
            if (isset($estado['desafios'][$estado['indice']])) {
                break;
            }
        }
    }

    public function estado(): ?array
    {
        return $_SESSION['batalha'] ?? null;
    }

    public function limpar(): void
    {
        unset($_SESSION['batalha']);
    }

    /**
     * Marca a batalha como já recompensada, para a vitória não conceder XP/ouro
     * mais de uma vez. O estado em sessão é responsabilidade deste serviço.
     */
    public function marcarRecompensado(): void
    {
        if (isset($_SESSION['batalha'])) {
            $_SESSION['batalha']['recompensado'] = true;
        }
    }

    /**
     * Versão do estado segura para enviar ao cliente: sem o gabarito dos desafios.
     */
    public function estadoPublico(?array $estado = null): array
    {
        $estado = $estado ?? $this->estado();
        if (!$estado) {
            return [];
        }
        $atual = $this->desafioAtualPublico($estado);
        return [
            'inimigo_nome'   => $estado['inimigo_nome'],
            'inimigo_svg'    => $estado['inimigo_svg'],
            'inimigo_hp'     => $estado['inimigo_hp'],
            'inimigo_hp_max' => $estado['inimigo_hp_max'],
            'heroi_hp'       => $estado['heroi_hp'],
            'heroi_hp_max'   => $estado['heroi_hp_max'],
            'heroi_mp'       => $estado['heroi_mp'],
            'heroi_mp_max'   => $estado['heroi_mp_max'],
            'combo'          => $estado['combo'],
            'especial_armado'=> $estado['especial_armado'],
            'indice'         => $estado['indice'],
            'total'          => $estado['total'],
            'finalizada'     => $estado['finalizada'],
            'morte_subita'   => !empty($estado['morte_subita']),
            'rodada_subita'  => (int) ($estado['rodada_subita'] ?? 0),
            'desafio'        => $atual,
        ];
    }

    /**
     * Desafio atual sem a resposta correta (para renderizar a pergunta).
     */
    private function desafioAtualPublico(array $estado): ?array
    {
        if (!empty($estado['finalizada']) || !isset($estado['desafios'][$estado['indice']])) {
            return null;
        }
        $d = $estado['desafios'][$estado['indice']];
        return [
            'id'          => (int) $d['id'],
            'tipo'        => $d['tipo'],
            'assunto'     => $d['assunto'],
            'pergunta'    => $d['pergunta'],
            'codigo'      => $d['codigo'],
            'opcoes'      => $d['opcoes'],
            'dificuldade' => (int) $d['dificuldade'],
        ];
    }

    /**
     * Processa a resposta do jogador ao desafio atual.
     *
     * @param mixed $resposta Valor enviado pelo cliente (formato varia por tipo).
     * @param bool  $viaIa    Se a resposta veio do Fragmento da IA (acerto automático).
     * @return array Resultado do turno para o cliente.
     */
    public function responder($resposta, bool $viaIa = false): array
    {
        $estado = $this->estado();
        if (!$estado || $estado['finalizada']) {
            return ['erro' => 'Nenhuma batalha ativa.'];
        }
        $this->garantirDesafioAtual($estado);
        if (!isset($estado['desafios'][$estado['indice']])) {
            return ['erro' => 'Nenhuma batalha ativa.'];
        }

        $desafio = $estado['desafios'][$estado['indice']];
        $correto = $viaIa ? true : $this->verificar($desafio, $resposta);

        $this->log->registrar($estado['personagem_id'], (int) $desafio['id'], $correto, $viaIa);

        // Fúria do Duelo Final: 1.0 no combate normal; cresce a cada rodada de
        // morte súbita. Aplica-se tanto ao dano causado quanto ao recebido.
        $furia = $this->multiplicadorFuria($estado);

        $retorno = [
            'correto'    => $correto,
            'via_ia'     => $viaIa,
            'explicacao' => $desafio['explicacao'],
            'eventos'    => [],
        ];

        if ($correto) {
            $estado['combo'] = min(COMBO_MAX, $estado['combo'] + 1);
            $estado['acertos']++;
            $danoInfo = $this->calcularDano($estado, (int) $desafio['dificuldade'], $furia);
            $estado['inimigo_hp'] = max(0, $estado['inimigo_hp'] - $danoInfo['total']);
            $estado['especial_armado'] = false;
            $estado['dano_total'] += $danoInfo['total'];
            $estado['dano_equip_total'] += $danoInfo['equip'];
            $retorno['dano_inimigo'] = $danoInfo['total'];
            $retorno['dano_equip'] = $danoInfo['equip']; // parte vinda da arma/acessório
            $retorno['combo'] = $estado['combo'];
        } else {
            $estado['combo'] = 0;
            $estado['erros']++;
            // Dano com e sem o equipamento: a diferença é o que o escudo bloqueou.
            $danoComEquip = max(1, $estado['inimigo_ataque'] - intdiv($estado['heroi_defesa'], 2));
            $danoSemEquip = max(1, $estado['inimigo_ataque'] - intdiv((int) $estado['heroi_defesa_base'], 2));
            $danoRecebido = max(1, (int) round($danoComEquip * $furia));
            $bloqueado = max(0, (int) round(($danoSemEquip - $danoComEquip) * $furia));
            $estado['heroi_hp'] = max(0, $estado['heroi_hp'] - $danoRecebido);
            $estado['bloqueado_total'] += $bloqueado;
            $retorno['dano_heroi'] = $danoRecebido;
            $retorno['bloqueado'] = $bloqueado; // dano que o escudo evitou
        }

        $estado['indice']++;

        // A batalha SÓ termina quando um HP zera — nunca por acabarem as perguntas.
        if ($estado['inimigo_hp'] <= 0) {
            $retorno['resultado'] = 'vitoria';
            $this->finalizar($estado, 'vitoria');
        } elseif ($estado['heroi_hp'] <= 0) {
            $retorno['resultado'] = 'derrota';
            $this->finalizar($estado, 'derrota');
        } else {
            // Ninguém caiu. Ao atingir o limite de ritmo, abre/segue o Duelo
            // Final: a fúria sobe a cada rodada até alguém tombar.
            if ($estado['indice'] >= $estado['total']) {
                $estado['morte_subita'] = true;
                $estado['rodada_subita'] = (int) $estado['rodada_subita'] + 1;
            }
            $this->garantirDesafioAtual($estado);
            $_SESSION['batalha'] = $estado;
            $retorno['resultado'] = null;
            $retorno['proximo'] = $this->desafioAtualPublico($estado);
        }

        $retorno['estado'] = $this->estadoPublico($estado);
        if (!empty($retorno['resultado'])) {
            $retorno['resumo'] = $this->resumoBatalha($estado, $retorno['resultado']);
        }
        return $retorno;
    }

    /** Multiplicador de fúria do Duelo Final (1.0 fora da morte súbita). */
    private function multiplicadorFuria(array $estado): float
    {
        if (empty($estado['morte_subita'])) {
            return 1.0;
        }
        return min(MORTE_SUBITA_RAGE_MAX, 1 + MORTE_SUBITA_RAGE_STEP * (int) $estado['rodada_subita']);
    }

    /**
     * Usa o Fragmento da IA Ancestral: acerta o desafio atual automaticamente,
     * mas com custo de reputação e marca permanente na fase.
     */
    public function usarFragmentoIa(array $personagem): array
    {
        // Valida a batalha ANTES de consumir o item/reputação: sem esta guarda,
        // usar o Fragmento sem batalha ativa gastava o item e derrubava a
        // reputação sem efeito algum. (Vale também no Duelo Final, em que o
        // índice já passou do limite mas a batalha segue ativa.)
        $estado = $this->estado();
        if (!$estado || $estado['finalizada']) {
            return ['erro' => 'Nenhuma batalha ativa.'];
        }

        $itemModel = new Item();
        $fragmento = $itemModel->findBy('svg_slug', self::ITEM_FRAGMENTO_IA);
        if (!$fragmento || $this->inventario->quantidade((int) $personagem['id'], (int) $fragmento['id']) < 1) {
            return ['erro' => 'Você não possui Fragmentos da IA Ancestral.'];
        }

        $this->inventario->remover((int) $personagem['id'], (int) $fragmento['id']);

        $estado['usou_ia'] = true;
        $_SESSION['batalha'] = $estado;
        $novaRep = $this->reputacao->ajustar((int) $personagem['id'], REPUTACAO_USO_IA);

        $retorno = $this->responder(null, true);
        $retorno['reputacao'] = $novaRep;
        return $retorno;
    }

    /**
     * Arma o ataque especial (consome MP; dobra o dano do próximo acerto).
     */
    public function armarEspecial(array $personagem): array
    {
        $estado = $this->estado();
        if (!$estado) {
            return ['erro' => 'Nenhuma batalha ativa.'];
        }
        if ($estado['especial_armado']) {
            return ['erro' => 'O especial já está carregado.'];
        }
        if ($estado['heroi_mp'] < CUSTO_MP_ESPECIAL) {
            return ['erro' => 'Mana insuficiente.'];
        }
        $estado['heroi_mp'] -= CUSTO_MP_ESPECIAL;
        $estado['especial_armado'] = true;
        $_SESSION['batalha'] = $estado;
        $this->personagens->update((int) $personagem['id'], ['mp_atual' => $estado['heroi_mp']]);
        return ['ok' => true, 'estado' => $this->estadoPublico($estado)];
    }

    /**
     * Usa uma poção do inventário durante a batalha (cura HP ou MP).
     */
    public function usarPocao(array $personagem, int $itemId): array
    {
        $estado = $this->estado();
        if (!$estado) {
            return ['erro' => 'Nenhuma batalha ativa.'];
        }
        $linha = $this->inventario->pegar((int) $personagem['id'], $itemId);
        if (!$linha || (int) $linha['quantidade'] < 1) {
            return ['erro' => 'Você não possui esse item.'];
        }
        $item = (new Item())->findById($itemId);
        if (!$item || $item['tipo'] !== 'pocao') {
            return ['erro' => 'Item inválido para uso em batalha.'];
        }
        $efeito = Item::efeito($item);

        // Acumula o quanto a poção realmente recuperou (respeitando o teto),
        // para o resumo de fim de batalha valorizar os consumíveis.
        if (!empty($efeito['cura_hp'])) {
            $antes = $estado['heroi_hp'];
            $estado['heroi_hp'] = min($estado['heroi_hp_max'], $estado['heroi_hp'] + (int) $efeito['cura_hp']);
            $estado['hp_curado_total'] = (int) ($estado['hp_curado_total'] ?? 0) + ($estado['heroi_hp'] - $antes);
        }
        if (!empty($efeito['cura_mp'])) {
            $antes = $estado['heroi_mp'];
            $estado['heroi_mp'] = min($estado['heroi_mp_max'], $estado['heroi_mp'] + (int) $efeito['cura_mp']);
            $estado['mp_curado_total'] = (int) ($estado['mp_curado_total'] ?? 0) + ($estado['heroi_mp'] - $antes);
        }

        $this->inventario->remover((int) $personagem['id'], $itemId);
        $_SESSION['batalha'] = $estado;

        $retorno = [
            'ok'     => true,
            'efeito' => $efeito,
            'estado' => $this->estadoPublico($estado),
        ];
        // Objetivo "usou a 1ª poção" (vale tanto em batalha quanto fora dela).
        $obj = (new ConquistaService())->concederObjetivo((int) $personagem['id'], 'primeira_pocao');
        if ($obj) {
            $retorno['objetivo'] = ['nome' => $obj['conquista']['nome'], 'ouro' => (int) $obj['ouro']];
        }
        return $retorno;
    }

    /**
     * Calcula o dano de um acerto considerando nível, dificuldade, combo, especial
     * e a fúria do Duelo Final. Devolve o total e a fatia vinda do equipamento de
     * ataque (linear no ataque), para mostrar ao jogador "+X da arma".
     *
     * @return array{total:int,equip:int}
     */
    private function calcularDano(array $estado, int $dificuldade, float $furia = 1.0): array
    {
        $multCombo = max(1, 1 + ($estado['combo'] - 1) * COMBO_BONUS);
        $multEspecial = $estado['especial_armado'] ? MULTIPLICADOR_ESPECIAL : 1.0;
        $mult = $multCombo * $multEspecial * $furia;

        $base = $estado['heroi_ataque'] + $estado['heroi_nivel'] * DANO_BASE_POR_NIVEL + $dificuldade * 2;
        $total = (int) round($base * $mult);

        // O bônus de ataque dos itens já está embutido em heroi_ataque; sua
        // contribuição ao dano é esse bônus vezes os mesmos multiplicadores.
        $equip = (int) round((int) ($estado['bonus_equip_ataque'] ?? 0) * $mult);
        $equip = max(0, min($equip, $total));
        return ['total' => $total, 'equip' => $equip];
    }

    /**
     * Soma os atributos do personagem com os bônus dos itens equipados.
     *
     * @return array{ataque:int,defesa:int}
     */
    public function atributosCombate(array $personagem): array
    {
        $d = $this->atributosDetalhados($personagem);
        return ['ataque' => $d['ataque'], 'defesa' => $d['defesa']];
    }

    /**
     * Como atributosCombate(), mas separando a base da classe dos bônus dos itens
     * equipados — base para o feedback de valor (dano da arma, bloqueio do escudo).
     *
     * @return array{ataque:int,defesa:int,ataque_classe:int,defesa_classe:int,bonus_ataque:int,bonus_defesa:int}
     */
    public function atributosDetalhados(array $personagem): array
    {
        $classe = CLASSES[$personagem['classe']] ?? CLASSES['ranger'];
        $ataqueClasse = (int) $classe['ataque'];
        $defesaClasse = (int) $classe['defesa'];
        $bonusAtaque = 0;
        $bonusDefesa = 0;

        $equipados = $this->db()->prepare(
            "SELECT i.efeito FROM inventario inv
             JOIN itens i ON i.id = inv.item_id
             WHERE inv.personagem_id = :p AND inv.equipado = 1"
        );
        $equipados->execute(['p' => (int) $personagem['id']]);
        foreach ($equipados->fetchAll() as $linha) {
            $efeito = $linha['efeito'] ? json_decode($linha['efeito'], true) : [];
            $bonusAtaque += (int) ($efeito['ataque'] ?? 0);
            $bonusDefesa += (int) ($efeito['defesa'] ?? 0);
        }
        return [
            'ataque'        => $ataqueClasse + $bonusAtaque,
            'defesa'        => $defesaClasse + $bonusDefesa,
            'ataque_classe' => $ataqueClasse,
            'defesa_classe' => $defesaClasse,
            'bonus_ataque'  => $bonusAtaque,
            'bonus_defesa'  => $bonusDefesa,
        ];
    }

    private function db(): PDO
    {
        return getConnection();
    }

    /**
     * Verifica se a resposta do jogador está correta para o tipo de desafio.
     *
     * @param mixed $resposta
     */
    private function verificar(array $desafio, $resposta): bool
    {
        $gabarito = $desafio['resposta'];

        switch ($desafio['tipo']) {
            case 'multipla':
            case 'erro':
                // Gabarito é o índice da opção correta.
                return (int) $resposta === (int) $gabarito;

            case 'vf':
                // Gabarito é booleano.
                return $this->paraBool($resposta) === $this->paraBool($gabarito);

            case 'completar':
                // Gabarito é texto; aceita lista de alternativas válidas.
                $aceitas = is_array($gabarito) ? $gabarito : [$gabarito];
                $normalizada = $this->normalizar((string) $resposta);
                foreach ($aceitas as $valida) {
                    if ($this->normalizar((string) $valida) === $normalizada) {
                        return true;
                    }
                }
                return false;

            case 'ordenar':
            case 'arrastar':
                // Gabarito é a sequência correta de índices/tokens.
                if (!is_array($resposta) || !is_array($gabarito)) {
                    return false;
                }
                return array_map('strval', $resposta) === array_map('strval', $gabarito);
        }
        return false;
    }

    private function paraBool($v): bool
    {
        if (is_bool($v)) return $v;
        if (is_string($v)) return in_array(strtolower($v), ['1', 'true', 'v', 'verdadeiro'], true);
        return (bool) $v;
    }

    private function normalizar(string $s): string
    {
        // Compara código ignorando espaços extras e ponto-e-vírgula final.
        $s = trim($s);
        $s = preg_replace('/\s+/', ' ', $s);
        return rtrim($s, '; ');
    }

    /**
     * Marca a batalha como finalizada (a recompensa é processada pelo controller).
     * Recebe o estado por referência para que o chamador veja finalizada=true.
     */
    private function finalizar(array &$estado, string $resultado): void
    {
        $estado['finalizada'] = true;
        $estado['resultado'] = $resultado;
        // Persiste HP/MP correntes no personagem.
        $this->personagens->update($estado['personagem_id'], [
            'hp_atual' => max(1, $estado['heroi_hp']),
            'mp_atual' => $estado['heroi_mp'],
        ]);
        $_SESSION['batalha'] = $estado;
    }

    /**
     * Monta o resumo de fim de batalha (vitória ou derrota): quanto o equipamento
     * e as poções pesaram, mais uma dica estratégica quando o herói cai. É o que
     * faz o jogador SENTIR o valor do que comprou na loja.
     */
    private function resumoBatalha(array $estado, string $resultado): array
    {
        $temEquip = ((int) ($estado['bonus_equip_ataque'] ?? 0)
                   + (int) ($estado['bonus_equip_defesa'] ?? 0)) > 0;

        $resumo = [
            'dano_total'   => (int) ($estado['dano_total'] ?? 0),
            'dano_arma'    => (int) ($estado['dano_equip_total'] ?? 0),
            'bloqueado'    => (int) ($estado['bloqueado_total'] ?? 0),
            'hp_curado'    => (int) ($estado['hp_curado_total'] ?? 0),
            'mp_curado'    => (int) ($estado['mp_curado_total'] ?? 0),
            'acertos'      => (int) ($estado['acertos'] ?? 0),
            'erros'        => (int) ($estado['erros'] ?? 0),
            'morte_subita' => !empty($estado['morte_subita']),
            'tem_equip'    => $temEquip,
        ];
        if ($resultado === 'derrota') {
            $resumo['dica'] = $this->dicaDerrota($estado);
        }
        return $resumo;
    }

    /**
     * Dica estratégica após a derrota — concreta e calculada do estado, sem abrir
     * a loja nem forçar compra. Apenas mostra o caminho.
     */
    private function dicaDerrota(array $estado): string
    {
        $hpInimigo = max(0, (int) $estado['inimigo_hp']);
        $hpMaxInimigo = max(1, (int) $estado['inimigo_hp_max']);

        // Chegou pertinho: faltava pouco HP do inimigo → mais ataque resolveria.
        if ($hpInimigo > 0 && $hpInimigo <= $hpMaxInimigo * 0.25) {
            return "Faltavam só {$hpInimigo} de HP para derrubá-lo! Uma arma mais forte fecharia a conta antes que ele revidasse.";
        }
        // Apanhou de um inimigo pesado → defesa faz cada erro doer menos.
        if ((int) $estado['inimigo_ataque'] >= INIMIGO_ATAQUE_ALTO) {
            $atk = (int) $estado['inimigo_ataque'];
            return "Esse bate {$atk} por erro. Um bom escudo derruba esse número pela metade — cada deslize machuca bem menos.";
        }
        // Caso geral: a economia da luta passa por errar menos e bater mais.
        return "Mantenha o combo (erre menos) e leve uma arma melhor: a luta acaba antes de o inimigo ter chance de revidar.";
    }
}
