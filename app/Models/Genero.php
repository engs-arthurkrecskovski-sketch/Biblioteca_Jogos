<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Genero extends Model
{
    protected $table = 'biblioteca_jogos.generos';
    protected $primaryKey = 'id_genero';
    public $timestamps = false;

    protected $fillable = ['nome'];

    public function jogos(): HasMany
    {
        return $this->hasMany(Jogo::class, 'id_genero', 'id_genero');
    }
}
