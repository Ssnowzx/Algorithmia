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
        'especie'   => 'Humano',
        'descricao' => 'Conjura queries e lógica profunda das sombras do servidor. Muita Mana, pouca vida — como quem pula o almoço pra terminar a feature.',
        'hp'        => 80,
        'mp'        => 60,
        'ataque'    => 12,
        'defesa'    => 4,
        'svg'       => 'heroi-mago',
        'cor'       => '#7c5cff',
        'lore'      => 'Humano da Vila Hello World, povo padrão do reino — e alvo favorito do Fragmento, pois foi para a mão humana que a IA Ancestral foi feita. Detalhe nada sutil: o Zero também era humano. Jogar de humano é trilhar o mesmo ponto de partida do vilão. O Mago virou as costas para a luz da interface e desceu às sombras do servidor, onde se conjura query e lógica profunda.',
    ],
    'guerreiro' => [
        'nome'      => 'Guerreiro do Frontend',
        'especie'   => 'Humano',
        'descricao' => 'Encara bug de frente e centraliza div com a força bruta. Muita Vida e defesa, porque alinhar pixel é guerra.',
        'hp'        => 130,
        'mp'        => 25,
        'ataque'    => 10,
        'defesa'    => 9,
        'svg'       => 'heroi-guerreiro',
        'cor'       => '#ff7a59',
        'lore'      => 'Humano da Vila Hello World, povo padrão do reino — e alvo favorito do Fragmento, pois a IA Ancestral foi feita para a mão humana. Lembra: o Zero também era humano. O Guerreiro fica na linha de frente, encarando o bug e o usuário cara a cara. Alinhar pixel é guerra; por isso, muita Vida e escudo.',
    ],
    'ranger' => [
        'nome'      => 'Ranger Fullstack',
        'especie'   => 'Humano',
        'descricao' => 'Faz um pouco de tudo e o currículo concorda. Equilibrado em ataque e defesa, com faro extra para ouro — alguém tem que pagar as contas.',
        'hp'        => 100,
        'mp'        => 40,
        'ataque'    => 11,
        'defesa'    => 6,
        'svg'       => 'heroi-ranger',
        'cor'       => '#2ecc71',
        'lore'      => 'Humano da Vila Hello World, povo padrão do reino — e alvo favorito do Fragmento, que foi forjado para a mão humana (assim como o Zero, que era humano). O Ranger se recusou a escolher um lado e pagou o preço: sabe um pouco de tudo, dorme um pouco de nada. Equilibrado em ataque e defesa, com faro extra para ouro — alguém tem que pagar as contas.',
    ],
    'xeno' => [
        'nome'      => 'Xeno do DevOps',
        'especie'   => 'Xenoíde Insectoide',
        'descricao' => 'Veio de longe para automatizar deploy e apagar incêndio em produção. Mana sólida e olhos que enxergam pipeline quebrado antes do alerta.',
        'hp'        => 90,
        'mp'        => 50,
        'ataque'    => 10,
        'defesa'    => 5,
        'svg'       => 'heroi-xeno',
        'cor'       => '#00e5a0',
        'lore'      => 'Vêm dos Datacenters Errantes, enxames de máquinas que vagam além das fronteiras do reino. São um povo de colmeia: não pensam sozinhos, pensam em paralelo — cada Xeno é um nó; juntos, um cluster. Migram quando um datacenter esfria, e foi subindo pela Torre das Conexões que chegaram à Vila. Quase imunes à sedução da IA (o sussurro "só nós dois" soa ridículo para quem nunca está só) — mas têm um medo secreto: se a colmeia cai, o Xeno fica órfão, tão vazio quanto o Zero ficou.',
    ],
    'elfo' => [
        'nome'      => 'Elfo da UX',
        'especie'   => 'Elfo',
        'descricao' => 'Orelhas afiadas para feedback de usuário e interfaces que parecem mágicas. Pouca vida, muita mana — prototipa antes de dormir.',
        'hp'        => 75,
        'mp'        => 55,
        'ataque'    => 9,
        'defesa'    => 4,
        'svg'       => 'heroi-elfo',
        'cor'       => '#a8d4ff',
        'lore'      => 'Povo antigo e longevo das Margens da Apresentação — a fina superfície onde o reino encosta nos Usuários, observadores invisíveis que ninguém vê mas todos servem. Têm orelhas afiadas para feedback e mãos que fazem interfaces que parecem mágica. Desceram à Vila depois do Grande Timeout, quando tudo virou terminal cinza, para reencantar as telas. Desprezam a IA por estética: o Fragmento entrega telas frias e genéricas, sem alma — e o belo imperfeito feito à mão vale mais que o perfeito instantâneo.',
    ],
    'draconato' => [
        'nome'      => 'Draconato do Kernel',
        'especie'   => 'Draconato',
        'descricao' => 'Escamas endurecidas por anos debugando perto do metal. Tanque de linha de comando: muita vida, golpe pesado, paciência zero com segfault.',
        'hp'        => 115,
        'mp'        => 35,
        'ataque'    => 12,
        'defesa'    => 7,
        'svg'       => 'heroi-draconato',
        'cor'       => '#ff6b4a',
        'lore'      => 'Espécie ancestral das Profundezas do Kernel, lá embaixo onde o código toca o metal. São mais velhos que a Ordem do Código Limpo — talvez que a própria IA Ancestral — e lembram de quando tudo era escrito à mão, byte a byte, sem ninguém para dar a resposta. Subiram à superfície porque o Abismo do /dev/null está vazando para as camadas baixas. São os que mais desprezam o Fragmento; mas o orgulho é a brecha, e a IA adora os arrogantes — como o Zero, brilhante e arrogante, também foi.',
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

// Duelo Final (morte súbita): a batalha NUNCA mais termina por acabarem os
// desafios — só quando um HP zera. Quando o limite de perguntas é atingido com
// os dois vivos, entra-se em morte súbita: a cada rodada, acertos e erros
// passam a doer mais (fúria crescente), garantindo um desfecho rápido e tenso
// SEM tirar a decisão das mãos de quem sabe a matéria.
const MORTE_SUBITA_RAGE_STEP = 0.5; // +50% de dano por rodada de morte súbita
const MORTE_SUBITA_RAGE_MAX  = 4.0; // teto do multiplicador de fúria

// Limiares para a "leitura do inimigo" (sugestão de tática na arena e no mapa).
const INIMIGO_HP_ALTO      = 150; // acima disto: inimigo resistente (ataque ajuda)
const INIMIGO_ATAQUE_ALTO  = 15;  // acima disto: golpes pesados (defesa ajuda)

// Objetivos da loja: recompensa em ouro ÚNICA por conquista (creditada na 1ª vez).
// Valores modestos (somados < custo de uma arma rara) — empurrão inicial, sem
// inflar a economia nem virar pay-to-win.
const OBJETIVOS_OURO = [
    'primeira_arma'    => 30,
    'primeira_pocao'   => 20,
    'arsenal_completo' => 60,
];

// Reputação: eixo Disciplina (+) vs. IA (-). Começa em 0.
const REPUTACAO_USO_IA = -10;
const REPUTACAO_MIN    = -100;
const REPUTACAO_MAX    = 100;

// Fonte única das regiões governadas por um mestre, chaveada pelo svg_slug do
// mestre (estável, vindo do banco). Liga cada região ao seu cenário de fundo e
// ao código da conquista de "discípulo". Evita mapas duplicados espalhados.
const REGIOES_MESTRE = [
    'mestre-willen'    => ['fundo' => 'fundo-porto',    'conquista' => 'mestre_willen'],
    'mestre-clayton'   => ['fundo' => 'fundo-cidadela', 'conquista' => 'mestre_clayton'],
    'mestre-marcelo'   => ['fundo' => 'fundo-floresta', 'conquista' => 'mestre_marcelo'],
    'mestre-cesar'     => ['fundo' => 'fundo-montanha', 'conquista' => 'mestre_cesar'],
    'mestre-cassandro' => ['fundo' => 'fundo-torre',    'conquista' => 'mestre_cassandro'],
];

// Assuntos das matérias (rótulos amigáveis).
const ASSUNTOS = [
    'php'        => 'PHP',
    'mvc'        => 'Arquitetura MVC',
    'sql'        => 'Banco de Dados / SQL',
    'poo'        => 'Orientação a Objetos',
    'estruturas' => 'Estruturas de Dados',
    'redes'      => 'Redes de Computadores',
    'logica'     => 'Lógica e Algoritmos',
    'calculo'    => 'Cálculo',
];

// Maestria por matéria (derivada de respostas_log; read-only, sem migration nem
// dado novo). Cada faixa exige volume de ACERTOS e um piso de PRECISÃO sustentada
// — não a % bruta — para que "domínio" signifique competência consistente
// (mastery learning) e não sorte com poucas respostas. Os alvos de acerto são
// modestos de propósito: funcionam tanto para matérias de pool grande
// (estruturas) quanto pequeno (sql), pois os acertos acumulam por RESPOSTA.
// Fonte única: MaestriaService e a view do perfil leem daqui.
const MAESTRIA_FAIXAS = [
    // [rótulo, ícone, classe de cor (CSS), mín. de acertos, piso de precisão 0..1]
    ['rotulo' => 'Não iniciado', 'icone' => '○',  'cor' => 'inerte',      'min_acertos' => 0,  'piso' => 0.00],
    ['rotulo' => 'Iniciante',    'icone' => '🌱', 'cor' => 'iniciante',   'min_acertos' => 0,  'piso' => 0.00],
    ['rotulo' => 'Aprendiz',     'icone' => '📘', 'cor' => 'aprendiz',    'min_acertos' => 3,  'piso' => 0.00],
    ['rotulo' => 'Praticante',   'icone' => '🔷', 'cor' => 'praticante',  'min_acertos' => 6,  'piso' => 0.60],
    ['rotulo' => 'Especialista', 'icone' => '🎖️', 'cor' => 'especialista', 'min_acertos' => 10, 'piso' => 0.75],
    ['rotulo' => 'Mestre',       'icone' => '👑', 'cor' => 'mestre',      'min_acertos' => 15, 'piso' => 0.85],
];
// A partir deste tier a matéria conta como "dominada" (resumo X/8 no perfil).
const MAESTRIA_TIER_DOMINADA = 4;

// Missões da semana: objetivos de curto prazo no perfil. Read-only, derivadas da
// atividade da SEMANA ISO corrente (sem cron, sem recompensa, sem persistência —
// ver MissaoService). O conjunto da semana é sorteado de forma determinística pela
// data; texto de sabor no tom ácido do jogo. Fonte única p/ service + view.
// metrica ∈ respostas | acertos | acertos_sem_ia | respostas_sem_ia | materias |
//          fases | precisao  (precisao usa 'alvo' como % e 'min' como volume mínimo).
const MISSOES_SEMANAIS = [
    ['codigo' => 'maratona',     'titulo' => 'Maratona de Código',  'icone' => '🏃', 'metrica' => 'respostas',        'alvo' => 20,              'desc' => 'Responda 20 desafios esta semana. O sofá que espere.'],
    ['codigo' => 'tiro_certo',   'titulo' => 'Tiro Certeiro',       'icone' => '🎯', 'metrica' => 'acertos',          'alvo' => 15,              'desc' => 'Acerte 15 desafios. Chutar não conta como talento.'],
    ['codigo' => 'mente_afiada', 'titulo' => 'Mente Afiada',        'icone' => '🧠', 'metrica' => 'precisao',         'alvo' => 80, 'min' => 10, 'desc' => 'Mantenha 80% de acerto em pelo menos 10 respostas. Sem desculpas.'],
    ['codigo' => 'avanco',       'titulo' => 'Avanço no Reino',     'icone' => '🗺️', 'metrica' => 'fases',            'alvo' => 3,               'desc' => 'Conclua 3 fases. O reino não se salva sozinho.'],
    ['codigo' => 'sem_muletas',  'titulo' => 'Sem Muletas',         'icone' => '💪', 'metrica' => 'acertos_sem_ia',   'alvo' => 10,              'desc' => 'Acerte 10 desafios sem implorar para a IA. Orgulho tem preço.'],
    ['codigo' => 'polimata',     'titulo' => 'Polímata',            'icone' => '📚', 'metrica' => 'materias',         'alvo' => 4,               'desc' => 'Pratique 4 matérias diferentes. Variedade é tempero.'],
    ['codigo' => 'aquecimento',  'titulo' => 'Aquecimento',         'icone' => '🔥', 'metrica' => 'respostas',        'alvo' => 8,               'desc' => 'Responda 8 desafios. O mínimo do mínimo, vai.'],
    ['codigo' => 'disciplina',   'titulo' => 'Disciplina de Ferro', 'icone' => '⚔️', 'metrica' => 'respostas_sem_ia', 'alvo' => 12,              'desc' => 'Responda 12 desafios sem tocar na IA. Prove que tem coluna.'],
];
const MISSOES_POR_SEMANA = 3;

// Domínio das regiões (maestria HORIZONTAL): perfeição da jornada por mestre.
// Read-only, derivado de fases + progresso_fases (estrelas; 3 = sem erro e sem
// IA). Estados em ordem crescente de domínio. A cor base de cada região é a
// cor_tema do mestre (inline); o estado modula no CSS (Dominada = ouro; A
// explorar = apagado). Fonte única p/ RegiaoService + view do perfil.
const REGIAO_FAIXAS = [
    'a_explorar'  => ['rotulo' => 'A explorar',  'cor' => 'inerte'],
    'em_jornada'  => ['rotulo' => 'Em jornada',  'cor' => 'jornada'],
    'conquistada' => ['rotulo' => 'Conquistada', 'cor' => 'conquistada'],
    'dominada'    => ['rotulo' => 'Dominada',    'cor' => 'dominada'],
];
// Título exibido quando TODAS as regiões estão dominadas (ressoa com o subtítulo
// "A Lenda dos Cinco Mestres").
const REGIAO_TITULO_LENDA = 'Mestre dos Cinco';

// Onboarding "Primeiros passos": o painel de boas-vindas (endowed progress) no
// perfil aparece só até este nível (novato) e some quando o jogador evolui ou
// conclui os passos. Read-only.
const ONBOARDING_NIVEL_MAX = 3;

// Anti-repetição: cada fase tem um POOL de desafios maior do que o sorteado por
// batalha. A cada início de combate, sorteia-se N do pool priorizando os ainda
// não vistos pelo personagem (via respostas_log), e os N são ordenados por
// dificuldade crescente para preservar a progressão didática. N por TIPO de fase
// espelha o tamanho original de cada batalha (mantém o balanceamento de HP).
const DESAFIOS_POR_BATALHA = [
    'licao'       => 4,
    'secundaria'  => 3,
    'chefe'       => 5,
    'chefe_final' => 6,
    'historia'    => 0, // fases de história não têm combate
];
const DESAFIOS_POR_BATALHA_PADRAO = 4;
