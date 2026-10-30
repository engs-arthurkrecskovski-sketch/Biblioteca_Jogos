<?php

namespace App\Http\Controllers;

use App\Models\Avaliacao;
use App\Models\BibliotecaItem;
use App\Models\Jogo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    private function usuario()
    {
        return Auth::guard('web')->user();
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
            ->with('jogo')
            ->paginate(12);

        return view('cliente.biblioteca', compact('itens'));
    }

    public function loja(Request $request)
    {
        $meus = $this->usuario()->biblioteca()->pluck('id_jogo');

        $jogos = Jogo::whereNotIn('id_jogo', $meus)
            ->when($request->q, fn ($q, $busca) => $q->where('titulo', 'like', "%{$busca}%"))
            ->orderBy('titulo')
            ->paginate(12)
            ->withQueryString();

        return view('cliente.loja', compact('jogos'));
    }

    public function comprar(Jogo $jogo): RedirectResponse
    {
        BibliotecaItem::firstOrCreate([
            'id_usuario' => $this->usuario()->id_usuario,
            'id_jogo'    => $jogo->id_jogo,
        ]);

        return redirect()->route('cliente.biblioteca')
            ->with('success', "{$jogo->titulo} foi adicionado à sua biblioteca.");
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