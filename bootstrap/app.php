<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    // 2. TAMBAHKAN BLOK JADWAL INI SEBELUM ->create();
    ->withSchedule(function (Schedule $schedule) {
        
        // Atur jadwal pengingat MCU dua kali sehari (Jam 8 Pagi & 8 Malam WITA)
        $schedule->command('mcu:send-reminders')
                 ->twiceDaily(8, 20)
                 ->timezone('Asia/Makassar');
    })->create();
