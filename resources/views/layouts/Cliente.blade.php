<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Save Point')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/biblioteca.css') }}" rel="stylesheet">
</head>
<body>

    <div class="topbar-mobile">
        <button type="button" id="menuToggle" aria-label="Abrir menu"><i class="bi bi-list"></i></button>
        <strong class="brand-name">Save Point</strong>
    </div>

    <aside class="sidebar" id="sidebar">
        <a href="{{ route('cliente.inicio') }}" class="brand">
            <span class="brand-icon"><i class="bi bi-controller"></i></span>
            <span class="brand-name">Save Point<small>Minha conta</small></span>
        </a>

        <div class="side-label">Menu</div>
        <ul class="side-nav">
            <li>
                <a href="{{ route('cliente.inicio') }}" class="{{ request()->routeIs('cliente.inicio') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i> Início
                </a>
            </li>
            <li>
                <a href="{{ route('cliente.biblioteca') }}" class="{{ request()->routeIs('cliente.biblioteca') ? 'active' : '' }}">
                    <i class="bi bi-collection-play"></i> Minha biblioteca
                </a>
            </li>
            <li>
                <a href="{{ route('cliente.loja') }}" class="{{ request()->routeIs('cliente.loja') ? 'active' : '' }}">
                    <i class="bi bi-bag"></i> Jogos disponíveis
                </a>
            </li>
            <li>
                <a href="{{ route('cliente.avaliar') }}" class="{{ request()->routeIs('cliente.avaliar*') ? 'active' : '' }}">
                    <i class="bi bi-star"></i> Avaliar
                </a>
            </li>
        </ul>

        <form method="POST" action="{{ route('logout') }}" class="px-3 mt-3">
            @csrf
            <button type="submit" class="btn btn-sm w-100 text-start side-logout">
                <i class="bi bi-box-arrow-left"></i> Sair
            </button>
        </form>

        <div class="side-footer"><br></div>
    </aside>

    <div class="main">
        <main class="content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
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
</body>
</html>