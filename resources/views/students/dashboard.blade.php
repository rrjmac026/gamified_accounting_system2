@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-xl shadow-lg mb-6 overflow-hidden">
                <div class="p-6 text-white relative">
                    <div class="absolute inset-0 bg-black opacity-10"></div>
                    <div class="relative z-10">
                        <h1 class="text-3xl font-bold mb-2">Welcome, {{ auth()->user()->name }}!</h1>
                        <p class="text-pink-100">
                            @if($student->sections->isNotEmpty())
                                {{ $student->sections->first()->course->course_name ?? 'No Course' }}
                                | Section {{ $student->sections->first()->name }}
                            @else
                                No Section Assigned
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-6 px-4 py-3 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- XP Card -->
                <div class="bg-gradient-to-br from-[#FF92C2] to-[#ff7bb5] rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-pink-100">Total XP</div>
                            <div class="text-3xl font-bold">{{ number_format($stats['total_xp']) }}</div>
                            <div class="text-sm text-pink-100 mt-1">Points Earned</div>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Tasks Submitted -->
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-emerald-100">Tasks Submitted</div>
                            <div class="text-3xl font-bold">{{ $stats['submitted_tasks'] }}</div>
                            <div class="text-sm text-emerald-100 mt-1">Completed Tasks</div>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Average Score -->
                <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-amber-100">Average Score</div>
                            <div class="text-3xl font-bold">{{ $stats['average_score'] }}%</div>
                            <div class="text-sm text-amber-100 mt-1">Overall Performance</div>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Rank -->
                <div class="bg-gradient-to-br from-purple-500 to-indigo-500 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-purple-100">Class Rank</div>
                            <div class="text-3xl font-bold">#{{ $stats['rank'] }}</div>
                            <div class="text-sm text-purple-100 mt-1">In Section</div>
                        </div>
                        <div class="bg-white/20 rounded-full p-3">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L13.09 8.26L19 9.27L14.5 13.14L15.82 19.02L12 16.77L8.18 19.02L9.5 13.14L5 9.27L10.91 8.26L12 2Z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Upcoming Deadlines -->
                <div class="lg:col-span-2">
                    <div class="bg-[#FFF0FA] rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                        <div class="border-b border-[#FFC8FB] p-6 bg-gradient-to-r from-[#FFF0FA] to-[#FFF6FD]">
                            <h2 class="text-xl font-semibold text-[#FF92C2] flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Upcoming Deadlines
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            @forelse($upcomingDeadlines as $task)
                                <a href="{{ route('students.performance-tasks.show', $task->id) }}" 
                                class="block bg-[#FFF6FD] border border-[#FFC8FB]/30 rounded-lg p-4 hover:bg-[#FFD9FF]/30 transition-colors duration-200">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-800 mb-1">{{ $task->title }}</h3>
                                            <p class="text-sm text-gray-600">
                                                {{ $task->subject->subject_name ?? 'No Subject' }}
                                            </p>
                                        </div>
                                        <div class="text-right ml-4">
                                            @if($task->due_date)
                                                <div class="text-sm font-medium text-gray-700">
                                                    Due: {{ $task->due_date->format('M d, Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $task->due_date->format('g:i A') }}
                                                </div>
                                            @else
                                                <div class="text-sm text-gray-500">No due date</div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-8">
                                    <div class="text-[#FF92C2] mb-2">
                                        <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-600">No upcoming deadlines</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Grades -->
                    <div class="bg-[#FFF0FA] rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden mt-6">
                        <div class="border-b border-[#FFC8FB] p-6 bg-gradient-to-r from-[#FFF0FA] to-[#FFF6FD]">
                            <h2 class="text-xl font-semibold text-[#FF92C2] flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Recent Grades
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            @forelse($recentGrades as $task)
                                @php
                                    $studentPivot = $task->students->first()?->pivot;
                                @endphp
                                <a href="{{ route('students.performance-tasks.show', $task->id) }}" 
                                   class="block bg-[#FFF6FD] border border-[#FFC8FB]/30 rounded-lg p-4 hover:bg-[#FFD9FF]/30 transition-colors duration-200">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-800 mb-1">{{ $task->title }}</h3>
                                            <p class="text-sm text-gray-600">
                                                {{ $task->subject->subject_name ?? 'No Subject' }}
                                            </p>
                                        </div>
                                        <div class="text-right ml-4">
                                            <div class="font-semibold text-lg text-[#FF92C2]">
                                                {{ $studentPivot->score ?? 'N/A' }}/{{ $task->max_score ?? 100 }}
                                            </div>
                                            @if($studentPivot && isset($studentPivot->xp_earned))
                                                <div class="text-sm text-emerald-600 font-medium">
                                                    XP: +{{ $studentPivot->xp_earned }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-8">
                                    <div class="text-[#FF92C2] mb-2">
                                        <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-600">No grades yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Quick Links -->
                    <div class="bg-[#FFF0FA] rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                        <div class="border-b border-[#FFC8FB] p-6 bg-gradient-to-r from-[#FFF0FA] to-[#FFF6FD]">
                            <h2 class="text-xl font-semibold text-[#FF92C2] flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                                Quick Links
                            </h2>
                        </div>
                        <div class="p-6 space-y-3">
                            <a href="{{ route('students.performance-tasks.index') }}" 
                               class="block w-full text-center bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] hover:from-[#ff6fb5] hover:to-[#ffb8f0] text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-md">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                View All Tasks
                            </a>
                            <a href="{{ route('students.subjects.index') }}" 
                               class="block w-full text-center bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-md">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                My Subjects
                            </a>
                            <a href="{{ route('students.todo.index') }}" 
                               class="block w-full text-center bg-gradient-to-r from-purple-500 to-indigo-500 hover:from-purple-600 hover:to-indigo-600 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-md">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                To-Do List
                            </a>
                        </div>
                    </div>

                    <!-- Level Progress -->
                    <div class="bg-[#FFF0FA] rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                        <div class="border-b border-[#FFC8FB] p-6 bg-gradient-to-r from-[#FFF0FA] to-[#FFF6FD]">
                            <h2 class="text-xl font-semibold text-[#FF92C2] flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                                </svg>
                                Level Progress
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <div class="text-4xl font-bold text-[#FF92C2] mb-2">Level {{ $levelData['current_level'] }}</div>
                                <div class="text-sm text-gray-600">{{ $levelData['xp_in_current_level'] }}/1000 XP to next level</div>
                            </div>
                            <div class="relative">
                                <div class="w-full bg-[#FFC8FB]/30 rounded-full h-4 overflow-hidden">
                                    <div class="bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] h-4 rounded-full transition-all duration-500 shadow-sm" 
                                         style="width: {{ $levelData['progress_percentage'] }}%"></div>
                                </div>
                                <div class="text-center mt-2 text-xs text-gray-600 font-medium">
                                    {{ number_format($levelData['progress_percentage'], 1) }}% Complete
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Tasks -->
                    <div class="bg-[#FFF0FA] rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="border-b border-[#FFC8FB] p-6">
                            <h2 class="text-xl font-semibold text-[#FF92C2] flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Upcoming Tasks
                            </h2>
                        </div>
                        <div class="p-6 space-y-3">
                            @forelse($upcomingTasks as $task)
                                <a href="{{ route('students.performance-tasks.show', $task->id) }}" 
                                   class="block p-4 bg-white rounded-lg border border-[#FFC8FB]/30 hover:bg-[#FFD9FF]/30 transition-colors duration-200">
                                    <h3 class="font-medium text-gray-900">{{ $task->title }}</h3>
                                    <p class="text-sm text-gray-600">{{ $task->subject->subject_name ?? 'No Subject' }}</p>
                                    <div class="flex items-center mt-2 text-xs text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Due {{ $task->due_date->format('M d, Y') }}
                                    </div>
                                </a>
                            @empty
                                <p class="text-gray-500 text-center py-4">No upcoming tasks</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection