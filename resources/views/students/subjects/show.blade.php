{{-- Subject Details Page --}}
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-700">
                    <div class="mb-6">
                        <nav class="flex items-center space-x-2 text-sm">
                            <a href="{{ route('students.subjects.index') }}" class="text-[#FF92C2] hover:text-[#ff6fb5] transition-colors duration-200">
                                My Subjects
                            </a>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span class="text-gray-600">{{ $subject->subject_code }}</span>
                        </nav>
                    </div>

                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-[#FF92C2] mb-2">{{ $subject->subject_code }} - {{ $subject->subject_name }}</h2>
                        <p class="text-gray-600">{{ $subject->description }}</p>
                    </div>

                    @if (session('success'))
                        <div class="mb-6 px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Instructors Section -->
                        <div class="bg-[#FFF6FD] border border-[#FFC8FB] rounded-lg p-4 sm:p-6">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                Instructors
                            </h3>
                            <div class="space-y-3">
                                @forelse($subject->instructors as $instructor)
                                    <div class="flex items-center p-3 bg-white rounded-lg border border-[#FFC8FB]/30">
                                        <div class="w-10 h-10 bg-[#FF92C2] rounded-full flex items-center justify-center text-white font-medium text-sm">
                                            {{ substr($instructor->user->name, 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium text-gray-900">{{ $instructor->user->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $instructor->user->email }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-600 text-center py-4">No instructors assigned yet.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Performance Tasks Section -->
                        <div class="bg-[#FFF6FD] border border-[#FFC8FB] rounded-lg p-4 sm:p-6">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                Recent Performance Tasks
                            </h3>
                            <div class="space-y-3">
                                @forelse($subject->performanceTasks->take(5) as $performanceTask)
                                    <a href="{{ route('students.performance-tasks.show', $performanceTask->id) }}" 
                                       class="block p-3 bg-white rounded-lg border border-[#FFC8FB]/30 hover:bg-[#FFD9FF]/30 hover:border-[#FF92C2] transition-all duration-200 cursor-pointer">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-900 text-sm">{{ $performanceTask->title }}</h4>
                                                <p class="text-xs text-gray-600 mt-1">
                                                    @if($performanceTask->due_date)
                                                        Due: {{ $performanceTask->due_date->format('M d, Y g:i A') }}
                                                    @else
                                                        <span class="text-gray-400">No due date set</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="ml-3 flex items-center space-x-2">
                                                @php
                                                    $student = auth()->user()->student;
                                                    $pivot = $performanceTask->students->firstWhere('id', $student->id)?->pivot;
                                                    $status = $pivot?->status ?? 'assigned';
                                                @endphp
                                                <span @class([
                                                    'px-2 py-1 text-xs rounded-full',
                                                    'bg-yellow-100 text-yellow-800' => $status === 'assigned',
                                                    'bg-blue-100 text-blue-800' => $status === 'in_progress',
                                                    'bg-green-100 text-green-800' => $status === 'submitted',
                                                    'bg-purple-100 text-purple-800' => $status === 'graded',
                                                    'bg-red-100 text-red-800' => in_array($status, ['late', 'missing', 'overdue'])
                                                ])>
                                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                </span>
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-8">
                                        <div class="text-[#FF92C2] mb-2">
                                            <svg class="mx-auto h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-600 text-sm">No performance tasks assigned yet.</p>
                                    </div>
                                @endforelse
                            </div>
                            
                            @if($subject->performanceTasks->count() > 5)
                                <div class="mt-4 text-center">
                                    <a href="{{ route('students.performance-tasks.index', ['subject' => $subject->id]) }}" 
                                       class="inline-flex items-center px-3 py-2 text-xs font-medium text-[#FF92C2] bg-white border border-[#FFC8FB] rounded-md hover:bg-[#FFF0FA] hover:text-[#ff6fb5] transition-colors duration-200">
                                        View All Performance Tasks
                                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>