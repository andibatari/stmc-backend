<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // Guard untuk Admin
        'admin_users' => [
            'driver' => 'session',
            'provider' => 'admin_users', // Gunakan provider 'admin_users'
        ],

        // Guard untuk Karyawan
        'employee_logins' => [
            'driver' => 'session',
            'provider' => 'employee_logins', // Gunakan provider 'employee_logins'
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class, // Model default Laravel
        ],

        // Provider untuk Admin
        'admin_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\AdminUser::class, // Model AdminUser
        ],

        // Provider untuk Karyawan
        'employee_logins' => [
            'driver' => 'eloquent',
            'model' => App\Models\EmployeeLogin::class, // Model EmployeeLogin
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reset Kata Sandi
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan "password broker" yang digunakan untuk
    | mengirim tautan reset password. Setiap broker memiliki driver yang
    | unik dan model yang sesuai.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        
        'admin_users' => [
            'provider' => 'admin_users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Opsi Otoritas Reset Kata Sandi
    |--------------------------------------------------------------------------
    |
    | Opsi ini mengontrol durasi token reset password yang valid
    | dan berapa kali pengguna dapat membuat token dalam satu jam.
    |
    */

    'password_timeout' => 10800,
];

