<?php
/**
 * Constantes globais do jogo e regras de balanceamento.
 * Centraliza valores ajustáveis (XP, dano, classes) para facilitar tuning.
 */

// Base URL para montar links internos. Funciona com php -S e Apache.
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
define('BASE_URL', $scriptDir === '' ? '/' : $scriptDir . '/');

define('NOME_JOGO', 'Algorithmia');
define('SUBTITULO_JOGO', 'A Lenda dos Cinco Mestres');

// Classes jogáveis: bônus aplicados sobre os atributos-base do personagem.
const CLASSES = [
    'mago' => [
        'nome'      => 'Mago do Backend',
        'descricao' => 'Conjura queries e lógica profunda das sombras do servidor. Muita Mana, pouca vida — como quem pula o almoço pra terminar a feature.',
        'hp'        => 80,
        'mp'        => 60,
        'ataque'    => 12,
        'defesa'    => 4,
        'svg'       => 'heroi-mago',
        'cor'       => '#7c5cff',
    ],
    'guerreiro' => [
        'nome'      => 'Guerreiro do Frontend',
        'descricao' => 'Encara bug de frente e centraliza div com a força bruta. Muita Vida e defesa, porque alinhar pixel é guerra.',
        'hp'        => 130,
        'mp'        => 25,
        'ataque'    => 10,
        'defesa'    => 9,
        'svg'       => 'heroi-guerreiro',
        'cor'       => '#ff7a59',
    ],
    'ranger' => [
        'nome'      => 'Ranger Fullstack',
        'descricao' => 'Faz um pouco de tudo e o currículo concorda. Equilibrado em ataque e defesa, com faro extra para ouro — alguém tem que pagar as contas.',
        'hp'        => 100,
        'mp'        => 40,
        'ataque'    => 11,
        'defesa'    => 6,
        'svg'       => 'heroi-ranger',
        'cor'       => '#2ecc71',
    ],
];

// Curva de XP: total acumulado para alcançar o nível N.
function xpParaNivel(int $nivel): int
{
    if ($nivel <= 1) {
        return 0;
    }
    return (int) round(100 * pow($nivel - 1, 1.5));
}

// Balanceamento de combate.
const DANO_BASE_POR_NIVEL   = 3;    // dano extra por nível do herói
const COMBO_BONUS           = 0.25; // +25% de dano por acerto consecutivo
const COMBO_MAX             = 4;    // teto do multiplicador de combo
const CUSTO_MP_ESPECIAL     = 15;   // mana gasta no ataque especial
const MULTIPLICADOR_ESPECIAL = 2.0; // o especial dobra o próximo dano

// Reputação: eixo Disciplina (+) vs. IA (-). Começa em 0.
const REPUTACAO_USO_IA = -10;
const REPUTACAO_MIN    = -100;
const REPUTACAO_MAX    = 100;

// Assuntos das matérias (rótulos amigáveis).
const ASSUNTOS = [
    'php'        => 'PHP',
    'mvc'        => 'Arquitetura MVC',
    'sql'        => 'Banco de Dados / SQL',
    'poo'        => 'Orientação a Objetos',
    'estruturas' => 'Estruturas de Dados',
    'redes'      => 'Redes de Computadores',
    'logica'     => 'Lógica e Cálculo',
];
