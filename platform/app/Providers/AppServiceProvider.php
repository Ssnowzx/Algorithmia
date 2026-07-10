<?php

declare(strict_types=1);

namespace App\Providers;

use App\Dominio\Combate\BatalhaEmSessao;
use App\Dominio\Combate\RepositorioDeBatalha;
use App\Dominio\Combate\SorteadorDeDesafios;
use App\Dominio\Combate\SorteioAntiRepeticao;
use App\Dominio\Tenancy\ContextoDoTenant;
use App\Dominio\Tenancy\Flags;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // As duas costuras do motor de batalha. Em produção, a batalha vive na
        // sessão e o sorteio embaralha; nos testes, ambas são substituídas para
        // que os vetores-ouro meçam a aritmética, e não o Mt19937 do PHP.
        $this->app->bind(RepositorioDeBatalha::class, BatalhaEmSessao::class);
        $this->app->bind(SorteadorDeDesafios::class, SorteioAntiRepeticao::class);

        // Os dois guardam o estado do pedido: de qual instituição ele é, e o que ela
        // enxerga. Sem `singleton`, cada `app()` devolvia uma instância nova e `atual()`
        // respondia `null` a quem não a definira — o `ResolverTenant` marcava o tenant
        // numa cópia, e o resto da aplicação lia outra. Só não quebrou até agora porque
        // ninguém lia `atual()` fora de quem acabara de escrevê-lo.
        $this->app->singleton(ContextoDoTenant::class);
        $this->app->singleton(Flags::class);
    }

    public function boot(): void
    {
        // Atribuir a uma coluna que não existe deve explodir no desenvolvimento,
        // não gravar silenciosamente nada. O legado escrevia via SQL cru, então
        // erros de nome de coluna apareciam de imediato; o Eloquent os engole.
        Model::preventSilentlyDiscardingAttributes($this->app->isLocal());

        $this->limitarTentativasDeAutenticacao();

        // `@flag('turmas') … @endflag`. A chave é validada contra `config/flags.php`:
        // um nome errado explode, e não some do menu em silêncio.
        Blade::if('flag', fn (string $chave): bool => $this->app->make(Flags::class)->ativa($chave));
    }

    /**
     * Sem isto, o `/entrar` aceita quantas senhas por segundo o atacante conseguir
     * enviar. O jogo tem contas de aluno e uma conta `mestre` com painel.
     *
     * Dois limites, porque um só não basta:
     *  - por e-mail+IP, contra adivinhar a senha de UMA conta;
     *  - por IP, contra varrer MUITAS contas com a mesma senha ("password spraying"),
     *    que passaria batido pelo primeiro.
     */
    private function limitarTentativasDeAutenticacao(): void
    {
        RateLimiter::for('entrar', function (Request $requisicao): array {
            $email = Str::transliterate(Str::lower((string) $requisicao->input('email', '')));

            return [
                Limit::perMinute(5)->by($email.'|'.$requisicao->ip()),
                Limit::perMinute(20)->by((string) $requisicao->ip()),
            ];
        });

        // Registro é mais caro de abusar e mais raro de usar: um limite só, por IP.
        RateLimiter::for('registrar', fn (Request $requisicao): Limit => Limit::perMinute(5)
            ->by((string) $requisicao->ip()));
    }
}
