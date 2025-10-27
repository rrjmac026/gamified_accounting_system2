@section('title', 'Backup Management')
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-xl rounded-lg">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-[#FF92C2]">Database Backups</h2>
                        <button onclick="createBackup()" 
                                class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] transition-all duration-200">
                            <i class="fas fa-plus mr-2"></i>Create Backup
                        </button>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-white p-4 rounded-lg shadow border border-[#FFC8FB]/50">
                            <div class="text-sm text-gray-600">Total Backups</div>
                            <div class="text-2xl font-bold text-[#FF92C2]">{{ $stats['total'] }}</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow border border-[#FFC8FB]/50">
                            <div class="text-sm text-gray-600">Completed</div>
                            <div class="text-2xl font-bold text-emerald-600">{{ $stats['completed'] }}</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow border border-[#FFC8FB]/50">
                            <div class="text-sm text-gray-600">Failed</div>
                            <div class="text-2xl font-bold text-red-600">{{ $stats['failed'] }}</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow border border-[#FFC8FB]/50">
                            <div class="text-sm text-gray-600">Total Size</div>
                            <div class="text-2xl font-bold text-[#FF92C2]">{{ number_format($stats['total_size'] / 1048576, 2) }} MB</div>
                        </div>
                    </div>

                    <!-- Backup List -->
                    <div class="bg-white rounded-lg shadow border border-[#FFC8FB]/50">
                        <table class="min-w-full divide-y divide-[#FFC8FB]">
                            <thead class="bg-[#FFC8FB]/10">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#FFC8FB]/50">
                                @forelse($backups as $backup)
                                    <tr class="hover:bg-[#FFF6FD]">
                                        <td class="px-6 py-4">{{ $backup->backup_name }}</td>
                                        <td class="px-6 py-4">{{ ucfirst($backup->backup_type) }}</td>
                                        <td class="px-6 py-4">
                                            <span @class([
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                'bg-emerald-100 text-emerald-800' => $backup->status === 'completed',
                                                'bg-yellow-100 text-yellow-800' => $backup->status === 'processing',
                                                'bg-red-100 text-red-800' => $backup->status === 'failed'
                                            ])>
                                                {{ ucfirst($backup->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">{{ number_format($backup->file_size / 1024, 2) }} KB</td>
                                        <td class="px-6 py-4">{{ $backup->created_at->diffForHumans() }}</td>
                                        <td class="px-6 py-4 flex items-center gap-2">
                                            @if($backup->status === 'completed')
                                                <a href="{{ route('admin.backups.download', $backup) }}" 
                                                   class="text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                            <button onclick="deleteBackup('{{ $backup->id }}')"
                                                    class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No backups found
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

    <script>
        async function createBackup() {
            if (!confirm('Are you sure you want to create a new backup?')) return;

            try {
                const response = await fetch('{{ route('admin.backups.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        backup_type: 'full'
                    })
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to create backup');
                }
            } catch (error) {
                alert('Error creating backup');
            }
        }

        async function deleteBackup(id) {
            if (!confirm('Are you sure you want to delete this backup?')) return;

            try {
                const response = await fetch(`/admin/backups/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to delete backup');
                }
            } catch (error) {
                alert('Error deleting backup');
            }
        }
    </script>
</x-app-layout>
