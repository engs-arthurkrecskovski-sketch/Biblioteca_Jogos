<?php

namespace App\Http\Controllers;

use App\Exceptions\RegraDeNegocioException;
use App\Models\Usuario;
use App\Services\BibliotecaService;
use Illuminate\Http\Request;

class BibliotecaController extends Controller
{
    public function store(Request $request, Usuario $usuario, BibliotecaService $service)
    {
        $dados = $request->validate([
            'id_jogo' => ['required', 'integer'],
        ], [
            'id_jogo.required' => 'Informe o ID do jogo.',
        ]);

        try {
            // chama a procedure sp_adicionar_jogo_biblioteca (o trigger grava o log)
            $service->adicionarJogo($usuario->id_usuario, (int) $dados['id_jogo']);
        } catch (RegraDeNegocioException $e) {
            return back()->withErrors(['id_jogo' => $e->getMessage()])->withInput();
        }

        return redirect()->route('usuarios.show', $usuario)
            ->with('sucesso', 'Jogo adicionado à biblioteca do usuário.');
    }
}
