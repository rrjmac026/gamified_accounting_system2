<?php

namespace App\Services;

use App\Models\DataBackup;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BackupService
{
    public function createBackup(string $type = 'full', ?int $userId = null): DataBackup
    {
        $backupName = $type . '_backup_' . now()->format('Y_m_d_His');
        $fileName = $backupName . '.sql';
        
        // Create backup record
        $backup = DataBackup::create([
            'backup_name' => $backupName,
            'backup_type' => $type,
            'status' => 'processing',
            'created_by' => $userId,
            'backup_date' => now(),
            'retention_until' => now()->addDays(30),
        ]);

        try {
            // Ensure backup directory exists
            $backupPath = storage_path('app/backups');
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $filePath = $backupPath . '/' . $fileName;

            // Get database configuration
            $db = config('database.connections.mysql');
            
            // Build command with proper escaping
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                escapeshellarg($db['username']),
                escapeshellarg($db['password']),
                escapeshellarg($db['host']),
                escapeshellarg($db['database']),
                escapeshellarg($filePath)
            );

            // Execute backup command
            $output = [];
            $resultCode = -1;
            exec($command . ' 2>&1', $output, $resultCode);

            if ($resultCode !== 0) {
                throw new \Exception('mysqldump failed: ' . implode("\n", $output));
            }

            if (!file_exists($filePath)) {
                throw new \Exception('Backup file was not created');
            }

            // Update backup record with success
            $backup->update([
                'file_path' => 'backups/' . $fileName,
                'file_size' => filesize($filePath),
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return $backup;

        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            
            // Update backup record with failure
            $backup->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    public static function cleanupExpired(): int
    {
        $expired = self::where('retention_until', '<', now())->get();
        foreach ($expired as $backup) {
            if ($backup->file_path && file_exists(storage_path('app/' . $backup->file_path))) {
                unlink(storage_path('app/' . $backup->file_path));
            }
            $backup->delete();
        }
        return $expired->count();
    }
}
