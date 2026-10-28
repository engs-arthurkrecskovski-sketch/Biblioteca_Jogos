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