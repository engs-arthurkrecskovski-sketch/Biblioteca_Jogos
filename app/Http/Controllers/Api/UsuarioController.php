<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\RegraDeNegocioException;
use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Services\BibliotecaService;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function __construct(private BibliotecaService $service)
    {
    }

    // GET /api/usuarios/{usuario}/horas  (function fn_total_horas_usuario)
    public function horas(Usuario $usuario)
    {
        return response()->json([
            'id_usuario' => $usuario->id_usuario,
            'total_horas' => $this->service->totalHoras($usuario->id_usuario),
        ]);
    }

    // POST /api/usuarios/{usuario}/biblioteca  (procedure + trigger de log)
    public function adicionarJogo(Request $request, Usuario $usuario)
    {
        $dados = $request->validate([
            'id_jogo' => ['required', 'integer'],
        ]);

        try {
            $this->service->adicionarJogo($usuario->id_usuario, (int) $dados['id_jogo']);
        } catch (RegraDeNegocioException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Jogo adicionado à biblioteca.',
            'id_usuario' => $usuario->id_usuario,
            'id_jogo' => (int) $dados['id_jogo'],
        ], 201);
    }
}
