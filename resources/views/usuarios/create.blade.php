@extends('layouts.app')
@section('titulo', 'Novo usuário')

@section('conteudo')
    <h1 class="h3 mb-3">Novo usuário</h1>
    <form method="POST" action="{{ route('usuarios.store') }}" class="card card-body">
        @include('usuarios._form')
    </form>
@endsection
