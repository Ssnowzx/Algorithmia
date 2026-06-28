<?php
/**
 * Verificação da lógica das missões da semana (MissaoService) — seleção
 * determinística e avaliação de progresso. Teste de unidade leve, sem framework,
 * lógica PURA (não toca banco). Rode com:  php tools/verificar_missoes.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Somente CLI.');
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/services/MissaoService.php';

$falhas = 0;
$total  = 0;

function checa(string $caso, $esperado, $obtido): void
{
    global $falhas, $total;
    $total++;
    if ($esperado === $obtido) {
        echo "  ✓ {$caso}\n";
        return;
    }
    $falhas++;
    $e = is_scalar($esperado) || $esperado === null ? var_export($esperado, true) : json_encode($esperado, JSON_UNESCAPED_UNICODE);
    $o = is_scalar($obtido)   || $obtido === null   ? var_export($obtido, true)   : json_encode($obtido, JSON_UNESCAPED_UNICODE);
    echo "  ✗ {$caso}\n      esperado: {$e}\n      obtido:   {$o}\n";
}

/** Acha uma missão do pool pelo código (para avaliar casos específicos). */
function missao(string $codigo): array
{
    foreach (MISSOES_SEMANAIS as $m) {
        if ($m['codigo'] === $codigo) {
            return $m;
        }
    }
    throw new RuntimeException("missão {$codigo} não existe");
}

function codigos(array $missoes): array
{
    return array_map(static fn ($m) => $m['codigo'], $missoes);
}

echo "== Seleção determinística ==\n";

$n = count(MISSOES_SEMANAIS);
checa('pool tem 8 missões', 8, $n);
checa('seleciona MISSOES_POR_SEMANA', (int) MISSOES_POR_SEMANA, count(MissaoService::selecionar(0)));
checa('mesma semana → mesmo conjunto', MissaoService::selecionar(123), MissaoService::selecionar(123));
checa('semana 0 → 3 primeiras', ['maratona', 'tiro_certo', 'mente_afiada'], codigos(MissaoService::selecionar(0)));
checa('semana 1 → desliza 1', ['tiro_certo', 'mente_afiada', 'avanco'], codigos(MissaoService::selecionar(1)));
checa('rotação módulo N (idx 8 == idx 0)', codigos(MissaoService::selecionar(0)), codigos(MissaoService::selecionar($n)));
checa('índice negativo não quebra (idx -1 → base 7)', ['disciplina', 'maratona', 'tiro_certo'], codigos(MissaoService::selecionar(-1)));
$sel = codigos(MissaoService::selecionar(5));
checa('3 missões distintas', 3, count(array_unique($sel)));

echo "\n== Avaliação contável ==\n";

$r = MissaoService::avaliar(missao('maratona'), ['respostas' => 8]);
checa('maratona 8/20 → atual 8', 8, $r['atual']);
checa('maratona 8/20 → pct 40', 40, $r['pct']);
checa('maratona 8/20 → incompleta', false, $r['completa']);

$r = MissaoService::avaliar(missao('maratona'), ['respostas' => 20]);
checa('maratona 20/20 → completa', true, $r['completa']);
checa('maratona 20/20 → pct 100', 100, $r['pct']);

$r = MissaoService::avaliar(missao('maratona'), ['respostas' => 25]);
checa('maratona 25/20 → pct travado em 100', 100, $r['pct']);
checa('maratona 25/20 → completa', true, $r['completa']);

checa('tiro_certo 15 acertos → completa', true, MissaoService::avaliar(missao('tiro_certo'), ['acertos' => 15])['completa']);
checa('sem_muletas 10 acertos s/ IA → completa', true, MissaoService::avaliar(missao('sem_muletas'), ['acertos_sem_ia' => 10])['completa']);
checa('disciplina 12 resp s/ IA → completa', true, MissaoService::avaliar(missao('disciplina'), ['respostas_sem_ia' => 12])['completa']);
checa('polimata 4 matérias → completa', true, MissaoService::avaliar(missao('polimata'), ['materias' => 4])['completa']);

$r = MissaoService::avaliar(missao('avanco'), ['fases' => 2]);
checa('avanco 2/3 fases → pct 67', 67, $r['pct']);
checa('avanco 2/3 fases → incompleta', false, $r['completa']);

checa('métrica ausente → atual 0', 0, MissaoService::avaliar(missao('polimata'), [])['atual']);

echo "\n== Avaliação de precisão (mente_afiada: 80% em 10) ==\n";

$r = MissaoService::avaliar(missao('mente_afiada'), ['respostas' => 5, 'acertos' => 5]);
checa('precisão volume<min → mede volume (5/10)', 10, $r['alvo']);
checa('precisão volume<min → unidade respostas', 'respostas', $r['unidade']);
checa('precisão volume<min → incompleta apesar de 100%', false, $r['completa']);
checa('precisão volume<min → nota orienta volume', 'responda 10 p/ valer', $r['nota']);

$r = MissaoService::avaliar(missao('mente_afiada'), ['respostas' => 10, 'acertos' => 9]);
checa('precisão 90% em 10 → completa', true, $r['completa']);
checa('precisão 90% → unidade %', '%', $r['unidade']);
checa('precisão 90% → atual 90', 90, $r['atual']);

$r = MissaoService::avaliar(missao('mente_afiada'), ['respostas' => 10, 'acertos' => 7]);
checa('precisão 70% em 10 → incompleta', false, $r['completa']);
checa('precisão 70% → pct 88 (70/80)', 88, $r['pct']);

checa('precisão 80% exato em 20 → completa', true, MissaoService::avaliar(missao('mente_afiada'), ['respostas' => 20, 'acertos' => 16])['completa']);

echo "\n== totalCompletas ==\n";
$avaliadas = [
    MissaoService::avaliar(missao('maratona'), ['respostas' => 20]),    // completa
    MissaoService::avaliar(missao('avanco'), ['fases' => 1]),           // não
    MissaoService::avaliar(missao('polimata'), ['materias' => 4]),      // completa
];
checa('totalCompletas → 2 de 3', 2, MissaoService::totalCompletas($avaliadas));

echo "\n";
if ($falhas === 0) {
    echo "✅ {$total}/{$total} verificações passaram.\n";
    exit(0);
}
echo "❌ {$falhas} de {$total} falharam.\n";
exit(1);
