@extends('layouts.app')
@section('titulo', 'Editar jogo')

@section('conteudo')
    <h1 class="h3 mb-3">Editar jogo #{{ $jogo->id_jogo }}</h1>
    <form method="POST" action="{{ route('jogos.update', $jogo) }}" class="card card-body">
        @method('PUT')
        @include('jogos._form')
    </form>
@endsection