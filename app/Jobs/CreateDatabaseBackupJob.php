<?php

namespace App\Jobs;

use App\Models\DataBackup;
use App\Services\BackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateDatabaseBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout
    public $tries = 1;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $userId,
        public string $backupType = 'database',
        public int $retentionDays = 30
    ) {}

    /**
     * Execute the job.
     */
    public function handle(BackupService $backupService): void
    {
        Log::info('Starting backup job', [
            'user_id' => $this->userId,
            'backup_type' => $this->backupType,
            'retention_days' => $this->retentionDays
        ]);

        try {
            if ($this->backupType === 'full') {
                $backup = $backupService->createFullBackup($this->userId, $this->retentionDays);
            } else {
                $backup = $backupService->createDatabaseBackup($this->userId, $this->retentionDays);
            }

            if ($backup->status === 'completed') {
                Log::info('Backup job completed successfully', [
                    'backup_id' => $backup->id,
                    'file_size' => $backup->file_size
                ]);
            } else {
                Log::error('Backup job completed with failure', [
                    'backup_id' => $backup->id,
                    'error' => $backup->error_message
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Backup job exception', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Backup job failed', [
            'user_id' => $this->userId,
            'backup_type' => $this->backupType,
            'error' => $exception->getMessage()
        ]);
    }
}