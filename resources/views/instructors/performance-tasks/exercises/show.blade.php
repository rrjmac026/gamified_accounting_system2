<x-app-layout>
    <div class="py-6 sm:py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Back + Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <a href="{{ route('instructors.performance-tasks.show', $task->id) }}"
                       class="inline-flex items-center text-[#D5006D] hover:text-[#FF6F91] font-medium text-sm mb-2">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Task
                    </a>
                    <h1 class="text-2xl font-bold text-gray-800">Manage Exercises</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Task: <span class="font-semibold text-gray-700">{{ $task->title }}</span>
                    </p>
                </div>

                {{-- Step summary badges --}}
                <div class="flex flex-col items-end gap-2">
                    <div class="flex items-center gap-2 px-4 py-2 bg-pink-50 border border-pink-200 rounded-xl text-sm text-pink-700">
                        <i class="fas fa-check-circle text-[#D5006D]"></i>
                        <span><strong>{{ count($stepTitles) }}</strong> of 10 steps enabled</span>
                    </div>
                    <a href="{{ route('instructors.performance-tasks.edit', $task->id) }}"
                       class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-[#D5006D] transition-colors">
                        <i class="fas fa-sliders-h"></i> Change enabled steps
                    </a>
                </div>
            </div>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            {{-- All 10 steps — enabled ones are interactive, disabled ones are grayed out --}}
            @php
                $allStepTitles = [
                    1  => 'Analyze Transactions',
                    2  => 'Journalize Transactions',
                    3  => 'Post to Ledger Accounts',
                    4  => 'Prepare Trial Balance',
                    5  => 'Journalize & Post Adjusting Entries',
                    6  => 'Prepare Adjusted Trial Balance',
                    7  => 'Prepare Financial Statements',
                    8  => 'Journalize & Post Closing Entries',
                    9  => 'Prepare Post-Closing Trial Balance',
                    10 => 'Reverse (Optional Step)',
                ];

                $enabledSteps = $task->enabled_steps_list; // e.g. [1, 3, 5]
            @endphp

            <div class="space-y-3">
                @foreach($allStepTitles as $stepNumber => $title)
                    @php
                        $isEnabled = in_array($stepNumber, $enabledSteps);
                        $exercises = $isEnabled ? $exercisesByStep->get($stepNumber, collect()) : collect();
                        $count     = $exercises->count();
                    @endphp

                    @if($isEnabled)
                        {{-- ── ENABLED STEP ─────────────────────────────────── --}}
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                                <div class="flex items-center gap-3">
                                    {{-- Step number badge --}}
                                    <span class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold
                                        {{ $count > 0 ? 'bg-[#D5006D] text-white' : 'bg-pink-100 text-[#D5006D]' }}">
                                        {{ $stepNumber }}
                                    </span>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-semibold text-gray-800 text-sm">{{ $title }}</p>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">
                                                <i class="fas fa-circle text-[6px]"></i> Enabled
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $count > 0 ? $count . ' exercise' . ($count > 1 ? 's' : '') : 'No exercises yet' }}
                                        </p>
                                    </div>
                                </div>

                                <a href="{{ route('instructors.performance-tasks.exercises.create', [$task->id, $stepNumber]) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white
                                          bg-gradient-to-r from-[#D5006D] to-[#FF6F91] rounded-lg hover:opacity-90 transition-all shadow-sm">
                                    <i class="fas fa-plus"></i> Add Exercise
                                </a>
                            </div>

                            {{-- Exercise rows --}}
                            @if($count > 0)
                                <ul class="divide-y divide-gray-50">
                                    @foreach($exercises as $exercise)
                                        <li class="flex items-center justify-between px-5 py-3 hover:bg-pink-50/40 transition-colors">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <span class="w-6 h-6 flex-shrink-0 flex items-center justify-center
                                                             bg-pink-100 text-[#D5006D] text-xs font-bold rounded-full">
                                                    {{ $exercise->order }}
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $exercise->title }}</p>
                                                    @if($exercise->description)
                                                        <p class="text-xs text-gray-400 truncate">{{ $exercise->description }}</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                                                <a href="{{ route('instructors.performance-tasks.exercises.edit', [$task, $exercise]) }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium
                                                          text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                                    <i class="fas fa-pencil-alt"></i> Edit
                                                </a>

                                                <form method="POST"
                                                      action="{{ route('instructors.performance-tasks.exercises.destroy', [$task, $exercise]) }}"
                                                      onsubmit="return confirm('Delete this exercise? This cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium
                                                                   text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                    @else
                        {{-- ── DISABLED STEP (grayed out, no actions) ───────── --}}
                        <div class="flex items-center gap-3 px-5 py-3.5 bg-gray-50 rounded-xl border border-dashed border-gray-200 opacity-60">
                            <span class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold bg-gray-200 text-gray-400 flex-shrink-0">
                                {{ $stepNumber }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-medium text-gray-400">{{ $title }}</p>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-200 text-gray-500">
                                        <i class="fas fa-ban text-[8px]"></i> Disabled
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400">Not visible to students</p>
                            </div>
                            <a href="{{ route('instructors.performance-tasks.edit', $task->id) }}"
                               class="flex-shrink-0 text-xs text-gray-400 hover:text-[#D5006D] transition-colors underline underline-offset-2">
                                Enable in task settings
                            </a>
                        </div>
                    @endif

                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>