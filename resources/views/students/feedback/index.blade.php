<x-app-layout>
    <!-- Student side -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-700">
                    <!-- Messages -->
                    @if($errors->any())
                        <div class="mb-4 p-4 rounded-md bg-red-50 border border-red-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Error:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="mb-4 p-4 rounded-md bg-green-50 border border-green-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="mb-4 p-4 rounded-md bg-blue-50 border border-blue-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">{{ session('info') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 rounded-md bg-red-50 border border-red-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-times-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-[#FF92C2]">Performance Task Feedback</h2>
                        <p class="mt-1 text-sm text-gray-600">Submit feedback for completed steps</p>
                    </div>

                    @if(isset($taskData) && count($taskData) > 0)
                        @foreach($taskData as $data)
                            <div class="mb-6 bg-white rounded-lg shadow-sm border border-[#FFC8FB] overflow-hidden">
                                <!-- Task Header -->
                                <div class="bg-gradient-to-r from-[#FFE5F9] to-[#FFF0FA] p-4 cursor-pointer hover:from-[#FFD9FF] hover:to-[#FFE5F9] transition-colors"
                                     onclick="toggleTask('task-{{ $data['task']->id }}')">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-800">
                                                {{ $data['task']->title }}
                                            </h3>
                                            <div class="mt-2 flex items-center gap-4 text-sm text-gray-600">
                                                <span>
                                                    <i class="fas fa-book text-[#FF92C2]"></i>
                                                    {{ $data['task']->subject->subject_name ?? 'N/A' }}
                                                </span>
                                                <span>
                                                    <i class="fas fa-user text-[#FF92C2]"></i>
                                                    {{ $data['task']->instructor->name ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <div class="text-center">
                                                <div class="text-2xl font-bold text-[#FF92C2]">
                                                    {{ $data['completed_steps'] }}/{{ $data['total_steps'] }}
                                                </div>
                                                <div class="text-xs text-gray-500">Steps Completed</div>
                                            </div>
                                            <div class="w-24 h-24">
                                                <svg class="transform -rotate-90" viewBox="0 0 100 100">
                                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#FFC8FB" stroke-width="8"/>
                                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#FF92C2" stroke-width="8"
                                                            stroke-dasharray="{{ $data['progress_percentage'] * 2.512 }}, 251.2"
                                                            stroke-linecap="round"/>
                                                    <text x="50" y="50" text-anchor="middle" dy="7" class="text-lg font-bold fill-[#FF92C2]">
                                                        {{ round($data['progress_percentage']) }}%
                                                    </text>
                                                </svg>
                                            </div>
                                            <i class="fas fa-chevron-down text-[#FF92C2] transition-transform duration-300" 
                                               id="icon-task-{{ $data['task']->id }}"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Steps List -->
                                <div id="task-{{ $data['task']->id }}" class="hidden">
                                    <div class="p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($data['steps'] as $stepNum => $stepInfo)
                                                <div class="border rounded-lg p-4 {{ $stepInfo['is_completed'] ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                                                    <div class="flex items-start justify-between">
                                                        <div class="flex-1">
                                                            <div class="flex items-center gap-2">
                                                                <span class="flex items-center justify-center w-8 h-8 rounded-full 
                                                                    {{ $stepInfo['is_completed'] ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                                                                    {{ $stepNum }}
                                                                </span>
                                                                <div>
                                                                    <h4 class="font-semibold text-gray-800">{{ $stepInfo['title'] }}</h4>
                                                                    @if($stepInfo['is_completed'])
                                                                        <p class="text-xs text-gray-600">
                                                                            Score: <span class="font-semibold">{{ $stepInfo['score'] }}</span>
                                                                            | Status: 
                                                                            <span class="capitalize px-2 py-0.5 rounded-full text-xs
                                                                                {{ $stepInfo['status'] === 'correct' ? 'bg-green-100 text-green-800' : '' }}
                                                                                {{ $stepInfo['status'] === 'passed' ? 'bg-blue-100 text-blue-800' : '' }}
                                                                                {{ $stepInfo['status'] === 'wrong' ? 'bg-red-100 text-red-800' : '' }}">
                                                                                {{ $stepInfo['status'] }}
                                                                            </span>
                                                                        </p>
                                                                    @else
                                                                        <p class="text-xs text-gray-500">Not completed yet</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="flex flex-col gap-2">
                                                            @if($stepInfo['has_feedback'])
                                                                <button onclick="viewFeedback({{ $stepInfo['feedback']->id }})" 
                                                                        class="px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors flex items-center gap-1">
                                                                    <i class="fas fa-eye"></i>
                                                                    View Feedback
                                                                </button>
                                                            @elseif($stepInfo['can_submit_feedback'])
                                                                <a href="{{ route('students.feedback.create', ['task_id' => $data['task']->id, 'step' => $stepNum]) }}" 
                                                                   class="px-3 py-1 text-xs bg-[#FF92C2] text-white rounded hover:bg-[#ff6fb5] transition-colors flex items-center gap-1">
                                                                    <i class="fas fa-comment"></i>
                                                                    Submit Feedback
                                                                </a>
                                                            @else
                                                                <span class="px-3 py-1 text-xs bg-gray-300 text-gray-600 rounded cursor-not-allowed">
                                                                    Complete Step First
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">No performance tasks available</p>
                            <p class="text-gray-400 text-sm mt-2">Complete tasks to submit feedback</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleTask(taskId) {
            const content = document.getElementById(taskId);
            const icon = document.getElementById('icon-' + taskId);
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        function viewFeedback(feedbackId) {
            window.location.href = '/students/feedback/' + feedbackId;
        }
    </script>
</x-app-layout>