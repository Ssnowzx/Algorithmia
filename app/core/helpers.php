<?php
/**
 * Funções auxiliares globais usadas pelas views e controllers.
 */

/**
 * Escapa texto para saída segura em HTML (prevenção de XSS).
 */
function e(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

/**
 * Monta uma URL interna a partir de uma rota controller/metodo/param.
 */
function url(string $rota = ''): string
{
    return BASE_URL . 'index.php?url=' . ltrim($rota, '/');
}

/**
 * Caminho para um asset estático em /public.
 */
function asset(string $caminho): string
{
    return BASE_URL . 'public/' . ltrim($caminho, '/');
}

/**
 * Insere a arte (pixel art PNG) de um asset do jogo a partir de
 * public/img/{slug}.png, como uma tag <img> (mantém o cache do navegador).
 *
 * @param string $slug   Caminho-base do asset (ex.: 'mestres/mestre-willen').
 * @param string $classe Classe(s) CSS opcional(is) aplicada(s) ao <img>.
 * @param string $attrs  Atributos HTML extras opcionais.
 * @return string Tag <img>, ou um marcador "▢" se o asset não existir.
 */
function svg(string $slug, string $classe = '', string $attrs = ''): string
{
    $png = __DIR__ . '/../../public/img/' . $slug . '.png';
    if (!is_file($png)) {
        // Marcador visível para slugs ainda não desenhados.
        return '<span class="svg-faltando" title="' . e($slug) . '">▢</span>';
    }
    $classeAttr = $classe !== '' ? ' class="' . e($classe) . '"' : '';
    $extra = $attrs !== '' ? ' ' . $attrs : '';
    return '<img src="' . asset('img/' . $slug . '.png') . '"'
        . $classeAttr . $extra . ' alt="' . e($slug) . '" loading="lazy">';
}

/**
 * Resolve um slug "nu" vindo do banco (ex.: mestre-willen, inimigo-bug,
 * item-espada) para o caminho do asset em public/img, escolhendo a subpasta
 * pelo prefixo. Caminhos que já contêm "/" são respeitados.
 */
function caminhoSvg(string $slug): string
{
    if ($slug === '') {
        return 'ui/placeholder';
    }
    if (strpos($slug, '/') !== false) {
        return $slug;
    }
    $mapa = [
        'mestre-'    => 'mestres/',
        'inimigo-'   => 'inimigos/',
        'heroi-'     => 'herois/',
        'item-'      => 'itens/',
        'npc-'       => 'inimigos/',
        'icone-'     => 'ui/',
        'troxeu-'    => 'ui/',
        'conquista-' => 'ui/',
    ];
    foreach ($mapa as $prefixo => $pasta) {
        if (str_starts_with($slug, $prefixo)) {
            return $pasta . $slug;
        }
    }
    return 'ui/' . $slug;
}

/**
 * Atalho: inclui um SVG a partir de um slug nu do banco.
 */
function svgSlug(string $slug, string $classe = ''): string
{
    return svg(caminhoSvg($slug), $classe);
}

/**
 * Resolve o cenário de fundo (pixel art) de uma região a partir do slug do
 * mestre. Regiões sem mestre (início/fim) caem na vila de Hello World.
 */
function fundoRegiao(?string $mestreSlug): string
{
    $mapa = [
        'mestre-willen'    => 'fundo-porto',     // Porto da Sintaxe
        'mestre-clayton'   => 'fundo-cidadela',  // Cidadela dos Objetos
        'mestre-marcelo'   => 'fundo-floresta',  // Floresta das Estruturas
        'mestre-cesar'     => 'fundo-montanha',  // Montanha do Cálculo
        'mestre-cassandro' => 'fundo-torre',     // Torre das Conexões
    ];
    return $mapa[$mestreSlug ?? ''] ?? 'fundo-vila';
}

/**
 * Campo escondido com o token CSRF para formulários POST.
 */
function csrf_field(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}

/**
 * Valida o token CSRF recebido em um POST.
 */
function csrf_valido(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

/**
 * Renderiza uma barra de progresso (HP, MP, XP) em HTML.
 */
function barra(int $atual, int $maximo, string $tipo): string
{
    $maximo = max(1, $maximo);
    $pct = max(0, min(100, (int) round($atual / $maximo * 100)));
    return '<div class="barra barra-' . e($tipo) . '">'
        . '<div class="barra-fill" style="width:' . $pct . '%"></div>'
        . '<span class="barra-label">' . $atual . ' / ' . $maximo . '</span>'
        . '</div>';
}

/**
 * Converte reputação numérica em um rótulo descritivo de alinhamento.
 */
function rotuloReputacao(int $rep): string
{
    if ($rep <= -60) return 'Servo da IA';
    if ($rep <= -20) return 'Tentado pelo Atalho';
    if ($rep < 20)   return 'Aprendiz Neutro';
    if ($rep < 60)   return 'Discípulo Dedicado';
    return 'Mestre do Código Limpo';
}
