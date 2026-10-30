<?php

namespace App\Services;

use App\Models\Assinatura;
use App\Services\Concerns\TraduzErroDoBanco;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/** Integracao com a procedure sp_renovar_assinatura. */
class AssinaturaService
{
    use TraduzErroDoBanco;

    public function renovar(int $idUsuario, string $plano): ?Assinatura
    {
        try {
            DB::statement(
                'CALL biblioteca_jogos.sp_renovar_assinatura(?::integer, ?::varchar)',
                [$idUsuario, $plano]
            );
        } catch (QueryException $e) {
            throw $this->traduzirErro($e);
        }

        return Assinatura::where('id_usuario', $idUsuario)
            ->orderByDesc('id_assinatura')
            ->first();
    }
}
