@extends('layouts.app')
@section('titulo', 'Ranking')

@section('conteudo')
    <h1 class="h3 mb-3">Ranking de jogos por gênero</h1>
    <p class="text-muted">Usa a function <code>fn_ranking_jogos_genero</code> do PostgreSQL.</p>

    <form method="GET" action="{{ route('ranking') }}" class="row g-2 mb-4">
        <div class="col-md-6">
            <select name="genero" class="form-select" required>
                <option value="">-- escolha um gênero --</option>
                @foreach ($generos as $g)
                    <option value="{{ $g->id_genero }}" @selected($generoSelecionado == $g->id_genero)>{{ $g->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary">Ver ranking</button></div>
    </form>

    @if ($generoSelecionado)
        <table class="table table-striped bg-white">
            <thead><tr><th>#</th><th>Jogo</th><th class="text-center">Nota média</th><th class="text-center">Avaliações</th></tr></thead>
            <tbody>
            @forelse ($ranking as $i => $linha)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><a href="{{ route('jogos.show', $linha->id_jogo) }}">{{ $linha->titulo }}</a></td>
                    <td class="text-center">{{ number_format($linha->nota_media, 2, ',', '.') }}</td>
                    <td class="text-center">{{ $linha->total_avaliacoes }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">Nenhum jogo avaliado neste gênero.</td></tr>
            @endforelse
            </tbody>
        </table>
    @endif
@endsection
