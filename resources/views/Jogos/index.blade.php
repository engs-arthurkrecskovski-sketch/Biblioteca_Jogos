@extends('layouts.app')
@section('titulo', 'Jogos')

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Jogos</h1>
        <a href="{{ route('jogos.create') }}" class="btn btn-primary">Novo jogo</a>
    </div>

    <form method="GET" action="{{ route('jogos.index') }}" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="busca" value="{{ request('busca') }}" class="form-control" placeholder="Buscar pelo título">
        </div>
        <div class="col-auto"><button class="btn btn-outline-secondary">Buscar</button></div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped align-middle bg-white">
            <thead>
            <tr>
                <th>ID</th><th>Título</th><th>Gênero</th><th>Desenvolvedora</th>
                <th class="text-end">Preço</th><th class="text-center">Nota</th><th></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($jogos as $jogo)
                <tr>
                    <td>{{ $jogo->id_jogo }}</td>
                    <td><a href="{{ route('jogos.show', $jogo) }}">{{ $jogo->titulo }}</a></td>
                    <td>{{ $jogo->genero?->nome ?? '-' }}</td>
                    <td>{{ $jogo->desenvolvedora?->nome ?? '-' }}</td>
                    <td class="text-end">R$ {{ number_format($jogo->preco, 2, ',', '.') }}</td>
                    <td class="text-center">
                        {{ $jogo->nota_media !== null ? number_format($jogo->nota_media, 2, ',', '.') : '-' }}
                        <small class="text-muted">({{ $jogo->total_avaliacoes }})</small>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('jogos.edit', $jogo) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        <form action="{{ route('jogos.destroy', $jogo) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Excluir este jogo?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">Nenhum jogo encontrado.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $jogos->links() }}
@endsection
