<?php

declare(strict_types=1);

namespace App\Providers;

use App\Dominio\Combate\BatalhaEmSessao;
use App\Dominio\Combate\RepositorioDeBatalha;
use App\Dominio\Combate\SorteadorDeDesafios;
use App\Dominio\Combate\SorteioAntiRepeticao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

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
    }
}
