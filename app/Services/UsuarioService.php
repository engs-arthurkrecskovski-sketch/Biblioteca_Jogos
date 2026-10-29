<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioService
{
    public function criar(array $dados): Usuario
    {
        return Usuario::create([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'senha_hash' => Hash::make($dados['senha']),
        ]);
    }

    public function atualizar(Usuario $usuario, array $dados): Usuario
    {
        $usuario->nome = $dados['nome'];
        $usuario->email = $dados['email'];

        if (!empty($dados['senha'])) {
            $usuario->senha_hash = Hash::make($dados['senha']);
        }

        $usuario->save();

        return $usuario;
    }

    public function excluir(Usuario $usuario): void
    {
        $usuario->delete();
    }
}
