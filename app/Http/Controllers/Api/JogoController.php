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

    // GET /api/jogos?busca=&genero=&per_page=
    public function index(Request $request)
    {
        $porPagina = min(max((int) $request->query('per_page', 15), 1), 100);

        return JogoResource::collection(
            $this->service->listar($request->only(['busca', 'genero']), $porPagina)
        );
    }
}