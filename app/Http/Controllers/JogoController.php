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