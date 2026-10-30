<?php

namespace App\Http\Controllers;

use App\Models\Avaliacao;
use App\Models\BibliotecaItem;
use App\Models\Jogo;
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

    public function comprar(Jogo $jogo): RedirectResponse
    {
        if (($jogo->preco ?? 0) > 0) {
            return redirect()->route('cliente.checkout', $jogo->id_jogo);
        }

        BibliotecaItem::firstOrCreate([
            'id_usuario' => $this->usuario()->id_usuario,
            'id_jogo'    => $jogo->id_jogo,
        ]);

        return redirect()->route('cliente.biblioteca')
            ->with('success', "{$jogo->titulo} foi adicionado à sua biblioteca.");
    }

    public function checkout(Jogo $jogo)
    {
        if ($this->jogoJaNaBiblioteca($jogo)) {
            return redirect()->route('cliente.biblioteca')
                ->with('success', "{$jogo->titulo} já está na sua biblioteca.");
        }

        if (($jogo->preco ?? 0) <= 0) {
            return redirect()->route('cliente.loja');
        }

        $jogo->load('genero');

        $pixCodigo = '00020126580014BR.GOV.BCB.PIX0136' . Str::uuid()
            . '5204000053039865406' . number_format($jogo->preco, 2, '', '')
            . '5802BR5909SAVEPOINT6009SAO PAULO6304ABCD';

        return view('cliente.checkout', compact('jogo', 'pixCodigo'));
    }

    public function pagar(Request $request, Jogo $jogo): RedirectResponse
    {
        if (($jogo->preco ?? 0) <= 0) {
            return redirect()->route('cliente.loja');
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

        BibliotecaItem::firstOrCreate([
            'id_usuario' => $this->usuario()->id_usuario,
            'id_jogo'    => $jogo->id_jogo,
        ]);

        return redirect()->route('cliente.biblioteca')
            ->with('success', "Pagamento confirmado! {$jogo->titulo} já está na sua biblioteca.");
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