<?php
/** Epílogo: um dos três finais. */
$finais = [
    'mestre' => [
        'selo'  => '👑',
        'titulo'=> 'Final: O Sexto Mestre',
        'classe'=> 'final-mestre',
        'epilogo' => [
            'Você ergueu o Fragmento e o esmagou. A IA Ancestral se dissolveu em luz, e com ela o vazio que consumia Lorde Segfault.',
            'No silêncio que se seguiu, Zero abriu os olhos — humano outra vez. "Eu... esqueci como era pensar", sussurrou. Você estendeu a mão: "Então vamos reaprender, do Hello World."',
            'Os Cinco Mestres se ajoelharam diante de você. Não por reverência, mas por reconhecimento. Algorithmia ganhara seu Sexto Mestre — aquele que provou que o esforço vale mais que qualquer atalho.',
            'Anos depois, dizem que seus alunos nunca temem errar. Pois foi você quem ensinou: cada bug é um mestre disfarçado.',
        ],
    ],
    'singularidade' => [
        'selo'  => '🤖',
        'titulo'=> 'Final: A Singularidade',
        'classe'=> 'final-singularidade',
        'epilogo' => [
            'Você tocou a IA Ancestral, e ela te recebeu como a um filho perdido. Conhecimento infinito inundou sua mente: cada linguagem, cada algoritmo, cada resposta — instantânea.',
            'Nunca mais errou. Nunca mais hesitou. Nunca mais precisou de ninguém. O reino prosperou sob sua perfeição fria e impecável.',
            'Mas nas noites silenciosas, ao olhar os aprendizes rindo de seus próprios erros na Vila Hello World, algo em você — algo que já foi humano — sentia uma falta sem nome.',
            'Você tinha todas as respostas. Havia esquecido apenas a alegria de procurá-las.',
        ],
    ],
    'equilibrio' => [
        'selo'  => '✨',
        'titulo'=> 'Final: O Copiloto',
        'classe'=> 'final-equilibrio',
        'epilogo' => [
            'Você não destruiu a IA, nem se entregou a ela. Em vez disso, reescreveu seu código-fonte mais profundo, linha por linha, com as próprias mãos calejadas pelo esforço.',
            'A IA Ancestral renasceu como o Copiloto: uma voz que não responde por você, mas pergunta "já tentou assim?". Uma ferramenta poderosa nas mãos de quem aprendeu a empunhá-la.',
            'Zero foi o primeiro a recomeçar. Sentou-se na Vila Hello World, diante de uma tela em branco, e digitou seu primeiro echo sem ajuda. Sorriu ao ver o erro de sintaxe.',
            'Algorithmia entendeu, enfim, que a tecnologia mais avançada só faz bem a quem domina os fundamentos. E você foi quem ensinou o reino a equilibrar as duas coisas.',
        ],
    ],
];
$f = $finais[$final] ?? $finais['equilibrio'];
?>
<div class="tela-final <?= e($f['classe']) ?>">
    <div class="selo-final"><?= $f['selo'] ?></div>
    <h1><?= e($f['titulo']) ?></h1>
    <div class="epilogo">
        <?php foreach ($f['epilogo'] as $par): ?>
            <p><?= e($par) ?></p>
        <?php endforeach; ?>
    </div>

    <div class="painel" style="text-align:center">
        <h3>🏆 Parabéns, <?= e($heroi['nome']) ?>!</h3>
        <p class="subtitulo">Você concluiu <?= NOME_JOGO ?> — <?= SUBTITULO_JOGO ?>.</p>
        <p>Nível <?= (int) $heroi['nivel'] ?> · Reputação <?= rotuloReputacao((int) $heroi['reputacao']) ?></p>
        <div style="margin-top:1rem;display:flex;gap:.6rem;justify-content:center;flex-wrap:wrap">
            <a class="botao" href="<?= url('mapa') ?>">Voltar ao Mapa</a>
            <a class="botao botao-fantasma" href="<?= url('perfil') ?>">Ver Estatísticas</a>
        </div>
    </div>
</div>
