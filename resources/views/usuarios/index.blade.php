@extends('layouts.app')
@section('titulo', 'Usuários')

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Usuários</h1>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">Novo usuário</a>
    </div>

    <form method="GET" action="{{ route('usuarios.index') }}" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="busca" value="{{ request('busca') }}" class="form-control" placeholder="Buscar por nome ou e-mail">
        </div>
        <div class="col-auto"><button class="btn btn-outline-secondary">Buscar</button></div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped align-middle bg-white">
            <thead><tr><th>ID</th><th>Nome</th><th>E-mail</th><th></th></tr></thead>
            <tbody>
            @forelse ($usuarios as $u)
                <tr>
                    <td>{{ $u->id_usuario }}</td>
                    <td><a href="{{ route('usuarios.show', $u) }}">{{ $u->nome }}</a></td>
                    <td>{{ $u->email }}</td>
                    <td class="text-end">
                        <a href="{{ route('usuarios.edit', $u) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        <form action="{{ route('usuarios.destroy', $u) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Excluir este usuário?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">Nenhum usuário encontrado.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $usuarios->links() }}
@endsection
