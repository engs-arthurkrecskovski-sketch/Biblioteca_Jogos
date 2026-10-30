@extends('layouts.app')
@section('titulo', $usuario->nome)

@section('conteudo')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="h3 mb-1">{{ $usuario->nome }}</h1>
            <div class="text-muted">{{ $usuario->email }}</div>
        </div>
        <div>
            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-outline-primary">Editar</a>
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-body text-center">
                <div class="text-muted">Horas jogadas</div>
                <div class="fs-2">{{ number_format($totalHoras, 1, ',', '.') }} h</div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Assinatura</div>
                <div class="card-body">
                    @if ($usuario->assinatura)
                        <div><strong>Plano:</strong> {{ $usuario->assinatura->plano }}</div>
                        <div><strong>Início:</strong> {{ $usuario->assinatura->data_inicio?->format('d/m/Y') }}</div>
                        <div class="mb-3"><strong>Expira em:</strong> {{ $usuario->assinatura->data_expiracao?->format('d/m/Y') ?? 'sem expiração' }}</div>
                    @else
                        <p class="text-muted">Sem assinatura.</p>
                    @endif

                    <form method="POST" action="{{ route('usuarios.assinatura.renovar', $usuario) }}" class="row g-2">
                        @csrf
                        <div class="col-auto">
                            <select name="plano" class="form-select">
                                <option value="gratuito">gratuito</option>
                                <option value="premium">premium (30 dias)</option>
                                <option value="premium_anual">premium_anual (365 dias)</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-success">Alterar plano</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">Biblioteca (20 jogos mais jogados)</div>
                <table class="table table-sm mb-0">
                    <thead><tr><th>Jogo</th><th class="text-end">Horas</th></tr></thead>
                    <tbody>
                    @forelse ($biblioteca as $item)
                        <tr>
                            <td><a href="{{ route('jogos.show', $item->id_jogo) }}">{{ $item->jogo?->titulo ?? 'Jogo #'.$item->id_jogo }}</a></td>
                            <td class="text-end">{{ number_format($item->horas_jogadas, 1, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-muted">Biblioteca vazia.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card mb-3">
                <div class="card-header">Adicionar jogo à biblioteca</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('usuarios.biblioteca.store', $usuario) }}" class="row g-2">
                        @csrf
                        <div class="col">
                            <input type="number" name="id_jogo" min="1" class="form-control" placeholder="ID do jogo" required value="{{ old('id_jogo') }}">
                        </div>
                        <div class="col-auto"><button class="btn btn-success">Adicionar</button></div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Últimos registros do log</div>
                <ul class="list-group list-group-flush">
                    @forelse ($logs as $log)
                        <li class="list-group-item small">
                            {{ $log->acao }} · jogo #{{ $log->id_jogo }} · {{ $log->data_acao?->format('d/m/Y H:i') }}
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">Sem registros.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
