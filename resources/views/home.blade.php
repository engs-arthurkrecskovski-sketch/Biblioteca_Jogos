@extends('layouts.app')

@section('titulo', 'Início – Biblioteca de Jogos')

@section('conteudo')

    {{-- Hero --}}
    <section class="hero">
        <h1>Todos os seus jogos num só lugar</h1>
        <p>Explore o catálogo, acompanhe as bibliotecas dos usuários e veja quais jogos têm as melhores avaliações.</p>
        <a href="{{ url('/jogos') }}" class="btn btn-accent me-2"><i class="bi bi-search me-1"></i> Explorar jogos</a>
        <a href="{{ url('/ranking') }}" class="btn btn-ghost"><i class="bi bi-trophy me-1"></i> Ver ranking</a>
    </section>

    {{-- Números --}}
    {{-- Ajuste os nomes das variáveis para os que o seu controller envia --}}
    <div class="row g-3">
        <div class="col-6 col-xl-3">
            <div class="stat stat-teal">
                <div class="stat-icon tone-teal"><i class="bi bi-joystick"></i></div>
                <div>
                    <div class="stat-value">{{ number_format($totalJogos ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Jogos</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat stat-blue">
                <div class="stat-icon tone-blue"><i class="bi bi-people"></i></div>
                <div>
                    <div class="stat-value">{{ number_format($totalUsuarios ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Usuários</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat stat-amber">
                <div class="stat-icon tone-amber"><i class="bi bi-star-fill"></i></div>
                <div>
                    <div class="stat-value">{{ number_format($totalAvaliacoes ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Avaliações</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat stat-rose">
                <div class="stat-icon tone-rose"><i class="bi bi-collection-play"></i></div>
                <div>
                    <div class="stat-value">{{ number_format($totalBiblioteca ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Jogos em bibliotecas</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Atalhos --}}
    <h2 class="section-title">Por onde começar</h2>
    <div class="row g-3">
        <div class="col-md-4">
            <a href="{{ url('/jogos') }}" class="shortcut">
                <div class="stat-icon tone-teal"><i class="bi bi-joystick"></i></div>
                <h3>Catálogo de jogos</h3>
                <p>Busque por título, veja detalhes, notas e avaliações.</p>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ url('/usuarios') }}" class="shortcut">
                <div class="stat-icon tone-blue"><i class="bi bi-person-badge"></i></div>
                <h3>Usuários</h3>
                <p>Consulte a biblioteca, as horas jogadas e a assinatura de cada pessoa.</p>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ url('/ranking') }}" class="shortcut">
                <div class="stat-icon tone-amber"><i class="bi bi-trophy"></i></div>
                <h3>Ranking por gênero</h3>
                <p>Descubra os jogos mais bem avaliados de cada gênero.</p>
            </a>
        </div>
    </div>

@endsection