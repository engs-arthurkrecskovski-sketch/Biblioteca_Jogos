@extends('layouts.app')
@section('titulo', $jogo->titulo)

@section('conteudo')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="h3 mb-1">{{ $jogo->titulo }}</h1>
            <div class="text-muted">
                {{ $jogo->genero?->nome ?? 'Sem gênero' }} · {{ $jogo->desenvolvedora?->nome ?? 'Sem desenvolvedora' }}
            </div>
        </div>
        <div>
            <a href="{{ route('jogos.edit', $jogo) }}" class="btn btn-outline-primary">Editar</a>
            <a href="{{ route('jogos.index') }}" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card card-body">
                <p>{{ $jogo->descricao ?? 'Sem descrição.' }}</p>
                <div><strong>Preço:</strong> R$ {{ number_format($jogo->preco, 2, ',', '.') }}</div>
                <div><strong>Lançamento:</strong> {{ $jogo->data_lancamento?->format('d/m/Y') ?? '-' }}</div>
                <div>
                    <strong>Plataformas:</strong>
                    @forelse ($jogo->plataformas as $p)
                        <span class="badge text-bg-secondary">{{ $p->nome }}</span>
                    @empty
                        -
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-body text-center">
                <div class="text-muted">Nota média (calculada pelo trigger)</div>
                <div class="fs-2">{{ $jogo->nota_media !== null ? number_format($jogo->nota_media, 2, ',', '.') : '-' }}</div>
                <div class="text-muted">{{ $jogo->total_avaliacoes }} avaliação(ões)</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">Avaliar este jogo</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('avaliacoes.store') }}">
                        @csrf
                        <input type="hidden" name="id_jogo" value="{{ $jogo->id_jogo }}">
                        <div class="mb-2">
                            <label class="form-label">ID do usuário</label>
                            <input type="number" name="id_usuario" class="form-control" min="1" required value="{{ old('id_usuario') }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Nota (1 a 5)</label>
                            <select name="nota" class="form-select" required>
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" @selected(old('nota') == $i)>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Comentário</label>
                            <textarea name="comentario" rows="2" class="form-control">{{ old('comentario') }}</textarea>
                        </div>
                        <button class="btn btn-success">Enviar avaliação</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">Últimas avaliações</div>
                <ul class="list-group list-group-flush">
                    @forelse ($avaliacoes as $a)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $a->usuario?->nome ?? 'Usuário #'.$a->id_usuario }}</strong>
                                <span class="badge text-bg-warning">{{ $a->nota }}/5</span>
                                <div class="text-muted small">{{ $a->comentario }}</div>
                            </div>
                            <form action="{{ route('avaliacoes.destroy', $a) }}" method="POST"
                                  onsubmit="return confirm('Excluir esta avaliação?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Excluir</button>
                            </form>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Nenhuma avaliação ainda.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
