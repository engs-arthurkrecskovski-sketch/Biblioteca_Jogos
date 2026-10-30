<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Biblioteca de Jogos')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/biblioteca.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

    {{-- Barra superior (só aparece no celular) --}}
    <div class="topbar-mobile">
        <button type="button" id="menuToggle" aria-label="Abrir menu"><i class="bi bi-list"></i></button>
        <strong class="brand-name">Save Point - Painel Administrativo</strong>
    </div>

    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        <a href="{{ url('/') }}" class="brand">
            <span class="brand-icon"><i class="bi bi-controller"></i></span>
            <span class="brand-name">Save Point<small>Painel Administrativo</small></span>
        </a>

        <div class="side-label">Menu</div>
        <ul class="side-nav">
            <li>
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i> Início
                </a>
            </li>
            <li>
                <a href="{{ url('/jogos') }}" class="{{ request()->is('jogos*') ? 'active' : '' }}">
                    <i class="bi bi-joystick"></i> Jogos
                </a>
            </li>
            <li>
                <a href="{{ url('/usuarios') }}" class="{{ request()->is('usuarios*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Usuários
                </a>
            </li>
            <li>
                <a href="{{ url('/ranking') }}" class="{{ request()->is('ranking*') ? 'active' : '' }}">
                    <i class="bi bi-trophy"></i> Ranking
                </a>
            </li>
        </ul>

        <div class="side-footer">Engenharia de Software<br>Centro Universitário Campo Real</div>
    </aside>

    {{-- Conteúdo --}}
    <div class="main">
        <main class="content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('conteudo')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        document.getElementById('menuToggle').addEventListener('click', e => {
            e.stopPropagation();
            sidebar.classList.toggle('open');
        });
        document.addEventListener('click', e => {
            if (!sidebar.contains(e.target)) sidebar.classList.remove('open');
        });
    </script>
    @stack('scripts')
</body>
</html>