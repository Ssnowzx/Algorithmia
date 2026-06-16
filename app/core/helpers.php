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
 * Asset estático com cache-busting por data de modificação (?v=filemtime).
 * Use para CSS/JS: garante que o navegador busque a versão nova após uma
 * mudança, mesmo com cache de longa duração (Expires) ativo no servidor.
 */
function assetV(string $caminho): string
{
    $url = asset($caminho);
    $arquivo = __DIR__ . '/../../public/' . ltrim($caminho, '/');
    if (is_file($arquivo)) {
        $url .= (strpos($url, '?') === false ? '?' : '&') . 'v=' . filemtime($arquivo);
    }
    return $url;
}

/**
 * Insere arte PNG de public/img/{slug}.png como <img>.
 * Ilustrações (mestres, fundos, mapas/): image-rendering auto via CSS específico.
 * Pixel art (heróis, inimigos, itens): image-rendering pixelated em style.css.
 *
 * @param string $slug   Caminho do asset (ex.: 'mestres/mestre-willen', 'mapas/fase-prologo-despertar').
 * @param string $classe Classe(s) CSS opcional(is) aplicada(s) ao <img>.
 * @param string $attrs  Atributos HTML extras opcionais.
 * @return string Tag <img>, ou um marcador "▢" se o asset não existir.
 */
/**
 * Resolve a melhor URL para um asset de imagem por slug (sem extensão),
 * preferindo o .webp otimizado e caindo para o .png de origem. Versiona pela
 * data de modificação (?v=) para cache eterno seguro. Memoiza por requisição
 * para evitar repetir stat()/filemtime() do disco a cada render.
 *
 * @return string|null URL versionada, ou null se o asset não existir.
 */
function srcImagem(string $slug): ?string
{
    static $cache = [];
    if (array_key_exists($slug, $cache)) {
        return $cache[$slug];
    }
    $dir = __DIR__ . '/../../public/img/';
    foreach (['webp', 'png'] as $ext) {
        $arquivo = $dir . $slug . '.' . $ext;
        if (is_file($arquivo)) {
            return $cache[$slug] = asset('img/' . $slug . '.' . $ext) . '?v=' . filemtime($arquivo);
        }
    }
    return $cache[$slug] = null;
}

function svg(string $slug, string $classe = '', string $attrs = ''): string
{
    $src = srcImagem($slug);
    if ($src === null) {
        // Marcador visível para slugs ainda não desenhados.
        return '<span class="svg-faltando" title="' . e($slug) . '">▢</span>';
    }
    $classeAttr = $classe !== '' ? ' class="' . e($classe) . '"' : '';
    $extra = $attrs !== '' ? ' ' . $attrs : '';
    return '<img src="' . $src . '"'
        . $classeAttr . $extra . ' alt="' . e($slug) . '" loading="lazy">';
}

/**
 * Logo unificada. Usa a versão compacta no header e a marca grande nas telas de impacto.
 */
