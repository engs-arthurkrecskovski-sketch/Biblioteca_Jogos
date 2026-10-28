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

     public function criar(array $dados): Jogo
    {
        return DB::transaction(function () use ($dados) {
            $plataformas = Arr::pull($dados, 'plataformas');

            $jogo = Jogo::create($dados);

            if (is_array($plataformas)) {
                $jogo->plataformas()->sync($plataformas);
            }

            // refresh traz os valores padrao do banco (total_avaliacoes, nota_media)
            return $jogo->refresh()->load(['genero', 'desenvolvedora', 'plataformas']);
        });
    }

    public function atualizar(Jogo $jogo, array $dados): Jogo
    {
        return DB::transaction(function () use ($jogo, $dados) {
            $plataformas = Arr::pull($dados, 'plataformas');

            $jogo->update($dados);

            if (is_array($plataformas)) {
                $jogo->plataformas()->sync($plataformas);
            }

            return $jogo->refresh()->load(['genero', 'desenvolvedora', 'plataformas']);
        });
    }

    public function excluir(Jogo $jogo): void
    {
        $jogo->delete();
    }
}
