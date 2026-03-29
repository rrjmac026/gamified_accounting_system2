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

        /* Row 0: Company name — bold, centred, white bg (copied from answer sheet) */
        .jexcel tbody tr:nth-child(1) td {
            font-weight: 700 !important;
            text-align: center !important;
            background-color: #ffffff !important;
            font-size: 14px !important;
            padding: 8px !important;
        }

        /* Row 1: Document title — bold, centred, white bg (copied from answer sheet) */
        .jexcel tbody tr:nth-child(2) td {
            font-weight: 700 !important;
            text-align: center !important;
            background-color: #ffffff !important;
            font-size: 14px !important;
            padding: 8px !important;
        }

        /* Row 2: Date field — bold, centred, white bg (copied from answer sheet) */
        .jexcel tbody tr:nth-child(3) td {
            font-weight: 700 !important;
            text-align: center !important;
            background-color: #ffffff !important;
            font-size: 14px !important;
            padding: 8px !important;
        }

        /* Row 3: Column headers — bold, centred, grey bg, double border (copied from answer sheet) */
        .jexcel tbody tr:nth-child(4) td {
            font-weight: 700 !important;
            text-align: center !important;
            background-color: #f3f4f6 !important;
            border-bottom: 2px solid #374151 !important;
        }

        /* Bold-cell class — toggled via context menu / Ctrl+B (copied from answer sheet) */
        .jexcel td.bold-cell { font-weight: 700 !important; }

        /* Peso-amount columns: right-align data rows */
        .jexcel tbody tr:nth-child(n+5) td:nth-child(n+2) { text-align: right; }

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
        .jexcel td.highlight { background-color: rgba(6,182,212,.08) !important; }

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
                    <div class="h-2 bg-gradient-to-r from-cyan-500 via-blue-600 to-indigo-600"></div>

                    <div class="p-6 sm:p-8">
                        <!-- Step Indicator and Progress -->
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-50 to-blue-50 text-cyan-700 rounded-full text-sm font-semibold border border-cyan-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Step 4 of 10</span>
                            </div>

                            <div class="flex-1 max-w-xs">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 h-full rounded-full transition-all duration-500" style="width: 40%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">40%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight mb-2">
                                    Trial Balance
                                </h1>
                                <p class="text-base sm:text-lg text-gray-600 leading-relaxed">
                                    List all accounts and their debit or credit balances to ensure totals are equal.
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
                            @php
                                $statusColors = [
                                    'correct'     => ['bg'=>'bg-green-50','border'=>'border-green-200','icon-bg'=>'bg-green-100','text'=>'text-green-600','text-bold'=>'text-green-900'],
                                    'passed'      => ['bg'=>'bg-blue-50', 'border'=>'border-blue-200', 'icon-bg'=>'bg-blue-100', 'text'=>'text-blue-600', 'text-bold'=>'text-blue-900'],
                                    'wrong'       => ['bg'=>'bg-red-50',  'border'=>'border-red-200',  'icon-bg'=>'bg-red-100',  'text'=>'text-red-600',  'text-bold'=>'text-red-900'],
                                    'in-progress' => ['bg'=>'bg-gray-50', 'border'=>'border-gray-200', 'icon-bg'=>'bg-gray-100', 'text'=>'text-gray-600', 'text-bold'=>'text-gray-900'],
                                ];
                                $cs = $statusColors[$submission->status] ?? $statusColors['in-progress'];
                            @endphp
                            <div class="flex items-center gap-3 p-3 {{ $cs['bg'] }} border {{ $cs['border'] }} rounded-lg">
                                <div class="flex-shrink-0 w-10 h-10 {{ $cs['icon-bg'] }} rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 {{ $cs['text'] }}" fill="currentColor" viewBox="0 0 20 20">
                                        @if(in_array($submission->status, ['correct','passed']))
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        @elseif($submission->status === 'wrong')
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        @else
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        @endif
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs {{ $cs['text'] }} font-medium">Status</p>
                                    <p class="text-lg font-bold {{ $cs['text-bold'] }}">{{ ucfirst($submission->status) }}</p>
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
                <div class="p-4 sm:p-6 bg-gradient-to-r from-cyan-50 to-blue-50 border-b border-cyan-100">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-cyan-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-cyan-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-cyan-900 mb-1">Instructions</h3>
                            <div class="text-sm text-cyan-800 leading-relaxed">
                                {!! $performanceTask->description ?? 'Review your ledger balances before continuing.' !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Form ──────────────────────────────────────────────────── --}}
                <form id="saveForm"
                      method="POST"
                      action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => 4]) }}">
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
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-lg hover:from-cyan-700 hover:to-blue-700 focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition-all text-sm font-semibold shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
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
    {{-- Structure copied exactly from instructor answer sheet step 4        --}}
    <script>
    (function () {

        const container = document.getElementById('spreadsheet');

        // ── PHP → JS data ────────────────────────────────────────────────────
        const savedDataRaw     = @json($submission->submission_data ?? null);
        const correctDataRaw   = @json($answerSheet->correct_data ?? null);
        const submissionStatus = @json($submission->status ?? null);
        const maxAttempts      = @json($performanceTask->max_attempts);
        const currentAttempts  = @json($submission->attempts ?? 0);
        const isReadOnly       = currentAttempts >= maxAttempts;

        // ── Constants (identical to answer sheet) ─────────────────────────────
        const HEADER_ROWS   = 4;   // rows 0-3: company / title / date / col-headers
        const COL_COUNT     = 3;
        const MIN_DATA_ROWS = 12;

        // ── Default header rows (identical to answer sheet) ───────────────────
        const defaultHeaders = [
            ['Durano Enterprise', '', ''],
            ['Trial Balance',     '', ''],
            ['Date: ____________','', ''],
            ['Account Title', 'Debit (₱)', 'Credit (₱)'],
        ];

        // ── Restore saved data (same logic as answer sheet) ───────────────────
        let dataRows;
        let boldCells = {};   // key: "row,col" → true

        if (savedDataRaw) {
            const parsedSaved = typeof savedDataRaw === 'string'
                ? JSON.parse(savedDataRaw)
                : savedDataRaw;

            // Support both new { data, metadata } format and legacy plain array
            const rawData = (parsedSaved && parsedSaved.data) ? parsedSaved.data : parsedSaved;

            dataRows = rawData.length <= MIN_DATA_ROWS
                ? rawData                        // old format — no headers stored
                : rawData.slice(HEADER_ROWS);    // new format — strip 4 header rows

            // Restore bold metadata
            if (parsedSaved && parsedSaved.metadata) {
                parsedSaved.metadata.forEach((rowMeta, rIdx) => {
                    if (!rowMeta) return;
                    rowMeta.forEach((cellMeta, cIdx) => {
                        if (cellMeta && cellMeta.bold) boldCells[`${rIdx},${cIdx}`] = true;
                    });
                });
            }
        } else {
            dataRows = Array(MIN_DATA_ROWS).fill(null).map(() => Array(COL_COUNT).fill(''));
        }

        while (dataRows.length < MIN_DATA_ROWS) dataRows.push(Array(COL_COUNT).fill(''));

        // ── Parse correct data (same { data, metadata } or plain array) ───────
        let correctData = null;
        if (correctDataRaw) {
            const parsedCorrect = typeof correctDataRaw === 'string'
                ? JSON.parse(correctDataRaw)
                : correctDataRaw;
            correctData = (parsedCorrect && parsedCorrect.data) ? parsedCorrect.data : parsedCorrect;
        }

        const fullData = [...defaultHeaders, ...dataRows];

        // ── Merge cells — header rows span all 3 cols (identical to answer sheet) ──
        const mergeCells = {
            'A1': [3, 1],   // Company name
            'A2': [3, 1],   // Document title
            'A3': [3, 1],   // Date field
        };

        // ── Cell styles (identical to answer sheet) ───────────────────────────
        const cellStyle = {};

        // Rows 1-3: company / title / date — white, bold, centred
        ['A1','B1','C1', 'A2','B2','C2', 'A3','B3','C3'].forEach(ref => {
            cellStyle[ref] = 'font-weight:700;text-align:center;background:#ffffff;font-size:14px;padding:8px;';
        });

        // Row 4: column headers
        ['A4','B4','C4'].forEach(ref => {
            cellStyle[ref] = 'font-weight:700;text-align:center;background:#f3f4f6;border-bottom:2px solid #374151;';
        });

        // Apply restored bold metadata to data rows
        Object.keys(boldCells).forEach(key => {
            const [r, c] = key.split(',').map(Number);
            const col = String.fromCharCode(65 + c);
            const ref = `${col}${r + 1}`;   // 1-based jSS reference
            cellStyle[ref] = (cellStyle[ref] || '') + 'font-weight:700;';
        });

        // ── Responsive dimensions ─────────────────────────────────────────────
        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;

        const colWidth = isMobile ? 160 : (isTablet ? 200 : 260);
        const numWidth = isMobile ? 120 : (isTablet ? 140 : 180);

        // ── Init jSpreadsheet (identical to answer sheet + student flags) ─────
        const table = jspreadsheet(container, {
            data             : fullData,
            mergeCells       : mergeCells,
            style            : cellStyle,
            minDimensions    : [COL_COUNT, fullData.length],
            defaultColWidth  : colWidth,
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

            columns: [
                { title: 'Account Title', type: 'text',    width: colWidth },
                { title: 'Debit (₱)',     type: 'numeric', width: numWidth, mask: '#,##0.00', decimal: '.' },
                { title: 'Credit (₱)',    type: 'numeric', width: numWidth, mask: '#,##0.00', decimal: '.' },
            ],

            // Context menu with Bold toggle (identical to answer sheet + student read-only guard)
            contextMenu: isReadOnly ? false : function (obj, x, y, e) {
                return [
                    { title: 'Insert row above', onclick: () => obj.insertRow(1, parseInt(y), true) },
                    { title: 'Insert row below', onclick: () => obj.insertRow(1, parseInt(y)) },
                    { title: 'Delete row',       onclick: () => obj.deleteRow(parseInt(y)) },
                    { type: 'line' },
                    { title: '✓ Toggle Bold', onclick: () => {
                        const sel = obj.getSelectedCoords();
                        if (!sel) return;
                        const [c1, r1, c2, r2] = sel;
                        for (let r = r1; r <= r2; r++) {
                            for (let c = c1; c <= c2; c++) {
                                const col = String.fromCharCode(65 + c);
                                const ref = `${col}${r + 1}`;
                                const cur = obj.getStyle(ref) || '';
                                if (cur.includes('font-weight:700')) {
                                    obj.setStyle(ref, 'font-weight', '');
                                } else {
                                    obj.setStyle(ref, 'font-weight', '700');
                                }
                            }
                        }
                    }},
                    { type: 'line' },
                    { title: 'Copy',  onclick: () => obj.copy(true) },
                    { title: 'Paste', onclick: () => {
                        if (navigator.clipboard) {
                            navigator.clipboard.readText().then(t => obj.paste(x, y, t));
                        }
                    }},
                ];
            },

            onload  : function () { applyAnswerStyles(); },
            onchange: function () { applyAnswerStyles(); },
        });

        // ── Ctrl+B / Cmd+B keyboard shortcut (identical to answer sheet) ──────
        if (!isReadOnly) {
            document.addEventListener('keydown', function (e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                    e.preventDefault();
                    const sel = table.getSelectedCoords();
                    if (!sel) return;
                    const [c1, r1, c2, r2] = sel;
                    for (let r = r1; r <= r2; r++) {
                        for (let c = c1; c <= c2; c++) {
                            const col = String.fromCharCode(65 + c);
                            const ref = `${col}${r + 1}`;
                            const cur = table.getStyle(ref) || '';
                            if (cur.includes('font-weight:700')) {
                                table.setStyle(ref, 'font-weight', '');
                            } else {
                                table.setStyle(ref, 'font-weight', '700');
                            }
                        }
                    }
                }
            });
        }

        // ── Answer-checking colour helper ─────────────────────────────────────
        function applyAnswerStyles() {
            if (!submissionStatus || !correctData) return;

            try {
                const data = table.getData();

                // Start from row index 4 — skip the 4 header rows
                for (let r = HEADER_ROWS; r < data.length; r++) {
                    for (let c = 0; c < COL_COUNT; c++) {
                        const studentVal = data[r][c];
                        const correctVal = correctData[r] ? correctData[r][c] : undefined;

                        const td = container.querySelector(
                            `.jexcel tbody tr:nth-child(${r + 1}) td:nth-child(${c + 2})`
                        );
                        if (!td) continue;

                        td.classList.remove('cell-correct', 'cell-wrong');

                        if (studentVal !== null && studentVal !== undefined && String(studentVal).trim() !== '') {
                            // Normalise: strip ₱, commas, whitespace; compare as numbers when possible
                            const norm = val => {
                                if (val === null || val === undefined || val === '') return '';
                                const cleaned = String(val).trim().replace(/[,₱\s]/g, '').toLowerCase();
                                const num = parseFloat(cleaned);
                                return isNaN(num) ? cleaned : num.toFixed(2);
                            };
                            td.classList.add(norm(studentVal) === norm(correctVal) ? 'cell-correct' : 'cell-wrong');
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

        // ── Form submit — persist data + bold metadata (identical to answer sheet) ──
        const form = document.getElementById('saveForm');
        if (form && !isReadOnly) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const data     = table.getData();
                const metadata = [];

                // Capture bold state per-cell by inspecting inline style
                data.forEach((row, rIdx) => {
                    metadata[rIdx] = [];
                    row.forEach((_, cIdx) => {
                        const col = String.fromCharCode(65 + cIdx);
                        const ref = `${col}${rIdx + 1}`;
                        const sty = table.getStyle(ref) || '';
                        if (sty.includes('font-weight:700') || sty.includes('font-weight: 700')) {
                            metadata[rIdx][cIdx] = { bold: true };
                        }
                    });
                });

                document.getElementById('submission_data').value = JSON.stringify({
                    data    : data,
                    metadata: metadata,
                });

                this.submit();
            });
        }

    })();
    </script>

</x-app-layout>