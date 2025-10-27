<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] dark:bg-[#595758] overflow-hidden shadow-lg sm:rounded-2xl p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-[#FF92C2] dark:text-[#FFC8FB]">Student Performance Report</h2>
                    <div class="flex space-x-3">
                        <button onclick="window.print()" class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] flex items-center">
                            <i class="fas fa-print mr-2"></i>Print
                        </button>
                        <a href="{{ route('admin.reports.students.export', ['format' => 'pdf'] + request()->all()) }}" 
                           class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center">
                            <i class="fas fa-file-pdf mr-2"></i>PDF
                        </a>
                        <a href="{{ route('admin.reports.students.export', ['format' => 'excel'] + request()->all()) }}" 
                           class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 flex items-center">
                            <i class="fas fa-file-excel mr-2"></i>Excel
                        </a>
                    </div>
                </div>

                <!-- Statistics Summary -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    @foreach($stats as $key => $value)
                        <div class="bg-white dark:bg-[#4a4949] rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-300 uppercase">{{ str_replace('_', ' ', $key) }}</h4>
                            <p class="text-2xl font-bold text-[#FF92C2]">{{ is_numeric($value) ? number_format($value, 2) : $value }}</p>
                        </div>
                    @endforeach
                </div>

                <!-- Detailed Report Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#FFC8FB]">
                        <thead class="bg-[#FFC8FB]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Total XP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Tasks Completed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Performance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-[#595758] divide-y divide-[#FFC8FB]">
                            @foreach($students as $student)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $student->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($student->total_xp) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $student->assignedTasks->where('status', 'completed')->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                <div class="bg-[#FF92C2] h-2.5 rounded-full" style="width: {{ min(100, $student->performance_rating) }}%"></div>
                                            </div>
                                            <span class="ml-2 text-sm text-gray-600">{{ number_format($student->performance_rating, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
