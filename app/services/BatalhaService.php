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
        $desafiosFase = $this->desafios->daFase((int) $fase['id']);
        $lista = [];
        foreach ($desafiosFase as $d) {
            $lista[] = Desafio::decodificar($d);
        }

        $combate = $this->atributosCombate($personagem);

        $estado = [
            'fase_id'         => (int) $fase['id'],
            'personagem_id'   => (int) $personagem['id'],
            'desafios'        => $lista,
            'indice'          => 0,
            'total'           => count($lista),
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
            'heroi_nivel'     => (int) $personagem['nivel'],
            'combo'           => 0,
            'especial_armado' => false,
            'acertos'         => 0,
            'erros'           => 0,
            'usou_ia'         => false,
            'finalizada'      => false,
            'resultado'       => null,
        ];

        $_SESSION['batalha'] = $estado;
        return $estado;
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
            'desafio'        => $atual,
        ];
    }

    /**
     * Desafio atual sem a resposta correta (para renderizar a pergunta).
     */
    private function desafioAtualPublico(array $estado): ?array
    {
        if ($estado['indice'] >= $estado['total']) {
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
        if (!$estado || $estado['finalizada'] || $estado['indice'] >= $estado['total']) {
            return ['erro' => 'Nenhuma batalha ativa.'];
        }

        $desafio = $estado['desafios'][$estado['indice']];
        $correto = $viaIa ? true : $this->verificar($desafio, $resposta);

        $this->log->registrar($estado['personagem_id'], (int) $desafio['id'], $correto, $viaIa);

        $retorno = [
            'correto'    => $correto,
            'via_ia'     => $viaIa,
            'explicacao' => $desafio['explicacao'],
            'eventos'    => [],
        ];

        if ($correto) {
            $estado['combo'] = min(COMBO_MAX, $estado['combo'] + 1);
            $estado['acertos']++;
            $dano = $this->calcularDano($estado, (int) $desafio['dificuldade']);
            $estado['inimigo_hp'] = max(0, $estado['inimigo_hp'] - $dano);
            $estado['especial_armado'] = false;
            $retorno['dano_inimigo'] = $dano;
            $retorno['combo'] = $estado['combo'];
        } else {
            $estado['combo'] = 0;
            $estado['erros']++;
            $danoRecebido = max(1, $estado['inimigo_ataque'] - intdiv($estado['heroi_defesa'], 2));
            $estado['heroi_hp'] = max(0, $estado['heroi_hp'] - $danoRecebido);
            $retorno['dano_heroi'] = $danoRecebido;
        }

        $estado['indice']++;
        $_SESSION['batalha'] = $estado;

        // Verifica condições de término.
        if ($estado['inimigo_hp'] <= 0) {
            $retorno['resultado'] = 'vitoria';
            $this->finalizar($estado, 'vitoria');
        } elseif ($estado['heroi_hp'] <= 0) {
            $retorno['resultado'] = 'derrota';
            $this->finalizar($estado, 'derrota');
        } elseif ($estado['indice'] >= $estado['total']) {
            // Acabaram os desafios e o inimigo continua de pé: DERROTA.
            // Só se vence derrubando o inimigo (ramo inimigo_hp <= 0 acima).
            $retorno['resultado'] = 'derrota';
            $this->finalizar($estado, 'derrota');
        } else {
            $retorno['resultado'] = null;
            $retorno['proximo'] = $this->desafioAtualPublico($estado);
        }

        $retorno['estado'] = $this->estadoPublico($estado);
        return $retorno;
    }

    /**
     * Usa o Fragmento da IA Ancestral: acerta o desafio atual automaticamente,
     * mas com custo de reputação e marca permanente na fase.
     */
    public function usarFragmentoIa(array $personagem): array
    {
        $itemModel = new Item();
        $fragmento = $itemModel->findBy('svg_slug', self::ITEM_FRAGMENTO_IA);
        if (!$fragmento || $this->inventario->quantidade((int) $personagem['id'], (int) $fragmento['id']) < 1) {
            return ['erro' => 'Você não possui Fragmentos da IA Ancestral.'];
        }

        $this->inventario->remover((int) $personagem['id'], (int) $fragmento['id']);

        $estado = $this->estado();
        if ($estado) {
            $estado['usou_ia'] = true;
            $_SESSION['batalha'] = $estado;
        }
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

        if (!empty($efeito['cura_hp'])) {
            $estado['heroi_hp'] = min($estado['heroi_hp_max'], $estado['heroi_hp'] + (int) $efeito['cura_hp']);
        }
        if (!empty($efeito['cura_mp'])) {
            $estado['heroi_mp'] = min($estado['heroi_mp_max'], $estado['heroi_mp'] + (int) $efeito['cura_mp']);
        }

        $this->inventario->remover((int) $personagem['id'], $itemId);
        $_SESSION['batalha'] = $estado;
        return [
            'ok'     => true,
            'efeito' => $efeito,
            'estado' => $this->estadoPublico($estado),
        ];
    }

    /**
     * Calcula o dano de um acerto considerando nível, dificuldade, combo e especial.
     */
    private function calcularDano(array $estado, int $dificuldade): int
    {
        $base = $estado['heroi_ataque'] + $estado['heroi_nivel'] * DANO_BASE_POR_NIVEL;
        $base += $dificuldade * 2;
        $multiploCombo = 1 + ($estado['combo'] - 1) * COMBO_BONUS;
        $dano = $base * max(1, $multiploCombo);
        if ($estado['especial_armado']) {
            $dano *= MULTIPLICADOR_ESPECIAL;
        }
        return (int) round($dano);
    }

    /**
     * Soma os atributos do personagem com os bônus dos itens equipados.
     *
     * @return array{ataque:int,defesa:int}
     */
    public function atributosCombate(array $personagem): array
    {
        $classe = CLASSES[$personagem['classe']] ?? CLASSES['ranger'];
        $ataque = (int) $classe['ataque'];
        $defesa = (int) $classe['defesa'];

        $equipados = $this->db()->prepare(
            "SELECT i.efeito FROM inventario inv
             JOIN itens i ON i.id = inv.item_id
             WHERE inv.personagem_id = :p AND inv.equipado = 1"
        );
        $equipados->execute(['p' => (int) $personagem['id']]);
        foreach ($equipados->fetchAll() as $linha) {
            $efeito = $linha['efeito'] ? json_decode($linha['efeito'], true) : [];
            $ataque += (int) ($efeito['ataque'] ?? 0);
            $defesa += (int) ($efeito['defesa'] ?? 0);
        }
        return ['ataque' => $ataque, 'defesa' => $defesa];
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
     */
    private function finalizar(array $estado, string $resultado): void
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
}
