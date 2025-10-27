<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Performance Task Progress') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            {{-- Card Container --}}
            <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl p-8 border border-pink-200/40">

                {{-- Header --}}
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">
                        {{ $performanceTask->title ?? 'Your Accounting Cycle Task' }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        Complete all 10 steps of the accounting cycle to finish your performance task.  
                        You can retry each step up to <strong>2 attempts</strong>.
                    </p>
                </div>

                {{-- Progress Bar --}}
                @php
                    $completedSteps = $completedSteps ?? [];
                    $progress = count($completedSteps) / 10 * 100;
                @endphp

                <div class="w-full bg-gray-200 rounded-full h-3 mb-8 overflow-hidden">
                    <div class="bg-pink-500 h-3 transition-all duration-500" style="width: {{ $progress }}%;"></div>
                </div>
                <p class="text-sm text-gray-600 mb-6">
                    Progress: {{ count($completedSteps) }} / 10 steps completed
                </p>

                {{-- Step List --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @for ($i = 1; $i <= 10; $i++)
                        @php
                            $isCompleted = in_array($i, $completedSteps);
                            $isNext = $i === (count($completedSteps) + 1);
                        @endphp

                        <div class="relative group border border-gray-200 hover:border-pink-300 rounded-xl p-5 bg-gradient-to-b from-white to-pink-50 shadow-sm hover:shadow-md transition-all">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="text-lg font-semibold text-gray-800">
                                    Step {{ $i }}
                                </h4>
                                @if ($isCompleted)
                                    <span class="text-green-600 text-sm font-medium">✓ Completed</span>
                                @elseif ($isNext)
                                    <span class="text-pink-500 text-sm font-medium">Next →</span>
                                @else
                                    <span class="text-gray-400 text-sm font-medium">Pending</span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-600 mb-4">
                                {{ [
                                    1 => 'Analyze Transactions',
                                    2 => 'Journalize Entries',
                                    3 => 'Post to Ledger',
                                    4 => 'Prepare Trial Balance',
                                    5 => 'Adjusting Entries',
                                    6 => 'Prepare Adjusted Trial Balance',
                                    7 => 'Prepare Worksheet',
                                    8 => 'Prepare Financial Statements',
                                    9 => 'Prepare Closing Entries',
                                    10 => 'Prepare Post-Closing Trial Balance'
                                ][$i] }}
                            </p>

                            <div class="flex justify-end">
                                @if ($isCompleted)
                                    <a href="{{ route('students.performance-tasks.step', ['id' => $performanceTask->id, 'step' => $i]) }}"
                                       class="text-sm font-medium px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition">
                                        Review
                                    </a>
                                @elseif ($isNext || $i === 1)
                                    <a href="{{ route('students.performance-tasks.step', ['id' => $performanceTask->id, 'step' => $i]) }}"
                                       class="text-sm font-medium px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition">
                                        Continue
                                    </a>
                                @else
                                    <button disabled
                                            class="text-sm font-medium px-4 py-2 bg-gray-200 text-gray-400 rounded-lg cursor-not-allowed">
                                        Locked
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</x-app-layout>