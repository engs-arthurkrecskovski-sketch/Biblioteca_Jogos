<?php

namespace App\Services\Concerns;

use App\Exceptions\RegraDeNegocioException;
use Illuminate\Database\QueryException;
use Throwable;

trait TraduzErroDoBanco
{
    /**
     * RAISE EXCEPTION dentro de uma procedure chega ao PHP com SQLSTATE P0001.
     * Nesse caso devolvemos a mensagem escrita na procedure como erro de negocio.
     */
    protected function traduzirErro(QueryException $e): Throwable
    {
        $sqlState = $e->errorInfo[0] ?? null;

        if ($sqlState === 'P0001') {
            $mensagem = $e->errorInfo[2] ?? $e->getMessage();
            $mensagem = preg_replace('/^ERROR:\s*/', '', $mensagem);
            $mensagem = trim((string) strtok($mensagem, "\n"));

            return new RegraDeNegocioException($mensagem);
        }

        return $e;
    }
}
