<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;

class BackupController extends Controller
{
    public function index()
    {
        $backupDestination = BackupDestination::create('backups', config('backup.backup.name'));
        $backups = $backupDestination->backups()->map(function(Backup $backup) {
            return [
                'path' => $backup->path(),
                'date' => $backup->date()->format('Y-m-d H:i:s'),
                'size' => $backup->size(),
            ];
        });
        
        $status = BackupDestinationStatusFactory::createForMonitorConfig(config('backup.monitor_backups')[0]);

        return view('admin.backups.index', [
            'backups' => $backups,
            'status' => $status
        ]);
    }

    public function create()
    {
        try {
            Artisan::call('backup:run');
            return back()->with('success', 'Backup completed successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function clean()
    {
        try {
            Artisan::call('backup:clean');
            return back()->with('success', 'Old backups cleaned successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Cleanup failed: ' . $e->getMessage());
        }
    }
}
