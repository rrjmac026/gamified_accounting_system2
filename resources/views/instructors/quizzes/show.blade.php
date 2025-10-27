<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8 sm:mb-12 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#FF92C2]">Quiz Question Details</h2>
                    <p class="text-gray-600 mt-2">Question #{{ $quiz->id }}</p>
                </div>
                <a href="{{ route('instructors.quizzes.index') }}" 
                   class="text-gray-600 hover:text-[#FF92C2] transition-colors flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Questions
                </a>
            </div>

            <!-- Main Content Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Question Details -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Question Information Card -->
                    <div class="bg-[#FFF0FA]/95 backdrop-blur-sm shadow-xl rounded-2xl border border-[#FFC8FB]/50 overflow-hidden">
                        <div class="bg-gradient-to-r from-[#FF92C2]/10 to-[#FFC8FB]/10 px-6 py-4 border-b border-[#FFC8FB]/30">
                            <h3 class="text-xl font-semibold text-[#FF92C2] flex items-center">
                                <i class="fas fa-question-circle mr-3"></i>Question Details
                            </h3>
                        </div>
                        
                        <div class="p-6 sm:p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Task -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-2 flex items-center">
                                        <i class="fas fa-tasks mr-2"></i>Associated Task
                                    </label>
                                    <div class="w-full rounded-lg bg-white/80 border border-[#FFC8FB]/50 px-4 py-3 shadow-sm">
                                        <span class="text-gray-800 font-medium">{{ $quiz->task->title ?? 'No task assigned' }}</span>
                                    </div>
                                </div>

                                <!-- Question Type -->
                                <div>
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-2 flex items-center">
                                        <i class="fas fa-list-ul mr-2"></i>Question Type
                                    </label>
                                    <div class="w-full rounded-lg bg-white/80 border border-[#FFC8FB]/50 px-4 py-3 shadow-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-[#FF92C2]/10 text-[#FF92C2]">
                                            {{ ucwords(str_replace('_', ' ', $quiz->type)) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Points -->
                                <div>
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-2 flex items-center">
                                        <i class="fas fa-star mr-2"></i>Points
                                    </label>
                                    <div class="w-full rounded-lg bg-white/80 border border-[#FFC8FB]/50 px-4 py-3 shadow-sm">
                                        <span class="text-2xl font-bold text-[#FF92C2]">{{ $quiz->points }}</span>
                                    </div>
                                </div>

                                <!-- Question Text -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-2 flex items-center">
                                        <i class="fas fa-align-left mr-2"></i>Question Text
                                    </label>
                                    <div class="w-full rounded-lg bg-white/80 border border-[#FFC8FB]/50 px-4 py-3 shadow-sm">
                                        <p class="text-gray-800 leading-relaxed">{{ $quiz->question_text }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Multiple Choice Options -->
                    @if($quiz->type === 'multiple_choice' && $quiz->options)
                        <div class="bg-[#FFF0FA]/95 backdrop-blur-sm shadow-xl rounded-2xl border border-[#FFC8FB]/50 overflow-hidden">
                            <div class="bg-gradient-to-r from-[#FF92C2]/10 to-[#FFC8FB]/10 px-6 py-4 border-b border-[#FFC8FB]/30">
                                <h3 class="text-xl font-semibold text-[#FF92C2] flex items-center">
                                    <i class="fas fa-check-circle mr-3"></i>Answer Options
                                </h3>
                            </div>
                            
                            <div class="p-6 sm:p-8">
                                <div class="space-y-3">
                                    @foreach($quiz->options as $index => $option)
                                        <div class="flex items-center p-4 rounded-xl transition-all duration-200 {{ $option === $quiz->correct_answer ? 'bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-300 shadow-lg' : 'bg-white/80 border border-[#FFC8FB]/50 hover:shadow-md' }}">
                                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full font-bold text-lg {{ $option === $quiz->correct_answer ? 'bg-green-500 text-white shadow-md' : 'bg-[#FFF0FA] text-[#FF92C2] border border-[#FFC8FB]' }}">
                                                {{ chr(65 + $index) }}
                                            </div>
                                            <div class="ml-4 flex-1">
                                                <span class="{{ $option === $quiz->correct_answer ? 'text-green-800 font-semibold' : 'text-gray-700' }}">
                                                    {{ $option }}
                                                </span>
                                                @if($option === $quiz->correct_answer)
                                                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-500 text-white">
                                                        <i class="fas fa-check mr-1"></i>Correct
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Correct Answer for Non-Multiple Choice -->
                        <div class="bg-[#FFF0FA]/95 backdrop-blur-sm shadow-xl rounded-2xl border border-[#FFC8FB]/50 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-green-200">
                                <h3 class="text-xl font-semibold text-green-700 flex items-center">
                                    <i class="fas fa-bullseye mr-3"></i>Correct Answer
                                </h3>
                            </div>
                            
                            <div class="p-6 sm:p-8">
                                <div class="w-full rounded-lg bg-green-50 border-2 border-green-200 px-4 py-4 shadow-sm">
                                    <p class="text-green-800 font-semibold text-lg">{{ $quiz->correct_answer }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column - Template & Statistics -->
                <div class="space-y-8">
                    <!-- Template Information -->
                    <div class="bg-[#FFF0FA]/95 backdrop-blur-sm shadow-xl rounded-2xl border border-[#FFC8FB]/50 overflow-hidden">
                        <div class="bg-gradient-to-r from-[#FF92C2]/10 to-[#FFC8FB]/10 px-6 py-4 border-b border-[#FFC8FB]/30">
                            <h3 class="text-xl font-semibold text-[#FF92C2] flex items-center">
                                <i class="fas fa-file-alt mr-3"></i>Template Information
                            </h3>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <!-- Template Basic Info -->
                            @if($quiz->template_name || $quiz->template_description)
                                <div class="bg-white/70 rounded-lg p-4 border border-[#FFC8FB]/30">
                                    @if($quiz->template_name)
                                        <div class="mb-3">
                                            <label class="block text-xs font-semibold text-[#FF92C2] uppercase tracking-wider mb-1">Template Name</label>
                                            <p class="text-gray-800 font-medium">{{ $quiz->template_name }}</p>
                                        </div>
                                    @endif

                                    @if($quiz->template_description)
                                        <div>
                                            <label class="block text-xs font-semibold text-[#FF92C2] uppercase tracking-wider mb-1">Description</label>
                                            <p class="text-gray-600 text-sm leading-relaxed">{{ $quiz->template_description }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- CSV Headers -->
                            @if($quiz->csv_template_headers)
                                <div class="bg-white/70 rounded-lg p-4 border border-[#FFC8FB]/30">
                                    <label class="block text-xs font-semibold text-[#FF92C2] uppercase tracking-wider mb-3">CSV Headers</label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($quiz->csv_template_headers as $header)
                                            <span class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-[#FF92C2]/10 to-[#FFC8FB]/10 text-[#FF92C2] rounded-lg text-sm font-medium border border-[#FFC8FB]/30">
                                                <i class="fas fa-tag mr-1 text-xs"></i>{{ $header }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Response Statistics -->
                    <div class="bg-[#FFF0FA]/95 backdrop-blur-sm shadow-xl rounded-2xl border border-[#FFC8FB]/50 overflow-hidden">
                        <div class="bg-gradient-to-r from-[#FF92C2]/10 to-[#FFC8FB]/10 px-6 py-4 border-b border-[#FFC8FB]/30">
                            <h3 class="text-xl font-semibold text-[#FF92C2] flex items-center">
                                <i class="fas fa-chart-bar mr-3"></i>Response Statistics
                            </h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-4">
                                <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-2xl font-bold text-blue-700">{{ $quiz->answers->count() }}</div>
                                            <div class="text-sm text-blue-600 font-medium">Total Responses</div>
                                        </div>
                                        <i class="fas fa-users text-blue-400 text-2xl"></i>
                                    </div>
                                </div>
                                
                                <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-xl border border-green-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-2xl font-bold text-green-700">
                                                {{ $quiz->answers->where('is_correct', true)->count() }}
                                            </div>
                                            <div class="text-sm text-green-600 font-medium">Correct Answers</div>
                                        </div>
                                        <i class="fas fa-check-circle text-green-400 text-2xl"></i>
                                    </div>
                                </div>

                                @if($quiz->answers->count() > 0)
                                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-4 rounded-xl border border-purple-200">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-2xl font-bold text-purple-700">
                                                    {{ round(($quiz->answers->where('is_correct', true)->count() / $quiz->answers->count()) * 100) }}%
                                                </div>
                                                <div class="text-sm text-purple-600 font-medium">Success Rate</div>
                                            </div>
                                            <i class="fas fa-percentage text-purple-400 text-2xl"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template Preview Table (Full Width) -->
            @if($quiz->csv_template_headers && $templateData)
                <div class="mt-8 bg-[#FFF0FA]/95 backdrop-blur-sm shadow-xl rounded-2xl border border-[#FFC8FB]/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-[#FF92C2]/10 to-[#FFC8FB]/10 px-6 py-4 border-b border-[#FFC8FB]/30">
                        <h3 class="text-xl font-semibold text-[#FF92C2] flex items-center">
                            <i class="fas fa-table mr-3"></i>Template Preview
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Sample data structure for this template</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gradient-to-r from-[#FF92C2]/5 to-[#FFC8FB]/5">
                                <tr>
                                    @foreach($templateData['headers'] as $header)
                                        <th class="px-6 py-4 text-left text-sm font-bold text-[#FF92C2] uppercase tracking-wider border-b-2 border-[#FFC8FB]/30 bg-white/50">
                                            <div class="flex items-center">
                                                <i class="fas fa-columns mr-2 text-xs"></i>
                                                {{ $header }}
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white/80 divide-y divide-[#FFC8FB]/20">
                                @foreach($templateData['sample_data'] as $rowIndex => $row)
                                    <tr class="hover:bg-[#FFF0FA]/30 transition-colors duration-150">
                                        @foreach($row as $cellIndex => $cell)
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="flex items-center">
                                                    @if($cellIndex === 0)
                                                        <span class="w-6 h-6 flex items-center justify-center rounded-full bg-[#FF92C2]/10 text-[#FF92C2] text-xs font-bold mr-3">
                                                            {{ $rowIndex + 1 }}
                                                        </span>
                                                    @endif
                                                    <span class="text-gray-700 font-medium">{{ $cell }}</span>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('instructors.quizzes.edit', $quiz) }}" 
                   class="px-8 py-3 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] text-white rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 inline-flex items-center font-semibold">
                    <i class="fas fa-edit mr-2"></i>Edit Question
                </a>
            </div>
        </div>
    </div>

    @php
    function getSampleValueForHeader($header) {
        $headerLower = strtolower($header);
        
        if (str_contains($headerLower, 'student id')) return '2024001';
        if (str_contains($headerLower, 'name')) return 'John Doe';
        if (str_contains($headerLower, 'subject')) return 'CS101';
        if (str_contains($headerLower, 'section')) return 'Section A';
        if (str_contains($headerLower, 'question')) return 'Sample Question';
        if (str_contains($headerLower, 'answer')) return 'Sample Answer';
        if (str_contains($headerLower, 'point')) return '10';
        if (str_contains($headerLower, 'email')) return 'student@example.com';
        if (str_contains($headerLower, 'grade')) return '85';
        
        return 'Sample Data';
    }
    @endphp
</x-app-layout>