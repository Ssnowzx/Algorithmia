<?php

declare(strict_types=1);

namespace Tests\Suporte;

use App\Dominio\Combate\CorretorDeRespostas;
use App\Dominio\Combate\MotorDeBatalha;
use App\Dominio\Progressao\ServicoDeConquistas;
use App\Dominio\Progressao\ServicoDeReputacao;
use App\Models\Conquista;
use App\Models\Desafio;
use App\Models\Fase;
use App\Models\Item;
use App\Models\ItemDoInventario;
use App\Models\Mestre;
use App\Models\Personagem;
use App\Models\RespostaLog;
use App\Models\Usuario;

/**
 * Constrói um mundo mínimo e determinístico para os testes de domínio.
 *
 * Não usa as seeds do jogo: elas mudam a cada importação e trariam centenas de
 * desafios reais para dentro das asserções. Cada teste monta só as linhas de que
 * precisa, com valores escolhidos para que os números esperados possam ser
 * calculados à mão a partir de `config/jogo.php`.
 */
final class Mundo
{
    /** @param  array<string,mixed>  $sobrescritas */
    public function heroi(string $classe = 'mago', array $sobrescritas = []): Personagem
    {
        /** @var array{hp:int,mp:int} $stat */
        $stat = config("jogo.classes.{$classe}");

        $usuario = Usuario::create([
            'nome' => 'Testador',
            'email' => uniqid('testador', true).'@algorithmia.test',
            'senha_hash' => 'irrelevante',
        ]);

        return Personagem::create(array_merge([
            'usuario_id' => $usuario->id,
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
        ], $sobrescritas));
    }

    /** @param  array<string,mixed>  $sobrescritas */
    public function mestre(array $sobrescritas = []): Mestre
    {
        return Mestre::create(array_merge([
            'nome' => 'Mestre de Teste',
            'titulo' => 'o Aferidor',
            'disciplina' => 'PHP',
            'regiao' => 'Porto da Sintaxe',
            'svg_slug' => 'mestre-willen',
            'ordem' => 1,
        ], $sobrescritas));
    }

    /** @param  array<string,mixed>  $sobrescritas */
    public function fase(array $sobrescritas = []): Fase
    {
        return Fase::create(array_merge([
            'ordem_global' => 1,
            'nome' => 'Fase de Teste',
            'tipo' => 'licao',
            'inimigo_nome' => 'Bug Selvagem',
            'inimigo_svg' => 'inimigo-bug',
            'inimigo_hp' => 60,
            'inimigo_ataque' => 10,
            'xp_recompensa' => 50,
            'ouro_recompensa' => 20,
        ], $sobrescritas));
    }

    /**
     * Desafio de múltipla escolha cuja resposta correta é sempre o índice 0.
     * Os testes respondem 0 para acertar e 1 para errar.
     *
     * @param  array<string,mixed>  $sobrescritas
     */
    public function desafio(int $faseId, int $dificuldade = 1, int $ordem = 0, array $sobrescritas = []): Desafio
    {
        return Desafio::create(array_merge([
            'fase_id' => $faseId,
            'ordem' => $ordem,
            'tipo' => 'multipla',
            'assunto' => 'php',
            'pergunta' => "Desafio de dificuldade {$dificuldade}",
            'opcoes' => ['certa', 'errada'],
            'resposta' => 0,
            'explicacao' => 'Porque sim.',
            'dificuldade' => $dificuldade,
        ], $sobrescritas));
    }

    /**
     * Converte o model na linha que o motor carrega no estado (com gabarito).
     *
     * @return array<string,mixed>
     */
    public function linha(Desafio $desafio): array
    {
        return [
            'id' => $desafio->id,
            'tipo' => $desafio->tipo,
            'assunto' => $desafio->assunto,
            'pergunta' => $desafio->pergunta,
            'codigo' => $desafio->codigo,
            'opcoes' => $desafio->opcoes,
            'resposta' => $desafio->resposta,
            'explicacao' => $desafio->explicacao,
            'dificuldade' => $desafio->dificuldade,
        ];
    }

    /**
     * @param  array<string,int>  $efeito  ex.: ['ataque' => 5] ou ['cura_hp' => 30]
     * @param  array<string,mixed>  $sobrescritas
     */
    public function item(string $tipo, array $efeito = [], array $sobrescritas = []): Item
    {
        return Item::create(array_merge([
            'nome' => 'Item de Teste',
            'tipo' => $tipo,
            'efeito' => $efeito,
            'preco' => 0,
            'svg_slug' => uniqid('item-teste-', true),
            'raridade' => 'comum',
            'compravel' => true,
        ], $sobrescritas));
    }

    public function darItem(Personagem $heroi, Item $item, int $quantidade = 1, bool $equipado = false): void
    {
        ItemDoInventario::create([
            'personagem_id' => $heroi->id,
            'item_id' => $item->id,
            'quantidade' => $quantidade,
            'equipado' => $equipado,
        ]);
    }

    /** Registra no catálogo as conquistas que o teste espera ver concedidas. */
    public function conquistas(string ...$codigos): void
    {
        foreach ($codigos as $codigo) {
            Conquista::create([
                'codigo' => $codigo,
                'nome' => ucfirst(str_replace('_', ' ', $codigo)),
                'descricao' => 'Conquista de teste.',
            ]);
        }
    }

    /** Marca um desafio como já respondido — alimenta o anti-repetição do sorteio. */
    public function marcarVisto(Personagem $heroi, Desafio $desafio): void
    {
        RespostaLog::registrar($heroi->id, $desafio->id, true, false);
    }

    /**
     * Motor com N desafios idênticos de dificuldade 1, em ordem fixa, e a
     * batalha já iniciada.
     *
     * @param  BatalhaEmMemoria  $repositorio  passe o mesmo para inspecionar o estado depois
     */
    public function motor(Personagem $heroi, Fase $fase, int $quantos, ?int $limite, BatalhaEmMemoria $repositorio): MotorDeBatalha
    {
        $sequencia = [];
        for ($i = 0; $i < $quantos; $i++) {
            $sequencia[] = $this->linha($this->desafio($fase->id, dificuldade: 1, ordem: $i));
        }

        $motor = new MotorDeBatalha(
            $repositorio,
            new SorteioFixo($sequencia, $limite),
            new CorretorDeRespostas,
            new ServicoDeReputacao,
            new ServicoDeConquistas,
        );
        $motor->iniciar($heroi, $fase);

        return $motor;
    }
}
