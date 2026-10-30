<?php

namespace Database\Seeders;

use App\Models\Administrador;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdministradorSeeder extends Seeder
{
    public function run(): void
    {
        Administrador::updateOrCreate(
            ['email' => 'admin@savepoint.com'],
            [
                'nome' => 'Administrador',
                'senha_hash' => Hash::make('admin123'),
            ]
        );
    }
}