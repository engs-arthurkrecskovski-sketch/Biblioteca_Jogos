<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avaliacao extends Model
{
    protected $table = 'biblioteca_jogos.avaliacoes';
    protected $primaryKey = 'id_avaliacao';
    public $timestamps = false;

    protected $fillable = ['id_usuario', 'id_jogo', 'nota', 'comentario'];

    protected $casts = [
        'nota' => 'integer',
        'data_avaliacao' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function jogo(): BelongsTo
    {
        return $this->belongsTo(Jogo::class, 'id_jogo', 'id_jogo');
    }
}
