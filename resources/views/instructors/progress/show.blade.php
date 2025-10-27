<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Student Overview -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">{{ $student->user->name }}</h2>
                            <p class="text-gray-600">{{ $student->student_id }}</p>
                            <p class="text-gray-600">{{ $student->course->name }} - {{ $student->sections->first()?->name }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-blue-600">Level {{ $metrics['level'] }}</div>
                            <p class="text-gray-600">Rank #{{ $metrics['class_rank'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                @foreach([
                    'completion_rate' => ['Task Completion', '%'],
                    'average_score' => ['Average Score', '%'],
                    'total_xp' => ['Total XP', ''],
                    'badges_earned' => ['Badges Earned', '']
                ] as $key => [$label, $suffix])
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-sm font-medium text-gray-500">{{ $label }}</h3>
                        <p class="text-2xl font-bold text-gray-800">
                            @if($key === 'completion_rate')
                                {{ $metrics['completed_tasks'] }} / {{ $metrics['total_tasks'] }}
                            @else
                                {{ $metrics[$key] }}{{ $suffix }}
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>

            <!-- Performance Tasks & Submissions -->
            <div class="bg-white shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Performance Tasks & Submissions</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Task</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($student->performanceTasks as $task)
                                    <tr>
                                        <td class="px-6 py-4">{{ $task->title }}</td>
                                        <td class="px-6 py-4">{{ $task->subject->subject_name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">
                                            @if($task->pivot->score !== null)
                                                {{ $task->pivot->score }}/{{ $task->max_score }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ strtolower($task->pivot->status ?? '') === 'graded' ? 'bg-green-100 text-green-800' : 
                                                   (strtolower($task->pivot->status ?? '') === 'submitted' ? 'bg-blue-100 text-blue-800' : 
                                                   (strtolower($task->pivot->status ?? '') === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                    'bg-gray-100 text-gray-800')) }}">
                                                {{ Str::title($task->pivot->status ?? 'Unknown') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($task->pivot->submitted_at)
                                                @php
                                                    $submittedAt = \Carbon\Carbon::parse($task->pivot->submitted_at);
                                                    $isLate = $task->due_date && $submittedAt->gt($task->due_date);
                                                @endphp
                                                <span class="{{ $isLate ? 'text-red-600' : 'text-gray-600' }}">
                                                    {{ $submittedAt->format('M d, Y g:ia') }}
                                                    @if($isLate)
                                                        <span class="text-xs">(Late)</span>
                                                    @endif
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            No performance tasks found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- XP Progress Chart -->
            <div class="bg-white shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">XP Progress Over Time</h3>
                        <span class="text-sm text-gray-500">
                            Total: {{ number_format($metrics['total_xp']) }} XP
                        </span>
                    </div>
                    
                    @if(isset($xpProgress) && count($xpProgress) > 0)
                        <div class="relative h-80 w-full">
                            <canvas id="xpChart" class="w-full h-full"></canvas>
                        </div>
                    @else
                        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p class="mt-2 text-sm font-medium text-gray-900">No XP transaction history</p>
                                <p class="mt-1 text-sm text-gray-500">XP data will appear here once the student earns XP</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const xpData = @json($xpProgress ?? []);
            
            if (!xpData || xpData.length === 0) {
                console.log('No XP data available');
                return;
            }

            const ctx = document.getElementById('xpChart');
            
            if (!ctx) {
                console.error('Chart canvas not found');
                return;
            }

            const totalXPChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: xpData.map(d => d.label),
                    datasets: [{
                        label: 'Total XP',
                        data: xpData.map(d => d.cumulative),
                        borderColor: '#FF92C2',
                        backgroundColor: 'rgba(255, 146, 194, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: '#FF92C2'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            padding: 12,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            usePointStyle: true,
                            callbacks: {
                                title: (items) => {
                                    if (!items.length) return '';
                                    const item = items[0];
                                    const data = xpData[item.dataIndex];
                                    return data.description || 'XP Update';
                                },
                                label: (context) => {
                                    return `Total XP: ${context.parsed.y.toLocaleString()} XP`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            beginAtZero: true,
                            type: 'linear',
                            display: true,
                            title: {
                                display: true,
                                text: 'Total XP'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    }
                }
            });

            window.addEventListener('resize', () => {
                totalXPChart.resize();
            });
        });
    </script>
    @endpush
</x-app-layout>