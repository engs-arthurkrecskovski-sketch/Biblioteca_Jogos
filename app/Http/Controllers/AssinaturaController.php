<?php

namespace App\Http\Controllers;

use App\Exceptions\RegraDeNegocioException;
use App\Models\Usuario;
use App\Services\AssinaturaService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssinaturaController extends Controller
{
    public function renovar(Request $request, Usuario $usuario, AssinaturaService $service)
    {
        $dados = $request->validate([
            'plano' => ['required', Rule::in(['gratuito', 'premium', 'premium_anual'])],
        ]);

        try {
            // chama a procedure sp_renovar_assinatura
            $service->renovar($usuario->id_usuario, $dados['plano']);
        } catch (RegraDeNegocioException $e) {
            return back()->withErrors(['plano' => $e->getMessage()]);
        }

        return redirect()->route('usuarios.show', $usuario)
            ->with('sucesso', 'Assinatura atualizada com sucesso.');
    }
}
