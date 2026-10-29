<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assinatura extends Model
{
    protected $table = 'biblioteca_jogos.assinaturas';
    protected $primaryKey = 'id_assinatura';
    public $timestamps = false;

    protected $fillable = ['id_usuario', 'plano', 'data_inicio', 'data_expiracao'];

    protected $casts = [
        'data_inicio' => 'date',
        'data_expiracao' => 'date',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
