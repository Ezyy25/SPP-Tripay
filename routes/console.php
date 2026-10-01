<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
Schedule::command('tagihan:generate-otomatis')->dailyAt('00:01');
Schedule::command('tagihan:kirim-pengingat')->dailyAt('00:05');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
