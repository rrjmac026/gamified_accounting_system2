<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            {{-- Breadcrumb --}}
            <nav class="mb-6 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('students.performance-tasks.index') }}" class="hover:text-indigo-600 transition-colors">Tasks</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('students.performance-tasks.step', ['id' => $performanceTask->id, 'step' => $step]) }}" class="hover:text-indigo-600 transition-colors">
                    Step {{ $step }} — {{ $stepTitles[$step] ?? '' }}
                </a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-800 font-medium">Attempt History</span>
            </nav>

            {{-- Header --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">
                <div class="h-2 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Attempt History</h1>
                                <p class="text-sm text-gray-500 mt-1">
                                    Step {{ $step }}: {{ $stepTitles[$step] ?? '' }} &mdash; {{ $performanceTask->title }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('students.performance-tasks.step', ['id' => $performanceTask->id, 'step' => $step]) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Back to Step
                        </a>
                    </div>
                </div>
            </div>

            {{-- Attempts List --}}
            @if($histories->isEmpty())
                <div class="bg-white rounded-xl shadow-md border border-gray-200 p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-500 font-medium">No attempts recorded for this step yet.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($histories as $history)
                        @php
                            $statusColors = match($history->status) {
                                'correct' => ['bar' => 'from-green-500 to-emerald-500', 'badge' => 'bg-green-100 text-green-800', 'icon_bg' => 'bg-green-100', 'icon' => 'text-green-600'],
                                'passed'  => ['bar' => 'from-blue-500 to-indigo-500',   'badge' => 'bg-blue-100 text-blue-800',  'icon_bg' => 'bg-blue-100',  'icon' => 'text-blue-600'],
                                'wrong'   => ['bar' => 'from-red-500 to-rose-500',      'badge' => 'bg-red-100 text-red-800',   'icon_bg' => 'bg-red-100',   'icon' => 'text-red-600'],
                                default   => ['bar' => 'from-gray-400 to-gray-500',     'badge' => 'bg-gray-100 text-gray-800', 'icon_bg' => 'bg-gray-100',  'icon' => 'text-gray-600'],
                            };
                            $maxPerStep = ($performanceTask->max_score ?? 1000) / 10;
                            $pct = $maxPerStep > 0 ? min(100, round(($history->score / $maxPerStep) * 100)) : 0;
                            $statusLabel = match($history->status) {
                                'correct' => 'Perfect', 'passed' => 'Passed', 'wrong' => 'Wrong', default => ucfirst($history->status ?? 'N/A')
                            };
                        @endphp

                        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                            <div class="h-1.5 bg-gradient-to-r {{ $statusColors['bar'] }}"></div>
                            <div class="p-5 sm:p-6">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 {{ $statusColors['icon_bg'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="text-sm font-bold {{ $statusColors['icon'] }}">#{{ $history->attempt_number }}</span>
                                        </div>
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="font-semibold text-gray-900">Attempt {{ $history->attempt_number }}</span>
                                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $statusColors['badge'] }}">{{ $statusLabel }}</span>
                                                @if($history->is_late)
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800">Late</span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $history->created_at->format('M d, Y \a\t h:i A') }}
                                                ({{ $history->created_at->diffForHumans() }})
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-6 text-center">
                                        <div>
                                            <p class="text-lg font-bold text-gray-900">{{ number_format($history->score, 2) }}</p>
                                            <p class="text-xs text-gray-500">Score</p>
                                        </div>
                                        <div>
                                            <p class="text-lg font-bold text-gray-900">{{ $pct }}%</p>
                                            <p class="text-xs text-gray-500">Percentage</p>
                                        </div>
                                        <a href="{{ route('students.performance-tasks.history-detail', ['id' => $performanceTask->id, 'step' => $step, 'attempt' => $history->attempt_number]) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-xs font-medium">
                                            View
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>

                                {{-- Progress bar --}}
                                <div class="mt-4">
                                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $history->status === 'correct' ? 'bg-green-500' : ($history->status === 'passed' ? 'bg-blue-500' : 'bg-red-400') }}"
                                             style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>

                                @if($history->remarks)
                                    <p class="mt-3 text-xs text-gray-600 bg-gray-50 rounded-lg px-3 py-2 border border-gray-100">
                                        {{ $history->remarks }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>