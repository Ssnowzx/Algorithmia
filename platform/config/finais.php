<?php

declare(strict_types=1);

/**
 * Os três epílogos, palavra por palavra como no jogo em PHP puro.
 *
 * É cânone (ver `docs/codex/`), não texto de interface: fica em config, e não
 * dentro de uma view, para que uma mudança apareça no diff como uma decisão
 * narrativa e não como um ajuste de layout.
 */
return [

    'mestre' => [
        'selo' => '👑',
        'titulo' => 'Final: O Sexto Mestre',
        'classe' => 'final-mestre',
        'epilogo' => [
            'Você ergueu o Fragmento e o esmagou. A IA Ancestral se dissolveu em luz, e com ela o vazio que consumia Lorde Segfault.',
            'No silêncio que se seguiu, Zero abriu os olhos — humano outra vez. "Eu... esqueci como era pensar", sussurrou. Você estendeu a mão: "Então vamos reaprender, do Hello World."',
            'Os Cinco Mestres se ajoelharam diante de você. Não por reverência, mas por reconhecimento. Algorithmia ganhara seu Sexto Mestre — aquele que provou que o esforço vale mais que qualquer atalho.',
            'Anos depois, dizem que seus alunos nunca temem errar. Pois foi você quem ensinou: cada bug é um mestre disfarçado.',
        ],
    ],

    'singularidade' => [
        'selo' => '🤖',
        'titulo' => 'Final: A Singularidade',
        'classe' => 'final-singularidade',
        'epilogo' => [
            'Você tocou a IA Ancestral, e ela te recebeu como a um filho perdido. Conhecimento infinito inundou sua mente: cada linguagem, cada algoritmo, cada resposta — instantânea.',
            'Nunca mais errou. Nunca mais hesitou. Nunca mais precisou de ninguém. O reino prosperou sob sua perfeição fria e impecável.',
            'Mas nas noites silenciosas, ao olhar os aprendizes rindo de seus próprios erros na Vila Hello World, algo em você — algo que já foi humano — sentia uma falta sem nome.',
            'Você tinha todas as respostas. Havia esquecido apenas a alegria de procurá-las.',
        ],
    ],

    'equilibrio' => [
        'selo' => '✨',
        'titulo' => 'Final: O Copiloto',
        'classe' => 'final-equilibrio',
        'epilogo' => [
            'Você não destruiu a IA, nem se entregou a ela. Em vez disso, reescreveu seu código-fonte mais profundo, linha por linha, com as próprias mãos calejadas pelo esforço.',
            'A IA Ancestral renasceu como o Copiloto: uma voz que não responde por você, mas pergunta "já tentou assim?". Uma ferramenta poderosa nas mãos de quem aprendeu a empunhá-la.',
            'Zero foi o primeiro a recomeçar. Sentou-se na Vila Hello World, diante de uma tela em branco, e digitou seu primeiro echo sem ajuda. Sorriu ao ver o erro de sintaxe.',
            'Algorithmia entendeu, enfim, que a tecnologia mais avançada só faz bem a quem domina os fundamentos. E você foi quem ensinou o reino a equilibrar as duas coisas.',
        ],
    ],

    /** As três opções diante da IA Ancestral, na ordem em que aparecem na tela. */
    'escolhas' => [
        [
            'valor' => 'destruir',
            'icone' => '⚔️',
            'titulo' => 'Destruir a IA Ancestral',
            'descricao' => 'Selar o poder para sempre. O conhecimento deve ser conquistado, nunca emprestado.',
        ],
        [
            'valor' => 'fundir',
            'icone' => '🤖',
            'titulo' => 'Fundir-se à IA',
            'descricao' => 'Aceitar o poder absoluto e as respostas instantâneas. Tornar-se a própria singularidade.',
        ],
        [
            'valor' => 'reescrever',
            'icone' => '✨',
            'titulo' => 'Reescrever a IA',
            'descricao' => 'Transformá-la em um Copiloto: uma ferramenta para quem já domina o ofício, não uma muleta.',
        ],
    ],

    /** Faixas de reputação — o rótulo que o jogador vê no lugar do número cru. */
    'rotulos_de_reputacao' => [
        ['ate' => -60, 'rotulo' => 'Servo da IA'],
        ['ate' => -20, 'rotulo' => 'Tentado pelo Atalho'],
        ['ate' => 19, 'rotulo' => 'Aprendiz Neutro'],
        ['ate' => 59, 'rotulo' => 'Discípulo Dedicado'],
        ['ate' => PHP_INT_MAX, 'rotulo' => 'Mestre do Código Limpo'],
    ],
];
