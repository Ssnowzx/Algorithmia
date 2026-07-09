<?php

declare(strict_types=1);

use App\Http\Controllers\AutenticacaoController;
use App\Http\Controllers\BatalhaController;
use App\Http\Controllers\HealthzController;
use App\Http\Controllers\HistoriaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\MestreController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PersonagemController;
use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;

/*
 * Rotas nomeadas no lugar de `?url=controller/metodo/param`.
 *
 * Toda rota que ESCREVE é POST. No legado, `historia/concluir` gravava progresso
 * e XP por GET; `loja/vender/5` transformava um item em ouro; `mestre/excluirFase/5`
 * apagava a fase e seus desafios em cascata; e `inventario/descartar/5` destruía o
 * item para sempre. Todas alcançáveis por um `<img src>`.
 */

Route::get('/healthz', HealthzController::class)->name('healthz');

// A lore é a vitrine da história: não exige conta.
Route::get('/historia', [HistoriaController::class, 'lore'])->name('lore');

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
        Route::get('/ranking', [RankingController::class, 'index'])->name('ranking');

        // ---------------------------------------------------------- história
        Route::get('/final', [HistoriaController::class, 'final'])->name('historia.final');
        Route::post('/final', [HistoriaController::class, 'escolherFinal'])->name('historia.escolher');

        Route::get('/historia/{fase}', [HistoriaController::class, 'ver'])
            ->whereNumber('fase')->name('historia.ver');
        Route::post('/historia/{fase}/concluir', [HistoriaController::class, 'concluir'])
            ->whereNumber('fase')->name('historia.concluir');

        // ---------------------------------------------------------- batalha
        // O curinga precisa ser numérico: sem isso ele engole `GET /batalha/responder`
        // e tenta carregar a fase de id "responder". Restringindo, a URI só casa com
        // a rota POST e o método errado devolve 405, como deve.
        Route::get('/batalha/{fase}', [BatalhaController::class, 'iniciar'])
            ->whereNumber('fase')->name('batalha.iniciar');

        Route::post('/batalha/responder', [BatalhaController::class, 'responder'])->name('batalha.responder');
        Route::post('/batalha/fragmento', [BatalhaController::class, 'fragmento'])->name('batalha.fragmento');
        Route::post('/batalha/especial', [BatalhaController::class, 'especial'])->name('batalha.especial');
        Route::post('/batalha/pocao', [BatalhaController::class, 'pocao'])->name('batalha.pocao');
        Route::post('/batalha/fugir', [BatalhaController::class, 'fugir'])->name('batalha.fugir');

        // -------------------------------------------------------------- loja
        Route::get('/loja', [LojaController::class, 'index'])->name('loja');
        Route::post('/loja/{item}/comprar', [LojaController::class, 'comprar'])->name('loja.comprar');
        Route::post('/loja/{item}/vender', [LojaController::class, 'vender'])->name('loja.vender');

        // --------------------------------------------------------- inventário
        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');
        Route::post('/inventario/{item}/equipar', [InventarioController::class, 'equipar'])->name('inventario.equipar');
        Route::post('/inventario/{item}/desequipar', [InventarioController::class, 'desequipar'])->name('inventario.desequipar');
        Route::post('/inventario/{item}/usar', [InventarioController::class, 'usar'])->name('inventario.usar');
        Route::post('/inventario/{item}/descartar', [InventarioController::class, 'descartar'])->name('inventario.descartar');
    });

    // ------------------------------------------------------- Painel do Mestre
    //
    // As rotas `/novo` e `/nova` vêm ANTES do curinga, e o curinga é numérico:
    // sem as duas coisas, `GET /mestre/desafios/novo` procuraria o desafio de id
    // "novo" e devolveria 404 no lugar do formulário.
    Route::middleware('mestre')->prefix('mestre')->name('mestre.')->group(function (): void {
        Route::get('/', [MestreController::class, 'index'])->name('painel');

        Route::get('/desafios', [MestreController::class, 'desafios'])->name('desafios');
        Route::get('/desafios/novo', [MestreController::class, 'desafioNovo'])->name('desafio.novo');
        Route::post('/desafios', [MestreController::class, 'desafioCriar'])->name('desafio.criar');
        Route::get('/desafios/{desafio}', [MestreController::class, 'desafioEditar'])->whereNumber('desafio')->name('desafio.editar');
        Route::post('/desafios/{desafio}', [MestreController::class, 'desafioAtualizar'])->whereNumber('desafio')->name('desafio.atualizar');
        Route::post('/desafios/{desafio}/excluir', [MestreController::class, 'desafioExcluir'])->whereNumber('desafio')->name('desafio.excluir');

        Route::get('/fases', [MestreController::class, 'fases'])->name('fases');
        Route::get('/fases/nova', [MestreController::class, 'faseNova'])->name('fase.nova');
        Route::post('/fases', [MestreController::class, 'faseCriar'])->name('fase.criar');
        Route::get('/fases/{fase}', [MestreController::class, 'faseEditar'])->whereNumber('fase')->name('fase.editar');
        Route::post('/fases/{fase}', [MestreController::class, 'faseAtualizar'])->whereNumber('fase')->name('fase.atualizar');
        Route::post('/fases/{fase}/excluir', [MestreController::class, 'faseExcluir'])->whereNumber('fase')->name('fase.excluir');

        Route::get('/itens', [MestreController::class, 'itens'])->name('itens');
        Route::get('/itens/novo', [MestreController::class, 'itemNovo'])->name('item.novo');
        Route::post('/itens', [MestreController::class, 'itemCriar'])->name('item.criar');
        Route::get('/itens/{item}', [MestreController::class, 'itemEditar'])->whereNumber('item')->name('item.editar');
        Route::post('/itens/{item}', [MestreController::class, 'itemAtualizar'])->whereNumber('item')->name('item.atualizar');
        Route::post('/itens/{item}/excluir', [MestreController::class, 'itemExcluir'])->whereNumber('item')->name('item.excluir');
    });
});
