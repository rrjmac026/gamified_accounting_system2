<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">Performance Task Submissions</h2>
                <p class="text-sm text-gray-500 mt-1">Submissions grouped by section</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('instructors.performance-tasks.submissions.export.excel') }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </a>
                <a href="{{ route('instructors.performance-tasks.submissions.export.pdf') }}"
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>

        @forelse($tasksBySection as $sectionId => $sectionTasks)
            @php
                // Section name comes from any task in this group
                $sectionName = $taskStats[$sectionTasks->first()->id]['section_name'] ?? 'No Section';
            @endphp

            {{-- Section Header --}}
            <div class="mb-3 mt-8 first:mt-0">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">{{ $sectionName }}</h3>
                        <p class="text-xs text-gray-500">{{ $sectionTasks->count() }} task{{ $sectionTasks->count() !== 1 ? 's' : '' }} in this section</p>
                    </div>
                </div>
                <div class="mt-2 border-t border-indigo-100"></div>
            </div>

            {{-- Task Cards for this Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach($sectionTasks as $task)
                    @php $stats = $taskStats[$task->id]; @endphp

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <!-- Task Header -->
                            <div class="flex items-start justify-between mb-1">
                                <h4 class="text-base font-semibold text-gray-900 flex-1 leading-snug">
                                    {{ $task->title }}
                                </h4>
                            </div>

                            @if($task->subject)
                                <p class="text-xs text-indigo-600 font-medium mb-2">{{ $task->subject->subject_name }}</p>
                            @endif

                            <p class="text-sm text-gray-500 mb-4 line-clamp-2">
                                {{ Str::limit(strip_tags($task->description), 80) }}
                            </p>

                            <!-- Submission Stats -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Students Submitted:</span>
                                    <span class="font-semibold text-gray-900">{{ $stats['unique_students'] }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Steps Answered:</span>
                                    <span class="font-semibold text-gray-900">
                                        {{ $stats['answered_steps'] }}
                                        @if($stats['total_possible_steps'] > 0)
                                            / {{ $stats['total_possible_steps'] }}
                                        @endif
                                    </span>
                                </div>

                                <!-- Breakdown dots -->
                                <div class="flex items-center gap-3 text-xs pt-0.5">
                                    <span class="inline-flex items-center gap-1 text-green-700">
                                        <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                        {{ $stats['correct_steps'] }} correct
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-blue-700">
                                        <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>
                                        {{ $stats['passed_steps'] }} passed
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-red-600">
                                        <span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span>
                                        {{ $stats['wrong_steps'] }} wrong
                                    </span>
                                </div>
                            </div>

                            <!-- Stacked Progress Bar -->
                            @if($stats['answered_steps'] > 0)
                                <div class="mb-4">
                                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                        <span>Progress</span>
                                        <span>{{ number_format($stats['progress_percent'], 1) }}%</span>
                                    </div>
                                    @php
                                        $total      = max($stats['total_possible_steps'], 1);
                                        $correctPct = ($stats['correct_steps'] / $total) * 100;
                                        $passedPct  = ($stats['passed_steps']  / $total) * 100;
                                        $wrongPct   = ($stats['wrong_steps']   / $total) * 100;
                                    @endphp
                                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden flex">
                                        <div class="bg-green-500 h-full" style="width: {{ $correctPct }}%"></div>
                                        <div class="bg-blue-500 h-full"  style="width: {{ $passedPct }}%"></div>
                                        <div class="bg-red-400 h-full"   style="width: {{ $wrongPct }}%"></div>
                                    </div>
                                </div>
                            @else
                                <div class="mb-4">
                                    <div class="w-full bg-gray-100 rounded-full h-2"></div>
                                    <p class="text-xs text-gray-400 mt-1">No submissions yet</p>
                                </div>
                            @endif

                            <!-- Due Date -->
                            @if($task->due_date)
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-4">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Due: {{ $task->due_date->format('M d, Y') }}
                                </div>
                            @endif

                            <!-- Action Button -->
                            <a href="{{ route('instructors.performance-tasks.submissions.show', $task->id) }}"
                               class="block w-full text-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                View Student Submissions
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

        @empty
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-tasks text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Performance Tasks</h3>
                <p class="text-gray-500 mb-4">You haven't created any performance tasks yet.</p>
                <a href="{{ route('instructors.performance-tasks.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Create Performance Task
                </a>
            </div>
        @endforelse
    </div>
</x-app-layout>