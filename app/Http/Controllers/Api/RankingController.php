<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genero;
use App\Services\RankingService;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    // GET /api/generos/{genero}/ranking?limite=5  (function fn_ranking_jogos_genero)
    public function __invoke(Request $request, Genero $genero, RankingService $service)
    {
        $limite = min(max((int) $request->query('limite', 10), 1), 50);

        return response()->json([
            'id_genero' => $genero->id_genero,
            'genero' => $genero->nome,
            'data' => $service->porGenero($genero->id_genero, $limite),
        ]);
    }
}