function marcaHtml(string $variante = 'header'): string
{
    $classes = [
        'header' => 'logo-marca logo-marca-header',
        'auth'   => 'logo-marca logo-marca-auth',
        'rodape' => 'logo-marca logo-marca-rodape',
        'splash' => 'logo-marca logo-marca-splash splash-marca',
        'hero'   => 'logo-marca logo-marca-hero',
    ];
    $classe = $classes[$variante] ?? 'logo-marca';
    $arquivos = [
        'header' => 'logo-header.png',
        'rodape' => 'logo-header.png',
        'auth'   => 'logo-marca-ilustrado.png',
        'splash' => 'logo-marca-ilustrado.png',
        'hero'   => 'logo-marca-ilustrado.png',
    ];
    $arquivo = $arquivos[$variante] ?? 'logo-header.png';
    $src = srcImagem('ui/logos/' . pathinfo($arquivo, PATHINFO_FILENAME));
    if ($src === null) {
        return '<span class="svg-faltando" title="ui/logos/' . e($arquivo) . '">▢</span>';
    }
    $lazy = $variante === 'splash' ? 'eager' : 'lazy';
    return '<img src="' . $src . '" class="' . e($classe) . '" alt="' . e(NOME_JOGO) . '" loading="' . $lazy . '">';
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
        'icone-'     => 'ui/icones/',
        'troxeu-'    => 'ui/trofeus/',
        'conquista-' => 'ui/trofeus/',
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
 * Resolve o cenário de fundo ilustrado de uma região a partir do slug do
 * mestre. Regiões sem mestre (início/fim) usam fundo explícito no controller.
 */
function fundoRegiao(?string $mestreSlug): string
{
    return REGIOES_MESTRE[$mestreSlug ?? '']['fundo'] ?? 'fundo-vila';
}

/**
 * Slug do ícone de mapa (fase-*) para uma ordem_global.
 */
function slugPalcoFase(int $ordemGlobal): ?string
{
    $path = iconeFaseMapa($ordemGlobal);
    if ($path === null) {
        return null;
    }
    return basename($path);
}

/**
 * Slug do ator em cena de diálogo.
 * Palco usa atores/ (ilustrado, alpha) — nunca mapas/ nem inimigos/ pixel.
 */
function caminhoAtor(string $slug): string
{
    if ($slug === '') {
        return '';
    }
    if (strpos($slug, '/') !== false) {
        return $slug;
    }
    // O Fragmento da IA aparece SÓ em diálogos (nunca como sprite de batalha):
    // se houver retrato ilustrado em atores/npc-fragmento, usa-o; senão mantém
    // o sprite pixel legado (inimigos/inimigo-ia-ancestral). Troca automática
    // assim que o PNG ilustrado for adicionado.
    if ($slug === 'inimigo-ia-ancestral'
        && is_file(__DIR__ . '/../../public/img/atores/npc-fragmento.png')) {
        return 'atores/npc-fragmento';
    }
    if (str_starts_with($slug, 'fase-')) {
        return 'atores/' . $slug;
    }
    if (str_starts_with($slug, 'mestre-')) {
        $atores = 'atores/' . $slug;
        if (is_file(__DIR__ . '/../../public/img/' . $atores . '.png')) {
            return $atores;
        }
        return 'mestres/' . $slug;
    }
    if (str_starts_with($slug, 'npc-')) {
        return 'atores/' . $slug;
    }
    return caminhoSvg($slug);
}

/** true = arte ilustrada (não pixel art) no palco de diálogo. */
function atorIlustrado(string $slug): bool
{
    $path = caminhoAtor($slug);
    if ($path === '' || str_starts_with($path, 'inimigos/')) {
        return false;
    }
    if (str_starts_with($path, 'atores/')
        || str_starts_with($path, 'mestres/')) {
        return true;
    }
    return false;
}

/** Insere sprite de ator de diálogo a partir de assets existentes em public/img/. */
function svgAtor(string $slug, string $classe = ''): string
{
    if ($slug === '') {
        return '';
    }
    $path = caminhoAtor($slug);
    if ($path === '' || !is_file(__DIR__ . '/../../public/img/' . $path . '.png')) {
        return '';
    }
    if (str_starts_with($slug, 'npc-')) {
        $classe = trim($classe . ' ator-ilustrado ator-npc');
    } elseif (str_starts_with($slug, 'mestre-')) {
        $classe = trim($classe . ' ator-ilustrado ator-mestre');
    } elseif (str_starts_with($slug, 'fase-')) {
        $classe = trim($classe . ' ator-ilustrado ator-fase');
    } elseif (atorIlustrado($slug)) {
        $classe = trim($classe . ' ator-ilustrado');
    } else {
        $classe = trim($classe . ' ator-pixel');
    }
    return svg($path, $classe);
}

/** Resolve slug de ator quando o banco não define svg_slug (ex.: Narrador). */
function slugAtorPorFalante(string $falante): string
{
    if (stripos($falante, 'Narrador') !== false) {
        return 'npc-narrador';
    }
    return '';
}

/**
 * Se existir retrato manual em atores/fase-*.png, usa no palco.
 * Caso contrário mantém o slug do banco (inimigo-*, npc-*, mestre-*).
 */
function slugAtorDialogo(string $slug, array $fase, ?array $mestre = null): string
{
    if ($slug !== '' && str_starts_with($slug, 'inimigo-')) {
        $palco = slugPalcoFase((int) ($fase['ordem_global'] ?? 0));
        if ($palco !== null && is_file(__DIR__ . '/../../public/img/atores/' . $palco . '.png')) {
            return $palco;
        }
    }
    if ($slug !== '') {
        return $slug;
    }
    return personagemFase($fase, $mestre)['slug'];
}

/** Fase exige cena de combate (lição, chefe, secundária). */
function faseEhCombate(array $fase): bool
{
    return in_array($fase['tipo'] ?? '', ['licao', 'chefe', 'chefe_final', 'secundaria'], true);
}

/**
 * Personagem principal ilustrado no palco (atores/fase-*.png ou NPC/mestre).
 *
 * @return array{slug: string, nome: string}
 */
function personagemFase(array $fase, ?array $mestre = null): array
{
    $nomeInimigo = !empty($fase['inimigo_nome']) ? (string) $fase['inimigo_nome'] : (string) $fase['nome'];
    $palco = slugPalcoFase((int) ($fase['ordem_global'] ?? 0));
    if ($palco !== null && is_file(__DIR__ . '/../../public/img/atores/' . $palco . '.png')) {
        return ['slug' => $palco, 'nome' => $nomeInimigo];
    }
    if ($mestre && !empty($mestre['svg_slug'])) {
        return [
            'slug' => (string) $mestre['svg_slug'],
            'nome' => (string) ($mestre['nome'] ?? $fase['nome']),
        ];
    }
    $ordem = (int) ($fase['ordem_global'] ?? 0);
    if ($ordem === 1) {
        return ['slug' => 'npc-anciao', 'nome' => 'Anciã da Vila'];
    }
    if ($ordem === 34 || ($fase['tipo'] ?? '') === 'chefe_final') {
        $final = slugPalcoFase(35);
        if ($final !== null && is_file(__DIR__ . '/../../public/img/atores/' . $final . '.png')) {
            return ['slug' => $final, 'nome' => 'Lorde Segfault'];
        }
        return ['slug' => 'npc-narrador', 'nome' => 'Lorde Segfault'];
    }
    return ['slug' => 'npc-narrador', 'nome' => (string) $fase['nome']];
}

/**
 * Ícone ilustrado de fase no mapa (todas as 35 fases).
 * Fallback para emoji se o PNG não existir.
 */
function iconeFaseMapa(int $ordemGlobal): ?string
{
    $mapa = [
        1  => 'mapas/fase-prologo-despertar',
        2  => 'mapas/fase-primeiros-passos',
        3  => 'mapas/fase-bug-primordial',
        4  => 'mapas/fase-porto-chegada',
        5  => 'mapas/fase-variaveis-eco',
        6  => 'mapas/fase-estruturas-controle',
        7  => 'mapas/fase-padrao-mvc',
        8  => 'mapas/fase-bau-select',
        9  => 'mapas/fase-parse-error-kraken',
        10 => 'mapas/fase-cidadela-chegada',
        11 => 'mapas/fase-classes-objetos',
        12 => 'mapas/fase-encapsulamento',
        13 => 'mapas/fase-heranca-composicao',
        14 => 'mapas/fase-interfaces-secretas',
        15 => 'mapas/fase-god-class',
        16 => 'mapas/fase-floresta-chegada',
        17 => 'mapas/fase-pilhas-filas',
        18 => 'mapas/fase-listas-nos',
        19 => 'mapas/fase-arvores-bigo',
        20 => 'mapas/fase-gol-quadrado',
        21 => 'mapas/fase-hidra-recursiva',
        22 => 'mapas/fase-montanha-chegada',
        23 => 'mapas/fase-sequencias-ritmo',
        24 => 'mapas/fase-recursao-limites',
        25 => 'mapas/fase-complexidade',
        26 => 'mapas/fase-verdade-zero',
        27 => 'mapas/fase-limite-colosso',
        28 => 'mapas/fase-torre-chegada',
        29 => 'mapas/fase-camadas-osi',
        30 => 'mapas/fase-ip-dns-rotas',
        31 => 'mapas/fase-tcp-udp-http',
        32 => 'mapas/fase-pacote-perdido',
        33 => 'mapas/fase-ddos-enxame',
        34 => 'mapas/fase-abismo-devnull',
        35 => 'mapas/fase-lorde-segfault',
    ];
    $slug = $mapa[$ordemGlobal] ?? null;
    if ($slug === null) {
        return null;
    }
    $png = __DIR__ . '/../../public/img/' . $slug . '.png';
    return is_file($png) ? $slug : null;
}

/**
 * Emblema ilustrado de região sem mestre (Hello World / Abismo).
 */
function iconeRegiaoMapa(string $chaveRegiao): ?string
{
    $mapa = [
        'inicio' => 'mapas/regiao-hello-world',
        'fim'    => 'mapas/regiao-abismo',
    ];
    return $mapa[$chaveRegiao] ?? null;
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
 * Retrato ilustrado do herói para o HUD do topo (cyber-fantasia).
 */
function retratoHud(string $classe): string
{
    $map = [
        'mago'      => 'herois/hud-mago',
        'guerreiro' => 'herois/hud-guerreiro',
        'ranger'    => 'herois/hud-ranger',
        'xeno'      => 'herois/hud-xeno',
        'elfo'      => 'herois/hud-elfo',
        'draconato' => 'herois/hud-draconato',
    ];
    return $map[$classe] ?? 'herois/hud-ranger';
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
