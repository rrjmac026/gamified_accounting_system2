<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataBackup;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

    public function store()
    {
        try {
            // Create storage directory if it doesn't exist
            $backupPath = storage_path('app/backups');
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $backup = $this->backupService->createBackup('full', Auth::id());
            
            if ($backup->status === 'completed') {
                return back()->with('success', "Backup completed successfully!");
            }

            return back()->with('error', 'Backup failed: ' . ($backup->error_message ?? 'Unknown error'));
        } catch (\Exception $e) {
            \Log::error('Backup failed: ' . $e->getMessage());
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function download(DataBackup $backup)
    {
        return response()->download(storage_path('app/' . $backup->file_path));
    }

    public function destroy(DataBackup $backup)
    {
        if (!$backup->canDelete()) {
            return back()->with('error', 'This backup cannot be deleted');
        }

        $success = $this->backupService->deleteBackup($backup);
        
        return back()->with(
            $success ? 'success' : 'error',
            $success ? 'Backup deleted successfully' : 'Failed to delete backup'
        );
    }

    public function cleanup()
    {
        $deletedCount = $this->backupService->cleanupExpiredBackups();
        
        return back()->with('success', "Cleaned up {$deletedCount} expired backups");
    }

    public function status(DataBackup $backup)
    {
        return response()->json([
            'id' => $backup->id,
            'status' => $backup->status,
            'progress' => $backup->progress ?? 0,
            'error_message' => $backup->error_message,
        ]);
    }

    protected function getBackupStats(): array
    {
        return [
            'total' => DataBackup::count(),
            'completed' => DataBackup::where('status', 'completed')->count(),
            'failed' => DataBackup::where('status', 'failed')->count(),
            'processing' => DataBackup::where('status', 'processing')->count(),
            'total_size' => DataBackup::where('status', 'completed')->sum('file_size'),
        ];
    }

    // Debug method - remove in production
    public function testBackup()
    {
        try {
            Log::info('Testing backup service');
            
            // Test database connection
            $connection = config('database.default');
            $config = config("database.connections.{$connection}");
            
            Log::info('Database config', [
                'connection' => $connection,
                'host' => $config['host'],
                'database' => $config['database'],
                'username' => $config['username']
            ]);
            
            // Test directory creation
            $backupDir = storage_path('app/backups');
            Log::info('Backup directory', [
                'path' => $backupDir,
                'exists' => file_exists($backupDir),
                'writable' => is_writable(dirname($backupDir))
            ]);
            
            // Test mysqldump availability
            $output = [];
            $returnCode = null;
            exec('mysqldump --version 2>&1', $output, $returnCode);
            
            Log::info('mysqldump test', [
                'return_code' => $returnCode,
                'output' => implode("\n", $output)
            ]);
            
            return response()->json([
                'database_config' => $config,
                'backup_directory' => $backupDir,
                'directory_writable' => is_writable(dirname($backupDir)),
                'mysqldump_available' => $returnCode === 0,
                'mysqldump_output' => implode("\n", $output)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Test backup failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}