<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('reports:generate-daily')->dailyAt('19:00');
Schedule::command('reports:generate-monthly')->lastDayOfMonth('19:00');

/*
php artisan schedule:work
php artisan reports:generate-monthly
php artisan reports:generate-daily
*/