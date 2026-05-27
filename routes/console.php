<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ==============================================
// USE LARAVEL SHARED WORKER (KHUSUS SHARED HOSTING)
// ==============================================

Schedule::command('queue:shared-worker --max-time=55 --memory=128 --tries=3')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
