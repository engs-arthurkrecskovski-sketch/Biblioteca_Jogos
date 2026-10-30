@extends('layouts.app')
@section('titulo', 'Jogos')

@section('conteudo')
    <div class="page-head">
        <div>
            <h1 class="page-title">Jogos</h1>
            <p class="page-sub">{{ number_format($jogos->total(), 0, ',', '.') }} jogos no catálogo</p>
        </div>
        <a href="{{ route('jogos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Novo jogo
        </a>
    </div>

    <form method="GET" action="{{ route('jogos.index') }}" class="toolbar">
        <div class="search">
            <i class="bi bi-search"></i>
            <input type="text" name="busca" value="{{ request('busca') }}" class="form-control" placeholder="Buscar pelo título">
        </div>
        <button class="btn btn-primary">Buscar</button>
        @if (request('busca'))
            <a href="{{ route('jogos.index') }}" class="btn btn-outline-secondary">Limpar</a>
        @endif
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Jogo</th>
                <th>Gênero</th>
                <th class="text-end">Preço</th>
                <th class="text-center">Nota</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($jogos as $jogo)
                @php
                    $nota = $jogo->nota_media;
                    $classeNota = $nota === null ? 'none' : ($nota >= 4 ? 'good' : ($nota >= 3 ? 'mid' : 'low'));
                    $preco = (float) $jogo->preco;
                @endphp
                <tr>
                    <td class="cell-id">{{ $jogo->id_jogo }}</td>
                    <td>
                        <a href="{{ route('jogos.show', $jogo) }}" class="game-title">{{ $jogo->titulo }}</a>
                        <span class="game-dev">{{ $jogo->desenvolvedora?->nome ?? '-' }}</span>
                    </td>
                    <td><span class="genre">{{ $jogo->genero?->nome ?? '-' }}</span></td>
                    <td class="text-end price {{ $preco == 0 ? 'free' : '' }}">
                        {{ $preco == 0 ? 'Grátis' : 'R$ ' . number_format($preco, 2, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <span class="nota {{ $classeNota }}">
                            @if ($nota !== null)
                                <i class="bi bi-star-fill"></i> {{ number_format($nota, 2, ',', '.') }}
                            @else
                                Sem nota
                            @endif
                        </span>
                        <span class="nota-count">({{ $jogo->total_avaliacoes }})</span>
                    </td>
                    <td class="text-end">
                        <div class="actions">
                            <a href="{{ route('jogos.edit', $jogo) }}" class="icon-btn" title="Editar" aria-label="Editar {{ $jogo->titulo }}">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('jogos.destroy', $jogo) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Excluir este jogo?')">
                                @csrf @method('DELETE')
                                <button class="icon-btn danger" title="Excluir" aria-label="Excluir {{ $jogo->titulo }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty"><i class="bi bi-joystick"></i>Nenhum jogo encontrado.</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="pager">{{ $jogos->links() }}</div>
@endsection