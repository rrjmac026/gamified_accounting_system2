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
                            <div class="text-sm font-medium text-purple-100">Pending Review</div>
                            <div class="text-3xl font-bold">{{ $stats['submissions_pending'] }}</div>
                            <div class="text-sm text-purple-100 mt-1">Submissions</div>
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
                                <i class="fas fa-file-alt mr-2"></i>
                                Recent Submissions
                            </h2>
                        </div>
                        <div class="p-6">
                            @forelse($recentSubmissions as $submission)
                                <div class="mb-4 p-4 bg-white rounded-lg border border-[#FFC8FB]/30 hover:bg-[#FFF6FD] transition-colors duration-200">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <!-- Student Name -->
                                            <h3 class="font-medium text-gray-900">{{ $submission->student->user->name }}</h3>
                                            
                                            <!-- Subject Name via Task -->
                                            <p class="text-sm text-gray-600">{{ $submission->task->subject->subject_name }}</p>
                                            
                                            <!-- Submission Time -->
                                            <p class="text-xs text-gray-500 mt-1">Submitted {{ $submission->submitted_at->diffForHumans() }}</p>
                                        </div>

                                        <!-- Review Button -->
                                        <a href="{{ route('instructors.task-submissions.show', $submission) }}" 
                                        class="px-3 py-1 bg-[#FF92C2] text-white text-sm rounded-lg hover:bg-[#ff6fb5]">
                                            Review
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No recent submissions</p>
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
                                <div class="mb-4 last:mb-0">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-gray-700">{{ $data['section_name'] }}</span>
                                        <span class="text-[#FF92C2] font-medium">{{ $data['avg_score'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-[#FF92C2] h-2.5 rounded-full" style="width: {{ $data['submission_rate'] }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Submission Rate: {{ $data['submission_rate'] }}%</p>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No performance data available</p>
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
                               class="block w-full text-center bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] hover:from-[#ff6fb5] hover:to-[#ffb8f0] text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Create New Task
                            </a>
                            <a href="{{ route('instructors.sections.index') }}" 
                               class="block w-full text-center bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-users mr-2"></i>
                                View Sections
                            </a>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
