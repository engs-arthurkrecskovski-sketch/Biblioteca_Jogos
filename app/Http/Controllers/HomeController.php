<?php

namespace App\Http\Controllers;

use App\Models\Avaliacao;
use App\Models\BibliotecaItem;
use App\Models\Jogo;
use App\Models\Usuario;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('home', [
            'totalJogos' => Jogo::count(),
            'totalUsuarios' => Usuario::count(),
            'totalAvaliacoes' => Avaliacao::count(),
            'totalBiblioteca' => BibliotecaItem::count(),
        ]);
    }
}
