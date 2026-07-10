<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * Sem `CONSOLE_HOST`, o console não existe.
 *
 * A diferença entre "um middleware recusa" e "a rota não foi registrada" é a diferença
 * entre uma linha de código que alguém pode remover e uma tela que não tem como ser
 * alcançada. O console lê e administra todas as instituições: publicá-lo por acidente no
 * domínio de uma escola daria a qualquer aluno a tela de login da plataforma para atacar.
 *
 * Esta classe **não** define `CONSOLE_HOST` — é o padrão de produção. O
 * `ConsoleDoOperadorTest` o define antes de a aplicação subir, que é a única janela em que
 * as rotas ainda podem ser registradas.
 */
final class ConsoleDesligadoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        // Explícito, e não por confiança na ordem dos testes: o `ConsoleDoOperadorTest`
        // define esta variável, e um teste cuja premissa depende de quem rodou antes não é
        // um teste — é uma coincidência.
        putenv('CONSOLE_HOST');
        unset($_ENV['CONSOLE_HOST'], $_SERVER['CONSOLE_HOST']);

        parent::setUp();
    }

    #[Test]
    public function sem_console_host_as_rotas_nao_sao_registradas(): void
    {
        // ARRANGE + ACT + ASSERT
        $this->assertFalse(Route::has('console.entrar'));
        $this->assertFalse(Route::has('console.painel'));
    }

    #[Test]
    public function o_console_responde_404_no_dominio_da_escola(): void
    {
        // ARRANGE: `localhost` é o host do tenant padrão.
        // ACT + ASSERT
        $this->get('http://localhost/console')->assertNotFound();
        $this->get('http://localhost/console/instituicoes')->assertNotFound();
        $this->post('http://localhost/console/instituicoes/1/ativar')->assertNotFound();
    }

    /** Um host que não é instituição nem console: o `ResolverTenant` recusa antes de tudo. */
    #[Test]
    public function o_console_responde_404_num_host_qualquer(): void
    {
        $this->get('http://console.algorithmia.test/console')->assertNotFound();
    }
}
