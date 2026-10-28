@extends('layouts.app')
@section('titulo', 'Início')

@section('conteudo')
    <h1 class="h3 mb-4">Biblioteca Digital de Jogos Eletrônicos</h1>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card text-center"><div class="card-body">
            <div class="fs-3">{{ number_format($totalJogos, 0, ',', '.') }}</div><div class="text-muted">Jogos</div>
        </div></div></div>
        <div class="col-md-3"><div class="card text-center"><div class="card-body">
            <div class="fs-3">{{ number_format($totalUsuarios, 0, ',', '.') }}</div><div class="text-muted">Usuários</div>
        </div></div></div>
        <div class="col-md-3"><div class="card text-center"><div class="card-body">
            <div class="fs-3">{{ number_format($totalAvaliacoes, 0, ',', '.') }}</div><div class="text-muted">Avaliações</div>
        </div></div></div>
        <div class="col-md-3"><div class="card text-center"><div class="card-body">
            <div class="fs-3">{{ number_format($totalBiblioteca, 0, ',', '.') }}</div><div class="text-muted">Jogos em bibliotecas</div>
        </div></div></div>
    </div>

    <div class="card">
        <div class="card-header">Recursos do PostgreSQL usados pela aplicação</div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Trigger</strong> trg_atualiza_nota_jogo: recalcula a nota média ao avaliar um jogo (tela do jogo).</li>
            <li class="list-group-item"><strong>Trigger</strong> trg_log_biblioteca + <strong>procedure</strong> sp_adicionar_jogo_biblioteca (tela do usuário).</li>
            <li class="list-group-item"><strong>Procedure</strong> sp_renovar_assinatura (tela do usuário).</li>
            <li class="list-group-item"><strong>Function</strong> fn_total_horas_usuario (tela do usuário) e fn_ranking_jogos_genero (tela de ranking).</li>
        </ul>
    </div>
@endsection