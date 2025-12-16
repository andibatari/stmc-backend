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

        // Guard API Karyawan
        'karyawan_api' => [ 
            'driver' => 'sanctum',
            'provider' => 'employee_logins', // <-- DIUBAH
        ],
        
        // Guard API Peserta MCU
        'peserta_api' => [ 
            'driver' => 'sanctum',
            'provider' => 'peserta_mcu_logins', // <-- DIUBAH
        ],
    ],

    'providers' => [
        'admin_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\AdminUser::class,
        ],
        // Karyawan provider sekarang menunjuk ke tabel login mereka
        'employee_logins' => [ 
            'driver' => 'eloquent',
            'model' => App\Models\EmployeeLogin::class, // <-- DIUBAH
        ],
        // Peserta MCU provider sekarang menunjuk ke tabel login mereka
        'peserta_mcu_logins' => [ 
            'driver' => 'eloquent',
            'model' => App\Models\PesertaMcuLogin::class, // <-- DIUBAH
        ],
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
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

