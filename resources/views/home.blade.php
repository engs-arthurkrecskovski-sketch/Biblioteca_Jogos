@extends('layouts.app')
@section('titulo', 'Início')

@section('conteudo')
    <h1 class="h3 mb-4">Biblioteca Digital de Jogos Eletrônicos</h1>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card text-center"><div class="card-body">
            <div class="fs-3">{{ number_format($totalJogos, 0, ',', '.') }}</div><div class="text-muted">Jogos</div>
        </div></div></div>
        <div class="col-md-3"><div class="card text-center"><div class="card-body">
            <div class="fs-3">{{ number_format($totalUsuarios, 0, ',', '.') }}</div><div class="text-muted">Usuários</div>
        </div></div></div>
        <div class="col-md-3"><div class="card text-center"><div class="card-body">
            <div class="fs-3">{{ number_format($totalAvaliacoes, 0, ',', '.') }}</div><div class="text-muted">Avaliações</div>
        </div></div></div>
        <div class="col-md-3"><div class="card text-center"><div class="card-body">
            <div class="fs-3">{{ number_format($totalBiblioteca, 0, ',', '.') }}</div><div class="text-muted">Jogos em bibliotecas</div>
        </div></div></div>
    </div>