<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('app-crons', function () {
    $firebaseNotification = app(\App\Services\FirebaseNotificationService::class);
    (new \App\Console\Commands\StartMeeting($firebaseNotification))->handle();
    (new \App\Console\Commands\ChangeMixxerStatus($firebaseNotification))->handle();
    (new \App\Console\Commands\FriendlyCheck($firebaseNotification))->handle();
})->everyMinute();
