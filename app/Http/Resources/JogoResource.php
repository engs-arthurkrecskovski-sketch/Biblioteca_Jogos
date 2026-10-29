<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JogoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_jogo' => $this->id_jogo,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'preco' => $this->preco,
            'data_lancamento' => $this->data_lancamento?->format('Y-m-d'),
            'nota_media' => $this->nota_media,
            'total_avaliacoes' => $this->total_avaliacoes,
            'genero' => $this->whenLoaded('genero', function () {
                return $this->genero ? [
                    'id_genero' => $this->genero->id_genero,
                    'nome' => $this->genero->nome,
                ] : null;
            }),
            'desenvolvedora' => $this->whenLoaded('desenvolvedora', function () {
                return $this->desenvolvedora ? [
                    'id_desenvolvedora' => $this->desenvolvedora->id_desenvolvedora,
                    'nome' => $this->desenvolvedora->nome,
                ] : null;
            }),
            'plataformas' => $this->whenLoaded('plataformas', function () {
                return $this->plataformas->map(function ($p) {
                    return ['id_plataforma' => $p->id_plataforma, 'nome' => $p->nome];
                })->values();jogo 
            }),
        ];
    }
}