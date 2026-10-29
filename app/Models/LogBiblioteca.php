<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Tabela preenchida automaticamente pelo trigger trg_log_biblioteca. */
class LogBiblioteca extends Model
{
    protected $table = 'biblioteca_jogos.log_biblioteca';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $casts = [
        'data_acao' => 'datetime',
    ];
}
