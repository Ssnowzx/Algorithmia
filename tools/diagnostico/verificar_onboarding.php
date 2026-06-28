<?php
/**
 * Verificação da lógica de onboarding (OnboardingService::montar) — checklist de
 * primeiros passos com head start. Teste de unidade leve, lógica PURA (não toca
 * banco). Rode com:  php tools/verificar_onboarding.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Somente CLI.');
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/services/OnboardingService.php';

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

echo "== Head start (endowed progress) ==\n";

$m = OnboardingService::montar([], 1);
checa('novato sem nada → 1/4 (só o herói)', 1, $m['completos']);
checa('1º passo (herói) já feito', true, $m['passos'][0]['feito']);
checa('2º passo (batalha) não feito', false, $m['passos'][1]['feito']);
checa('total = 4', 4, $m['total']);
checa('novato com passo incompleto → mostra', true, $m['mostrar']);
checa('o head start é "Forjar seu herói"', 'Forjar seu herói', $m['passos'][0]['label']);

echo "\n== Progressão ==\n";

checa('+batalha → 2/4', 2, OnboardingService::montar(['batalha' => true], 1)['completos']);
checa('+batalha+item → 3/4', 3, OnboardingService::montar(['batalha' => true, 'item' => true], 1)['completos']);
$m = OnboardingService::montar(['batalha' => true, 'item' => true, 'conquista' => true], 1);
checa('tudo feito → 4/4', 4, $m['completos']);
checa('checklist completo → some (mostrar=false)', false, $m['mostrar']);

echo "\n== Visibilidade (gate de nível) ==\n";

checa('nível 3 (limiar) + incompleto → mostra', true, OnboardingService::montar([], 3)['mostrar']);
checa('nível 4 (veterano) → não mostra', false, OnboardingService::montar([], 4)['mostrar']);
checa('veterano: herói ainda marcado como feito', true, OnboardingService::montar([], 9)['passos'][0]['feito']);
checa('flags ausentes não quebram (montar([],2))', 1, OnboardingService::montar([], 2)['completos']);

echo "\n";
if ($falhas === 0) {
    echo "✅ {$total}/{$total} verificações passaram.\n";
    exit(0);
}
echo "❌ {$falhas} de {$total} falharam.\n";
exit(1);
