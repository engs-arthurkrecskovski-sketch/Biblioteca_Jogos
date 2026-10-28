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
