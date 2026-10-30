@extends('layouts.cliente')

@section('titulo', 'Avaliar jogos – Save Point')

@section('conteudo')

    <h2 class="section-title">Avaliar jogos</h2>

    @forelse ($itens as $item)
        @php $minha = $minhasNotas[$item->id_jogo] ?? null; @endphp

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    {{ $item->jogo->titulo }}
                    @if ($minha)
                        <span class="badge text-bg-success ms-2">Avaliado</span>
                    @endif
                </h5>

                <form method="POST" action="{{ route('cliente.avaliar.salvar', $item->id_jogo) }}" class="row g-2">
                    @csrf
                    <div class="col-md-2">
                        <select name="nota" class="form-select" required>
                            <option value="">Nota</option>
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" @selected(($minha->nota ?? null) == $i)>{{ $i }} ★</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-8">
                        <input type="text" name="comentario" class="form-control"
                               placeholder="Comentário (opcional)" maxlength="1000"
                               value="{{ $minha->comentario ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-accent w-100">{{ $minha ? 'Atualizar' : 'Enviar' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="alert alert-light">
            Você ainda não tem jogos para avaliar.
            <a href="{{ route('cliente.loja') }}">Veja os jogos disponíveis</a>.
        </div>
    @endforelse

    <div class="mt-3">{{ $itens->links() }}</div>

@endsection