<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;

class UsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuarioAtual = $this->route('usuario');
        $criando = $usuarioAtual === null;

        return [
            'nome' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', function ($atributo, $valor, $falhar) use ($usuarioAtual) {
                $existe = Usuario::where('email', $valor)
                    ->when($usuarioAtual, function ($q) use ($usuarioAtual) {
                        $q->where('id_usuario', '!=', $usuarioAtual->id_usuario);
                    })
                    ->exists();

                if ($existe) {
                    $falhar('Este e-mail já está cadastrado.');
                }
            }],
            'senha' => [$criando ? 'required' : 'nullable', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'senha.required' => 'A senha é obrigatória.',
            'senha.min' => 'A senha deve ter no mínimo 6 caracteres.',
        ];
    }
}
