<?php

use App\Http\Controllers\AssinaturaController;
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\BibliotecaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JogoController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::resource('jogos', JogoController::class);
Route::resource('usuarios', UsuarioController::class);

Route::post('usuarios/{usuario}/biblioteca', [BibliotecaController::class, 'store'])->name('usuarios.biblioteca.store');
Route::post('usuarios/{usuario}/assinatura', [AssinaturaController::class, 'renovar'])->name('usuarios.assinatura.renovar');

Route::resource('avaliacoes', AvaliacaoController::class)
    ->only(['store', 'destroy'])
    ->parameters(['avaliacoes' => 'avaliacao']);

Route::get('ranking', RankingController::class)->name('ranking');