<?php

declare(strict_types=1);

namespace Algorithmia\Testes\Suporte;

use PDO;

/**
 * Constrói um mundo mínimo e determinístico no banco de teste.
 *
 * Os testes de caracterização não usam as seeds do jogo: elas mudam a cada
 * migration e trariam 955 desafios reais para dentro das asserções. Aqui cada
 * teste monta só as linhas de que precisa, com valores escolhidos para que os
 * números esperados possam ser calculados à mão a partir de config/config.php.
 */
final class Mundo
{
    private PDO $db;

    /** Tabelas na ordem em que podem ser esvaziadas (FK desligada de todo jeito). */
    private const TABELAS = [
        'respostas_log', 'conquistas_personagem', 'escolhas', 'dialogos',
        'progresso_fases', 'inventario', 'desafios', 'fases',
        'personagens', 'usuarios', 'conquistas', 'itens', 'mestres',
    ];

    public function __construct()
    {
        $this->db = getConnection();
    }

    /** Zera o banco de teste e a sessão. Chamado antes de cada teste. */
    public function limpar(): void
    {
        $this->db->exec('SET FOREIGN_KEY_CHECKS = 0');
        foreach (self::TABELAS as $tabela) {
            $this->db->exec("TRUNCATE TABLE {$tabela}");
        }
        $this->db->exec('SET FOREIGN_KEY_CHECKS = 1');
        $_SESSION = [];
    }

    /**
     * Cria um herói. Os padrões de HP/MP vêm da classe, como faz o jogo ao
     * criar personagem, mas cada campo pode ser sobrescrito.
     *
     * @param array<string,mixed> $sobrescritas
     */
    public function heroi(string $classe = 'mago', array $sobrescritas = []): array
    {
        $stat = CLASSES[$classe];
        $usuarioId = $this->inserir('usuarios', [
            'nome' => 'Testador',
            'email' => 'testador+' . uniqid('', true) . '@algorithmia.test',
            'senha_hash' => password_hash('irrelevante', PASSWORD_DEFAULT),
            'papel' => 'jogador',
        ]);

        $dados = array_merge([
            'usuario_id' => $usuarioId,
            'nome' => 'Herói de Teste',
            'classe' => $classe,
            'nivel' => 1,
            'xp' => 0,
            'hp_max' => $stat['hp'],
            'hp_atual' => $stat['hp'],
            'mp_max' => $stat['mp'],
            'mp_atual' => $stat['mp'],
            'ouro' => 50,
            'reputacao' => 0,
            'capitulo' => 0,
        ], $sobrescritas);

        $id = $this->inserir('personagens', $dados);
        return (new \Personagem())->findById($id);
    }

    /** @param array<string,mixed> $sobrescritas */
    public function mestre(array $sobrescritas = []): array
    {
        $id = $this->inserir('mestres', array_merge([
            'nome' => 'Mestre de Teste',
            'titulo' => 'o Aferidor',
            'disciplina' => 'PHP',
            'regiao' => 'Porto da Sintaxe',
            'svg_slug' => 'mestre-willen',
            'cor_tema' => '#7c5cff',
            'ordem' => 1,
        ], $sobrescritas));
        return (new \Mestre())->findById($id);
    }

    /** @param array<string,mixed> $sobrescritas */
    public function fase(array $sobrescritas = []): array
    {
        $id = $this->inserir('fases', array_merge([
            'mestre_id' => null,
            'ordem_global' => 1,
            'nome' => 'Fase de Teste',
            'tipo' => 'licao',
            'inimigo_nome' => 'Bug Selvagem',
            'inimigo_svg' => 'inimigo-bug',
            'inimigo_hp' => 60,
            'inimigo_ataque' => 10,
            'xp_recompensa' => 50,
            'ouro_recompensa' => 20,
            'item_drop_id' => null,
            'requisito_fase_id' => null,
        ], $sobrescritas));
        return (new \Fase())->findById($id);
    }

