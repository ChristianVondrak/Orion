<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

return function (Schedule $schedule): void {
    // 1) Cada lunes 08:00 → desviación de horas de proyecto
    $schedule
        ->command('alerts:project-deviation')
        ->weeklyOn(1, '08:00')
        ->withoutOverlapping();

    // 2) Cada lunes 08:10 → desviación de horas por usuario
    $schedule
        ->command('alerts:user-deviation')
        ->weeklyOn(1, '08:05')
        ->withoutOverlapping();

    // 3) Diario 09:00 → inactividad de usuarios (p.ej. sin timmings 3 días)
    $schedule
        ->command('alerts:user-inactivity')
        ->dailyAt('09:00')
        ->withoutOverlapping();

    // 4) Diario 18:00 → rendimiento inusual de usuarios
    $schedule
        ->command('alerts:user-performance')
        ->dailyAt('19:00')
        ->withoutOverlapping();

    $schedule->command('worksnaps:sync')->dailyAt('19:20');
};
