<?php

namespace App\Http\Controllers;

use App\Http\Requests\JogoRequest;
use App\Models\Desenvolvedora;
use App\Models\Genero;
use App\Models\Jogo;
use App\Services\JogoService;
use Illuminate\Http\Request;

class JogoController extends Controller
{
    public function __construct(private JogoService $service)
    {
    }

     public function index(Request $request)
    {
        $jogos = $this->service->listar($request->only(['busca', 'genero']));

        return view('jogos.index', compact('jogos'));
    }

    public function create()
    {
        return view('jogos.create', [
            'jogo' => new Jogo(),
            'generos' => Genero::orderBy('nome')->get(),
            'desenvolvedoras' => Desenvolvedora::orderBy('nome')->get(),
        ]);
    }


    public function store(JogoRequest $request)
    {
        $jogo = $this->service->criar($request->validated());

        return redirect()->route('jogos.show', $jogo)
            ->with('sucesso', 'Jogo cadastrado com sucesso.');
    }

     public function show(Jogo $jogo)
    {
        $jogo->load(['genero', 'desenvolvedora', 'plataformas']);

        $avaliacoes = $jogo->avaliacoes()
            ->with('usuario')
            ->orderByDesc('data_avaliacao')
            ->limit(10)
            ->get();

        return view('jogos.show', compact('jogo', 'avaliacoes'));
    }

    public function edit(Jogo $jogo)
    {
        return view('jogos.edit', [
            'jogo' => $jogo,
            'generos' => Genero::orderBy('nome')->get(),
            'desenvolvedoras' => Desenvolvedora::orderBy('nome')->get(),
        ]);
    }

    public function update(JogoRequest $request, Jogo $jogo)
    {
        $this->service->atualizar($jogo, $request->validated());

        return redirect()->route('jogos.show', $jogo)
            ->with('sucesso', 'Jogo atualizado com sucesso.');
    }

    public function destroy(Jogo $jogo)
    {
        $this->service->excluir($jogo);

        return redirect()->route('jogos.index')
            ->with('sucesso', 'Jogo excluído com sucesso.');
    }
}