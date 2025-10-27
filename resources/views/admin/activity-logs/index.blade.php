@section('title', 'Activity Logs')
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-6 text-gray-700">
                    @if (session('success'))
                        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead class="bg-[#FFC8FB]">
                                <tr>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900">User</th>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900">Action</th>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900">IP Address</th>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900">User Agent</th>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900">Date & Time</th>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-[#FFF6FD] divide-y divide-[#FFC8FB]">
                                @forelse ($logs as $log)
                                    <tr class="hover:bg-[#FFD9FF] transition-colors duration-150">
                                        <td class="py-4 px-6 text-sm text-gray-700">
                                            {{ $log->user->name ?? 'Unknown' }}<br>
                                            <span class="text-xs text-gray-500">{{ $log->user->email ?? '' }}</span>
                                        </td>
                                        <td class="py-4 px-6 text-sm text-gray-700 capitalize">{{ $log->action }}</td>
                                        <td class="py-4 px-6 text-sm text-gray-700">{{ $log->ip_address ?? '-' }}</td>
                                        <td class="py-4 px-6 text-sm text-gray-700 break-words max-w-xs">
                                            {{ Str::limit($log->user_agent, 80) }}
                                        </td>
                                        <td class="py-4 px-6 text-sm text-gray-700">
                                            {{ $log->created_at->format('M d, Y h:i A') }}
                                        </td>
                                        <td class="py-4 px-6 text-sm">
                                            <a href="{{ route('admin.activity-logs.show', $log->id) }}" 
                                               class="text-[#FF92C2] hover:text-[#ff6fb5]">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 px-6 text-sm text-center text-gray-600">
                                            No activity logs found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

