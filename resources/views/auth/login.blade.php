<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar - Save Point</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #e8ecf1; min-height: 100vh;
               display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .login-card { width: 100%; max-width: 420px; background: #fff; border-radius: 20px;
                      box-shadow: 0 8px 30px rgba(19,34,46,.12); overflow: hidden; }
        .login-head { background: #13222e; color: #fff; padding: 2rem; text-align: center; }
        .login-head .icon { width: 56px; height: 56px; border-radius: 16px; background: #ff8a4c;
                            display: inline-flex; align-items: center; justify-content: center;
                            font-size: 1.6rem; margin-bottom: .75rem; }
        .login-head h1 { font-family: 'Sora', sans-serif; font-size: 1.5rem; margin: 0; }
        .login-head p { margin: .25rem 0 0; opacity: .75; font-size: .95rem; }
        .login-body { padding: 2rem; }
        .btn-save { background: #ff8a4c; border: 0; color: #fff; font-weight: 700; padding: .7rem; border-radius: 12px; }
        .btn-save:hover { background: #f27632; color: #fff; }
        .form-control { padding: .7rem .9rem; border-radius: 12px; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-head">
            <span class="icon"><i class="bi bi-controller"></i></span>
            <h1>Save Point</h1>
            <p>Biblioteca de jogos</p>
        </div>

        <div class="login-body">
            @if ($errors->any())
                <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.entrar') }}">
                {{-- @csrf gera o token que protege o formulário contra CSRF --}}
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="form-control" required autofocus>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-save w-100">Entrar</button>
            </form>
        </div>
    </div>
</body>
</html>