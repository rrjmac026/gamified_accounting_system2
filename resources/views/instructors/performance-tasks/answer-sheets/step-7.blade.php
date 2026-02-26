<x-app-layout>
    {{-- ═══════════════════════════ jSpreadsheet CDN ═══════════════════════════ --}}
    <script src="https://cdn.jsdelivr.net/npm/jspreadsheet-ce@4.13.4/dist/index.js"></script>
    <link  rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jspreadsheet-ce@4.13.4/dist/jspreadsheet.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jsuites/dist/jsuites.js"></script>
    <link  rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsuites/dist/jsuites.css" />

    <style>
        /* ── Page header / badge styles ──────────────────────────────────────── */
        .answer-key-header {
            background: linear-gradient(135deg, #f9fafb 0%, #f3e8ff 50%, #faf5ff 100%);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid #e9d5ff;
            box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.1), 0 2px 4px -1px rgba(139, 92, 246, 0.06);
            position: relative;
            overflow: hidden;
        }
        .answer-key-header::before {
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(167, 139, 250, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .answer-key-header::after {
            content: '';
            position: absolute;
            bottom: -30%; left: -5%;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(196, 181, 253, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .step-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.3), 0 2px 4px -1px rgba(139, 92, 246, 0.2);
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .step-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px -1px rgba(139, 92, 246, 0.4), 0 3px 5px -1px rgba(139, 92, 246, 0.3);
        }
        .step-badge svg { width: 1rem; height: 1rem; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        .header-title {
            font-size: 2.25rem;
            font-weight: 800;
            background: linear-gradient(135deg, #581c87 0%, #7c3aed 50%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }
        .header-description {
            color: #6b7280;
            font-size: 1rem;
            line-height: 1.625;
            max-width: 48rem;
            margin-bottom: 0.75rem;
            position: relative;
            z-index: 1;
        }
        .task-info-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.875rem;
            background: white;
            color: #7c3aed;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid #e9d5ff;
            box-shadow: 0 1px 3px 0 rgba(139, 92, 246, 0.1);
            position: relative;
            z-index: 1;
            transition: all 0.2s ease;
        }
        .task-info-badge:hover { background: #faf5ff; border-color: #d8b4fe; transform: translateX(4px); }
        .task-info-badge svg { width: 1rem; height: 1rem; }

        /* ── jSpreadsheet overrides ───────────────────────────────────────────── */
        body { overflow-x: hidden; }
        #spreadsheet { width: 100%; }
        #spreadsheet .jexcel_content { overflow: auto; }
        .jexcel td { border-color: #d1d5db !important; }

        /* ── Rows 1-3: company / statement title / period — bold, centred ─────── */
        .jexcel tbody tr:nth-child(1) td,
        .jexcel tbody tr:nth-child(2) td,
        .jexcel tbody tr:nth-child(3) td {
            font-weight: 700 !important;
            text-align: center !important;
        }
        .jexcel tbody tr:nth-child(1) td { font-size: 14px !important; }
        .jexcel tbody tr:nth-child(2) td,
        .jexcel tbody tr:nth-child(3) td { font-size: 13px !important; }

        /* ── Separator columns (D = col index 3, H = col index 7) ───────────── */
        /* Applied dynamically via applyColumnStyles() */

        /* ── Bold-cell toggle ────────────────────────────────────────────────── */
        .jexcel td.bold-cell { font-weight: 700 !important; }

        /* ── Right-align numeric value columns ───────────────────────────────── */
        /* Cols B,C,F,G,J,K,L (indices 1,2,5,6,9,10,11) for data rows ────────── */
        /* Applied dynamically via applyColumnStyles() */

        /* ── Total / double-underline rows ───────────────────────────────────── */
        .jexcel td.total-border-top    { border-top: 1px solid #000 !important; font-weight: 700 !important; }
        .jexcel td.total-double-bottom { border-bottom: 3px double #000 !important; }

        /* ── Scrollbar polish ────────────────────────────────────────────────── */
        #spreadsheet ::-webkit-scrollbar        { width: 6px; height: 6px; }
        #spreadsheet ::-webkit-scrollbar-track  { background: transparent; }
        #spreadsheet ::-webkit-scrollbar-thumb  { background: #d1d5db; border-radius: 9999px; }

        @media (max-width: 640px) { .jexcel td, .jexcel th { font-size: 11px; padding: 3px; } }
        @media (min-width: 640px) and (max-width: 1024px) { .jexcel td, .jexcel th { font-size: 12px; } }
    </style>

    <div class="py-4 sm:py-6 lg:py-8">

        @if (session('error'))
            <div class="mb-6 animate-slideDown">
                <div class="flex items-start gap-3 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-red-800 mb-1">Error</h3>
                        <p class="text-sm text-red-700 leading-relaxed">{{ session('error') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 animate-slideDown">
                <div class="flex items-start gap-3 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-sm">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-green-800 mb-1">Success</h3>
                        <p class="text-sm text-green-700 leading-relaxed">{{ session('success') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-green-400 hover:text-green-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Enhanced Header Section -->
        <div class="answer-key-header">
            <div class="step-badge">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                <span>Answer Key - Step 7 of 10</span>
            </div>

            <h1 class="header-title">Answer Key: Financial Statements</h1>

            <p class="header-description">
                Create the correct answer key for the Financial Statements following McGraw Hill format. This will be used to automatically grade student submissions.
            </p>

            <div class="task-info-badge">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span>Task: {{ $task->title }}</span>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Instructions Section -->
            <div class="p-4 sm:p-6 bg-purple-50 border-b border-purple-100">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-purple-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-purple-900 mb-1">Instructions for Creating Answer Key</h3>
                        <p class="text-xs sm:text-sm text-purple-800">
                            Fill in the correct answers below following McGraw Hill financial statement format. Students' submissions will be compared against this answer key for grading. Empty cells will be ignored during comparison.
                        </p>
                    </div>
                </div>
            </div>

            <form id="answerKeyForm"
                  action="{{ route('instructors.performance-tasks.answer-sheets.update', ['task' => $task, 'step' => 7]) }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="p-3 sm:p-4 lg:p-6">
                    <div class="border rounded-lg shadow-inner bg-gray-50 overflow-hidden">
                        <div class="overflow-x-auto overflow-y-auto"
                             style="max-height: calc(100vh - 400px); min-height: 400px;">
                            <div id="spreadsheet" class="bg-white min-w-full"></div>
                        </div>
                        <input type="hidden" name="correct_data" id="correctData" required>
                    </div>
                    <div class="mt-2 text-xs text-gray-500 sm:hidden text-center">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        Swipe to scroll spreadsheet
                    </div>
                </div>

                <div class="p-4 sm:p-6 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between gap-3 sm:gap-4">

                        <a href="{{ route('instructors.performance-tasks.answer-sheets.show', $task) }}"
                           class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors text-sm sm:text-base">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Answer Sheets
                        </a>

                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                            <button type="button" onclick="openImportModal()"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 transition-colors text-sm sm:text-base">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Import File
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 transition-colors text-sm sm:text-base">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Answer Key &amp; Continue
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════════ jSpreadsheet initialisation ═══════════════════ --}}
    <script>
    (function () {

        const container    = document.getElementById('spreadsheet');
        const savedDataRaw = @json($sheet->correct_data ?? null);

        // ── Constants ─────────────────────────────────────────────────────────
        const COL_COUNT     = 12;
        const MIN_DATA_ROWS = 35;

        // ── Column letter helper ──────────────────────────────────────────────
        const COLS = ['A','B','C','D','E','F','G','H','I','J','K','L'];

        // ── Default initial data (matches HOT initialData exactly) ───────────
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

        // ── Restore / build initial data ──────────────────────────────────────
        let fullData, boldCells = {};

        if (savedDataRaw) {
            const parsed = typeof savedDataRaw === 'string'
                ? JSON.parse(savedDataRaw)
                : savedDataRaw;

            fullData = (parsed && parsed.data) ? parsed.data : parsed;

            // Restore bold metadata
            if (parsed && parsed.metadata) {
                parsed.metadata.forEach((rowMeta, rIdx) => {
                    if (!rowMeta) return;
                    rowMeta.forEach((cellMeta, cIdx) => {
                        if (cellMeta && cellMeta.bold) boldCells[`${rIdx},${cIdx}`] = true;
                    });
                });
            }
        } else {
            fullData = defaultData;
        }

        // Ensure minimum rows
        while (fullData.length < MIN_DATA_ROWS) {
            fullData.push(Array(COL_COUNT).fill(null));
        }

        // ── Merge cells — mirrors HOT mergeCells config exactly ───────────────
        const mergeCells = {
            // Income Statement header (cols A-D = indices 0-3)
            'A1': [4, 1], 'A2': [4, 1], 'A3': [4, 1],
            // Statement of Changes in Equity header (cols E-H = indices 4-7)
            'E1': [4, 1], 'E2': [4, 1], 'E3': [4, 1],
            // Balance Sheet header (cols I-L = indices 8-11)
            'I1': [4, 1], 'I2': [4, 1], 'I3': [4, 1],
        };

        // ── Cell styles ───────────────────────────────────────────────────────
        const cellStyle = {};

        // Rows 1-3: bold, centred per-section headers
        // Row 1: font-size 14px
        COLS.forEach(c => {
            cellStyle[`${c}1`] = 'font-weight:700;text-align:center;font-size:14px;';
            cellStyle[`${c}2`] = 'font-weight:700;text-align:center;font-size:13px;';
            cellStyle[`${c}3`] = 'font-weight:700;text-align:center;font-size:13px;';
        });

        // Separator cols D and H (index 3 and 7): grey bg + right border
        fullData.forEach((_, rIdx) => {
            const rNum = rIdx + 1;
            cellStyle[`D${rNum}`] = (cellStyle[`D${rNum}`] || '') + 'background:#f8f9fa;border-right:2px solid #dee2e6;';
            cellStyle[`H${rNum}`] = (cellStyle[`H${rNum}`] || '') + 'background:#f8f9fa;border-right:2px solid #dee2e6;';
        });

        // Section label bold rows (matching HOT renderer conditions)
        const boldLabelRows = {
            // Income Statement labels — col A (index 0)
            'Revenues:': 0, 'Less: Expenses': 0, 'Net Income': 0,
            // Statement of Changes labels — col E (index 4)
            'Total': 4, 'Durano, Capital, ending': 4,
            // Balance Sheet labels — col I (index 8)
            'Assets': 8, 'Current assets': 8, 'Non-current assets': 8,
            'Liabilities': 8, "Owner's Equity": 8,
            'Total current assets': 8, 'Total Non-current assets': 8,
            'Total Assets': 8, 'Total liabilities': 8,
            "Total Liabilities and Owner's Equity": 8,
        };

        // Total border-top rows (matching HOT borderTop logic)
        const totalBorderLabels = new Set([
            'Net Income', 'Total', 'Durano, Capital, ending',
            'Total current assets', 'Total Non-current assets',
            'Total Assets', 'Total liabilities',
            "Total Liabilities and Owner's Equity",
        ]);

        // Double-bottom rows
        const doubleBottomLabels = new Set([
            'Durano, Capital, ending', 'Total Assets',
            "Total Liabilities and Owner's Equity",
        ]);

        fullData.forEach((row, rIdx) => {
            if (rIdx < 3) return; // skip header rows already styled
            const rNum = rIdx + 1;
            row.forEach((val, cIdx) => {
                if (!val || String(val).trim() === '') return;
                const cellVal = String(val).trim();
                const ref = `${COLS[cIdx]}${rNum}`;

                // Bold section labels
                if (boldLabelRows.hasOwnProperty(cellVal)) {
                    cellStyle[ref] = (cellStyle[ref] || '') + 'font-weight:700;';
                }

                // Total rows — border-top + bold
                if (totalBorderLabels.has(cellVal)) {
                    cellStyle[ref] = (cellStyle[ref] || '') + 'font-weight:700;border-top:1px solid #000;';
                    // Apply border-top to numeric siblings in same row
                    // IS numeric cols: 1,2 → B,C; SCE: 5,6 → F,G; BS: 9,10,11 → J,K,L
                    [1,2,5,6,9,10,11].forEach(nIdx => {
                        const nRef = `${COLS[nIdx]}${rNum}`;
                        cellStyle[nRef] = (cellStyle[nRef] || '') + 'border-top:1px solid #000;';
                    });
                }

                // Double underline for final totals
                if (doubleBottomLabels.has(cellVal)) {
                    cellStyle[ref] = (cellStyle[ref] || '') + 'border-bottom:3px double #000;';
                    [1,2,5,6,9,10,11].forEach(nIdx => {
                        const nRef = `${COLS[nIdx]}${rNum}`;
                        cellStyle[nRef] = (cellStyle[nRef] || '') + 'border-bottom:3px double #000;';
                    });
                }
            });
        });

        // Right-align numeric value columns for data rows (row 4+)
        const numericColIndices = [1, 2, 5, 6, 9, 10, 11];
        fullData.forEach((_, rIdx) => {
            if (rIdx < 3) return;
            const rNum = rIdx + 1;
            numericColIndices.forEach(cIdx => {
                const ref = `${COLS[cIdx]}${rNum}`;
                cellStyle[ref] = (cellStyle[ref] || '') + 'text-align:right;';
            });
        });

        // Apply saved bold metadata
        Object.keys(boldCells).forEach(key => {
            const [r, c] = key.split(',').map(Number);
            const ref = `${COLS[c]}${r + 1}`;
            cellStyle[ref] = (cellStyle[ref] || '') + 'font-weight:700;';
        });

        // ── Responsive dimensions ─────────────────────────────────────────────
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

        // ── Build columns array ───────────────────────────────────────────────
        const columns = COLS.map((_, i) => ({
            type : numericColIndices.includes(i) ? 'numeric' : 'text',
            width: colWidths[i],
            ...(numericColIndices.includes(i) ? { mask: '#,##0.00', decimal: '.' } : {}),
        }));

        // ── Init jSpreadsheet ─────────────────────────────────────────────────
        const table = jspreadsheet(container, {
            data             : fullData,
            minDimensions    : [COL_COUNT, fullData.length],
            defaultColWidth  : isMobile ? 120 : 150,
            mergeCells       : mergeCells,
            style            : cellStyle,
            columns          : columns,
            minDimensions    : [COL_COUNT, fullData.length],
            tableWidth       : '100%',
            tableOverflow    : true,
            tableHeight      : `${tableH}px`,
            allowFormulas    : true,
            columnSorting    : false,
            columnDrag       : false,
            rowDrag          : false,
            allowInsertRow   : true,
            allowInsertColumn: false,
            allowDeleteRow   : true,
            allowDeleteColumn: false,
            columnResize     : true,
            rowResize        : true,
            copyCompatibility: true,
            minSpareRows     : 1,

            // ── Context menu with Bold toggle ─────────────────────────────────
            contextMenu: function (obj, x, y, e) {
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
        });

        // ── Ctrl+B / Cmd+B keyboard shortcut ─────────────────────────────────
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

        // ── Expose for potential external use ─────────────────────────────────
        window.table = table;

        // ── Responsive resize ─────────────────────────────────────────────────
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

        // ── Form submit — persist data + bold metadata ────────────────────────
        document.getElementById('answerKeyForm').addEventListener('submit', function (e) {
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

            document.getElementById('correctData').value = JSON.stringify({
                data     : data,
                metadata : metadata,
            });

            this.submit();
        });

    })();
    </script>

    {{-- ═══════════════════════════ Import modal ════════════════════════════════ --}}
    @include('instructors.performance-tasks.answer-sheets._import-modal', ['step' => 7])

    {{-- ── Import modal bridge ────────────────────────────────────────────── --}}
    <script>
    (function () {

        const HEADER_ROWS   = 3;  // 3 header rows
        const COL_COUNT     = 12;
        const MIN_DATA_ROWS = 35;

        const COLS = ['A','B','C','D','E','F','G','H','I','J','K','L'];

        const HEADER_KEYWORDS = [
            'date', 'debit', 'credit', 'account', 'description', 'title', 'durano',
            'income', 'statement', 'changes', 'equity', 'balance', 'sheet', 'assets',
            'liabilities', 'revenues', 'expenses', 'capital', 'withdrawals', 'enterprise',
        ];

        window.applyImport = function () {
            const rawImport = window.__importParsedData;

            if (!rawImport || !rawImport.length) {
                document.getElementById('importErrorText').textContent = 'No data to import.';
                document.getElementById('importError').style.display   = 'flex';
                return;
            }
            if (typeof table === 'undefined' || !table) {
                document.getElementById('importErrorText').textContent = 'Spreadsheet not ready.';
                document.getElementById('importError').style.display   = 'flex';
                return;
            }

            let dataRows = [...rawImport];

            const markerIdx = dataRows.findIndex(row =>
                String(row[0] ?? '').trim() === '##DATA_START##'
            );
            if (markerIdx !== -1) {
                dataRows = dataRows.slice(markerIdx + 1);
            } else {
                function rowIsHeader(row) {
                    const cells = row.map(c => String(c ?? '').trim());
                    if (!cells.some(c => c !== '')) return false;
                    if (cells.some(c => c !== '' && !isNaN(parseFloat(c)))) return false;
                    return cells.some(cell =>
                        HEADER_KEYWORDS.some(kw => cell.toLowerCase().includes(kw))
                    );
                }
                let stripped = 0;
                while (dataRows.length > 0 && stripped < 10 && rowIsHeader(dataRows[0])) {
                    dataRows.shift(); stripped++;
                }
                if (dataRows.length > 0 && dataRows[0].every(c => String(c ?? '').trim() === '')) {
                    dataRows.shift();
                }
            }

            while (dataRows.length > 0 &&
                   dataRows[dataRows.length - 1].every(c => String(c ?? '').trim() === '')) {
                dataRows.pop();
            }

            if (dataRows.length === 0) {
                document.getElementById('importErrorText').textContent =
                    'No data rows found. Make sure the template data cells are filled in.';
                document.getElementById('importError').style.display = 'flex';
                return;
            }

            const norm = row => {
                const r = row.map(c => (c === null || c === undefined) ? '' : String(c));
                while (r.length < COL_COUNT) r.push('');
                return r.slice(0, COL_COUNT);
            };

            const currentFull = table.getData();
            const headers     = currentFull.slice(0, HEADER_ROWS);

            let newFull = [...headers, ...dataRows.map(norm)];
            while (newFull.length < HEADER_ROWS + MIN_DATA_ROWS) {
                newFull.push(Array(COL_COUNT).fill(''));
            }

            table.setData(newFull);
            closeImportModal();

            const t = document.getElementById('importToast');
            const m = document.getElementById('importToastMsg');
            if (t && m) {
                m.textContent     = `Imported ${dataRows.length} data rows successfully. Review then save.`;
                t.style.display   = 'flex';
                t.style.opacity   = '1';
                t.style.transform = 'translateY(0)';
                setTimeout(() => {
                    t.style.opacity   = '0';
                    t.style.transform = 'translateY(8px)';
                    setTimeout(() => { t.style.display = 'none'; }, 300);
                }, 3500);
            }
        };

    })();
    </script>

</x-app-layout>