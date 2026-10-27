<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jogo extends Model
{
    protected $table = 'biblioteca_jogos.jogos';
    protected $primaryKey = 'id_jogo';
    public $timestamps = false;

    // nota_media e total_avaliacoes NAO sao preenchiveis: quem atualiza e o trigger do banco
    protected $fillable = [
        'titulo',
        'descricao',
        'preco',
        'data_lancamento',
        'id_desenvolvedora',
        'id_genero',
    ];
 protected $casts = [
        'preco' => 'float',
        'nota_media' => 'float',
        'total_avaliacoes' => 'integer',
        'data_lancamento' => 'date',
    ];

    public function genero(): BelongsTo
    {
        return $this->belongsTo(Genero::class, 'id_genero', 'id_genero');
    }