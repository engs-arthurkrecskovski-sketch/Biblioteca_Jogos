<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JogoRequest;
use App\Http\Resources\JogoResource;
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
        $porPagina = min(max((int) $request->query('per_page', 15), 1), 100);

        return JogoResource::collection(
            $this->service->listar($request->only(['busca', 'genero']), $porPagina)
        );
    }
}
    public function show(Jogo $jogo)
    {
        return new JogoResource($jogo->load(['genero', 'desenvolvedora', 'plataformas']));
    }

    public function store(JogoRequest $request)
    {
        $jogo = $this->service->criar($request->validated());

        return (new JogoResource($jogo))->response()->setStatusCode(201);
    }