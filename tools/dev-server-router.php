<?php
/**
 * Router de desenvolvimento para o servidor embutido do PHP.
 *
 * O `php -S` NÃO suporta requisições Range (HTTP 206), que o elemento <video>
 * do Chrome/Safari exige para reproduzir MP4. Sem isso, as intros em vídeo das
 * fases ficam em "buffering" eterno só no ambiente local. Em produção isso não
 * acontece: o Apache (.htaccess) já trata Range nativamente.
 *
 * Use este router APENAS em dev, para testar as cinemáticas localmente:
 *
 *   php -S 127.0.0.1:8000 tools/dev-server-router.php
 *
 * Ele intercepta arquivos .mp4 e responde com Range; todo o resto
 * (index.php?url=..., CSS/JS/imagens) cai no tratamento padrão do servidor.
 */

$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$file = getcwd() . $uri;

// Bloqueia path traversal e só serve .mp4 reais de dentro do projeto.
$real = realpath($file);
$raiz = realpath(getcwd());
if ($real !== false
    && $raiz !== false
    && str_starts_with($real, $raiz)
    && preg_match('/\.mp4$/i', $uri)
    && is_file($real)
) {
    $size  = filesize($real);
    $start = 0;
    $end   = $size - 1;

    header('Content-Type: video/mp4');
    header('Accept-Ranges: bytes');

    if (isset($_SERVER['HTTP_RANGE'])
        && preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $m)
    ) {
        if ($m[1] !== '') { $start = (int) $m[1]; }
        if ($m[2] !== '') { $end = (int) $m[2]; }
        $end = min($end, $size - 1);
        header('HTTP/1.1 206 Partial Content');
        header("Content-Range: bytes $start-$end/$size");
    }

    header('Content-Length: ' . ($end - $start + 1));

    $fp   = fopen($real, 'rb');
    fseek($fp, $start);
    $left = $end - $start + 1;
    while ($left > 0 && !feof($fp)) {
        $chunk = fread($fp, (int) min(8192, $left));
        echo $chunk;
        $left -= strlen($chunk);
        flush();
    }
    fclose($fp);
    return true;
}

// Qualquer outra coisa: deixa o servidor embutido resolver (estático/index.php).
return false;
