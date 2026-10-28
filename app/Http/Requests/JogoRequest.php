<?php

namespace App\Http\Requests;

use App\Models\Desenvolvedora;
use App\Models\Genero;
use App\Models\Plataforma;
use Illuminate\Foundation\Http\FormRequest;

class JogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        
        $obrigatorio = $this->isMethod('PATCH') ? 'sometimes' : 'required';

        return [
            'titulo' => [$obrigatorio, 'string', 'max:150'],
            'descricao' => ['nullable', 'string'],
            'preco' => [$obrigatorio, 'numeric', 'min:0', 'max:99999999.99'],
            'data_lancamento' => ['nullable', 'date'],
            'id_desenvolvedora' => ['nullable', 'integer', function ($atributo, $valor, $falhar) {
                if (!Desenvolvedora::whereKey($valor)->exists()) {
                    $falhar('A desenvolvedora informada não existe.');
                }
            }],
            'id_genero' => ['nullable', 'integer', function ($atributo, $valor, $falhar) {
                if (!Genero::whereKey($valor)->exists()) {
                    $falhar('O gênero informado não existe.');
                }
            }],
            'plataformas' => ['sometimes', 'array'],
            'plataformas.*' => ['integer', function ($atributo, $valor, $falhar) {
                if (!Plataforma::whereKey($valor)->exists()) {
                    $falhar('Uma das plataformas informadas não existe.');
                }
            }],
        ];
    }
