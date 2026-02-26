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

        /* Rows 1-3: company / statement title / period — bold, centred (copied from answer sheet) */
        .jexcel tbody tr:nth-child(1) td,
        .jexcel tbody tr:nth-child(2) td,
        .jexcel tbody tr:nth-child(3) td {
            font-weight: 700 !important;
            text-align: center !important;
        }
        .jexcel tbody tr:nth-child(1) td { font-size: 14px !important; }
        .jexcel tbody tr:nth-child(2) td,
        .jexcel tbody tr:nth-child(3) td { font-size: 13px !important; }

        /* Bold-cell toggle (copied from answer sheet) */
        .jexcel td.bold-cell { font-weight: 700 !important; }

        /* Total / double-underline rows (copied from answer sheet) */
        .jexcel td.total-border-top    { border-top: 1px solid #000 !important; font-weight: 700 !important; }
        .jexcel td.total-double-bottom { border-bottom: 3px double #000 !important; }

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
        .jexcel td.highlight { background-color: rgba(59,130,246,.08) !important; }

        /* Scrollbar */
        #spreadsheet ::-webkit-scrollbar        { width: 6px; height: 6px; }
        #spreadsheet ::-webkit-scrollbar-track  { background: transparent; }
        #spreadsheet ::-webkit-scrollbar-thumb  { background: #d1d5db; border-radius: 9999px; }

        @media (max-width: 640px)                        { .jexcel td, .jexcel th { font-size: 11px; padding: 3px; } }
        @media (min-width: 640px) and (max-width: 1024px) { .jexcel td, .jexcel th { font-size: 12px; } }

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
                    <div class="h-2 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-600"></div>

                    <div class="p-6 sm:p-8">
                        <!-- Step Indicator and Progress -->
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 rounded-full text-sm font-semibold border border-blue-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                                <span>Step 7 of 10</span>
                            </div>

                            <div class="flex-1 max-w-xs">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-full rounded-full transition-all duration-500" style="width: 70%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">70%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight mb-2">
                                    Financial Statements
                                </h1>
                                <p class="text-base sm:text-lg text-gray-600 leading-relaxed">
                                    Prepare the financial statements following McGraw Hill format: Income Statement, Statement of Owner's Equity, and Balance Sheet.
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

                            @if(isset($submission->score))
                            <div class="flex items-center gap-3 p-3 bg-purple-50 rounded-lg border border-purple-200">
                                <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-purple-600 font-medium">Score</p>
                                    <p class="text-lg font-bold text-purple-900">{{ $submission->score }}%</p>
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
                <div class="p-4 sm:p-6 bg-blue-50 border-b border-blue-100">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-blue-900 mb-1">Instructions</h3>
                            <div class="text-sm text-blue-800 leading-relaxed">
                                {!! $performanceTask->description ?? 'Complete all three financial statements in the McGraw Hill format below. Make sure to fill in all amounts accurately.' !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Form ──────────────────────────────────────────────────── --}}
                <form id="saveForm"
                      method="POST"
                      action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => 7]) }}">
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

                            <button type="submit" id="submitButton"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm font-semibold shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
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
    {{-- Structure copied exactly from instructor answer sheet step 7        --}}
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
        const COL_COUNT     = 12;
        const MIN_DATA_ROWS = 35;

        // ── Column letter helper (identical to answer sheet) ──────────────────
        const COLS = ['A','B','C','D','E','F','G','H','I','J','K','L'];

        // ── Numeric value column indices (identical to answer sheet) ──────────
        const numericColIndices = [1, 2, 5, 6, 9, 10, 11];

        // ── Default initial data (identical to answer sheet defaultData) ──────
        const defaultData = [
            ["Durano Enterprise", null, null, null, "Durano Enterprise", null, null, null, "Durano Enterprise", null, null, null],
            ["Income Statement", null, null, null, "Statement of Changes in Equity", null, null, null, "Balance Sheet", null, null, null],
            ["For the month ended February 29, 2024", null, null, null, "For the month ended February 29, 2024", null, null, null, "As of February 29, 2024", null, null, null],
            ["", "", "", "", "", "", "", "", "", "", "", ""],
            ["Revenues:", "", "", "", "Durano, Capital, beginning", "", "", "", "Assets", "", "", ""],
            ["Service Revenue", "", "", "", "Add: Investment", "", "", "", "Current assets", "", "", ""],
            ["", "", "", "", "          Net Income", "", "", "", "Cash", "", "", ""],
            ["Less: Expenses", "", "", "", "Total", "", "", "", "Accounts receivable", "", "", ""],
            ["Rent expense", "", "", "", "Less: Durano, Withdrawals", "", "", "", "Supplies", "", "", ""],
            ["Utilities expense", "", "", "", "Durano, Capital, ending", "", "", "", "Total current assets", "", "", ""],
            ["Salaries expense", "", "", "", "", "", "", "", "", "", "", ""],
            ["Supplies expense", "", "", "", "", "", "", "", "Non-current assets", "", "", ""],
            ["Depreciation expense", "", "", "", "", "", "", "", "Furniture and fixture", "", "", ""],
            ["Net Income", "", "", "", "", "", "", "", "Accumulated depreciation-furniture and fixture", "", "", ""],
            ["", "", "", "", "", "", "", "", "Equipment", "", "", ""],
            ["", "", "", "", "", "", "", "", "Accumulated depreciation-equipment", "", "", ""],
            ["", "", "", "", "", "", "", "", "Land", "", "", ""],
            ["", "", "", "", "", "", "", "", "Total Non-current assets", "", "", ""],
            ["", "", "", "", "", "", "", "", "Total Assets", "", "", ""],
            ["", "", "", "", "", "", "", "", "", "", "", ""],
            ["", "", "", "", "", "", "", "", "Liabilities", "", "", ""],
            ["", "", "", "", "", "", "", "", "Accounts payable", "", "", ""],
            ["", "", "", "", "", "", "", "", "Notes payable", "", "", ""],
            ["", "", "", "", "", "", "", "", "Utilities payable", "", "", ""],
            ["", "", "", "", "", "", "", "", "Total liabilities", "", "", ""],
            ["", "", "", "", "", "", "", "", "", "", "", ""],
            ["", "", "", "", "", "", "", "", "Owner's Equity", "", "", ""],
            ["", "", "", "", "", "", "", "", "Durano, Capital", "", "", ""],
            ["", "", "", "", "", "", "", "", "Total Liabilities and Owner's Equity", "", "", ""],
            ["", "", "", "", "", "", "", "", "", "", "", ""],
            [null, null, null, null, null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null, null, null, null, null],
        ];

        // ── Restore saved data + bold metadata (same logic as answer sheet) ───
        let fullData, boldCells = {};

        if (savedDataRaw) {
            const parsed = typeof savedDataRaw === 'string'
                ? JSON.parse(savedDataRaw)
                : savedDataRaw;

            fullData = (parsed && parsed.data) ? parsed.data : parsed;

            if (parsed && parsed.metadata) {
                parsed.metadata.forEach((rowMeta, rIdx) => {
                    if (!rowMeta) return;
                    rowMeta.forEach((cellMeta, cIdx) => {
                        if (cellMeta && cellMeta.bold) boldCells[`${rIdx},${cIdx}`] = true;
                    });
                });
            }
        } else {
            fullData = JSON.parse(JSON.stringify(defaultData));
        }

        while (fullData.length < MIN_DATA_ROWS) fullData.push(Array(COL_COUNT).fill(null));

        // ── Parse correct data ────────────────────────────────────────────────
        let correctData = null;
        if (correctDataRaw) {
            const parsedCorrect = typeof correctDataRaw === 'string'
                ? JSON.parse(correctDataRaw)
                : correctDataRaw;
            correctData = (parsedCorrect && parsedCorrect.data) ? parsedCorrect.data : parsedCorrect;
        }

        // ── Merge cells (identical to answer sheet) ───────────────────────────
        const mergeCells = {
            'A1': [4, 1], 'A2': [4, 1], 'A3': [4, 1],
            'E1': [4, 1], 'E2': [4, 1], 'E3': [4, 1],
            'I1': [4, 1], 'I2': [4, 1], 'I3': [4, 1],
        };

        // ── Cell styles (identical to answer sheet) ───────────────────────────
        const cellStyle = {};

        // Rows 1-3: bold, centred per-section headers
        COLS.forEach(c => {
            cellStyle[`${c}1`] = 'font-weight:700;text-align:center;font-size:14px;';
            cellStyle[`${c}2`] = 'font-weight:700;text-align:center;font-size:13px;';
            cellStyle[`${c}3`] = 'font-weight:700;text-align:center;font-size:13px;';
        });

        // Separator cols D and H: grey bg + right border
        fullData.forEach((_, rIdx) => {
            const rNum = rIdx + 1;
            cellStyle[`D${rNum}`] = (cellStyle[`D${rNum}`] || '') + 'background:#f8f9fa;border-right:2px solid #dee2e6;';
            cellStyle[`H${rNum}`] = (cellStyle[`H${rNum}`] || '') + 'background:#f8f9fa;border-right:2px solid #dee2e6;';
        });

        // Bold label rows
        const boldLabelRows = {
            'Revenues:': 0, 'Less: Expenses': 0, 'Net Income': 0,
            'Total': 4, 'Durano, Capital, ending': 4,
            'Assets': 8, 'Current assets': 8, 'Non-current assets': 8,
            'Liabilities': 8, "Owner's Equity": 8,
            'Total current assets': 8, 'Total Non-current assets': 8,
            'Total Assets': 8, 'Total liabilities': 8,
            "Total Liabilities and Owner's Equity": 8,
        };

        const totalBorderLabels = new Set([
            'Net Income', 'Total', 'Durano, Capital, ending',
            'Total current assets', 'Total Non-current assets',
            'Total Assets', 'Total liabilities',
            "Total Liabilities and Owner's Equity",
        ]);

        const doubleBottomLabels = new Set([
            'Durano, Capital, ending', 'Total Assets',
            "Total Liabilities and Owner's Equity",
        ]);

        fullData.forEach((row, rIdx) => {
            if (rIdx < 3) return;
            const rNum = rIdx + 1;
            row.forEach((val, cIdx) => {
                if (!val || String(val).trim() === '') return;
                const cellVal = String(val).trim();
                const ref = `${COLS[cIdx]}${rNum}`;

                if (boldLabelRows.hasOwnProperty(cellVal)) {
                    cellStyle[ref] = (cellStyle[ref] || '') + 'font-weight:700;';
                }

                if (totalBorderLabels.has(cellVal)) {
                    cellStyle[ref] = (cellStyle[ref] || '') + 'font-weight:700;border-top:1px solid #000;';
                    numericColIndices.forEach(nIdx => {
                        const nRef = `${COLS[nIdx]}${rNum}`;
                        cellStyle[nRef] = (cellStyle[nRef] || '') + 'border-top:1px solid #000;';
                    });
                }

                if (doubleBottomLabels.has(cellVal)) {
                    cellStyle[ref] = (cellStyle[ref] || '') + 'border-bottom:3px double #000;';
                    numericColIndices.forEach(nIdx => {
                        const nRef = `${COLS[nIdx]}${rNum}`;
                        cellStyle[nRef] = (cellStyle[nRef] || '') + 'border-bottom:3px double #000;';
                    });
                }
            });
        });

        // Right-align numeric value columns for data rows (row 4+)
        fullData.forEach((_, rIdx) => {
            if (rIdx < 3) return;
            const rNum = rIdx + 1;
            numericColIndices.forEach(cIdx => {
                const ref = `${COLS[cIdx]}${rNum}`;
                cellStyle[ref] = (cellStyle[ref] || '') + 'text-align:right;';
            });
        });

        // Apply restored bold metadata
        Object.keys(boldCells).forEach(key => {
            const [r, c] = key.split(',').map(Number);
            const ref = `${COLS[c]}${r + 1}`;
            cellStyle[ref] = (cellStyle[ref] || '') + 'font-weight:700;';
        });

        // ── Responsive dimensions (identical to answer sheet) ─────────────────
        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;

        const colWidths = isMobile
            ? Array(12).fill(100)
            : (isTablet
                ? [200, 100, 100, 20, 200, 100, 100, 20, 200, 100, 100, 120]
                : [240, 110, 110, 30, 240, 110, 110, 30, 240, 110, 110, 130]);

        const tableH = isMobile
            ? Math.min(Math.max(window.innerHeight * 0.5, 350), 500)
            : (isTablet
                ? Math.min(Math.max(window.innerHeight * 0.6, 450), 600)
                : Math.min(Math.max(window.innerHeight * 0.65, 550), 700));

        // ── Build columns array (identical to answer sheet) ───────────────────
        const columns = COLS.map((_, i) => ({
            type : numericColIndices.includes(i) ? 'numeric' : 'text',
            width: colWidths[i],
            ...(numericColIndices.includes(i) ? { mask: '#,##0.00', decimal: '.' } : {}),
        }));

        // ── Init jSpreadsheet (identical to answer sheet + student flags) ─────
        const table = jspreadsheet(container, {
            data             : fullData,
            minDimensions    : [COL_COUNT, fullData.length],
            defaultColWidth  : isMobile ? 120 : 150,
            mergeCells       : mergeCells,
            style            : cellStyle,
            columns          : columns,
            tableWidth       : '100%',
            tableOverflow    : true,
            tableHeight      : `${tableH}px`,
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

            // Context menu with Bold toggle (identical to answer sheet + student guard)
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
                                const ref = `${COLS[c]}${r + 1}`;
                                const cur = obj.getStyle(ref) || '';
                                if (cur.includes('font-weight:700') || cur.includes('font-weight: 700')) {
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
                            const ref = `${COLS[c]}${r + 1}`;
                            const cur = table.getStyle(ref) || '';
                            if (cur.includes('font-weight:700') || cur.includes('font-weight: 700')) {
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
        // Mirrors HOT grading logic: skips header rows (0-3), separator cols (3,7),
        // and template label cells. Only grades cells where student entered something
        // different from the template value.
        function applyAnswerStyles() {
            if (!submissionStatus || !correctData) return;

            try {
                const data = table.getData();

                // Template label cells — never graded (mirrors HOT templateCells set)
                // These are the fixed row/col pairs that contain pre-filled labels
                const templateCols = {
                    0: new Set([0,4,8]),   // col A = IS labels, col E = SCE labels, col I = BS labels
                };

                // Build template values from defaultData for comparison
                for (let r = 3; r < data.length; r++) {
                    for (let c = 0; c < COL_COUNT; c++) {
                        // Skip separator columns
                        if (c === 3 || c === 7) continue;

                        const studentVal = data[r][c];
                        const correctVal = correctData[r] ? correctData[r][c] : undefined;
                        const templateVal = defaultData[r] ? defaultData[r][c] : undefined;

                        const td = container.querySelector(
                            `.jexcel tbody tr:nth-child(${r + 1}) td:nth-child(${c + 2})`
                        );
                        if (!td) continue;

                        td.classList.remove('cell-correct', 'cell-wrong');

                        // Normalise helper
                        const norm = val => {
                            if (val === null || val === undefined || val === '') return '';
                            return String(val).replace(/[₱,\s()]/g, '').trim().toLowerCase();
                        };

                        const normStudent  = norm(studentVal);
                        const normTemplate = norm(templateVal);

                        // Only grade cells where student entered something different from template
                        if (normStudent !== '' && normStudent !== normTemplate) {
                            const normCorrect = norm(correctVal);
                            td.classList.add(normStudent === normCorrect ? 'cell-correct' : 'cell-wrong');
                        }
                    }
                }
            } catch (err) {
                console.warn('Error applying answer styles:', err);
            }
        }

        // ── Responsive resize (identical to answer sheet) ─────────────────────
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const el = container.querySelector('.jexcel_content');
                if (!el) return;
                const nm = window.innerWidth < 640;
                const nt = window.innerWidth >= 640 && window.innerWidth < 1024;
                const nh = nm
                    ? Math.min(Math.max(window.innerHeight * 0.5, 350), 500)
                    : (nt
                        ? Math.min(Math.max(window.innerHeight * 0.6, 450), 600)
                        : Math.min(Math.max(window.innerHeight * 0.65, 550), 700));
                el.style.maxHeight = `${nh}px`;
            }, 250);
        });

        // ── Form submit — persist data + bold metadata (identical to answer sheet) ──
        const form = document.getElementById('saveForm');
        if (form && !isReadOnly) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const data     = table.getData();
                const metadata = [];

                data.forEach((row, rIdx) => {
                    metadata[rIdx] = [];
                    row.forEach((_, cIdx) => {
                        const ref = `${COLS[cIdx]}${rIdx + 1}`;
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