@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nome</label>
        <input type="text" name="nome" class="form-control" maxlength="150" required value="{{ old('nome', $usuario->nome) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">E-mail</label>
        <input type="email" name="email" class="form-control" maxlength="150" required value="{{ old('email', $usuario->email) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Senha @if($usuario->exists)<small class="text-muted">(deixe em branco para manter)</small>@endif</label>
        <input type="password" name="senha" class="form-control" @if(!$usuario->exists) required @endif minlength="6">
    </div>
</div>
<div class="mt-4">
    <button class="btn btn-primary">Salvar</button>
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
