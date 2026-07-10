<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Tests\TestCase;

/**
 * O comentário do nginx prometia um CSP restritivo desde sempre. Nenhum header
 * existia. Estes testes existem para que a promessa não volte a ser só um comentário.
 */
final class PoliticaDeConteudoTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function toda_pagina_web_traz_o_header(): void
    {
        // ARRANGE + ACT
        $resposta = $this->get('/');

        // ASSERT
        $resposta->assertOk();
        $this->assertNotNull($resposta->headers->get('Content-Security-Policy'));
    }

    #[Test]
    public function o_script_src_nao_permite_inline(): void
    {
        // ARRANGE + ACT
        $csp = (string) $this->get('/')->headers->get('Content-Security-Policy');

        // ASSERT: um `unsafe-inline` em script-src desliga o CSP na prática.
        $this->assertMatchesRegularExpression("/script-src [^;]*'nonce-/", $csp);
        $this->assertDoesNotMatchRegularExpression("/script-src [^;]*'unsafe-inline'/", $csp);
        $this->assertDoesNotMatchRegularExpression("/script-src [^;]*'unsafe-eval'/", $csp);
    }

    #[Test]
    public function o_nonce_do_header_e_o_do_html_sao_o_mesmo(): void
    {
        // ARRANGE + ACT: a home tem um `<script>` inline.
        $resposta = $this->get('/');
        $csp = (string) $resposta->headers->get('Content-Security-Policy');
        $html = $resposta->getContent();

        // ASSERT
        $this->assertSame(1, preg_match("/'nonce-([^']+)'/", $csp, $doHeader));
        $this->assertIsString($html);
        $this->assertStringContainsString('nonce="'.$doHeader[1].'"', $html);
    }

    #[Test]
    public function o_nonce_muda_a_cada_requisicao(): void
    {
        // ARRANGE + ACT
        $primeiro = (string) $this->get('/')->headers->get('Content-Security-Policy');
        $segundo = (string) $this->get('/')->headers->get('Content-Security-Policy');

        // ASSERT: um nonce fixo é o mesmo que não ter nonce.
        $this->assertNotSame($primeiro, $segundo);
    }

    #[Test]
    public function a_politica_fecha_as_portas_que_o_jogo_nao_usa(): void
    {
        // ARRANGE + ACT
        $csp = (string) $this->get('/')->headers->get('Content-Security-Policy');

        // ASSERT
        $this->assertStringContainsString("default-src 'none'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
        $this->assertStringContainsString("base-uri 'none'", $csp);
        $this->assertStringContainsString("form-action 'self'", $csp);

        // `data:` em img-src é um canal de exfiltração de graça, e o jogo não usa.
        $this->assertStringContainsString("img-src 'self'", $csp);
        $this->assertDoesNotMatchRegularExpression('/img-src [^;]*data:/', $csp);
    }

    /**
     * A política diz `'self'` para script, img e font. No dia em que alguém puser um
     * Google Fonts no layout ou um CDN no CSS, a página quebra em produção e passa nos
     * testes — a menos que este aqui exista.
     */
    #[Test]
    public function nenhum_recurso_externo_e_carregado_pelo_html_ou_pelo_css(): void
    {
        // ARRANGE
        $ofensores = [];

        foreach ($this->arquivos(resource_path('views'), '.blade.php') as $arquivo) {
            $conteudo = (string) file_get_contents($arquivo);

            if (preg_match('/<(?:link|script|img)[^>]+(?:src|href)\s*=\s*["\']https?:/i', $conteudo) === 1) {
                $ofensores[] = basename($arquivo);
            }
        }

        foreach ($this->arquivos(public_path('css'), '.css') as $arquivo) {
            $conteudo = (string) file_get_contents($arquivo);

            if (preg_match('/url\(\s*["\']?https?:|@import\s+(?:url\()?["\']https?:/i', $conteudo) === 1) {
                $ofensores[] = basename($arquivo);
            }
        }

        // ASSERT
        $this->assertSame([], $ofensores, 'o CSP diz \'self\'; estes arquivos pedem outro host');
    }

    /** @return list<string> */
    private function arquivos(string $raiz, string $sufixo): array
    {
        if (! is_dir($raiz)) {
            return [];
        }

        $achados = [];

        /** @var SplFileInfo $arquivo */
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($raiz)) as $arquivo) {
            if ($arquivo->isFile() && str_ends_with($arquivo->getFilename(), $sufixo)) {
                $achados[] = $arquivo->getPathname();
            }
        }

        return $achados;
    }

    /**
     * O `nonce` autoriza elementos `<script>`, nunca atributos `onclick`/`onsubmit`.
     * Um só que volte obriga `script-src 'unsafe-inline'`, e o CSP inteiro perde o
     * sentido. Por isso o teste olha as views, e não a resposta.
     */
    #[Test]
    public function nenhuma_view_usa_atributo_de_evento_inline(): void
    {
        // ARRANGE
        $views = resource_path('views');
        $encontrados = [];

        /** @var SplFileInfo $arquivo */
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($views)) as $arquivo) {
            if (! $arquivo->isFile() || ! str_ends_with($arquivo->getFilename(), '.blade.php')) {
                continue;
            }

            $conteudo = (string) file_get_contents($arquivo->getPathname());

            if (preg_match('/\son(click|submit|change|load|error|mouseover)\s*=/i', $conteudo) === 1) {
                $encontrados[] = $arquivo->getFilename();
            }
        }

        // ASSERT
        $this->assertSame([], $encontrados, 'use data-confirmar + public/js/confirmar.js');
    }
}
