<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Administrador extends Authenticatable
{
    protected $table = 'biblioteca_jogos.administradores';
    protected $primaryKey = 'id_admin';
    public $timestamps = false;

    protected $fillable = ['nome', 'email', 'senha_hash'];
    protected $hidden = ['senha_hash'];

    public function getAuthPassword(): string
    {
        return $this->senha_hash;
    }

    public function getRememberTokenName(): string
    {
        return '';
    }
}