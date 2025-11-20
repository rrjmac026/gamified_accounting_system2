<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-xl shadow-lg mb-6 overflow-hidden">
                <div class="p-6 text-white relative">
                    <div class="absolute inset-0 bg-black opacity-10"></div>
                    <div class="relative z-10">
                        <h1 class="text-3xl font-bold mb-2">Welcome, {{ auth()->user()->name }}!</h1>
                        <p class="text-pink-100">
                            {{ $instructor->department }} | {{ $instructor->specialization }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Subjects Card -->
                <div class="bg-gradient-to-br from-[#FF92C2] to-[#ff7bb5] rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-pink-100">Total Subjects</div>
                            <div class="text-3xl font-bold">{{ $stats['total_subjects'] }}</div>
                            <div class="text-sm text-pink-100 mt-1">Assigned Courses</div>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <i class="fas fa-book-open text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Students Card -->
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-emerald-100">Total Students</div>
                            <div class="text-3xl font-bold">{{ $stats['total_students'] }}</div>
                            <div class="text-sm text-emerald-100 mt-1">Active Learners</div>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Active Tasks Card -->
                <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-amber-100">Active Tasks</div>
                            <div class="text-3xl font-bold">{{ $stats['active_tasks'] }}</div>
                            <div class="text-sm text-amber-100 mt-1">Ongoing Activities</div>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <i class="fas fa-tasks text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Pending Submissions -->
                <div class="bg-gradient-to-br from-purple-500 to-indigo-500 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-purple-100">Needs Review</div>
                            <div class="text-3xl font-bold">{{ $stats['submissions_pending'] }}</div>
                            <div class="text-sm text-purple-100 mt-1">Steps to Check</div>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Submissions -->
                <div class="lg:col-span-2">
                    <div class="bg-[#FFF0FA] rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="border-b border-[#FFC8FB] p-6">
                            <h2 class="text-xl font-semibold text-[#FF92C2] flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                Recently Completed Steps
                            </h2>
                        </div>
                        <div class="p-6">
                            @forelse($recentSubmissions as $submission)
                                @php
                                    // Calculate progress for this student on this task
                                    $totalSteps = 10;
                                    $completedSteps = \App\Models\PerformanceTaskSubmission::where('task_id', $submission->task_id)
                                        ->where('student_id', $submission->student_id)
                                        ->where('status', 'correct')
                                        ->count();
                                    $progressPercent = ($completedSteps / $totalSteps) * 100;
                                    
                                    // Step titles
                                    $stepTitles = [
                                        1 => 'Analyze Transactions',
                                        2 => 'Journalize Transactions',
                                        3 => 'Post to Ledger Accounts',
                                        4 => 'Prepare Trial Balance',
                                        5 => 'Journalize & Post Adjusting Entries',
                                        6 => 'Prepare Adjusted Trial Balance',
                                        7 => 'Prepare Financial Statements',
                                        8 => 'Journalize & Post Closing Entries',
                                        9 => 'Prepare Post-Closing Trial Balance',
                                        10 => 'Reverse (Optional Step)',
                                    ];
                                    $stepTitle = $stepTitles[$submission->step] ?? "Step {$submission->step}";
                                @endphp
                                
                                <div class="mb-4 p-4 bg-white rounded-lg border border-[#FFC8FB]/30 hover:bg-[#FFF6FD] transition-colors duration-200">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <!-- Student Name & Status Badge -->
                                            <div class="flex items-center gap-2 mb-1">
                                                <h3 class="font-medium text-gray-900">{{ $submission->student->user->name }}</h3>
                                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-medium">
                                                    <i class="fas fa-check-circle"></i> Completed
                                                </span>
                                            </div>
                                            
                                            <!-- Task & Step Info -->
                                            <p class="text-sm text-gray-600">{{ $submission->task->title }}</p>
                                            <p class="text-xs text-[#FF92C2] font-medium mt-0.5">
                                                <i class="fas fa-tasks mr-1"></i>{{ $stepTitle }}
                                            </p>
                                            
                                            <!-- Progress Bar -->
                                            <div class="mt-3">
                                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                    <span class="font-medium">Overall Progress</span>
                                                    <span class="font-semibold text-[#FF92C2]">{{ $completedSteps }}/10 steps</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                                    <div class="bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] h-2.5 rounded-full transition-all duration-300" 
                                                         style="width: {{ $progressPercent }}%"></div>
                                                </div>
                                            </div>
                                            
                                            <!-- Completion Time & Score -->
                                            <div class="flex items-center gap-4 mt-2">
                                                <p class="text-xs text-gray-500">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Completed {{ $submission->updated_at->diffForHumans() }}
                                                </p>
                                                @if($submission->score)
                                                    <p class="text-xs text-green-600 font-medium">
                                                        <i class="fas fa-star mr-1"></i>
                                                        Score: {{ $submission->score }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- View Details Button -->
                                        <a href="{{ route('instructors.performance-tasks.submissions.show-student', ['task' => $submission->task_id, 'student' => $submission->student->user_id]) }}" 
                                           class="ml-4 px-3 py-1.5 bg-[#FF92C2] text-white text-sm rounded-lg hover:bg-[#ff6fb5] transition-colors duration-200 whitespace-nowrap flex items-center gap-1">
                                            <i class="fas fa-eye text-xs"></i>
                                            <span>View</span>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <i class="fas fa-clipboard-check text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500 font-medium">No completed steps yet</p>
                                    <p class="text-sm text-gray-400 mt-1">Completed student work will appear here</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Performance Overview -->
                    <div class="bg-[#FFF0FA] rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 mt-6">
                        <div class="border-b border-[#FFC8FB] p-6">
                            <h2 class="text-xl font-semibold text-[#FF92C2] flex items-center">
                                <i class="fas fa-chart-line mr-2"></i>
                                Section Performance
                            </h2>
                        </div>
                        <div class="p-6">
                            @forelse($performanceData as $data)
                                <div class="mb-6 last:mb-0">
                                    <div class="flex justify-between items-center mb-2">
                                        <div>
                                            <span class="text-gray-700 font-medium">{{ $data['section_name'] }}</span>
                                            <span class="text-xs text-gray-500 ml-2">
                                                {{ $data['active_students'] }}/{{ $data['total_students'] }} active
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[#FF92C2] font-bold text-lg">{{ number_format($data['avg_score'], 1) }}%</span>
                                            <span class="text-xs text-gray-500 block">Avg Score</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Progress Bar -->
                                    <div class="relative">
                                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                            <div class="bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] h-3 rounded-full transition-all duration-500 relative"
                                                 style="width: {{ $data['submission_rate'] }}%">
                                                @if($data['submission_rate'] > 15)
                                                    <span class="absolute inset-0 flex items-center justify-center text-xs text-white font-medium">
                                                        {{ number_format($data['submission_rate'], 1) }}%
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($data['submission_rate'] <= 15)
                                            <span class="text-xs text-gray-600 mt-1 inline-block">
                                                {{ number_format($data['submission_rate'], 1) }}% completion rate
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-1 text-xs text-gray-500 flex items-center justify-between">
                                        <span>
                                            <i class="fas fa-tasks mr-1"></i>
                                            Task Completion Rate
                                        </span>
                                        @if($data['submission_rate'] >= 80)
                                            <span class="text-green-600 font-medium">
                                                <i class="fas fa-check-circle"></i> Excellent
                                            </span>
                                        @elseif($data['submission_rate'] >= 60)
                                            <span class="text-blue-600 font-medium">
                                                <i class="fas fa-thumbs-up"></i> Good
                                            </span>
                                        @elseif($data['submission_rate'] >= 40)
                                            <span class="text-yellow-600 font-medium">
                                                <i class="fas fa-exclamation-circle"></i> Fair
                                            </span>
                                        @else
                                            <span class="text-red-600 font-medium">
                                                <i class="fas fa-arrow-up"></i> Needs Attention
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <i class="fas fa-chart-bar text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No performance data available</p>
                                    <p class="text-sm text-gray-400 mt-1">Data will appear once students start submitting</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-[#FFF0FA] rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="border-b border-[#FFC8FB] p-6">
                            <h2 class="text-xl font-semibold text-[#FF92C2] flex items-center">
                                <i class="fas fa-bolt mr-2"></i>
                                Quick Actions
                            </h2>
                        </div>
                        <div class="p-6 space-y-3">
                            <a href="{{ route('instructors.performance-tasks.create') }}" 
                               class="block w-full text-center bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] hover:from-[#ff6fb5] hover:to-[#ffb8f0] text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-md">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Create New Task
                            </a>
                            <a href="{{ route('instructors.sections.index') }}" 
                               class="block w-full text-center bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-md">
                                <i class="fas fa-users mr-2"></i>
                                View Sections
                            </a>
                            <a href="{{ route('instructors.performance-tasks.submissions.index') }}" 
                               class="block w-full text-center bg-gradient-to-r from-purple-500 to-indigo-500 hover:from-purple-600 hover:to-indigo-600 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-md">
                                <i class="fas fa-clipboard-check mr-2"></i>
                                Review Submissions
                            </a>
                        </div>
                    </div>

                    <!-- Upcoming Tasks -->
                    @if($upcomingTasks->isNotEmpty())
                    <div class="bg-[#FFF0FA] rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="border-b border-[#FFC8FB] p-6">
                            <h2 class="text-xl font-semibold text-[#FF92C2] flex items-center">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Upcoming Deadlines
                            </h2>
                        </div>
                        <div class="p-6 space-y-3">
                            @foreach($upcomingTasks as $task)
                                <div class="p-3 bg-white rounded-lg border border-[#FFC8FB]/30 hover:shadow-sm transition-shadow">
                                    <h4 class="font-medium text-sm text-gray-900">{{ $task->title }}</h4>
                                    <p class="text-xs text-gray-600 mt-1">{{ $task->subject->subject_name }}</p>
                                    <p class="text-xs text-[#FF92C2] mt-2">
                                        <i class="fas fa-clock mr-1"></i>
                                        Due {{ $task->due_date->diffForHumans() }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>