    /**
     * Cria um desafio de múltipla escolha cuja resposta correta é sempre o
     * índice 0 — os testes respondem 0 para acertar e 1 para errar.
     *
     * @return array<string,mixed> linha já decodificada (como o motor consome)
     */
    public function desafio(int $faseId, int $dificuldade = 1, int $ordem = 0): array
    {
        $id = $this->inserir('desafios', [
            'fase_id' => $faseId,
            'ordem' => $ordem,
            'tipo' => 'multipla',
            'assunto' => 'php',
            'pergunta' => "Desafio de dificuldade {$dificuldade}",
            'codigo' => null,
            'opcoes' => json_encode(['certa', 'errada'], JSON_THROW_ON_ERROR),
            'resposta' => json_encode(0, JSON_THROW_ON_ERROR),
            'explicacao' => 'Porque sim.',
            'dificuldade' => $dificuldade,
        ]);
        return \Desafio::decodificar((new \Desafio())->findById($id));
    }

    /**
     * Desafio de tipo arbitrário: o teste fornece tipo, opcoes e resposta.
     * Usado para cobrir a verificação de vf/completar/ordenar/arrastar.
     *
     * @param array<string,mixed> $sobrescritas campos crus da tabela (opcoes/resposta já em JSON)
     * @return array<string,mixed> linha já decodificada
     */
    public function desafioCru(int $faseId, array $sobrescritas): array
    {
        $id = $this->inserir('desafios', array_merge([
            'fase_id' => $faseId,
            'ordem' => 0,
            'tipo' => 'multipla',
            'assunto' => 'php',
            'pergunta' => 'Pergunta.',
            'codigo' => null,
            'opcoes' => null,
            'resposta' => json_encode(0, JSON_THROW_ON_ERROR),
            'explicacao' => 'Porque sim.',
            'dificuldade' => 1,
        ], $sobrescritas));
        return \Desafio::decodificar((new \Desafio())->findById($id));
    }

    /** Marca um desafio como já respondido — alimenta o anti-repetição do sorteio. */
    public function marcarVisto(int $personagemId, int $desafioId): void
    {
        $this->inserir('respostas_log', [
            'personagem_id' => $personagemId,
            'desafio_id' => $desafioId,
            'correta' => 1,
            'usou_ia' => 0,
        ]);
    }

    /**
     * @param array<string,mixed> $efeito  ex.: ['ataque' => 5] ou ['cura_hp' => 30]
     * @param array<string,mixed> $sobrescritas
     */
    public function item(string $tipo, array $efeito, array $sobrescritas = []): array
    {
        $id = $this->inserir('itens', array_merge([
            'nome' => 'Item de Teste',
            'descricao' => null,
            'tipo' => $tipo,
            'efeito' => json_encode($efeito, JSON_THROW_ON_ERROR),
            'preco' => 0,
            'svg_slug' => 'item-teste-' . uniqid('', true),
            'raridade' => 'comum',
            'compravel' => 1,
        ], $sobrescritas));
        return (new \Item())->findById($id);
    }

    /** Coloca um item no inventário; equipado=1 faz seus bônus valerem no combate. */
    public function darItem(int $personagemId, int $itemId, int $quantidade = 1, bool $equipado = false): void
    {
        $this->inserir('inventario', [
            'personagem_id' => $personagemId,
            'item_id' => $itemId,
            'quantidade' => $quantidade,
            'equipado' => $equipado ? 1 : 0,
        ]);
    }

    /** Registra no catálogo as conquistas cujos códigos o teste espera ver concedidas. */
    public function conquistas(string ...$codigos): void
    {
        foreach ($codigos as $codigo) {
            $this->inserir('conquistas', [
                'codigo' => $codigo,
                'nome' => ucfirst(str_replace('_', ' ', $codigo)),
                'descricao' => 'Conquista de teste.',
                'svg_slug' => 'conquista-generica',
                'secreta' => 0,
            ]);
        }
    }

    /** Recarrega a linha do personagem direto do banco (pós-efeitos). */
    public function recarregarHeroi(int $id): array
    {
        return (new \Personagem())->findById($id);
    }

    /** @return list<array<string,mixed>> */
    public function logDeRespostas(int $personagemId): array
    {
        $stmt = $this->db->prepare(
            'SELECT desafio_id, correta, usou_ia FROM respostas_log WHERE personagem_id = :p ORDER BY id ASC'
        );
        $stmt->execute(['p' => $personagemId]);
        return $stmt->fetchAll();
    }

    /** @param array<string,mixed> $dados */
    private function inserir(string $tabela, array $dados): int
    {
        $colunas = array_keys($dados);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $tabela,
            implode(', ', $colunas),
            implode(', ', array_map(static fn (string $c): string => ':' . $c, $colunas))
        );
        $this->db->prepare($sql)->execute($dados);
        return (int) $this->db->lastInsertId();
    }
}
