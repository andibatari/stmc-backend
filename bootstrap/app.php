<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        // Matikan baris di bawah ini agar tidak error karena file console.php tidak ada
        // commands: __DIR__.'/../routes/console.php', 
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        
        // // Eksekusi Pertama: Jam 10:10 WITA
        // $schedule->command('mcu:send-reminders')
        //          ->dailyAt('10:39')
        //          ->timezone('Asia/Makassar');
                 
        // // Eksekusi Kedua: Jam 19:00 WITA
        // $schedule->command('mcu:send-reminders')
        //          ->dailyAt('19:00')
        //          ->timezone('Asia/Makassar');

         // Atur jadwal pengingat MCU: H-H (Jam 6 Pagi) & H-1 (Jam 7 Malam / 19:00 WITA)
        $schedule->command('mcu:send-reminders')
                 ->everyminute() // 🌟 Ubah menjadi everyMinute() untuk memastikan dijalankan setiap menit
                 ->timezone('Asia/Makassar');
                 
    })->create();