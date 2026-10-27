<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Desenvolvedora extends Model
{
    protected $table = 'biblioteca_jogos.desenvolvedoras';
    protected $primaryKey = 'id_desenvolvedora';
    public $timestamps = false;

    protected $fillable = ['nome', 'pais_origem', 'ano_fundacao'];

    public function jogos(): HasMany
    {
        return $this->hasMany(Jogo::class, 'id_desenvolvedora', 'id_desenvolvedora');
    }
}
