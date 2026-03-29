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

        /* Row 1 — account name headers (copied from answer sheet) */
        .jexcel tbody tr:nth-child(1) td {
            font-weight: 700 !important;
            text-align: center !important;
            background-color: #e5e7eb !important;
            white-space: normal !important;
            word-break: break-word !important;
            border-bottom: 2px solid #6b7280 !important;
        }

        /* Row 2 — Date / Debit / Credit sub-labels (copied from answer sheet) */
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
        .jexcel td.highlight { background-color: rgba(147,51,234,.08) !important; }

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
                    <div class="h-2 bg-gradient-to-r from-purple-500 via-pink-600 to-red-600"></div>

                    <div class="p-6 sm:p-8">
                        <!-- Step Indicator and Progress -->
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-50 to-pink-50 text-purple-700 rounded-full text-sm font-semibold border border-purple-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                                <span>Step 3 of 10</span>
                            </div>

                            <div class="flex-1 max-w-xs">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-purple-500 to-pink-600 h-full rounded-full transition-all duration-500" style="width: 30%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">30%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight mb-2">
                                    Posting to the Ledger
                                </h1>
                                <p class="text-base sm:text-lg text-gray-600 leading-relaxed">
                                    Transfer journalized entries into their respective ledger accounts to update balances.
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
                <div class="p-4 sm:p-6 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-purple-100">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-purple-900 mb-1">T-Account Ledger Instructions</h3>
                            <div class="text-sm text-purple-800 leading-relaxed">
                                {!! $performanceTask->description ?? 'Post transactions to T-accounts with debit entries on the left and credit entries on the right. Calculate the balance for each account.' !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Form ──────────────────────────────────────────────────── --}}
                <form id="saveForm"
                      method="POST"
                      action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => 3]) }}">
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
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all text-sm font-semibold shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
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
    {{-- Structure copied exactly from instructor answer sheet step 3        --}}
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

        // ── Accounts list (identical to answer sheet) ─────────────────────────
        const accounts = [
            'Cash', 'Accounts Receivable', 'Supplies', 'Furniture & Fixture',
            'Land', 'Equipment', 'Accumulated Depreciation - F&F',
            'Accumulated Depreciation - Equipment',
            'Accounts Payable', 'Notes Payable', 'Utilities Payable', 'Capital',
            'Withdrawals', 'Service Revenue', 'Rent Expense', 'Utilities Expense',
            'Salaries Expense', 'Supplies Expense', 'Depreciation Expense',
            'Income Summary'
        ];

        // Original HOT used 6 cols per account: Date | blank | Debit | Credit | blank | Date
        const COLS_PER_ACCOUNT = 6;
        const COL_COUNT        = accounts.length * COLS_PER_ACCOUNT;
        const HEADER_ROWS      = 2;
        const MIN_DATA_ROWS    = 15;

        // ── Header rows — identical to answer sheet ───────────────────────────
        const headerRow1 = accounts.flatMap(name => [name, '', '', '', '', '']);
        const headerRow2 = Array(accounts.length).fill(['Date', '', 'Debit (₱)', 'Credit (₱)', '', 'Date']).flat();
        const blankRow   = () => Array(COL_COUNT).fill('');

        // ── Restore saved data (same logic as answer sheet) ───────────────────
        let dataRows;
        if (savedData) {
            const parsed = JSON.parse(savedData);
            if (parsed.length <= MIN_DATA_ROWS) {
                // Old format without headers
                dataRows = parsed;
            } else {
                // New format — strip the 2 header rows
                dataRows = parsed.slice(HEADER_ROWS);
            }
        } else {
            dataRows = Array(MIN_DATA_ROWS).fill(null).map(blankRow);
        }

        while (dataRows.length < MIN_DATA_ROWS) dataRows.push(blankRow());

        const fullData = [headerRow1, headerRow2, ...dataRows];

        // ── Column-letter helper (identical to answer sheet) ──────────────────
        function colLetter(idx) {
            let s = '', n = idx + 1;
            while (n > 0) {
                const r = (n - 1) % 26;
                s = String.fromCharCode(65 + r) + s;
                n = Math.floor((n - 1) / 26);
            }
            return s;
        }

        // ── Merge cells — account name spans 6 cols in row 1 ─────────────────
        const mergeCells = {};
        accounts.forEach((_, i) => {
            mergeCells[`${colLetter(i * COLS_PER_ACCOUNT)}1`] = [COLS_PER_ACCOUNT, 1];
        });

        // ── Cell styles (identical to answer sheet) ───────────────────────────
        const cellStyle = {};

        // Row 1: account name headers
        accounts.forEach((_, i) => {
            for (let c = 0; c < COLS_PER_ACCOUNT; c++) {
                cellStyle[`${colLetter(i * COLS_PER_ACCOUNT + c)}1`] =
                    'font-weight:700;text-align:center;background:#e5e7eb;border-bottom:2px solid #6b7280;white-space:normal;word-break:break-word;';
            }
        });

        // Row 2: sub-label headers
        for (let c = 0; c < COL_COUNT; c++) {
            cellStyle[`${colLetter(c)}2`] =
                'font-weight:700;text-align:center;background:#f3f4f6;border-bottom:2px solid #6b7280;';
        }

        // ── Responsive dimensions ─────────────────────────────────────────────
        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;

        // ── Init jSpreadsheet (identical config to answer sheet + student flags) ──
        const table = jspreadsheet(container, {
            data             : fullData,
            mergeCells       : mergeCells,
            style            : cellStyle,
            minDimensions    : [COL_COUNT, fullData.length],
            defaultColWidth  : isMobile ? 80 : (isTablet ? 90 : 100),
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

            onload  : function () { applyColumnTints(); applyAnswerStyles(); },
            onchange: function () { applyColumnTints(); applyAnswerStyles(); },
        });

        // ── T-account column tinting (identical to answer sheet) ──────────────
        // Per-account block (6 cols): 0=Date, 1=blank, 2=Debit, 3=Credit, 4=blank, 5=Date
        function applyColumnTints() {
            const tbody = container.querySelector('.jexcel tbody');
            if (!tbody) return;
            tbody.querySelectorAll('tr').forEach((tr, rowIdx) => {
                if (rowIdx < HEADER_ROWS) return;
                const cells = tr.querySelectorAll('td');
                cells.forEach((td, tdIdx) => {
                    if (tdIdx === 0) return; // skip row-number header
                    const colIdx = tdIdx - 1;
                    const pattern = colIdx % COLS_PER_ACCOUNT;
                    td.style.background  = '';
                    td.style.borderRight = '';
                    if      (pattern === 0 || pattern === 5) td.style.background = '#f9fafb';
                    else if (pattern === 2) {
                        td.style.background  = '#fef3c7';
                        td.style.borderRight = '2px solid #6b7280';
                    }
                    else if (pattern === 3) td.style.background = '#dbeafe';
                });
            });
        }

        setTimeout(applyColumnTints, 100);

        // ── Answer-checking colour helper ─────────────────────────────────────
        function applyAnswerStyles() {
            if (!submissionStatus || !correctData || !savedData) return;

            try {
                const parsedCorrect = typeof correctData === 'string' ? JSON.parse(correctData) : correctData;
                const data = table.getData();

                for (let r = HEADER_ROWS; r < data.length; r++) {
                    for (let c = 0; c < COL_COUNT; c++) {
                        // Skip blank spacer columns (pattern 1 and 4)
                        const pattern = c % COLS_PER_ACCOUNT;
                        if (pattern === 1 || pattern === 4) continue;

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

        // ── Responsive height on window resize ───────────────────────────────
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const nm = window.innerWidth < 640;
                const nt = window.innerWidth >= 640 && window.innerWidth < 1024;
                const el = container.querySelector('.jexcel_content');
                if (el) el.style.maxHeight = nm ? '350px' : (nt ? '450px' : '500px');
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