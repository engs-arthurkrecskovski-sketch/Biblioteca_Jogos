@extends('layouts.app')
@section('titulo', 'Novo jogo')

@section('conteudo')
    <h1 class="h3 mb-3">Novo jogo</h1>
    <form method="POST" action="{{ route('jogos.store') }}" class="card card-body">
        @include('jogos._form')
    </form>
@endsection
