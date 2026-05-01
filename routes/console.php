<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule monthly depreciation to run on the 1st of each month at midnight
Schedule::command('depreciation:run')
    ->monthlyOn(1, '00:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/depreciation-schedule.log'));
