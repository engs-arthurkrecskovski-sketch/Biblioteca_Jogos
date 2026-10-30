@extends('layouts.cliente')

@section('titulo', 'Início – Save Point')

@section('conteudo')

    <section class="hero">
        <h1>Olá, {{ $usuario->nome }}</h1>
        <p>Acompanhe sua biblioteca, descubra novos jogos e conte o que achou dos que você já jogou.</p>
        <a href="{{ route('cliente.loja') }}" class="btn btn-accent me-2"><i class="bi bi-bag me-1"></i> Ver jogos disponíveis</a>
        <a href="{{ route('cliente.biblioteca') }}" class="btn btn-ghost"><i class="bi bi-collection-play me-1"></i> Minha biblioteca</a>
    </section>

    <div class="row g-3">
        <div class="col-6 col-xl-4">
            <div class="stat stat-teal">
                <div class="stat-icon tone-teal"><i class="bi bi-joystick"></i></div>
                <div>
                    <div class="stat-value">{{ number_format($totalJogos, 0, ',', '.') }}</div>
                    <div class="stat-label">Jogos na biblioteca</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-4">
            <div class="stat stat-blue">
                <div class="stat-icon tone-blue"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="stat-value">{{ number_format($totalHoras, 0, ',', '.') }}</div>
                    <div class="stat-label">Horas jogadas</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="stat stat-amber">
                <div class="stat-icon tone-amber"><i class="bi bi-star-fill"></i></div>
                <div>
                    <div class="stat-value">{{ number_format($totalAvaliacoes, 0, ',', '.') }}</div>
                    <div class="stat-label">Avaliações feitas</div>
                </div>
            </div>
        </div>
    </div>

    <h2 class="section-title">Por onde começar</h2>
    <div class="row g-3">
        <div class="col-md-4">
            <a href="{{ route('cliente.biblioteca') }}" class="shortcut">
                <div class="stat-icon tone-teal"><i class="bi bi-collection-play"></i></div>
                <h3>Minha biblioteca</h3>
                <p>Veja os jogos que você tem e as horas jogadas em cada um.</p>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('cliente.loja') }}" class="shortcut">
                <div class="stat-icon tone-blue"><i class="bi bi-bag"></i></div>
                <h3>Jogos disponíveis</h3>
                <p>Busque no catálogo e adicione novos jogos à sua biblioteca.</p>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('cliente.avaliar') }}" class="shortcut">
                <div class="stat-icon tone-amber"><i class="bi bi-star"></i></div>
                <h3>Avaliar jogos</h3>
                <p>Dê uma nota e deixe um comentário sobre o que você jogou.</p>
            </a>
        </div>
    </div>

@endsection