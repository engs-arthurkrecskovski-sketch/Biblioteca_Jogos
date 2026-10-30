@extends('layouts.cliente')

@section('titulo', 'Pagamento – Save Point')

@section('conteudo')

    <h2 class="section-title">Finalizar compra</h2>

    @php $metodo = old('metodo', 'pix'); @endphp

    <div class="row g-3">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('cliente.pagar', $jogo->id_jogo) }}" id="formPagamento">
                @csrf

                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Forma de pagamento</h5>

                        {{-- PIX --}}
                        <input type="radio" class="btn-check" name="metodo" id="metodoPix" value="pix" @checked($metodo === 'pix')>
                        <label class="btn btn-outline-secondary w-100 text-start mb-2" for="metodoPix">
                            <i class="bi bi-qr-code me-2"></i> Pagar com Pix
                        </label>

                        <div id="painelPix" class="border rounded p-3 mb-3" style="display:none;">
                            <p class="mb-2">Copie o código abaixo e pague no app do seu banco:</p>
                            <textarea id="pixCodigo" class="form-control mb-2" rows="3" readonly>{{ $pixCodigo }}</textarea>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="copiarPix">
                                <i class="bi bi-clipboard me-1"></i> Copiar código
                            </button>
                            <p class="text-muted small mt-3 mb-0">
                                Pagamento de demonstração: ao clicar em "Confirmar pagamento", o jogo é liberado.
                            </p>
                        </div>

                        {{-- CARTÃO --}}
                        <input type="radio" class="btn-check" name="metodo" id="metodoCartao" value="cartao" @checked($metodo === 'cartao')>
                        <label class="btn btn-outline-secondary w-100 text-start" for="metodoCartao">
                            <i class="bi bi-credit-card me-2"></i> Cartão de crédito
                        </label>

                        <div id="painelCartao" class="border rounded p-3 mt-3" style="display:none;">
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label" for="nome_cartao">Nome no cartão</label>
                                    <input type="text" id="nome_cartao" name="nome_cartao" class="form-control"
                                           value="{{ old('nome_cartao') }}" maxlength="60" autocomplete="cc-name"
                                           placeholder="Como está impresso no cartão">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="numero_cartao">Número do cartão</label>
                                    <input type="text" id="numero_cartao" name="numero_cartao" class="form-control"
                                           value="{{ old('numero_cartao') }}" inputmode="numeric" maxlength="23"
                                           autocomplete="cc-number" placeholder="0000 0000 0000 0000">
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="validade">Validade</label>
                                    <input type="text" id="validade" name="validade" class="form-control"
                                           value="{{ old('validade') }}" inputmode="numeric" maxlength="5"
                                           autocomplete="cc-exp" placeholder="MM/AA">
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="cvv">CVV</label>
                                    <input type="password" id="cvv" name="cvv" class="form-control"
                                           inputmode="numeric" maxlength="4" autocomplete="cc-csc" placeholder="123">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-accent">
                        <i class="bi bi-check2-circle me-1"></i> Confirmar pagamento
                    </button>
                    <a href="{{ route('cliente.loja') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>

        {{-- RESUMO --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Resumo</h5>
                    <p class="mb-1">{{ $jogo->titulo }}</p>
                    <p class="text-muted mb-3">{{ $jogo->genero->nome ?? '' }}</p>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Total</span>
                        <strong>R$ {{ number_format($jogo->preco, 2, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const radios = document.querySelectorAll('input[name="metodo"]');
        const painelPix = document.getElementById('painelPix');
        const painelCartao = document.getElementById('painelCartao');

        function atualizarPaineis() {
            const metodo = document.querySelector('input[name="metodo"]:checked')?.value;
            painelPix.style.display = metodo === 'pix' ? 'block' : 'none';
            painelCartao.style.display = metodo === 'cartao' ? 'block' : 'none';
        }
        radios.forEach(r => r.addEventListener('change', atualizarPaineis));
        atualizarPaineis();

        document.getElementById('numero_cartao').addEventListener('input', e => {
            const digitos = e.target.value.replace(/\D/g, '').slice(0, 19);
            e.target.value = digitos.replace(/(.{4})/g, '$1 ').trim();
        });

        document.getElementById('validade').addEventListener('input', e => {
            let v = e.target.value.replace(/\D/g, '').slice(0, 4);
            if (v.length > 2) v = v.slice(0, 2) + '/' + v.slice(2);
            e.target.value = v;
        });

        document.getElementById('cvv').addEventListener('input', e => {
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        document.getElementById('copiarPix').addEventListener('click', () => {
            const campo = document.getElementById('pixCodigo');
            campo.select();
            navigator.clipboard.writeText(campo.value);
        });
    </script>

@endsection