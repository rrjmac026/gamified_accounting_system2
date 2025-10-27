<x-app-layout>
    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('instructors.performance-tasks.answer-sheets.index') }}" 
               class="inline-flex items-center text-sm text-[#D5006D] hover:text-[#FF6F91] mb-3 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to tasks
            </a>
            <h1 class="text-2xl font-semibold text-[#D5006D]">{{ $task->title }}</h1>
            <p class="text-sm text-[#FF6F91] mt-1">Configure answer sheets for each step</p>
        </div>

        {{-- Step Boxes --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @for ($i = 1; $i <= 10; $i++)
                @php
                    $sheet = $answerSheets->firstWhere('step', $i);
                @endphp
                <a href="{{ route('instructors.performance-tasks.answer-sheets.edit', [$task->id, $i]) }}" 
                   class="group block">
                    <div class="relative bg-[#FAF3F3] rounded-xl border-2 
                        {{ $sheet ? 'border-[#D5006D]' : 'border-[#FF9AAB]' }}
                        p-4 h-36 flex flex-col justify-between hover:shadow-md hover:border-[#FF6F91] transition-all duration-300">

                        {{-- Header (Step + Icon) --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-[#D5006D]">Step {{ $i }}</span>
                            <div class="w-6 h-6 rounded-full flex items-center justify-center 
                                {{ $sheet ? 'bg-[#FF9AAB]' : 'bg-[#FAF3F3] border border-[#FF9AAB]' }}">
                                @if ($sheet)
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-[#FF9AAB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                @endif
                            </div>
                        </div>

                        {{-- Step Title (Optional: Show accounting step name) --}}
                        <div class="mt-2 flex-grow flex items-center">
                            <p class="text-xs font-medium text-[#D5006D] leading-snug text-center w-full">
                                {{ $stepTitles[$i] ?? 'Accounting Step' }}
                            </p>
                        </div>

                        {{-- Status --}}
                        <p class="text-xs text-center mt-2 {{ $sheet ? 'text-[#D5006D]' : 'text-[#FF6F91]' }}">
                            {{ $sheet ? 'Configured' : 'Not configured' }}
                        </p>

                        <div class="absolute inset-0 rounded-xl ring-2 ring-[#FF6F91] opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                    </div>
                </a>
            @endfor
        </div>

        {{-- Info Box --}}
        <div class="mt-8 p-4 bg-[#FAF3F3] border border-[#FF9AAB] rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-[#D5006D] mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-[#D5006D]">Configure Each Step</h3>
                    <p class="text-sm text-[#FF6F91] mt-1">
                        Click on any step to set up the answer sheet and grading criteria.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
