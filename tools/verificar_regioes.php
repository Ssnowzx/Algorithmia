<?php
/**
 * Verificação da lógica de domínio das regiões (RegiaoService) — maestria
 * horizontal. Teste de unidade leve, sem framework, lógica PURA (não toca
 * banco). Rode com:  php tools/verificar_regioes.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Somente CLI.');
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/services/RegiaoService.php';

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

echo "== faixaDe: estados de domínio ==\n";

// A explorar.
$f = RegiaoService::faixaDe(5, 0, 0, 0);
checa('0 concluídas → A explorar', 'a_explorar', $f['chave']);
checa('A explorar → não dominada', false, $f['dominada']);
checa('A explorar → pct 0', 0, $f['pct']);
checa('A explorar → dica entrar', 'Entre na região', $f['dica']);

// Em jornada.
$f = RegiaoService::faixaDe(5, 2, 1, 5);
checa('2/5 concluídas → Em jornada', 'em_jornada', $f['chave']);
checa('Em jornada → faltam 3 fases', 'Faltam 3 fases', $f['dica']);
checa('Em jornada → pct = estrelas/max (5/15=33)', 33, $f['pct']);

// Singular ("Faltam 1 fase").
checa('4/5 → "Faltam 1 fase" (singular)', 'Faltam 1 fase', RegiaoService::faixaDe(5, 4, 0, 8)['dica']);

// Conquistada (todas concluídas, nem todas perfeitas).
$f = RegiaoService::faixaDe(5, 5, 3, 13);
checa('5/5 concluídas, 3 perfeitas → Conquistada', 'conquistada', $f['chave']);
checa('Conquistada → não dominada', false, $f['dominada']);
checa('Conquistada → perfeccione 2', 'Perfeccione 2 p/ dominar', $f['dica']);
checa('Conquistada → pct 87 (13/15)', 87, $f['pct']);

// Dominada (perfeição total).
$f = RegiaoService::faixaDe(5, 5, 5, 15);
checa('5/5 perfeitas → Dominada', 'dominada', $f['chave']);
checa('Dominada → dominada=true', true, $f['dominada']);
checa('Dominada → pct 100', 100, $f['pct']);
checa('Dominada → dica domínio total', 'Domínio total 👑', $f['dica']);

echo "\n== faixaDe: defensivo ==\n";

$f = RegiaoService::faixaDe(5, 9, 9, 99);
checa('clamp concluídas>total → 5', 5, $f['concluidas']);
checa('clamp estrelas>max → 15', 15, $f['estrelas']);
checa('clamp tudo cheio → Dominada', 'dominada', $f['chave']);

$f = RegiaoService::faixaDe(0, 0, 0, 0);
checa('total 0 → sem divisão por zero (pct 0)', 0, $f['pct']);
checa('total 0 → A explorar', 'a_explorar', $f['chave']);

checa('perfeitas clampadas a concluídas (5,2,5,4 → perfeitas=2)', 2, RegiaoService::faixaDe(5, 2, 5, 4)['perfeitas']);

echo "\n== totalDominadas / tituloLenda ==\n";

$mk = static fn (int $t, int $c, int $p, int $e): array => ['faixa' => RegiaoService::faixaDe($t, $c, $p, $e)];
$parcial = [
    $mk(5, 5, 5, 15),  // dominada
    $mk(5, 5, 5, 15),  // dominada
    $mk(5, 2, 0, 4),   // em jornada
    $mk(5, 0, 0, 0),   // a explorar
    $mk(5, 5, 2, 11),  // conquistada
];
checa('totalDominadas → 2 de 5', 2, RegiaoService::totalDominadas($parcial));
checa('tituloLenda parcial → vazio', '', RegiaoService::tituloLenda($parcial));

$tudo = [$mk(5,5,5,15), $mk(4,4,4,12), $mk(5,5,5,15), $mk(5,5,5,15), $mk(6,6,6,18)];
checa('todas dominadas → totalDominadas 5', 5, RegiaoService::totalDominadas($tudo));
checa('todas dominadas → título "Mestre dos Cinco"', 'Mestre dos Cinco', RegiaoService::tituloLenda($tudo));
checa('lista vazia → totalDominadas 0', 0, RegiaoService::totalDominadas([]));
checa('lista vazia → sem título', '', RegiaoService::tituloLenda([]));

echo "\n";
if ($falhas === 0) {
    echo "✅ {$total}/{$total} verificações passaram.\n";
    exit(0);
}
echo "❌ {$falhas} de {$total} falharam.\n";
exit(1);
