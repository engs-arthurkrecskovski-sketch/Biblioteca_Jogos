<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use App\Services\RankingService;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function __invoke(Request $request, RankingService $service)
    {
        $generos = Genero::orderBy('nome')->get();
        $generoSelecionado = $request->query('genero');
        $ranking = [];

        if ($generoSelecionado) {
            $ranking = $service->porGenero((int) $generoSelecionado, 10);
        }

        return view('ranking', compact('generos', 'generoSelecionado', 'ranking'));
    }
}
