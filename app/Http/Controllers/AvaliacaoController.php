<?php

namespace App\Http\Controllers;

use App\Exceptions\RegraDeNegocioException;
use App\Http\Requests\AvaliacaoRequest;
use App\Models\Avaliacao;
use App\Services\AvaliacaoService;

class AvaliacaoController extends Controller
{
    public function __construct(private AvaliacaoService $service)
    {
    }

    public function store(AvaliacaoRequest $request)
    {
        try {
            $avaliacao = $this->service->criar($request->validated());
        } catch (RegraDeNegocioException $e) {
            return back()->withErrors(['id_usuario' => $e->getMessage()])->withInput();
        }

        return redirect()->route('jogos.show', $avaliacao->id_jogo)
            ->with('sucesso', 'Avaliação registrada. A nota média foi recalculada pelo trigger.');
    }

    public function destroy(Avaliacao $avaliacao)
    {
        $idJogo = $avaliacao->id_jogo;

        $this->service->excluir($avaliacao);

        return redirect()->route('jogos.show', $idJogo)
            ->with('sucesso', 'Avaliação excluída. A nota média foi recalculada pelo trigger.');
    }
}
