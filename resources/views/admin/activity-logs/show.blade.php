<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-6 text-gray-700">
                    {{-- Title --}}
                    <h2 class="text-2xl font-semibold mb-4 text-[#FF92C2]">
                        Activity Log Details
                    </h2>

                    {{-- Flash Message --}}
                    @if (session('success'))
                        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Details Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border border-[#FFC8FB] rounded-lg">
                            <tbody class="divide-y divide-[#FFC8FB]">
                                <tr>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900 bg-[#FFC8FB]">ID</th>
                                    <td class="py-3 px-6 text-sm bg-[#FFF6FD]">{{ $log->id }}</td>
                                </tr>
                                <tr>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900 bg-[#FFC8FB]">User</th>
                                    <td class="py-3 px-6 text-sm bg-[#FFF6FD]">
                                        {{ $log->user->name ?? 'N/A' }}
                                        <span class="text-gray-500 text-xs">({{ $log->user->email ?? 'N/A' }})</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900 bg-[#FFC8FB]">Action</th>
                                    <td class="py-3 px-6 text-sm bg-[#FFF6FD] capitalize">{{ $log->action }}</td>
                                </tr>
                                <tr>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900 bg-[#FFC8FB]">IP Address</th>
                                    <td class="py-3 px-6 text-sm bg-[#FFF6FD]">{{ $log->ip_address }}</td>
                                </tr>
                                <tr>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900 bg-[#FFC8FB]">User Agent</th>
                                    <td class="py-3 px-6 text-sm bg-[#FFF6FD] break-all">{{ $log->user_agent }}</td>
                                </tr>
                                <tr>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900 bg-[#FFC8FB]">Created At</th>
                                    <td class="py-3 px-6 text-sm bg-[#FFF6FD]">{{ $log->created_at->format('F j, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900 bg-[#FFC8FB]">Updated At</th>
                                    <td class="py-3 px-6 text-sm bg-[#FFF6FD]">{{ $log->updated_at->format('F j, Y g:i A') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Back Button --}}
                    <div class="mt-6">
                        <a href="{{ route('admin.activity-logs.index') }}" 
                           class="inline-block px-4 py-2 bg-[#FF92C2] hover:bg-[#ff6fb5] text-white rounded-md shadow-sm transition">
                            ← Back to Activity Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

