<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// İş listesi hatırlatmaları normalde panel açıkken gönderilir. Sunucuya zamanlayıcı
// (cron: * * * * * php artisan schedule:run) kurulursa panel kapalıyken de çalışır.
Artisan::command('tasks:remind', function () {
    $this->info(\App\Services\TaskReminders::sendAll().' bildirim gönderildi.');
})->purpose('İş listesi hatırlatmalarını gönderir');

Schedule::command('tasks:remind')->everyMinute()->withoutOverlapping();
