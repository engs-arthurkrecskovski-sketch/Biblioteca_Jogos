<?php

use App\Http\Controllers\AssinaturaController;
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\BibliotecaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JogoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::guard('admin')->check()) return redirect()->route('home');
    if (Auth::guard('web')->check())   return redirect('/minha-biblioteca');
    return redirect()->route('login');
});

Route::middleware('guest:admin,web')->group(function () {
    Route::get('/login', [LoginController::class, 'form'])->name('login');
    Route::post('/login', [LoginController::class, 'entrar'])->name('login.entrar');
});
Route::post('/logout', [LoginController::class, 'sair'])->name('logout');

Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::get('/', HomeController::class)->name('home');

    Route::resource('jogos', JogoController::class);
    Route::resource('usuarios', UsuarioController::class);

    Route::post('usuarios/{usuario}/biblioteca', [BibliotecaController::class, 'store'])->name('usuarios.biblioteca.store');
    Route::post('usuarios/{usuario}/assinatura', [AssinaturaController::class, 'renovar'])->name('usuarios.assinatura.renovar');

    Route::resource('avaliacoes', AvaliacaoController::class)
        ->only(['store', 'destroy'])
        ->parameters(['avaliacoes' => 'avaliacao']);

    Route::get('ranking', RankingController::class)->name('ranking');
});

Route::middleware('auth:web')->get('/minha-biblioteca', function () {
    return 'Área do cliente em construção. Olá, ' . Auth::guard('web')->user()->nome;
});