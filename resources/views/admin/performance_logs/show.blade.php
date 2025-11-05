<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-lg rounded-lg sm:rounded-2xl">
                <div class="p-4 sm:p-6 text-gray-700">
                    {{-- Header with Back Button --}}
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-[#FF92C2]">Performance Log Details</h2>
                        <a href="{{ route('admin.performance-logs.index') }}" 
                           class="text-gray-600 hover:text-[#FF92C2] transition-colors duration-150">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        {{-- Basic Information Card --}}
                        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md border border-[#FFC8FB]/30">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Basic Information</h3>
                            </div>
                            <dl class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <dt class="text-sm text-gray-500 font-medium">Student:</dt>
                                    <dd class="text-sm text-gray-900 font-semibold text-right">
                                        {{ $performanceLog->student->user->name ?? 'N/A' }}
                                        @if($performanceLog->student)
                                            <div class="text-xs text-gray-500 mt-1">
                                                ID: {{ $performanceLog->student->student_id ?? 'N/A' }}
                                            </div>
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex justify-between items-start border-t border-gray-100 pt-3">
                                    <dt class="text-sm text-gray-500 font-medium">Subject:</dt>
                                    <dd class="text-sm text-gray-900 font-semibold text-right">
                                        @if($performanceLog->subject)
                                            {{ $performanceLog->subject->subject_name }}
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $performanceLog->subject->subject_code }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex justify-between items-start border-t border-gray-100 pt-3">
                                    <dt class="text-sm text-gray-500 font-medium">Task:</dt>
                                    <dd class="text-sm text-gray-900 font-semibold text-right">
                                        @if($performanceLog->performanceTask)
                                            {{ $performanceLog->performanceTask->title }}
                                            <div class="text-xs text-gray-500 mt-1">
                                                ID: {{ $performanceLog->performanceTask->id }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">No task assigned</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Performance Details Card --}}
                        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md border border-[#FFC8FB]/30">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-chart-line text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Performance Details</h3>
                            </div>
                            <dl class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <dt class="text-sm text-gray-500 font-medium">Metric:</dt>
                                    <dd class="text-sm text-gray-900 font-semibold text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $performanceLog->formatted_metric }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="flex justify-between items-start border-t border-gray-100 pt-3">
                                    <dt class="text-sm text-gray-500 font-medium">Value:</dt>
                                    <dd class="text-sm text-gray-900 font-semibold text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ number_format($performanceLog->value, 2) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="flex justify-between items-start border-t border-gray-100 pt-3">
                                    <dt class="text-sm text-gray-500 font-medium">Recorded At:</dt>
                                    <dd class="text-sm text-gray-900 font-semibold text-right">
                                        {{ $performanceLog->recorded_at->format('M d, Y') }}
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $performanceLog->recorded_at->format('h:i:s A') }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $performanceLog->recorded_at->diffForHumans() }}
                                        </div>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- Additional Context Section (if task exists) --}}
                    @if($performanceLog->performanceTask)
                        <div class="mt-6 bg-white p-4 sm:p-6 rounded-lg shadow-md border border-[#FFC8FB]/30">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-info-circle text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Task Context</h3>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Max Score</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $performanceLog->performanceTask->max_score ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">XP Reward</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $performanceLog->performanceTask->xp_reward ?? 'N/A' }} XP
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Max Attempts</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $performanceLog->performanceTask->max_attempts ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Due Date</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $performanceLog->performanceTask->due_date ? $performanceLog->performanceTask->due_date->format('M d, Y') : 'No deadline' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Timestamps Section --}}
                    <div class="mt-6 bg-gradient-to-r from-gray-50 to-gray-100 p-4 rounded-lg">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-gray-600">
                            <div>
                                <span class="font-medium">Created:</span>
                                <span>{{ $performanceLog->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Updated:</span>
                                <span>{{ $performanceLog->updated_at->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-6 flex flex-col sm:flex-row justify-between gap-3">
                        <a href="{{ route('admin.performance-logs.index') }}" 
                           class="w-full sm:w-auto px-6 py-2.5 text-center bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-150 shadow-md hover:shadow-lg">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Logs
                        </a>
                        
                        @if($performanceLog->performanceTask)
                            <a href="{{ route('admin.performance-tasks.show', $performanceLog->performanceTask) }}" 
                               class="w-full sm:w-auto px-6 py-2.5 text-center bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] transition-colors duration-150 shadow-md hover:shadow-lg">
                                <i class="fas fa-tasks mr-2"></i>View Task Details
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>