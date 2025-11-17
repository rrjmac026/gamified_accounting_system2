@section('title', 'Feedback Records')

<x-app-layout>
    {{-- Header Section --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-700">
                    {{-- Messages --}}
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

                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-[#FF92C2]">Feedback Records Management</h2>
                        <p class="mt-1 text-sm text-gray-600">View and manage all student feedback submissions</p>
                    </div>

                    {{-- Enhanced Search Form --}}
                    <div class="mb-6 bg-white rounded-lg shadow-sm border border-[#FFC8FB] p-4">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-search text-white text-sm"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">Search Feedback Records</h3>
                        </div>
                        
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   id="feedback-search"
                                   placeholder="Search by task, step, student, or type..." 
                                   class="w-full pl-11 pr-4 py-3 border border-[#FFC8FB]/50 rounded-xl bg-white/70 focus:bg-white focus:border-[#FF92C2] focus:ring-2 focus:ring-[#FF92C2]/20 focus:outline-none transition-all duration-200 text-gray-700 placeholder-gray-400">
                        </div>
                        <div class="mt-4 flex justify-end">
                            <span class="text-xs text-gray-500" id="feedback-counter">
                                Showing <span id="visible-count">0</span> task(s)
                            </span>
                        </div>
                    </div>

                    {{-- Feedback Records by Task --}}
                    @if(count($taskData) > 0)
                        <div class="space-y-4" id="feedback-records-container">
                            @foreach($taskData as $data)
                                <div class="task-card mb-6 bg-white rounded-lg shadow-sm border border-[#FFC8FB] overflow-hidden hover:shadow-md transition-shadow duration-300">
                                    <!-- Task Header -->
                                    <div class="bg-gradient-to-r from-[#FFE5F9] to-[#FFF0FA] p-4 cursor-pointer hover:from-[#FFD9FF] hover:to-[#FFE5F9] transition-colors"
                                         onclick="toggleTask('task-{{ $data['task']->id }}')">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-800">
                                                    <i class="fas fa-book text-[#FF92C2] mr-2"></i>
                                                    {{ $data['task']->title }}
                                                </h3>
                                                <div class="mt-2 flex items-center gap-4 text-sm text-gray-600">
                                                    <span>
                                                        <i class="fas fa-graduation-cap text-[#FF92C2]"></i>
                                                        {{ $data['task']->subject->subject_name ?? 'N/A' }}
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-user text-[#FF92C2]"></i>
                                                        {{ $data['task']->instructor->name ?? 'N/A' }}
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-comments text-[#FF92C2]"></i>
                                                        {{ $data['total_feedbacks'] }} Feedback(s)
                                                    </span>
                                                    @if($data['total_unread'] > 0)
                                                        <span class="ml-auto px-2 py-1 rounded-full text-xs bg-red-100 text-red-700 font-semibold">
                                                            {{ $data['total_unread'] }} Unread
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <i class="fas fa-chevron-down text-[#FF92C2] transition-transform duration-300" 
                                               id="icon-task-{{ $data['task']->id }}"></i>
                                        </div>
                                    </div>

                                    <!-- Steps with Feedbacks -->
                                    <div id="task-{{ $data['task']->id }}" class="hidden">
                                        <div class="p-4">
                                            <div class="space-y-4">
                                                @foreach($data['steps'] as $stepNum => $stepInfo)
                                                    @if($stepInfo['feedback_count'] > 0)
                                                        <div class="border rounded-lg overflow-hidden bg-gray-50 border-gray-200">
                                                            <!-- Step Header -->
                                                            <div class="bg-gradient-to-r from-gray-100 to-gray-50 p-3 cursor-pointer hover:from-gray-150 hover:to-gray-100 transition-colors"
                                                                 onclick="toggleStep('step-{{ $stepNum }}-task-{{ $data['task']->id }}')">
                                                                <div class="flex items-center justify-between">
                                                                    <div class="flex items-center gap-3">
                                                                        <span class="flex items-center justify-center w-7 h-7 rounded-full bg-[#FF92C2] text-white text-sm font-semibold">
                                                                            {{ $stepNum }}
                                                                        </span>
                                                                        <div>
                                                                            <h4 class="font-semibold text-gray-800">{{ $stepInfo['title'] }}</h4>
                                                                            <p class="text-xs text-gray-600">{{ $stepInfo['feedback_count'] }} feedback(s) submitted</p>
                                                                        </div>
                                                                    </div>
                                                                    <i class="fas fa-chevron-down text-gray-600 transition-transform duration-300" 
                                                                       id="icon-step-{{ $stepNum }}-task-{{ $data['task']->id }}"></i>
                                                                </div>
                                                            </div>

                                                            <!-- Feedbacks for this Step -->
                                                            <div id="step-{{ $stepNum }}-task-{{ $data['task']->id }}" class="hidden">
                                                                <div class="divide-y divide-gray-200">
                                                                    @foreach($stepInfo['feedbacks'] as $feedback)
                                                                        <div class="p-4 hover:bg-white transition-colors">
                                                                            <div class="flex items-start justify-between mb-3">
                                                                                <div class="flex-1">
                                                                                    <div class="flex items-center gap-2">
                                                                                        <h5 class="font-semibold text-gray-800">
                                                                                            {{ $feedback->student->user->name }}
                                                                                        </h5>
                                                                                        @if(!$feedback->is_read)
                                                                                            <span class="inline-block w-2 h-2 rounded-full bg-red-500"></span>
                                                                                        @endif
                                                                                    </div>
                                                                                    <p class="text-xs text-gray-500 mt-1">
                                                                                        {{ $feedback->created_at->format('M d, Y H:i') }}
                                                                                    </p>
                                                                                </div>
                                                                                <div class="flex items-center gap-2">
                                                                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-[#FFD9FF] text-[#FF92C2] capitalize">
                                                                                        {{ $feedback->feedback_type }}
                                                                                    </span>
                                                                                </div>
                                                                            </div>

                                                                            <div class="bg-blue-50 rounded-lg p-3 mb-3 border border-blue-200">
                                                                                <p class="text-sm text-gray-700">{{ $feedback->feedback_text ?? 'No content provided' }}</p>
                                                                            </div>

                                                                            <div class="flex flex-col sm:flex-row gap-2">
                                                                                <a href="{{ route('admin.feedback-records.show', $feedback) }}" 
                                                                                   class="px-3 py-1.5 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors flex items-center justify-center gap-1">
                                                                                    <i class="fas fa-eye"></i>
                                                                                    View Details
                                                                                </a>
                                                                                <a href="{{ route('admin.feedback-records.edit', $feedback) }}" 
                                                                                   class="px-3 py-1.5 text-xs bg-[#FF92C2] text-white rounded hover:bg-[#ff6fb5] transition-colors flex items-center justify-center gap-1">
                                                                                    <i class="fas fa-edit"></i>
                                                                                    Edit
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-comments text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">No feedback records available</p>
                            <p class="text-gray-400 text-sm mt-2">Feedback submissions will appear here</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
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

        function toggleStep(stepId) {
            const content = document.getElementById(stepId);
            const icon = document.getElementById('icon-' + stepId);
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        const feedbackSearch = document.getElementById("feedback-search");
        const taskCards = document.querySelectorAll(".task-card");
        const visibleCountSpan = document.getElementById("visible-count");

        feedbackSearch.addEventListener("keyup", function() {
            let searchValue = this.value.toLowerCase();
            let visibleCount = 0;

            taskCards.forEach(card => {
                let cardText = card.textContent.toLowerCase();
                if (cardText.includes(searchValue)) {
                    card.style.display = "";
                    visibleCount++;
                } else {
                    card.style.display = "none";
                }
            });
            visibleCountSpan.textContent = visibleCount;
        });

        // Initialize count
        visibleCountSpan.textContent = {{ count($taskData) }};
    </script>
</x-app-layout>