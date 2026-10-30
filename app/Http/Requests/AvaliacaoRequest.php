<?php

namespace App\Http\Requests;

use App\Models\Jogo;
use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;

class AvaliacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_usuario' => ['required', 'integer', function ($atributo, $valor, $falhar) {
                if (!Usuario::whereKey($valor)->exists()) {
                    $falhar('O usuário informado não existe.');
                }
            }],
            'id_jogo' => ['required', 'integer', function ($atributo, $valor, $falhar) {
                if (!Jogo::whereKey($valor)->exists()) {
                    $falhar('O jogo informado não existe.');
                }
            }],
            'nota' => ['required', 'integer', 'between:1,5'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_usuario.required' => 'Informe o ID do usuário.',
            'nota.required' => 'A nota é obrigatória.',
            'nota.between' => 'A nota deve ser de 1 a 5.',
        ];
    }
}
