<?php

namespace App\Services;

use App\Services\Concerns\TraduzErroDoBanco;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Integracao com o PostgreSQL:
 *  - procedure sp_adicionar_jogo_biblioteca (valida e insere; o trigger trg_log_biblioteca grava o log)
 *  - function  fn_total_horas_usuario
 */
class BibliotecaService
{
    use TraduzErroDoBanco;

    public function adicionarJogo(int $idUsuario, int $idJogo): void
    {
        try {
            DB::statement(
                'CALL biblioteca_jogos.sp_adicionar_jogo_biblioteca(?::integer, ?::integer)',
                [$idUsuario, $idJogo]
            );
        } catch (QueryException $e) {
            throw $this->traduzirErro($e);
        }
    }

    public function totalHoras(int $idUsuario): float
    {
        $linha = DB::selectOne(
            'SELECT biblioteca_jogos.fn_total_horas_usuario(?::integer) AS total',
            [$idUsuario]
        );

        return (float) $linha->total;
    }
}
