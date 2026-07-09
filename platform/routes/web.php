<?php

declare(strict_types=1);

use App\Http\Controllers\AutenticacaoController;
use App\Http\Controllers\BatalhaController;
use App\Http\Controllers\HealthzController;
use App\Http\Controllers\HistoriaController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PersonagemController;
use Illuminate\Support\Facades\Route;

/*
 * Rotas nomeadas no lugar de `?url=controller/metodo/param`.
 *
 * Toda rota que ESCREVE é POST. No legado, `historia/concluir` gravava progresso
 * e XP por GET, e os endpoints de batalha validavam o token CSRF mas aceitavam
 * qualquer método — bastava um `<img src>` para disparar um turno.
 */

Route::get('/healthz', HealthzController::class)->name('healthz');

Route::middleware('guest')->group(function (): void {
    Route::get('/entrar', [AutenticacaoController::class, 'mostrarLogin'])->name('login');
    Route::post('/entrar', [AutenticacaoController::class, 'entrar']);

    Route::get('/registrar', [AutenticacaoController::class, 'mostrarRegistro'])->name('registro');
    Route::post('/registrar', [AutenticacaoController::class, 'registrar']);
});

Route::middleware('auth')->group(function (): void {
    Route::post('/sair', [AutenticacaoController::class, 'sair'])->name('sair');

    // Ainda sem herói: estas duas não podem exigir personagem, sob pena de laço.
    Route::get('/personagem/criar', [PersonagemController::class, 'mostrarCriacao'])->name('personagem.criar');
    Route::post('/personagem', [PersonagemController::class, 'criar'])->name('personagem.salvar');

    Route::middleware('personagem')->group(function (): void {
        Route::get('/', [MapaController::class, 'index'])->name('mapa');
        Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');

        Route::get('/historia/{fase}', [HistoriaController::class, 'ver'])
            ->whereNumber('fase')->name('historia.ver');

        // POST. No legado isto era GET e gravava progresso e XP: um <img src>
        // avançava a campanha de quem apenas abriu uma página.
        Route::post('/historia/{fase}/concluir', [HistoriaController::class, 'concluir'])
            ->whereNumber('fase')->name('historia.concluir');

        // O curinga precisa ser numérico: sem isso ele engole `GET /batalha/responder`
        // e tenta carregar a fase de id "responder". Restringindo, a URI só casa com
        // a rota POST e o método errado devolve 405, como deve.
        Route::get('/batalha/{fase}', [BatalhaController::class, 'iniciar'])
            ->whereNumber('fase')
            ->name('batalha.iniciar');

        // Turnos: JSON sobre POST, com token no cabeçalho X-CSRF-TOKEN.
        Route::post('/batalha/responder', [BatalhaController::class, 'responder'])->name('batalha.responder');
        Route::post('/batalha/fragmento', [BatalhaController::class, 'fragmento'])->name('batalha.fragmento');
        Route::post('/batalha/especial', [BatalhaController::class, 'especial'])->name('batalha.especial');
        Route::post('/batalha/pocao', [BatalhaController::class, 'pocao'])->name('batalha.pocao');
        Route::post('/batalha/fugir', [BatalhaController::class, 'fugir'])->name('batalha.fugir');
    });
});
