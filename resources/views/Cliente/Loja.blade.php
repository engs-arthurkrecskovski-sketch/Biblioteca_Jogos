@extends('layouts.cliente')

@section('titulo', 'Jogos disponíveis – Save Point')

@section('conteudo')

    <h2 class="section-title">Jogos disponíveis</h2>

    <form method="GET" action="{{ route('cliente.loja') }}" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar por título">
        </div>
        <div class="col-auto">
            <button class="btn btn-accent"><i class="bi bi-search me-1"></i> Buscar</button>
        </div>
    </form>

    <div class="row g-3">
        @forelse ($jogos as $jogo)
            @php $pago = ($jogo->preco ?? 0) > 0; @endphp

            <div class="col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $jogo->titulo }}</h5>
                        <p class="text-muted mb-2">{{ $jogo->genero->nome ?? '' }}</p>
                        <strong>{{ $pago ? 'R$ ' . number_format($jogo->preco, 2, ',', '.') : 'Grátis' }}</strong>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <form method="POST" action="{{ route('cliente.comprar', $jogo->id_jogo) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-accent w-100">
                                @if ($pago)
                                    <i class="bi bi-cart-check me-1"></i> Comprar
                                @else
                                    <i class="bi bi-bag-plus me-1"></i> Adicionar à biblioteca
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light">Nenhum jogo encontrado.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $jogos->links() }}</div>

@endsection