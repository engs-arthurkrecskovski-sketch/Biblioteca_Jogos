<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'usuarios'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    | Dois guards separados: a sessão de um cliente não vale como sessão de
    | administrador, e vice-versa. É isso que protege o /admin.
    */

    'guards' => [
        // Clientes (usuários da biblioteca)
        'web' => [
            'driver' => 'session',
            'provider' => 'usuarios',
        ],

        // Administradores (painel)
        'admin' => [
            'driver' => 'session',
            'provider' => 'administradores',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    | Dizem de qual model/tabela cada guard busca o usuário.
    */

    'providers' => [
        'usuarios' => [
            'driver' => 'eloquent',
            'model' => App\Models\Usuario::class,
        ],

        'administradores' => [
            'driver' => 'eloquent',
            'model' => App\Models\Administrador::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    | Não usamos recuperação de senha, mas mantemos a seção apontando para
    | um provider que existe.
    */

    'passwords' => [
        'usuarios' => [
            'provider' => 'usuarios',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];