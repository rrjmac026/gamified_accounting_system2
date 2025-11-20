@section('title', 'Performance Logs')
<x-app-layout>
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-center px-4 sm:px-8 mt-4 gap-4">
        <h2 class="text-lg sm:text-xl font-semibold text-[#FF92C2]">Performance Logs</h2>
    </div>

    {{-- Enhanced Search Form --}}
    <div class="px-4 sm:px-8 mt-8">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-[#FFC8FB]/30 p-6">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-search text-white text-sm"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Search Performance Logs</h3>
            </div>
            
            <div class="space-y-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           id="log-search"
                           placeholder="Search by student, subject, task, or metric..." 
                           class="w-full pl-11 pr-4 py-3 border border-[#FFC8FB]/50 rounded-xl bg-white/70 focus:bg-white focus:border-[#FF92C2] focus:ring-2 focus:ring-[#FF92C2]/20 focus:outline-none transition-all duration-200 text-gray-700 placeholder-gray-400">
                </div>
                <div class="flex justify-end">
                    <span class="text-xs text-gray-500" id="log-counter">
                        Showing {{ $logs->count() }} logs
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-700">
                    @if (session('success'))
                        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Responsive Table --}}
                    <div class="relative">
                        <div class="overflow-x-auto">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden shadow-md rounded-lg">
                                    <table class="min-w-full divide-y divide-[#FFC8FB]">
                                        <thead class="bg-[#FFC8FB] text-xs uppercase">
                                            <tr>
                                                <th scope="col" class="py-3 px-4">
                                                    Student
                                                </th>
                                                <th scope="col" class="hidden sm:table-cell py-3 px-4">
                                                    Subject
                                                </th>
                                                <th scope="col" class="hidden md:table-cell py-3 px-4">
                                                    Task
                                                </th>
                                                <th scope="col" class="py-3 px-4">
                                                    Metric
                                                </th>
                                                <th scope="col" class="py-3 px-4">
                                                    Value
                                                </th>
                                                <th scope="col" class="hidden lg:table-cell py-3 px-4">
                                                    Recorded At
                                                </th>
                                                <th scope="col" class="py-3 px-4">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="log-table-body">
                                            @forelse ($logs as $log)
                                                <tr class="bg-white border-b border-[#FFC8FB] hover:bg-[#FFD9FF] transition-colors duration-150">
                                                    <td class="py-3 px-4 font-medium">
                                                        {{ $log->student->user->name ?? 'N/A' }}
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ $log->student->student_number ?? 'N/A' }}
                                                        </div>
                                                    </td>
                                                    <td class="hidden sm:table-cell py-3 px-4">
                                                        @if($log->subject)
                                                            <span class="text-sm">{{ $log->subject->subject_name }}</span>
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                {{ $log->subject->subject_code }}
                                                            </div>
                                                        @else
                                                            <span class="text-sm text-gray-400">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td class="hidden md:table-cell py-3 px-4">
                                                        @if($log->performanceTask)
                                                            <span class="text-sm">{{ Str::limit($log->performanceTask->title, 30) }}</span>
                                                        @else
                                                            <span class="text-sm text-gray-400">No task assigned</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-4">
                                                        <div class="flex items-center gap-2">
                                                            <i class="fas fa-chart-line text-xs text-[#FF92C2]"></i>
                                                            <span class="text-sm font-medium">{{ $log->formatted_metric }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-4">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ number_format($log->value, 2) }}
                                                        </span>
                                                    </td>
                                                    <td class="hidden lg:table-cell py-3 px-4">
                                                        <div class="text-sm">
                                                            {{ $log->recorded_at->format('M d, Y') }}
                                                            <div class="text-xs text-gray-500">
                                                                {{ $log->recorded_at->format('h:i A') }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-4">
                                                        <div class="flex flex-col sm:flex-row gap-2">
                                                            <a href="{{ route('admin.performance-logs.show', $log) }}" 
                                                            class="text-[#FF92C2] hover:text-[#ff6fb5] transition-colors duration-150">
                                                                <i class="fas fa-eye"></i>
                                                                <span class="ml-2 sm:hidden">View</span>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="py-8 px-4 text-center text-gray-500">
                                                        <div class="flex flex-col items-center">
                                                            <i class="fas fa-chart-bar text-4xl mb-4 text-[#FFC8FB]"></i>
                                                            <p class="text-lg font-medium text-gray-900 mb-1">No performance logs found</p>
                                                            <p class="text-gray-600">Performance data will appear here once recorded</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    @if($logs->hasPages())
                        <div class="mt-4 sm:mt-6">
                            {{ $logs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Client-side Search Script --}}
    <script>
        const logSearch = document.getElementById("log-search");
        const logTableBody = document.getElementById("log-table-body");
        const logRows = logTableBody.getElementsByTagName("tr");
        const logCounter = document.getElementById("log-counter");

        logSearch.addEventListener("keyup", function() {
            let searchValue = this.value.toLowerCase();
            let visibleCount = 0;

            for (let i = 0; i < logRows.length; i++) {
                let rowText = logRows[i].textContent.toLowerCase();
                if (rowText.includes(searchValue)) {
                    logRows[i].style.display = "";
                    visibleCount++;
                } else {
                    logRows[i].style.display = "none";
                }
            }
            logCounter.textContent = `Showing ${visibleCount} log${visibleCount !== 1 ? 's' : ''}`;
        });
    </script>
</x-app-layout>