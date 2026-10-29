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
}