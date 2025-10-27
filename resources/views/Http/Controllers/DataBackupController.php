<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use App\Models\DataBackup;
use Illuminate\Support\Facades\Auth;

class DataBackupController extends Controller
{
    public function index()
    {
        $backups = DataBackup::latest()->paginate(10);
        return view('backups.index', compact('backups'));
    }

    public function store(BackupService $backupService)
    {
        $backup = $backupService->createBackup('full', Auth::id());
        return redirect()->back()->with('status', "Backup {$backup->status}");
    }

    public function download(DataBackup $backup)
    {
        if ($backup->status !== 'completed' || !$backup->file_path) {
            abort(404, "Backup not available");
        }
        return response()->download(storage_path("app/{$backup->file_path}"));
    }

    public function destroy(DataBackup $backup)
    {
        if ($backup->file_path && file_exists(storage_path("app/{$backup->file_path}"))) {
            unlink(storage_path("app/{$backup->file_path}"));
        }
        $backup->delete();
        return redirect()->back()->with('status', 'Backup deleted');
    }
}
