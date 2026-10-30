<?php

namespace App\Http\Controllers;

use App\Models\Avaliacao;
use App\Models\BibliotecaItem;
use App\Models\Jogo;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClienteController extends Controller
{
    private function usuario()
    {
        return Auth::guard('web')->user();
    }

    private function jogoJaNaBiblioteca(Jogo $jogo): bool
    {
        return $this->usuario()->biblioteca()->where('id_jogo', $jogo->id_jogo)->exists();
    }

    /**
     * Jogos que estão no carrinho (guardado na sessão).
     * Remove sozinho o que já está na biblioteca ou deixou de ser pago.
     */
    private function itensCarrinho()
    {
        $ids = session('carrinho', []);

        if (empty($ids)) {
            return collect();
        }

        $meus = $this->usuario()->biblioteca()->pluck('id_jogo');

        $jogos = Jogo::with('genero')
            ->whereIn('id_jogo', $ids)
            ->whereNotIn('id_jogo', $meus)
            ->where('preco', '>', 0)
            ->get();

        session(['carrinho' => $jogos->pluck('id_jogo')->all()]);

        return $jogos;
    }

    public function inicio()
    {
        $usuario = $this->usuario()->load('assinatura');

        return view('cliente.inicio', [
            'usuario'         => $usuario,
            'totalJogos'      => $usuario->biblioteca()->count(),
            'totalHoras'      => $usuario->biblioteca()->sum('horas_jogadas'),
            'totalAvaliacoes' => $usuario->avaliacoes()->count(),
        ]);
    }

    public function biblioteca()
    {
        $itens = $this->usuario()
            ->biblioteca()
            ->with('jogo.genero')
            ->paginate(12);

        return view('cliente.biblioteca', compact('itens'));
    }

    public function loja(Request $request)
    {
        $meus = $this->usuario()->biblioteca()->pluck('id_jogo');

        $jogos = Jogo::with('genero')
            ->whereNotIn('id_jogo', $meus)
            ->when($request->q, fn ($q, $busca) => $q->where('titulo', 'like', "%{$busca}%"))
            ->orderBy('titulo')
            ->paginate(12)
            ->withQueryString();

        return view('cliente.loja', compact('jogos'));
    }


    public function jogo(Jogo $jogo)
    {
        $jogo->load('genero');

        $naBiblioteca = $this->jogoJaNaBiblioteca($jogo);

        $notaMedia = Avaliacao::where('id_jogo', $jogo->id_jogo)->avg('nota');
        $totalAvaliacoes = Avaliacao::where('id_jogo', $jogo->id_jogo)->count();

        $avaliacoes = Avaliacao::where('id_jogo', $jogo->id_jogo)
            ->limit(10)
            ->get();

        $nomes = Usuario::whereIn('id_usuario', $avaliacoes->pluck('id_usuario'))
            ->pluck('nome', 'id_usuario');

        return view('cliente.jogo', compact(
            'jogo', 'naBiblioteca', 'notaMedia', 'totalAvaliacoes', 'avaliacoes', 'nomes'
        ));
    }

    /**
     * Jogo grátis vai direto pra biblioteca.
     * Jogo pago vai pro carrinho.
     */
    public function comprar(Jogo $jogo): RedirectResponse
    {
        if (($jogo->preco ?? 0) > 0) {
            return $this->adicionarCarrinho($jogo);
        }

        BibliotecaItem::firstOrCreate(
            [
                'id_usuario' => $this->usuario()->id_usuario,
                'id_jogo'    => $jogo->id_jogo,
            ],
            [
                'horas_jogadas'  => 0,
                'data_aquisicao' => now(),
            ]
        );

        return redirect()->route('cliente.biblioteca')
            ->with('success', "{$jogo->titulo} foi adicionado à sua biblioteca.");
    }

    public function carrinho()
    {
        $itens = $this->itensCarrinho();
        $total = $itens->sum('preco');

        $pixCodigo = '00020126580014BR.GOV.BCB.PIX0136' . Str::uuid()
            . '5204000053039865406' . number_format($total, 2, '', '')
            . '5802BR5909SAVEPOINT6009SAO PAULO6304ABCD';

        return view('cliente.carrinho', compact('itens', 'total', 'pixCodigo'));
    }

    public function adicionarCarrinho(Jogo $jogo): RedirectResponse
    {
        if ($this->jogoJaNaBiblioteca($jogo)) {
            return redirect()->route('cliente.biblioteca')
                ->with('success', "{$jogo->titulo} já está na sua biblioteca.");
        }

        if (($jogo->preco ?? 0) <= 0) {
            return redirect()->route('cliente.loja');
        }

        $carrinho = session('carrinho', []);

        if (in_array($jogo->id_jogo, $carrinho)) {
            return back()->with('success', "{$jogo->titulo} já está no seu carrinho.");
        }

        $carrinho[] = $jogo->id_jogo;
        session(['carrinho' => $carrinho]);

        return back()->with('success', "{$jogo->titulo} foi adicionado ao carrinho.");
    }

    public function removerCarrinho(Jogo $jogo): RedirectResponse
    {
        $carrinho = array_values(array_diff(session('carrinho', []), [$jogo->id_jogo]));
        session(['carrinho' => $carrinho]);

        return redirect()->route('cliente.carrinho')
            ->with('success', "{$jogo->titulo} foi removido do carrinho.");
    }

    public function pagarCarrinho(Request $request): RedirectResponse
    {
        $itens = $this->itensCarrinho();

        if ($itens->isEmpty()) {
            return redirect()->route('cliente.carrinho');
        }

        if ($request->filled('numero_cartao')) {
            $request->merge([
                'numero_cartao' => preg_replace('/\D/', '', $request->numero_cartao),
            ]);
        }

        $request->validate([
            'metodo'        => ['required', 'in:pix,cartao'],
            'nome_cartao'   => ['required_if:metodo,cartao', 'nullable', 'string', 'max:60'],
            'numero_cartao' => ['required_if:metodo,cartao', 'nullable', 'digits_between:13,19'],
            'validade'      => ['required_if:metodo,cartao', 'nullable', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'cvv'           => ['required_if:metodo,cartao', 'nullable', 'digits_between:3,4'],
        ], [
            'metodo.required'              => 'Escolha a forma de pagamento.',
            'nome_cartao.required_if'      => 'Informe o nome impresso no cartão.',
            'numero_cartao.required_if'    => 'Informe o número do cartão.',
            'numero_cartao.digits_between' => 'O número do cartão deve ter entre 13 e 19 dígitos.',
            'validade.required_if'         => 'Informe a validade do cartão.',
            'validade.regex'               => 'A validade deve estar no formato MM/AA.',
            'cvv.required_if'              => 'Informe o código de segurança (CVV).',
            'cvv.digits_between'           => 'O CVV deve ter 3 ou 4 dígitos.',
        ]);

        foreach ($itens as $jogo) {
            BibliotecaItem::firstOrCreate(
                [
                    'id_usuario' => $this->usuario()->id_usuario,
                    'id_jogo'    => $jogo->id_jogo,
                ],
                [
                    'horas_jogadas'  => 0,
                    'data_aquisicao' => now(),
                ]
            );
        }

        session()->forget('carrinho');

        $mensagem = $itens->count() === 1
            ? "Pagamento confirmado! {$itens->first()->titulo} já está na sua biblioteca."
            : "Pagamento confirmado! {$itens->count()} jogos já estão na sua biblioteca.";

        return redirect()->route('cliente.biblioteca')->with('success', $mensagem);
    }

    public function avaliar()
    {
        $usuario = $this->usuario();

        $itens = $usuario->biblioteca()->with('jogo')->paginate(10);

        $minhasNotas = $usuario->avaliacoes()
            ->whereIn('id_jogo', $itens->pluck('id_jogo'))
            ->get()
            ->keyBy('id_jogo');

        return view('cliente.avaliar', compact('itens', 'minhasNotas'));
    }

    public function salvarAvaliacao(Request $request, Jogo $jogo): RedirectResponse
    {
        $usuario = $this->usuario();

        abort_unless(
            $usuario->biblioteca()->where('id_jogo', $jogo->id_jogo)->exists(),
            403
        );

        $dados = $request->validate([
            'nota'       => ['required', 'integer', 'between:1,5'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ]);

        Avaliacao::updateOrCreate(
            ['id_usuario' => $usuario->id_usuario, 'id_jogo' => $jogo->id_jogo],
            $dados
        );

        return back()->with('success', 'Avaliação salva.');
    }
}