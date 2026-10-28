<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Biblioteca de Jogos')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">Biblioteca de Jogos</a>
        <div class="navbar-nav">
            <a class="nav-link" href="{{ route('jogos.index') }}">Jogos</a>
            <a class="nav-link" href="{{ route('usuarios.index') }}">Usuários</a>
            <a class="nav-link" href="{{ route('ranking') }}">Ranking</a>
        </div>
    </div>
</nav>


<main class="container py-4">
    @if (session('sucesso'))
        <div class="alert alert-success">{{ session('sucesso') }}</div>
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
</body>
</html>
