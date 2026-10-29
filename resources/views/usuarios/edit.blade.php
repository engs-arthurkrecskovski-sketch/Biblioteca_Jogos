@extends('layouts.app')
@section('titulo', 'Editar usuário')

@section('conteudo')
    <h1 class="h3 mb-3">Editar usuário #{{ $usuario->id_usuario }}</h1>
    <form method="POST" action="{{ route('usuarios.update', $usuario) }}" class="card card-body">
        @method('PUT')
        @include('usuarios._form')
    </form>
@endsection
