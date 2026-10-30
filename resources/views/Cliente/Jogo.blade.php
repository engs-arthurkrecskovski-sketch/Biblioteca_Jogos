@extends('layouts.cliente')

@section('titulo', $jogo->titulo . ' – Save Point')

@section('conteudo')

    @php
        $pago = ($jogo->preco ?? 0) > 0;

        // Desenvolvedora: aceita coluna de texto ou relação com "nome"
        $dev = $jogo->desenvolvedora ?? null;
        $dev = is_object($dev) ? ($dev->nome ?? '') : $dev;

        // Data de lançamento
        $lancamento = $jogo->data_lancamento ?? null;
        if ($lancamento && !($lancamento instanceof \Carbon\CarbonInterface)) {
            try { $lancamento = \Carbon\Carbon::parse($lancamento); } catch (\Throwable $e) { $lancamento = null; }
        }

        // Plataformas (se a relação existir)
        $plataformas = $jogo->plataformas ?? collect();
    @endphp

    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
        <div>
            <h2 class="section-title mb-1">{{ $jogo->titulo }}</h2>
            <div class="text-muted">
                {{ $jogo->genero->nome ?? '' }}@if ($dev) · {{ $dev }}@endif
            </div>
        </div>
        <a href="{{ url()->previous() === url()->current() ? route('cliente.loja') : url()->previous() }}"
           class="btn btn-outline-secondary">Voltar</a>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="card card-jogo h-100">
                <div class="card-body">
                    @if (!empty($jogo->descricao))
                        <p>{{ $jogo->descricao }}</p>
                    @endif

                    <p class="mb-1">
                        <strong>Preço:</strong>
                        {{ $pago ? 'R$ ' . number_format($jogo->preco, 2, ',', '.') : 'Grátis' }}
                    </p>

                    @if ($lancamento)
                        <p class="mb-1"><strong>Lançamento:</strong> {{ $lancamento->format('d/m/Y') }}</p>
                    @endif

                    @if (is_iterable($plataformas) && count($plataformas))
                        <p class="mb-1"><strong>Plataformas:</strong></p>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach ($plataformas as $plataforma)
                                <span class="badge text-bg-secondary">{{ $plataforma->nome ?? '' }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-jogo h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <div class="text-muted">Nota média</div>
                    <div class="display-5 fw-bold">
                        {{ $notaMedia ? number_format($notaMedia, 2, ',', '.') : '–' }}
                    </div>
                    <div class="text-muted mb-3">{{ $totalAvaliacoes }} avaliação(ões)</div>

                    @if ($naBiblioteca)
                        <a href="{{ route('cliente.avaliar') }}" class="btn btn-accent">
                            <i class="bi bi-star me-1"></i> Avaliar este jogo
                        </a>
                        <small class="text-muted mt-2">Este jogo já está na sua biblioteca.</small>
                    @elseif ($pago)
                        <a href="{{ route('cliente.checkout', $jogo->id_jogo) }}" class="btn btn-accent">
                            <i class="bi bi-cart-check me-1"></i> Comprar
                        </a>
                    @else
                        <form method="POST" action="{{ route('cliente.comprar', $jogo->id_jogo) }}">
                            @csrf
                            <button type="submit" class="btn btn-accent w-100">
                                <i class="bi bi-bag-plus me-1"></i> Adicionar à biblioteca
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card card-jogo">
        <div class="card-body">
            <h5 class="card-title mb-3">Últimas avaliações</h5>

            @forelse ($avaliacoes as $av)
                <div class="py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <strong>{{ $nomes[$av->id_usuario] ?? 'Usuário' }}</strong>
                    <span class="badge text-bg-warning ms-1">{{ $av->nota }}/5</span>
                    @if (!empty($av->comentario))
                        <div class="text-muted">{{ $av->comentario }}</div>
                    @endif
                </div>
            @empty
                <p class="text-muted mb-0">Este jogo ainda não tem avaliações.</p>
            @endforelse
        </div>
    </div>

@endsection