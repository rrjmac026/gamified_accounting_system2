<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataBackup;
use App\Services\BackupService;
use App\Jobs\CreateDatabaseBackupJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class DataBackupController extends Controller
{
    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = DataBackup::with('creator')->latest();
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('backup_type', $request->type);
        }

        $backups = $query->paginate(15);
        $stats = $this->getBackupStats();

        return view('admin.settings.index', compact('backups', 'stats'));
    }

    public function store(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'backup_type' => 'nullable|in:database,full',
                'retention_days' => 'nullable|integer|min:1|max:365',
                'async' => 'nullable|boolean'
            ]);

            $backupType = $validated['backup_type'] ?? 'full';
            $retentionDays = $validated['retention_days'] ?? 30;
            // Default to SYNC (false) instead of async
            $async = $validated['async'] ?? false;

            // Create storage directory if it doesn't exist
            $backupPath = storage_path('app/backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            // Check if async or sync backup
            if ($async && config('queue.default') !== 'sync') {
                // Only use async if queue is properly configured
                CreateDatabaseBackupJob::dispatch(
                    Auth::id(),
                    $backupType,
                    $retentionDays
                );

                return back()->with('success', 'Backup process started. You will be notified when it completes.');
            } else {
                // Synchronous backup (immediate processing)
                // This works without queue:work running
                if ($backupType === 'full') {
                    $backup = $this->backupService->createFullBackup(Auth::id(), $retentionDays);
                } else {
                    $backup = $this->backupService->createDatabaseBackup(Auth::id(), $retentionDays);
                }
                
                if ($backup->status === 'completed') {
                    return back()->with('success', "Backup completed successfully! Size: {$backup->formatted_size}");
                }

                return back()->with('error', 'Backup failed: ' . ($backup->error_message ?? 'Unknown error'));
            }

        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function download(DataBackup $backup)
    {
        try {
            if (!$backup->canDownload()) {
                return back()->with('error', 'Backup is not available for download');
            }

            $filePath = storage_path('app/' . $backup->file_path);
            
            if (!File::exists($filePath)) {
                Log::error('Backup file not found', [
                    'backup_id' => $backup->id,
                    'file_path' => $filePath
                ]);
                return back()->with('error', 'Backup file not found');
            }

            return response()->download(
                $filePath,
                $backup->backup_name . '.zip',
                [
                    'Content-Type' => 'application/zip',
                    'Content-Disposition' => 'attachment; filename="' . $backup->backup_name . '.zip"'
                ]
            );

        } catch (\Exception $e) {
            Log::error('Download failed: ' . $e->getMessage(), [
                'backup_id' => $backup->id
            ]);
            return back()->with('error', 'Failed to download backup: ' . $e->getMessage());
        }
    }

    public function destroy(DataBackup $backup)
    {
        try {
            if (!$backup->canDelete()) {
                return back()->with('error', 'This backup cannot be deleted at this time');
            }

            $this->backupService->deleteBackup($backup);
            
            return back()->with('success', 'Backup deleted successfully');

        } catch (\Exception $e) {
            Log::error('Delete backup failed: ' . $e->getMessage(), [
                'backup_id' => $backup->id
            ]);
            return back()->with('error', 'Failed to delete backup: ' . $e->getMessage());
        }
    }

    public function cleanup()
    {
        try {
            $deletedCount = $this->backupService->cleanExpiredBackups();
            
            return back()->with('success', "Cleaned up {$deletedCount} expired backup(s)");

        } catch (\Exception $e) {
            Log::error('Cleanup failed: ' . $e->getMessage());
            return back()->with('error', 'Cleanup failed: ' . $e->getMessage());
        }
    }

    public function status(DataBackup $backup)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $backup->id,
                'status' => $backup->status,
                'progress' => $backup->progress ?? 0,
                'error_message' => $backup->error_message,
                'file_size' => $backup->file_size,
                'formatted_size' => $backup->formatted_size,
                'completed_at' => $backup->completed_at?->toIso8601String(),
                'can_download' => $backup->canDownload(),
                'can_delete' => $backup->canDelete(),
            ]
        ]);
    }

    public function statistics()
    {
        try {
            $stats = [
                'total' => DataBackup::count(),
                'completed' => DataBackup::where('status', 'completed')->count(),
                'failed' => DataBackup::where('status', 'failed')->count(),
                'processing' => DataBackup::where('status', 'processing')->count(),
                'total_size' => DataBackup::where('status', 'completed')->sum('file_size'),
                'latest_backup' => DataBackup::where('status', 'completed')
                    ->latest('completed_at')
                    ->first(),
                'expired_count' => DataBackup::where('retention_until', '<=', now())->count(),
                'by_type' => [
                    'database' => DataBackup::where('backup_type', 'database')->count(),
                    'full' => DataBackup::where('backup_type', 'full')->count(),
                ],
                'recent_backups' => DataBackup::latest()->limit(5)->get(),
            ];

            // Format total size
            if ($stats['total_size']) {
                $units = ['B', 'KB', 'MB', 'GB'];
                $bytes = $stats['total_size'];
                $i = 0;
                
                for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                    $bytes /= 1024;
                }
                
                $stats['formatted_total_size'] = round($bytes, 2) . ' ' . $units[$i];
            } else {
                $stats['formatted_total_size'] = '0 B';
            }

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Statistics failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics'
            ], 500);
        }
    }

    protected function getBackupStats(): array
    {
        $totalSize = DataBackup::where('status', 'completed')->sum('file_size');
        
        // Format total size
        $formattedSize = '0 B';
        if ($totalSize) {
            $units = ['B', 'KB', 'MB', 'GB'];
            $bytes = $totalSize;
            $i = 0;
            
            for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                $bytes /= 1024;
            }
            
            $formattedSize = round($bytes, 2) . ' ' . $units[$i];
        }

        return [
            'total' => DataBackup::count(),
            'completed' => DataBackup::where('status', 'completed')->count(),
            'failed' => DataBackup::where('status', 'failed')->count(),
            'processing' => DataBackup::where('status', 'processing')->count(),
            'total_size' => $totalSize,
            'formatted_total_size' => $formattedSize,
            'latest_backup' => DataBackup::where('status', 'completed')
                ->latest('completed_at')
                ->first(),
        ];
    }

    public function testBackup()
    {
        try {
            Log::info('Testing backup service');
            
            $results = [
                'timestamp' => now()->toDateTimeString(),
                'tests' => []
            ];

            // Test 1: Database connection
            try {
                $connection = config('database.default');
                $config = config("database.connections.{$connection}");
                
                \DB::connection()->getPdo();
                
                $results['tests']['database_connection'] = [
                    'status' => 'success',
                    'connection' => $connection,
                    'driver' => $config['driver'],
                    'host' => $config['host'],
                    'database' => $config['database'],
                ];
            } catch (\Exception $e) {
                $results['tests']['database_connection'] = [
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
            }

            // Test 2: Backup directory
            $backupDir = storage_path('app/backups');
            try {
                if (!File::exists($backupDir)) {
                    File::makeDirectory($backupDir, 0755, true);
                }
                
                $results['tests']['backup_directory'] = [
                    'status' => 'success',
                    'path' => $backupDir,
                    'exists' => File::exists($backupDir),
                    'writable' => is_writable($backupDir),
                    'free_space' => $this->formatBytes(disk_free_space($backupDir))
                ];
            } catch (\Exception $e) {
                $results['tests']['backup_directory'] = [
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
            }

            // Test 3: mysqldump availability
            $output = [];
            $returnCode = null;
            exec('mysqldump --version 2>&1', $output, $returnCode);
            
            $results['tests']['mysqldump'] = [
                'status' => $returnCode === 0 ? 'available' : 'not_available',
                'return_code' => $returnCode,
                'output' => implode("\n", $output),
                'note' => $returnCode !== 0 ? 'Will use PHP fallback method' : null
            ];

            // Test 4: PHP Extensions
            $results['tests']['php_extensions'] = [
                'zip' => extension_loaded('zip') ? 'installed' : 'missing',
                'pdo' => extension_loaded('pdo') ? 'installed' : 'missing',
                'mysqli' => extension_loaded('mysqli') ? 'installed' : 'missing',
            ];

            // Test 5: Queue configuration
            $queueDriver = config('queue.default');
            $results['tests']['queue_configuration'] = [
                'driver' => $queueDriver,
                'status' => $queueDriver === 'sync' ? 'synchronous' : 'asynchronous',
                'note' => $queueDriver === 'sync' 
                    ? 'Backups will run immediately (recommended for small databases)' 
                    : 'Backups will run in background (requires queue:work)'
            ];

            // Overall status
            $criticalTests = ['database_connection', 'backup_directory'];
            $criticalPassed = collect($results['tests'])
                ->filter(fn($test, $key) => in_array($key, $criticalTests))
                ->every(fn($test) => ($test['status'] ?? '') === 'success');

            $results['overall_status'] = $criticalPassed ? 'ready' : 'issues_found';
            $results['message'] = $criticalPassed 
                ? 'All critical systems ready for backup' 
                : 'Some critical issues found';

            Log::info('Backup test completed', $results);

            return response()->json($results);
            
        } catch (\Exception $e) {
            Log::error('Test backup failed', ['error' => $e->getMessage()]);
            return response()->json([
                'overall_status' => 'failed',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    // Quick backup endpoint for AJAX calls
    public function quickBackup()
    {
        try {
            // Check if there's already a backup in progress
            $processing = DataBackup::where('status', 'processing')->exists();
            
            if ($processing) {
                return response()->json([
                    'success' => false,
                    'message' => 'A backup is already in progress'
                ], 409);
            }

            // Always run synchronously for quick backups
            $backup = $this->backupService->createDatabaseBackup(Auth::id(), 30);

            if ($backup->status === 'completed') {
                return response()->json([
                    'success' => true,
                    'message' => 'Backup completed successfully',
                    'backup' => [
                        'id' => $backup->id,
                        'name' => $backup->backup_name,
                        'size' => $backup->formatted_size
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . ($backup->error_message ?? 'Unknown error')
            ], 500);

        } catch (\Exception $e) {
            Log::error('Quick backup failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to start backup: ' . $e->getMessage()
            ], 500);
        }
    }
}