@extends('layouts.cliente')

@section('titulo', 'Carrinho – Save Point')

@section('conteudo')

    <h2 class="section-title">Carrinho</h2>

    @if ($itens->isEmpty())
        <div class="card card-jogo">
            <div class="card-body text-center py-5">
                <i class="bi bi-cart3 display-4 text-muted"></i>
                <p class="mt-3 mb-3 text-muted">Seu carrinho está vazio.</p>
                <a href="{{ route('cliente.loja') }}" class="btn btn-accent">
                    <i class="bi bi-bag me-1"></i> Ver jogos disponíveis
                </a>
            </div>
        </div>
    @else
        <form method="POST" action="{{ route('cliente.carrinho.pagar') }}" id="formPagamento">
            @csrf

            <div class="row g-3">
                {{-- Pagamento --}}
                <div class="col-lg-8">
                    <div class="card card-jogo mb-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Jogos no carrinho</h5>

                            @foreach ($itens as $jogo)
                                <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div>
                                        <a href="{{ route('cliente.jogo', $jogo->id_jogo) }}" class="titulo-jogo">{{ $jogo->titulo }}</a>
                                        <div class="text-muted small">{{ $jogo->genero->nome ?? '' }}</div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <strong>R$ {{ number_format($jogo->preco, 2, ',', '.') }}</strong>
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                formaction="{{ route('cliente.carrinho.remover', $jogo->id_jogo) }}"
                                                formnovalidate
                                                title="Remover do carrinho"
                                                aria-label="Remover {{ $jogo->titulo }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card card-jogo">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Forma de pagamento</h5>

                            @php $metodo = old('metodo', 'pix'); @endphp

                            {{-- Pix --}}
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="metodo" id="metodoPix" value="pix" {{ $metodo === 'pix' ? 'checked' : '' }}>
                                <label class="form-check-label" for="metodoPix"><i class="bi bi-qr-code me-1"></i> Pagar com Pix</label>
                            </div>
                            <div id="boxPix" class="border rounded p-3 mb-3">
                                <p class="mb-2">Copie o código abaixo e pague no app do seu banco:</p>
                                <textarea id="pixCodigo" class="form-control mb-2" rows="3" readonly>{{ $pixCodigo }}</textarea>
                                <button type="button" id="btnCopiar" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-clipboard me-1"></i> Copiar código
                                </button>
                                <p class="text-muted small mt-3 mb-0">Pagamento de demonstração: ao clicar em "Confirmar pagamento", os jogos são liberados.</p>
                            </div>

                            {{-- Cartão --}}
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="metodo" id="metodoCartao" value="cartao" {{ $metodo === 'cartao' ? 'checked' : '' }}>
                                <label class="form-check-label" for="metodoCartao"><i class="bi bi-credit-card me-1"></i> Cartão de crédito</label>
                            </div>
                            <div id="boxCartao" class="border rounded p-3">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label" for="nome_cartao">Nome impresso no cartão</label>
                                        <input type="text" class="form-control" id="nome_cartao" name="nome_cartao" value="{{ old('nome_cartao') }}" maxlength="60" autocomplete="cc-name">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="numero_cartao">Número do cartão</label>
                                        <input type="text" class="form-control" id="numero_cartao" name="numero_cartao" value="{{ old('numero_cartao') }}" inputmode="numeric" maxlength="23" autocomplete="cc-number">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="validade">Validade (MM/AA)</label>
                                        <input type="text" class="form-control" id="validade" name="validade" value="{{ old('validade') }}" placeholder="MM/AA" maxlength="5" autocomplete="cc-exp">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="cvv">CVV</label>
                                        <input type="text" class="form-control" id="cvv" name="cvv" inputmode="numeric" maxlength="4" autocomplete="cc-csc">
                                    </div>
                                </div>
                                <p class="text-muted small mt-3 mb-0">Pagamento de demonstração: nenhum dado do cartão é guardado.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Resumo --}}
                <div class="col-lg-4">
                    <div class="card card-jogo">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Resumo</h5>

                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Jogos</span>
                                <span>{{ $itens->count() }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Total</span>
                                <strong>R$ {{ number_format($total, 2, ',', '.') }}</strong>
                            </div>

                            <button type="submit" class="btn btn-accent w-100 mb-2">
                                <i class="bi bi-check2-circle me-1"></i> Confirmar pagamento
                            </button>
                            <a href="{{ route('cliente.loja') }}" class="btn btn-outline-secondary w-100">Continuar comprando</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <script>
            (function () {
                const pix = document.getElementById('metodoPix');
                const cartao = document.getElementById('metodoCartao');
                const boxPix = document.getElementById('boxPix');
                const boxCartao = document.getElementById('boxCartao');

                function atualizar() {
                    boxPix.style.display = pix.checked ? 'block' : 'none';
                    boxCartao.style.display = cartao.checked ? 'block' : 'none';
                }
                pix.addEventListener('change', atualizar);
                cartao.addEventListener('change', atualizar);
                atualizar();

                document.getElementById('btnCopiar').addEventListener('click', function () {
                    const campo = document.getElementById('pixCodigo');
                    campo.select();
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(campo.value);
                    } else {
                        document.execCommand('copy');
                    }
                    this.innerHTML = '<i class="bi bi-check2 me-1"></i> Copiado';
                });

                const validade = document.getElementById('validade');
                validade.addEventListener('input', function () {
                    let v = this.value.replace(/\D/g, '').slice(0, 4);
                    if (v.length > 2) v = v.slice(0, 2) + '/' + v.slice(2);
                    this.value = v;
                });
            })();
        </script>
    @endif

@endsection