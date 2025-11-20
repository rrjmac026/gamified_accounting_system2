@section('title', 'Backup Management')
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Messages -->
            @if(session('success'))
                <div id="successAlert" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="absolute top-0 right-0 px-4 py-3">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div id="errorAlert" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="absolute top-0 right-0 px-4 py-3">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <!-- Processing Alert -->
            <div id="processingAlert" class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative hidden">
                <div class="flex items-center">
                    <i class="fas fa-spinner fa-spin mr-3"></i>
                    <div class="flex-1">
                        <span class="block sm:inline font-semibold">Backup in progress...</span>
                        <div class="mt-2">
                            <div class="bg-blue-200 rounded-full h-2 overflow-hidden">
                                <div id="progressBar" class="bg-blue-600 h-2 transition-all duration-500" style="width: 0%"></div>
                            </div>
                            <span id="progressText" class="text-sm mt-1 block">0%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-[#FFF0FA] overflow-hidden shadow-xl rounded-lg">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-[#FF92C2]">Database Backups</h2>
                        <div class="flex gap-2">
                            <button onclick="refreshBackups()" 
                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-200">
                                <i class="fas fa-sync-alt mr-2"></i>Refresh
                            </button>
                            <button onclick="testBackupSystem()" 
                                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200">
                                <i class="fas fa-flask mr-2"></i>Test System
                            </button>
                            <button onclick="createBackup()" 
                                    class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] transition-all duration-200">
                                <i class="fas fa-plus mr-2"></i>Create Backup
                            </button>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-white p-4 rounded-lg shadow border border-[#FFC8FB]/50">
                            <div class="text-sm text-gray-600">Total Backups</div>
                            <div class="text-2xl font-bold text-[#FF92C2]">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow border border-[#FFC8FB]/50">
                            <div class="text-sm text-gray-600">Completed</div>
                            <div class="text-2xl font-bold text-emerald-600">{{ $stats['completed'] ?? 0 }}</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow border border-[#FFC8FB]/50">
                            <div class="text-sm text-gray-600">Processing</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['processing'] ?? 0 }}</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow border border-[#FFC8FB]/50">
                            <div class="text-sm text-gray-600">Total Size</div>
                            <div class="text-2xl font-bold text-[#FF92C2]">
                                {{ $stats['formatted_total_size'] ?? '0 B' }}
                            </div>
                        </div>
                    </div>

                    <!-- Backup List -->
                    <div class="bg-white rounded-lg shadow border border-[#FFC8FB]/50 overflow-hidden">
                        <table class="min-w-full divide-y divide-[#FFC8FB]">
                            <thead class="bg-[#FFC8FB]/10">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#FFC8FB]/50" id="backupsTableBody">
                                @forelse($backups as $backup)
                                    <tr class="hover:bg-[#FFF6FD]" data-backup-id="{{ $backup->id }}">
                                        <td class="px-6 py-4 text-sm">{{ $backup->backup_name }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="px-2 py-1 bg-[#FFC8FB]/20 text-[#FF92C2] rounded text-xs font-medium">
                                                {{ ucfirst($backup->backup_type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span @class([
                                                'px-2 py-1 rounded-full text-xs font-medium backup-status',
                                                'bg-emerald-100 text-emerald-800' => $backup->status === 'completed',
                                                'bg-yellow-100 text-yellow-800' => $backup->status === 'processing',
                                                'bg-red-100 text-red-800' => $backup->status === 'failed'
                                            ])>
                                                @if($backup->status === 'processing')
                                                    <i class="fas fa-spinner fa-spin mr-1"></i>
                                                @endif
                                                {{ ucfirst($backup->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            @if($backup->status === 'processing')
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full backup-progress" 
                                                         style="width: {{ $backup->progress }}%"></div>
                                                </div>
                                                <span class="text-xs text-gray-600 backup-progress-text">{{ $backup->progress }}%</span>
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm">{{ $backup->formatted_size }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $backup->created_at->diffForHumans() }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                @if($backup->status === 'completed')
                                                    <a href="{{ route('admin.backups.download', $backup) }}" 
                                                       class="text-blue-600 hover:text-blue-800 p-2"
                                                       title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endif
                                                
                                                @if($backup->status === 'failed')
                                                    <button onclick="showError('{{ addslashes($backup->error_message ?? 'Unknown error') }}')"
                                                            class="text-orange-600 hover:text-orange-800 p-2"
                                                            title="View Error">
                                                        <i class="fas fa-exclamation-circle"></i>
                                                    </button>
                                                @endif

                                                <form method="POST" action="{{ route('admin.backups.destroy', $backup) }}" 
                                                      onsubmit="return confirm('Are you sure you want to delete this backup?')"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-800 p-2"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-database text-4xl text-gray-300 mb-2"></i>
                                            <p>No backups found. Create your first backup!</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($backups->hasPages())
                        <div class="mt-4">
                            {{ $backups->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Create Backup Modal -->
    <div id="backupModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Backup</h3>
                
                <form id="backupForm" method="POST" action="{{ route('admin.backups.store') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Backup Type</label>
                        <select name="backup_type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#FF92C2] focus:ring focus:ring-[#FF92C2] focus:ring-opacity-50">
                            <option value="database">Database Only</option>
                            <option value="full">Full Backup (Database + Files)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Retention Days</label>
                        <input type="number" 
                               name="retention_days" 
                               value="30" 
                               min="1" 
                               max="365"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#FF92C2] focus:ring focus:ring-[#FF92C2] focus:ring-opacity-50">
                        <p class="text-xs text-gray-500 mt-1">Backup will be automatically deleted after this many days</p>
                    </div>

                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="async" value="1" checked class="rounded border-gray-300 text-[#FF92C2] focus:ring-[#FF92C2]">
                            <span class="ml-2 text-sm text-gray-600">Run in background (recommended)</span>
                        </label>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" 
                                onclick="closeBackupModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-[#FF92C2] text-white rounded-md hover:bg-[#ff6fb5]">
                            Create Backup
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let progressCheckInterval = null;

        // Check for processing backups on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkForProcessingBackups();
        });

        function checkForProcessingBackups() {
            const processingBackups = document.querySelectorAll('[data-backup-id]');
            let hasProcessing = false;

            processingBackups.forEach(row => {
                const statusBadge = row.querySelector('.backup-status');
                if (statusBadge && statusBadge.textContent.trim().toLowerCase().includes('processing')) {
                    hasProcessing = true;
                }
            });

            if (hasProcessing) {
                startProgressTracking();
            }
        }

        function startProgressTracking() {
            if (progressCheckInterval) {
                clearInterval(progressCheckInterval);
            }

            showProcessingAlert();

            progressCheckInterval = setInterval(async () => {
                await updateBackupProgress();
            }, 2000); // Check every 2 seconds
        }

        function stopProgressTracking() {
            if (progressCheckInterval) {
                clearInterval(progressCheckInterval);
                progressCheckInterval = null;
            }
            hideProcessingAlert();
        }

        function showProcessingAlert() {
            document.getElementById('processingAlert').classList.remove('hidden');
        }

        function hideProcessingAlert() {
            document.getElementById('processingAlert').classList.add('hidden');
        }

        async function updateBackupProgress() {
            try {
                const response = await fetch('{{ route('admin.backups.index') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) return;

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                let hasProcessing = false;
                let maxProgress = 0;

                // Update each backup row
                document.querySelectorAll('[data-backup-id]').forEach(row => {
                    const backupId = row.getAttribute('data-backup-id');
                    const newRow = doc.querySelector(`[data-backup-id="${backupId}"]`);

                    if (newRow) {
                        // Update status
                        const statusCell = row.querySelector('.backup-status');
                        const newStatusCell = newRow.querySelector('.backup-status');
                        if (statusCell && newStatusCell) {
                            statusCell.className = newStatusCell.className;
                            statusCell.innerHTML = newStatusCell.innerHTML;

                            if (newStatusCell.textContent.trim().toLowerCase().includes('processing')) {
                                hasProcessing = true;
                            }
                        }

                        // Update progress bar
                        const progressBar = row.querySelector('.backup-progress');
                        const newProgressBar = newRow.querySelector('.backup-progress');
                        if (progressBar && newProgressBar) {
                            const progress = parseInt(newProgressBar.style.width);
                            progressBar.style.width = newProgressBar.style.width;
                            maxProgress = Math.max(maxProgress, progress);
                        }

                        // Update progress text
                        const progressText = row.querySelector('.backup-progress-text');
                        const newProgressText = newRow.querySelector('.backup-progress-text');
                        if (progressText && newProgressText) {
                            progressText.textContent = newProgressText.textContent;
                        }

                        // Update actions column
                        const actionsCell = row.cells[6];
                        const newActionsCell = newRow.cells[6];
                        if (actionsCell && newActionsCell) {
                            actionsCell.innerHTML = newActionsCell.innerHTML;
                        }
                    }
                });

                // Update global progress bar
                if (hasProcessing) {
                    document.getElementById('progressBar').style.width = maxProgress + '%';
                    document.getElementById('progressText').textContent = maxProgress + '%';
                } else {
                    // All backups completed
                    stopProgressTracking();
                    showSuccessNotification('Backup completed successfully!');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }

            } catch (error) {
                console.error('Error updating progress:', error);
            }
        }

        function showSuccessNotification(message) {
            const alert = document.createElement('div');
            alert.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded shadow-lg z-50';
            alert.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-xl"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(alert);

            setTimeout(() => {
                alert.remove();
            }, 5000);
        }

        function createBackup() {
            document.getElementById('backupModal').classList.remove('hidden');
        }

        function closeBackupModal() {
            document.getElementById('backupModal').classList.add('hidden');
        }

        function showError(message) {
            alert('Backup Error:\n\n' + message);
        }

        async function refreshBackups() {
            window.location.reload();
        }

        async function testBackupSystem() {
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';
            button.disabled = true;

            try {
                const response = await fetch('{{ route('admin.backups.test') }}', {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();
                
                let message = `Backup System Test Results\n\n`;
                message += `Overall Status: ${data.overall_status}\n`;
                message += `Message: ${data.message}\n\n`;
                
                message += `Database Connection: ${data.tests.database_connection?.status || 'N/A'}\n`;
                message += `Backup Directory: ${data.tests.backup_directory?.status || 'N/A'}\n`;
                message += `  - Writable: ${data.tests.backup_directory?.writable ? 'Yes' : 'No'}\n`;
                message += `  - Free Space: ${data.tests.backup_directory?.free_space || 'N/A'}\n\n`;
                message += `mysqldump: ${data.tests.mysqldump?.status || 'N/A'}\n`;
                if (data.tests.mysqldump?.status === 'not_available') {
                    message += `  Note: Will use PHP fallback method (this is fine!)\n`;
                }
                message += `\nPHP Extensions:\n`;
                message += `  - ZIP: ${data.tests.php_extensions?.zip || 'N/A'}\n`;
                message += `  - PDO: ${data.tests.php_extensions?.pdo || 'N/A'}\n`;
                message += `  - MySQLi: ${data.tests.php_extensions?.mysqli || 'N/A'}\n`;

                alert(message);
            } catch (error) {
                alert('Test failed: ' + error.message);
            } finally {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }

        // Close modal when clicking outside
        document.getElementById('backupModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeBackupModal();
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.getElementById('successAlert')?.remove();
            document.getElementById('errorAlert')?.remove();
        }, 5000);
    </script>
</x-app-layout>