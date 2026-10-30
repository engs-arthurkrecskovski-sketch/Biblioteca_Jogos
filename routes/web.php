<?php

use App\Http\Controllers\AssinaturaController;
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\BibliotecaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JogoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [LoginController::class, 'form'])->name('login');
Route::post('/login', [LoginController::class, 'entrar'])->name('login.entrar');
Route::post('/logout', [LoginController::class, 'sair'])->name('logout');

Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/', HomeController::class)->name('home');

    Route::resource('jogos', JogoController::class);

    Route::resource('usuarios', UsuarioController::class);
    Route::post('usuarios/{usuario}/assinatura', [AssinaturaController::class, 'renovar'])
        ->name('usuarios.assinatura.renovar');
    Route::post('usuarios/{usuario}/biblioteca', [BibliotecaController::class, 'store'])
        ->name('usuarios.biblioteca.store');

    Route::resource('avaliacoes', AvaliacaoController::class)
        ->only(['store', 'destroy'])
        ->parameters(['avaliacoes' => 'avaliacao']);

    Route::get('ranking', RankingController::class)->name('ranking');
});

Route::get('/minha-biblioteca', fn () => redirect()->route('cliente.inicio'));

Route::middleware('auth:web')->prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/', [ClienteController::class, 'inicio'])->name('inicio');
    Route::get('/biblioteca', [ClienteController::class, 'biblioteca'])->name('biblioteca');
    Route::get('/loja', [ClienteController::class, 'loja'])->name('loja');

    Route::post('/loja/{jogo}/comprar', [ClienteController::class, 'comprar'])->name('comprar');

    Route::get('/loja/{jogo}/checkout', [ClienteController::class, 'checkout'])->name('checkout');
    Route::post('/loja/{jogo}/pagar', [ClienteController::class, 'pagar'])->name('pagar');

    Route::get('/avaliar', [ClienteController::class, 'avaliar'])->name('avaliar');
    Route::post('/avaliar/{jogo}', [ClienteController::class, 'salvarAvaliacao'])->name('avaliar.salvar');
});