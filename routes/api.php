<?php

use App\Http\Controllers\Api\JogoController;
use App\Http\Controllers\Api\RankingController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::apiResource('jogos', JogoController::class);

    Route::get('generos/{genero}/ranking', RankingController::class)->name('ranking');
    Route::get('usuarios/{usuario}/horas', [UsuarioController::class, 'horas'])->name('usuarios.horas');
    Route::post('usuarios/{usuario}/biblioteca', [UsuarioController::class, 'adicionarJogo'])->name('usuarios.biblioteca');
});