<?php

declare(strict_types=1);

namespace App\Providers;

use App\Dominio\Combate\BatalhaEmSessao;
use App\Dominio\Combate\RepositorioDeBatalha;
use App\Dominio\Combate\SorteadorDeDesafios;
use App\Dominio\Combate\SorteioAntiRepeticao;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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
    }

    public function boot(): void
    {
        // Atribuir a uma coluna que não existe deve explodir no desenvolvimento,
        // não gravar silenciosamente nada. O legado escrevia via SQL cru, então
        // erros de nome de coluna apareciam de imediato; o Eloquent os engole.
        Model::preventSilentlyDiscardingAttributes($this->app->isLocal());

        $this->limitarTentativasDeAutenticacao();
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
