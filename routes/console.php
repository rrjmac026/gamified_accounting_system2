<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $backupService = app(BackupService::class);
    $count = $backupService->cleanExpiredBackups();
    
    \Log::info("Cleaned up {$count} expired backups");
})->daily()->at('02:00')->name('cleanup-expired-backups');
