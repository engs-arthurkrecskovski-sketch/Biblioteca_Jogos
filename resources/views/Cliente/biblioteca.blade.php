@extends('layouts.cliente')

@section('titulo', 'Minha biblioteca – Save Point')

@section('conteudo')

    <h2 class="section-title">Minha biblioteca</h2>

    <div class="row g-3">
        @forelse ($itens as $item)
            <div class="col-md-6 col-xl-4">
                <div class="card card-jogo h-100">
                    <div class="card-body">
                        <h5 class="card-title">
    <a href="{{ route('cliente.jogo', $item->id_jogo) }}" class="titulo-jogo">{{ $item->jogo->titulo }}</a>
</h5>
                        <p class="text-muted mb-2">{{ $item->jogo->genero->nome ?? '' }}</p>
                        <p class="mb-0">
                            <i class="bi bi-clock-history me-1"></i>
                            {{ number_format($item->horas_jogadas ?? 0, 0, ',', '.') }} h jogadas
                        </p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('cliente.avaliar') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-star me-1"></i> Avaliar
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light">
                    Sua biblioteca está vazia.
                    <a href="{{ route('cliente.loja') }}">Veja os jogos disponíveis</a>.
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $itens->links() }}</div>

@endsection