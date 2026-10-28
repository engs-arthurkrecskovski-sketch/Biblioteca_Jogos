@csrf
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Título</label>
        <input type="text" name="titulo" class="form-control" maxlength="150" required
               value="{{ old('titulo', $jogo->titulo) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Preço (R$)</label>
        <input type="number" step="0.01" min="0" name="preco" class="form-control" required
               value="{{ old('preco', $jogo->preco) }}">
    </div>
    <div class="col-12">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" rows="3" class="form-control">{{ old('descricao', $jogo->descricao) }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label">Data de lançamento</label>
        <input type="date" name="data_lancamento" class="form-control"
               value="{{ old('data_lancamento', $jogo->data_lancamento?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Gênero</label>
        <select name="id_genero" class="form-select">
            <option value="">-- selecione --</option>
            @foreach ($generos as $g)
                <option value="{{ $g->id_genero }}" @selected(old('id_genero', $jogo->id_genero) == $g->id_genero)>{{ $g->nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Desenvolvedora</label>
        <select name="id_desenvolvedora" class="form-select">
            <option value="">-- selecione --</option>
            @foreach ($desenvolvedoras as $d)
                <option value="{{ $d->id_desenvolvedora }}" @selected(old('id_desenvolvedora', $jogo->id_desenvolvedora) == $d->id_desenvolvedora)>{{ $d->nome }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="mt-4">
    <button class="btn btn-primary">Salvar</button>
    <a href="{{ route('jogos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
