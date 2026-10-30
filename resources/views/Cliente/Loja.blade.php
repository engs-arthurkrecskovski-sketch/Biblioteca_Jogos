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

    @php $carrinhoIds = session('carrinho', []); @endphp

    <div class="row g-3">
        @forelse ($jogos as $jogo)
            @php
                $pago = ($jogo->preco ?? 0) > 0;
                $noCarrinho = in_array($jogo->id_jogo, $carrinhoIds);
            @endphp

            <div class="col-md-6 col-xl-4">
                <div class="card card-jogo h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('cliente.jogo', $jogo->id_jogo) }}" class="titulo-jogo">{{ $jogo->titulo }}</a>
                        </h5>
                        <p class="text-muted mb-2">{{ $jogo->genero->nome ?? '' }}</p>
                        <strong>{{ $pago ? 'R$ ' . number_format($jogo->preco, 2, ',', '.') : 'Grátis' }}</strong>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @if ($pago && $noCarrinho)
                            <a href="{{ route('cliente.carrinho') }}" class="btn btn-sm btn-ghost-dark w-100">
                                <i class="bi bi-cart-check me-1"></i> No carrinho · Ver carrinho
                            </a>
                        @elseif ($pago)
                            <form method="POST" action="{{ route('cliente.carrinho.adicionar', $jogo->id_jogo) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-accent w-100">
                                    <i class="bi bi-cart-plus me-1"></i> Adicionar ao carrinho
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('cliente.comprar', $jogo->id_jogo) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-accent w-100">
                                    <i class="bi bi-bag-plus me-1"></i> Adicionar à biblioteca
                                </button>
                            </form>
                        @endif
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