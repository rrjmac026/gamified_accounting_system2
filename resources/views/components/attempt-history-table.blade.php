@props(['histories', 'task', 'step'])

<div class="space-y-4">
    @foreach($histories->sortByDesc('attempt_number') as $h)
    @php
        $colors = match($h->status) {
            'correct'     => ['ring' => 'ring-green-200',  'accent' => 'bg-green-500',  'badge' => 'bg-green-100 text-green-800',   'icon_bg' => 'bg-green-100',  'icon_text' => 'text-green-600'],
            'passed'      => ['ring' => 'ring-blue-200',   'accent' => 'bg-blue-500',   'badge' => 'bg-blue-100 text-blue-800',     'icon_bg' => 'bg-blue-100',   'icon_text' => 'text-blue-600'],
            'wrong'       => ['ring' => 'ring-red-200',    'accent' => 'bg-red-500',    'badge' => 'bg-red-100 text-red-800',       'icon_bg' => 'bg-red-100',    'icon_text' => 'text-red-600'],
            default       => ['ring' => 'ring-gray-200',   'accent' => 'bg-gray-400',   'badge' => 'bg-gray-100 text-gray-700',     'icon_bg' => 'bg-gray-100',   'icon_text' => 'text-gray-500'],
        };

        $statusLabel = match($h->status) {
            'correct'     => 'Perfect',
            'passed'      => 'Passed',
            'wrong'       => 'Wrong',
            default       => ucfirst($h->status),
        };

        $isBest = ($h->score == $histories->max('score') && $h->score > 0);
        $isLatest = ($h->attempt_number == $histories->max('attempt_number'));

        // Score percentage relative to task max per step (max_score / 10)
        $maxPerStep = ($task->max_score ?? 1000) / 10;
        $pct = $maxPerStep > 0 ? min(100, round(($h->score / $maxPerStep) * 100)) : 0;
    @endphp

    <div class="bg-white rounded-xl shadow-sm ring-1 {{ $colors['ring'] }} overflow-hidden
                transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">

        {{-- Left accent bar --}}
        <div class="flex">
            <div class="w-1.5 flex-shrink-0 {{ $colors['accent'] }}"></div>

            <div class="flex-1 p-5">
                {{-- ── Top row ─────────────────────────────────────────────── --}}
                <div class="flex flex-wrap items-start justify-between gap-3 mb-4">

                    {{-- Attempt number + badges --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-full {{ $colors['icon_bg'] }} flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold {{ $colors['icon_text'] }}">
                                    {{ $h->attempt_number }}
                                </span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm leading-tight">
                                    Attempt {{ $h->attempt_number }}
                                </p>
                                <p class="text-xs text-gray-400 leading-tight">
                                    {{ $h->created_at->format('M d, Y · h:i A') }}
                                </p>
                            </div>
                        </div>

                        {{-- Status badge --}}
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors['badge'] }}">
                            @if($h->status === 'correct')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @elseif($h->status === 'wrong')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                            {{ $statusLabel }}
                        </span>

                        @if($isBest)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                Best
                            </span>
                        @endif

                        @if($isLatest)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                                Latest
                            </span>
                        @endif

                        @if($h->is_late)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                ⚠ Late
                            </span>
                        @endif
                    </div>

                    {{-- View detail button --}}
                    <a href="{{ route('students.performance-tasks.history-detail', ['id' => $task->id, 'step' => $step, 'attempt' => $h->attempt_number]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-700
                              border border-indigo-200 rounded-lg text-xs font-medium
                              hover:bg-indigo-100 hover:border-indigo-300 transition-colors flex-shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Review Answer
                    </a>
                </div>

                {{-- ── Score bar + stats ───────────────────────────────────── --}}
                <div class="space-y-3">
                    {{-- Score progress bar --}}
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-xs text-gray-500 font-medium">Score</span>
                            <span class="text-sm font-bold text-gray-900">
                                {{ number_format($h->score, 2) }}
                                <span class="text-gray-400 font-normal text-xs">/ {{ number_format($maxPerStep, 0) }}</span>
                            </span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700
                                        {{ $h->status === 'correct' ? 'bg-green-500' : ($h->status === 'passed' ? 'bg-blue-500' : 'bg-red-400') }}"
                                 style="width: {{ $pct }}%">
                            </div>
                        </div>
                        <p class="text-right text-xs text-gray-400 mt-0.5">{{ $pct }}%</p>
                    </div>

                    {{-- Quick stats --}}
                    <div class="flex flex-wrap gap-4 pt-1 border-t border-gray-100 text-xs text-gray-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                            </svg>
                            {{ $h->error_count }} {{ Str::plural('error', $h->error_count) }}
                        </span>

                        @if($h->remarks)
                        <span class="flex items-center gap-1 flex-1 min-w-0">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            <span class="truncate">{{ $h->remarks }}</span>
                        </span>
                        @endif
                    </div>
                </div>

            </div>{{-- end flex-1 --}}
        </div>{{-- end flex --}}
    </div>{{-- end card --}}
    @endforeach
</div>