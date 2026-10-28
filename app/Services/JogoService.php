<?php

namespace App\Services;

use App\Models\Jogo;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class JogoService
{
    /** Lista jogos com busca por titulo e filtro por genero. */
    public function listar(array $filtros = [], int $porPagina = 15): LengthAwarePaginator
    {
        return Jogo::query()
            ->with(['genero', 'desenvolvedora'])
            ->when(!empty($filtros['busca']), function ($q) use ($filtros) {
                $q->where('titulo', 'ilike', '%' . $filtros['busca'] . '%');
            })
            ->when(!empty($filtros['genero']), function ($q) use ($filtros) {
                $q->where('id_genero', (int) $filtros['genero']);
            })
            ->orderBy('titulo')
            ->orderBy('id_jogo')
            ->paginate($porPagina)
            ->withQueryString();
    }