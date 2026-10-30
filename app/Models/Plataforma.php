<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Plataforma extends Model
{
    protected $table = 'biblioteca_jogos.plataformas';
    protected $primaryKey = 'id_plataforma';
    public $timestamps = false;

    protected $fillable = ['nome'];

    public function jogos(): BelongsToMany
    {
        return $this->belongsToMany(
            Jogo::class,
            'biblioteca_jogos.jogos_plataformas',
            'id_plataforma',
            'id_jogo'
        );
    }
}