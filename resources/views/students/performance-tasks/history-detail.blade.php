<x-app-layout>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.3.0/dist/handsontable.full.min.css">
@endpush

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Breadcrumb --}}
        <nav class="mb-6 flex flex-wrap items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('students.performance-tasks.index') }}" class="hover:text-indigo-600 transition-colors">Tasks</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('students.performance-tasks.step', ['id' => $task->id, 'step' => $step]) }}" class="hover:text-indigo-600 transition-colors">
                Step {{ $step }} — {{ $stepTitles[$step] ?? '' }}
            </a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('students.performance-tasks.step-history', ['id' => $task->id, 'step' => $step]) }}" class="hover:text-indigo-600 transition-colors">History</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-800 font-medium">Attempt {{ $attempt }}</span>
        </nav>

        {{-- Header card --}}
        @php
            $statusColors = match($history->status) {
                'correct' => ['bar' => 'from-green-500 to-emerald-500', 'icon_bg' => 'bg-green-100', 'icon' => 'text-green-600', 'badge' => 'bg-green-100 text-green-800'],
                'passed'  => ['bar' => 'from-blue-500 to-indigo-500',   'icon_bg' => 'bg-blue-100',  'icon' => 'text-blue-600',  'badge' => 'bg-blue-100 text-blue-800'],
                'wrong'   => ['bar' => 'from-red-500 to-rose-500',      'icon_bg' => 'bg-red-100',   'icon' => 'text-red-600',   'badge' => 'bg-red-100 text-red-800'],
                default   => ['bar' => 'from-gray-400 to-gray-500',     'icon_bg' => 'bg-gray-100',  'icon' => 'text-gray-600',  'badge' => 'bg-gray-100 text-gray-800'],
            };
            $maxPerStep = ($task->max_score ?? 1000) / 10;
            $pct = $maxPerStep > 0 ? min(100, round(($history->score / $maxPerStep) * 100)) : 0;
            $statusLabel = match($history->status) {
                'correct' => 'Perfect', 'passed' => 'Passed', 'wrong' => 'Wrong', default => ucfirst($history->status)
            };
        @endphp

        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">
            <div class="h-2 bg-gradient-to-r {{ $statusColors['bar'] }}"></div>
            <div class="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 {{ $statusColors['icon_bg'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 {{ $statusColors['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <h1 class="text-2xl font-bold text-gray-900">Attempt {{ $attempt }}</h1>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $statusColors['badge'] }}">{{ $statusLabel }}</span>
                                @if($history->is_late)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-800">Late</span>
                                @endif
                            </div>
                            <p class="text-gray-500 text-sm">
                                Submitted {{ $history->created_at->format('F d, Y \a\t h:i A') }}
                                ({{ $history->created_at->diffForHumans() }})
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('students.performance-tasks.step-history', ['id' => $task->id, 'step' => $step]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back to History
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-xl font-bold text-gray-900">{{ number_format($history->score, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Score</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-xl font-bold text-gray-900">{{ $pct }}%</p>
                        <p class="text-xs text-gray-500 mt-0.5">Percentage</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm font-semibold text-gray-800 leading-snug">{{ $statusLabel }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Result</p>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $history->status === 'correct' ? 'bg-green-500' : ($history->status === 'passed' ? 'bg-blue-500' : 'bg-red-400') }}"
                             style="width: {{ $pct }}%; transition: width 1s ease;"></div>
                    </div>
                </div>

                @if($history->remarks)
                <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">System Remarks</p>
                    <p class="text-sm text-gray-700">{{ $history->remarks }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Spreadsheet card --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 3v18M14 3v18"/>
                    </svg>
                    <h2 class="font-semibold text-gray-800">Your Answer — Attempt {{ $attempt }}</h2>
                </div>
                <div class="hidden sm:flex items-center gap-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-green-200 border border-green-400 inline-block"></span>Correct
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-red-200 border border-red-400 inline-block"></span>Wrong
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-gray-100 border border-gray-300 inline-block"></span>Unchecked
                    </span>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <div class="border-2 border-gray-200 rounded-xl overflow-hidden bg-gray-50">
                    <div class="overflow-x-auto overflow-y-auto" style="max-height: 65vh; min-height: 300px; width: 100%;">
                        <div id="history-spreadsheet" class="bg-white"></div>
                    </div>
                </div>
                @if(!$history->submission_data)
                    <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700">
                        No spreadsheet data was saved for this attempt.
                    </div>
                @endif
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-wrap gap-3">
                <a href="{{ route('students.performance-tasks.step-history', ['id' => $task->id, 'step' => $step]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    All Attempts
                </a>
                <a href="{{ route('students.performance-tasks.step', ['id' => $task->id, 'step' => $step]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Back to Step
                </a>
            </div>
        </div>

    </div>
    </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/handsontable@14.3.0/dist/handsontable.full.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container   = document.getElementById('history-spreadsheet');
        const historyData = @json($history->submission_data);
        const correctRaw  = @json($exercise->correct_data ?? $answerSheet->correct_data ?? null);
        const step        = {{ (int) $step }};

        if (!historyData) {
            container.innerHTML = '<div class="p-8 text-center text-gray-400">No data saved for this attempt.</div>';
            return;
        }

        function parseSpreadsheetData(raw) {
            try {
                let parsed = raw;
                while (typeof parsed === 'string') { parsed = JSON.parse(parsed); }
                if (parsed && !Array.isArray(parsed) && parsed.data) {
                    return { data: parsed.data, metadata: parsed.metadata || null };
                }
                return { data: Array.isArray(parsed) ? parsed : null, metadata: null };
            } catch (e) {
                return { data: null, metadata: null };
            }
        }

        const { data: displayData, metadata } = parseSpreadsheetData(historyData);
        if (!displayData) {
            container.innerHTML = '<div class="p-8 text-center text-gray-400">Could not parse submission data.</div>';
            return;
        }

        const { data: correctData } = correctRaw ? parseSpreadsheetData(correctRaw) : { data: null };

        // Normalize — mirrors PHP normalizeValue()
        const normalizeVal = (val) => {
            if (val === null || val === undefined || val === '') return '';
            const s = String(val).trim().replace(/[₱,\s]/g, '').toLowerCase();
            const n = parseFloat(s);
            return isNaN(n) ? s : n.toFixed(2);
        };

        // Build per-cell correct/wrong lookup
        const cellStatus = [];
        if (correctData) {
            displayData.forEach((row, rIdx) => {
                cellStatus[rIdx] = [];
                (Array.isArray(row) ? row : []).forEach((cell, cIdx) => {
                    const hasValue = cell !== null && cell !== undefined && String(cell).trim() !== '';
                    if (!hasValue) { cellStatus[rIdx][cIdx] = null; return; }
                    const correctVal = correctData[rIdx]?.[cIdx];
                    cellStatus[rIdx][cIdx] = normalizeVal(cell) === normalizeVal(correctVal) ? 'correct' : 'wrong';
                });
            });
        }

        function pesoRenderer(instance, td, row, col, prop, value) {
            Handsontable.renderers.NumericRenderer.apply(this, arguments);
            if (value !== null && value !== undefined && value !== '') {
                const num = typeof value === 'number' ? value : parseFloat(String(value).replace(/[,₱\s]/g, ''));
                if (!isNaN(num)) {
                    td.innerHTML = '&#8369;' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
            return td;
        }

        function cellsCallback(row, col) {
            const props = {};

            if (step === 4) {
                if (row <= 2) {
                    if (col !== 1) {
                        props.renderer = function(inst, td) { td.innerHTML = ''; td.style.background = 'white'; td.style.border = 'none'; };
                        return props;
                    }
                    props.renderer = function(inst, td, r, c, p, value) {
                        Handsontable.renderers.TextRenderer.apply(this, arguments);
                        td.innerHTML = '<strong>' + (value || '') + '</strong>';
                        td.style.textAlign = 'center';
                        if (row === 0) td.style.fontSize = '16px';
                    };
                    return props;
                }
                if (row === 3) {
                    props.renderer = function(inst, td, r, c, p, value) {
                        Handsontable.renderers.TextRenderer.apply(this, arguments);
                        td.innerHTML = '<strong>' + (value || '') + '</strong>';
                        td.style.textAlign = 'center';
                        td.style.backgroundColor = '#f3f4f6';
                    };
                    return props;
                }
            }

            props.renderer = function(inst, td, r, c, p, value, cellProps) {
                if (step === 4 && c > 0) {
                    pesoRenderer.apply(this, arguments);
                } else {
                    Handsontable.renderers.TextRenderer.apply(this, arguments);
                }
                if (metadata && metadata[r]?.[c]?.bold) td.style.fontWeight = 'bold';

                const status = cellStatus[r]?.[c];
                if (status === 'correct') {
                    td.style.backgroundColor = '#dcfce7';
                    td.style.color = '#166534';
                } else if (status === 'wrong') {
                    td.style.backgroundColor = '#fee2e2';
                    td.style.color = '#991b1b';
                }
            };

            return props;
        }

        // Calculate column count from data
        const maxCols = Math.max(...displayData.map(r => Array.isArray(r) ? r.length : 0), 5);

        new Handsontable(container, {
            data              : displayData,
            readOnly          : true,
            rowHeaders        : true,
            colHeaders        : true,
            width             : '100%',
            height            : window.innerWidth < 640 ? 350 : 500,
            stretchH          : 'none',       // ✅ don't stretch — let columns be natural width
            colWidths         : 120,          // ✅ fixed width per column so they don't collapse
            licenseKey        : 'non-commercial-and-evaluation',
            cells             : cellsCallback,
            columnSorting     : false,
            manualColumnResize: true,
            contextMenu       : false,
            renderAllRows     : false,
        });
    });
</script>
@endpush

    <style>
        #history-spreadsheet .htCore td { border-color: #d1d5db; }
        #history-spreadsheet ::-webkit-scrollbar { width: 6px; height: 6px; }
        #history-spreadsheet ::-webkit-scrollbar-track { background: transparent; }
        #history-spreadsheet ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }
    </style>

</x-app-layout>