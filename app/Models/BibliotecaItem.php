<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tabela biblioteca (chave primaria composta id_usuario + id_jogo).
 * Uso somente para LEITURA: para adicionar jogos usamos a procedure
 * sp_adicionar_jogo_biblioteca (ver BibliotecaService).
 */
class BibliotecaItem extends Model
{
    protected $table = 'biblioteca_jogos.biblioteca';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'horas_jogadas' => 'float',
        'data_aquisicao' => 'datetime',
    ];

    public function jogo(): BelongsTo
    {
        return $this->belongsTo(Jogo::class, 'id_jogo', 'id_jogo');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
