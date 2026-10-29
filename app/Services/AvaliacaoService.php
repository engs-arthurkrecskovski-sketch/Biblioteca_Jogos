<?php

namespace App\Services;

use App\Exceptions\RegraDeNegocioException;
use App\Models\Avaliacao;

/**
 * Ao criar/excluir uma avaliacao, o trigger trg_atualiza_nota_jogo recalcula
 * nota_media e total_avaliacoes do jogo. Nada precisa ser feito no PHP.
 */
class AvaliacaoService
{
    public function criar(array $dados): Avaliacao
    {
        $jaAvaliou = Avaliacao::where('id_usuario', $dados['id_usuario'])
            ->where('id_jogo', $dados['id_jogo'])
            ->exists();

        if ($jaAvaliou) {
            throw new RegraDeNegocioException('Este usuário já avaliou este jogo.');
        }

        return Avaliacao::create($dados);
    }

    public function excluir(Avaliacao $avaliacao): void
    {
        $avaliacao->delete();
    }
}
