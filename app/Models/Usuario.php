<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'biblioteca_jogos.usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = ['nome', 'email', 'senha_hash'];
    protected $hidden = ['senha_hash'];

    public function getAuthPassword(): string
    {
        return (string) $this->senha_hash;
    }

    public function getRememberTokenName(): string
    {
        return '';
    }

    public function assinatura(): HasOne
    {
        return $this->hasOne(Assinatura::class, 'id_usuario', 'id_usuario')->latestOfMany('id_assinatura');
    }

    public function biblioteca(): HasMany
    {
        return $this->hasMany(BibliotecaItem::class, 'id_usuario', 'id_usuario');
    }

    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class, 'id_usuario', 'id_usuario');
    }
}