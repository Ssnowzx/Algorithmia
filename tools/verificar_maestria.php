<?php
/**
 * Verificação da lógica de maestria por matéria (MaestriaService).
 *
 * É um teste de unidade leve, sem framework (o projeto não usa Composer/PHPUnit):
 * exercita a lógica PURA com casos-limite no padrão Arrange-Act-Assert. Não toca
 * banco. Rode com:  php tools/verificar_maestria.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Somente CLI.');
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/services/MaestriaService.php';

$falhas = 0;
$total  = 0;

/** Assert de igualdade com rótulo do caso. */
function checa(string $caso, $esperado, $obtido): void
{
    global $falhas, $total;
    $total++;
    $ok = $esperado === $obtido;
    if (!$ok) {
        $falhas++;
        $e = is_scalar($esperado) ? var_export($esperado, true) : json_encode($esperado, JSON_UNESCAPED_UNICODE);
        $o = is_scalar($obtido)   ? var_export($obtido, true)   : json_encode($obtido, JSON_UNESCAPED_UNICODE);
        echo "  ✗ {$caso}\n      esperado: {$e}\n      obtido:   {$o}\n";
    } else {
        echo "  ✓ {$caso}\n";
    }
}

echo "== Maestria: faixa por (total, acertos) ==\n";

// ARRANGE/ACT/ASSERT — 0 respostas => Não iniciado.
$f = MaestriaService::faixaDe(0, 0);
checa('0/0 → tier 0 (Não iniciado)', 0, $f['tier']);
checa('0/0 → rótulo Não iniciado', 'Não iniciado', $f['rotulo']);
checa('0/0 → próximo.pct 0', 0, $f['proximo']['pct']);
checa('0/0 → próximo.dica "Responda para começar"', 'Responda para começar', $f['proximo']['dica']);

// Baixo volume + 100% NÃO vira domínio (o ponto central da feature).
$f = MaestriaService::faixaDe(2, 2);
checa('2/2 (100%) → tier 1 (Iniciante), não Mestre', 1, $f['tier']);
checa('2/2 → precisão 100', 100, $f['precisao']);
checa('2/2 → falta 1 p/ Aprendiz', 'Faltam 1 p/ Aprendiz', $f['proximo']['dica']);

// Aprendiz pela contagem de acertos (sem piso de precisão nesse degrau).
$f = MaestriaService::faixaDe(4, 3);
checa('4/3 → tier 2 (Aprendiz)', 2, $f['tier']);
checa('4/3 → próximo Praticante, pct 50 (3/6)', 50, $f['proximo']['pct']);

// Alto volume + baixa precisão TRAVADO por precisão (não sobe só por acumular).
$f = MaestriaService::faixaDe(20, 8);
checa('20/8 (40%) → tier 2 (Aprendiz), travado p/ Praticante', 2, $f['tier']);
checa('20/8 → barra reflete precisão (67%, não 100%)', 67, $f['proximo']['pct']);
checa('20/8 → dica fala de precisão', 'Precisão 40% → suba p/ 60% e vire Praticante', $f['proximo']['dica']);

// Praticante.
$f = MaestriaService::faixaDe(10, 9);
checa('10/9 (90%) → tier 3 (Praticante)', 3, $f['tier']);
checa('10/9 → falta 1 p/ Especialista', 'Faltam 1 p/ Especialista', $f['proximo']['dica']);

// Especialista => "dominada".
$f = MaestriaService::faixaDe(12, 11);
checa('12/11 (92%) → tier 4 (Especialista)', 4, $f['tier']);
checa('12/11 → dominada = true', true, $f['dominada']);
checa('12/11 → falta 4 p/ Mestre', 'Faltam 4 p/ Mestre', $f['proximo']['dica']);

// Mestre (topo) — sem próximo.
$f = MaestriaService::faixaDe(16, 15);
checa('16/15 (94%) → tier 5 (Mestre)', 5, $f['tier']);
checa('16/15 → máximo = true', true, $f['maximo']);
checa('16/15 → próximo = null', null, $f['proximo']);

// Fronteira: 15 acertos mas precisão 75% < 85% => fica Especialista, travado por precisão.
$f = MaestriaService::faixaDe(20, 15);
checa('20/15 (75%) → tier 4 (Especialista), não Mestre', 4, $f['tier']);
checa('20/15 → dica de precisão p/ Mestre', 'Precisão 75% → suba p/ 85% e vire Mestre', $f['proximo']['dica']);

// Fronteira de piso exato (60%) => Praticante.
checa('10/6 (60% exato) → tier 3 (Praticante)', 3, MaestriaService::faixaDe(10, 6)['tier']);
checa('11/6 (54%) → tier 2 (Aprendiz, piso não batido)', 2, MaestriaService::faixaDe(11, 6)['tier']);

// Defensivo: acertos > total é clampado.
$f = MaestriaService::faixaDe(2, 5);
checa('2/5 (clamp) → acertos = 2', 2, $f['acertos']);
checa('2/5 (clamp) → precisão 100', 100, $f['precisao']);

echo "\n== porMateria / totalDominadas ==\n";

// ARRANGE: estatísticas parciais (só 2 das 8 matérias têm resposta).
$estat = [
    'estruturas' => ['total' => 12, 'acertos' => 11], // Especialista (dominada)
    'sql'        => ['total' => 2,  'acertos' => 2],   // Iniciante
];
// ACT
$m = MaestriaService::porMateria($estat);
// ASSERT
checa('porMateria → cobre as 8 matérias', count(ASSUNTOS), count($m));
checa('porMateria → estruturas Especialista', 4, $m['estruturas']['faixa']['tier']);
checa('porMateria → php ausente vira Não iniciado', 0, $m['php']['faixa']['tier']);
checa('porMateria → mantém rótulo amigável', ASSUNTOS['sql'], $m['sql']['rotulo']);
checa('totalDominadas → 1', 1, MaestriaService::totalDominadas($m));
checa('porMateria([]) → ainda 8 (tudo Não iniciado)', count(ASSUNTOS), count(MaestriaService::porMateria([])));

echo "\n";
if ($falhas === 0) {
    echo "✅ {$total}/{$total} verificações passaram.\n";
    exit(0);
}
echo "❌ {$falhas} de {$total} falharam.\n";
exit(1);
