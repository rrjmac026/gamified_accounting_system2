<x-app-layout>
    {{-- ═══════════════════════════ jSpreadsheet CDN ═══════════════════════════ --}}
    <script src="https://cdn.jsdelivr.net/npm/jspreadsheet-ce@4.13.4/dist/index.js"></script>
    <link  rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jspreadsheet-ce@4.13.4/dist/jspreadsheet.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jsuites/dist/jsuites.js"></script>
    <link  rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsuites/dist/jsuites.css" />

    <style>
        body { overflow-x: hidden; }
        #spreadsheet { width: 100%; }
        #spreadsheet .jexcel_content { overflow: auto; }
        .jexcel td { border-color: #d1d5db !important; }

        /* Row 1 — main headers (copied from answer sheet) */
        .jexcel tbody tr:nth-child(1) td {
            font-weight: 700 !important;
            text-align: center !important;
            background-color: #e5e7eb !important;
            white-space: normal !important;
            word-break: break-word !important;
            border-bottom: 2px solid #6b7280 !important;
        }

        /* Row 2 — sub-labels (copied from answer sheet) */
        .jexcel tbody tr:nth-child(2) td {
            font-weight: 700 !important;
            text-align: center !important;
            background-color: #f3f4f6 !important;
            border-bottom: 2px solid #6b7280 !important;
        }

        /* Answer feedback cell colours */
        .jexcel td.cell-correct {
            background-color: #dcfce7 !important;
            border: 2px solid #16a34a !important;
            color: #166534 !important;
        }
        .jexcel td.cell-wrong {
            background-color: #fee2e2 !important;
            border: 2px solid #dc2626 !important;
            color: #991b1b !important;
        }

        /* Selection tint */
        .jexcel td.highlight { background-color: rgba(16,185,129,.08) !important; }

        /* Scrollbar */
        #spreadsheet ::-webkit-scrollbar        { width: 6px; height: 6px; }
        #spreadsheet ::-webkit-scrollbar-track  { background: transparent; }
        #spreadsheet ::-webkit-scrollbar-thumb  { background: #d1d5db; border-radius: 9999px; }

        @media (max-width: 640px)                        { .jexcel td, .jexcel th { font-size: 12px; padding: 4px; } }
        @media (min-width: 640px) and (max-width: 1024px) { .jexcel td, .jexcel th { font-size: 13px; } }

        /* Animation for flash messages */
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-slideDown { animation: slideDown 0.3s ease-out; }
    </style>

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-10">

            {{-- ── Flash messages ──────────────────────────────────────────── --}}
            <div class="mb-6 space-y-4">
                @if (session('error'))
                    <div class="animate-slideDown">
                        <div class="flex items-start gap-3 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-red-800 mb-1">Error</h3>
                                <p class="text-sm text-red-700 leading-relaxed">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                @if (session('success'))
                    <div class="animate-slideDown">
                        <div class="flex items-start gap-3 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-sm">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-green-800 mb-1">Success</h3>
                                <p class="text-sm text-green-700 leading-relaxed">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <x-view-answers-button :submission="$submission" :performanceTask="$performanceTask" :step="$step" />

            {{-- ── Page Header ──────────────────────────────────────────────── --}}
            <div class="mb-6 sm:mb-8">
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                    <div class="h-2 bg-gradient-to-r from-emerald-500 via-teal-600 to-cyan-600"></div>

                    <div class="p-6 sm:p-8">
                        <!-- Step Indicator and Progress -->
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 rounded-full text-sm font-semibold border border-emerald-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                                <span>Step 5 of 10</span>
                            </div>

                            <div class="flex-1 max-w-xs">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 h-full rounded-full transition-all duration-500" style="width: 50%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">50%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight mb-2">
                                    Adjusting Entries
                                </h1>
                                <p class="text-base sm:text-lg text-gray-600 leading-relaxed">
                                    Record adjusting entries for accrued, deferred, and estimated items before preparing financial statements.
                                </p>
                            </div>
                        </div>

                        <!-- Meta Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center gap-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                                <div class="flex-shrink-0 w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-amber-600 font-medium">Attempts Remaining</p>
                                    <p class="text-lg font-bold text-amber-900">{{ $performanceTask->max_attempts - ($submission->attempts ?? 0) }}/{{ $performanceTask->max_attempts }}</p>
                                </div>
                            </div>

                            @if($submission && $submission->status)
                            <div class="flex items-center gap-3 p-3 {{ $submission->status === 'correct' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} rounded-lg border">
                                <div class="flex-shrink-0 w-10 h-10 {{ $submission->status === 'correct' ? 'bg-green-100' : 'bg-red-100' }} rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 {{ $submission->status === 'correct' ? 'text-green-600' : 'text-red-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                        @if($submission->status === 'correct')
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        @else
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        @endif
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs {{ $submission->status === 'correct' ? 'text-green-600' : 'text-red-600' }} font-medium">Status</p>
                                    <p class="text-lg font-bold {{ $submission->status === 'correct' ? 'text-green-900' : 'text-red-900' }}">{{ ucfirst($submission->status) }}</p>
                                </div>
                            </div>
                            @endif

                            @if(isset($submission->score) && ($submission->attempts ?? 0) >= 2)
                            <div class="flex items-center gap-3 p-3 bg-purple-50 rounded-lg border border-purple-200">
                                <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-purple-600 font-medium">Score</p>
                                     <p class="text-lg font-bold text-purple-900">{{ number_format($submission->score, 2) }} pts</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Main Content Card ────────────────────────────────────────── --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">

                <!-- Instructions -->
                <div class="p-4 sm:p-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-emerald-100">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-emerald-900 mb-1">Adjusting Entry Instructions</h3>
                            <div class="text-sm text-emerald-800 leading-relaxed">
                                {!! $performanceTask->description ?? 'Record adjusting entries with proper account titles, references, and the corresponding debit and credit amounts.' !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Form ──────────────────────────────────────────────────── --}}
                <form id="saveForm"
                      method="POST"
                      action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => 5]) }}">
                    @csrf

                    <div class="p-3 sm:p-4 lg:p-6">
                        <div class="border-2 border-gray-300 rounded-xl shadow-inner bg-gray-50 overflow-hidden">
                            <div class="overflow-x-auto overflow-y-auto"
                                 style="max-height: calc(100vh - 400px); min-height: 400px;">
                                <div id="spreadsheet" class="bg-white min-w-full"></div>
                            </div>
                            <input type="hidden" name="submission_data" id="submission_data" required>
                        </div>

                        <div class="mt-3 flex items-center justify-center gap-2 text-xs text-gray-500 sm:hidden">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                            <span>Swipe to scroll spreadsheet</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="p-4 sm:p-6 bg-gray-50 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-4">
                            <button type="button" onclick="window.history.back()"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-white text-gray-700 border-2 border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all text-sm font-medium">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back
                            </button>
                            <x-step-history-button :performanceTask="$performanceTask" :step="$step" :submission="$submission" />
                            <button type="submit" id="submitButton"

                            <button type="submit" id="submitButton"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg hover:from-emerald-700 hover:to-teal-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all text-sm font-semibold shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                {{ ($submission->attempts ?? 0) >= $performanceTask->max_attempts ? 'disabled' : '' }}>
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save and Continue
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- ═══════════════════ jSpreadsheet initialisation ═══════════════════ --}}
    {{-- Structure copied exactly from instructor answer sheet step 5        --}}
    <script>
    (function () {

        const container = document.getElementById('spreadsheet');

        // ── PHP → JS data ────────────────────────────────────────────────────
        const savedData        = @json($submission->submission_data ?? null);
        const correctData      = @json($answerSheet->correct_data ?? null);
        const submissionStatus = @json($submission->status ?? null);
        const maxAttempts      = @json($performanceTask->max_attempts);
        const currentAttempts  = @json($submission->attempts ?? 0);
        const isReadOnly       = currentAttempts >= maxAttempts;

        // ── Constants (identical to answer sheet) ─────────────────────────────
        const HEADER_ROWS   = 2;
        const COL_COUNT     = 6;
        const MIN_DATA_ROWS = 15;

        // ── Header row (identical to answer sheet) ────────────────────────────
        const headerRow1 = ['Date', '', 'Account Titles and Explanation', 'Account Number', 'Debit (₱)', 'Credit (₱)'];
        const blankRow   = () => Array(COL_COUNT).fill('');

        // ── Restore saved data (same logic as answer sheet) ───────────────────
        let dataRows;
        if (savedData) {
            const parsed = JSON.parse(savedData);
            dataRows = parsed.length <= MIN_DATA_ROWS
                ? parsed                     // old format — no headers stored
                : parsed.slice(HEADER_ROWS); // new format — strip 2 header rows
        } else {
            dataRows = Array(MIN_DATA_ROWS).fill(null).map(blankRow);
        }

        while (dataRows.length < MIN_DATA_ROWS) dataRows.push(blankRow());

        const fullData = [headerRow1, ...dataRows];

        // ── Merge cells — "Date" spans cols A-B in row 1 (identical to answer sheet) ──
        const mergeCells = { 'A1': [2, 1] };

        // ── Cell styles (identical to answer sheet) ───────────────────────────
        const cellStyle = {};

        // Row 1: main headers — grey background, bold, centred
        ['A1','B1','C1','D1','E1','F1'].forEach(ref => {
            cellStyle[ref] = 'font-weight:700;text-align:center;background:#e5e7eb;white-space:normal;word-break:break-word;border-bottom:2px solid #6b7280;';
        });

        // Row 2: sub-labels — light grey, bold, centred
        ['A2','B2','C2','D2','E2','F2'].forEach(ref => {
            cellStyle[ref] = 'font-weight:700;text-align:center;background:#f3f4f6;border-bottom:2px solid #6b7280;';
        });

        // ── Responsive dimensions ─────────────────────────────────────────────
        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;

        // ── Init jSpreadsheet (identical to answer sheet + student flags) ─────
        const table = jspreadsheet(container, {
            data             : fullData,
            minDimensions    : [COL_COUNT, fullData.length],
            defaultColWidth  : isMobile ? 120 : 150,
            mergeCells       : mergeCells,
            style            : cellStyle,
            tableWidth       : '100%',
            tableOverflow    : true,
            tableHeight      : isMobile ? '350px' : (isTablet ? '450px' : '500px'),
            allowFormulas    : true,
            columnSorting    : false,
            columnDrag       : false,
            rowDrag          : false,
            allowInsertRow   : !isReadOnly,
            allowInsertColumn: false,
            allowDeleteRow   : !isReadOnly,
            allowDeleteColumn: false,
            columnResize     : true,
            rowResize        : true,
            copyCompatibility: true,
            editable         : !isReadOnly,
            minSpareRows     : isReadOnly ? 0 : 1,

            columns: [],

            contextMenu: isReadOnly ? false : function (obj, x, y, e) {
                return [
                    { title: 'Insert row above', onclick: () => obj.insertRow(1, parseInt(y), true) },
                    { title: 'Insert row below', onclick: () => obj.insertRow(1, parseInt(y)) },
                    { title: 'Delete row',       onclick: () => obj.deleteRow(parseInt(y)) },
                    { type: 'line' },
                    { title: 'Copy',  onclick: () => obj.copy(true) },
                    { title: 'Paste', onclick: () => {
                        if (navigator.clipboard) {
                            navigator.clipboard.readText().then(t => obj.paste(x, y, t));
                        }
                    }},
                ];
            },

            onload  : function () { applyBorders(); applyAnswerStyles(); },
            onchange: function () { applyBorders(); applyAnswerStyles(); },
        });

        // ── Right-border on Credit column (col F / index 5) ──────────────────
        // Mirrors the HOT afterRenderer: TD.style.borderRight = '3px solid #000000'
        function applyBorders() {
            const tbody = container.querySelector('.jexcel tbody');
            if (!tbody) return;
            tbody.querySelectorAll('tr').forEach((tr, rowIdx) => {
                if (rowIdx < HEADER_ROWS) return;   // skip header rows
                const cells = tr.querySelectorAll('td');
                cells.forEach((td, tdIdx) => {
                    if (tdIdx === 0) return;         // skip row-number td
                    const colIdx = tdIdx - 1;        // real 0-based data column
                    td.style.borderRight = '';
                    if (colIdx === 5) {              // Credit = last data col
                        td.style.borderRight = '3px solid #000000';
                    }
                });
            });
        }

        setTimeout(applyBorders, 100);

        // ── Answer-checking colour helper ─────────────────────────────────────
        function applyAnswerStyles() {
            if (!submissionStatus || !correctData || !savedData) return;

            try {
                const parsedCorrect = typeof correctData === 'string' ? JSON.parse(correctData) : correctData;
                const data = table.getData();

                // Start from row index 1 — skip the 1 rendered header row (index 0)
                for (let r = 1; r < data.length; r++) {
                    for (let c = 0; c < COL_COUNT; c++) {
                        const studentVal = data[r][c];
                        const correctVal = parsedCorrect[r] ? parsedCorrect[r][c] : undefined;

                        const td = container.querySelector(
                            `.jexcel tbody tr:nth-child(${r + 1}) td:nth-child(${c + 2})`
                        );
                        if (!td) continue;

                        td.classList.remove('cell-correct', 'cell-wrong');

                        if (studentVal !== null && studentVal !== undefined && String(studentVal).trim() !== '') {
                            const normStudent = String(studentVal).trim().toLowerCase();
                            const normCorrect  = String(correctVal ?? '').trim().toLowerCase();
                            td.classList.add(normStudent === normCorrect ? 'cell-correct' : 'cell-wrong');
                        }
                    }
                }
            } catch (err) {
                console.warn('Error applying answer styles:', err);
            }
        }

        // ── Responsive resize ─────────────────────────────────────────────────
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const el = container.querySelector('.jexcel_content');
                if (el) {
                    const nm = window.innerWidth < 640;
                    const nt = window.innerWidth >= 640 && window.innerWidth < 1024;
                    el.style.maxHeight = nm ? '350px' : (nt ? '450px' : '500px');
                }
            }, 250);
        });

        // ── Form submit: serialise full grid ──────────────────────────────────
        const form = document.getElementById('saveForm');
        if (form && !isReadOnly) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                // getData() returns the full 2-D array including both header rows
                document.getElementById('submission_data').value = JSON.stringify(table.getData());
                this.submit();
            });
        }

    })();
    </script>

</x-app-layout>