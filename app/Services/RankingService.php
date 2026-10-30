<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/** Integracao com a function fn_ranking_jogos_genero. */
class RankingService
{
    public function porGenero(int $idGenero, int $limite = 10): array
    {
        return DB::select(
            'SELECT * FROM biblioteca_jogos.fn_ranking_jogos_genero(?::integer, ?::integer)',
            [$idGenero, $limite]
        );
    }
}
