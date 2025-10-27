<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'backup:create {--type=full : Backup type}';
    protected $description = 'Create database backup';

    public function handle(BackupService $backupService)
    {
        $type = $this->option('type');
        
        $this->info("Starting {$type} backup...");
        $command = "mysqldump -u {$db['username']} -p'{$db['password']}' {$db['database']} > {$fullPath}";
exec($command);
        
        try {
            $backup = $backupService->createBackup($type);
            $this->info("Backup completed successfully! ID: {$backup->id}");
        } catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}