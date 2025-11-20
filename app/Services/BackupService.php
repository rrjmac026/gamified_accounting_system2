<?php

namespace App\Services;

use App\Models\DataBackup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Carbon\Carbon;

class BackupService
{
    /**
     * Create a database backup
     */
    public function createDatabaseBackup(int $userId, int $retentionDays = 30): DataBackup
    {
        $backupName = 'database_backup_' . now()->format('Y-m-d_H-i-s');
        
        // Create backup record
        $backup = DataBackup::create([
            'backup_name' => $backupName,
            'backup_type' => 'database',
            'status' => 'processing',
            'created_by' => $userId,
            'backup_date' => now(),
            'retention_until' => now()->addDays($retentionDays),
            'progress' => 0
        ]);

        try {
            // Create backup directory if it doesn't exist
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $sqlFile = $backupDir . '/' . $backupName . '.sql';
            $zipFile = $backupDir . '/' . $backupName . '.zip';

            // Update progress
            $backup->update(['progress' => 10]);

            // Get database configuration
            $connection = config('database.default');
            $database = config("database.connections.{$connection}.database");
            $username = config("database.connections.{$connection}.username");
            $password = config("database.connections.{$connection}.password");
            $host = config("database.connections.{$connection}.host");
            $port = config("database.connections.{$connection}.port", 3306);

            // Try using mysqldump first (faster) - but handle Windows properly
            $useMysqldump = false;
            if (function_exists('exec')) {
                $useMysqldump = $this->tryMysqldump($host, $port, $database, $username, $password, $sqlFile);
            }
            
            if (!$useMysqldump) {
                // Use PHP method with progress tracking
                Log::info('Using PHP method for database backup');
                $this->createDatabaseBackupPHP($sqlFile, $backup);
            }

            $backup->update(['progress' => 70]);

            // Verify SQL file was created
            if (!file_exists($sqlFile) || filesize($sqlFile) == 0) {
                throw new \Exception('Failed to create SQL backup file');
            }

            // Create ZIP archive
            $zip = new ZipArchive();
            if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                $zip->addFile($sqlFile, basename($sqlFile));
                $zip->close();
                
                // Remove the SQL file after zipping
                if (file_exists($sqlFile)) {
                    unlink($sqlFile);
                }
            } else {
                throw new \Exception('Failed to create ZIP archive');
            }

            $backup->update(['progress' => 90]);

            // Verify ZIP file
            if (!file_exists($zipFile) || filesize($zipFile) == 0) {
                throw new \Exception('Failed to create ZIP file');
            }

            // Get file size
            $fileSize = filesize($zipFile);
            
            // Update backup record
            $backup->update([
                'file_path' => 'backups/' . basename($zipFile),
                'file_size' => $fileSize,
                'status' => 'completed',
                'completed_at' => now(),
                'progress' => 100
            ]);

            Log::info('Database backup completed', [
                'backup_id' => $backup->id,
                'file_size' => $this->formatBytes($fileSize)
            ]);

            return $backup->fresh();

        } catch (\Exception $e) {
            Log::error('Database backup failed', [
                'backup_id' => $backup->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Clean up any partial files
            if (isset($sqlFile) && file_exists($sqlFile)) {
                @unlink($sqlFile);
            }
            if (isset($zipFile) && file_exists($zipFile)) {
                @unlink($zipFile);
            }

            $backup->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'progress' => 0
            ]);

            return $backup->fresh();
        }
    }

    /**
     * Try to use mysqldump command (with proper Windows support)
     */
    private function tryMysqldump(string $host, int $port, string $database, string $username, string $password, string $outputFile): bool
    {
        try {
            // Check if mysqldump is available
            $testCommand = PHP_OS_FAMILY === 'Windows' ? 'where mysqldump' : 'which mysqldump';
            exec($testCommand . ' 2>&1', $output, $returnCode);
            
            if ($returnCode !== 0) {
                Log::info('mysqldump not found in PATH');
                return false;
            }

            // Build mysqldump command with proper escaping for Windows
            if (PHP_OS_FAMILY === 'Windows') {
                // Windows command
                $command = sprintf(
                    'mysqldump --user=%s --password=%s --host=%s --port=%d --single-transaction --quick --lock-tables=false %s > "%s" 2>&1',
                    $username,
                    $password,
                    $host,
                    $port,
                    $database,
                    $outputFile
                );
            } else {
                // Unix/Linux command
                $command = sprintf(
                    'mysqldump --user=%s --password=%s --host=%s --port=%d --single-transaction --quick --lock-tables=false %s > %s 2>&1',
                    escapeshellarg($username),
                    escapeshellarg($password),
                    escapeshellarg($host),
                    $port,
                    escapeshellarg($database),
                    escapeshellarg($outputFile)
                );
            }

            // Execute command
            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($outputFile) && filesize($outputFile) > 0) {
                Log::info('mysqldump backup successful');
                return true;
            }

            Log::warning('mysqldump failed', [
                'return_code' => $returnCode,
                'output' => implode("\n", $output)
            ]);
            return false;

        } catch (\Exception $e) {
            Log::warning('mysqldump exception, will use PHP fallback', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Create database backup using PHP (fallback method with progress tracking)
     */
    private function createDatabaseBackupPHP(string $outputFile, DataBackup $backup = null): void
    {
        $connection = DB::connection();
        $pdo = $connection->getPdo();
        
        $handle = fopen($outputFile, 'w');
        
        if (!$handle) {
            throw new \Exception('Cannot create backup file');
        }

        try {
            // Write header
            fwrite($handle, "-- Database Backup\n");
            fwrite($handle, "-- Generated: " . now()->toDateTimeString() . "\n");
            fwrite($handle, "-- Database: " . config('database.connections.' . config('database.default') . '.database') . "\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
            fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
            fwrite($handle, "SET time_zone = \"+00:00\";\n\n");

            // Get all tables
            $tables = $connection->select('SHOW TABLES');
            $dbName = config('database.connections.' . config('database.default') . '.database');
            $tableKey = "Tables_in_{$dbName}";

            $totalTables = count($tables);
            $processedTables = 0;

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                // Progress update
                $processedTables++;
                $progress = 20 + (($processedTables / $totalTables) * 40); // 20-60% range
                if ($backup) {
                    $backup->update(['progress' => (int)$progress]);
                }
                
                // Get table structure
                $createTable = $connection->select("SHOW CREATE TABLE `{$tableName}`");
                fwrite($handle, "\n-- --------------------------------------------------------\n");
                fwrite($handle, "-- Table structure for table `{$tableName}`\n");
                fwrite($handle, "-- --------------------------------------------------------\n\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
                fwrite($handle, $createTable[0]->{'Create Table'} . ";\n\n");

                // Get table data
                $rowCount = $connection->selectOne("SELECT COUNT(*) as count FROM `{$tableName}`")->count;
                
                if ($rowCount > 0) {
                    fwrite($handle, "-- Dumping data for table `{$tableName}`\n\n");
                    
                    // Process in chunks for large tables
                    $chunkSize = 1000;
                    $offset = 0;
                    
                    while ($offset < $rowCount) {
                        $rows = $connection->select("SELECT * FROM `{$tableName}` LIMIT {$chunkSize} OFFSET {$offset}");
                        
                        foreach ($rows as $row) {
                            $values = [];
                            foreach ($row as $value) {
                                if (is_null($value)) {
                                    $values[] = 'NULL';
                                } else {
                                    $values[] = $pdo->quote($value);
                                }
                            }
                            
                            $columns = array_keys((array) $row);
                            $columnsStr = '`' . implode('`, `', $columns) . '`';
                            $valuesStr = implode(', ', $values);
                            
                            fwrite($handle, "INSERT INTO `{$tableName}` ({$columnsStr}) VALUES ({$valuesStr});\n");
                        }
                        
                        $offset += $chunkSize;
                    }
                    
                    fwrite($handle, "\n");
                }
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fwrite($handle, "\n-- Backup completed at " . now()->toDateTimeString() . "\n");
            
        } finally {
            fclose($handle);
        }
    }

    /**
     * Create full backup (database + files)
     */
    public function createFullBackup(int $userId, int $retentionDays = 30): DataBackup
    {
        $backupName = 'full_backup_' . now()->format('Y-m-d_H-i-s');
        
        $backup = DataBackup::create([
            'backup_name' => $backupName,
            'backup_type' => 'full',
            'status' => 'processing',
            'created_by' => $userId,
            'backup_date' => now(),
            'retention_until' => now()->addDays($retentionDays),
            'progress' => 0
        ]);

        try {
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $zipFile = $backupDir . '/' . $backupName . '.zip';
            $sqlFile = $backupDir . '/database_temp_' . now()->format('Y-m-d_H-i-s') . '.sql';
            
            $backup->update(['progress' => 10]);

            // Create database backup
            $connection = config('database.default');
            $database = config("database.connections.{$connection}.database");
            $username = config("database.connections.{$connection}.username");
            $password = config("database.connections.{$connection}.password");
            $host = config("database.connections.{$connection}.host");
            $port = config("database.connections.{$connection}.port", 3306);

            $useMysqldump = false;
            if (function_exists('exec')) {
                $useMysqldump = $this->tryMysqldump($host, $port, $database, $username, $password, $sqlFile);
            }
            
            if (!$useMysqldump) {
                $this->createDatabaseBackupPHP($sqlFile, $backup);
            }

            $backup->update(['progress' => 50]);

            // Create ZIP with database and important files
            $zip = new ZipArchive();
            if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception('Failed to create ZIP archive');
            }

            // Add database
            $zip->addFile($sqlFile, 'database.sql');

            // Add important files (with progress tracking)
            $this->addDirectoryToZip($zip, app_path(), 'app');
            $backup->update(['progress' => 60]);
            
            $this->addDirectoryToZip($zip, config_path(), 'config');
            $backup->update(['progress' => 70]);
            
            $this->addDirectoryToZip($zip, database_path('migrations'), 'database/migrations');
            $backup->update(['progress' => 80]);
            
            $this->addDirectoryToZip($zip, resource_path('views'), 'resources/views');
            
            // Add public uploads if exists
            $uploadsPath = public_path('uploads');
            if (File::exists($uploadsPath)) {
                $this->addDirectoryToZip($zip, $uploadsPath, 'public/uploads');
            }

            $backup->update(['progress' => 90]);
            
            $zip->close();
            
            // Clean up temp SQL file
            if (file_exists($sqlFile)) {
                unlink($sqlFile);
            }

            // Verify ZIP file
            if (!file_exists($zipFile) || filesize($zipFile) == 0) {
                throw new \Exception('Failed to create ZIP file');
            }

            $fileSize = filesize($zipFile);
            
            $backup->update([
                'file_path' => 'backups/' . basename($zipFile),
                'file_size' => $fileSize,
                'status' => 'completed',
                'completed_at' => now(),
                'progress' => 100
            ]);

            Log::info('Full backup completed', [
                'backup_id' => $backup->id,
                'file_size' => $this->formatBytes($fileSize)
            ]);

            return $backup->fresh();

        } catch (\Exception $e) {
            Log::error('Full backup failed', [
                'backup_id' => $backup->id,
                'error' => $e->getMessage()
            ]);

            // Clean up
            if (isset($sqlFile) && file_exists($sqlFile)) {
                @unlink($sqlFile);
            }
            if (isset($zipFile) && file_exists($zipFile)) {
                @unlink($zipFile);
            }

            $backup->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'progress' => 0
            ]);

            return $backup->fresh();
        }
    }

    /**
     * Add directory to ZIP archive recursively
     */
    private function addDirectoryToZip(ZipArchive $zip, string $directory, string $localName): void
    {
        if (!File::exists($directory)) {
            Log::warning("Directory not found: {$directory}");
            return;
        }

        try {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = $localName . '/' . substr($filePath, strlen($directory) + 1);
                    
                    // Normalize path separators for cross-platform compatibility
                    $relativePath = str_replace('\\', '/', $relativePath);
                    
                    $zip->addFile($filePath, $relativePath);
                }
            }
        } catch (\Exception $e) {
            Log::warning("Failed to add directory to ZIP: {$directory}", [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Download backup file
     */
    public function downloadBackup(DataBackup $backup)
    {
        if ($backup->status !== 'completed') {
            throw new \Exception('Backup is not available for download');
        }

        $filePath = storage_path('app/' . $backup->file_path);
        
        if (!File::exists($filePath)) {
            throw new \Exception('Backup file not found');
        }

        return response()->download(
            $filePath,
            $backup->backup_name . '.zip',
            ['Content-Type' => 'application/zip']
        );
    }

    /**
     * Delete backup
     */
    public function deleteBackup(DataBackup $backup): void
    {
        if ($backup->file_path) {
            $filePath = storage_path('app/' . $backup->file_path);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $backup->delete();
        
        Log::info('Backup deleted', ['backup_id' => $backup->id]);
    }

    /**
     * Clean expired backups
     */
    public function cleanExpiredBackups(): int
    {
        $expiredBackups = DataBackup::where('retention_until', '<=', now())
            ->where('status', 'completed')
            ->get();

        $count = 0;
        foreach ($expiredBackups as $backup) {
            try {
                $this->deleteBackup($backup);
                $count++;
            } catch (\Exception $e) {
                Log::error('Failed to delete expired backup', [
                    'backup_id' => $backup->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Expired backups cleaned', ['count' => $count]);
        return $count;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}